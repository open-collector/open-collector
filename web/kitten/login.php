<?php

/*  Collector (Garcia, Kornell, Kerr, Blake & Haffey)
    A program for running experiments on the web
    Copyright 2012-2016 Mikey Garcia & Nate Kornell


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 
		Kitten release (2019-2020) author: Dr. Anthony Haffey (a.haffey@reading.ac.uk)
*/

require("../../../headers.php");
$http_origin = $_SERVER['HTTP_ORIGIN'];

$return_obj = new stdClass();					// Leaving this as an object rather than just echoing the response for flexibility 
$return_obj->error_msg = 'Awaiting';  // in the future if more than messages needed in response to post request

require_once "../../../mailerPassword.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';



if (in_array($http_origin,$valid_headers)) {
	header("Access-Control-Allow-Origin: $http_origin");
} else {
	$return_obj->error_msg = "Attempt to log on from an invalid website";
	echo json_encode($return_obj); //this might never be read
	return;	
}

require_once "cleanRequests.php";
require_once "../../../sqlConnect.php";
require_once "../../../oCollector_captcha_keys.php";

//require_once "../../../oCollector_captcha_keys.php";

$user_email    = isset($_POST['user_email'])    ? $_POST['user_email']    : '';
$user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';

function create_random_code($length){
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);        
  $new_code= '';
  for ($i = 0; $i < $length; $i++) {
    $new_code .= $characters[rand(0, $charactersLength - 1)];
  }
  return $new_code;
}

function email_user($email_type,
                    $user_email,
                    $email_confirm_code){
	$mail = new PHPMailer(true);                          // Passing `true` enables exceptions
	$mail->SMTPDebug = 0;                                 // Enable verbose debug output
	//$mail->isSMTP();                                    // Set mailer to use SMTP
	$mail->Host = 'ocollector.org';  											// smtp2.example.com, Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = "$mailer_user";											// SMTP username
	$mail->Password = "$mailer_password";                 // SMTP password
	$mail->SMTPSecure = 'tls';
	$mail->SMTPOptions = array(
		'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
		)
	);                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to
	$mail->setFrom('no-reply@ocollector.org', 'Collector');
	$mail->isHTML(true);                                  // Set email format to HTML
			

  switch($email_type){
    case "registration":
      $msg = "Dear $user_email \n \nThank you for registering with Open-Collector. Before you can use your new profile, we need to confirm this is a valid address. Please proceed to the following link to confirm: \n www.ocollector.org/web/kitten/confirm.php?email=$user_email&confirm_code=$email_confirm_code \nMany thanks, \nSome Open Solutions";

      // use wordwrap() if lines are longer than 70 characters
      $msg = wordwrap($msg,70);

      // send email
			$mail->Subject = "Confirmation code for Registering with Collector";
			$mail->Body    = $msg;
			$mail->AltBody = $msg;

			$mail->addAddress($user_email);     // Add a recipient
			$mail->send();			
      break;
    case "forgot": 
      $msg = "Dear $user_email \n \nThere has been a request to reset the password for your account. Please go to the following link to set your new password: \n www.ocollector.org/UpdatePassword.php?email=$user_email&confirm_code=$email_confirm_code \nMany thanks, \nThe Open-Collector team";

      $msg = wordwrap($msg,70); // use wordwrap() if lines are longer than 70 characters        
      mail($user_email,"Resetting password with Open-Collector",$msg); // send email
      $_SESSION['login_error'] = "You have just been given an e-mail to reset your password. Please click on the link included.";
      break;
  }
}


function validate_captcha($captcha_secret, $captcha_response){
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captcha_secret.'&response='.$captcha_response); 
  
  // Decode json data 
  $responseData = json_decode($verifyResponse); 
   
  // If reCAPTCHA response is valid 
  if($responseData->success){ 
    return true;
  }else{ 
    return false;
  }
}




//use switch statement instead??
if($_POST["login_type"] == "register") {
  $sql="SELECT * FROM users WHERE email='$user_email'";
  $result = $conn->query($sql);    
  if($result->num_rows>0){
    $return_obj->error_msg = "user already exists";
  } else {
    // create random string as confirm code
    
    $salt = create_random_code(20);
    $pepper = create_random_code(20);	
    $email_confirm_code = create_random_code(20);
		$hashed_password = password_hash($salt.$user_password.$pepper, PASSWORD_BCRYPT);

    $sql = "INSERT INTO `users` (`email`, `password`, `email_confirm_code`, `salt`,`pepper`,`account_status`) VALUES('$user_email', '$hashed_password', '$email_confirm_code','$salt','$pepper','u')";
    if ($conn->query($sql) === TRUE) {			
      if(validate_captcha($captcha_secret, $_POST['g-recaptcha-response'])){
        $success_fail = "fail";  //not really, but need to confirm with e-mail code first
        $return_obj->error_msg = "E-mail not sent. Please contact anthony dot haffey @ gmail dot com."; 

        email_user( "registration",
                    $user_email,
                    $email_confirm_code);
				$return_obj->error_msg = "You have just received a registration e-mail. Please check your spam box in case it has gone there. You won't be able to proceed until you've clicked on the link in the e-mail.";
				
				
      } else {
        $return_obj->error_msg  = 'Robot verification failed, please try again.';
      }
    } else {      
      $return_obj->error_msg = "Error adding user: $result " . $conn->error;
    }
  }
	echo json_encode($return_obj);
}
if($_POST["login_type"] == "forgot") {
  $sql="SELECT * FROM users WHERE email='$user_email'";     
  $result = $conn->query($sql);
  if($result->num_rows == 0){
    $success_fail = "fail"; 
    $_SESSION['login_error'] = "This account is not registered - please double check that you typed it in correctly.";
  } else {
    
    if(validate_captcha($captcha_secret, $_POST['g-recaptcha-response'])){
      $email_confirm_code = create_random_code(20);
      $sql = "UPDATE `users` SET `email_confirm_code`  = '$email_confirm_code' WHERE `email` = '$user_email'";
      if ($conn->query($sql) === TRUE) {
        $success_fail = "fail";  //not really, but need to confirm with e-mail code first
        $_SESSION['login_error'] = "E-Mail not sent,, please contact anthony dot haffey at gmail dot com.";
        email_user( "forgot",
                    $user_email,
                    $email_confirm_code);
      } else {
        $success_fail = "fail";
        $_SESSION['login_error'] = "Error adding user: $result " . $conn->error;
      }
    } else {
      $success_fail = "fail";
      $_SESSION['login_error'] = 'Robot verification failed, please try again.';
    }
  }
}

if($_POST["login_type"]=="logout"){
  unset($_SESSION['user_email']);
	$return_obj->error_msg	= "You have succesfully logged out";
	echo json_encode($return_obj);	
}
if($_POST["login_type"] == "login"){ 
  $sql = "SELECT * FROM users WHERE email='$user_email'";    
  $result = $conn->query($sql);
	
	if($result->num_rows > 1){
		$return_obj->error_msg = 'Please contact team@someopen.solutions -  there are multiple instances of this e-mail address registered.';				
  } else if($result->num_rows == 1){
    $row = mysqli_fetch_array($result);
    if($row['account_status'] == 'V'){  
      if (password_verify($row['salt'].$user_password.$row['pepper'], $row['password'])){
				if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
					if(validate_captcha($captcha_secret, $_POST['g-recaptcha-response'])){
						$_SESSION['user_email'] = "$user_email";
						$return_obj->error_msg = "You have succesfully logged in.";						
          } else {
            $return_obj->error_msg = 'Robot verification failed, please try again.';
          }             
        } else { 
					$return_obj->error_msg = 'Please check on the reCAPTCHA box.';
				}
      } else {
        $return_obj->error_msg = 'Invalid e-mail address and/or password.';
      }						
    } else {
      $return_obj->error_msg = "This account has been locked out. Please check your e-mails for a code to log you back in.";
    }    
  } else {
    $return_obj->error_msg = 'Invalid e-mail address and/or password.';
	}
	echo json_encode($return_obj);
}
?>
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

		Kitten release (2019) author: Dr. Anthony Haffey (a.haffey@reading.ac.uk)
*/
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once 'Code/InitiateCollector.php';
//require_once ("cleanRequests.php");

$all_data 	 		 = $_POST['all_data'];
$participant 		 = $_SESSION['participant_code'];
$completion_code = $_SESSION['completion_code'];
$location	 			 = $_SESSION['location'];

// identify researchers here
//mysql to find researchers who contributed to this experiment...?

require_once "../../../mailerPassword.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);                            // Passing `true` enables exceptions
try {
  //Server settings
  $mail->SMTPDebug = 0;                                 // Enable verbose debug output
  $mail->isSMTP();                                      // Set mailer to use SMTP
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
  $mail->setFrom('no-reply@ocollector.org', 'Open-Collector');


	require("../../sqlConnect.php");

	$sql = "SELECT * FROM `view_experiment_users` WHERE `location`='$location'";
	$user_array = [];
	$result 					 = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		$user = $row['email'];
		$experiment_id = $row['experiment_id'];
		$body_alt_body = "Hello, <br><br> Participant $participant has just completed the task. <br><br> Their completion code was $completion_code. <br><br> To decrypt the data, please go to www.ocollector.org/".$_SESSION['version']."/index.php and upload the attached file using the 'data' tab. <br><br> Best wishes, <br><br> The open-collector team.";

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = "Collector - $participant completed with code: $completion_code";
		$mail->Body    = $body_alt_body;
		$mail->AltBody = $body_alt_body;

		$mail->AddStringAttachment($all_data,"encrypted_$experiment_id-$participant.txt");
		$mail->addAddress($user);     // Add a recipient
		$mail->send();
	}

	echo "Your encrypted data has been emailed to the researcher(s). Completion code is: <br><br><b> $completion_code </b><br><br> Warning - completion codes may get muddled if you try to do multiple experiments at the same time. Please don't. encrypted data = $encrypted_data";

	//$mail->isHTML(true);                                  // Set email format to HTML

} catch (Exception $e) {
  echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
?>

<?php
return array(
'root'                       => array('Dir'  , '.'),
'index'                      => array('File' , 'index.php'),

'Apps'                       => array('Dir'  , 'Apps'),


'Experiments'                => array('Dir'  , 'Experiments'),
  'Common'                   => array('Dir'  , 'Experiments/_Common'),
    'Common Settings'        => array('File' , 'Experiments/_Common/Common Settings.json'),
    'Password'               => array('File' , 'Experiments/_Common/Password.php'),
    'Ineligibility Dir'      => array('Dir'  , 'Experiments/_Common/Ineligible'),
    'Media Dir'              => array('Dir'  , 'Experiments/_Common/Media'),
    'Media'                  => array('---'  , 'Experiments/_Common/Media/[var]'),
    'Custom Trial Types'     => array('Dir'  , 'Experiments/_Common/TrialTypes'),
    'Custom Trial Type Dir'  => array('Dir'  , 'Experiments/_Common/TrialTypes/[var]'),
      'Custom Trial Template'=> array('File' , 'Experiments/_Common/TrialTypes/[var]/template.html'),
      'Custom Trial Scoring' => array('File' , 'Experiments/_Common/TrialTypes/[var]/scoring.js'),

  'Current Experiment'       => array('Dir'  , 'Experiments/[Current Experiment]'),
    'Current Index'          => array('File' , 'Experiments/[Current Experiment]/index.php'),
    'Current Return'         => array('File' , 'Experiments/[Current Experiment]/Return.php'),
    'Conditions'             => array('CSV'  , 'Experiments/[Current Experiment]/Conditions.csv'),
    'Experiment Settings'    => array('File' , 'Experiments/[Current Experiment]/Settings.json'),
    'Stimuli Dir'            => array('Dir'  , 'Experiments/[Current Experiment]/Stimuli'),
      'Stimuli'              => array('CSV'  , 'Experiments/[Current Experiment]/Stimuli/[Stimuli]'),
    'Procedure Dir'          => array('Dir'  , 'Experiments/[Current Experiment]/Procedure'),
      'Procedure'            => array('CSV'  , 'Experiments/[Current Experiment]/Procedure/[Procedure]'),
    'Surveys'                => array('Dir'  , 'Experiments/_Common/Surveys'),
    'Survey'                 => array('CSV'  , 'Experiments/_Common/Surveys/[var]'),

'Code'                       => array('Dir'  , 'Code'),
  'Welcome'                  => array('File' , 'Code/Welcome.php'),
  'Custom Functions'         => array('File' , 'Code/customFunctions.php'),
  'Login'                    => array('File' , 'Code/login.php'),
  'Experiment Page'          => array('File' , 'Code/Experiment.php'),
  'Trial Content'            => array('File' , 'Code/trialContent.html'),
  'Initiate Collector'       => array('File' , 'Code/initiateCollector.php'),
  'Trial Validator Require'  => array('File' , 'Code/trialValidator.require.php'),
  'No JS'                    => array('File' , 'Code/nojs.php'),
  'Set Password'             => array('File' , 'Code/setPassword.php'),
  'Shuffle Functions'        => array('File' , 'Code/shuffleFunctions.php'),
  'Custom Functions'         => array('File' , 'Code/customFunctions.php'),

  'CSS Dir'                  => array('Dir'  , 'Code/css'),
    'Icon'                   => array('File' , 'Code/css/icon.png'),
    'Global CSS'             => array('File' , 'Code/css/global.css'),
    'Jquery UI CSS'          => array('File' , 'Code/css/jquery-ui.min.css'),
    'Jquery UI Custom CSS'   => array('File' , 'Code/css/jquery-ui-1.10.4.custom.min.css'),
    'No JS Warning'          => array('File' , 'Code/css/nojswarning.png'),
    'Tools CSS'              => array('File' , 'Code/css/tools.css'),
    'Tutorial CSS'           => array('File' , 'Code/css/tutorial.css'),
    'Fonts'                  => array('Dir'  , 'Code/css/fonts'),
    'CSS Images'             => array('Dir'  , 'Code/css/images'),

  'Classes'                  => array('Dir'  , 'Code/classes'),
    'Ajax Json'              => array('File' , 'Code/classes/Ajax_Json.php'),
    'Ajax Survey'            => array('File' , 'Code/classes/AjaxSurvey.php'),
    'Conditions Class'       => array('File' , 'Code/classes/ConditionController.php'),
    'Control Files Class'    => array('File' , 'Code/classes/ControlFile.php'),
    'Debug Class'            => array('File' , 'Code/classes/DebugController.php'),
    'Errors Class'           => array('File' , 'Code/classes/ErrorController.php'),
    'Procedure Class'        => array('File' , 'Code/classes/Procedure.php'),
    'SideData Class'         => array('File' , 'Code/classes/SideData.php'),
    'Status Class'           => array('File' , 'Code/classes/StatusController.php'),
    'Stimuli Class'          => array('File' , 'Code/classes/Stimuli.php'),
    'system map'             => array('File' , 'Code/classes/systemMap.php'),

  'JS Dir'                   => array('Dir'  , 'Code/javascript'),
    'Trial JS'               => array('File' , 'Code/javascript/Trial.js'),
    'Experiment JS'          => array('File' , 'Code/javascript/Experiment.js'),
    'Jquery'                 => array('File' , 'Code/javascript/jquery-3.1.0.min.js'),
    'Jquery UI Custom'       => array('File' , 'Code/javascript/jquery-ui-1.10.4.custom.min.js'),
    'Jquery UI'              => array('File' , 'Code/javascript/jquery-1.12.4.min.js'),
    'Login JS'               => array('File' , 'Code/javascript/loggingIn.js'),
    'Sha256 JS'              => array('File' , 'Code/javascript/sha256.js'),

'Data'                       => array('Dir'  , 'Data'),
  'Session Dir'              => array('Dir'  , 'Data/sess'),
    'Session'                => array('Sess' , 'Data/sess/sess_[Session ID]'),
    'PHP Session Table'      => array('JSON' , 'Data/sess/session_table.json'),
  'User Data'                => array('Dir'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/[Username]'),
    'User Stimuli'           => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/[Username]/stimuli.csv'),
    'User Procedure'         => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/[Username]/procedure.csv'),
    'User Responses'         => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/[Username]/responses.csv'),
    'User Globals'           => array('JSON' , 'Data/[Current Experiment]-Data[Data Sub Dir]/[Username]/globals.json'),
  'Current Data Dir'         => array('Dir'  , 'Data/[Current Experiment]-Data[Data Sub Dir]'),
    'Random Assignments'     => array('File' , 'Data/[Current Experiment]-Data[Data Sub Dir]/RandomAssignments.txt'),
    'Status Begin Data'      => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/Status_Begin.csv'),
    'Status End Data'        => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/Status_End.csv'),
    'SideData Data'          => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/SideData.csv'),
    'Output Dir'             => array('Dir'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/Output'),
      'Output'               => array('CSV'  , 'Data/[Current Experiment]-Data[Data Sub Dir]/Output/[Username].csv'),


      
'Admin'                      => array('Dir'  , 'Admin'),
  'Admin Index'              => array('File' , 'Admin/index.php'),
  'Tools'                    => array('Dir'  , 'Admin/Tools'),
'Jsons'                      => array('Dir'  , 'Jsons'),
'Trial Types'                => array('Dir'  , 'TrialTypes'),
  'Trial Type Dir'           => array('Dir'  , 'TrialTypes/[var]'),
    'Trial Template'         => array('File' , 'TrialTypes/[var]/template.html'),
    'Trial Scoring'          => array('File' , 'TrialTypes/[var]/scoring.js'),
  'default scoring'          => array('File' , 'TrialTypes/defaultTrialScoring.js'),
);

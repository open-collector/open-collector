<?php
/*  Collector
    A program for running experiments on the web
    Copyright 2012-2016 Mikey Garcia & Nate Kornell
 */

require 'initiateCollector.php';
require $_PATH->get('Experiment Require');

// check that the state is set
if (empty($_SESSION['state'])) {
    // get out if you're not supposed to be here
    $root = $_PATH->get('root');
    header("Location: $root");
    exit;
}

// this only happens once, so that refreshing the page doesn't do anything, and
// recording a new line of data is the only way to update the timestamp
if (!isset($_SESSION['Timestamp'])) {
    $_SESSION['Timestamp'] = microtime(true);
}

// start an output buffer, so that if we need to redirect, we aren't
// blocked by previously sent content
ob_start();

/*
 * RECORD DATA
 *
 * Whenever experiment.php finds $_POST data, it will try to store that data
 * immediately, rather than simply holding it through the trial.
 * If the main trial and all post trials are completed, the data will
 * be written using the recordTrial() function.
 */
if ($_POST !== array()) {
    // score data
    require $_TRIAL->getRelatedFile('scoring');
    $data = isset($data) ? $data : $_POST;

    if (isset($sideData)) {
        if (empty($sidedata_label)) {
            $sidedata_label = $trial_type;
        }
        $_SIDE->add($sideData, $sidedata_label);
    }

    // record data and advance to next PostTrial if applicable
    $_EXPT->record($data);
    if (($_EXPT->advance()) === 1) {
        recordTrial($_EXPT->getTrial(-1));
    }

    // still need to complete this file, don't write to file yet
    header('Location: ' . $_PATH->get('Experiment Page'));
    exit;
}

// Now that data is recorded, see if we are finished
if ($_EXPT->isComplete()) {
    gotoDone();
}

/*
 *  PREPARE TRIAL FOR DISPLAY
 */
// get the related files for the trial
$addedScripts = array_filter(array($_TRIAL->getRelatedFile('script')));
$addedStyles = array_filter(array($_TRIAL->getRelatedFile('style')));

$helper = $_TRIAL->getRelatedFile('helper');
if (!empty($helper)) {
    require $helper;
}

// update the text string by looking for variables in it (e.g. $var) and getting
// the values using $_EXPT->get()
$_EXPT->updateVariables('text');

// override time in debug mode, use standard timing if no debug time is set
if ($_SESSION['Debug'] == true && $_SETTINGS->debug_time != '') {
    $_EXPT->update('max time', $_SETTINGS->debug_time);
}

/*
 *  DISPLAY
 */
$postTo = $_PATH->get('Experiment Page');
$trialFail = false; // used to show diagnostic information when a trial fails

$title = 'Experiment';
$_dataController = 'experiment';
$_dataAction = $_TRIAL->get('trial type');

require $_PATH->get('Header');

// actually include the trial type display file here
$display = $_TRIAL->getRelatedFile('display');
if ($display): ?>
<script src="<?= $_PATH->get('Collector JS', 'url') ?>"></script>
<script>
    Collector.inputs.min = "<?= $_EXPT->get('min time') ?>";
    Collector.inputs.max = "<?= $_EXPT->get('max time') ?>";
</script>
<form class="experimentForm <?= $formClass; ?> invisible" action="<?= $postTo; ?>" method="post" id="content" autocomplete="off">
  <?php include $display ?>

  <?php if ($_SESSION['Debug']) : ?>
  <button type="submit" style="position: absolute; top: 50px; right: 50px;"onclick="$('form').submit()">Debug Submit!</button>
  <?php endif; ?>

  <input id="Duration"          name="Duration"          type="hidden"  value="-1"/>
  <input id="First_Input_Time"  name="First_Input_Time"  type="hidden"  value="-1"/>
  <input id="Last_Input_Time"   name="Last_Input_Time"   type="hidden"  value="-1"/>
  <input id="Focus"             name="Focus"             type="hidden"  value="-1"/>
</form>



<?php else: ?>
<h2>Could not find the following trial type: <strong><?= $_TRIAL->get('trial type') ?></strong></h2>
<p>Check your procedure file to make sure everything is in order. All information about this trial is displayed below.</p>

<!-- default trial is always user timing so you can click 'Done' and progress through the experiment -->
<div>
  <form name="UserTiming" class="UserTiming" action="<?= $postTo; ?>" method="post" autocomplete="off">
    <input id="RT"      name="RT"      type="hidden" value="-1"/>
    <input id="RTfirst" name="RTfirst" type="hidden" value="-1"/>
    <input id="RTlast"  name="RTlast"  type="hidden" value="-1"/>
    <input id="Focus"   name="Focus"   type="hidden" value="-1"/>
    <input class="collectorButton collectorAdvance" id="FormSubmitButton" type="submit" value="Done" />
  </form>
</div>

<?php
$trialFail = true;
endif; ?>

<!-- hidden field that JQuery/JavaScript uses to check the timing to $postTo -->
<div id="maxTime" class="hidden"><?= $_EXPT->get('max time'); ?></div>
<div id="minTime" class="hidden"><?= $_EXPT->get('min time'); ?></div>

<?php
// Diagnostics
if (($_SETTINGS->trial_diagnostics == true) || ($trialFail == true)) {
    datadump($_TRIAL->getDebugInfo());
}
?>

<!-- pre-cache to start loading next trial resources -->
<div class="precachenext">
<?php
if ($_EXPT->getTrial(1) !== null) {
    $item = $_EXPT->getTrial(1)->get('item');
    if (is_array($item) && !empty($item)) {
        foreach (array_values($_EXPT->getTrial(1)->get('item')) as $val) {
            if (show($val) !== $val) {
                echo show($val);
            }
        }
    }
}
?>
</div>

<?php
// get footer and flush data to screen
require $_PATH->get('Footer');

ob_end_flush();

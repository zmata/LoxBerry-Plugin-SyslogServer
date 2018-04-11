<?php
require_once "loxberry_system.php";
require_once "loxberry_web.php";
require_once "Config/Lite.php";
require_once "function.php";

$L = LBWeb::readlanguage("language.ini");

$template_title = "Syslog Server";
$helplink = $L['LINKS.WIKI'];

$helptemplate = "pluginhelp.html";

$navbar[1]['Name'] = $L['NAVBAR.FIRST'];
$navbar[1]['URL'] = 'index.php';

$navbar[2]['Name'] = $L['NAVBAR.SECOND'];
$navbar[2]['URL'] = 'loganalyzer/index.php';
$navbar[2]['target'] = '_blank';

if ($_POST['save_new']) {
  zmata_new_config($lbpconfigdir, $lbplogdir, $_POST['name'], $_POST['ip']);
  zmata_new_rsyslog($lbpconfigdir, $lbplogdir, $_POST['name'], $_POST['ip']);
  zmata_new_loganalyzer($lbpconfigdir, $lbplogdir, $_POST['name'], $_POST['ip']);

  $command = 'sudo '. $lbpbindir. '/service.sh restart rsyslog.service';
  $status = shell_exec($command);

  header ("Location: index.php");
}

if ($_POST['change']) {
  zmata_new_config($lbpconfigdir, $lbplogdir, $_POST['name'], $_POST['ip']);
  zmata_new_rsyslog($lbpconfigdir, $lbplogdir, $_POST['name'], $_POST['ip']);
  zmata_new_loganalyzer($lbpconfigdir, $lbplogdir, $_POST['name'], $_POST['ip']);

  $command = 'sudo '. $lbpbindir. '/service.sh restart rsyslog.service';
  $status = shell_exec($command);

  header ("Location: index.php");
}

if ($_POST['save_del']) {
  //Log Analyzer
  $file = $lbpconfigdir. '/loganalyzer/'. $_POST['name']. '.php';
  unlink($file);
  
  //rsyslog.d
  $file = $lbpconfigdir. '/rsyslog.d/'. $_POST['name']. '.conf';
  unlink($file);

  //config
  $file = $lbpconfigdir. '/'. $_POST['name']. '.ini';
  unlink($file);

  //logfile
  $file = $lbplogdir. '/'. $_POST['name']. '.log';
  unlink($file);

  //restart rsyslog
  $command = 'sudo '. $lbpbindir. '/service.sh restart rsyslog.service';
  $status = shell_exec($command);

  header ("Location: index.php");
}

// NAVBAR
$navbar[1]['active'] = True;

LBWeb::lbheader($template_title, $helplink, $helptemplate);

//NEW
if ($_POST['req_new']) {
  echo '<p class="wide">'. $L['LOGS.NEW']. '</p>';
  echo '<div class="ui-corner-all ui-shadow">';
  echo '<form action="index.php" method="post">';
  echo '<label for="name">'. $L['LOGS.NAME']. '</label>';
  echo '<input data-inline="true" data-mini="true" name="name" id="name" placeholder="Text input" type="text">'; 
  echo '<label for="ip">'. $L['LOGS.IP']. '</label>';
  echo '<input data-inline="true" data-mini="true" name="ip" id="ip" placeholder="Text input" type="text">'; 
  echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="plus" type="submit" name="save_new" value='. $L['LOGS.SAVE']. '>';
  echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="back" type="submit" name="return" value='. $L['LOGS.RETURN']. '>';
  echo '</form>';
  echo '</div>';
}

//DEL
elseif ($_POST['req_del']) {
  echo '<p class="wide">'. $L['LOGS.DEL']. '</p>';
  echo '<div class="ui-corner-all ui-shadow">';
  echo '<p>'. $L['LOGS.DELTEXT1']. ' '. $_POST['name']. '</p>';
  echo '<p>'. $L['LOGS.DELTEXT2']. '</p>';
  echo '<form action="index.php" method="post">';
  echo '<input type="hidden" name="name" value="'. $_POST['name']. '">';
  echo '<input type="hidden" name="ip" value="'. $_POST['ip']. '">';
  echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="delete" type="submit" name="save_del" value='. $L['LOGS.DELETE']. '>';
  echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="back" type="submit" name="return" value='. $L['LOGS.RETURN']. '>';
  echo '</form></div>';
}

//MAIN
else {
  echo '<p>'. $L['MAIN.INTRO1']. '</p>';
  echo '<p>'. $L['MAIN.INTRO2']. '</p>';
  echo '<br>';

  //LOGs
  echo '<p class="wide">'. $L['LOGS.HEAD']. '</p>';
  echo '<form action="index.php" method="post">';
  echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="plus" type="submit" name="req_new" value='. $L['LOGS.NEW']. '>';
  echo '</form>';

  if ($_POST['name']) {
    $logname = $_POST['name'];
    $logip   = $_POST['ip'];
  }

  $mask = $lbpconfigdir. "/". "*.ini";
  foreach (glob($mask) as $file) {
    // read cfg file
    $cfg = new Config_Lite("$file",INI_SCANNER_RAW);
    $name=$cfg->get(null,"name");
    $ip=$cfg->get(null,"ip");
    echo '<div class="ui-corner-all ui-shadow ui-field-contain">';
    echo '<form action="index.php" method="post">';
    echo '<input type="hidden" name="name" value="'. $name. '">';
    echo '<input type="hidden" name="ip" value="'. $ip. '">';
    echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="delete" type="submit" name="req_del" value='. $L['LOGS.DEL']. '>';
    echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="info" type="submit" name="show_detail" value='. $ip. '&nbsp-&nbsp'. $name. '>';
    echo '</form>';
    echo '</div>';

    if (!$logname) {
      $logname = $name;
      $logip   = $ip;
    }
    $found = 'X';
  }
  if ($found) {
    echo '<br><br>';

    //DETAILS
    echo '<p class="wide">'. $L['LOGDETAIL.HEAD']. '</p>';
    echo '<div>';
    echo '<form action="index.php" method="post">';
    echo '<label for="name">'. $L['LOGS.NAME']. '</label>';
    echo '<input data-inline="true" data-mini="true" name="name" id="name" placeholder="Text input" value='. $logname. ' type="text" disabled="disabled">';
    echo '<label for="ip">'. $L['LOGS.IP']. '</label>';
    echo '<input data-inline="true" data-mini="true" name="ip" id="ip" placeholder="Text input" value='. $logip. ' type="text">'; 
    echo '<input data-role="button" data-inline="true" data-mini="true" data-icon="check" type="submit" name="change" value='. $L['LOGS.SAVE']. '>'; 
    echo '</form>';
    echo '</div>';
  }
}

LBWeb::lbfooter();
?>

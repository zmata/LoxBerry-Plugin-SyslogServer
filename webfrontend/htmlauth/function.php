<?php
require_once "loxberry_system.php";
require_once "loxberry_web.php";
require_once "Config/Lite.php";

function zmata_new_config($confdir, $logdir, $name, $ip) {
  $file = $confdir. '/'. $name. '.ini';
  $cfg = new Config_Lite("$file");
  $cfg->set(null,"name",$name);
  $cfg->set(null,"ip",$ip);
  $cfg->save();
  $status = shell_exec('touch '. $logdir. '/'. $name. '.log');
}

function zmata_new_rsyslog($confdir, $logdir, $name, $ip) {
  $file = $confdir. '/rsyslog.d/'. $name. '.conf';
  $text = ':fromhost-ip, isequal, "'. $ip. '" '. $logdir. '/'. $name. '.log';
  file_put_contents($file, $text. PHP_EOL, LOCK_EX);
  $text = '& ~';
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX);
}

function zmata_new_loganalyzer($confdir, $logdir, $name, $ip) {
  $file = $confdir. '/loganalyzer/'. $name. '.php';
  $text = "<?php";
  file_put_contents($file, $text. PHP_EOL, LOCK_EX);
  $text = "$". "CFG['Sources']['". $name. "']['ID'] = '". $name. "';";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX);
  $text = "$". "CFG['Sources']['". $name. "']['Name'] = '". $name. "';";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX);
  $text = "$". "CFG['Sources']['". $name. "']['ViewID'] = 'SYSLOG';";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX);
  $text = "$". "CFG['Sources']['". $name. "']['SourceType'] = SOURCE_DISK;";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX);
  $text = "$". "CFG['Sources']['". $name. "']['LogLineType'] = 'misc';";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX);
  $text = "$". "CFG['Sources']['". $name. "']['DiskFile'] = '". $logdir. "/". $name. ".log';";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX); 
  $text = "?>";
  file_put_contents($file, $text. PHP_EOL, FILE_APPEND | LOCK_EX); 
}
?>


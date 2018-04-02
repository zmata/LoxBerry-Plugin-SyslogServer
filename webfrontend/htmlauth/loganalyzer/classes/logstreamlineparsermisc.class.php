<?php
/*
*********************************************************************
* LogAnalyzer - http://loganalyzer.adiscon.com
* -----------------------------------------------------------------     *
* Loxone Smart Home Miniserver Log Parser Class by Zmata
*********************************************************************
*/
// --- Avoid directly accessing this file!
if ( !defined('IN_PHPLOGCON') )
{
die('Hacking attempt');
exit;
}
// ---
// --- Basic Includes
require_once($gl_root_path . 'classes/enums.class.php');
require_once($gl_root_path . 'include/constants_errors.php');
require_once($gl_root_path . 'include/constants_logstream.php');
// ---
class LogStreamLineParsermisc extends LogStreamLineParser {
//      protected $_arrProperties = null;
// Constructor
public function __construct () {
return; // Nothing
}
public function LogStreamLineParsermisc() {
self::__construct();
}
/**
* ParseLine
*
* @param arrArguments array in&out: properties of interest. There can be no guarantee the logstream can actually deliver them.
* @return integer Error stat
*/
public function ParseLine($szLine, &$arrArguments)
{
// Sample (Syslog): Mar 10 14:45:44 debandre anacron[3226]: Job `cron.daily' terminated (mailing output)
if ( preg_match("/(...)(?:.|..)([0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}) ([a-zA-Z0-9_\-\.]{1,256}) ([A-Za-z0-9_\-\/\.]{1,32})\[(.*?)\]:(.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone1';
// Copy parsed properties!
$arrArguments[SYSLOG_DATE] = GetEventTime($out[1] . " " . $out[2]);
$arrArguments[SYSLOG_HOST] = $out[3];
$arrArguments[SYSLOG_SYSLOGTAG] = $out[4];
$arrArguments[SYSLOG_PROCESSID] = $out[5];
$arrArguments[SYSLOG_MESSAGE] = $out[6];
}
// Sample (Syslog): Mar 10 14:45:39 debandre syslogd 1.4.1#18: restart
else if ( preg_match("/(...)(?:.|..)([0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}) ([a-zA-Z0-9_\-\.]{1,256}) ([A-Za-z0-9_\-\/\.]{1,32}):(.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone2';
$loxone = $out[3]. ';'. $out[4].':'.  $out[5]. $out[6];
$loxone = str_replace(': ',':',$loxone);
$loxone = str_replace('#015','',$loxone);
$loxone = str_replace('#01','',$loxone);
$loxout = explode(';',$loxone);
// Copy parsed properties!
$arrArguments[SYSLOG_DATE] = GetEventTime($loxout[0]. ' '. $loxout[1]);
$arrArguments[SYSLOG_PROCESSID] = $loxout[2];
$arrArguments[SYSLOG_MESSAGE] = $loxout[3];
}
// Sample (Syslog): Mar 10 14:45:39 debandre syslogd restart
else if ( preg_match("/(...)(?:.|..)([0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}) ([a-zA-Z0-9_\-\.]{1,256}) ([A-Za-z0-9_\-\/\.]{1,32}) (.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone3';
// Copy parsed properties!
$arrArguments[SYSLOG_DATE] = GetEventTime($out[1] . " " . $out[2]);
$arrArguments[SYSLOG_HOST] = $out[3];
$arrArguments[SYSLOG_SYSLOGTAG] = $out[4];
$arrArguments[SYSLOG_MESSAGE] = $out[5];
}
// Sample (Syslog): Mar 7 17:18:35 debandre exiting on signal 15
else if ( preg_match("/(...)(?:.|..)([0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}) (.*?) (.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone4';
// Copy parsed properties!
$arrArguments[SYSLOG_DATE] = GetEventTime($out[1] . " " . $out[2]);
$arrArguments[SYSLOG_HOST] = $out[3];
$arrArguments[SYSLOG_MESSAGE] = $out[4];
}
// Sample (RSyslog): 2008-03-28T11:07:40+01:00 localhost rger: test 1
else if ( preg_match("/([0-9]{4,4}-[0-9]{1,2}-[0-9]{1,2}T[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}.[0-9]{1,2}:[0-9]{1,2}) (.*?) (.*?):(.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone5';
// Copy parsed properties!
$arrArguments[SYSLOG_DATE] = GetEventTime($out[1]);
$arrArguments[SYSLOG_HOST] = $out[2];
$arrArguments[SYSLOG_SYSLOGTAG] = $out[3];
$arrArguments[SYSLOG_MESSAGE] = $out[4];
}
// Sample (RSyslog): 2017-10-24T10:37:48.680031+02:00 2017-10-24 10:37:50;Kotel - spínání;Off#015
else if ( preg_match("/([0-9]{4,4}-[0-9]{1,2}-[0-9]{1,2}T[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}\.[0-9]{1,6}.[0-9]{1,2}:[0-9]{1,2}) (.*?) (.*?):(.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone6';
$loxone = $out[2]. ';'. $out[3].':'.  $out[4]. $out[5];
$loxone = str_replace(': ',':',$loxone);
$loxone = str_replace('#015','',$loxone);
$loxone = str_replace('#01','',$loxone);
$loxout = explode(';',$loxone);
// Copy parsed properties!
$arrArguments[SYSLOG_DATE] = GetEventTime($loxout[0]. ' '. $loxout[1]);
$arrArguments[SYSLOG_PROCESSID] = $loxout[2];
$arrArguments[SYSLOG_MESSAGE] = $loxout[3];
}
// Sample: 2008-03-28T15:17:05.480876+01:00,**NO MATCH**
else if ( preg_match("/([0-9]{4,4}-[0-9]{1,2}-[0-9]{1,2}T[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}\.[0-9]{1,6}.[0-9]{1,2}:[0-9]{1,2}),(.*?)$/", $szLine, $out ) )
{
$arrArguments[SYSLOG_MESSAGETYPE] = 'Loxone7';
// Some kind of debug message or something ...
$arrArguments[SYSLOG_DATE] = GetEventTime($out[1]);
$arrArguments[SYSLOG_MESSAGE] = $out[2];
}
else
{
if ( isset($arrArguments[SYSLOG_MESSAGE]) && strlen($arrArguments[SYSLOG_MESSAGE]) > 0 )
OutputDebugMessage("Unparseable syslog msg - '" . $arrArguments[SYSLOG_MESSAGE] . "'", DEBUG_ERROR);
}
// If SyslogTag is set, we check for MessageType!
if ( isset($arrArguments[SYSLOG_SYSLOGTAG]) )
{
if ( strpos($arrArguments[SYSLOG_SYSLOGTAG], "EvntSLog" ) !== false )
$arrArguments[SYSLOG_MESSAGETYPE] = IUT_NT_EventReport;
}
// Return success!
return SUCCESS;
}
}
?>

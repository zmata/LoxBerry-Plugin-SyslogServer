<?php
require_once "loxberry_system.php";
require_once "loxberry_web.php";
require_once "Config/Lite.php";

/*
function zmata_option_set($optval, $value) {
  if ($value == $optval)
    $option = '<option value="'. $optval. '" selected>'. $optval. '</option>';
  else
    $option = '<option value="'. $optval. '">'. $optval. '</option>';
  return $option;
}
*/

$L = LBWeb::readlanguage("language.ini");

$template_title = "Modbus Gateway";
//$helplink = "http://www.loxwiki.eu/display/LOXBERRY/Modbus+Gateway";
$helplink = $L['LINKS.WIKI'];

$helptemplate = "pluginhelp.html";

$navbar[1]['Name'] = $L['NAVBAR.FIRST'];
$navbar[1]['URL'] = 'index.php';

$navbar[2]['Name'] = $L['NAVBAR.SECOND'];
$navbar[2]['URL'] = 'configuration.php';


// NAVBAR
$navbar[1]['active'] = True;

LBWeb::lbheader($template_title, $helplink, $helptemplate);

LBWeb::lbfooter();
?>

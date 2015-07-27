<?php
#
# Hier werden die Textbausteine definiert.
#

$region = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

switch ($region) {
    case "de":
	include("languages/de.php");
	break;
    default:
	include("languages/en.php");
	break;
}

?>
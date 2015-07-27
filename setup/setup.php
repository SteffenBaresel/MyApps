<?php
include_once('../config/languages.php');


echo "
<!DOCTYPE html>
<html>
 <head>
  <title>My Apps - Setup</title>
  <link rel='shortcut icon' type='image/x-icon' href='../images/favicon.ico'>
  <script type='text/javascript' src='../jquery-ui-1.11.4.custom/external/jquery/jquery.js'></script>
  <script type='text/javascript' src='../jquery-ui-1.11.4.custom/jquery-ui.js'></script>
  <script type='text/javascript' src='../scripts/MyApps.js'></script>
  <link rel='stylesheet' type='text/css' href='../style/MyApps.css'>
 </head>
 <body>
  <div id='header'>My Apps - Setup</div>
  <div id='setup'>
   <div id='setup1'>
";

echo $lang['setup-description'];

echo "
   </div>
   <div id='setup2'></div>
  </div>
  <div id='button'><button onclick='createtables();'>". $lang['run-setup-button'] ."</button></div>
 </body>
</html>
";

?>
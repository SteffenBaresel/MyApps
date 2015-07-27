<?php
include_once('config/languages.php');
include_once('config/config.php');

// Create connection
$conn = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname'], $config['dbport']);

// Check connection
if ($conn->connect_errno) {
    die('MySQL Datenbank Verbindung schlug fehl (' . $conn->connect_errno . '): '. $conn->connect_error .' Bitte &uuml;berpr&uuml;fen Sie ob eine MySQL Datenbank auf dem Host: '. $config['dbhost'] . ' gestartet ist oder ob die Datenbank: '. $config['dbname'] .' installiert wurde.<br>');
}

if (!($stmt = $conn->prepare("select * from MyAppsTable"))) {
    header("Location: setup/setup.php");
    exit;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
}


echo "
<!DOCTYPE html>
<html>
 <head>
  <title>". $lang['app-title'] ."</title>
  <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico'>
  <script type='text/javascript' src='https://www.google.com/jsapi'></script>
  <script type='text/javascript' src='jquery-ui-1.11.4.custom/external/jquery/jquery.js'></script>
  <script type='text/javascript' src='jquery-ui-1.11.4.custom/jquery-ui.js'></script>
  <script type='text/javascript' src='scripts/MyApps.js'></script>
  <link rel='stylesheet' href='jquery-ui-1.11.4.custom/jquery-ui.css'>
  <link rel='stylesheet' type='text/css' href='style/MyApps.css'>
 </head>
 <script type='text/javascript'>
  $(function() {
    $('#dialog-form').dialog({
	autoOpen: false,
	height: 300,
	width: 500,
	modal: true,
	buttons: {
	    '". $lang['add-button'] ."': addapp,
	    '". $lang['cancel'] ."': function() {
		$('#dialog-form').dialog('close');
		location.reload();
	    }
	}
    });

    $('#dialog-form-edit').hide();
  });

  function opendialog() {
    $('#dialog-form').dialog('open');
  }

  function addapp() {
    $.ajax({
        dataType: 'json',
        url: '/setup/setup.exec.php',
	method: 'POST',
        data: {
	    method: 'ADD-APP-LINK',
	    adsc: $('#adsc').val(),
	    aurl: $('#aurl').val(),
	    aipt: $('#aipt').val()
	},
        success: function( json ) {
	    if(json.ADDED == '1') {
		alert('App added.');
		location.reload();
	    } else {
		alert('Error');
	    }
        },
	error: function( jqXHR, textStatus ) {
	    alert(textStatus);
	}
    });
  }

  function deleteapp(appid) {
    $.ajax({
        dataType: 'json',
        url: '/setup/setup.exec.php',
	method: 'POST',
        data: {
	    method: 'DEL-APP-LINK',
	    aid: appid
	},
        success: function( json ) {
	    if(json.DELETED == '1') {
		$('.app' + appid).hide();
		$('.del' + appid).hide();
		alert('App deleted.');
	    } else {
		alert('Error');
	    }
        },
	error: function( jqXHR, textStatus ) {
	    alert(textStatus);
	}
    });
}

  function editapp(appid) {
    $('#dialog-form-edit').dialog({
	autoOpen: true,
	height: 300,
	width: 500,
	modal: true,
	buttons: {
	    '". $lang['edit-button'] ."': function() {
		changeapp(appid);
		$('#dialog-form-edit').dialog('close');
	    },
	    '". $lang['rem-button'] ."': function() {
		deleteapp(appid);
		$('#dialog-form-edit').dialog('close');
	    },
	    '". $lang['cancel'] ."': function() {
		$('#dialog-form-edit').dialog('close');
	    }
	},
	open: function() {
	    $.ajax({
    		dataType: 'json',
    		url: '/setup/setup.exec.php',
    		data: {
		    method: 'GET-APP-CONFIG',
		    id: appid
		},
    		success: function( json ) {
        	    $('input#edsc').val(json.ADSC);
		    $('input#eurl').val(json.AURL);
		    $('input#eipt').val(json.AIPT);
    		}
	    });
	}
    });
  }

  function changeapp(appid) {
    var dsc = $('#dialog-form-edit input#edsc').val();
    var url = $('#dialog-form-edit input#eurl').val();
    var ipt = $('#dialog-form-edit input#eipt').val();
    $.ajax({
        dataType: 'json',
        url: '/setup/setup.exec.php',
	method: 'POST',
        data: {
	    method: 'UPDATE-APP-LINK',
	    eid: appid,
	    edsc: dsc,
	    eurl: url,
	    eipt: ipt
	},
        success: function( json ) {
	    if(json.UPDATED == '1') {
		$('#main div.app' + appid + ' span:first-child').html(dsc);
		$('#main div.app' + appid + '').removeAttr('onclick').click(function() {
		    window.open(url,'_blank');
		});
		$('#main div.app' + appid + '').removeAttr('style').css('background-image', 'url(\'' + ipt + '\')');
		alert('App updated.');
	    } else {
		alert('Error');
	    }
        },
	error: function( jqXHR, textStatus ) {
	    alert(textStatus);
	}
    });
  }
  function showdel() {
    $('body').removeClass('default');
    $('body').addClass('editor');
    $('#main #deldiv').removeClass('hide');
    $('#main #deldiv').addClass('show');
    $('#add button:last-child').removeClass('hide');
    $('#add button:last-child').addClass('show');
    $('#header').html('". $lang['app-title'] ." - ". $lang['title-delete'] ."');
    document.title = '". $lang['app-title'] ." - ". $lang['title-delete'] ."';
    $('#add button:first-child').html('". $lang['close-button'] ."');
    $('#add button:first-child').unbind('click');
    $('#add button:first-child').attr('onclick', '').click(hidedel);
  }

  function hidedel() {
    $('body').removeClass('editor');
    $('body').addClass('default');
    $('#main #deldiv').removeClass('show');
    $('#main #deldiv').addClass('hide');
    $('#add button:last-child').removeClass('show');
    $('#add button:last-child').addClass('hide');
    $('#header').html('". $lang['app-title'] ."');
    document.title = '". $lang['app-title'] ."';
    $('#add button:first-child').html('". $lang['del-button'] ."');
    $('#add button:first-child').unbind('click');
    $('#add button:first-child').attr('onclick', '').click(showdel);
    location.reload();
  }
 </script>
 <body class='default'>

  <div id='dialog-form' title='". $lang['create-app-title'] ."'>
   <form>
    <fieldset>
     <label for='dsc'>". $lang['label']['dsc'] ."</label>
     <input type='text' name='dsc' id='adsc' placeholder='Appname' class='text ui-widget-content ma-ui-corner-all'><br>
     <label for='url'>". $lang['label']['url'] ."</label>
     <input type='text' name='url' id='aurl' placeholder='http://www.domain.com' class='text ui-widget-content ma-ui-corner-all'><br>
     <label for='ipt'>". $lang['label']['ipt'] ."</label>
     <input type='text' name='ipt' id='aipt' placeholder='http://www.domain.com/image.png' class='text ui-widget-content ma-ui-corner-all'>
    </fieldset>
   </form>
  </div>

  <div id='dialog-form-edit' title='". $lang['edit-app-title'] ."'>
   <form>
    <fieldset>
     <label for='edsc'>". $lang['label']['dsc'] ."</label>
     <input type='text' name='edsc' id='edsc' placeholder='Appname' class='text ui-widget-content ma-ui-corner-all'><br>
     <label for='eurl'>". $lang['label']['url'] ."</label>
     <input type='text' name='eurl' id='eurl' placeholder='http://www.domain.com' class='text ui-widget-content ma-ui-corner-all'><br>
     <label for='eipt'>". $lang['label']['ipt'] ."</label>
     <input type='text' name='eipt' id='eipt' placeholder='http://www.domain.com/image.png' class='text ui-widget-content ma-ui-corner-all'>
    </fieldset>
   </form>
  </div>

  <div id='header'>My Apps</div><div id='searchcontrol'>Loading</div>
  <div id='main'>";

$out_id = NULL;
$out_dsc = NULL;
$out_url = NULL;
$out_icn = NULL;
if (!$stmt->bind_result($out_id, $out_dsc, $out_url, $out_icn)) {
    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

while ($stmt->fetch()) {
    echo "<div id='app' class='app". $out_id ."' onclick='window.open(\"". $out_url ."\", \"_blank\");' style='background-image: url(\"". $out_icn ."\");'><span>". $out_dsc ."</span></div><div id='deldiv' class='hide del". $out_id ."' onclick=\"editapp('". $out_id ."');\" title='". $lang['title-delete'] ."'></div>";
}

echo "  </div>
  <div id='add'><button class='show' onclick='showdel();'>". $lang['del-button'] ."</button><button class='hide' onclick='opendialog();'>". $lang['add-button'] ."</button></div>
  <div id='footer'>&copy; 2015 Steffen Baresel</div>
 </body>
</html>
";

$stmt->close();

?>

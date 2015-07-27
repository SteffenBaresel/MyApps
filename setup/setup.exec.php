<?php
include_once('../config/languages.php');
include_once('../config/config.php');

$response["REGION"] = $region;

// Create connection
$conn = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname'], $config['dbport']);

// Check connection
if ($conn->connect_errno) {
    die($conn->connect_error);
}
$response["MYSQL-CONNECT"] = '1';
$response["DB-CONNECT"] = '1';
$response["DB-NAME"] = $config['dbname'];

header('Content-Type: application/json');

if (isset($_GET['method'])) {
    if ($_GET['method'] == "CREATE-TABLES") {
	if (!($stmt = $conn->prepare("CREATE TABLE IF NOT EXISTS MyAppsTable (id INT(50) UNSIGNED AUTO_INCREMENT PRIMARY KEY, dsc VARCHAR(100) NOT NULL, url VARCHAR(100000) NOT NULL, icon VARCHAR(100000))"))) {
            $response["TABLES"]["1"]["TABLE-PREPARE"] = '0';
            $response["TABLES"]["1"]["TABLE-NAME"] = 'MyAppsTable';
	    $response["TABLES"]["1"]["ERRNO"] = $conn->errno;
	    $response["TABLES"]["1"]["ERROR"] = $conn->error;
	} else {
            $response["TABLES"]["1"]["TABLE-PREPARE"] = '1';
            $response["TABLES"]["1"]["TABLE-NAME"] = 'MyAppsTable';
	    if (!$stmt->execute()) {
        	$response["TABLES"]["1"]["TABLE-CREATE"] = '0';
        	$response["TABLES"]["1"]["TABLE-NAME"] = 'MyAppsTable';
		$response["TABLES"]["1"]["ERRNO"] = $conn->errno;
		$response["TABLES"]["1"]["ERROR"] = $conn->error;
		$response["EXEC"] = '0';
    		$response["MESSAGE"] = $lang['create-tables-error'];
	    } else {
        	$response["TABLES"]["1"]["TABLE-CREATE"] = '1';
        	$response["TABLES"]["1"]["TABLE-NAME"] = 'MyAppsTable';
		$response["EXEC"] = '1';
    		$response["MESSAGE"] = $lang['create-tables-success'];
	    }
	}
        echo json_encode($response);
    } else if ($_GET['method'] == "GET-APP-CONFIG") {
	if (!($stmt = $conn->prepare("SELECT DSC,URL,ICON FROM MyAppsTable WHERE ID=?"))) {
            $response["PREPARE"] = '0';
	    $response["ERRNO"] = $conn->errno;
	    $response["ERROR"] = $conn->error;
	} else {
	    if (!$stmt->bind_param("i", $_GET['id'])) {
        	$response["BIND"] = '0';
		$response["ERRNO"] = $conn->errno;
		$response["ERROR"] = $conn->error;
	    } else {
		if (!$stmt->execute()) {
		    $response["EXEC"] = '0';
		    $response["ERRNO"] = $conn->errno;
		    $response["ERROR"] = $conn->error;
		} else {
		    $out_dsc = NULL;
		    $out_url = NULL;
		    $out_icn = NULL;
		    if (!$stmt->bind_result($out_dsc, $out_url, $out_icn)) {
			$response["BIND-RESULT"] = '0';
			$response["ERRNO"] = $conn->errno;
			$response["ERROR"] = $conn->error;
		    } else {
			while ($stmt->fetch()) {
			    $response["ADSC"] = $out_dsc;
			    $response["AURL"] = $out_url;
			    $response["AIPT"] = $out_icn;
			}
		    }
		}
	    }
	} 
        echo json_encode($response);
    } else if ($_GET['method'] == "SEARCH-APP") {
	if (!($stmt = $conn->prepare("SELECT ID,DSC,URL,ICON FROM MyAppsTable WHERE DSC like ? OR URL like ? ORDER BY DSC ASC"))) {
            $response["PREPARE"] = '0';
	    $response["ERRNO"] = $conn->errno;
	    $response["ERROR"] = $conn->error;
	} else {
	    $text = '%'. $_GET['text'] .'%';
	    if (!$stmt->bind_param("ss", $text, $text)) {
        	$response["BIND"] = '0';
		$response["ERRNO"] = $conn->errno;
		$response["ERROR"] = $conn->error;
		echo json_encode($response);
	    } else {
		if (!$stmt->execute()) {
		    $response["EXEC"] = '0';
		    $response["ERRNO"] = $conn->errno;
		    $response["ERROR"] = $conn->error;
		    echo json_encode($response);
		} else {
		    $out_id = NULL;
		    $out_dsc = NULL;
		    $out_url = NULL;
		    $out_icn = NULL;
		    if (!$stmt->bind_result($out_id,$out_dsc, $out_url, $out_icn)) {
			$response["BIND-RESULT"] = '0';
			$response["ERRNO"] = $conn->errno;
			$response["ERROR"] = $conn->error;
			echo json_encode($response);
		    } else {
			$apps = "";
			while($stmt->fetch()) {
			    $apps.= "{\"AID\":". json_encode($out_id) .",\"ADSC\":". json_encode($out_dsc) .",\"AURL\":". json_encode($out_url) .",\"AIPT\":". json_encode($out_icn) ."},";
			}
			$apps = substr_replace($apps, "", -1);
			echo "{\"TITLE\":\"". $lang['title-delete'] ."\",\"APPS\":[". $apps ."]}";
		    }
		}
	    }
	}
    }
} else {

// POST Requests
if (isset($_POST['method'])) {
    if ($_POST['method'] == "ADD-APP-LINK") {
	if (!($stmt = $conn->prepare("INSERT INTO MyAppsTable(dsc,url,icon) VALUES (?,?,?)"))) {
    	    $response["ADDED"] = '0';
	    $response["ERRNO"] = $conn->errno;
	    $response["ERROR"] = $conn->error;
	} else {
	    if (!$stmt->bind_param("sss", $_POST['adsc'], $_POST['aurl'], $_POST['aipt'])) {
        	$response["ADDED"] = '0';
		$response["ERRNO"] = $conn->errno;
		$response["ERROR"] = $conn->error;
	    } else {
		if (!$stmt->execute()) {
        	    $response["ADDED"] = '0';
		    $response["ERRNO"] = $conn->errno;
		    $response["ERROR"] = $conn->error;
    		} else {
        	    $response["ADDED"] = '1';
    		}
	    }
	}
        echo json_encode($response);
    } else if ($_POST['method'] == "DEL-APP-LINK") {
	if (!($stmt = $conn->prepare("DELETE FROM MyAppsTable WHERE ID=?"))) {
    	    $response["DELETED"] = '0';
	    $response["ERRNO"] = $conn->errno;
	    $response["ERROR"] = $conn->error;
	} else {
	    if (!$stmt->bind_param("i", $_POST['aid'])) {
        	$response["DELETED"] = '0';
		$response["ERRNO"] = $conn->errno;
		$response["ERROR"] = $conn->error;
	    } else {
		if (!$stmt->execute()) {
        	    $response["DELETED"] = '0';
		    $response["ERRNO"] = $conn->errno;
		    $response["ERROR"] = $conn->error;
    		} else {
        	    $response["DELETED"] = '1';
    		}
	    }
	}
        echo json_encode($response);
    } else if ($_POST['method'] == "UPDATE-APP-LINK") {
	if (!($stmt = $conn->prepare("UPDATE MyAppsTable SET dsc=?, url=?, icon=? WHERE id=?"))) {
    	    $response["UPDATED"] = '0';
	    $response["ERRNO"] = $conn->errno;
	    $response["ERROR"] = $conn->error;
	} else {
	    if (!$stmt->bind_param("sssi", $_POST['edsc'], $_POST['eurl'], $_POST['eipt'], $_POST['eid'])) {
        	$response["UPDATED"] = '0';
		$response["ERRNO"] = $conn->errno;
		$response["ERROR"] = $conn->error;
	    } else {
		if (!$stmt->execute()) {
        	    $response["UPDATED"] = '0';
		    $response["ERRNO"] = $conn->errno;
		    $response["ERROR"] = $conn->error;
    		} else {
        	    $response["UPDATED"] = '1';
    		}
	    }
	}
        echo json_encode($response);
    } else {
        $response["EXEC"] = '0';
        $response["MESSAGE"] = $lang['create-tables-error'];
        echo json_encode($response);
    }
} else {
    $response["MESSAGE"] = $lang['no-method-error'];
    echo json_encode($response);
}

// Ende POST Requests

}

$stmt->close();

?>
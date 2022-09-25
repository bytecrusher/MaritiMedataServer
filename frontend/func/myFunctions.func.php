<?php
/**
 * a set of functions for handle some user and board functions.
 *
 * @author: Guntmar Höche
 * @license: TBD
 */

 include_once("password.func.php");
 include_once("dbConfig.func.php");
 require_once(dirname(__FILE__).'/../../configuration.php');

class myFunctions {

	/**
	 * Returns true when the user is checked in, else false
	 */
	public static function is_checked_in() {
		return isset($_SESSION['userid']);
	}

	/**
	 * Returns a random string
	 */
	public static function random_string() {
		if(function_exists('openssl_random_pseudo_bytes')) {
			$bytes = openssl_random_pseudo_bytes(16);
			$str = bin2hex($bytes);
		} else if(function_exists('mcrypt_create_iv')) {
			$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
			$str = bin2hex($bytes);
		} else {
			$str = md5(uniqid(configuration::$md5secretstring, true));
		}
		return $str;
	}

	/**
	 * Returns the URL to the site without the script name
	 */
	public static function getSiteURL() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
	}

  /*
  * Get all of my Board by user id.
  */
  public static function getMyBoards($userid) {
    //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);
    if (!$userid == null) {
      $pdo = dbConfig::getInstance();
      $myboards = $pdo->prepare("SELECT * FROM boardconfig WHERE owner_userid = " . $userid . " ORDER BY id");
      $result = $myboards->execute();
      $myboards2 = $myboards->fetchAll(PDO::FETCH_ASSOC);
      return $myboards2;
    }
  }

  /*
  * Get Board by Board id. Only one dataset will return.
  */
  public static function getBoardById($boardId) {
    if (!$boardId == null) {
      $pdo = dbConfig::getInstance();
      $myboards = $pdo->prepare("SELECT * FROM boardconfig WHERE id = " . $boardId . " ORDER BY id LIMIT 1");
      $result = $myboards->execute();
      $myboards2 = $myboards->fetch(PDO::FETCH_ASSOC);
      return $myboards2;
    }
  }

  /*
  * Get Board by Board macaddress. Only one dataset will return.
  */
  // TODO: get "boardtyp->name" instead of boardtypid. This name should be the name of the firmware.

  public static function getBoardByMac($boardMac) {
    if (!$boardMac == null) {
      $pdo = dbConfig::getInstance();
      $myboards = $pdo->prepare("SELECT * FROM boardconfig WHERE macaddress = '" . $boardMac . "' ORDER BY id LIMIT 1");
      //var_dump($myboards);
      $result = $myboards->execute();
      $myboards2 = $myboards->fetch(PDO::FETCH_ASSOC);
      return $myboards2;
      //return $myboards;
    }
  }

/*
  * Get Board by Board TTN appid and devid. Only one dataset will return.
  */
  // TODO: get "boardtyp->name" instead of boardtypid. This name should be the name of the firmware.

  public static function getBoardByTTN($ttn_app_id, $ttn_dev_id) {
    if ((!$ttn_app_id == null) && (!$ttn_dev_id == null)) {
      $pdo = dbConfig::getInstance();
      $myboards = $pdo->prepare("SELECT * FROM boardconfig WHERE ttn_app_id = '$ttn_app_id' AND ttn_dev_id = '$ttn_dev_id' ORDER BY id LIMIT 1");
      //var_dump($myboards);
      $result = $myboards->execute();
      $myboards2 = $myboards->fetch(PDO::FETCH_ASSOC);
      return $myboards2;
      //return $myboards;
    }
  }

  /*
  * Get all sensors of a given board id.
  */
  public static function getAllSensorsOfBoard($id) {
    $pdo = dbConfig::getInstance();
    //$mysensors2 = $pdo->prepare("SELECT * FROM sensorconfig WHERE boardid = ? ORDER BY id");
    $mysensors2 = $pdo->prepare("SELECT sensorconfig.*, sensortypes.name as boardid FROM sensorconfig, sensortypes WHERE (boardid = ?) and (typid = sensortypes.id) ORDER BY sensorconfig.id; ");
    $mysensors2->execute(array($id));
    $sensorsOfBoard = $mysensors2->fetchAll(PDO::FETCH_ASSOC);
    return $sensorsOfBoard;
  }

    /*
  * Get all sensors of a given board id.
  */
  public static function getAllSensorsOfBoardold($id) {
    $pdo = dbConfig::getInstance();
    $mysensors2 = $pdo->prepare("SELECT * FROM sensorconfig WHERE boardid = ? ORDER BY id");
    //$mysensors2 = $pdo->prepare("SELECT sensortypes.*, sensortypes.name as boardid FROM sensorconfig, sensortypes WHERE (boardid = ?) and (typid = sensortypes.id) ORDER BY sensorconfig.id; ");
    $mysensors2->execute(array($id));
    //$mysensors2->execute($id);
    $sensorsOfBoard = $mysensors2->fetchAll(PDO::FETCH_ASSOC);
    return $sensorsOfBoard;
  }

  /*
  * Get all sensors of a given board id with dashboard and sensor typ 1 (temp).
  */
  public static function getAllSensorsOfBoardWithDashboardAndTemp($id) {
    $pdo = dbConfig::getInstance();
    $mysensors2 = $pdo->prepare("SELECT * FROM sensorconfig WHERE boardid = ? AND onDashboard = 1	AND typid = 1 ORDER BY id");
    $mysensors2->execute(array($id));
    $sensorsOfBoard = $mysensors2->fetchAll(PDO::FETCH_ASSOC);
    return $sensorsOfBoard;
  }

/*
  * Get all sensors of a given board id with dashboard.
  */
  public static function getAllSensorsOfBoardWithDashboard($id) {
    $pdo = dbConfig::getInstance();
    $mysensors2 = $pdo->prepare("SELECT * FROM sensorconfig WHERE boardid = ? AND onDashboard = 1 ORDER BY id");
    $mysensors2->execute(array($id));
    $sensorsOfBoard = $mysensors2->fetchAll(PDO::FETCH_ASSOC);
    return $sensorsOfBoard;
  }

  /*
  * Get all sensors of a given board id with dashboard.
  */
  public static function getAllSensorsOfBoardWithDashboardWithTypeName($id) {
    $pdo = dbConfig::getInstance();
    //$mysensors2 = $pdo->prepare("SELECT *, typeid as sensortypes.name FROM sensorconfig, sensortypes WHERE boardid = ? AND typid = sensortypes.name AND onDashboard = 1 ORDER BY id");
    $mysensors2 = $pdo->prepare("SELECT sensorconfig.*, sensortypes.name as typename FROM sensorconfig, sensortypes WHERE boardid = ? AND typid = sensortypes.id AND onDashboard = 1 ORDER BY id");
    $mysensors2->execute(array($id));
    $sensorsOfBoard = $mysensors2->fetchAll(PDO::FETCH_ASSOC);
    return $sensorsOfBoard;
  }

  /*
  * Get the lastet sensor data of a given sensor id.
  */
  public static function getLatestSensorData($sensorId, $maxNrOfValue = 1) {
    if ($maxNrOfValue >= 1) {
      $pdo = dbConfig::getInstance();
      $mysensors = $pdo->prepare("SELECT * FROM sensordata WHERE sensorid = ? ORDER BY id DESC LIMIT $maxNrOfValue");
      $mysensors->execute(array($sensorId));
      $SensorData = $mysensors->fetchAll(PDO::FETCH_ASSOC);
      return $SensorData;
    }
  }

  /*
  * Get all GPS data of a given (sensor id).
  */
  public static function getAllGpsData($boardid, $maxNrOfValue = 1) {
    if ($maxNrOfValue >= 1) {
      $pdo = dbConfig::getInstance();
      $mysensors = $pdo->prepare("SELECT sensorconfig.*, sensortypes.name as typename FROM `sensorconfig`, sensortypes WHERE boardid = ? AND typid = sensortypes.id AND sensortypes.name = 'GPS'");
      $mysensors->execute(array($boardid));
      //var_dump($boardid);
      $SensorData = $mysensors->fetch(PDO::FETCH_ASSOC);
      //var_dump($SensorData);
      if ($SensorData != false) {
        $myGps = $pdo->prepare("SELECT * FROM `sensordata` WHERE sensorid = ?");
        $myGps->execute(array($SensorData["id"]));
        $myGpsData = $myGps->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($myGpsData);
        return $myGpsData;
      } else {
        return 0;
      }
    }
  }

  /*
  *
  */
  public static function getSensorConfig($id) {
    $pdo = dbConfig::getInstance();
    //$mysensors2 = $pdo->prepare("SELECT * FROM sensorconfig WHERE id = ? AND onDashboard = 1 ORDER BY id LIMIT 1");
    $mysensors2 = $pdo->prepare("SELECT * FROM sensorconfig WHERE id = ? ORDER BY id LIMIT 1");
    $mysensors2->execute(array($id));
    $sensorsOfBoard = $mysensors2->fetch(PDO::FETCH_ASSOC);
    return $sensorsOfBoard;
  }

  /*
  * Get Sensor type object of a given sensor sensorTypId id.
  */
  public static function getSensorType($sensorTypId) {
    $pdo = dbConfig::getInstance();
    $sensortyps = $pdo->prepare("SELECT * FROM sensortypes WHERE id = ? ORDER BY id LIMIT 1");
    $sensortyps->execute(array($sensorTypId));
    $SensorData2 = $sensortyps->fetch(PDO::FETCH_ASSOC);
    return $SensorData2;
  }

  /*
  * Get all Sensor type object of a given sensor sensorTypId id.
  */
  public static function getAllSensorType() {
    $pdo = dbConfig::getInstance();
    $sensortyps = $pdo->prepare("SELECT * FROM sensortypes ORDER BY id");
    $sensortyps->execute();
    $SensorData2 = $sensortyps->fetchAll(PDO::FETCH_ASSOC);

    //$sensortyps = $pdo->prepare("SELECT * FROM sensortypes ORDER BY id ");
    //$resultsensortyps = $sensortyps->execute();
    //var_dump($SensorData2);
    return $SensorData2;
  }

  /*
  * Get all Users from db.
  */
  public static function getAllUsers() {
    $pdo = dbConfig::getInstance();
    $statement = $pdo->prepare("SELECT * FROM users ORDER BY id");
    $result = $statement->execute();
    return $statement->fetchAll();
  }

	/**
	 * Outputs an error message and stops the further exectution of the script.
	 */
	public function error($error_msg) {
		include("common/header.inc.php");
		include("common/error.inc.php");
		include("common/footer.inc.php");
		exit();
	}

  /**
	 * Write $text into a log file.
	 */
  public static function writeToLog($text)
  {
    $format = "csv"; // Possibilities: csv and txt
    $datum_zeit = date("d.m.Y H:i:s");
    $ip = $_SERVER["REMOTE_ADDR"];
    $site = $_SERVER['REQUEST_URI'];
    $browser = $_SERVER["HTTP_USER_AGENT"];
    $monate = array(1 => "Januar", 2 => "Februar", 3 => "Maerz", 4 => "April", 5 => "Mai", 6 => "Juni", 7 => "Juli", 8 => "August", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember");
    $monat = date("n");
    $jahr = date("y");
    $dateiname = dirname(__FILE__)."/logs/log_" . $monate[$monat] . "_$jahr.$format";
    $header = array("Datum", "IP", "Seite", "Browser");
    $infos = array($datum_zeit, $ip, $site, $browser, $text);
    if ($format == "csv") {
      $eintrag2 = '"' . implode('", "', $infos) . '"';
    } else {
      $eintrag2 = implode("\t", $infos);
    }
    $write_header = !file_exists($dateiname);
    $datei = fopen($dateiname, "a");
    if ($write_header) {
      if ($format == "csv") {
        $header_line = '"' . implode('", "', $header) . '"';
      } else {
        $header_line = implode("\t", $header);
      }
      fputs($datei, $header_line . "\n");
    }
    fputs($datei, $eintrag2 . "\n");
    fclose($datei);
  }
}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Calendar API</title>
<link href='https://fonts.googleapis.com/css?family=Raleway:400,800' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="date-picker/jquery.datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>

<body>
<?php    
require_once 'google-api-php-client/vendor/autoload.php';
session_start(); 

// ********************************************************  //
// Values from https://console.developers.google.com
// ********************************************************    //
$client_id = '*****.apps.googleusercontent.com';
$client_secret = '****_****';
$redirect_uri = '****/index.php';
$client = new Google_Client();
$client->setApplicationName("Calendar Details");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');   // Gets refreshtoken
$client->setScopes(array('https://www.googleapis.com/auth/calendar.readonly'));

// for log out.
if (isset($_GET['logout'])) {
	unset($_SESSION['token']);
}

// user accepted access, exchange it
if (isset($_GET['code'])) {	
$client->authenticate($_GET['code']);  
$_SESSION['token'] = $client->getAccessToken();
$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

// user has not authenticated, give them a link to login    
if (!isset($_SESSION['token'])) {
	$authUrl = $client->createAuthUrl();
	print "<a class='login' href='$authUrl'>Connect Me!</a>";
}    

// access established, we can now create our service
if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
	print "<a class='logout' href='****/index.php?logout=1'>LogOut</a><br>";	
	$service = new Google_Service_Calendar($client);  
	$calendarList  = $service->calendarList->listCalendarList();
	
	echo "n.<br />"; 
	$count = 1;
	while(true) {
		//Loop through each calendar
		foreach ($calendarList->getItems() as $calendarListEntry) {
			$color = $calendarListEntry->getBackgroundColor();
			$title = $calendarListEntry->getSummary();		
			$events = $service->events->listEvents($calendarListEntry->id);
			$posIT = strpos($title, "ay"); $posFar = strpos($title, "farrah"); $posHol = strpos($title, "holidays"); //personal cals
			$posKent = strpos($title, "eter");
			
			if ($posIT === false && $posFar === false && $posHol === false){
				echo '<p class="big" style="margin-bottom:20px; display:inline;">AGENT: '. str_replace("Purple Shield - ", "", $title) .' ('.$calendarListEntry->getLocation().') </p>';
				echo '<div class="fc" style="background:'. $color .'"></div><br><br>';			
			
				//Go through each event on current calendar
				foreach ($events->getItems() as $event) {
					$eTitle = $event->getSummary();	
					$theCreator = $event->getCreator()->getEmail();	
					$dateCreated = new DateTime($event->getCreated());
					$dateCreated->setTimeZone(new DateTimeZone('Canada/Atlantic'));
					$dateUpdated = new DateTime($event->getUpdated());
					$dateUpdated->setTimeZone(new DateTimeZone('Canada/Atlantic'));
					$dateStart = "2016-09-10";
					$dateEnd = "2016-10-17";
					
					
					$Kcount = 0; $KcountCan = 0; $KcountCon = 0; $KcountS = 0; $KcountNS = 0; $KcancelP =0;					
					
							if ($posKent !== false){							
								
									echo $count . "  Created: ".  date_format($dateCreated, 'Y-m-d h:i A') .  "  Updated: ".  date_format($dateUpdated, 'Y-m-d h:i A') . " Created by: " . $theCreator . " Title: " . $eTitle ."<br>";	
									$count++;
									
					}
									
				}//foreach event
				
	
				$confirm = $sale + $noSale; $confirmP = ($confirm/$total) * 100;
				$usP = ($us/$total) * 100; $weCancelP = ($weCancel/$us) * 100; $weSoldP = ($weSold/$us) * 100;				
				$mikeP = ($mike/$total) * 100; $heCancelP = ($heCancel/$mike) * 100; $heSoldP = ($heSold/$mike) * 100;	
				$saleP = ($sale/$confirm) * 100;
				$cancelP =($cancel/$total) * 100;				
				
				
				$total=0; $us=0; $mike=0; $sale=0; $noSale = 0; $confirm = 0; $cancel = 0; $weSold = 0; $weCancel = 0; $heSold = 0; $heCancel = 0;
			
			}//if not personal cal
		}//foreach calendar
		echo "V: " . $vcount++."<br>";
		echo "F: " . $fcount."<br>";
		echo "M: " . $mcount."<br>";
		
		
		$pageToken = $calendarList->getNextPageToken();
		if ($pageToken) {
			$optParams = array('pageToken' => $pageToken, 'singleEvents' => true, 'orderBy' => 'startTime', 'maxResults' => 1000, );
			$calendarList = $service->calendarList->listCalendarList($optParams);
		} 
		else { break; }
	
	}//while(true)
}//if access

function check_in_range($dateStart, $dateEnd, $dateCreated){
	// Convert to timestamp
	$dateStart = strtotime($dateStart);
	$dateEnd = strtotime($dateEnd);
	$dateCreated = $dateCreated->format('Y-m-d');
	$dateCreated = strtotime($dateCreated);
	
	// Check that user date is between start & end
	if(($dateCreated >= $dateStart) && ($dateCreated <= $dateEnd)){
		return "true";
	} else{ return "false"; }
		  
}
?>

</body>

</html>
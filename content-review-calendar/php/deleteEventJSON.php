<?php

//--------------------------------------------------------------------------------------------------
// This script deletes an event posted to it from a JSON file.
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

// Require our Event class and datetime utilities
require dirname(__FILE__) . '/utils.php';

	$originalTitle		= $_POST['originalTitle'];


$oldJson = file_get_contents(dirname(__FILE__) . '/../json/events.json');
$eventData = json_decode($oldJson, true);
$match = false;

foreach ($eventData as $key => $event) {
	if ($event['title'] == $originalTitle) {
		unset($eventData[$key]);
		$match = true;
	}
}
if ($match == false) {
	echo 'Record not found';
}

$newJson = json_encode($eventData, true);
if(!is_null($newJson)) {
	file_put_contents(dirname(__FILE__) . '/../json/events.json', $newJson);
}
echo $newJson;
?>
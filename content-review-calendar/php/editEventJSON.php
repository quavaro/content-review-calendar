<?php

//--------------------------------------------------------------------------------------------------
// This script adds events posted to it into a JSON file.
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

// Require our Event class and datetime utilities
require dirname(__FILE__) . '/utils.php';

	$title 				= $_POST['title'];
	$start 				= $_POST['start'];
	$end 				= $_POST['end'];
	$where_event		= $_POST['where_event'];
	$content_event		= $_POST['content_event'];
	$contact			= $_POST['contact'];
	$color				= $_POST['color'];
	$originalTitle		= $_POST['originalTitle'];
	$repeat				= $_POST['repeats'];
	$repetitions		= $_POST['repetitions'];


$oldJson = file_get_contents(dirname(__FILE__) . '/../json/events.json');
$eventData = json_decode($oldJson, true);
$match = false;

foreach ($eventData as $key => $event) {
	//if this is the first instance of the title, edit the array record
	if ($event['title'] == $originalTitle && $match == false) {
		$eventData[$key]['title'] = $title;
		$eventData[$key]['start'] = $start;
		$eventData[$key]['end'] = $end;
		$eventData[$key]['location'] = $where_event;
		$eventData[$key]['description'] = $content_event;
		$eventData[$key]['contact'] = $contact;
		$eventData[$key]['color'] = $color;
		$eventData[$key]['repeat'] = $repeat;
		$eventData[$key]['repetitions'] = $repetitions;
		$eventData[$key]['originalTitle'] = $title;
		$match = true;
	}
	//else if this is a repeat instance of the title, delete it. New repeat instances set up below
	elseif ($event['title'] == $originalTitle && $match == true) {
		unset($eventData[$key]);
	}
}
//if no event with the posted title was found create a new one
if ($match == false) {
	$eventData[] = (object) array('title' => $title, 'start' => $start, 'end' => $end, 'location' => $where_event, 'description' => $content_event, 'contact' => $contact, 'color' => $color, 'repeat' => $repeat, 'repetitions' => $repetitions, 'originalTitle' => $originalTitle);
}

//create any requested repeat instances
if ($repeat!='never') {
	while ($repetitions > 0) {
		$start = date('Y-m-d H:i', strtotime($start . '+1 ' . $repeat));
		$end =  date('Y-m-d H:i', strtotime($end . '+1 ' . $repeat));
		$eventData[] = (object) array('title' => $title, 'start' => $start, 'end' => $end, 'location' => $where_event, 'description' => $content_event, 'contact' => $contact, 'color' => $color, 'originalTitle' => $originalTitle);
		$repetitions--;
	}
}

$newJson = json_encode($eventData, true);
if(!is_null($newJson)) {
	file_put_contents(dirname(__FILE__) . '/../json/events.json', $newJson);
}
echo $newJson;
?>
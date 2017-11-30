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
	$location			= $_POST['location'];
	$description		= $_POST['description'];
	$contact			= $_POST['contact'];
	$color				= $_POST['color'];
	$originalTitle		= $_POST['originalTitle'];
	$repeat				= $_POST['repeat'];
	$repetitions		= $_POST['repetitions'];

function ordinaltext($nthday) {
	switch ($nthday) {
		case 0:
			$nthday = "first";
			break;
		case 1:
			$nthday = "second";
			break;
		case 2:
			$nthday = "third";
			break;
		case 3:
			$nthday = "fourth";
			break;
		case 4:
			$nthday = "last";
			break;
		}
		return $nthday;
}


$oldJson = file_get_contents(dirname(__FILE__) . '/../json/events.json');
$eventData = json_decode($oldJson, true);
$match = false;
$end= date('Y-m-d', strtotime($end . '+1 day'));

foreach ($eventData as $key => $event) {
	//if this is the first instance of the title, edit the array record
	if ($event['title'] == $originalTitle && $match == false) {
		$eventData[$key]['title'] = $title;
		$eventData[$key]['start'] = $start;
		$eventData[$key]['end'] = $end;
		$eventData[$key]['location'] = $location;
		$eventData[$key]['description'] = $description;
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
	$eventData[] = (object) array('title' => $title, 'start' => $start, 'end' => $end, 'location' => $location, 'description' => $description, 'contact' => $contact, 'color' => $color, 'repeat' => $repeat, 'repetitions' => $repetitions, 'originalTitle' => $originalTitle);
}

//create any requested repeat instances
if ($repeat!='never') {
	//if this is a "second tuesday of the month" type of request
	if ($repeat=='monthWeek') {
		$j= $repetitions;
		$startweekday = date('l', strtotime($start)); //get text representation of day, e.g. Monday

		$startmonthday = date('j', strtotime($start)); // day of month, 1 to 31
		$endmonthday = date('j', strtotime($end));

		$eventlength = $endmonthday - $startmonthday;

		$startdaycount = floor(($startmonthday - 1) / 7);

		$startordinal = ordinaltext($startdaycount);
	
		for ($i = 1; $j > 0; $j--, $i++) {
			
			$start = date('Y-m-d', strtotime($startordinal . ' ' . $startweekday . ' of +' . $i . ' month'));
			$end =  date('Y-m-d', strtotime($start . ' + ' . $eventlength . ' days'));
			$eventData[] = (object) array('title' => $title, 'start' => $start, 'end' => $end, 'location' => $location, 'description' => $description, 'contact' => $contact, 'color' => $color, 'repeat' => $repeat, 'repetitions' => $repetitions, 'originalTitle' => $originalTitle);
		}

	}
	else {
		$i = $repetitions;
		while ($i > 0) {
			$start = date('Y-m-d', strtotime($start . '+1 ' . $repeat));
			$end =  date('Y-m-d', strtotime($end . '+1 ' . $repeat));
			
			$eventData[] = (object) array('title' => $title, 'start' => $start, 'end' => $end, 'location' => $location, 'description' => $description, 'contact' => $contact, 'color' => $color, 'repeat' => $repeat, 'repetitions' => $repetitions, 'originalTitle' => $originalTitle);
			$i--;
		}
	}
	
}

$newJson = json_encode($eventData, true);
if(!is_null($newJson)) {
	file_put_contents(dirname(__FILE__) . '/../json/events.json', $newJson);
}
echo $newJson;
?>
<?php
	// Enabling error reporting
	error_reporting(-1);
	ini_set('display_errors', 'On');

	require_once __DIR__ . '/firebase.php';
	require_once __DIR__ . '/push.php';

	$firebase = new Firebase();
	$push = new Push();

	// optional payload
	$payload = array();
	$payload['team'] = 'India';
	$payload['score'] = '5.6';

	// notification title
	$title = 'abc';
	 
	// notification message
	$message = 'xyz';
	 
	// push type - single user / topic
	$push_type = 'individual';
	 
	// whether to include to image or not
	$include_image = isset($_GET['include_image']) ? TRUE : FALSE;


	$push->setTitle($title);
	$push->setMessage($message);
	if ($include_image) {
		$push->setImage('http://api.androidhive.info/images/minion.jpg');
	} else {
		$push->setImage('');
	}
	$push->setIsBackground(FALSE);
	$push->setPayload($payload);


	$json = '';
	$response = '';

	if ($push_type == 'topic') {
		$json = $push->getPush();
		$response = $firebase->sendToTopic('global', $json);
	} else if ($push_type == 'individual') {
		$json = $push->getPush();
		$regId = isset($_GET['regId']) ? $_GET['regId'] : 'eQWpexZYvOE:APA91bHiyiIaa9FtkQd26xetqKII-omWnvzUanu1X-8b1V8mHlZoCr34qcpgHe4YZuiX7BYSJ7ugX4EmfbhliiQv12L8eJmHvI99W14DJB3Mm-eQJj2-UPPyYrYFMzvfLbbgITwmq2Yu';
		$response = $firebase->send($regId, $json);
	}
?>
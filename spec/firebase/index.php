<?php

 
        require_once __DIR__ . '/firebase.php';
        require_once __DIR__ . '/push.php';
 
        $firebase = new Firebase();
        $push = new Push();
 
        // optional payload
        $payload = array();
        $payload['team'] = 'India';
        $payload['score'] = '5.6';
 
        // notification title
        $title = 'title test';
         
        // notification message
        $message = 'msg test';
         
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
			echo 'a';
            $json = $push->getPush();
            $regId = 'dfNgM-PJeCE:APA91bH6Gg_aq-rLXnXQaQn7-BfOIQdTdgSKXNW-R7nMm-Vl6I4-GLjEen_nIUHPtR7tjiTdRQVxT98rYc9VeEQabWxheR1ZzszB06AdisWtCEdPVhBB0I4W613lPAXx8_evS1M-0dhe';
            $response = $firebase->send($regId, $json);
			var_dump($response);
        }
        ?>
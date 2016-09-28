<?php

/**
 * Description of CaremanagerLogicComponent
 *
 * @author nao
 */
App::uses('Component', 'Controller');
App::import('Component', 'Base');
App::import('Vendor', 'ApnsPHP/Autoload');
App::uses('ApnsPHP_Push', 'Lib/ApnsPHP');
App::import('Vendor', 'GCM/GCMPushMessage');

class NotificationComponent extends BaseComponent {
	public $Controller;
    public $iOS_badge;
    public $iOS_sound;
	public $ANDROID_API_KEY = 'AIzaSyAet7R-mbNB7xEV81pFKxXK2Yaxegvn-PM'; //production key
	//public $ANDROID_API_KEY = 'AIzaSyB5zD_y0voU6MitxjHodjyG6aye-D0_UbM'; //test key

    /**
     * 	$message string
     * 	$device token
     * 	$type IOS, ANDROID
     */
    public function Send($message, $device, $type) {
        //ignore_user_abort(true);
        //set_time_limit(0);
        print('Start ...');
        //ios devices
		$respose = '';
        if ($type == 1) {
            $respose = array('ios' => $this->SendIos($message, $device));
        }
        //android devices
        else {
            $respose = array('android' => $this->SendAndroid($message, ($device)));
        }
        print('Finish ...');
		return $respose;
    }

    public function SendIos($msg, $token) {
        // Report all PHP errors
        error_reporting(-1);

        // Using Autoload all classes are loaded on-demand
        // Instanciate a new ApnsPHP_Push object
        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            'Vendor/ApnsPHP/KuilimaDistributionAPNS.pem'
        );


        // Connect to the Apple Push Notification Service
        $push->connect();

        // Instantiate a new Message with a single recipient
        $message = new ApnsPHP_Message();
		if(is_array($token)) {
			foreach($token as $v) {
				if (preg_match('~^[a-f0-9]{64}$~i', $v)) {
					$message->addRecipient($v);
				}
			}
		}
		else {
			if (preg_match('~^[a-f0-9]{64}$~i', $token)) {
				$message->addRecipient($token);
			}

		}

		$recipients = $message->getRecipients();
		if(!empty($recipients)) {
			// Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
			// over a ApnsPHP_Message object retrieved with the getErrors() message.
			$message->setCustomIdentifier("Message-Badge-3");

			// Set badge icon to "3"
			//$message->setBadge(3);

			// Set a simple welcome text
			$message->setText($msg);

			// Play the default sound
			$message->setSound();

			// Set a custom property
			//$message->setCustomProperty('acme2', array('bang', 'whiz'));

			// Set another custom property
			//$message->setCustomProperty('acme3', array('bing', 'bong'));

			// Set the expiry value to 30 seconds
			//$message->setExpiry(30);

			// Add the message to the message queue
			$push->add($message);

			// Send all messages in the message queue
			$push->send();

			// Disconnect from the Apple Push Notification Service
			$push->disconnect();

			// Examine the error message container
			$aErrorQueue = $push->getErrors();
			if (!empty($aErrorQueue)) {
				return $aErrorQueue;
			}
		}

    }


    public function SendAndroid($message, $receives = array()) {
        print('Start send android...');

        //notification object
        $notification = new GCMPushMessage($this->ANDROID_API_KEY);
		$notification->android_title = 'test_title';
        //add devices
        $notification->setDevices($receives);
        //start send
        $response = $notification->send($message, $notification->android_title);
        //print(json_decode($response, $assoc_array = false));

        print('End send android...');

        return json_decode($response, TRUE);
    }

	public function logErrorIos($logs, $file) {
		//log ios
		foreach ($logs as $key => $log) {
			$deviceIos = $log['MESSAGE']->getRecipients();
			CakeLog::write($file, 'Token:' . $deviceIos[$key-1] . ' ' . $log['ERRORS'][0]['statusMessage']);
		}
	}

	public function logErrorAndroid($logs, $devices, $file) {
		//log ios
		foreach ($logs['results'] as $key => $log) {
			if(isset($log['error'])) {
				CakeLog::write($file, 'Token:' . $devices[$key] . ' ' . $log['error']);
			}
		}
	}

}

?>
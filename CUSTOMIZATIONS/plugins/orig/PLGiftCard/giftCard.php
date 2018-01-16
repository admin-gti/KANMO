<?php
	require_once('../../config/gticonfig.php');

	function is_online()
	{
        $check_if_online = checkdnsrr('google.com', 'ANY');
        $check_if_api_connected = checkdnsrr(SYSCONFIG_GIFTCARD_DOMAIN, 'ANY');
        $i = 0;
        if(!$check_if_online AND !$check_if_api_connected):
    	    while(!$check_if_online AND !$check_if_api_connected):
    	    	if($i == 10):
    	    		if($check_if_online AND !$check_if_api_connected):
    	    			die(json_encode(array('status_code' => 408, 'message' => 'Unable to retrieve external resources. Your network connection is too slow!')));
    	    		elseif(!$check_if_online AND !$check_if_api_connected):
    	    			die(json_encode(array('status_code' => 500, 'message' => 'Unable to connect to the internet. Please check your internet connection!')));
    	    		else:
    	    			echo json_encode(array('status_code' => 200, 'message' => 'SUCCESS'));
    	    		endif;
    	    	endif;
    	        if(!$check_if_online):
    	        	$check_if_online = checkdnsrr('google.com', 'ANY');
    	        endif;
    	        if(!$check_if_api_connected):
    	        	$check_if_api_connected = checkdnsrr(SYSCONFIG_GIFTCARD_DOMAIN, 'ANY');
    	        endif;
    	        $i++;
    	        usleep(500000);
    	    endwhile;
    	else:
    		echo json_encode(array('status_code' => 200, 'message' => 'SUCCESS'));
       	endif;
	}

	$gc_api_auth_key = base64_encode(SYSCONFIG_GIFTCARD_USERID.':'.SYSCONFIG_GIFTCARD_PASSKEY);

	$action 	= NULL;
	$cardNumber = NULL;
	$email 		= NULL;
	$gc_data 	= NULL;

	if($_SERVER['REQUEST_METHOD'] == 'GET'):
		if(!isset($_GET['action'])):
			die(json_encode(array('status_code' => 400, 'message' => 'No parameters detected!')));
		else:
			if($_GET['action'] == ''):
				die(json_encode(array('status_code' => 400, 'message' => 'No parameters value detected !')));
			else:
				$action 	= $_GET['action'];
			endif;
		endif;
		if(isset($_GET['cardNumber']) AND $_GET['cardNumber'] != ''):
			$cardNumber = $_GET['cardNumber'];
		endif;
		if(isset($_GET['email']) AND $_GET['email'] != ''):
			$email = $_GET['email'];
		endif;
	elseif($_SERVER['REQUEST_METHOD'] == 'POST'):
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);
		$action 	= $request->action;
		if($action == 'rechargeGC'):
			$gc_data = $request->data;
		endif;
		if($action == 'redeemGC'):
			$gc_data = $request->data;
		endif;
	endif;

	switch ($action):
		case 'checkGCStatus':
			echo checkGCStatus($gc_api_auth_key, $cardNumber);
			break;
		case 'rechargeGC':
			echo rechargeGC($gc_api_auth_key, $gc_data);
			break;
		case 'getCustomerID':
			echo getCustomerID($gc_api_auth_key, $email);
			break;
		case 'redeemGC':
			echo redeemGC($gc_api_auth_key, $gc_data);
			break;
		case 'checkNetworkStatus':
			echo is_online();
			break;
		default:
			echo json_encode(array('error' => 'Action value is not valid !'));
			break;
	endswitch;

	/**
	* CHECK GC STATUS METHOD
	* This method will serve as  the utility function for
	* checking gift card status if valid, exist or not
	* @param string $gc_api_auth_key 	- API authentication
	* @param string $cardNumber 		- Gift Card Number
	* @return Gift Card status details
	*/
	function checkGCStatus($gc_api_auth_key, $cardNumber)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_GIFTCARD_URL.SYSCONFIG_GIFTCARD_FETCH_GC.$cardNumber,
		    CURLOPT_USERAGENT 		=> 'Check Gift Card Status',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$gc_api_auth_key)
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		$xml = simplexml_load_string($resp);

		$json = json_encode($xml);

		$array = json_decode($json, TRUE);
		// return the data result fetch from the api url
		return json_encode($array);
	}

	/**
	* RECHARGE GIFT CARD METHOD
	* This method will serve as  the utility function for
	* for recharging gift card
	* @param string $gc_api_auth_key 	- API authentication
	* @param json $gc_data 				- request content
	* @return Gift Card Details
	*/

	function rechargeGC($gc_api_auth_key, $gc_data)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_GIFTCARD_URL.SYSCONFIG_GIFTCARD_RECHARGE_GC,
		    CURLOPT_USERAGENT 		=> 'Recharge Gift Card',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$gc_api_auth_key, 'Content-Type: text/xml'),
		    CURLOPT_POST 			=> 1,
		    CURLOPT_POSTFIELDS 		=> $gc_data,
		    CURLINFO_HEADER_OUT 	=> 1
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		$xml = simplexml_load_string($resp);

		$json = json_encode($xml);

		$array = json_decode($json, TRUE);
		// return the data result fetch from the api url
		return json_encode($array);
	}

	/**
	* GET CUSTOMER ID METHOD
	* This method will serve as  the utility function for
	* checking gift card status if valid, exist or not
	* @param string $gc_api_auth_key 	- API authentication
	* @param string $email 				- Customer Email
	* @return Customer details
	*/
	function getCustomerID($gc_api_auth_key, $email)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_GIFTCARD_GET_CUST_ID.'&email='.$email,
		    CURLOPT_USERAGENT 		=> 'Check Gift Card Status',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$gc_api_auth_key)
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		// $xml = simplexml_load_string($resp);

		// $json = json_encode($xml);

		// $array = json_decode($json, TRUE);
		// return the data result fetch from the api url
		return $resp;
	}

	/**
	* REDEEM GIFT CARD METHOD
	* This method will serve as  the utility function for
	* for redeeming gift card
	* @param string $gc_api_auth_key 	- API authentication
	* @param json $gc_data 				- request content
	* @return Gift Card Details
	*/

	function redeemGC($gc_api_auth_key, $gc_data)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_GIFTCARD_URL.SYSCONFIG_GIFTCARD_REDEEM_GC,
		    CURLOPT_USERAGENT 		=> 'Redeem Gift Card',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$gc_api_auth_key, 'Content-Type: text/xml'),
		    CURLOPT_POST 			=> 1,
		    CURLOPT_POSTFIELDS 		=> $gc_data,
		    CURLINFO_HEADER_OUT 	=> 1
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		$xml = simplexml_load_string($resp);

		$json = json_encode($xml);

		$array = json_decode($json, TRUE);
		// return the data result fetch from the api url
		return json_encode($array);
	}
?>
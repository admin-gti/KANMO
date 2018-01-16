<?php
	require_once('../../config/gticonfig.php');

	function is_online()
	{
        $check_if_online = checkdnsrr('google.com', 'ANY');
        $check_if_api_connected = checkdnsrr(SYSCONFIG_CRM_DOMAIN, 'ANY');
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
    	        	$check_if_api_connected = checkdnsrr(SYSCONFIG_CRM_DOMAIN, 'ANY');
    	        endif;
    	        $i++;
    	        usleep(500000);
    	    endwhile;
    	else:
    		echo json_encode(array('status_code' => 200, 'message' => 'SUCCESS'));
       	endif;
	}

	$crm_api_auth_key = base64_encode(SYSCONFIG_CRM_USERID.':'.SYSCONFIG_CRM_PASSKEY);

	$action 	= NULL;
	$code 		= NULL;
	$mobile 	= NULL;
	$voucher_data 	= NULL;
	$customer_data 	= NULL;

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
		if(isset($_GET['code']) AND $_GET['code'] != ''):
			$code = $_GET['code'];
		endif;
		if(isset($_GET['mobile']) AND $_GET['mobile'] != ''):
			$mobile = $_GET['mobile'];
		endif;
	elseif($_SERVER['REQUEST_METHOD'] == 'POST'):
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);
		$action 	= $request->action;
		if($action == 'redeemVoucher'):
			$voucher_data = $request->voucherData;
		elseif($action == 'updateCutomerDetails'):
			$customer_data = $request->customerData;
		endif;
	endif;

	switch ($action):
		case 'checkVoucherStatus':
			echo checkVoucherStatus($crm_api_auth_key, $code, $mobile);
			break;
		case 'redeemVoucher':
			echo redeemVoucher($crm_api_auth_key, $voucher_data);
			break;
		case 'checkNetworkStatus':
			echo is_online();
			break;
		case 'updateCutomerDetails':
			echo updateCutomerDetails($crm_api_auth_key, $customer_data);
			break;
		default:
			echo json_encode(array('error' => 'Action value is not valid !'));
			break;
	endswitch;

	/**
	* CHECK VOUCHER STATUS METHOD
	* This method will serve as  the utility function for
	* checking voucher status if valid, redeemable or not
	* @param string $crm_api_auth_key 	- API authentication
	* @param string $code 				- Voucher Code
	* @param string $mobile 			- Customer Mobile Number
	* @return Voucher status details
	*/

	function checkVoucherStatus($crm_api_auth_key, $code, $mobile)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_CRM_CHECKVOUCHERSTATUS.'&details=true&code='.$code.'&mobile='.$mobile,
		    CURLOPT_USERAGENT 		=> 'Check Voucher Status',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$crm_api_auth_key)
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		// return the data result fetch from the api url
		return $resp;
	}

	/**
	* REDEEM VOUCHER METHOD
	* This method will serve as  the utility function for
	* checking voucher status if valid, redeemable or not
	* @param string $crm_api_auth_key 	- API authentication
	* @param json $voucher_data 		- request content
	* @return Voucher status details
	*/

	function redeemVoucher($crm_api_auth_key, $voucher_data)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_CRM_REDEEMVOUCHER,
		    CURLOPT_USERAGENT 		=> 'Redeem Voucher',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$crm_api_auth_key, 'Content-Type: application/json'),
		    CURLOPT_POST 			=> 1,
		    CURLOPT_POSTFIELDS 		=> json_encode($voucher_data),
		    CURLINFO_HEADER_OUT 	=> 1
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		// return the data result fetch from the api url
		return $resp;
	}

	/**
	* UPDATE CUSTOMER DETAILS METHOD
	* This method will serve as  the utility function for
	* updating customer details and passing customer SID to CRM
	* @param string $crm_api_auth_key 	- API authentication
	* @param json $customer_data 		- request content
	* @return Customer Update Details status
	*/
	function updateCutomerDetails($crm_api_auth_key, $customer_data)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_CRM_UPDATECUSTOMERDETAILS,
		    CURLOPT_USERAGENT 		=> 'Redeem Voucher',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$crm_api_auth_key, 'Content-Type: application/json'),
		    CURLOPT_POST 			=> 1,
		    CURLOPT_POSTFIELDS 		=> json_encode($customer_data),
		    CURLINFO_HEADER_OUT 	=> 1
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Close request to clear up some resources
		curl_close($curl);

		// return the data result fetch from the api url
		return $resp;
	}

?>
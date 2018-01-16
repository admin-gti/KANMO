<?php
	require_once('../../config/gticonfig.php');

	$crm_api_auth_key = base64_encode(SYSCONFIG_CRM_USERID.':'.SYSCONFIG_CRM_PASSKEY);

	$action 	= NULL;
	$mobileNo 	= NULL;
	$points 	= NULL;
	$v_code 	= NULL;
	$loyaltyData = NULL;

	$db = new SQLite3('C:\Program Files (x86)\Genie Technologies Inc\Kanmo Prism Interface\DB\prismSO_db.db');

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
		if(isset($_GET['mobile']) AND $_GET['mobile'] != ''):
			$mobileNo = $_GET['mobile'];
		endif;
		if(isset($_GET['points']) AND $_GET['points'] != ''):
			$points = $_GET['points'];
		endif;
		if(isset($_GET['v_code']) AND $_GET['v_code'] != ''):
			$v_code = $_GET['v_code'];
		endif;
	elseif($_SERVER['REQUEST_METHOD'] == 'POST'):
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);
		$action 	= $request->action;
		if($action == 'redeemPoints'):
			$loyaltyData = $request->loyaltyData;
		endif;
	endif;

	switch ($action):
		// case 'checkNetworkStatus':
		// 	echo is_online();
		// 	break;
		case 'issueValidationCode':
			echo issueValidationCode($crm_api_auth_key, $mobileNo, $points);
			break;
		case 'checkPointsStatus':
			echo checkPointsStatus($crm_api_auth_key, $mobileNo, $points, $v_code);
			break;
		case 'redeemPoints':
			echo redeemPoints($crm_api_auth_key, $loyaltyData, $db);
			break;
		default:
			echo json_encode(array('error' => 'Action value is not valid !'));
			break;
	endswitch;


	/**
	* ISSUE VALIDATION CODE METHOD
	* This method will serve as  the utility function for
	* issuing validation code to customer through OTP
	* @param string $crm_api_auth_key 	- API authentication
	* @param string $points 			- Points Value input
	* @param string $mobileNo 			- Customer Mobile Number
	* @return Validation details
	*/

	function issueValidationCode($crm_api_auth_key, $mobileNo, $points)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_CRM_ISSUEVALIDATIONCODE.'&mobile='.$mobileNo.'&points='.$points,
		    CURLOPT_USERAGENT 		=> 'Issue Loyalty Validation Code',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$crm_api_auth_key),
		    CURLOPT_CONNECTTIMEOUT  => 3,
		    CURLOPT_TIMEOUT  		=> 5
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);

		// Get HTTP response status code
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// Close request to clear up some resources
		curl_close($curl);

		if($http_status_code == 0):
			$resp = json_encode(array('response' => array('status' => array('success' => 'false', 'code' => 0, 'message' => 'Unable to retrieve external resources'))));
		endif;

		// return the data result fetch from the api url
		return $resp;
	}


	/**
	* CHECK POINTS STATUS METHOD
	* This method will serve as  the utility function for
	* checking points status if redeemable or not
	* @param string $crm_api_auth_key 	- API authentication
	* @param string $points 			- Points Value input
	* @param string $mobileNo 			- Customer Mobile Number
	* @return Points Status details
	*/

	function checkPointsStatus($crm_api_auth_key, $mobileNo, $points, $v_code)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_CRM_CHECKPOINTSSTATUS.'&mobile='.$mobileNo.'&points='.$points.'&validation_code='.$v_code,
		    CURLOPT_USERAGENT 		=> 'Check Points Status',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$crm_api_auth_key),
		    CURLOPT_CONNECTTIMEOUT  => 3,
		    CURLOPT_TIMEOUT  		=> 5
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		// Get HTTP response status code
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		// Close request to clear up some resources
		curl_close($curl);

		if($http_status_code == 0):
			$resp = json_encode(array('response' => array('status' => array('success' => 'false', 'code' => 0, 'message' => 'Unable to retrieve external resources'))));
		endif;

		// return the data result fetch from the api url
		return $resp;
	}

	/**
	* REDEEM POINTS METHOD
	* This method will serve as  the utility function for
	* redeeming loyalty points
	* @param string $crm_api_auth_key 	- API authentication
	* @param json $gc_data 				- request content
	* @return Points Status details
	*/

	function redeemPoints($crm_api_auth_key, $loyaltyData, $db)
	{
		// // Get cURL resource
		// $curl = curl_init();
		// // Set some options - we are passing in a useragent too here
		// curl_setopt_array($curl, array(
		//     CURLOPT_RETURNTRANSFER 	=> 1,
		//     CURLOPT_URL 			=> SYSCONFIG_CRM_URL.SYSCONFIG_CRM_REDEEMPOINTS,
		//     CURLOPT_USERAGENT 		=> 'Redeem Points',
		//     CURLOPT_SSL_VERIFYPEER 	=> 0,
		//     CURLOPT_SSL_VERIFYHOST 	=> 0,
		//     CURLOPT_HTTPHEADER 		=> array('Authorization: Basic '.$crm_api_auth_key, 'Content-Type: application/json'),
		//     CURLOPT_POST 			=> 1,
		//     CURLOPT_POSTFIELDS 		=> json_encode($loyaltyData),
		//     CURLINFO_HEADER_OUT 	=> 1
		// ));
		// // Send the request & save response to $resp
		// $resp = curl_exec($curl);
		
		// // Close request to clear up some resources
		// curl_close($curl);

		// // return the data result fetch from the api url
		// return $resp;

		$rawData = json_encode($loyaltyData);

		$query = $db->exec("INSERT INTO oap_upload (oau_raw, oau_date, oau_type, oau_method, oau_header) VALUES ('".$rawData."', '".date('Y-m-d')."', 'C', 'POST', '".SYSCONFIG_CRM_URL.SYSCONFIG_CRM_REDEEMPOINTS."')");
		if($query):
			echo json_encode(array('status_code' => 200, 'message' => 'OK'));
		else:
			echo json_encode(array('status_code' => 500, 'message' => 'INTERNAL SERVER ERROR'));
		endif;
	}
?>
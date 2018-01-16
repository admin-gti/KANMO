<?php
	session_start();
	require_once('../../config/gticonfig.php');

	$db = new SQLite3('C:\Program Files (x86)\Genie Technologies Inc\Kanmo Prism Interface\DB\prismSO_db.db');

	if(!isset($_SESSION['api_username']) OR is_null($_SESSION['api_username']) OR $_SESSION['api_username'] == ''):
		$_SESSION['api_username'] = SYSCONFIG_MAGENTO_USERID;
	endif;

	if(!isset($_SESSION['api_password']) OR is_null($_SESSION['api_password']) OR $_SESSION['api_password'] == ''):
		$_SESSION['api_password'] = SYSCONFIG_MAGENTO_PASSKEY;
	endif;

	if(!isset($_SESSION['api_access_token']) OR is_null($_SESSION['api_access_token']) OR $_SESSION['api_access_token'] == ''):
		getAccessToken(SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_GETADMINTOKEN);
	endif;

	$action 		= NULL;
	if(!isset($_GET['action'])):
		echo json_encode(array('error_code' => 0, 'error_description' => 'No parameters detected !'));
	else:
		if($_GET['action'] == ''):
			echo json_encode(array('error_code' => 1, 'error_description' => 'No parameters value detected !'));
		else:
			$action 		= $_GET['action'];
		endif;
	endif;

	switch ($action):
		case 'getAccessToken':
			echo getAccessToken(array('username' => $_SESSION['api_username'], 'password' => $_SESSION['api_password']));
			break;
		case 'getCustomerEmail':
			echo getCustomerEmail($_SESSION['api_access_token']);
			break;
		case 'getCustomerDetails':
			echo getCustomerDetails($_SESSION['api_access_token']);
			break;
		case 'getCartItems':
			echo getCartItems($_SESSION['api_access_token']);
			break;
		case 'getWishListItems':
			echo getWishListItems($_SESSION['api_access_token']);
			break;
		case 'getGiftRegistryItems':
			echo getGiftRegistryItems($_SESSION['api_access_token']);
			break;
		case 'deleteCartItem':
			echo deleteCartItem($_SESSION['api_access_token'], $db);
			break;
		case 'deleteWishListItem':
			echo deleteWishListItem($_SESSION['api_access_token'], $db);
			break;
		case 'deleteGiftRegistryItem':
			echo deleteGiftRegistryItem($_SESSION['api_access_token'], $db);
			break;
		// case 'checkNetworkStatus':
		// 	echo is_online();
		// 	break;
		default:
			echo json_encode(array('error' => 'Action value is not valid !'));
			break;
	endswitch;


	/**
	* GET ACCESS TOKEN METHOD
	* This method will serve as  the utility function for
	* retrieving access token from the API for authorization
	* to access the protected resources
	* @param string $url 		- API access token url
	* @param array $post_data 	- user credentials for the API
	*							 to get the access token
	* @return array access token value and HTTP status code
	*/
	function getAccessToken($post_data = array(), $request_origin = NULL)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_GETADMINTOKEN,
		    CURLOPT_USERAGENT 		=> 'Access Token',
		    CURLOPT_POST 			=> 1,
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_POSTFIELDS 		=> $post_data,
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
			$result = array('status_code' => 0);
		else:
			// replace the double qoute in the token
			$token = str_replace('"', '', $resp);

			// composing the result to be return
			$result = array('status_code' => $http_status_code);

			// save access token to php session
			$_SESSION['api_access_token'] = $token;

			if(!is_null($request_origin)):
				switch ($request_origin):
					case 'deleteCartItem':
						echo deleteCartItem($_SESSION['api_access_token'], $db);
						break;
					case 'deleteWishListItem':
						echo deleteWishListItem($_SESSION['api_access_token'], $db);
						break;
					case 'deleteGiftRegistryItem':
						echo deleteGiftRegistryItem($_SESSION['api_access_token'], $db);
						break;
				endswitch;
			endif;
		endif;

		// return the data result fetch from the api url
		return json_encode($result, true);
	}

	/**
	* GET CUSTOMER EMAIL METHOD
	* This method will serve as  the utility function for
	* retrieving all CART items from the resource server based on
	* the given customer id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return array all items in carts
	*/
	function getCustomerEmail($access_token)
	{
		// Get cURL resource
		$curl = curl_init();

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_GETCUSTOMERDETAILS.'?searchCriteria[filterGroups][0][filters][0][field]=email&searchCriteria[filterGroups][0][filters][0][value]='.$_GET['email'].'&searchCriteria[filterGroups][0][filters][0][conditionType]=eq',
		    CURLOPT_USERAGENT 		=> 'Get Customer Details',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
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
			$resp = array('status_code' => 0);
		else:
			// push the status code value to the $resp array
			$resp 					= json_decode($resp, true);
			$resp['result'] 		= $resp;
			$resp['status_code'] 	= $http_status_code;
			if(!is_null($resp) AND array_key_exists('items', $resp)):
				$resp['length'] 	= count($resp['items']);
			else:
				$resp['length'] 	= 0;
			endif;

			foreach ($resp as $key => $value):
				if($key != 'result' AND $key != 'status_code' AND $key != 'length'):
					unset($resp[$key]);
				endif;
			endforeach;
		endif;


		// return the data result fetch from the api url
		return json_encode($resp, true);
	}


	/**
	* GET CUSTOMER DETAILS METHOD
	* This method will serve as  the utility function for
	* retrieving all CART items from the resource server based on
	* the given customer id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return array all items in carts
	*/
	function getCustomerDetails($access_token)
	{
		// Get cURL resource
		$curl = curl_init();

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_GETCUSTOMERDETAILS.'?searchCriteria[filter_groups][0][filters][0][field]='.$_GET['searchType'].'&searchCriteria[filter_groups][0][filters][0][value]='.$_GET['keyword'].'&searchCriteria[filter_groups][0][filters][0][condition_type]=eq',
		    CURLOPT_USERAGENT 		=> 'Get Customer Details',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
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
			$resp = array('status_code' => 0);
		else:
			// push the status code value to the $resp array
			$resp 					= json_decode($resp, true);
			$resp['result'] 		= $resp;
			$resp['status_code'] 	= $http_status_code;
			if(!is_null($resp) AND array_key_exists('items', $resp)):
				$resp['length'] 	= count($resp['items']);
			else:
				$resp['length'] 	= 0;
			endif;

			foreach ($resp as $key => $value):
				if($key != 'result' AND $key != 'status_code' AND $key != 'length'):
					unset($resp[$key]);
				endif;
			endforeach;
		endif;


		// return the data result fetch from the api url
		return json_encode($resp, true);
	}


	/**
	* GET CART ITEMS METHOD
	* This method will serve as  the utility function for
	* retrieving all CART items from the resource server based on
	* the given customer id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return array all items in carts
	*/
	function getCartItems($access_token)
	{
		// Get cURL resource
		$curl = curl_init();

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_ITEMS.$_GET['customer_id'].'/'.SYSCONFIG_MAGENTO_GETCARTITEMS,
		    CURLOPT_USERAGENT 		=> 'Items in Cart',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
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
			$resp = array('status_code' => 0);
		else:
			// push the status code value to the $resp array
			$resp 					= json_decode($resp, true);
			$resp['result'] 		= $resp;
			$resp['status_code'] 	= $http_status_code;
			if(!is_null($resp) AND array_key_exists('items', $resp)):
				$resp['length'] 		= count($resp['items']);
			else:
				$resp['length'] 		= 0;
			endif;

			foreach ($resp as $key => $value):
				if($key != 'result' AND $key != 'status_code' AND $key != 'length'):
					unset($resp[$key]);
				endif;
			endforeach;
		endif;
		// return the data result fetch from the api url
		return json_encode($resp, true);
	}

	/**
	* GET WISHLIST ITEMS METHOD
	* This method will serve as  the utility function for
	* retrieving all WISHLISTS items from the resource server based on
	* the given customer id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return array all items in wishlists
	*/
	function getWishListItems($access_token)
	{
		// Get cURL resource
		$curl = curl_init();

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_ITEMS.$_GET['customer_id'].'/'.SYSCONFIG_MAGENTO_GETWISHLISTITEMS,
		    CURLOPT_USERAGENT 		=> 'Items in WishList',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
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
			$resp = array('status_code' => 0);
		else:
			// push the status code value to the $resp array
			$resp 					= json_decode($resp, true);
			$resp['result'] 		= $resp;
			$resp['status_code'] 	= $http_status_code;
			if(!is_null($resp) AND array_key_exists('items', $resp)):
				$resp['length'] 		= count($resp['items']);
			else:
				$resp['length'] 		= 0;
			endif;

			foreach ($resp as $key => $value):
				if($key != 'result' AND $key != 'status_code' AND $key != 'length'):
					unset($resp[$key]);
				endif;
			endforeach;
		endif;
		// return the data result fetch from the api url
		return json_encode($resp, true);
	}

	/**
	* GET GIFT REGISTRY ITEMS METHOD
	* This method will serve as  the utility function for
	* retrieving all GIFT REGISTRY items from the resource server based on
	* the given customer id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return array all items in gift registries
	*/
	function getGiftRegistryItems($access_token)
	{
		// Get cURL resource
		$curl = curl_init();

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_ITEMS.$_GET['customer_id'].'/'.SYSCONFIG_MAGENTO_GETGIFTREGISTRYITEMS,
		    CURLOPT_USERAGENT 		=> 'Items in Gift Registry',
		    CURLOPT_SSL_VERIFYPEER 	=> 0,
		    CURLOPT_SSL_VERIFYHOST 	=> 0,
		    CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
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
			$resp = array('status_code' => 0);
		else:
			// push the status code value to the $resp array
			$resp 					= json_decode($resp, true);

			if(!empty(array_filter($resp)) AND array_key_exists(0, $resp)):
				$resp['result'] 		= $resp[0];
				$resp['status_code'] 	= $http_status_code;
				if(!is_null($resp[0]) AND array_key_exists('items', $resp[0])):
					$resp['length'] 		= count($resp[0]['items']);
				else:
					$resp['length'] 		= 0;
				endif;
				unset($resp[0]);
			else:
				$resp['result'] 		= $resp;
				$resp['status_code'] 	= $http_status_code;
				if(!is_null($resp) AND array_key_exists('items', $resp)):
					$resp['length'] 		= count($resp['items']);
				else:
					$resp['length'] 		= 0;
				endif;
				
			endif;
		endif;

		// return the data result fetch from the api url
		return json_encode($resp, true);
	}

	/**
	* DELETE CART ITEM METHOD
	* This method will serve as  the utility function for
	* deleting specific item from the resource server based on
	* the given cart id, item id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return boolean true if success else json data
	*/
	function deleteCartItem($access_token, $db)
	{
		// // Get cURL resource
		// $curl = curl_init();

		// // Set some options - we are passing in a useragent too here
		// curl_setopt_array($curl, array(
		//     CURLOPT_RETURNTRANSFER 	=> 1,
		//     CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.'carts/'.$_GET['id'].'/items/'.$_GET['item_id'].'/',
		//     CURLOPT_USERAGENT 		=> 'Delete Cart Item',
		//     CURLOPT_SSL_VERIFYPEER 	=> 0,
		//     CURLOPT_SSL_VERIFYHOST 	=> 0,
		//     CURLOPT_CUSTOMREQUEST 	=> 'delete',
		//     CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
		// ));

		// // Send the request & save response to $resp
		// $resp = curl_exec($curl);

		// // Get HTTP response status code
		// $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// // Close request to clear up some resources
		// curl_close($curl);

		// $resp = json_decode($resp, true);

		// if($http_status_code == 200 OR $http_status_code == 404):
		// 	// push the status code value to the $resp array
		// 	$result = array('deleted' => $resp, 'status_code' => $http_status_code);

		// 	// return the data result fetch from the api url
		// 	return json_encode($result, true);
		// elseif($http_status_code == 401):
		// 	echo getAccessToken(array('username' => $_SESSION['api_username'], 'password' => $_SESSION['api_password']), 'deleteCartItem');
		// endif;

		$query = $db->exec("INSERT INTO oap_upload (oau_raw, oau_date, oau_type, oau_method, oau_header) VALUES ('', '".date('Y-m-d')."', 'M', 'DELETE', '".SYSCONFIG_MAGENTO_URL."carts/".$_GET['id']."/items/".$_GET['item_id']."')");
		if($query):
			echo json_encode(array('status_code' => 200, 'message' => 'OK'));
		else:
			echo json_encode(array('status_code' => 500, 'message' => 'INTERNAL SERVER ERROR'));
		endif;
	}

	/**
	* DELETE WISH LIST ITEM METHOD
	* This method will serve as  the utility function for
	* deleting specific item from the resource server based on
	* the given wishlist id, item id and access token
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return boolean true if success else json data
	*/
	function deleteWishListItem($access_token, $db)
	{
		// // Get cURL resource
		// $curl = curl_init();

		// // Set some options - we are passing in a useragent too here
		// curl_setopt_array($curl, array(
		//     CURLOPT_RETURNTRANSFER 	=> 1,
		//     CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.'wishlist/'.$_GET['id'].'/remove-item',
		//     CURLOPT_USERAGENT 		=> 'Delete Wish List Item',
		//     CURLOPT_SSL_VERIFYPEER 	=> 0,
		//     CURLOPT_SSL_VERIFYHOST 	=> 0,
		//     CURLOPT_POST 			=> 1,
		//     CURLOPT_POSTFIELDS 		=> array('itemId' => $_GET['item_id'], 'qty' => $_GET['item_qty']),
		//     CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
		// ));

		// // Send the request & save response to $resp
		// $resp = curl_exec($curl);

		// // Get HTTP response status code
		// $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// // Close request to clear up some resources
		// curl_close($curl);

		// $resp = json_decode($resp, true);

		// if($http_status_code == 200 OR $http_status_code == 404):
		// 	// push the status code value to the $resp array
		// 	$resp['status_code'] = $http_status_code;

		// 	// return the data result fetch from the api url
		// 	return json_encode($resp, true);
		// elseif($http_status_code == 401):
		// 	echo getAccessToken(array('username' => $_SESSION['api_username'], 'password' => $_SESSION['api_password']), 'deleteWishListItem');
		// endif;

		$rawData = json_encode(array('itemId' => $_GET['item_id'], 'qty' => $_GET['item_qty']));

		$query = $db->exec("INSERT INTO oap_upload (oau_raw, oau_date, oau_type, oau_method, oau_header) VALUES ('".$rawData."', '".date('Y-m-d')."', 'M', 'POST', '".SYSCONFIG_MAGENTO_URL."wishlist/".$_GET['id']."/remove-item/')");
		if($query):
			echo json_encode(array('status_code' => 200, 'message' => 'OK'));
		else:
			echo json_encode(array('status_code' => 500, 'message' => 'INTERNAL SERVER ERROR'));
		endif;
	}

	/**
	* DELETE GIFT REGISTRY ITEM METHOD
	* This method will serve as  the utility function for
	* deleting specific item from the resource server based on
	* the given gift registry id, item id and access token, item quantity
	* @param string $url 			- API access token url
	* @param array $access_token 	- access token value from
	* 								the API for authorization to
	*								retrieve the protected resources
	* @return boolean true if success else json data
	*/
	function deleteGiftRegistryItem($access_token, $db)
	{
		// // Get cURL resource
		// $curl = curl_init();

		// // Set some options - we are passing in a useragent too here
		// curl_setopt_array($curl, array(
		//     CURLOPT_RETURNTRANSFER 	=> 1,
		//     CURLOPT_URL 			=> SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_DELETEGIFTREGISTRYITEMS,
		//     CURLOPT_USERAGENT 		=> 'Delete Wish List Item',
		//     CURLOPT_SSL_VERIFYPEER 	=> 0,
		//     CURLOPT_SSL_VERIFYHOST 	=> 0,
		//     CURLOPT_POST 			=> 1,
		//     CURLOPT_POSTFIELDS 		=> array('giftRegistryId' => $_GET['id'], 'itemId' => $_GET['item_id'], 'qty' => $_GET['item_qty']),
		//     CURLOPT_HTTPHEADER 		=> array('Authorization: Bearer '.$access_token),
		// ));

		// // Send the request & save response to $resp
		// $resp = curl_exec($curl);

		// // Get HTTP response status code
		// $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// // Close request to clear up some resources
		// curl_close($curl);

		// $resp = json_decode($resp, true);

		// if($http_status_code == 200 OR $http_status_code == 404):
		// 	// push the status code value to the $resp array
		// 	$resp['status_code'] = $http_status_code;

		// 	// return the data result fetch from the api url
		// 	return json_encode($resp, true);
		// elseif($http_status_code == 401):
		// 	echo getAccessToken(array('username' => $_SESSION['api_username'], 'password' => $_SESSION['api_password']), 'deleteGiftRegistryItem');
		// endif;

		$rawData = json_encode(array('giftRegistryId' => $_GET['id'], 'itemId' => $_GET['item_id'], 'qty' => $_GET['item_qty']));

		$query = $db->exec("INSERT INTO oap_upload (oau_raw, oau_date, oau_type, oau_method, oau_header) VALUES ('".$rawData."', '".date('Y-m-d')."', 'M', 'POST', '".SYSCONFIG_MAGENTO_URL.SYSCONFIG_MAGENTO_DELETEGIFTREGISTRYITEMS."')");
		if($query):
			echo json_encode(array('status_code' => 200, 'message' => 'OK'));
		else:
			echo json_encode(array('status_code' => 500, 'message' => 'INTERNAL SERVER ERROR'));
		endif;
	}

?>
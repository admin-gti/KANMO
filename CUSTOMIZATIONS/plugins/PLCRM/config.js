var crmHandler = ['ModelEvent', '$http', '$window', 'ModelService', 'NotificationService', '$state', 'DocumentPersistedData', '$timeout', 'LoadingScreen', '$interval', function(ModelEvent, $http, $window, ModelService, NotificationService, $state, DocumentPersistedData, $timeout, LoadingScreen, $interval){

	var customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country;

	// Event handler to capture after item insert in the current document
	var afterItemInsert = function($q, item){
		var deferred = $q.defer();
		var activeDocumentSid 	= DocumentPersistedData.DocumentInformation.Sid;
		if(sessionStorage.getItem('hasCustomer') == 1)
		{
			ModelService.get('Item', {document_sid: activeDocumentSid, sid: item.sid}).then(function(data){
				if(data.length != 0)
				{
					var activeCustomer 		= ModelService.fromCache('Customer')[0];
					ModelService.get('Address', {customer_sid: activeCustomer.sid, filter: '(active,eq,true)AND(primary_flag,eq,true)'}).then(function(addresses){
						item.st_postal_code = addresses[0].postal_code;
						item.st_country 	= addresses[0].country_name;
						item.note1 			= addresses[0].sid;
						item.save().then(function(){
							deferred.resolve();
						});
					});
				}
				else
				{
					deferred.resolve();
				}
			});
		}
		else
		{
			deferred.resolve();
		}
		
		return deferred.promise;
	};

	// ModelEvent.addListener('item', 'onBeforeInsert', beforeItemInsert);
	ModelEvent.addListener('item', 'onAfterInsert', afterItemInsert);

}]

ConfigurationManager.addHandler(crmHandler);


// Button Hooks for CANCEL TRANSACTION BUTTON
ButtonHooksManager.addHandler(['after_posCancelDocCancel', 'after_navPosTransactionCancel'], function($q){
    var deferred = $q.defer();

    sessionStorage.removeItem('hasCustomer');

    deferred.resolve();
    return deferred.promise;
});

// Button Hooks for TENDER BUTTON TRANSACTION
ButtonHooksManager.addHandler(['before_posTransactionTenderTransaction'], function($q, ModelService, NotificationService, $stateParams, LoadingScreen, $state, $window, $http){
    var deferred 			= $q.defer();
    var servername 			= $window.location.origin;
    var activeDocumentSid 	= $stateParams.document_sid;
    var customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country;
    ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){

    	if(doc[0].bt_cuid == '' || doc[0].bt_cuid == null)
    	{
    		ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
    			var item_type = [];

    			angular.forEach(items, function(value, key){
    				if(value.item_type == 3)
    				{
    					item_type.push(1);
    				}
    				else
    				{
    					item_type.push(0);
    				}
    			});

				var sendCustomerSid = function(phoneNo)
				{
					ModelService.get('Customer', {filter: 'primary_phone_no,eq,'+phoneNo}).then(function(data){
					    var customerData = {
											root:{
											customer:[
											  {
											    mobile:data[0].primary_phone_no,
											    custom_fields:{
											    	field:[
											    		{
											    			name: "cust_sid",
											    			value: data[0].sid
											    		}
											    	]
											    }
											  }
											]
											}
										};

					   	$http.post(servername + '/plugins/PLVoucher/redeemVoucher.php', {action: 'updateCutomerDetails', customerData: customerData}).then(function(res){
					   		if(res.data != '' && res.data.toUpperCase() != 'FALSE')
					   		{
					   			var jsonObject = JSON.parse(res.data);
					   			if(jsonObject.status_code == 200)
					   			{
					   			    $state.go($state.current, {}, {reload: true}).then(function(){
					   			        $modal.open(modalOptions);
					   			        LoadingScreen.Enable = !1;
					   			        deferred.resolve();
					   			    });
					   			}
					   			else
					   			{
					   			    NotificationService.addAlert(jsonObject.message, 'Server Error', 'static', false).then(function(){
					   			        $state.go($state.current, {}, {reload: true});
					   			        deferred.resolve();
					   			    });
					   			    LoadingScreen.Enable = !1;
					   			}
					   			// if(!('status_code' in jsonObject))
					   			// {
					   			// 	var success_request = jsonObject.response.status.success;
					   			// 	var request_code 	= jsonObject.response.status.code;
					   			// 	var request_message = jsonObject.response.status.message;

					   			// 	if(success_request.toUpperCase() != 'TRUE' && request_code != 200 && request_message.toUpperCase() != 'SUCCESS')
					   			// 	{
					   			// 		NotificationService.addAlert(jsonObject.response.customers.customer[0].item_status.message, 'Customer Detail Validation', 'static', false).then(function(){
					   			// 		    $state.go($state.current, {}, {reload: true});
					   			// 		    deferred.resolve();
					   			// 		});
					   			// 		LoadingScreen.Enable = !1;
					   			// 	}
					   			// 	else
					   			// 	{
					   			// 		LoadingScreen.Enable = !1;
					   			// 		deferred.resolve();
					   			// 	}
					   			// }
					   			// else if(jsonObject.status_code == 400)
					   			// {
					   			// 	NotificationService.addAlert(jsonObject.message, 'Bad Request', 'static', false).then(function(){
					   			// 		$state.go($state.current, {}, {reload: true});
					   			// 		deferred.resolve();
					   			// 	});
					   			// 	LoadingScreen.Enable = !1;
					   			// }
					   			// else if(jsonObject.status_code == 500)
					   			// {
					   			// 	NotificationService.addAlert(jsonObject.message, 'Network Error', 'static', false).then(function(){
					   			// 		$state.go($state.current, {}, {reload: true});
					   			// 		deferred.resolve();
					   			// 	});
					   			// 	LoadingScreen.Enable = !1;
					   			// }
					   		}
					   		else
					   		{
					   			NotificationService.addAlert('Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
					   				$state.go($state.current, {}, {reload: true});
					   				deferred.resolve();
					   			});
					   			LoadingScreen.Enable = !1;
					   		}
					   });
					});
				}

				// var is_online = false;
				// var checkNetworkStatus = function()
				// {
				// 	var d = $q.defer();
				// 	$http.get(servername + '/plugins/PLVoucher/redeemVoucher.php?action=checkNetworkStatus').then(function(result){
				// 	    var online   = JSON.parse(result.data);
					    
				// 	    if(online.status_code == 200)
				// 	    {
				// 	        is_online = true;
				// 	        d.resolve();
				// 	    }
				// 	    else
				// 	    {
				// 	        LoadingScreen.Enable = !1;
				// 	        if(online.status_code == 408)
				// 	        {
				// 	            NotificationService.addAlert(online.message, 'Request Timeout', 'static', false).then(function(){
				// 	                $state.go($state.current, {}, {reload: true});
				// 	            });
				// 	        }
				// 	        else if(online.status_code == 500)
				// 	        {
				// 	            NotificationService.addAlert(online.message, 'Network Error', 'static', false).then(function(){
				// 	                $state.go($state.current, {}, {reload: true});
				// 	            });
				// 	        }
				// 	        d.resolve();
				// 	    }
				// 	});

				// 	return d.promise;
				// }

				// NEW Customer Function
				var newCustomerFunction = function(customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country)
				{
				   	var newCustomer = ModelService.create('Customer');

				   	newCustomer.primary_phone_no 	= mobileNo;
				   	newCustomer.full_name 			= customerName;
				   	newCustomer.first_name 			= customerFName;
				   	newCustomer.last_name 			= customerLName;
				   	newCustomer.customer_id 		= cust_id;
				   	newCustomer.udffield05 			= externalId;
				   	newCustomer.addresses 			= [	{"address_line_1": address_one_one,"address_line_2":address_one_two,"address_line_3":city_one,"postal_code":postal_code, "address_line_4": province_one, "address_line_5": district_one, "country_name": country , "primary_flag": 1},
				   										{"address_line_1": address_two_one,"address_line_2":address_two_two,"address_line_3":city_two,"postal_code":postal_code, "address_line_4": province_two, "address_line_5": district_two, "country_name": country},
				   										{"address_line_1": address_three_one,"address_line_2":address_three_two,"address_line_3":city_three,"postal_code":postal_code, "address_line_4": province_three, "address_line_5": district_three, "country_name": country}
				   									  ];
				   	newCustomer.phones 				= [{"phone_no": mobileNo}];
				   	newCustomer.emails 				= [{"email_address": customerEmail}];
				   	newCustomer.insert().then(function(data){
				   		var customerSid = data.sid;
			   		    var newCust 	= data;

						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
							var currentDocData = doc[0];
							currentDocData.addBillToCustomer(newCust).then(function(){
								currentDocData.addShipToCustomer(newCust).then(function(){
									var chainRequest = $q.when();
									var count = [];
									angular.forEach(items, function(value, key){
										chainRequest = chainRequest.then(function(){
											var item = value;
											item.st_postal_code 	= postal_code;
											item.st_address_line1   = address_one_one,
											item.st_address_line2   = address_one_two,
											item.st_address_line3 	= city_one;
											item.st_cuid 			= customerSid;
											item.st_id 				= cust_id;
											item.st_first_name 		= customerFName;
											item.st_last_name 		= customerLName;
											item.st_primary_phone_no= mobileNo;
											item.st_email 			= customerEmail;
											item.st_address_uid 	= primary_address;
											return item.save().then(function(){
												count.push(1);
											});
										});
									});

									$q.all(count).then(function(){
										sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
										// sendCustomerSid(mobileNo);
										LoadingScreen.Enable = 0;
										deferred.resolve();
									});
								});
							});
						});

				   	}, function(error){
				   		LoadingScreen.Enable = !1;
				   		NotificationService.addAlert(error.data[0].errormsg, 'Customer Data Validation', 'static', false).then(function(){
				   			sessionStorage.setItem('hasCustomer', 0);
				   			deferred.resolve();
				   		});
				   	});

				};

				// Update Customer Function
				var updateCustomerFunctionDefault = function(data, customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country)
				{
					var updateCustomer = data;

					updateCustomer.primary_phone_no = mobileNo;
					updateCustomer.full_name 		= customerName;
					updateCustomer.first_name 		= customerFName;
					updateCustomer.last_name 		= customerLName;
					updateCustomer.customer_id 		= cust_id;
					updateCustomer.udffield05 		= externalId;
					updateCustomer.save().then(function(data){
						var customerSid = data.sid;
						ModelService.get('Address', {customer_sid: customerSid, sort: 'seq_no,asc'}).then(function(addresses){
							// Add customer address
							if(addresses.length >= 3)
							{
								var primary_address;

								switch(true) {
								    case addresses[0].primary_flag:
								        primary_address = addresses[0].sid;
								        break;
								    case addresses[1].primary_flag:
								        primary_address = addresses[1].sid;
								        break;
							        case addresses[2].primary_flag:
							            primary_address = addresses[2].sid;
							            break;
								}
								var newAdd1 = addresses[0];
								newAdd1.customer_sid 	= customerSid;
								newAdd1.address_line_1 	= address_one_one;
								newAdd1.address_line_2 	= address_one_two;
								newAdd1.address_line_3 	= city_one;
								newAdd1.address_line_4 	= province_one;
								newAdd1.address_line_5 	= district_one;
								newAdd1.city 			= city_one;
								newAdd1.country_name 	= country;
								newAdd1.postal_code 	= postal_code;
								newAdd1.save().then(function(){
									var newAdd2 = addresses[1];
									newAdd2.customer_sid 	= customerSid;
									newAdd2.address_line_1 	= address_two_one;
									newAdd2.address_line_2 	= address_two_two;
									newAdd2.address_line_3 	= city_two;
									newAdd2.address_line_4 	= province_two;
									newAdd2.address_line_5 	= district_two;
									newAdd2.city 			= city_two;
									newAdd2.country_name 	= country;
									newAdd2.postal_code 	= postal_code;
									newAdd2.save().then(function(){
										var newAdd3 = addresses[2];
										newAdd3.customer_sid 	= customerSid;
										newAdd3.address_line_1 	= address_three_one;
										newAdd3.address_line_2 	= address_three_two;
										newAdd1.address_line_3 	= city_three;
										newAdd3.address_line_4 	= province_three;
										newAdd3.address_line_5 	= district_three;
										newAdd3.city 			= city_three;
										newAdd3.country_name 	= country;
										newAdd3.postal_code 	= postal_code;
										newAdd3.save().then(function(){
											ModelService.get('Email',{customer_sid: customerSid,filter: 'primary_flag,eq,true'}).then(function(email){
									            var updatedEmail 				= email[0];
									            	updatedEmail.email_address 	= customerEmail;
									            	updatedEmail.save().then(function(){
									            		ModelService.get('Phone', {customer_sid: customerSid, filter: 'primary_flag,eq,true'}).then(function(phone){
									            			var updatedPhone 			= phone[0];
									            				updatedPhone.phone_no 	= mobileNo;
									            				updatedPhone.save().then(function(){
		            					    						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
		            					        						var billToCustData = [{	"bt_cuid" 				: customerSid,
		            					        												"bt_id" 				: cust_id,
		            					        												"bt_first_name" 		: customerFName,
		            					        												"bt_last_name" 			: customerLName,
		            					        												"bt_primary_phone_no" 	: mobileNo,
		            					        												"bt_address_line1" 		: address_one_one,
		            					        												"bt_address_line2" 		: address_one_two,
		            					        												"bt_postal_code" 		: postal_code,
		            					        												"bt_email"  			: customerEmail
		            					        											}];
		            					        						$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,'+ doc[0].row_version, billToCustData).then(function(data){
		            					        							var shipToCustData = [{	"st_cuid" 				: customerSid,
		            					        													"st_id" 				: cust_id,
		            					        													"st_first_name" 		: customerFName,
		            					        													"st_last_name" 			: customerLName,
		            					        													"st_primary_phone_no" 	: mobileNo,
		            					        													"st_address_line1" 		: address_one_one,
		            					        													"st_address_line2" 		: address_one_two,
		            					        													"st_postal_code" 		: postal_code,
		            					        													"st_email"  			: customerEmail
		            					        												}];
		            					        							$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,' + data.data[0].row_version, shipToCustData).then(function(){
 									     										var chainRequest = $q.when();
 									     										var count = [];
 									     										angular.forEach(items, function(value, key){
 									     											chainRequest = chainRequest.then(function(){
 									     												var item = value;
 									     												item.st_postal_code 	= postal_code;
 									     												item.st_address_line1   = address_one_one,
 									     												item.st_address_line2   = address_one_two,
 									     												item.st_address_line3 	= city_one;
 									     												item.st_cuid 			= customerSid;
 									     												item.st_id 				= cust_id;
 									     												item.st_first_name 		= customerFName;
 									     												item.st_last_name 		= customerLName;
 									     												item.st_primary_phone_no= mobileNo;
 									     												item.st_email 			= customerEmail;
 									     												item.st_address_uid 	= primary_address;
 																							return item.save().then(function(){
 																								count.push(1);
 																							});
 									     											});
 									     										});

 									     										$q.all(count).then(function(){
 									     											sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
 									     											// sendCustomerSid(mobileNo);
 									     											LoadingScreen.Enable = 0;
 									     											deferred.resolve();
 									     										});
		            					        							});
		            					        						});
		            					    						});	
									            				});
									            		});
									            	});
											});
										});
									});
								});
							}
							else if(addresses.length == 1)
							{
								var primary_address;

								switch(true) {
								    case addresses[0].primary_flag:
								        primary_address = addresses[0].sid;
								        break;
								}
								var newAdd1 = addresses[0];
								newAdd1.customer_sid 	= customerSid;
								newAdd1.address_line_1 	= address_one_one;
								newAdd1.address_line_2 	= address_one_two;
								newAdd1.address_line_3 	= city_one;
								newAdd1.city 			= city_one;
								newAdd1.country_name 	= country;
								// newAdd1.state 			= province_one;
								newAdd1.postal_code 	= postal_code;
								newAdd1.save().then(function(){
									var newAdd2 = ModelService.create('Address');
									newAdd2.customer_sid 	= customerSid;
									newAdd2.address_line_1 	= address_two_one;
									newAdd2.address_line_2 	= address_two_two;
									newAdd2.address_line_3 	= city_two;
									newAdd2.city 			= city_two;
									newAdd2.country_name 	= country;
									// newAdd2.state 			= province_two;
									newAdd2.postal_code 	= postal_code;
									updateCustomer.addAddress(newAdd2).then(function(){
										var newAdd3 = ModelService.create('Address');
										newAdd3.customer_sid 	= customerSid;
										newAdd3.address_line_1 	= address_three_one;
										newAdd3.address_line_2 	= address_three_two;
										newAdd1.address_line_3 	= city_three;
										newAdd3.city 			= city_three;
										newAdd3.country_name 	= country;
										// newAdd3.state 			= province_three;
										newAdd3.postal_code 	= postal_code;
										updateCustomer.addAddress(newAdd3).then(function(){
											ModelService.get('Email',{customer_sid: customerSid,filter: 'primary_flag,eq,true'}).then(function(email){
									            var updatedEmail 				= email[0];
									            	updatedEmail.email_address 	= customerEmail;
									            	updatedEmail.save().then(function(){
									            		ModelService.get('Phone', {customer_sid: customerSid, filter: 'primary_flag,eq,true'}).then(function(phone){
									            			var updatedPhone 			= phone[0];
									            				updatedPhone.phone_no 	= mobileNo;
									            				updatedPhone.save().then(function(){
		            					    						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
		            					        						var billToCustData = [{	"bt_cuid" 				: customerSid,
		            					        												"bt_id" 				: cust_id,
		            					        												"bt_first_name" 		: customerFName,
		            					        												"bt_last_name" 			: customerLName,
		            					        												"bt_primary_phone_no" 	: mobileNo,
		            					        												"bt_address_line1" 		: address_one_one,
		            					        												"bt_address_line2" 		: address_one_two,
		            					        												"bt_postal_code" 		: postal_code,
		            					        												"bt_email"  			: customerEmail
		            					        											}];
		            					        						$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,'+ doc[0].row_version, billToCustData).then(function(data){
		            					        							var shipToCustData = [{	"st_cuid" 				: customerSid,
		            					        													"st_id" 				: cust_id,
		            					        													"st_first_name" 		: customerFName,
		            					        													"st_last_name" 			: customerLName,
		            					        													"st_primary_phone_no" 	: mobileNo,
		            					        													"st_address_line1" 		: address_one_one,
		            					        													"st_address_line2" 		: address_one_two,
		            					        													"st_postal_code" 		: postal_code,
		            					        													"st_email"  			: customerEmail
		            					        												}];
		            					        							$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,' + data.data[0].row_version, shipToCustData).then(function(){
									     										var chainRequest = $q.when();
									     										var count = [];
									     										angular.forEach(items, function(value, key){
									     											chainRequest = chainRequest.then(function(){
									     												var item = value;
									     												item.st_postal_code 	= postal_code;
									     												item.st_address_line1   = address_one_one,
									     												item.st_address_line2   = address_one_two,
									     												item.st_address_line3 	= city_one;
									     												item.st_cuid 			= customerSid;
									     												item.st_id 				= cust_id;
									     												item.st_first_name 		= customerFName;
									     												item.st_last_name 		= customerLName;
									     												item.st_primary_phone_no= mobileNo;
									     												item.st_email 			= customerEmail;
									     												item.st_address_uid 	= primary_address;
																							return item.save().then(function(){
																								count.push(1);
																							});
									     											});
									     										});

									     										$q.all(count).then(function(){
									     											sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
									     											// sendCustomerSid(mobileNo);
									     											LoadingScreen.Enable = 0;
									     											deferred.resolve();
									     										});
		            					        							});
		            					        						});
		            					    						});	
									            				});
									            		});
									            	});
											});
										});
									});
								});
							}
							else if(addresses.length == 2)
							{
								var primary_address;

								switch(true) {
								    case addresses[0].primary_flag:
								        primary_address = addresses[0].sid;
								        break;
								    case addresses[1].primary_flag:
								        primary_address = addresses[1].sid;
								        break;
								}

								var newAdd1 = addresses[0];
								newAdd1.customer_sid 	= customerSid;
								newAdd1.address_line_1 	= address_one_one;
								newAdd1.address_line_2 	= address_one_two;
								newAdd1.address_line_3 	= city_one;
								newAdd1.city 			= city_one;
								newAdd1.country_name 	= country;
								// newAdd1.state 			= province_one;
								newAdd1.postal_code 	= postal_code;
								newAdd1.save().then(function(){
									var newAdd2 = addresses[1];
									newAdd2.customer_sid 	= customerSid;
									newAdd2.address_line_1 	= address_two_one;
									newAdd2.address_line_2 	= address_two_two;
									newAdd2.address_line_3 	= city_two;
									newAdd2.city 			= city_two;
									newAdd2.country_name 	= country;
									// newAdd2.state 			= province_two;
									newAdd2.postal_code 	= postal_code;
									newAdd2.save().then(function(){
										var newAdd3 = ModelService.create('Address');
										newAdd3.customer_sid 	= customerSid;
										newAdd3.address_line_1 	= address_three_one;
										newAdd3.address_line_2 	= address_three_two;
										newAdd1.address_line_3 	= city_three;
										newAdd3.city 			= city_three;
										newAdd3.country_name 	= country;
										// newAdd3.state 			= province_three;
										newAdd3.postal_code 	= postal_code;
										updateCustomer.addAddress(newAdd3).then(function(){
											ModelService.get('Email',{customer_sid: customerSid,filter: 'primary_flag,eq,true'}).then(function(email){
									            var updatedEmail 				= email[0];
									            	updatedEmail.email_address 	= customerEmail;
									            	updatedEmail.save().then(function(){
									            		ModelService.get('Phone', {customer_sid: customerSid, filter: 'primary_flag,eq,true'}).then(function(phone){
									            			var updatedPhone 			= phone[0];
									            				updatedPhone.phone_no 	= mobileNo;
									            				updatedPhone.save().then(function(){
		            					    						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
		            					        						var billToCustData = [{	"bt_cuid" 				: customerSid,
		            					        												"bt_id" 				: cust_id,
		            					        												"bt_first_name" 		: customerFName,
		            					        												"bt_last_name" 			: customerLName,
		            					        												"bt_primary_phone_no" 	: mobileNo,
		            					        												"bt_address_line1" 		: address_one_one,
		            					        												"bt_address_line2" 		: address_one_two,
		            					        												"bt_postal_code" 		: postal_code,
		            					        												"bt_email"  			: customerEmail
		            					        											}];
		            					        						$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,'+ doc[0].row_version, billToCustData).then(function(data){
		            					        							var shipToCustData = [{	"st_cuid" 				: customerSid,
		            					        													"st_id" 				: cust_id,
		            					        													"st_first_name" 		: customerFName,
		            					        													"st_last_name" 			: customerLName,
		            					        													"st_primary_phone_no" 	: mobileNo,
		            					        													"st_address_line1" 		: address_one_one,
		            					        													"st_address_line2" 		: address_one_two,
		            					        													"st_postal_code" 		: postal_code,
		            					        													"st_email"  			: customerEmail
		            					        												}];
		            					        							$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,' + data.data[0].row_version, shipToCustData).then(function(){
		            					     									var chainRequest = $q.when();
									     										var count = [];
									     										angular.forEach(items, function(value, key){
									     											chainRequest = chainRequest.then(function(){
									     												var item = value;
									     												item.st_postal_code 	= postal_code;
									     												item.st_address_line1   = address_one_one,
									     												item.st_address_line2   = address_one_two,
									     												item.st_address_line3 	= city_one;
									     												item.st_cuid 			= customerSid;
									     												item.st_id 				= cust_id;
									     												item.st_first_name 		= customerFName;
									     												item.st_last_name 		= customerLName;
									     												item.st_primary_phone_no= mobileNo;
									     												item.st_email 			= customerEmail;
									     												item.st_address_uid 	= primary_address;
																							return item.save().then(function(){
																								count.push(1);
																							});
									     											});
									     										});

									     										$q.all(count).then(function(){
									     											sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
									     											// sendCustomerSid(mobileNo);
									     											LoadingScreen.Enable = 0;
									     											deferred.resolve();
									     										});
																			});
		            					        						});
		            					    						});	
									            				});
									            		});
									            	});
											});
										});
									});
								});
							}
							else if(addresses.length == 0)
							{
								var primary_address;

								var newAdd1 = ModelService.create('Address');
								newAdd1.customer_sid 	= customerSid;
								newAdd1.address_line_1 	= address_one_one;
								newAdd1.address_line_2 	= address_one_two;
								newAdd1.address_line_3 	= city_one;
								newAdd1.city 			= city_one;
								newAdd1.country_name 	= country;
								// newAdd1.state 			= province_one;
								newAdd1.postal_code 	= postal_code;
								newAdd1.active 			= 1;
								newAdd1.primary_flag 	= 1;
								newAdd1.seq_no 			= 1;
								updateCustomer.addAddress(newAdd1).then(function(add1){
									primary_address = add1.sid;
									var newAdd2 = ModelService.create('Address');
									newAdd2.customer_sid 	= customerSid;
									newAdd2.address_line_1 	= address_two_one;
									newAdd2.address_line_2 	= address_two_two;
									newAdd2.address_line_3 	= city_two;
									newAdd2.city 			= city_two;
									newAdd2.country_name 	= country;
									// newAdd2.state 			= province_two;
									newAdd2.postal_code 	= postal_code;
									newAdd2.active 			= 1;
									updateCustomer.addAddress(newAdd2).then(function(){
										var newAdd3 = ModelService.create('Address');
										newAdd3.customer_sid 	= customerSid;
										newAdd3.address_line_1 	= address_three_one;
										newAdd3.address_line_2 	= address_three_two;
										newAdd1.address_line_3 	= city_three;
										newAdd3.city 			= city_three;
										newAdd3.country_name 	= country;
										// newAdd3.state 			= province_three;
										newAdd3.postal_code 	= postal_code;
										newAdd3.active 			= 1;
										updateCustomer.addAddress(newAdd3).then(function(){
											ModelService.get('Email',{customer_sid: customerSid,filter: 'primary_flag,eq,true'}).then(function(email){
									            var updatedEmail 				= email[0];
									            	updatedEmail.email_address 	= customerEmail;
									            	updatedEmail.save().then(function(){
									            		ModelService.get('Phone', {customer_sid: customerSid, filter: 'primary_flag,eq,true'}).then(function(phone){
									            			var updatedPhone 			= phone[0];
									            				updatedPhone.phone_no 	= mobileNo;
									            				updatedPhone.save().then(function(){
		            					    						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
		            					        						var billToCustData = [{	"bt_cuid" 				: customerSid,
		            					        												"bt_id" 				: cust_id,
		            					        												"bt_first_name" 		: customerFName,
		            					        												"bt_last_name" 			: customerLName,
		            					        												"bt_primary_phone_no" 	: mobileNo,
		            					        												"bt_address_line1" 		: address_one_one,
		            					        												"bt_address_line2" 		: address_one_two,
		            					        												"bt_postal_code" 		: postal_code,
		            					        												"bt_email"  			: customerEmail
		            					        											}];
		            					        						$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,'+ doc[0].row_version, billToCustData).then(function(data){
		            					        							var shipToCustData = [{	"st_cuid" 				: customerSid,
		            					        													"st_id" 				: cust_id,
		            					        													"st_first_name" 		: customerFName,
		            					        													"st_last_name" 			: customerLName,
		            					        													"st_primary_phone_no" 	: mobileNo,
		            					        													"st_address_line1" 		: address_one_one,
		            					        													"st_address_line2" 		: address_one_two,
		            					        													"st_postal_code" 		: postal_code,
		            					        													"st_email"  			: customerEmail
		            					        												}];
		            					        							$http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,' + data.data[0].row_version, shipToCustData).then(function(){
		            					     									var chainRequest = $q.when();
									     										var count = [];
									     										angular.forEach(items, function(value, key){
									     											chainRequest = chainRequest.then(function(){
									     												var item = value;
									     												item.st_postal_code 	= postal_code;
									     												item.st_address_line1   = address_one_one,
									     												item.st_address_line2   = address_one_two,
									     												item.st_address_line3 	= city_one;
									     												item.st_cuid 			= customerSid;
									     												item.st_id 				= cust_id;
									     												item.st_first_name 		= customerFName;
									     												item.st_last_name 		= customerLName;
									     												item.st_primary_phone_no= mobileNo;
									     												item.st_email 			= customerEmail;
									     												item.st_address_uid 	= primary_address;
																							return item.save().then(function(){
																								count.push(1);
																							});
									     											});
									     										});

									     										$q.all(count).then(function(){
									     											sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
									     											// sendCustomerSid(mobileNo);
									     											LoadingScreen.Enable = 0;
									     											deferred.resolve();
									     										});
		            					        							});
		            					        						});
		            					    						});	
									            				});
									            		});
									            	});
											});
										});
									});
								});
							}
						});
					});
				};

    			$q.all(item_type).then(function(response){

    				if(response.indexOf(1) !== -1)
    				{
						$http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'retrieveCustomerData'}).then(function(result){
							var jsonObj 	= JSON.parse(result.data);
							customerType 	= jsonObj.recordType;
							if(jsonObj.status == 200)
							{
								customerData 	= JSON.parse(jsonObj.result);
								mobileNo 		= customerData.customer.mobile;
								// mobileNo2 		= customerData.customer.mobile_2;
								cust_id 		= customerData.customer.customer_id;
								cust_sid 		= customerData.customer.cust_sid;
								customerName 	= customerData.customer.name;
								customerFName 	= customerData.customer.customer_firstname;
								customerLName 	= customerData.customer.customer_lastname;
								customerEmail 	= customerData.customer.email;
								customerGender 	= customerData.customer.gender;
								externalId 		= customerData.customer.external_id;
		            			address_one_one = customerData.customer.address_one_home_one;
		            			address_one_two = customerData.customer.address_one_home_two;
		            			address_two_one = customerData.customer.address_two_home_one;
		            			address_two_two = customerData.customer.address_two_home_two;
		            			address_three_one = customerData.customer.address_three_home_o;
		            			address_three_two = customerData.customer.address_three_home_t;
		            			
								city_one 			= customerData.customer.city_one;
								city_two 			= customerData.customer.city_two;
								city_three 			= customerData.customer.city_three;
								district_one 		= customerData.customer.district_one;
								district_two 		= customerData.customer.district_two;
								district_three 		= customerData.customer.district_three;
								province_one 		= customerData.customer.province_one;
								province_two 		= customerData.customer.province_two;
								province_three 		= customerData.customer.province_three;
								postal_code     	= customerData.customer.zip_code;
								country     		= customerData.customer.country_origin;


								var filter;

								filter = {filter: 'primary_phone_no,eq,' + mobileNo};
							
								ModelService.get('Customer', filter).then(function(data){
								   // if(data.length != 0 && customerType == 0)
								   // {
								   // 		updateCustomerFunction(data[data.length - 1], customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
								   // }
								   if(data.length != 0)
								   {
								   		updateCustomerFunctionDefault(data[data.length - 1], customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
								   }
								   else
								   {
								   		newCustomerFunction(customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
								   }
								});
								sessionStorage.setItem('hasCustomer', 1);
							}
							else
							{
								LoadingScreen.Enable = !1;
								sessionStorage.setItem('hasCustomer', 0);
								NotificationService.addAlert('Customer details is required in this transaction.', 'Customer Required', 'static', false).then(function(){
									$state.go($state.current, {}, {reload: true});
									deferred.reject();
								});
							}
						});
    				}
    				else
    				{
						var confirm = NotificationService.addConfirm('Do you want to add customer details in this transaction?', 'Customer Validation');
						confirm.then(function(x){
							if(x)
							{
								$http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'retrieveCustomerData'}).then(function(result){
									var jsonObj 	= JSON.parse(result.data);
									customerType 	= jsonObj.recordType;
									if(jsonObj.status == 200)
									{
										customerData 	= JSON.parse(jsonObj.result);
										mobileNo 		= customerData.customer.mobile;
										// mobileNo2 		= customerData.customer.mobile_2;
										cust_id 		= customerData.customer.customer_id;
										cust_sid 		= customerData.customer.cust_sid;
										customerName 	= customerData.customer.name;
										customerFName 	= customerData.customer.customer_firstname;
										customerLName 	= customerData.customer.customer_lastname;
										customerEmail 	= customerData.customer.email;
										customerGender 	= customerData.customer.gender;
										externalId 		= customerData.customer.external_id;
                            			address_one_one = customerData.customer.address_one_home_one;
                            			address_one_two = customerData.customer.address_one_home_two;
                            			address_two_one = customerData.customer.address_two_home_one;
                            			address_two_two = customerData.customer.address_two_home_two;
                            			address_three_one = customerData.customer.address_three_home_o;
                            			address_three_two = customerData.customer.address_three_home_t;
                            			
										city_one 			= customerData.customer.city_one;
										city_two 			= customerData.customer.city_two;
										city_three 			= customerData.customer.city_three;
										district_one 		= customerData.customer.district_one;
										district_two 		= customerData.customer.district_two;
										district_three 		= customerData.customer.district_three;
										province_one 		= customerData.customer.province_one;
										province_two 		= customerData.customer.province_two;
										province_three 		= customerData.customer.province_three;
										postal_code     	= customerData.customer.zip_code;
										country     		= customerData.customer.country_origin;


										// mobileNo 		= (customerData.customer.mobile != '-NA-') ? customerData.customer.mobile : '';
										// // mobileNo2 		= customerData.customer.mobile_2;
										// cust_id 		= (customerData.customer.customer_id != '-NA-') ? customerData.customer.customer_id : '';
										// cust_sid 		= (customerData.customer.cust_sid != '-NA-') ? customerData.customer.cust_sid : '';
										// customerName 	= (customerData.customer.name != '-NA-') ? customerData.customer.name : '';
										// customerFName 	= (customerData.customer.customer_firstname != '-NA-') ? customerData.customer.customer_firstname : '';
										// customerLName 	= (customerData.customer.customer_lastname != '-NA-') ? customerData.customer.customer_lastname : '';
										// customerEmail 	= (customerData.customer.email != '-NA-') ? customerData.customer.email : '';
										// customerGender 	= (customerData.customer.gender != '-NA-') ? customerData.customer.gender : '';
										// externalId 		= (customerData.customer.external_id != '-NA-') ? customerData.customer.external_id : '';
          //                   			address_one_one = (customerData.customer.address_one_home_one != '-NA-') ? customerData.customer.address_one_home_one : '';
          //                   			address_one_two = (customerData.customer.address_one_home_two != '-NA-') ? customerData.customer.address_one_home_two : '';
          //                   			address_two_one = (customerData.customer.address_two_home_one != '-NA-') ? customerData.customer.address_two_home_one: '';
          //                   			address_two_two = (customerData.customer.address_two_home_two != '-NA-') ? customerData.customer.address_two_home_two : '';
          //                   			address_three_one = (customerData.customer.address_three_home_o != '-NA-') ? customerData.customer.address_three_home_o : '';
          //                   			address_three_two = (customerData.customer.address_three_home_t != '-NA-') ? customerData.customer.address_three_home_t : '';
                            			
										// city_one 			= (customerData.customer.city_one != '-NA-') ? customerData.customer.city_one : '';
										// city_two 			= (customerData.customer.city_two != '-NA-') ? customerData.customer.city_two : '';
										// city_three 			= (customerData.customer.city_three != '-NA-') ? customerData.customer.city_three : '';
										// district_one 		= (customerData.customer.district_one != '-NA-') ? customerData.customer.district_one : '';
										// district_two 		= (customerData.customer.district_two != '-NA-') ? customerData.customer.district_two : '';
										// district_three 		= (customerData.customer.district_three != '-NA-') ? customerData.customer.district_three : '';
										// province_one 		= (customerData.customer.province_one != '-NA-') ? customerData.customer.province_one : '';
										// province_two 		= (customerData.customer.province_two != '-NA-') ? customerData.customer.province_two : '';
										// province_three 		= (customerData.customer.province_three != '-NA-') ? customerData.customer.province_three : '';
										// postal_code     	= (customerData.customer.zip_code != '-NA-') ? customerData.customer.zip_code : '';

										var filter;

										filter = {filter: 'primary_phone_no,eq,' + mobileNo};
									
										ModelService.get('Customer', filter).then(function(data){
											// if(data.length != 0 && customerType == 0)
											// {
											// 	updateCustomerFunction(data[data.length - 1], customerSid, customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
											// }
											if(data.length != 0)
											{
												updateCustomerFunctionDefault(data[data.length - 1], customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
											}
											else
											{
												newCustomerFunction(customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
											}
										});
										sessionStorage.setItem('hasCustomer', 1);
									}
									else
									{
										LoadingScreen.Enable = !1;
										sessionStorage.setItem('hasCustomer', 0);
										NotificationService.addAlert('Unable to load customer data. This transaction has no customer details.', 'Customer Data Validation', 'static', false).then(function(){
											deferred.resolve();
										});
									}
								});
							}
							else
							{
								sessionStorage.setItem('hasCustomer', 0);
								deferred.resolve();
							}
						});
    				}
    				
    			});
    		});
    	}
    	else
    	{
    		deferred.resolve();
    	}
    });

    return deferred.promise;
});

// Button Hooks for AFTER REMOVE CUSTOMER BUTTON
ButtonHooksManager.addHandler(['after_posTransactionRemoveCustomer'], function($q, $stateParams, ModelService, $http, $window, $state, NotificationService){
    var deferred = $q.defer();
    var servername = $window.location.origin;
    var removeSessionData = function(){
    	sessionStorage.removeItem('hasCustomer');
    	sessionStorage.removeItem('voucherCode');
    	sessionStorage.removeItem('voucherItems');
    	sessionStorage.removeItem('gcItem');
    	sessionStorage.removeItem('activeCustomer');
    	sessionStorage.removeItem('cardItems');
    	sessionStorage.removeItem('loyaltyItems');
    	sessionStorage.removeItem('loyaltyCode');
    	sessionStorage.removeItem('gcRedeemData');
    	sessionStorage.removeItem('gcRedeemNumber');
    }

    var activeDocumentSid = $stateParams.document_sid;

    if(sessionStorage.getItem('voucherItems') !== null && sessionStorage.getItem('loyaltyItems') === null)
    {
        var voucherItems = JSON.parse(sessionStorage.getItem('voucherItems'));
        if(voucherItems.voucherType.indexOf(1) !== -1)
        {
            var params = [{"Params":{"unspreadfrom": voucherItems.transaction_type,"discountreason":"COUPON","sid": activeDocumentSid},"MethodName":"UnSpreadDocumentDiscount"}];
            $http.post(servername + '/v1/rpc', params).then(function(){
				setTimeout(function(){
					ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
						var chainRequest = $q.when();
						var count = [];
						angular.forEach(items, function(value, key){
							chainRequest = chainRequest.then(function(){
								var item = value;
								item.st_postal_code 	= null;
								item.st_address_line1 	= null;
								item.st_address_line2 	= null;
								item.st_address_line3 	= null;
								item.st_cuid 			= null;
								item.st_id 				= null;
								item.st_first_name 		= null;
								item.st_last_name 		= null;
								item.st_primary_phone_no= null;
								item.st_email 			= null;
								item.st_address_uid 	= null;
								return item.save().then(function(){
									count.push(1);
								});
							});
						});

						$q.all(count).then(function(){
							ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
								var chainRequest = $q.when();
								if(doc[0].discounts.length > 0)
								{
									angular.forEach(doc[0].discounts, function(value, key){
										chainRequest = chainRequest.then(function(){
											return $http.delete(servername + value.link).then(function(){
											});
										});
										if(key == doc[0].discounts.length - 1)
										{
											removeSessionData();
											$state.go($state.current, {}, {reload: true});
											deferred.resolve();
										}
									});
								}
								else
								{
									removeSessionData();
									deferred.resolve();
								}
							});
						});
					});
				}, 1000);
                
            });
        }
        else
        {
        	removeSessionData();
        	deferred.resolve();
        }
    }
	
	else if(sessionStorage.getItem('loyaltyItems') !== null && sessionStorage.getItem('voucherItems') === null)
    {
        var loyaltyItems = JSON.parse(sessionStorage.getItem('loyaltyItems'));
        var params = [{"Params":{"unspreadfrom":loyaltyItems.transaction_type,"discountreason":"LOYALTY POINTS","sid": activeDocumentSid},"MethodName":"UnSpreadDocumentDiscount"}];
    	$http.post(servername + '/v1/rpc', params).then(function(){

    		var voucherItems = JSON.parse(sessionStorage.getItem('voucherItems'));
    		if(voucherItems.voucherType.indexOf(1) !== -1)
    		{
    		    var params = [{"Params":{"unspreadfrom": voucherItems.transaction_type,"discountreason":"COUPON","sid": activeDocumentSid},"MethodName":"UnSpreadDocumentDiscount"}];
    		    $http.post(servername + '/v1/rpc', params).then(function(){
    		        
    		    });
    		}

			setTimeout(function(){
				ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
					var chainRequest = $q.when();
					var count = [];
					angular.forEach(items, function(value, key){
						chainRequest = chainRequest.then(function(){
							var item = value;
							item.st_postal_code 	= null;
							item.st_address_line1 	= null;
							item.st_address_line2 	= null;
							item.st_address_line3 	= null;
							item.st_cuid 			= null;
							item.st_id 				= null;
							item.st_first_name 		= null;
							item.st_last_name 		= null;
							item.st_primary_phone_no= null;
							item.st_email 			= null;
							item.st_address_uid 	= null;
							return item.save().then(function(){
								count.push(1);
							});
						});
					});

					$q.all(count).then(function(){
						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
							var chainRequest = $q.when();
							if(doc[0].discounts.length > 0)
							{
								angular.forEach(doc[0].discounts, function(value, key){
									chainRequest = chainRequest.then(function(){
										return $http.delete(servername + value.link).then(function(){
										});
									});
									if(key == doc[0].discounts.length - 1)
									{
										removeSessionData();
										$state.go($state.current, {}, {reload: true});
										deferred.resolve();
									}
								});
							}
							else
							{
								removeSessionData();
								deferred.resolve();
							}
						});
					});
				});
			}, 1000)
    	});

    }
    else if(sessionStorage.getItem('loyaltyItems') !== null && sessionStorage.getItem('voucherItems') !== null)
    {
        var loyaltyItems = JSON.parse(sessionStorage.getItem('loyaltyItems'));
        var params = [{"Params":{"unspreadfrom":loyaltyItems.transaction_type,"discountreason":"LOYALTY POINTS","sid": activeDocumentSid},"MethodName":"UnSpreadDocumentDiscount"}];
    	$http.post(servername + '/v1/rpc', params).then(function(){
			setTimeout(function(){
				ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
					var chainRequest = $q.when();
					var count = [];
					angular.forEach(items, function(value, key){
						chainRequest = chainRequest.then(function(){
							var item = value;
							item.st_postal_code 	= null;
							item.st_address_line1 	= null;
							item.st_address_line2 	= null;
							item.st_address_line3 	= null;
							item.st_cuid 			= null;
							item.st_id 				= null;
							item.st_first_name 		= null;
							item.st_last_name 		= null;
							item.st_primary_phone_no= null;
							item.st_email 			= null;
							item.st_address_uid 	= null;
							return item.save().then(function(){
								count.push(1);
							});
						});
					});

					$q.all(count).then(function(){
						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
							var chainRequest = $q.when();
							if(doc[0].discounts.length > 0)
							{
								angular.forEach(doc[0].discounts, function(value, key){
									chainRequest = chainRequest.then(function(){
										return $http.delete(servername + value.link).then(function(){
										});
									});
									if(key == doc[0].discounts.length - 1)
									{
										removeSessionData();
										$state.go($state.current, {}, {reload: true});
										deferred.resolve();
									}
								});
							}
							else
							{
								removeSessionData();
								deferred.resolve();
							}
						});
					});
				});
			}, 1000);
    	});
    }

    else
    {
    	setTimeout(function(){
    		ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
    			var chainRequest = $q.when();
    			var count = [];
    			angular.forEach(items, function(value, key){
    				chainRequest = chainRequest.then(function(){
    					var item = value;
    					item.st_postal_code 	= null;
    					item.st_address_line1 	= null;
    					item.st_address_line2 	= null;
    					item.st_address_line3 	= null;
    					item.st_cuid 			= null;
    					item.st_id 				= null;
    					item.st_first_name 		= null;
    					item.st_last_name 		= null;
    					item.st_primary_phone_no= null;
    					item.st_email 			= null;
    					item.st_address_uid 	= null;
    					return item.save().then(function(){
    						count.push(1);
    					});
    				});
    			});

    			$q.all(count).then(function(){
    				removeSessionData();
    				$state.go($state.current, {}, {reload: true});
    				deferred.resolve();
    			});
    		});
    	}, 1000);
    }

    return deferred.promise;
});
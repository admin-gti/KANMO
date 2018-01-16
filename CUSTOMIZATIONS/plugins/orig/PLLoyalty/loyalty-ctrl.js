var loyalty = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "NotificationService", 
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, NotificationService) {
		'use strict';
		var sess = $http.defaults.headers.common['Auth-Session'];
		var servername = $window.location.origin;

		$scope.detail = {};
		var deferred = $q.defer();

		var activeDocumentSid 	= $stateParams.document_sid;
		var activeCustomer 		= ModelService.fromCache('Customer')[0];
		var type 				= [];
		var transaction_type 	= 0;
		ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
			angular.forEach(items, function(value, key){
				if(value.item_type == 3)
				{
					type.push(1);
				}
				else
				{
					type.push(0);
				}
				
			});
		});

		// Method use to close modal dialog
		$scope.closeModalDialog = function(){
		   $modalInstance.dismiss();
		   $state.go($state.current, {}, {reload: true});
		};

		// Method use to issue loyalty validation code
		$scope.applyLoyalty = function(){
			if($scope.pointsValue != '')
			{
				LoadingScreen.Enable = 1, $http.get(servername + '/plugins/PLLoyalty/loyalty.php?action=checkNetworkStatus').then(function(result){
				    var online   = JSON.parse(result.data);
				    if(online.status_code == 200)
				    {
    	    			$http.get(servername + '/plugins/PLLoyalty/loyalty.php?action=issueValidationCode&mobile=' + activeCustomer.primary_phone_no + '&points=' + $scope.pointsValue).then(function(result){
    	    				if(result.data != '' && result.data.toUpperCase() != 'FALSE')
    						{
    							var jsonObject 		= JSON.parse(result.data);
    							if(!('status_code' in jsonObject))
    							{
    								var success_request = jsonObject.response.status.success;
    								var request_code 	= jsonObject.response.status.code;
    								if(request_code == 200 && success_request)
    								{
    									LoadingScreen.Enable = 0;
    									$scope.loyaltyCode = true;
    									RN.showSuccessfulMessage('Success', jsonObject.response.validation_code.code.item_status.message);
 
    								}
    								else
    								{
    									NotificationService.addAlert(jsonObject.response.validation_code.code.item_status.message, 'Points Validation', 'static', false);
    									LoadingScreen.Enable = !1;
    								}
    							}
    							else if(jsonObject.status_code == 400)
    							{
    								NotificationService.addAlert(jsonObject.message, 'Bad Request', 'static', false);
    								LoadingScreen.Enable = !1;	
    							}
    							else if(jsonObject.status_code == 500)
    							{
    								NotificationService.addAlert(jsonObject.message, 'Network Error', 'static', false);
    								LoadingScreen.Enable = !1;	
    							}

    						}
    						else
    						{
    							NotificationService.addAlert('Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
    							    $state.go($state.current, {}, {reload: true});
    							});
    							LoadingScreen.Enable = !1;
    						}
    	    			});
				    }
				    else
				    {
				        LoadingScreen.Enable = 0;
				        if(online.status_code == 408)
				        {
				            NotificationService.addAlert(online.message, 'Request Timeout', 'static', false).then(function(){
				            });
				        }
				        else if(online.status_code == 500)
				        {
				            NotificationService.addAlert(online.message, 'Network Error', 'static', false).then(function(){
				            });
				        }
				    }
				});
			}
			else
			{
				RN.showError('Invalid Input', 'Please enter points value first!');
			}
		};

		$scope.validateLoyaltyCode = function()
		{
	    	if(sessionStorage.getItem('loyaltyCode') !== null)
	    	{
	    		// var codes = JSON.parse(sessionStorage.getItem('loyaltyCode'));
	    		// if(codes.loyaltyCode.indexOf($scope.validationCode) !== -1)
	    		// {
	    		// 	NotificationService.addAlert('Validation code is already exist! Please try different code.', 'Points Validation', 'static', false);
	    		// 	LoadingScreen.Enable = !1;
	    		// }
	    		// else
	    		// {

	    		// }

    			LoadingScreen.Enable = 1, $http.get(servername + '/plugins/PLLoyalty/loyalty.php?action=checkNetworkStatus').then(function(result){
				    var online   = JSON.parse(result.data);
				    if(online.status_code == 200)
				    {
				    	if(type.indexOf(1) !== -1)
				    	{
				    		transaction_type = 1;
				    	}

    	    			$http.get(servername + '/plugins/PLLoyalty/loyalty.php?action=checkPointsStatus&mobile=' + activeCustomer.primary_phone_no + '&points=' + $scope.pointsValue + '&v_code=' + $scope.validationCode).then(function(result){
    	    				if(result.data != '' && result.data.toUpperCase() != 'FALSE')
    						{
    							var jsonObject 		= JSON.parse(result.data);
    							if(!('status_code' in jsonObject))
    							{
    								var success_request = jsonObject.response.status.success;
    								var request_code 	= jsonObject.response.status.code;
    								var is_redeemable 	= jsonObject.response.points.redeemable.is_redeemable;
    								if(request_code == 200 && success_request.toUpperCase() == 'TRUE' && is_redeemable.toUpperCase() == 'TRUE')
    								{
    									var discount_value 	= jsonObject.response.points.redeemable.points_redeem_value;

    									ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
    										var currentDoc = doc[0];
    										if(transaction_type == 0)
    										{
    											currentDoc.manual_disc_type 	= 2;
    											currentDoc.manual_disc_value 	= discount_value;
    											currentDoc.manual_disc_reason 	= 'LOYALTY POINTS';
    										}
    										else
    										{
    											currentDoc.manual_order_disc_type 	= 2;
    											currentDoc.manual_order_disc_value 	= discount_value;
    											currentDoc.manual_order_disc_reason = 'LOYALTY POINTS';
    										}

    										currentDoc.save().then(function(){
    											if(sessionStorage.getItem('loyaltyCode') !== null)
    											{
    												var codes = JSON.parse(sessionStorage.getItem('loyaltyCode'));

    												codes.loyaltyCode.push($scope.validationCode);
    												sessionStorage.setItem('loyaltyCode', JSON.stringify(codes));
    											}
    											else
    											{
    												var codes = [];
    												codes.push($scope.validationCode);
    												sessionStorage.setItem('loyaltyCode', JSON.stringify({loyaltyCode: codes}));
    											}

    											if(sessionStorage.getItem('loyaltyItems') !== null)
    											{
    												var items = JSON.parse(sessionStorage.getItem('loyaltyItems'));
    												items.item.push({loyaltyCode: $scope.validationCode, points: $scope.pointsValue, discountValue: discount_value, mobile: activeCustomer.primary_phone_no});
    												sessionStorage.setItem('loyaltyItems', JSON.stringify({item: items.item, transaction_type: transaction_type}));
    											}
    											else
    											{
    												var item 		= [];

    												item.push({loyaltyCode: $scope.validationCode, points: $scope.pointsValue, discountValue: discount_value, mobile: activeCustomer.primary_phone_no});
    												sessionStorage.setItem('voucherItems', JSON.stringify({item: item, transaction_type: transaction_type}));
    											}

    											var params = [{"Params":{"spreadto":transaction_type,"discountreason":"LOYALTY POINTS","sid": activeDocumentSid},"MethodName":"SpreadDocumentDiscount"}];

    											$http.post(servername + '/v1/rpc', params, {headers:{"Auth-Session":sess}}).then(function(){
    												RN.showSuccessfulMessage('Success', 'Points has been applied successfully.');
    												$modalInstance.dismiss();
    												$state.go($state.current, {}, {reload: true});
    											});
    										},function(error){
												NotificationService.addAlert(error.data[0].errormsg, 'Points Validation', 'static', false);
											});

    									});
    								}
    								else
    								{
    									NotificationService.addAlert(jsonObject.response.points.redeemable.item_status.message, 'Points Validation', 'static', false);
    									LoadingScreen.Enable = !1;
    								}
    							}
    							else if(jsonObject.status_code == 400)
    							{
    								NotificationService.addAlert(jsonObject.message, 'Bad Request', 'static', false);
    								LoadingScreen.Enable = !1;	
    							}
    							else if(jsonObject.status_code == 500)
    							{
    								NotificationService.addAlert(jsonObject.message, 'Network Error', 'static', false);
    								LoadingScreen.Enable = !1;	
    							}
    						}
    						else
    						{
    							NotificationService.addAlert('Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
    							    $state.go($state.current, {}, {reload: true});
    							});
    							LoadingScreen.Enable = !1;
    						}
    	    			});
				    }
				    else
				    {
				        LoadingScreen.Enable = 0;
				        if(online.status_code == 408)
				        {
				            NotificationService.addAlert(online.message, 'Request Timeout', 'static', false).then(function(){
				            });
				        }
				        else if(online.status_code == 500)
				        {
				            NotificationService.addAlert(online.message, 'Network Error', 'static', false).then(function(){
				            });
				        }
				    }
    			});
	    	}
	    	else
	    	{
    			LoadingScreen.Enable = 1, $http.get(servername + '/plugins/PLLoyalty/loyalty.php?action=checkNetworkStatus').then(function(result){
				    var online   = JSON.parse(result.data);
				    if(online.status_code == 200)
				    {
				    	if(type.indexOf(1) !== -1)
				    	{
				    		transaction_type = 1;
				    	}

    	    			$http.get(servername + '/plugins/PLLoyalty/loyalty.php?action=checkPointsStatus&mobile=' + activeCustomer.primary_phone_no + '&points=' + $scope.pointsValue + '&v_code=' + $scope.validationCode).then(function(result){
    	    				if(result.data != '' && result.data.toUpperCase() != 'FALSE')
    						{
    							var jsonObject 		= JSON.parse(result.data);
    							if(!('status_code' in jsonObject))
    							{
    								var success_request = jsonObject.response.status.success;
    								var request_code 	= jsonObject.response.status.code;
    								var is_redeemable 	= jsonObject.response.points.redeemable.is_redeemable;
    								if(request_code == 200 && success_request.toUpperCase() == 'TRUE' && is_redeemable.toUpperCase() == 'TRUE')
    								{
    									var discount_value 	= jsonObject.response.points.redeemable.points_redeem_value;

    									ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
    										var currentDoc = doc[0];
    										if(transaction_type == 0)
    										{
    											currentDoc.manual_disc_type 	= 2;
    											currentDoc.manual_disc_value 	= discount_value;
    											currentDoc.manual_disc_reason 	= 'LOYALTY POINTS';
    										}
    										else
    										{
    											currentDoc.manual_order_disc_type 	= 2;
    											currentDoc.manual_order_disc_value 	= discount_value;
    											currentDoc.manual_order_disc_reason = 'LOYALTY POINTS';
    										}

    										currentDoc.save().then(function(){
    											if(sessionStorage.getItem('loyaltyCode') !== null)
    											{
    												var codes = JSON.parse(sessionStorage.getItem('loyaltyCode'));

    												codes.loyaltyCode.push($scope.validationCode);
    												sessionStorage.setItem('loyaltyCode', JSON.stringify(codes));
    											}
    											else
    											{
    												var codes = [];
    												codes.push($scope.validationCode);
    												sessionStorage.setItem('loyaltyCode', JSON.stringify({loyaltyCode: codes}));
    											}

    											if(sessionStorage.getItem('loyaltyItems') !== null)
    											{
    												var items = JSON.parse(sessionStorage.getItem('loyaltyItems'));
    												items.item.push({loyaltyCode: $scope.validationCode, points: $scope.pointsValue, discountValue: discount_value, mobile: activeCustomer.primary_phone_no});
    												sessionStorage.setItem('loyaltyItems', JSON.stringify({item: items.item, transaction_type: transaction_type}));
    											}
    											else
    											{
    												var item 		= [];

    												item.push({loyaltyCode: $scope.validationCode, points: $scope.pointsValue, discountValue: discount_value, mobile: activeCustomer.primary_phone_no});
    												sessionStorage.setItem('loyaltyItems', JSON.stringify({item: item, transaction_type: transaction_type}));
    											}

    											var params = [{"Params":{"spreadto":transaction_type,"discountreason":"LOYALTY POINTS","sid": activeDocumentSid},"MethodName":"SpreadDocumentDiscount"}];

    											$http.post(servername + '/v1/rpc', params, {headers:{"Auth-Session":sess}}).then(function(){
    												RN.showSuccessfulMessage('Success', 'Points has been applied successfully.');
    												$modalInstance.dismiss();
    												$state.go($state.current, {}, {reload: true});
    											});
    										},function(error){
												NotificationService.addAlert(error.data[0].errormsg, 'Points Validation', 'static', false);
											});

    									});
    								}
    								else
    								{
    									NotificationService.addAlert(jsonObject.response.points.redeemable.item_status.message, 'Points Validation', 'static', false);
    									LoadingScreen.Enable = !1;
    								}
    							}
    							else if(jsonObject.status_code == 400)
    							{
    								NotificationService.addAlert(jsonObject.message, 'Bad Request', 'static', false);
    								LoadingScreen.Enable = !1;	
    							}
    							else if(jsonObject.status_code == 500)
    							{
    								NotificationService.addAlert(jsonObject.message, 'Network Error', 'static', false);
    								LoadingScreen.Enable = !1;	
    							}
    						}
    						else
    						{
    							NotificationService.addAlert('Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
    							    $state.go($state.current, {}, {reload: true});
    							});
    							LoadingScreen.Enable = !1;
    						}
    	    			});
				    }
				    else
				    {
				        LoadingScreen.Enable = 0;
				        if(online.status_code == 408)
				        {
				            NotificationService.addAlert(online.message, 'Request Timeout', 'static', false).then(function(){
				            });
				        }
				        else if(online.status_code == 500)
				        {
				            NotificationService.addAlert(online.message, 'Network Error', 'static', false).then(function(){
				            });
				        }
				    }
    			});
	    	}

		};
}];

window.angular.module('prismPluginsSample.controller.loyaltyCtrl', [])
   .controller('loyaltyCtrl', loyalty)
   .directive('focusMe', function () {
       return {
           link: function(scope, element, attrs) {
               scope.$watch(attrs.focusMe, function(value) {
                   if(value === true) {
                       element[0].focus();
                       element[0].select();
                   }
               });
           }
       };
   });

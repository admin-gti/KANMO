var voucher = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "DocumentPersistedData", "NotificationService",
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, DocumentPersistedData, NotificationService) {
		'use strict';
		var sess 				= $http.defaults.headers.common['Auth-Session'];
		var servername 			= $window.location.origin;
		var api_url 			= 'https://us.api.capillarytech.com/v1.1/coupon/';

		var existingItems 		= [];
		var deferred 			= $q.defer();
		var activeDocumentSid 	= $stateParams.document_sid;

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
		$scope.cancelModalDialog = function(){
		   $modalInstance.dismiss();
		   $state.go($state.current, {}, {reload: true});
		};

		// Method use to save instalment detail from the user
		$scope.redeemVoucher = function(){
			LoadingScreen.Enable = !0;
			if(type.indexOf(1) !== -1)
			{
				transaction_type = 1;
			}

			if($scope.voucherCode != '' && $scope.voucherCode != null)
			{
				if(sessionStorage.getItem('voucherCode') !== null)
				{
					var codes = JSON.parse(sessionStorage.getItem('voucherCode'));
					if(codes.voucherCode.indexOf($scope.voucherCode) !== -1)
					{
						NotificationService.addAlert('Voucher code is already exist! Please try different voucher code.', 'Voucher Validation', 'static', false);
						LoadingScreen.Enable = !1;
					}
					else
					{
						ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
							$http.get(servername + '/plugins/PLVoucher/redeemVoucher.php?action=checkVoucherStatus&code=' + $scope.voucherCode + '&mobile=' + doc[0].bt_primary_phone_no, {headers:{"Auth-Session": sess}}).then(function(result){
								if(result.data != '' && result.data.toUpperCase() != 'FALSE')
								{
									var jsonObject = JSON.parse(result.data);
									if(!('status_code' in jsonObject))
									{
										var success_request = jsonObject.response.status.success;
										var request_code 	= jsonObject.response.status.code;
										var request_message = jsonObject.response.status.message;

										var date 		= new Date();
										var dateToday 	= new Date(date.toISOString().substring(0, 10));

										if(success_request.toUpperCase() == 'TRUE' && request_code == 200 && request_message.toUpperCase() == 'SUCCESS')
										{
											var is_redeemable 	= jsonObject.response.coupons.redeemable.is_redeemable;
											var discount_type 	= jsonObject.response.coupons.redeemable.series_info.discount_type;
											var discount_value 	= jsonObject.response.coupons.redeemable.series_info.discount_value;
											var coupon_name 	= jsonObject.response.coupons.redeemable.series_info.description;

											var valid_till 		= new Date(jsonObject.response.coupons.redeemable.series_info.valid_till);
											if(is_redeemable.toUpperCase() == 'TRUE' && valid_till >= dateToday)
											{
												
												if(discount_value > 0)
												{
													var manual_disc_type = 0;
													if(discount_type.toUpperCase() == 'PERC')
													{
														manual_disc_type = 1;
													}
													else if(discount_type.toUpperCase() == 'ABS')
													{
														manual_disc_type = 2;
													}

													var currentDoc = doc[0];
													if(transaction_type == 0)
													{
														currentDoc.manual_disc_type 	= manual_disc_type;
														currentDoc.manual_disc_value 	= discount_value;
														currentDoc.manual_disc_reason 	= 'COUPON';
													}
													else
													{
														currentDoc.manual_order_disc_type 	= manual_disc_type;
														currentDoc.manual_order_disc_value 	= discount_value;
														currentDoc.manual_order_disc_reason = 'COUPON';
													}
													
													currentDoc.save().then(function(){
														
														if(sessionStorage.getItem('voucherCode') !== null)
														{
															var codes = JSON.parse(sessionStorage.getItem('voucherCode'));

															codes.voucherCode.push($scope.voucherCode);
															sessionStorage.setItem('voucherCode', JSON.stringify(codes));
														}
														else
														{
															var codes = [];
															codes.push($scope.voucherCode);
															sessionStorage.setItem('voucherCode', JSON.stringify({voucherCode: codes}));
														}

														if(sessionStorage.getItem('voucherItems') !== null)
														{
															var items = JSON.parse(sessionStorage.getItem('voucherItems'));
															items.item.push({voucherCode: $scope.voucherCode, discountType: manual_disc_type, discountValue: discount_value});
															items.voucherType.push(1);
															sessionStorage.setItem('voucherItems', JSON.stringify({item: items.item, voucherType: items.voucherType, transaction_type: transaction_type}));
														}
														else
														{
															var item 		= [];
															var voucherType = [];

															item.push({voucherCode: $scope.voucherCode, discountType: manual_disc_type, discountValue: discount_value});
															voucherType.push(1);
															sessionStorage.setItem('voucherItems', JSON.stringify({item: item, voucherType: voucherType, transaction_type: transaction_type}));
														}

														// ModelService.get('Document', {sid: activeDocumentSid}).then(function(new_doc){
														// 	var spreadDiscount = new_doc[0];
														// 	var params = {"spreadto": 0};
														// 	spreadDiscount.spreadDiscount(params).then(function(){
														// 		RN.showSuccessfulMessage('Success', 'Voucher ' + $scope.voucherCode + ' has been applied successfully.');
														// 		$modalInstance.dismiss();
														// 		$state.go($state.current, {}, {reload: true});
														// 	});
														// });

														var params = [{"Params":{"spreadto":transaction_type,"discountreason":"COUPON","sid": doc[0].sid},"MethodName":"SpreadDocumentDiscount"}];

														$http.post(servername + '/v1/rpc', params, {headers:{"Auth-Session":sess}}).then(function(){
															RN.showSuccessfulMessage('Success', 'Voucher ' + $scope.voucherCode + ' has been applied successfully.');
															$modalInstance.dismiss();
															$state.go($state.current, {}, {reload: true});
														});
													},function(error){
														NotificationService.addAlert(error.data[0].errormsg, 'Voucher Validation', 'static', false);
													});
												}
												else
												{
													$http.get(servername + '/api/backoffice/promotionslist/pcppromotion/pcpvalidationcoupon?filter=couponcode,eq,'+ coupon_name).then(function(result){
														if(result.data.data.length > 0)
														{
															if(sessionStorage.getItem('voucherCode') !== null)
															{
																var codes = JSON.parse(sessionStorage.getItem('voucherCode'));
																codes.voucherCode.push($scope.voucherCode);
																sessionStorage.setItem('voucherCode', JSON.stringify(codes));
															}
															else
															{
																var codes = [];
																codes.push($scope.voucherCode);
																sessionStorage.setItem('voucherCode', JSON.stringify({voucherCode: codes}));
															}

															if(sessionStorage.getItem('voucherItems') !== null)
															{
																var items 	= JSON.parse(sessionStorage.getItem('voucherItems'));
																items.item.push({voucherCode: $scope.voucherCode, discountType: 0, discountValue: 0});
																items.voucherType.push(2);

																var voucherItems = {item: items.item, voucherType: items.voucherType, transaction_type: transaction_type};
																sessionStorage.setItem('voucherItems', JSON.stringify(voucherItems));
															}
															else
															{
																var voucherType = [];
																var item 		= [];
																item.push({voucherCode: $scope.voucherCode, discountType: 0, discountValue: 0});
																voucherType.push(2);
																sessionStorage.setItem('voucherItems', JSON.stringify({item: item, voucherType: voucherType, transaction_type: transaction_type}));
															}

															var params = [{coupon_code: coupon_name, in_or_out: 1, doc_sid: doc[0].sid, origin_application: 'RProPrismWeb'}];
															$http.post(servername + '/v1/rest/document/'+ doc[0].sid + '/coupon', params).then(function(){
																var params = [{"Params":{"DocumentSid": doc[0].sid},"MethodName":"PCPromoApplyManually"}];
																$http.post(servername + '/v1/rpc', params, {headers:{"Auth-Session":sess}}).then(function(){
																	RN.showSuccessfulMessage('Success', 'Voucher ' + $scope.voucherCode + ' has been applied successfully.');
																	$modalInstance.dismiss();
																	$state.go($state.current, {}, {reload: true});
																});
															});
														}
														else
														{
															NotificationService.addAlert('Unable to find Vouchen Code in the list. Please check your input!', 'Voucher Validation', 'static', false);
														}
													});
												}
											}
											else
											{
												NotificationService.addAlert('Current voucher is already expired.', 'Voucher Validation', 'static', false);
												LoadingScreen.Enable = !1;
											}
											
											LoadingScreen.Enable = !1;
										}
										else
										{
											if(jsonObject.response.coupons.redeemable.item_status.code == 736)
											{
												NotificationService.addAlert('Coupon is already used!', 'Voucher Validation', 'static', false);
												LoadingScreen.Enable = !1;
											}
											else if(request_code == 0)
											{
												LoadingScreen.Enable = !1;
												NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
											}
											else
											{
												NotificationService.addAlert(jsonObject.response.coupons.redeemable.item_status.message, 'Voucher Validation', 'static', false);
												LoadingScreen.Enable = !1;
											}
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
						});
					}
				}
				else
				{
					ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
						$http.get(servername + '/plugins/PLVoucher/redeemVoucher.php?action=checkVoucherStatus&code=' + $scope.voucherCode + '&mobile=' + doc[0].bt_primary_phone_no, {headers:{"Auth-Session": sess}}).then(function(result){
							if(result.data != '' && result.data.toUpperCase() != 'FALSE')
							{
								var jsonObject = JSON.parse(result.data);
								if(!('status_code' in jsonObject))
								{
									var success_request = jsonObject.response.status.success;
									var request_code 	= jsonObject.response.status.code;
									var request_message = jsonObject.response.status.message;

									var date 		= new Date();
									var dateToday 	= new Date(date.toISOString().substring(0, 10));

									if(success_request.toUpperCase() == 'TRUE' && request_code == 200 && request_message.toUpperCase() == 'SUCCESS')
									{
										var is_redeemable 	= jsonObject.response.coupons.redeemable.is_redeemable;
										var discount_type 	= jsonObject.response.coupons.redeemable.series_info.discount_type;
										var discount_value 	= jsonObject.response.coupons.redeemable.series_info.discount_value;
										var coupon_name 	= jsonObject.response.coupons.redeemable.series_info.description;

										var valid_till 		= new Date(jsonObject.response.coupons.redeemable.series_info.valid_till);
										if(is_redeemable.toUpperCase() == 'TRUE' && valid_till >= dateToday)
										{
											
											if(discount_value > 0)
											{
												var manual_disc_type = 0;
												if(discount_type.toUpperCase() == 'PERC')
												{
													manual_disc_type = 1;
												}
												else if(discount_type.toUpperCase() == 'ABS')
												{
													manual_disc_type = 2;
												}

												var currentDoc = doc[0];
												if(transaction_type == 0)
												{
													currentDoc.manual_disc_type 	= manual_disc_type;
													currentDoc.manual_disc_value 	= discount_value;
													currentDoc.manual_disc_reason 	= 'COUPON';
												}
												else
												{
													currentDoc.manual_order_disc_type 	= manual_disc_type;
													currentDoc.manual_order_disc_value 	= discount_value;
													currentDoc.manual_order_disc_reason = 'COUPON';
												}
												currentDoc.save().then(function(){
													
													if(sessionStorage.getItem('voucherCode') !== null)
													{
														var codes = JSON.parse(sessionStorage.getItem('voucherCode'));

														codes.voucherCode.push($scope.voucherCode);
														sessionStorage.setItem('voucherCode', JSON.stringify(codes));
													}
													else
													{
														var codes = [];
														codes.push($scope.voucherCode);
														sessionStorage.setItem('voucherCode', JSON.stringify({voucherCode: codes}));
													}

													if(sessionStorage.getItem('voucherItems') !== null)
													{
														var items = JSON.parse(sessionStorage.getItem('voucherItems'));
														items.item.push({voucherCode: $scope.voucherCode, discountType: manual_disc_type, discountValue: discount_value});
														items.voucherType.push(1);
														sessionStorage.setItem('voucherItems', JSON.stringify({item: items.item, voucherType: items.voucherType, transaction_type: transaction_type}));
													}
													else
													{
														var item 		= [];
														var voucherType = [];

														item.push({voucherCode: $scope.voucherCode, discountType: manual_disc_type, discountValue: discount_value});
														voucherType.push(1);
														sessionStorage.setItem('voucherItems', JSON.stringify({item: item, voucherType: voucherType, transaction_type: transaction_type}));
													}

													// ModelService.get('Document', {sid: activeDocumentSid}).then(function(new_doc){
													// 	var spreadDiscount = new_doc[0];
													// 	var params = {"spreadto": 0};
													// 	spreadDiscount.spreadDiscount(params).then(function(){
													// 		RN.showSuccessfulMessage('Success', 'Voucher ' + $scope.voucherCode + ' has been applied successfully.');
													// 		$modalInstance.dismiss();
													// 		$state.go($state.current, {}, {reload: true});
													// 	});
													// });

													var params = [{"Params":{"spreadto":transaction_type,"discountreason":"COUPON","sid": doc[0].sid},"MethodName":"SpreadDocumentDiscount"}];

													$http.post(servername + '/v1/rpc', params, {headers:{"Auth-Session":sess}}).then(function(){
														RN.showSuccessfulMessage('Success', 'Voucher ' + $scope.voucherCode + ' has been applied successfully.');
														$modalInstance.dismiss();
														$state.go($state.current, {}, {reload: true});
													});
												},function(error){
													NotificationService.addAlert(error.data[0].errormsg, 'Voucher Validation', 'static', false);
												});
											}
											else
											{
												$http.get(servername + '/api/backoffice/promotionslist/pcppromotion/pcpvalidationcoupon?filter=couponcode,eq,'+ coupon_name).then(function(result){
													if(result.data.data.length > 0)
													{
														if(sessionStorage.getItem('voucherCode') !== null)
														{
															var codes = JSON.parse(sessionStorage.getItem('voucherCode'));
															codes.voucherCode.push($scope.voucherCode);
															sessionStorage.setItem('voucherCode', JSON.stringify(codes));
														}
														else
														{
															var codes = [];
															codes.push($scope.voucherCode);
															sessionStorage.setItem('voucherCode', JSON.stringify({voucherCode: codes}));
														}

														if(sessionStorage.getItem('voucherItems') !== null)
														{
															var items 	= JSON.parse(sessionStorage.getItem('voucherItems'));
															items.item.push({voucherCode: $scope.voucherCode, discountType: 0, discountValue: 0});
															items.voucherType.push(2);

															var voucherItems = {item: items.item, voucherType: items.voucherType, transaction_type: transaction_type};
															sessionStorage.setItem('voucherItems', JSON.stringify(voucherItems));
														}
														else
														{
															var voucherType = [];
															var item 		= [];
															item.push({voucherCode: $scope.voucherCode, discountType: 0, discountValue: 0});
															voucherType.push(2);
															sessionStorage.setItem('voucherItems', JSON.stringify({item: item, voucherType: voucherType, transaction_type: transaction_type}));
														}

														var params = [{coupon_code: coupon_name, in_or_out: 1, doc_sid: doc[0].sid, origin_application: 'RProPrismWeb'}];
														$http.post(servername + '/v1/rest/document/'+ doc[0].sid + '/coupon', params).then(function(){
															var params = [{"Params":{"DocumentSid": doc[0].sid},"MethodName":"PCPromoApplyManually"}];
															$http.post(servername + '/v1/rpc', params, {headers:{"Auth-Session":sess}}).then(function(){
																RN.showSuccessfulMessage('Success', 'Voucher ' + $scope.voucherCode + ' has been applied successfully.');
																$modalInstance.dismiss();
																$state.go($state.current, {}, {reload: true});
															});
														});
													}
													else
													{
														NotificationService.addAlert('Unable to find Vouchen Code in the list. Please check your input!', 'Voucher Validation', 'static', false);
													}
												});
											}
										}
										else
										{
											NotificationService.addAlert('Current voucher is already expired.', 'Voucher Validation', 'static', false);
											LoadingScreen.Enable = !1;
										}
										
										LoadingScreen.Enable = !1;
									}
									else
									{
										if(jsonObject.response.coupons.redeemable.item_status.code == 736)
										{
											NotificationService.addAlert('Coupon is already used!', 'Voucher Validation', 'static', false);
											LoadingScreen.Enable = !1;
										}
										else if(request_code == 0)
										{
											LoadingScreen.Enable = !1;
											NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
										}
										else
										{
											NotificationService.addAlert(jsonObject.response.coupons.redeemable.item_status.message, 'Voucher Validation', 'static', false);
											LoadingScreen.Enable = !1;
										}
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
					});
				}
			}
			else
			{
				LoadingScreen.Enable = !1;
				RN.showError('Voucher Code Validation','Voucher code is required!');
			}
		};
}];

window.angular.module('prismPluginsSample.controller.voucherCtrl', [])
   .controller('voucherCtrl', voucher)
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

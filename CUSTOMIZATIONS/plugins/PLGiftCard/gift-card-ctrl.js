var giftCard = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "DocumentPersistedData", "NotificationService", "sharedProperties",
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, DocumentPersistedData, NotificationService, sharedProperties) {
		'use strict';
		var sess 				= $http.defaults.headers.common['Auth-Session'];
		var servername 			= $window.location.origin;

		var existingItems 		= [];
		var deferred 			= $q.defer();
		var activeDocumentSid 	= $stateParams.document_sid;

		$scope.gcCount = '';
		// Method use to close modal dialog
		$scope.closeModalDialog = function(){
			$modalInstance.dismiss();
			$state.go($state.current, {}, {reload: true});
		};

		$scope.gcType = 'SINGLE';

		$scope.saveGCType = function(){
			if($scope.gcType == 'SINGLE')
			{
				sharedProperties.setGCCount(0);
				var modalOptions = {
				        backdrop: 'static',
				        // windowClass: 'sm',
				        size: 'md', // sm, md, lg
				        templateUrl: '/plugins/PLGiftCard/validateCard.htm',
				        controller: 'validateCardCtrl',
				        keyboard: false
				    };
				$modal.open(modalOptions);
			}
			else
			{
				if($scope.gcCount != '')
				{
					sharedProperties.setGCCount(parseInt($scope.gcCount) - 1);
					var modalOptions = {
					        backdrop: 'static',
					        // windowClass: 'sm',
					        size: 'md', // sm, md, lg
					        templateUrl: '/plugins/PLGiftCard/validateCard.htm',
					        controller: 'validateCardCtrl',
					        keyboard: false
					    };
					$modal.open(modalOptions);
				}
				else
				{
					RN.showError('Error','Number of card(s) is required!');
				}
			}
		};
}];

window.angular.module('prismPluginsSample.controller.giftCardCtrl', [])
   .controller('giftCardCtrl', giftCard)
   .service('sharedProperties', function () {
           var gcNumber, gcCount;

           return {
               getGCNumber: function () {
                   return gcNumber;
               },
               setGCNumber: function(value) {
                   gcNumber = value;
               },
               getGCCount: function () {
                   return gcCount;
               },
               setGCCount: function(value) {
                   gcCount = value;
               }
           };
       });


var redeemCard = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "DocumentPersistedData", "NotificationService",
  	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, DocumentPersistedData, NotificationService) {
  		'use strict';
  		var sess 				= $http.defaults.headers.common['Auth-Session'];
  		var servername 			= $window.location.origin;

  		var existingItems 		= [];
  		var deferred 			= $q.defer();
  		var activeDocumentSid 	= $stateParams.document_sid;
  		// Method use to close modal dialog
  		$scope.closeModalDialog = function(){
  			$modalInstance.dismiss();
  		};

  		$scope.applyCard = function(){
  			if($scope.gcNumber != '' && $scope.gcNumber != null && typeof $scope.gcNumber != 'undefined')
  			{
  				if(sessionStorage.getItem('gcRedeemNumber') !== null)
  				{
    				LoadingScreen.Enable = 1, $http.get(servername + '/plugins/PLGiftCard/giftCard.php?action=checkGCStatus&cardNumber='+ $scope.gcNumber).then(function(result){
	    	    		if(result.data != '' && result.data.toUpperCase() != 'FALSE')
	    	    		{
	    	    			var obj = JSON.parse(result.data);
	    	    			if(obj == 'false')
	    	    			{
	    	    				LoadingScreen.Enable = 0;
	    	    				NotificationService.addAlert('Unable to retrieve external resources. Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
	    	    				});
	    	    			}
	    	    			else
	    	    			{
	    	    				if(obj.api_status.key == 'ERR_RESPONSE_SUCCESS')
	    	    				{
	    	    					if(sessionStorage.getItem('gcRedeemData') === null)
	    	    					{
	    	    						sessionStorage.setItem('gcRedeemData', JSON.stringify([{cardNumber: $scope.gcNumber, amount: 0, tender_sid: '', redeemableValue: obj.response.gift_card.current_value}]));
	    	    						sessionStorage.setItem('gcRedeemNumber', $scope.gcNumber);
	    	    						RN.showSuccessfulMessage('Success', 'Card Number has been applied successfully.');
	    	    						$modalInstance.dismiss();
	    	    						LoadingScreen.Enable = 0;
	    	    					}
	    	    					else
	    	    					{
	    	    						var gcRedeemData = JSON.parse(sessionStorage.getItem('gcRedeemData'));
	    	    						gcRedeemData.push({cardNumber: $scope.gcNumber, amount: 0, tender_sid: '', redeemableValue: obj.response.gift_card.current_value});
	    	    						sessionStorage.setItem('gcRedeemData', JSON.stringify(gcRedeemData));
	    	    						sessionStorage.setItem('gcRedeemNumber', $scope.gcNumber);
	    	    						$modalInstance.dismiss();
	    	    						LoadingScreen.Enable = 0;
	    	    					}

	    	    				}
	    	    				else if(obj.api_status.key == 0)
	    	    				{
	    	    					NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
	    	    					LoadingScreen.Enable = 0;
	    	    				}
	    	    				else
	    	    				{
	    	    					RN.showError('Error','Card number does not exist!');
	    	    					LoadingScreen.Enable = 0;
	    	    				}
	    	    			}
	    	    		}
	    	    		else
	    	    		{
	    	    			NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
	    	    			LoadingScreen.Enable = 0;
	    	    		}
	    	    	});
  				}
  				else
  				{
    				LoadingScreen.Enable = 1, $http.get(servername + '/plugins/PLGiftCard/giftCard.php?action=checkGCStatus&cardNumber='+ $scope.gcNumber).then(function(result){
	    	    		if(result.data != '' && result.data.toUpperCase() != 'FALSE')
	    	    		{
	    	    			var obj = JSON.parse(result.data);
	    	    			if(obj == 'false')
	    	    			{
	    	    				LoadingScreen.Enable = 0;
	    	    				NotificationService.addAlert('Unable to retrieve external resources. Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
	    	    				});
	    	    			}
	    	    			else
	    	    			{
	    	    				if(obj.api_status.key == 'ERR_RESPONSE_SUCCESS')
	    	    				{
	    	    					if(sessionStorage.getItem('gcRedeemData') === null)
	    	    					{
	    	    						sessionStorage.setItem('gcRedeemData', JSON.stringify([{cardNumber: $scope.gcNumber, amount: 0, tender_sid: '', redeemableValue: obj.response.gift_card.current_value}]));
	    	    						sessionStorage.setItem('gcRedeemNumber', $scope.gcNumber);
	    	    						RN.showSuccessfulMessage('Success', 'Card Number has been applied successfully.');
	    	    						$modalInstance.dismiss();
	    	    						LoadingScreen.Enable = 0;
	    	    					}
	    	    					else
	    	    					{
	    	    						var gcRedeemData = JSON.parse(sessionStorage.getItem('gcRedeemData'));
	    	    						gcRedeemData.push({cardNumber: $scope.gcNumber, amount: 0, tender_sid: '', redeemableValue: obj.response.gift_card.current_value});
	    	    						sessionStorage.setItem('gcRedeemData', JSON.stringify(gcRedeemData));
	    	    						sessionStorage.setItem('gcRedeemNumber', $scope.gcNumber);
	    	    						RN.showSuccessfulMessage('Success', 'Card Number has been applied successfully.');
	    	    						$modalInstance.dismiss();
	    	    						LoadingScreen.Enable = 0;
	    	    					}

	    	    				}
	    	    				else if(obj.api_status.key == 0)
	    	    				{
	    	    					NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
	    	    					LoadingScreen.Enable = 0;
	    	    				}
	    	    				else
	    	    				{
	    	    					RN.showError('Error','Card number does not exist!');
	    	    					LoadingScreen.Enable = 0;
	    	    				}
	    	    			}
	    	    		}
	    	    		else
	    	    		{
	    	    			NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
	    	    			LoadingScreen.Enable = 0;
	    	    		}
	    	    	});
  				}
  			}
  			else
  			{
  				RN.showError('Error', 'Please enter card number first!');
  			}
  		};
  }];

window.angular.module('prismPluginsSample.controller.redeemCardCtrl', [])
  .controller('redeemCardCtrl', redeemCard)
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

  	var validateCard = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "DocumentPersistedData", "NotificationService", "sharedProperties",
    	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, DocumentPersistedData, NotificationService, sharedProperties) {
    		'use strict';
    		var sess 				= $http.defaults.headers.common['Auth-Session'];
    		var servername 			= $window.location.origin;

    		var existingItems 		= [];
    		var deferred 			= $q.defer();
    		var activeDocumentSid 	= $stateParams.document_sid;
    		// Method use to close modal dialog
    		$scope.closeModalDialog = function(){
    			$modalInstance.dismiss();
    			$state.go($state.current, {}, {reload: true});
    			
    		};

    		$scope.validateGC = function(){

    			if($scope.gcNumber == '')
    			{
    				RN.showError('Error','Please enter card number first!');
    			}
    			else
    			{
    				LoadingScreen.Enable = 1, $http.get(servername + '/plugins/PLGiftCard/giftCard.php?action=checkGCStatus&cardNumber='+ $scope.gcNumber).then(function(result){
			    		if(result.data != '' && result.data.toUpperCase() != 'FALSE')
			    		{
			    			var obj = JSON.parse(result.data);
			    			if(obj == 'false')
			    			{
			    				LoadingScreen.Enable = 0;
			    				NotificationService.addAlert('Unable to retrieve external resources. Your network connection is too slow!', 'Request Timeout', 'static', false).then(function(){
			    				});
			    			}
			    			else
			    			{
			    				if(obj.api_status.key == 'ERR_RESPONSE_SUCCESS')
			    				{
			    					sharedProperties.setGCNumber($scope.gcNumber);
		    						var modalOptions = {
		    						        backdrop: 'static',
		    						        // windowClass: 'sm',
		    						        size: 'md', // sm, md, lg
		    						        templateUrl: '/plugins/PLGiftCard/gcDenominations.htm',
		    						        controller: 'gcDenominationsCtrl',
		    						        keyboard: false
		    						    };
		    						$modal.open(modalOptions);
		    						LoadingScreen.Enable = 0;
			    				}
			    				else if(obj.api_status.key == 0)
			    				{
			    					NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
			    					LoadingScreen.Enable = 0;
			    				}
			    				else
			    				{
			    					RN.showError('Error','Card number '+ $scope.gcNumber + ' does not exist!');
			    					LoadingScreen.Enable = 0;
			    				}
			    			}
			    		}
			    		else
			    		{
			    			NotificationService.addAlert('Network connection error. Possible cause no internet connection or slow internet speed.', 'Network Error', 'static', false);
			    			LoadingScreen.Enable = 0;
			    		}
			    	});
    			}


    		};
    }];

  window.angular.module('prismPluginsSample.controller.validateCardCtrl', [])
    .controller('validateCardCtrl', validateCard)
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

  	var gcDenominations = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "DocumentPersistedData", "NotificationService", "sharedProperties", "$modalStack",
    	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, DocumentPersistedData, NotificationService, sharedProperties, $modalStack) {
    		'use strict';
    		var sess 				= $http.defaults.headers.common['Auth-Session'];
    		var servername 			= $window.location.origin;

    		var existingItems 		= [];
    		var deferred 			= $q.defer();
    		var activeDocumentSid 	= $stateParams.document_sid;

    		$http.get(servername + '/v1/rest/inventory?cols=*&filter=(dcs_code,eq,888800101)AND(active,eq,true)&sort=alu,asc').then(function(gcItems){
    			var items = [];
    			var chainRequest = $q.when();
    			angular.forEach(gcItems.data, function(value, key){
    				chainRequest = chainRequest.then(function(){
    					return $http.get(servername + value.sbsinventoryactiveprices[0].link + '?cols=*').then(function(price){
    						gcItems.data[key].active_price = price.data[0].price;
    						items.push(gcItems.data[key]);
    					});
    				});
    				
    			});

    			$q.all(items).then(function(result){
    				$scope.gcItems = items;
    			});
    		});

    		// Method use to close modal dialog
    		$scope.closeModalDialog = function(){
    			if(sharedProperties.getGCCount() != 0)
    			{
    				sharedProperties.setGCCount(sharedProperties.getGCCount() - 1);
    				$modalInstance.dismiss();
    				var modalOptions = {
    				        backdrop: 'static',
    				        // windowClass: 'sm',
    				        size: 'md', // sm, md, lg
    				        templateUrl: '/plugins/PLGiftCard/validateCard.htm',
    				        controller: 'validateCardCtrl',
    				        keyboard: false
    				    };
    				$modal.open(modalOptions);

    			}
    			else
    			{
    				$modalStack.dismissAll();
    				$state.go($state.current, {}, {reload: true});
    			}


    		};

    		$scope.addGCAmount = function(amount, alu){
    			ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
    				if(doc[0].items.length == 0)
    				{
    					$http.get(servername + '/v1/rest/inventory?cols=*&filter=(dcs_code,eq,888800101)AND(alu,eq,'+ alu +')').then(function(item){
    						var currentDoc = doc[0];

    						var newItem = ModelService.create('Item');
    							newItem.origin_application 	= 'RProPrismWeb';
    							newItem.invn_sbs_item_sid 	= item.data[0].sid;
    							newItem.fulfill_store_sid 	= item.data[0].fulfill_store_sid;
    							newItem.document_sid 		= doc[0].sid;
    							newItem.note5 				= sharedProperties.getGCNumber();
    							currentDoc.addItem(newItem).then(function(){
    								$state.go($state.current, {}, {reload: true});
    							});
    					});
    				}
    				else
    				{
    					$http.get(servername + '/v1/rest/document/' + doc[0].sid + '/item?cols=*&filter=(document_sid,eq,' + doc[0].sid + ')AND(alu,eq,' + alu + ')').then(function(items){
    						if(items.data.length > 0 && items.data[0].note5 == sharedProperties.getGCNumber())
    						{
    							var itemData = [{'quantity' : items.data[0].quantity + 1}];
    							$http.put(servername + '/v1/rest/document/' + doc[0].sid + '/item/' + items.data[0].sid + '?filter=row_version,eq,'+ items.data[0].row_version, itemData).then(function(){
    								$state.go($state.current, {}, {reload: true});
    							});

    						}
    						else
    						{
    							$http.get(servername + '/v1/rest/inventory?cols=*&filter=(dcs_code,eq,888800101)AND(alu,eq,'+ alu +')').then(function(item){
    								var currentDoc = doc[0];

    								var newItem = ModelService.create('Item');
    									newItem.origin_application 	= 'RProPrismWeb';
    									newItem.invn_sbs_item_sid 	= item.data[0].sid;
    									newItem.fulfill_store_sid 	= item.data[0].fulfill_store_sid;
    									newItem.document_sid 		= doc[0].sid;
    									newItem.note5 				= sharedProperties.getGCNumber();
    									currentDoc.addItem(newItem).then(function(){
    										$state.go($state.current, {}, {reload: true});
    									});
    							});
    						}
    					});
    				}
    			});
    		}
    }];

  window.angular.module('prismPluginsSample.controller.gcDenominationsCtrl', [])
    .controller('gcDenominationsCtrl', gcDenominations)
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


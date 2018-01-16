var changeShippingAdd = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "NotificationService", "$modalStack",
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, RN, LoadingScreen, NotificationService, $modalStack) {
		'use strict';

		var sess = $http.defaults.headers.common['Auth-Session'];
		var servername = $window.location.origin;
		var activeDocument = ModelService.fromCache('Document')[0];
		var deferred = $q.defer();

		var getItemShippingAddress = function(){
			ModelService.get('Item',{document_sid:$stateParams.document_sid,cols:'*'})
			.then(function(result){
				if(result.length > 0)
				{
				    $scope.viewItem = result;
		    	    ModelService.get('Address',{customer_sid: activeDocument.bt_cuid, cols:'*'})
		    	    .then(function(result){
		      	 		$scope.custAddresses = result;
		      	 		deferred.resolve();
		      	 	});
				}
				else
				{
					LoadingScreen.Enable = !1;
					NotificationService.addAlert('Item is required for this transaction!', 'Item Validation', 'static', false).then(function(){
					    $modalStack.dismissAll();
					    $state.go($state.current, {}, {reload: true});
					    deferred.resolve();
					});
				}
			});
	  	 	return deferred.promise;
		};

  	 	LoadingScreen.Enable = !0, getItemShippingAddress().then(function(){
  	 		LoadingScreen.Enable = !1;
  	 	});

		$scope.closeModalForm = function(){
			$modalInstance.dismiss();
			$state.go($state.current, {}, {reload: true});
		}


 		$scope.saveShippingAddress = function(){
 			var validate 		= [];
 			var itemToUpdate 	= [];
 			var chainRequest 	= $q.when();

 			angular.forEach($scope.viewItem, function(item){
 				if(item.st_address_uid == '' || item.st_address_uid == null)
 				{
 					validate.push('0');
 				}
 				else
 				{
 					validate.push('1');
 					itemToUpdate.push(item);
 				}
 			});

 			if(validate.indexOf('0') === -1)
 			{
	 			angular.forEach(itemToUpdate, function(item){
	 				chainRequest = chainRequest.then(function(){
						var itemLink = item.link;
						var row      = item.row_version;
						var selected_add_sid = item.st_address_uid;

						var dataUpdate = servername+itemLink+"?filter=row_version,eq,"+row;

			    	    return ModelService.get('Address',{customer_sid: activeDocument.bt_cuid, sid: selected_add_sid, cols:'*'})
			    	    .then(function(ca){
			    	    	var address1 = ca[0].address_line_1;
				    	    var address2 = ca[0].address_line_2;
				    	    var address3 = ca[0].address_line_3;
				    	    var address4 = ca[0].address_line_4;
				    	    var address5 = ca[0].address_line_5;
				    	    var address6 = ca[0].address_line_6;
				    	    var country = ca[0].country_name;
				    	    var postal_code = ca[0].postal_code;
				    	    var address_sid = ca[0].sid;

				    		var infoData = "[{\"st_address_line1\":\"" + address1 + "\",";
					    	    infoData += "\"st_address_line2\":\"" + address2 + "\",";
					    	    infoData += "\"st_address_line3\":\"" + address3 + "\",";
					    	    infoData += "\"st_address_line4\":\"" + address4 + "\",";
					    	    infoData += "\"st_address_line5\":\"" + address5 + "\",";
					    	    infoData += "\"st_address_line6\":\"" + address6 + "\",";
					    	    infoData += "\"st_country\":\""+ country +"\",";
					    	    infoData += "\"st_postal_code\":\"" + postal_code + "\",";
					    	    infoData += "\"st_address_uid\":\"" + address_sid + "\"";
					    	    infoData += "}]";

				    	    $http.put(dataUpdate,infoData,{headers:{"Auth-Session":sess}}).then(function(res){});
			      	 	});
	 				});

	 			});

	 			RN.showSuccessfulMessage('Updated', 'Shipping address has been updated');
	 			$modalInstance.dismiss();
	 			$state.go($state.current, {}, {reload: true});
 			}
 			else
 			{
 				RN.showError('Error', 'Please fill all item(s) addresses!');
 			}

 		}

}];

window.angular.module('prismPluginsSample.controller.changeShippingAddCtrl', [])
   .controller('changeShippingAddCtrl', changeShippingAdd);

/*========== Adding and Updating Slip Reason Controller ===========*/
var slipReason = ["$scope", "$http", "ModelService2", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "ModelEvent", "$timeout", "$rootScope",
	function($scope, $http, ModelService2, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, NotificationService, LoadingScreen, ModelEvent, $timeout, $rootScope) {
		'use strict';
		var sess 		= $http.defaults.headers.common['Auth-Session'];
		var servername 	= $window.location.origin;
		var deferred 	= $q.defer();
	    var activeSlip 	= ModelService2.fromCache('Transferslip')[0];
	    LoadingScreen.Enable = !0;

		if(activeSlip.slipcomment.length != 0)
		{
			ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
				$scope.slipReason 		= result[0].comments;
				$scope.cancelBtn 		= true;
				$scope.headerTitle 		= 'Update Slip Reason';
				LoadingScreen.Enable 	= !1;
			});
		}
		else
		{
			$scope.slipReason 		= '';
			$scope.cancelBtn 		= false;
			$scope.headerTitle 		= 'Slip Reason';
			LoadingScreen.Enable 	= !1;
		}

		// Method use to save instalment detail from the user
		$scope.saveReason = function(){

			if(activeSlip.slipcomment.length == 0)
			{
				var slipReasonData = "{\"data\":";
					slipReasonData += "[{\"commentno\":1,";
					slipReasonData += "\"comments\":\""+$scope.slipReason+"\",";
					slipReasonData += "\"originapplication\":\"RproPrismWeb\",";
					slipReasonData += "\"slipsid\":"+activeSlip.sid+"}]}";

				$http.post(servername + "/api/backoffice/transferslip/slipcomment",slipReasonData,{headers:{"Auth-Session":sess}})
				.then(function(res){
				  $modalInstance.dismiss();
				  $state.go($state.current, {}, {reload: true});
				});
			}
			else
			{
				ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
					var slipReasonData = "{\"data\":";
						slipReasonData += "[{\"comments\":\""+$scope.slipReason+"\",";
						slipReasonData += "\"rowversion\":"+result[0].rowversion+"}]}";
					$http.put(servername + "/api/backoffice/transferslip/slipcomment/"+activeSlip.slipcomment[0].sid+"?filter=rowversion,eq,"+result[0].rowversion,slipReasonData,{headers:{"Auth-Session":sess}})
					.then(function(){
						$modalInstance.dismiss();
						$state.go($state.current, {}, {reload: true});
						if($scope.slipReason.toUpperCase() == 'NORMAL')
						{
						    $http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
						        var x = [];
						        angular.forEach(res.data.data, function(value){
						            if(value.itemnote10 != '' && value.itemnote10 != null)
						            {
						                x.push('1');
						            }
						            else
						            {
						                x.push('0');
						            }
						        });
						        console.log('Normal');
						        console.log(x.indexOf('0'));
						        if(x.indexOf('0') !== -1)
						        {
						            var modalOptions = {
						                backdrop: 'static',
						                windowClass: 'full',
						                // size: 'lg', // sm, md, lg
						                templateUrl: '/plugins/PLTransferSlip/cartonNumbers.htm',
						                controller: 'cartonNumberCtrl',
						                keyboard: false
						            };

						            $modal.open(modalOptions);
						            $scope.slipComment = $scope.slipReason;
						        }
						    });

						}
						else if($scope.slipReason.toUpperCase() == 'DAMAGE')
						{
						    $http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
						        var x = [];
						        angular.forEach(res.data.data, function(value){
						            if(value.itemnote1 != '' && value.itemnote1 != null && value.itemnote2 != '' && value.itemnote2 != null && value.itemnote3 != '' && value.itemnote3 != null && value.itemnote10 != '' && value.itemnote10 != null)
						            {
						                x.push('1');
						            }
						            else
						            {
						                x.push('0');
						            }
						        });
						        console.log('Damage');
						        console.log(x.indexOf('0'));
						        if(x.indexOf('0') !== -1)
						        {
						            var modalOptions = {
						                backdrop: 'static',
						                windowClass: 'full',
						                // size: 'lg', // sm, md, lg
						                templateUrl: '/plugins/PLTransferSlip/cartonNumbers.htm',
						                controller: 'cartonNumberCtrl',
						                keyboard: false
						            };

						            $modal.open(modalOptions);
						            $scope.slipComment = $scope.slipReason;
						        }
						    });
						}
					});
				});

			}
		};

		$scope.cancelModalDialog = function(){
			$modalInstance.dismiss();
			$state.go($state.current, {}, {reload: true});
		}
}];

window.angular.module('prismPluginsSample.controller.slipReasonCtrl', [])
   .controller('slipReasonCtrl', slipReason);
/*========== End of Adding and Updating Slip Reason Controller ===========*/

/*========== Updating Item Carton Number Controller ===========*/
var cartonNumber = ["$scope", "$http", "ModelService2", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "ModelEvent", "$timeout",
	function($scope, $http, ModelService2, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, NotificationService, LoadingScreen, ModelEvent, $timeout) {
		'use strict';
		var sess 		= $http.defaults.headers.common['Auth-Session'];
		var servername 	= $window.location.origin;
		var deferred 	= $q.defer();
	    var activeSlip 	= ModelService2.fromCache('Transferslip')[0];

	    LoadingScreen.Enable = !0, ModelService2.get('SlipItem',{slipsid: activeSlip.sid}).then(function(result){
			$scope.slipItems 		= result;
			ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
				$scope.slipComment 	= result[0].comments;

				if($scope.slipComment.toUpperCase() == 'NORMAL')
				{
					$http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
						var x = [];
					    angular.forEach(res.data.data, function(value){
					    	if(value.itemnote10 != '' && value.itemnote10 != null)
					    	{
					    		x.push('1');
					    	}
					    	else
					    	{
					    		x.push('0');
					    	}
					    });
					    if(x.indexOf('0') === -1)
					    {
					    	$scope.btnCloseDisabled = false;
					    }
					    else
					    {
					    	$scope.btnCloseDisabled = true;
					    }
					});
				}
				else if($scope.slipComment.toUpperCase() == 'DAMAGE')
				{
					$http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
						var x = [];
					    angular.forEach(res.data.data, function(value){
					    	if(value.itemnote1 != '' && value.itemnote1 != null && value.itemnote2 != '' && value.itemnote2 != null && value.itemnote3 != '' && value.itemnote3 != null && value.itemnote10 != '' && value.itemnote10 != null)
					    	{
					    		x.push('1');
					    	}
					    	else
					    	{
					    		x.push('0');
					    	}
					    });
					    if(x.indexOf('0') === -1)
					    {
					    	$scope.btnCloseDisabled = false;
					    }
					    else
					    {
					    	$scope.btnCloseDisabled = true;
					    }
					});
				}
			});

			$http.get(servername + "/api/backoffice/invnudf?cols=*&filter=udfno,eq,23").then(function(res1){
				$http.get(servername + "/api/backoffice/invnudf/invnudfoption?cols=*&filter=udfsid,eq,"+res1.data.data[0].sid).then(function(result1){
					$scope.damageReason1 = result1.data.data;
				});
			});

			$http.get(servername + "/api/backoffice/invnudf?cols=*&filter=udfno,eq,24").then(function(res2){
				$http.get(servername + "/api/backoffice/invnudf/invnudfoption?cols=*&filter=udfsid,eq,"+res2.data.data[0].sid).then(function(result2){
					$scope.damageReason2 = result2.data.data;
				});
				LoadingScreen.Enable = !1;
				
			});
		});
		$scope.closeModalDialog = function(){
			$modalInstance.dismiss();
			$state.go($state.current, {}, {reload: true});
		}

		$scope.updateCartonNumber = function(){
			var chainRequest = $q.when();
			var validate = [];
  			ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
	  	    	var currentComment = result[0].comments;
	  	    	var cartonNumber, damage1, damage2, damageComment;
	  	    	if(currentComment.toUpperCase() == 'NORMAL')
	  	    	{
	  	    		angular.forEach($scope.slipItems, function(item){
	  	    			if(item.itemnote10 == '' || item.itemnote10 == null)
	  	    			{
	  	    				validate.push('0');
	  	    			}
	  	    			else
	  	    			{
	  	    				validate.push('1');
	  	    			}
	  	    		});

	  	    		if(validate.indexOf('0') === -1)
	  	    		{
	  	    			var promises 	= [];
	  	    			var dfr 		= $q.defer();
		  	    		angular.forEach($scope.slipItems, function(item) {
		  	    			chainRequest = chainRequest.then(function(){
			  	    			var cartonItemData = "{\"data\":";
				  	    			cartonItemData += "[{\"itemnote10\":\""+item.itemnote10+"\",";
				  	    			cartonItemData += "\"rowversion\":"+item.rowversion+"}]}";
			  	    			return $http.put(servername + "/api/backoffice/transferslip/slipitem/"+item.sid+"?filter=rowversion,eq,"+item.rowversion,cartonItemData,{headers:{"Auth-Session":sess}})
			  	    					.then(function(res){
			  	    						cartonNumber = item.itemnote10;
			  	    						dfr.resolve(res);
			  	    					});
		  	    			});
		  	    			promises.push(dfr.promise);
		  	    		});

		  	    		$q.all(promises).then(
		  	    		        // success
		  	    		        // results: an array of data objects from each deferred.resolve(data) call
		  	    		        function(results) {
		  	    		        	sessionStorage.setItem('damageDetails', JSON.stringify({cartonNumber: cartonNumber, damage1: '', damage2: '', damageComment: ''}));
		  	    		            NotificationService.showSuccessfulMessage('Success','Carton Number details has been updated successfully');
		  	    		            $modalInstance.dismiss();
		  	    		            $state.go($state.current, {}, {reload: true});
		  	    		        },
		  	    		        // error
		  	    		        function(response) {
		  	    		        }
		  	    		    );
	  	    		}
	  	    		else
	  	    		{
	  	    			NotificationService.showError('Error', 'Please fill all required fields !');
	  	    		}
	  	    	}
	  	    	else if(currentComment.toUpperCase() == 'DAMAGE')
	  	    	{
	  	    		angular.forEach($scope.slipItems, function(item){
	  	    			if(item.itemnote10 == '' || item.itemnote10 == null || item.itemnote1 == '' || item.itemnote1 == null || item.itemnote2 == '' || item.itemnote2 == null || item.itemnote3 == '' || item.itemnote3 == null)
	  	    			{
	  	    				validate.push('0');
	  	    			}
	  	    		});

	  	    		if(validate.indexOf('0') === -1)
	  	    		{
	  	    			var promises 	= [];
	  	    			var dfr 		= $q.defer();
		  	    		angular.forEach($scope.slipItems, function(item) {
		  	    			chainRequest = chainRequest.then(function(){
			  	    			var cartonItemData = "{\"data\":";
				  	    			cartonItemData += "[{\"itemnote10\":\""+item.itemnote10+"\",";
				  	    			cartonItemData += "\"itemnote1\":\""+item.itemnote1+"\",";
				  	    			cartonItemData += "\"itemnote2\":\""+item.itemnote2+"\",";
				  	    			cartonItemData += "\"itemnote3\":\""+item.itemnote3+"\",";
				  	    			cartonItemData += "\"rowversion\":"+item.rowversion+"}]}";

			  	    			return $http.put(servername + "/api/backoffice/transferslip/slipitem/"+item.sid+"?filter=rowversion,eq,"+item.rowversion,cartonItemData,{headers:{"Auth-Session":sess}})
			  	    					.then(function(res){
			  	    						cartonNumber 	= item.itemnote10;
			  	    						damage1 		= item.itemnote1;
			  	    						damage2 		= item.itemnote2;
			  	    						damageComment 	= item.itemnote3;
			  	    						dfr.resolve(res);
			  	    					});
		  	    			});
		  	    			promises.push(dfr.promise);
		  	    		});

		  	    		$q.all(promises).then(
		  	    		        // success
		  	    		        // results: an array of data objects from each deferred.resolve(data) call
		  	    		        function(results) {
		  	    		        	sessionStorage.setItem('damageDetails', JSON.stringify({cartonNumber: cartonNumber, damage1: damage1, damage2: damage2, damageComment: damageComment}));
		  	    		            NotificationService.showSuccessfulMessage('Success','Carton Number details has been updated successfully');
		  	    		            $modalInstance.dismiss();
		  	    		            $state.go($state.current, {}, {reload: true});
		  	    		        },
		  	    		        // error
		  	    		        function(response) {
		  	    		        }
		  	    		    );
	  	    		}
	  	    		else
	  	    		{
	  	    			NotificationService.showError('Error', 'Please fill all required fields !');
	  	    		}
	  	    	}

	  	    });

		}
}];

window.angular.module('prismPluginsSample.controller.cartonNumberCtrl', [])
  .controller('cartonNumberCtrl', cartonNumber);
/*========== End of Updating Item Carton Number Controller ===========*/

/*========== Adding Item Carton Number Controller ===========*/
var itemCartonNumber = ["$scope", "$http", "ModelService2", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "ModelEvent", "$timeout", "$modalStack",
  	function($scope, $http, ModelService2, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, NotificationService, LoadingScreen, ModelEvent, $timeout, $modalStack) {
  		'use strict';
  		var sess 		= $http.defaults.headers.common['Auth-Session'];
  		var servername 	= $window.location.origin;
  		var deferred 	= $q.defer();
  	    var activeSlip 	= ModelService2.fromCache('Transferslip')[0];
  	    var activeItems = ModelService2.fromCache('SlipItem');
  	    ModelService2.get('Transferslip', {sid: activeSlip.sid}).then(function(slip){
  	    	var activeItem 	= slip[0].slipitem[slip[0].slipitem.length - 1];
  	    	ModelService2.get('SlipItem', {sid: activeItem.sid}).then(function(slipitem){
  	    		$scope.activeItemSid = activeItem.sid;
  	    		$scope.itemAlu 		= slipitem[0].alu;
  	    		if(slipitem[0].itemnote10 != '' && slipitem[0].itemnote10 != null)
  	    		{
  	    			$scope.crtnNumber = slipitem[0].itemnote10;
  	    			$scope.dmgReason1 = slipitem[0].itemnote1;
  	    			$scope.dmgReason2 = slipitem[0].itemnote2;
  	    			$scope.dmgComment = slipitem[0].itemnote3;
  	    		}
  	    		else if(sessionStorage.getItem('damageDetails') !== null)
  	    		{
  	    			var dmgDetails = JSON.parse(sessionStorage.getItem('damageDetails'));
  	    			$scope.crtnNumber = dmgDetails.cartonNumber;
  	    			$scope.dmgReason1 = dmgDetails.damage1;
  	    			$scope.dmgReason2 = dmgDetails.damage2;
  	    			$scope.dmgComment = dmgDetails.damageComment;
  	    		}
  	    		else
  	    		{
  	    			$scope.crtnNumber = '';
  	    			$scope.dmgReason1 = '';
  	    			$scope.dmgReason2 = '';
  	    			$scope.dmgComment = '';
  	    		}
  	    	});

  	    });

  	    if(activeSlip.slipcomment.length != 0)
  	    {
  	    	LoadingScreen.Enable = !0,ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
  	    		$scope.slipComment 	= result[0].comments;
  	    		
  	    		var promise1 = $http.get(servername + "/api/backoffice/invnudf?cols=*&filter=udfno,eq,23").then(function(res1){
  	    			$http.get(servername + "/api/backoffice/invnudf/invnudfoption?cols=*&filter=udfsid,eq,"+res1.data.data[0].sid).then(function(result1){
  	    				$scope.damageReason1 	= result1.data.data;
  	    			});
  	    		});

  	    		var promise2 = $http.get(servername + "/api/backoffice/invnudf?cols=*&filter=udfno,eq,24").then(function(res2){
  	    			$http.get(servername + "/api/backoffice/invnudf/invnudfoption?cols=*&filter=udfsid,eq,"+res2.data.data[0].sid).then(function(result2){
  	    				$scope.damageReason2 = result2.data.data;
  	    			});
  	    		});

  	    		$q.all([promise1, promise2]).then(function(){
  	    			LoadingScreen.Enable 	= !1;
  	    		});
  	    	});
  	    }

  		$scope.cancelModalDialog = function(){
  			$modalInstance.dismiss();
  			$state.go($state.current, {}, {reload: true});
  		}

  		$scope.saveItemCartonNumber = function(activeItemSid){

  			var cartonItemData = "{\"data\":";
			var activeItemSid = $scope.activeItemSid;
			ModelService2.get('SlipItem', {sid: activeItemSid}).then(function(slipitem){
	  			ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
		  	    	var currentComment = result[0].comments;
		  	    	if(currentComment.toUpperCase() == 'NORMAL')
		  	    	{
		  	    		if($scope.crtnNumber == '' || $scope.crtnNumber == null)
		  	    		{
		  	    			NotificationService.showError('Error', 'Carton number is required!');
		  	    		}
		  	    		else
		  	    		{
		  	    			cartonItemData += "[{\"itemnote10\":\""+$scope.crtnNumber+"\",";
		  	    			cartonItemData += "\"rowversion\":"+slipitem[0].rowversion+"}]}";
		  	    			$http.put(servername + "/api/backoffice/transferslip/slipitem/" + activeItemSid + "?filter=rowversion,eq,"+slipitem[0].rowversion,cartonItemData,{headers:{"Auth-Session":sess}})
		  	    			.then(function(res){
		  	    				sessionStorage.setItem('damageDetails', JSON.stringify({cartonNumber: $scope.crtnNumber, damage1: '', damage2: '', damageComment: ''}));
		  	    				NotificationService.showSuccessfulMessage('Success','Carton Number details has been added successfully');
		  	    				$modalInstance.dismiss();
		  	    				$state.go($state.current, {}, {reload: true});
		  	    			});
		  	    		}
		  	    	}
		  	    	else if(currentComment.toUpperCase() == 'DAMAGE')
		  	    	{
		  	    		if($scope.crtnNumber == '' || $scope.crtnNumber == null || $scope.dmgReason1 == '' || $scope.dmgReason1 == null || $scope.dmgReason2 == '' || $scope.dmgReason2 == null || $scope.dmgComment == '' || $scope.dmgComment == null)
		  	    		{
		  	    			NotificationService.showError('Error', 'Please fill all fields first!');
		  	    		}
		  	    		else
		  	    		{
		  	    			cartonItemData += "[{\"itemnote10\":\""+$scope.crtnNumber+"\",";
		  	    			cartonItemData += "\"itemnote1\":\""+$scope.dmgReason1+"\",";
		  	    			cartonItemData += "\"itemnote2\":\""+$scope.dmgReason2+"\",";
		  	    			cartonItemData += "\"itemnote3\":\""+$scope.dmgComment+"\",";
		  	    			cartonItemData += "\"rowversion\":"+slipitem[0].rowversion+"}]}";

		  	    			$http.put(servername + "/api/backoffice/transferslip/slipitem/"+activeItemSid+"?filter=rowversion,eq,"+slipitem[0].rowversion,cartonItemData,{headers:{"Auth-Session":sess}})
		  	    			.then(function(res){
		  	    				sessionStorage.setItem('damageDetails', JSON.stringify({cartonNumber: $scope.crtnNumber, damage1: $scope.dmgReason1, damage2: $scope.dmgReason2, damageComment: $scope.dmgComment}));
		  	    				NotificationService.showSuccessfulMessage('Success','Carton Number details has been added successfully');
		  	    				$modalInstance.dismiss();
		  	    				$state.go($state.current, {}, {reload: true});
		  	    			});
		  	    		}
		  	    	}

		  	    });
			});
  		};

  		$scope.updateCartonNumber = function(cartonNumber){
  			$scope.crtnNumber = cartonNumber;
  		}
  }];

  window.angular.module('prismPluginsSample.controller.itemCartonNumberCtrl', [])
    .controller('itemCartonNumberCtrl', itemCartonNumber);
/*========== End of Adding Item Carton Number Controller ===========*/





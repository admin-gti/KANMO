var instalment = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", 
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, NotificationService, LoadingScreen) {
		'use strict';
		var sess 			= $http.defaults.headers.common['Auth-Session'];
		var servername 		= $window.location.origin;

		var existingItems 	= [];
		var deferred 		= $q.defer();
		var activeDocument 	= ModelService.fromCache('Document')[0];

		$scope.bankIssuer 	= activeDocument.comment1; 
		$scope.instalmentDuration 	= activeDocument.comment2; 

		// Method use to close modal dialog
		$scope.closeModalDialog = function(){
		   $modalInstance.dismiss();
		   $state.go($state.current, {}, {reload: true});
		};

		// Method use to save instalment detail from the user
		$scope.saveInstalmentDetails = function(){
			if($scope.bankIssuer == '' || $scope.instalmentDuration == '')
			{
				NotificationService.showError('Error','Please fill all fields !');
			}
			else
			{
				var link = servername+activeDocument.link+"?filter=row_version,eq,"+activeDocument.row_version;
	    		var infoData = "[{\"comment1\":\"" + $scope.bankIssuer + "\",";
		    	    infoData += "\"comment2\":\"" + $scope.instalmentDuration + "\"}]";

	    	    $http.put(link,infoData,{headers:{"Auth-Session":sess}})
	    	    .then(function(){
	    	        NotificationService.showSuccessfulMessage('Success', 'Instalment details has been successfully added');
	    	        $modalInstance.dismiss();
	    	        $state.go($state.current, {}, {reload: true});
	    	    });
			}
		};
}];

window.angular.module('prismPluginsSample.controller.instalmentCtrl', [])
   .controller('instalmentCtrl', instalment);

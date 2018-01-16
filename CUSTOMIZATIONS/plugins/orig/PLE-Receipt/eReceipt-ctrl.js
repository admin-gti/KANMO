var eReceipt = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", 
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, NotificationService, LoadingScreen) {
		'use strict';
		var sess = $http.defaults.headers.common['Auth-Session'];
		var servername = $window.location.origin;

		$scope.detail = {};
		var deferred = $q.defer();

		// Method use to prevent user in editing textbox
		$scope.notAllowInput = function(e){
			e.preventDefault();
			e.stopPropagation();
		};

		// Method use to get current customer info
		var getCustomerInfo = function(){
			var activeCustomer = ModelService.fromCache('Customer')[0];
			$scope.detail.customerEmail = activeCustomer.email_address;
			$scope.detail.mobilePhone = activeCustomer.primary_phone_no;
			deferred.resolve();
			return deferred.promise;
		}

		$scope.notEditableChange = function(value){
			if(value != '')
			{
				$scope.detail.dateOfBirth = value;
			}
			else
			{
				$scope.detail.dateOfBirth = $filter('date')(new Date(),'MM/dd/yyyy');
			}
		};
		
		$scope.detail.dateOfBirth = $filter('date')(new Date(),'MM/dd/yyyy');

		$scope.validateMobile = function(value){
			$scope.detail.mobilePhone = value.replace(/[^0-9]/g, '');
			if(value.length < 2)
			{
				$scope.detail.mobilePhone = '09';
			}
		}

		// Method use to close modal dialog
		$scope.closeModalDialog = function(){
		   $modalInstance.dismiss();
		   $state.go($state.current, {}, {reload: true});
		};

		// Method use to save instalment detail from the user
		$scope.saveDetails = function(){

		};

		LoadingScreen.Enable = !0, getCustomerInfo().then(function(){
			LoadingScreen.Enable = !1;
		});
}];

window.angular.module('prismPluginsSample.controller.eReceiptCtrl', [])
   .controller('eReceiptCtrl', eReceipt);

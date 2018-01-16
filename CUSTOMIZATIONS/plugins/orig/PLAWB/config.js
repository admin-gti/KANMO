SideButtonsManager.addButton({
    label: 'Shipment Details',
    sections: ['transactionNew', 'transactionEdit'],
    handler: ['$modal', '$q', '$stateParams', 'ResourceNotificationService', '$window', 'NotificationService', 'LoadingScreen', '$state', 'DocumentPersistedData', '$http', 'ModelService', function($modal, $q, $stateParams, RN, $window, NotificationService, LoadingScreen, $state, DocumentPersistedData, $http, ModelService) {

        var modalOptions = {
            backdrop: 'static',
            keyboard: false,
            windowClass: 'width: 3000px;',
            templateUrl: '/plugins/PLAWB/awb.htm',
            controller: 'addAWBCtrl'
        };
        var deferred = $q.defer();
        
        $http.get('v1/rest/document/'+$stateParams.document_sid+'?cols=status', {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(resp){
//        console.log(resp.data[0].status);
            if(resp.data[0].status != 4){
                $modal.open(modalOptions);
            } else {
                RN.showError( 'WARNING', 'This is final transaction. Cannot be modified.');
                deferred.reject();
            }
        });
        
        return deferred.promise;
    }]
});

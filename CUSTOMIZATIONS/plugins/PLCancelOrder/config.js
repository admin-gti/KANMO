ButtonHooksManager.addHandler(['before_posOrderDetailsDeactivateOrder'],['$modal', '$q', '$stateParams', 'ResourceNotificationService', function($modal, $q, $stateParams, NotificationService) {
        
        var deferred = $q.defer();
        
        var modalOptions;
        modalOptions = {
            backdrop: 'static',
            windowClass: 'small',
            templateUrl: '/plugins/PLCancelOrder/cancelOrder.htm',
            controller: 'addCancelOrderCtrl'
        };

        $modal.open(modalOptions);
        
        deferred.resolve();

        return deferred.promise;
        
    }]
);


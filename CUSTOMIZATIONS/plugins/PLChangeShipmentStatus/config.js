ButtonHooksManager.addHandler(['before_navCustomUpdateOMSSubStatus'],['$modal', '$q', '$stateParams', 'ResourceNotificationService', 'ModelService', function($modal, $q, $stateParams, NotificationService, ModelService) {
        
        var deferred = $q.defer();
        
        //console.log('double');
        
        var modalOptions;
        modalOptions = {
            backdrop: 'static',
//            windowClass: 'full',
            size: 'lm',
            templateUrl: '/plugins/PLChangeShipmentStatus/changeShipmentStatus.htm',
            controller: 'changeShipmentStatusCtrl'
        };
        $modal.open(modalOptions);
        
        deferred.resolve();
        return deferred.promise;
        
    }]
);


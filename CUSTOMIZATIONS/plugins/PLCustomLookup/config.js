ButtonHooksManager.addHandler(['before_navCustomATransactionLookup', 'before_navCustomAATransactionLookup'],['$modal', '$q', '$stateParams', 'ResourceNotificationService', 'ModelService', function($modal, $q, $stateParams, NotificationService, ModelService) {
        
        var deferred = $q.defer();
        
        //console.log('double');
        
        var modalOptions;
        modalOptions = {
            backdrop: 'static',
            windowClass: 'full',
//            size: 'full',
            templateUrl: '/plugins/PLCustomLookup/customLookup.htm',
            controller: 'customLookupCtrl'
        };
        $modal.open(modalOptions);
        
        deferred.resolve();
        return deferred.promise;
        
    }]
);


ButtonHooksManager.addHandler(['after_posInstalmentButton'],['$modal','ModelService', '$q', function($modal, a, $q) {
		var deferred = $q.defer();
        var modalOptions = {
            backdrop: 'static',
            // windowClass: 'sm',
            size: 'md', // sm, md, lg
            templateUrl: '/plugins/PLInstalment/index.htm',
            controller: 'instalmentCtrl'
        };

        $modal.open(modalOptions);

        return deferred.promise;
    }]
);

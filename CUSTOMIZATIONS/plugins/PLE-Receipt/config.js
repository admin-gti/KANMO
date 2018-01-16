ButtonHooksManager.addHandler(['after_posEReceiptButton'],['$modal','ModelService', '$q', function($modal, a, $q) {
		var deferred = $q.defer();
        var modalOptions = {
            backdrop: 'static',
            // windowClass: 'sm',
            size: 'md', // sm, md, lg
            templateUrl: '/plugins/PLE-Receipt/index.htm',
            controller: 'eReceiptCtrl'
        };

        $modal.open(modalOptions);

        return deferred.promise;
    }]
);

ButtonHooksManager.addHandler(['after_navPOSUpdateMultipleOrderType'],['$modal', '$q', '$stateParams', 'ResourceNotificationService', 'ModelService', function($modal, $q, $stateParams, NotificationService, ModelService) {
        
        var deferred = $q.defer();
        
        var modalOptions;
        modalOptions = {
            backdrop: 'static',
            windowClass: 'full',
            size: 'lg',
            templateUrl: '/plugins/PLFulfillmentAndFreight/fulfillmentandfreight.htm',
            controller: 'fulfillmentandFreightCtrl'
        };
        

        /*---SHIPMENT DETAILS => DISPATCHED--------------------------------*/
        $.ajax({
            url: "/plugins/PLOverallValidation/validation.php"
            ,type: "GET"
            ,data: {type:4,sid:$stateParams.document_sid}
            ,success: function(val1){
                    console.log(val1);
//                if(val1.homedelivery > 0 || val1.diffstore > 0){
//                    $modal.open(modalOptions);
//                    deferred.resolve();
//                } else {
//                    deferred.resolve();
//                }
                
                if(val1.homedelivery > 0 || val1.diffstore > 0){
                    $modal.open(modalOptions);
                    deferred.resolve();
                } else {

                    deferred.resolve();
                }
            }
        });
        
        deferred.resolve();

        return deferred.promise;
        
    }]
);


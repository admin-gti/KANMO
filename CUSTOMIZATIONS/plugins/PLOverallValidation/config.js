ButtonHooksManager.addHandler(['before_posTransactionTenderTransaction'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ResourceNotificationService, $rootScope, HookEvent, $stateParams, base64, $http, ModelService,LoadingScreen) {
        var deferred = $q.defer();
        
        var pickupdiff = localStorage.getItem('isfullfilllocation');
        var freightAmt = localStorage.getItem('freightamt');
        var homedelivery = localStorage.getItem('deliverydiffstore');
        console.log(pickupdiff+' '+freightAmt+' '+homedelivery);
        if(pickupdiff>0){
            ResourceNotificationService.showWarning( 'Warning', 'No fulfillment location selected for pickup different store item/s.');
            deferred.reject();
        }
        if(freightAmt>0){
            ResourceNotificationService.showWarning( 'Warning', 'Shipping Rate is required for home delivery item/s.');
            deferred.reject();
        }
        if(homedelivery>0){
            ResourceNotificationService.showWarning( 'Warning', 'No store to fulfill for home delivery item/s.');
            deferred.reject();
        }
            
            
        ModelService.get('Document',{sid:$stateParams.document_sid}).then(function(data){

            /*---SHIPMENT DETAILS => DISPATCHED--------------------------------*/
            LoadingScreen.Enable = 1,$.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:1,sid:$stateParams.document_sid}
                ,success: function(val1){
//                    console.log(val1);
                    if(val1.dispatched != ''){
                        ResourceNotificationService.showWarning( 'WARNING', val1.dispatched);
                        LoadingScreen.Enable = !1;
                        deferred.reject();
                    } else {
                        $.ajax({
                            url: "/plugins/PLOverallValidation/validation.php"
                            ,type: "GET"
                            ,data: {type:5,sid:$stateParams.document_sid}
                            ,success: function(val2){
//                                console.log(val2);
                                if(val2.ofull != ''){
                                    ResourceNotificationService.showWarning( 'WARNING', val2.ofull);
                                    LoadingScreen.Enable = !1;
                                    deferred.reject();
                                } else {
                                    LoadingScreen.Enable = !1;
                                    deferred.resolve();
                                }
                            }
                        });
                    }
                }
            });
            
            $.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:5,sid:$stateParams.document_sid}
                ,success: function(val1){
//                    console.log(val1);
                    if(val1.ofull != ''){
                        ResourceNotificationService.showWarning( 'WARNING', val1.ofull);
                        LoadingScreen.Enable = !1;
                        deferred.reject();
                    } else {
                        LoadingScreen.Enable = !1;
                        deferred.resolve();
                    }
                }
            });
            
            

        });
//------------------------------------------------------------------------------
        
        return deferred.promise;
    }
);

ButtonHooksManager.addHandler(['before_posOrderDetailsFulfillOrder'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ResourceNotificationService, $rootScope, HookEvent, $stateParams, base64, $http, ModelService,LoadingScreen) {
        var deferred = $q.defer();
        
        ModelService.get('Document',{sid:$stateParams.document_sid}).then(function(data){

            /*---SHIPMENT DETAILS => SOURCE SO VALIDATION--------------------------------*/
            LoadingScreen.Enable = 1,$.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:2,sid:$stateParams.document_sid}
                ,success: function(val1){
//                    console.log(val1);
                    if(val1.source != ''){
                        ResourceNotificationService.showWarning( 'WARNING', val1.source);
                        LoadingScreen.Enable = !1;
                        deferred.reject();
                        
                    } else {
                        LoadingScreen.Enable = !1;
                        deferred.resolve();
                    }
                }
            });
            

        });
//------------------------------------------------------------------------------
        return deferred.promise;
    }
);

ButtonHooksManager.addHandler(['before_posOrderDepositOk'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ResourceNotificationService, $rootScope, HookEvent, $stateParams, base64, $http, ModelService,LoadingScreen) {
        var deferred = $q.defer();
        
        var totaldue = $("#ordersTotalDue").val();
        var depstAmt = $("#depositAmount").val();
        
//        console.log(totaldue);
//        console.log(depstAmt);
        
        if(totaldue != depstAmt) {
             ResourceNotificationService.showWarning( 'WARNING', 'ORDERS TOTAL DUE must be equal to DEPOSIT AMOUNT');
            deferred.reject();
        } else {
            deferred.resolve();
        }
//------------------------------------------------------------------------------
        return deferred.promise;
    }
);

//ButtonHooksManager.addHandler(['before_navPosTenderUpdateOnly'],
//    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ResourceNotificationService, $rootScope, HookEvent, $stateParams, base64, $http, ModelService,LoadingScreen) {
//        var deferred = $q.defer();
//        
//        ModelService.get('Document',{sid:$stateParams.document_sid}).then(function(data){
//            console.log(data);
//        });
//        
//        deferred.reject();
////------------------------------------------------------------------------------
//        return deferred.promise;
//    }
//);


ButtonHooksManager.addHandler(['before_posTransactionTenderTransaction'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService, $rootScope, HookEvent, $stateParams, base64, $http) {
        var deferred = $q.defer();
        
        $http.get('v1/rest/document/'+$stateParams.document_sid+'?cols=*',{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
            
            $.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:6,sid:$stateParams.document_sid}
                ,success: function(val1){
                    
                    if(val1.payatstore > 0) {
                    
                        var doc = res.data[0];
                        var infoData = "[{\"due_amt\":\""+doc.transaction_total_amt+"\",";
                            infoData += "\"order_changed_flag\":\"0\"";
                            infoData += "}]";

                        $http.put(doc.link+"?filter=row_version,eq,"+doc.row_version,infoData,{headers:{"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){

                            deferred.resolve();
                            return deferred.promise;

                        });
                    } else {
                        deferred.resolve();
                        return deferred.promise;
                    }
                }
            });
            
            
        });
        return deferred.promise;
//------------------------------------------------------------------------------
        
        
    }
);
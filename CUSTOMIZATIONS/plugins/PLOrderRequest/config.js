ButtonHooksManager.addHandler(['after_navPosTenderUpdateOnly', 'after_navPosTenderPrintUpdate'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService, $rootScope, HookEvent, $stateParams, base64, $http, prismSessionInfo) {
        
        var deferred = $q.defer();    
        var workstation = prismSessionInfo.get().workstationid;
    
        $.ajax({
            url: "https://"+PRISM_CUSTOM.URL+"/plugins/PLOrderRequest/validate.php"
            ,type: "GET"
            ,crossDomain: true
            ,data: {docsid:$stateParams.document_sid}
            ,success: function(msg){
    //            console.log(msg);
                if(msg.val == 1){

                    $http.get('v1/rest/document/'+$stateParams.document_sid+'?cols=link,row_version',{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
    //                    console.log(res);

                        var tag = "[{";
                            tag += "\"notes_order\":\"SOURCE\"";
                            tag += "}]";

                        $http.put(res.data[0].link+"?filter=row_version,eq,"+res.data[0].row_version,tag,{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).success(function(){

                            //------------------------SPLIT SO ACCORDINGLY----------------------------------    
                            $.ajax({
                                url: "https://"+PRISM_CUSTOM.URL+"/plugins/PLOrderRequest/orderRequest.php"
                                ,type: "GET"
                                ,crossDomain: true
                                ,data: {docsid:$stateParams.document_sid,auth:sessionStorage.getItem("PRISMAUTH"),ism:1,ws:workstation}
                                ,success: function(msg){
//                                    return false;
                                   deferred.resolve();  
                                   return deferred.promise;
                                }
                            }); 
                            //---------------------------------END------------------------------------------

                        });
                    });



                } else {
                    //------------------------SPLIT SO ACCORDINGLY----------------------------------    
                    $.ajax({
                        url: "https://"+PRISM_CUSTOM.URL+"/plugins/PLOrderRequest/orderRequest.php"
                        ,type: "GET"
                        ,crossDomain: true
                        ,data: {docsid:$stateParams.document_sid,auth:sessionStorage.getItem("PRISMAUTH"),ism:0,ws:workstation}
                        ,success: function(msg){
//                            return false;
                           deferred.resolve();  
                           return deferred.promise;
                        }
                    }); 
                //---------------------------------END------------------------------------------
                }
            }
        });
//    return false;
        return deferred.promise;
    }
);


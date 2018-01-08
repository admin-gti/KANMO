ButtonHooksManager.addHandler(['before_posTransactionOrderDetails'],
//ButtonHooksManager.addHandler(['before_posOrderFulfillmentOk', 'before_posTransactionOrderDetails'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService, $rootScope, HookEvent, $stateParams, base64, $http, ResourceNotificationService, LoadingScreen) {
        var deferred = $q.defer();
//        console.log($stateParams.document_sid);
        
        var vURL               = MARTJACK.URL;
        var urlGetHistory      = MARTJACK.GETHISTORY;
        var urlGetInfo         = MARTJACK.GETINFO;
        var urlCreateShip      = MARTJACK.CREATESHIPMENT;
        var urlUpdateStat      = MARTJACK.UPDATESHIPSTAT;
        var merchantID         = MARTJACK.MERCHANTID;
        var consumer_key       = MARTJACK.CONSUMERKEY;
        var consumer_secretkey = MARTJACK.CONSUMER_SECRETKEY;
        
        var auth_nonce         = Math.random().toString(36).replace(/[^a-z]/, '').substr(2);
        var auth_timestamp     = Math.round((new Date()).getTime() / 1000.0);

//        $.ajax({
//            url: "/plugins/PLWaitingforPickup/totalDep.php"
//            ,type: "GET"
//            ,data: {sid:$stateParams.document_sid}
//            ,success: function(){
////                a.inventory = rest;
//                deferred.resolve();
//
//            }
//        });
        
        
        
//        $http.get('v1/rest/document/'+$stateParams.document_sid+'?cols=ref_order_sid', {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(aaa){
////            console.log(a);
//            $http.get('v1/rest/document/'+$stateParams.document_sid+'?cols=*', {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(a){
//    //            console.log(a.data[0].notes_order);
//
//                if(a.data[0].order_type == 0){
//
//                    if(a.data[0].udf_string2 != ''){ //TAGGING SO AS WAITING FOR PICKUP
//
//                        /*---WAITING FOR PICKUP @SAMESTORE-SHIP*/
//                        var httpMethod = 'POST',
//                            url = vURL+urlGetHistory+merchantID,
//                            parameters = {
//                            oauth_consumer_key : consumer_key,
//                            oauth_nonce : auth_nonce,
//                            oauth_timestamp : auth_timestamp,
//                            oauth_signature_method : 'HMAC-SHA1',
//                            oauth_version : '1.0'
//                        },
//                        consumerSecret = consumer_secretkey,
//                        oauthSignatureGetHistory = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
//
//                        LoadingScreen.Enable = 1,$.ajax({
//                            url: "/plugins/PLChangeStat/changeStatus.php"
//                            ,type: "GET"
//                            ,data: {an:auth_nonce,at:auth_timestamp,s:oauthSignatureGetHistory,type:1,sid:a.data[0].notes_order}
//                            ,success: function(scs1){
//    //                            
//    //                            console.log(scs1.Message);
//    //                            console.log(scs1.Orders[0].OrderId);
//    //                            return false;
//                                if(scs1.Message == 'Successful'){
//                                    LoadingScreen.Enable = !1;
//                                    var httpMethod = 'GET',
//                                        url = vURL+urlGetInfo+merchantID+'/'+scs1.Orders[0].OrderId,
//                                        parameters = {
//                                        oauth_consumer_key : consumer_key,
//                                        oauth_nonce : auth_nonce,
//                                        oauth_timestamp : auth_timestamp,
//                                        oauth_signature_method : 'HMAC-SHA1',
//                                        oauth_version : '1.0'
//                                    },
//                                    consumerSecret = consumer_secretkey,
//                                    oauthSignatureGetInfo = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
//
//                                    $.ajax({
//                                        url: "/plugins/PLChangeStat/changeStatus.php"
//                                        ,type: "GET"
//                                        ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:oauthSignatureGetInfo,type:2,oid:scs1.Orders[0].OrderId}
//                                        ,success: function(scs2){
//    //                                        console.log(scs2);
//    //                                        return false;
//
//                                            var httpMethod = 'POST',
//                                                url = vURL+urlCreateShip,
//                                                parameters = {
//                                                oauth_consumer_key : consumer_key,
//                                                oauth_nonce : auth_nonce,
//                                                oauth_timestamp : auth_timestamp,
//                                                oauth_signature_method : 'HMAC-SHA1',
//                                                oauth_version : '1.0'
//                                            },
//                                            consumerSecret = consumer_secretkey,
//                                            oauthSignatureCreateShip = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
//
//    //                                        $.ajax({
//    //                                            url: "/plugins/PLChangeStat/changeStatus.php"
//    //                                            ,type: "GET"
//    //                                            ,data: {sid:$stateParams.document_sid,type:4}
//    //                                            ,success: function(ads){
//
//    //                                            if (ads>0) {
//                                                        $.ajax({
//                                                            url: "/plugins/PLChangeStat/changeStatus.php"
//                                                            ,type: "GET"
//                                                            ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:oauthSignatureCreateShip,type:3,oid:scs1.Orders[0].OrderId}
//                                                            ,success: function(msg){
//
//    //                                                            return false;
//
//                                    //                            if(msg.messageCode == '1027'){
//                                    //
//                                    //                                NotificationService.showWarning( 'WARNING', "This record already have shipment details.");
//                                    //
//                                    //                            } else {
//
//                                                                    var httpMethod = 'GET',
//                                                                        url = vURL+'Order/'+merchantID+'/'+msg.OrderID+'/Shipments',
//                                                                        parameters = {
//                                                                        oauth_consumer_key : consumer_key,
//                                                                        oauth_nonce : auth_nonce,
//                                                                        oauth_timestamp : auth_timestamp,
//                                                                        oauth_signature_method : 'HMAC-SHA1',
//                                                                        oauth_version : '1.0'
//                                                                    },
//                                                                    consumerSecret = consumer_secretkey,
//                                                                    encodedSignaturegetShipment = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
//
//                                                                     $.ajax({
//                                                                        url: "/plugins/PLChangeStat/changeStatus.php"
//                                                                        ,type: "GET"
//                                                                        ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:encodedSignaturegetShipment,type:4,id:msg.OrderID}
//                                                                        ,success: function(msgs){
//
//                                                                            var httpMethod = 'POST',
//                                                                                url = vURL+urlUpdateStat+merchantID,
//                                                                                parameters = {
//                                                                                oauth_consumer_key : consumer_key,
//                                                                                oauth_nonce : auth_nonce,
//                                                                                oauth_timestamp : auth_timestamp,
//                                                                                oauth_signature_method : 'HMAC-SHA1',
//                                                                                oauth_version : '1.0'
//                                                                            },
//                                                                            consumerSecret = consumer_secretkey,
//                                                                            encodedSignatureUpdateShipStat = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
//
//                                                                            $.ajax({
//                                                                                url: "/plugins/PLChangeStat/changeStatus.php"
//                                                                                ,type: "GET"
//                                                                                ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:encodedSignatureUpdateShipStat,type:5,data:msgs.Shipments[0].ShipmentId}
//                                                                                ,success: function(msgss){        
//
//                                                                                    var infoData = "[{\"udf_string2\":\"WAITING FOR PICKUP\"";
//                                                                                        infoData += "}]";
//
//                                                                                    var row_version = parseInt(a.data[0].row_version);
//
//                                                                                    $http.put(a.data[0].link+"?filter=row_version,eq,"+a.data[0].row_version,infoData,{headers:{"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).success(function(){
//    //
//                                                                                        deferred.resolve();
//    //
//                                                                                    });
//                                                                                }
//                                                                            });
//
//                                                                        }
//                                                                    });
//
//                                    //                            }
//
//                                                            }
//                                                        });
//    //                                                } else {
//
//    //                                                    deferred.resolve();
//
//    //                                                }
//                                                }
//    //                                        });
//
//    //                            if(msg.messageCode == '1027'){
//
//    //                                    }
//                                    });
//
//                                } else {
//
//                                    ResourceNotificationService.showError( 'ERROR!', ' SO cant be fulfilled.\nThis is SOURCE SO.');
//    //                                deferred.resolve();
//                                    LoadingScreen.Enable = !1;
//
//                                }
//
//                            }
//                        });
//
//                    } else {
//                        deferred.resolve();
//                    }
//
//                } else {
//
//                    deferred.resolve();
//
//                }
//            });
//        });
        deferred.resolve();
//        return false;

////------------------------------------------------------------------------------
////        return false;
        return deferred.promise;
    }
);


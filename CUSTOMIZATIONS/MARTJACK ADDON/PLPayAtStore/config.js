ButtonHooksManager.addHandler(['before_navPosTenderUpdateOnly', 'before_navPosTenderPrintUpdate'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService, $rootScope, HookEvent, $stateParams, base64, $http) {
        var deferred = $q.defer();
//        console.log('test');
        var vURL               = MARTJACK.URL;
        var urlSaveMerchant    = MARTJACK.SAVEMERCHANT;
        var urlAuthorizeOrder  = MARTJACK.AUTHORIZEORDER;
        var urlUpdateStat      = MARTJACK.UPDATESHIPSTAT;
        var merchantID         = MARTJACK.MERCHANTID;
        var consumer_key       = MARTJACK.CONSUMERKEY;
        var consumer_secretkey = MARTJACK.CONSUMER_SECRETKEY;
        
        var auth_nonce         = Math.random().toString(36).replace(/[^a-z]/, '').substr(2);
        var auth_timestamp     = Math.round((new Date()).getTime() / 1000.0);

        $.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:6,sid:$stateParams.document_sid}
                ,success: function(val1){
                    
                    if(val1.payatstore > 0) {
                        console.log('test');
                        var httpMethod = 'POST',
                            url = vURL+urlSaveMerchant+merchantID,
                            parameters = {
                            oauth_consumer_key : consumer_key,
                            oauth_nonce : auth_nonce,
                            oauth_timestamp : auth_timestamp,
                            oauth_signature_method : 'HMAC-SHA1',
                            oauth_version : '1.0'
                        },
                        consumerSecret = consumer_secretkey,
                        oauthSignatureSaveM = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
                        //*------------------------END----------------------------------------*/        
                        $http.get('v1/rest/document/'+$stateParams.document_sid+'?cols=*',{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){

                            console.log(res.data[0]);

                            if(res.data[0].notes_general != ''){

                                var doc = res.data[0].notes_general;

                                $.ajax({
                                    url: "/plugins/PLPayAtStore/payatstore.php"
                                    ,type: "GET"
                                    ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:oauthSignatureSaveM,type:1}
                                    ,success: function(msg){

                //                        console.log(msg.messageCode);
                                        if(msg.messageCode == '1004'){

                                            var httpMethod = 'POST',
                                                url = vURL+urlAuthorizeOrder,
                                                parameters = {
                                                oauth_consumer_key : consumer_key,
                                                oauth_nonce : auth_nonce,
                                                oauth_timestamp : auth_timestamp,
                                                oauth_signature_method : 'HMAC-SHA1',
                                                oauth_version : '1.0'
                                            },
                                            consumerSecret = consumer_secretkey,
                                            oauthSignatureAuthorize = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');                   

                                             $.ajax({
                                                url: "/plugins/PLPayAtStore/payatstore.php"
                                                ,type: "GET"
                                                ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:oauthSignatureAuthorize,type:2,ustr:msg.OrderID}
                                                ,success: function(msgs){

                                                    var httpMethod = 'POST',
                                                        url = vURL+urlUpdateStat+merchantID,
                                                        parameters = {
                                                        oauth_consumer_key : consumer_key,
                                                        oauth_nonce : auth_nonce,
                                                        oauth_timestamp : auth_timestamp,
                                                        oauth_signature_method : 'HMAC-SHA1',
                                                        oauth_version : '1.0'
                                                    },
                                                    consumerSecret = consumer_secretkey,
                                                    oauthSignatureUpdateStat = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, ''); 

                                                    $.ajax({
                                                        url: "/plugins/PLPayAtStore/payatstore.php"
                                                        ,type: "GET"
                                                        ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:oauthSignatureAuthorize,type:3,ustr:msg.OrderID}
                                                        ,success: function(msgss){

//                                                        var infoData = "[{\"so_cancel_flag\":\"true\"";
//                                                            infoData += "}]";
//
//                                                        var row_version = parseInt(res.data[0].row_version);
//
//                                                        $http.put(res.data[0].link+"?filter=row_version,eq,"+res.data[0].row_version,infoData,{headers:{"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).success(function(){

                                                            deferred.resolve();

//                                                        });

                                                        }
                                                    });

                                                }
                                            });

                                        } else {
                                            deferred.resolve();
                                        }


                                    }
                                });

                            } else {

                                deferred.resolve();

                            }

                        });
        
                    } else {
                        deferred.resolve();
                    }
                    
                }
                
        });
        //--------------------------------------------------------------------*/
        return deferred.promise;
    }
);


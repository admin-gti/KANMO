var addCancelOrderCtrl = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "$stateParams","LoadingScreen","ResourceNotificationService","$modalInstance",
    function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window, $stateParams, i, NotificationService,$modalInstance) {
    
    var deferred =o.defer();
//    console.log(c.get().subsidiarysid);
    /*----------------------------MAIN----------------------------------------*/
    //GENERATE SIGNATURE
    var vURL               = MARTJACK.URL;
    var urlGetHistory      = MARTJACK.GETHISTORY;
    var urlCancelOrder     = MARTJACK.CANCELORDER;
    var merchantID         = MARTJACK.MERCHANTID;
    var consumer_key       = MARTJACK.CONSUMERKEY;
    var consumer_secretkey = MARTJACK.CONSUMER_SECRETKEY;

    var auth_nonce         = Math.random().toString(36).replace(/[^a-z]/, '').substr(2);
    var auth_timestamp     = Math.round((new Date()).getTime() / 1000.0);
    /*------------------------------------------------------------------------*/
    
    
    
    /*-----------------------------CLOSE--------------------------------------*/
    a.close = function(a){
        e.dismiss();
        m.go(m.current, {}, {reload: true});
    }
    /*------------------------------------------------------------------------*/
    
    /*-----------------------------APPLY--------------------------------------*/
    a.update = function(){
//        console.log($("#cancelreason").val());

        var cancel_reason = $("#cancelreason").val();
        var cancel_comment = $("#cancelcomment").val();
        
        b.get('v1/rest/document/'+$stateParams.document_sid+'?cols=*', {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(a){
            console.log(a.data[0]);
            
            var httpMethod = 'POST',
                url = vURL+urlGetHistory+merchantID,
                parameters = {
                oauth_consumer_key : consumer_key,
                oauth_nonce : auth_nonce,
                oauth_timestamp : auth_timestamp,
                oauth_signature_method : 'HMAC-SHA1',
                oauth_version : '1.0'
            },
            consumerSecret = consumer_secretkey,
            oauthSignatureGetHistory = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

            $.ajax({
                url: "/plugins/PLCancelOrder/cancelOrder.php"
                ,type: "GET"
                ,data: {an:auth_nonce,at:auth_timestamp,s:oauthSignatureGetHistory,type:1,sid:$stateParams.document_sid}
                ,success: function(scs1){
                    console.log(scs1);
                    
                    if(scs1.Message == "Successful"){
                        
                        var httpMethod = 'POST',
                            url = vURL+urlCancelOrder,
                            parameters = {
                            oauth_consumer_key : consumer_key,
                            oauth_nonce : auth_nonce,
                            oauth_timestamp : auth_timestamp,
                            oauth_signature_method : 'HMAC-SHA1',
                            oauth_version : '1.0'
                        },
                        consumerSecret = consumer_secretkey,
                        oauthSignatureCancelOrder = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                        $.ajax({
                            url: "/plugins/PLCancelOrder/cancelOrder.php"
                            ,type: "GET"
                            ,data: {an:auth_nonce,at:auth_timestamp,s:oauthSignatureCancelOrder,type:2,oid:scs1.Orders[0].OrderId,cr:cancel_reason,cc:cancel_comment}
                            ,success: function(scs2){
                                
                                if(scs2.messageCode == '1002'){
                                    
                                    deferred.resolve();
                                    
                                } else {
                                    
                                    deferred.reject();
                                    return false;
                                    
                                }
                                
                            }
                        });
                        
                    } else {
                        deferred.reject();
                        return false;
                    }
                    
                }
            });
            
        });
    }
    /*------------------------------------------------------------------------*/
//    deferred.resolve();
    return deferred.promise;
    
}];

window.angular.module('prismPluginsSample.controller.addCancelOrderCtrl', [])
   .controller('addCancelOrderCtrl', addCancelOrderCtrl);

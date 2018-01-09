var addAWB = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "$stateParams","LoadingScreen","ResourceNotificationService","$modalInstance",
    function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window, $stateParams, i, NotificationService,$modalInstance) {
    
    var deferred =o.defer();
    
    /*----------------------------MAIN----------------------------------------*/
    //GENERATE SIGNATURE
    var vURL               = MARTJACK.URL;
    var urlGetInfo         = MARTJACK.GETINFO;
    var urlUpdateCourier   = MARTJACK.UPDATECOURIER;
    var urlCreateManifest  = MARTJACK.CREATEMANIFEST;
    var urlUpdateStat      = MARTJACK.UPDATESHIPSTAT;
    var merchantID         = MARTJACK.MERCHANTID;
    var consumer_key       = MARTJACK.CONSUMERKEY;
    var consumer_secretkey = MARTJACK.CONSUMER_SECRETKEY;
    
    var auth_nonce         = Math.random().toString(36).replace(/[^a-z]/, '').substr(2);
    var auth_timestamp     = Math.round((new Date()).getTime() / 1000.0);
    
    ms.get("Shippingmethod", {
        cols: "sid,method",
        filter: "(active,eq,1)AND(subsidiary_sid,eq," + c.get().subsidiarysid + ")",
        sort: "method,ASC"
    }).then(function(t) {
        a.shipMethods = pu.responseParser(t);
    });
    
        
    a.update = function(a){    
        
    /*-------------------------CREATE SHIPMENT SIGNATURE----------------------*/
        var courier = ($("#couriername").val()).substr(7);
        var shipping = $("#shipping_date").val();
        var awb      = $("#awbnumber").val();
        
        i.Enable = 1;
        if(courier == ''){
            NotificationService.showWarning( 'WARNING', "Courier Name is required");
           
        }
        if(shipping == ''){
            NotificationService.showWarning( 'WARNING', "Shipping date is required");
        }
        if(awb == ''){
            NotificationService.showWarning( 'WARNING', "AWB Number is required");
        }
        
        if(courier == '' || shipping == '' || awb == ''){
            i.Enable = 1;
            deferred.resolve;
        }
        
        ms.get("Shippingmethod", {
                cols: "method",
                filter: "(sid,eq,"+courier+")"
            }).then(function(t) {
                var courier_fname = t[0].method;
        
            b.get('v1/rest/document/'+$stateParams.document_sid+'?cols=ref_order_sid', {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res1){


                b.get('v1/rest/document/'+res1.data[0].ref_order_sid+'?cols=*', {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res2){

//                    console.log(res2.data[0]);

                   var httpMethod = 'GET',
                        url = url = vURL+'Order/'+merchantID+'/'+res2.data[0].notes_general+'/Shipments', 
                        parameters = {
                        oauth_consumer_key : consumer_key,
                        oauth_nonce : auth_nonce,
                        oauth_timestamp : auth_timestamp,
                        oauth_signature_method : 'HMAC-SHA1',
                        oauth_version : '1.0'
                    },
                    consumerSecret = consumer_secretkey,
                    oauthSignatureGetInfo = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                    $.ajax({
                        url: "/plugins/PLAWB/awb.php"
                        ,type: "GET"
                        ,data: {sid:$stateParams.document_sid,an:auth_nonce,at:auth_timestamp,s:oauthSignatureGetInfo,type:1,id:res2.data[0].notes_general}
                        ,success: function(res3){

                            if(res3.Message == "Successful"){

                                console.log(res3.Shipments[0].ShipmentId);

                                var httpMethod = 'POST',
                                    url = vURL+urlUpdateCourier+merchantID, 
                                    parameters = {
                                    oauth_consumer_key : consumer_key,
                                    oauth_nonce : auth_nonce,
                                    oauth_timestamp : auth_timestamp,
                                    oauth_signature_method : 'HMAC-SHA1',
                                    oauth_version : '1.0'
                                },
                                consumerSecret = consumer_secretkey,
                                oauthSignatureUpdateCourier = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                                $.ajax({
                                    url: "/plugins/PLAWB/awb.php"
                                    ,type: "GET"
                                    ,data: {an:auth_nonce,at:auth_timestamp,s:oauthSignatureUpdateCourier,sh:shipping,aw:'KANMO',cn:courier_fname,type:2,id:res2.data[0].notes_general,sid:res3.Shipments[0].ShipmentId}
                                    ,success: function(res4){
                                        
                                        if(res4.messageCode == '1007'){
                                            
                                            var httpMethod = 'POST',
                                                url = vURL+urlCreateManifest+merchantID, 
                                                parameters = {
                                                oauth_consumer_key : consumer_key,
                                                oauth_nonce : auth_nonce,
                                                oauth_timestamp : auth_timestamp,
                                                oauth_signature_method : 'HMAC-SHA1',
                                                oauth_version : '1.0'
                                            },
                                            consumerSecret = consumer_secretkey,
                                            oauthSignatureCreateManifest = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                                            $.ajax({
                                                url: "/plugins/PLAWB/awb.php"
                                                ,type: "GET"
                                                ,data: {an:auth_nonce,at:auth_timestamp,s:oauthSignatureCreateManifest,cn:courier_fname,type:4,id:res2.data[0].notes_general,sid:res3.Shipments[0].ShipmentId,store:res2.data[0].store_uid}
                                                ,success: function(res4){
                                                    console.log(res4);
                                                }
                                            });
                                            
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
                                            encodedSignatureUpdateShipStat = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                                            $.ajax({
                                               url: "/plugins/PLAWB/awb.php"
                                               ,type: "GET"
                                               ,data: {an:auth_nonce,at:auth_timestamp,s:encodedSignatureUpdateShipStat,type:3,data:res3.Shipments[0].ShipmentId}
                                               ,success: function(res5){

                                                if(res5.messageCode == '1007'){
                                                    
                                                     b.get('v1/rest/document?cols=*&filter=sid,eq,'+$stateParams.document_sid,{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(doc){
                                                        var datapost = "[{";
                                                            datapost += "\"tracking_number\":\""+awb+"\",";
                                                            datapost += "\"udf_string2\":\"DISPATCHED\",";
                                                            datapost += "\"ship_method_sid\":\""+courier+"\"";
        //                                                    store += "\"ship_date\":\""++"\",";
                                                            datapost += "}]";

                                                        b.put("v1/rest/document/"+$stateParams.document_sid+"?filter=row_version,eq,"+doc.data[0].row_version,datapost,{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).success(function(){});
                                                        NotificationService.showSuccessfulMessage( 'Updated!', 'Shipment Details succesfully save');
                                                        i.Enable = !1,
                                                        deferred.resolve;
                                                        
                                                    });
                                                    
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

                            } else {

                                deferred.reject();
                                return false;
                            }

                        }
                    });

                });

            });
        });
        return deferred.promise;
    };

    
    /*-----------------------------CLOSE--------------------------------------*/
    a.close = function(a){
        e.dismiss('cancel');
        m.go(m.current, {}, {reload: true});
    }
    /*------------------------------------------------------------------------*/
//    
//    /*-----------------------------ADDING AWB INFOS---------------------------*/
//    a.update = function(a){
////        
//        console.log($("#shipping_date").val());
//        console.log($("#awbnumber").val());
//        console.log($("#couriername").val());
//        
//        var infoData = "MerchantID="+merchantID+"&InputFormat=application/json&InputData={";
//            infoData += "\"shipment\":";
//            infoData += "{";
//            infoData += "\"OrderId\":\"12341734\",";
//            infoData += "\"LocationRefCode\":\"Location2\",";
//            infoData += "\"ShipDate\":\"" +$("#shipping_date").val()+"\",";
//            infoData += "\"ShipmentType\":\"normal\"";
//            infoData += "\"CourierName\":\"" +$("#couriername").val()+"\",";
//            infoData += "\"AWBNumber\":\"" +$("#awbnumber").val()+"\"";
//            infoData += "\"lineitems\":";
//            infoData += "[";
//            infoData += "{";
//            infoData += "\"OrderLineId\":\"12341734\",";
//            infoData += "\"Weight\":\"0\",";
//            infoData += "\"Quantity\":\"5\"";
//            infoData += "}";
//            infoData += "],";
//            infoData += "\"MerchantId\":\"" +merchantID+"\"";
//            infoData += "}";
//        console.log(infoData);
//        var createShip = {
//            method: 'POST',
//            url: url+"?oauth_consumer_key="+consumer_key+"&oauth_nonce="+auth_nonce+"&oauth_signature="+encodedSignature_createship+"&oauth_signature_method=HMAC-SHA1&oauth_timestamp="+auth_timestamp+"&oauth_version=1.0",
//            headers: {
//              'Content-Type': 'application/x-www-form-urlencoded',
//              "accept": "application/json"
//            },
//            data: infoData
//        }

//        i.Enable = 1, b(createShip).then(function(g){
//            
//            
//            deferred.resolve();
//        }
//        , function(error) {
//            NotificationService.showError( 'Error!', 'Connection timeout to Martjack');
//            i.Enable = !1;
//            e.dismiss('cancel');
//            m.go(m.current, {}, {reload: true});
//        }
//        );
        
//        NotificationService.showSuccessfulMessage( 'Updated!', 'Fulfillment location successfully added');
//        
//        e.dismiss('cancel');
//        m.go(m.current, {}, {reload: true});
        
        deferred.resolve();
//    }
    /*------------------------------------------------------------------------*/
    return deferred.promise;
    
}];

window.angular.module('prismPluginsSample.controller.addAWBCtrl', [])
   .controller('addAWBCtrl', addAWB);

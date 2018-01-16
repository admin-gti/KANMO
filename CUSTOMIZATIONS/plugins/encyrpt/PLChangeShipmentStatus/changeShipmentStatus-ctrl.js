var changeShipmentStatus = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "LoadingScreen", "ResourceNotificationService",
function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window,i,NotificationService) {
    
    var deferred = o.defer();
    
    var vURL                = MARTJACK.URL;
    var urlGetHistory       = MARTJACK.GETHISTORY;
    var urlGetInfo          = MARTJACK.GETINFO;
    var urlUChangeSubStatus = MARTJACK.CHANGESUBSTATUS;
    var urlUpdateStat       = MARTJACK.UPDATESHIPSTAT;
    var merchantID          = MARTJACK.MERCHANTID;
    var consumer_key        = MARTJACK.CONSUMERKEY;
    var consumer_secretkey  = MARTJACK.CONSUMER_SECRETKEY;

    var auth_nonce          = Math.random().toString(36).replace(/[^a-z]/, '').substr(2);
    var auth_timestamp      = Math.round((new Date()).getTime() / 1000.0);
    
    $("#displaydetails").fadeOut('fast');
    $("#shipStatus1").fadeOut('fast');
//    $("#shipStatus2").fadeOut('fast');
    
    a.search = function(){
        
        var reference_no = $("#reference_order").val();
        
        /*---WAITING FOR PICKUP @SAMESTORE-SHIP*/
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

        i.Enable = 1,$.ajax({
            url: "/plugins/PLChangeStat/getOMSInfo.php"
            ,type: "GET"
            ,data: {an:auth_nonce,at:auth_timestamp,s:oauthSignatureGetHistory,type:1,sid:reference_no}
            ,success: function(scs1){
//                            
                a.shipstate = scs1.Orders[0].SubStatus;
                console.log(a.shipstate);
                if(scs1.Message == 'Successful'){
                    i.Enable = !1;
                    var httpMethod = 'GET',
                        url = vURL+urlGetInfo+merchantID+'/'+scs1.Orders[0].OrderId,
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
                        url: "/plugins/PLChangeStat/getOMSInfo.php"
                        ,type: "GET"
                        ,data: {sid:0,an:auth_nonce,at:auth_timestamp,s:oauthSignatureGetInfo,type:2,oid:scs1.Orders[0].OrderId,ref:reference_no,isview:1}
                        ,success: function(scs2){
                            
                            b.get('v1/rest/document?cols=*'+"&filter=(notes_order,eq,"+reference_no+")AND(notes_general,NN)",
                            {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
                                    a.doc = res.data[0];
                                    if(res.data.length > 0) {                    

                                        $("#displaydetails").fadeIn('fast');
                                        $("#displaystatusdetails").fadeIn('fast');
                                        $("#displayNoRecord").fadeOut('fast');
                                        
                                        deferred.resolve();
                                        
                                    } else {
                                        
                                        $("#displaydetails").fadeOut('fast');
                                        $("#displaystatusdetails").fadeOut('fast');
                                        $("#displayNoRecord").fadeIn('fast');
                                        
                                        deferred.resolve();
                                        
                                    }

                            });
                        }

                    });

                } else {

                    deferred.resolve();
                    i.Enable = !1;

                }

            }
        });
        
        
        b.get('v1/rest/document?cols=*'+"&filter=(notes_order,eq,"+reference_no+")AND(notes_general,NN)",
        {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
                a.doc = res.data[0];
                if(res.data.length > 0) {               
                    $("#displaydetails").fadeIn('fast');
                    $("#displaystatusdetails").fadeIn('fast');
                    $("#displayNoRecord").fadeOut('fast');
                } else {
                    $("#displaydetails").fadeOut('fast');
                    $("#displaystatusdetails").fadeOut('fast');
                    $("#displayNoRecord").fadeIn('fast');
                }
                
                b.get('v1/rest/document?cols=*'+"&filter=(ref_order_sid,eq,"+a.doc.sid+")",
                {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(document){
                    
                    if(document.data.length > 0) {
                        a.ref_order_sid = 1;
                    } else {
                        a.ref_order_sid = '';
                    }
                });
//                console.log(a.stat);
                
        });
       
   }
   
   a.update = function(){
        
        var status = $("#changeStatus").val();
//        console.log(status);
        data = a.doc;
        console.log(status);
        console.log(a.doc);
        
//        if(status == 'PK'){
            var httpMethod = 'POST',
                url = vURL+urlUChangeSubStatus+merchantID+'/'+data.notes_general,
                parameters = {
                oauth_consumer_key : consumer_key,
                oauth_nonce : auth_nonce,
                oauth_timestamp : auth_timestamp,
                oauth_signature_method : 'HMAC-SHA1',
                oauth_version : '1.0'
            },
            consumerSecret = consumer_secretkey,
            encodedSignaturegetSubStatus = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
//            console.log(url);
             $.ajax({
                url: "/plugins/PLChangeShipmentStatus/changeOrder.php"
                ,type: "GET"
                ,data: {sid:data.sid,an:auth_nonce,at:auth_timestamp,s:encodedSignaturegetSubStatus,type:5,id:data.notes_general,stat:status}
                ,success: function(msgs){
                    NotificationService.showSuccessfulMessage('SUCCESS!', 'Status successfully updated');
                    deferred.resolve();
                }
            });
            
            if(status == 'DP' || status == 'DL'){
                var httpMethod = 'GET',
                    url = vURL+'Order/'+merchantID+'/'+data.notes_general+'/Shipments',
                    parameters = {
                    oauth_consumer_key : consumer_key,
                    oauth_nonce : auth_nonce,
                    oauth_timestamp : auth_timestamp,
                    oauth_signature_method : 'HMAC-SHA1',
                    oauth_version : '1.0'
                },
                consumerSecret = consumer_secretkey,
                encodedSignaturegetShipment = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                $.ajax({
                    url: "/plugins/PLChangeShipmentStatus/changeOrder.php"
                    ,type: "GET"
                    ,data: {sid:data.sid,an:auth_nonce,at:auth_timestamp,s:encodedSignaturegetShipment,type:2,id:data.notes_general}
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
                        encodedSignatureUpdateShipStat = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');

                        $.ajax({
                            url: "/plugins/PLChangeShipmentStatus/changeOrder.php"
                            ,type: "GET"
                            ,data: {sid:data.sid,an:auth_nonce,at:auth_timestamp,s:encodedSignatureUpdateShipStat,type:3,data:msgs.Shipments[0].ShipmentId,stat:status}
                            ,success: function(msgss){        

                                deferred.resolve();

                            }
                        });

                    }
                });
            }
            if(status == 'FA' || status == 'DP'){
                    var modalOptions = {
                        backdrop: 'static',
                        keyboard: false,
                        size: 'lg',
                        templateUrl: '/plugins/PLChangeShipmentStatus/printOut.htm',
                        controller: 'FAandDispatchPrintOutCtrl'
                    };
                    
//                    localStorage.removeItem('headerforPrintout');
                    
                    localStorage.setItem('headerforPrintout', 'Print Out Label');
                    
                    f.open(modalOptions);
            }
            
            NotificationService.showSuccessfulMessage( 'Updated!', 'Status has been Updated!');
                deferred.resolve();

       
   }
   
   a.close = function(){
    e.dismiss();
    m.go(m.current, {}, {reload: true});
   }
   
   return deferred.promise;

}];

window.angular.module('prismPluginsSample.controller.changeShipmentStatusCtrl', [])
   .controller('changeShipmentStatusCtrl', changeShipmentStatus);
   


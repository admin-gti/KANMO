var FAandDispatchPrintOut = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "LoadingScreen", "ResourceNotificationService",
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
    
    var httpMethod = 'GET',
            url = vURL+'Order/'+merchantID+'/'+localStorage.getItem('omsID')+'/Shipments',
            parameters = {
            oauth_consumer_key : consumer_key,
            oauth_nonce : auth_nonce,
            oauth_timestamp : auth_timestamp,
            oauth_signature_method : 'HMAC-SHA1',
            oauth_version : '1.0'
        },
        consumerSecret = consumer_secretkey,
        encodedSignaturegetShipment = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
    
    i.Enable = 1;
    console.log(localStorage.getItem('omsID'));
    b.get("/plugins/PLChangeShipmentStatus/changeOrder.php?an="+auth_nonce+"&at="+auth_timestamp+"&s="+encodedSignaturegetShipment+"&type=2&id="+localStorage.getItem('omsID')).then(function(msgs) {
        b.get("/plugins/PLChangeShipmentStatus/printOut.php?oid="+msgs.data.Shipments[0].ShipmentId+"&stype="+localStorage.getItem('Shipmenttype')).then(function(msgss) {
            a.printout = msgss.data;
            deferred.resolve();
            i.Enable = !1; 
        });
    });
    
    a.headerforPrintout = localStorage.getItem('headerforPrintout');
    
    a.close = function(){
        e.dismiss();
        m.go(m.current, {}, {reload: true});
    }
    
    a.print = function(){
        console.log('test');
        var printContents = document.getElementById('printingID').innerHTML;
        var originalContents = document.body.innerHTML;
//        printContents.css({ "width": "300px","height": "548px","left": "50%", "margin-left": "-105px", "margin-top": "-293px" });
        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        
//        e.dismiss();
//        m.go(m.current, {}, {reload: true});
    }
    
    return deferred.promise;
    
}];

window.angular.module('prismPluginsSample.controller.FAandDispatchPrintOutCtrl', [])
   .controller('FAandDispatchPrintOutCtrl', FAandDispatchPrintOut);
   


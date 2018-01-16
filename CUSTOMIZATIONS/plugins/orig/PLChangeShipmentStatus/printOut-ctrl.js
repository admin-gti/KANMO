var FAandDispatchPrintOut = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "LoadingScreen", "ResourceNotificationService",
function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window,i,NotificationService) {
    
    var deferred = o.defer();
    return deferred.promise;
    
    a.headerforPrintout = localStorage.getItem('headerforPrintout');
    console.log(a.headerforPrintout);
    
    a.close = function(){
        e.dismiss();
        m.go(m.current, {}, {reload: true});
    }
    
}];

window.angular.module('prismPluginsSample.controller.FAandDispatchPrintOutCtrl', [])
   .controller('FAandDispatchPrintOutCtrl', FAandDispatchPrintOut);
   


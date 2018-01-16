var viewInventoryCtrl = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "$stateParams","LoadingScreen","ResourceNotificationService","$modalInstance",
    function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window, $stateParams, i, NotificationService,$modalInstance) {
    var deferred =o.defer();
    
    
    
    $.ajax({
        url: "/plugins/PLFulfillmentAndFreight/viewInventory.php"
        ,type: "GET"
        ,data: {type:1,sid:$stateParams.document_sid,sto:localStorage.getItem('locationID')}
        ,success: function(rest){
            a.inventory = rest;
            a.storeName = localStorage.getItem('StoreName');
            deferred.resolve();
            
        }
    });
    a.close = function(){
        $modalInstance.dismiss();
        m.go(m.current, {}, {reload: true});
    }
    /*------------------------------------------------------------------------*/
//    deferred.resolve();
    return deferred.promise;
    
}];

window.angular.module('prismPluginsSample.controller.viewInventoryCtrl', [])
   .controller('viewInventoryCtrl', viewInventoryCtrl);

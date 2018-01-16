var committedQtyCM = ['ModelEvent', '$modal', 'ModelService2', 'NotificationService', '$window', '$http', '$state', '$stateParams', 'ModelService', function(ModelEvent, $modal, ModelService2, NotificationService, $window, $http, $state, $stateParams, ModelService) {
    var servername = $window.location.origin;
    var sess = $http.defaults.headers.common['Auth-Session'];

    var handlerAfterItemInsert = function($q, item) {
        var activeDoc = ModelService.fromCache('Document')[0];
        var deferred = $q.defer();
        var activeDocumentSid = $stateParams.document_sid;
        $http.get(servername + '/plugins/PLCommittedQty/committed_qty.php?action=getCommittedQty&alu=' + item.alu + '&store_code=' + activeDoc.original_store_code).then(function(result){
            var jsonObj = JSON.parse(result.data);
            var true_oh_qty = parseInt(item.inventory_on_hand_quantity) - parseInt(jsonObj.QTY); 
            sessionStorage.setItem('committed_qty_data', JSON.stringify({item_alu: item.alu, committed_qty: jsonObj.QTY, oh_qty: item.inventory_on_hand_quantity, t_oh_qty: true_oh_qty}));
        });

        deferred.resolve();
        //return the deferred promise
        return deferred.promise;
    };

    var handlerBeforeItemSave = function($q, item) {
        var deferred = $q.defer();
        var activeDocumentSid = $stateParams.document_sid;
        ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
            $http.get(servername + '/plugins/PLCommittedQty/committed_qty.php?action=getCommittedQty&alu=' + item.alu + '&store_code=' + doc[0].original_store_code).then(function(result){
                var jsonObj = JSON.parse(result.data);
                var true_oh_qty = parseInt(item.inventory_on_hand_quantity) - parseInt(jsonObj.QTY);
                if(true_oh_qty >= item.quantity)
                {
                    deferred.resolve();
                }
                else
                {
                    NotificationService.addAlert('Quantity is greater than True On Hand Qty!', 'Item Quantity Validation', 'static', false).then(function(){
                        deferred.reject();
                        $state.go($state.current, {}, {reload: true});
                    });
                }
            });
        });

        //return the deferred promise
        return deferred.promise;
    };

    ModelEvent.addListener('item', ['onAfterInsert'], handlerAfterItemInsert);
    ModelEvent.addListener('item', ['onBeforeSave'], handlerBeforeItemSave);
}]

ConfigurationManager.addHandler(committedQtyCM);

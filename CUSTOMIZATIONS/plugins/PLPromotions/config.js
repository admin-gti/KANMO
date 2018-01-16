var promotionCM = ['ModelEvent', '$modal', 'ModelService2', 'NotificationService', '$window', '$http', '$state', '$stateParams', function(ModelEvent, $modal, ModelService2, NotificationService, $window, $http, $state, $stateParams) {
    var servername = $window.location.origin;
    var sess = $http.defaults.headers.common['Auth-Session'];

    var handlerAfterItem = function($q, item) {
        var deferred = $q.defer();
        var activeDocumentSid = $stateParams.document_sid;
        var params = [{"Params":{"DocumentSid": activeDocumentSid},"MethodName":"PCPromoApplyManually"}];
        $http.post(servername + '/v1/rpc', params).then(function(){
            deferred.resolve();
        });

        //return the deferred promise
        return deferred.promise;
    };

    ModelEvent.addListener('item', ['onAfterInsert', 'onAfterSave', 'onAfterRemove'], handlerAfterItem);
}]

ConfigurationManager.addHandler(promotionCM);
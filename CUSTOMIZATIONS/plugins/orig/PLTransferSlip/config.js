/*=============== FOR UPDATING SLIP REASON =============*/
SideButtonsManager.addButton({
    label: 'Update Slip Reason',
    sections: ['store-ops.transfers.new'],
    handler: ['$modal', function($modal) {

        var modalOptions = {
            backdrop: 'static',
            // windowClass: 'sm',
            size: 'sm', // sm, md, lg
            templateUrl: '/plugins/PLTransferSlip/slipReason.htm',
            controller: 'slipReasonCtrl',
            keyboard: false
        };

        $modal.open(modalOptions);
    }]
});
/*=============== END OF UPDATING SLIP REASON =============*/

/*=============== FOR UPDATING CARTON NUMBER =============*/
SideButtonsManager.addButton({
    label: 'Update Carton Number',
    sections: ['store-ops.transfers.new'],
    handler: ['$modal', 'ModelService2', 'NotificationService', '$state', function($modal, ModelService2, NotificationService, $state) {
        var activeSlip   = ModelService2.fromCache('Transferslip')[0];

        if(activeSlip.slipcomment.length != 0)
        {
            var modalOptions = {
                backdrop: 'static',
                windowClass: 'full',
                // size: 'lg', // sm, md, lg
                templateUrl: '/plugins/PLTransferSlip/cartonNumbers.htm',
                controller: 'cartonNumberCtrl',
                keyboard: false
            };

            $modal.open(modalOptions);
        }
        else
        {
            NotificationService.addAlert('No Slip Reason found. Please set it first!', 'Slip Reason Validation', 'static', false).then(function(){
                $state.go($state.current, {}, {reload: true});
            });
        }
    }]
});

/*=============== END OF UPDATING CARTON NUMBER =============*/

/*=============== FOR CREATING NEW CARTON NUMBER PER ITEM =============*/
var tranferSlipConfig = ['ModelEvent','$modal', 'ModelService2', 'NotificationService', '$window', '$http', '$state', function(ModelEvent, $modal, ModelService2, NotificationService, $window, $http, $state){
    var servername = $window.location.origin;
    var sess       = $http.defaults.headers.common['Auth-Session'];
    var handlerAfterSlipItem = function($q, slipitem){
        var deferred = $q.defer();

        var openModal = function(){
            $modal.open({
                backdrop: 'static',
                // windowClass: 'sm',
                size: 'md', // sm, md, lg
                templateUrl: '/plugins/PLTransferSlip/itemCartonNumber.htm',
                controller: 'itemCartonNumberCtrl',
                keyboard: false
            });
        };

        var activeSlip   = ModelService2.fromCache('Transferslip')[0];

        if(activeSlip.slipcomment.length != 0)
        {
            openModal();
            //resolve the deferred operation
            deferred.resolve();
        }
        else
        {
            //diplay confimation window
            var confirm = NotificationService.addAlert('No Slip Reason found. Please set it first!', 'Slip Reason Validation', 'static', false);

            //resolve the result of the confirmation
            confirm.then(function(){
                $http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
                    var chainRequest = $q.when();
                    angular.forEach(res.data.data, function(value){
                        chainRequest = chainRequest.then(function(){
                            return $http.delete(servername + '/api/backoffice/transferslip/'+ activeSlip.sid +'/slipitem/'+ value.sid).then(function(){
                                $state.go($state.current, {}, {reload: true});
                            });
                        });
                    });
                });
                deferred.resolve();
            });

            
        }

        //return the deferred promise
        return deferred.promise;
    };

    var handlerAfterTransferslip = function($q, transferslip){
        var deferred = $q.defer();

        var modalOptions = {
            backdrop: 'static',
            // windowClass: 'sm',
            size: 'sm', // sm, md, lg
            templateUrl: '/plugins/PLTransferSlip/slipReason.htm',
            controller: 'slipReasonCtrl',
            keyboard: false
        };

        $modal.open(modalOptions);
        deferred.resolve();

        //return the deferred promise
        return deferred.promise;
    };

    ModelEvent.addListener('slipitem', ['onAfterInsert'], handlerAfterSlipItem);
    ModelEvent.addListener('backoffice/transferslip', ['onAfterInsert'], handlerAfterTransferslip);
}]

ConfigurationManager.addHandler(tranferSlipConfig);
/*=============== END OF CREATING NEW CARTON NUMBER PER ITEM =============*/

ButtonHooksManager.addHandler(['before_updateTransferSlipBtn'],['ModelService2', '$q', 'NotificationService', '$rootScope', 'HookEvent', '$http', '$window', '$state', '$modal',
    function(ModelService2, $q, NotificationService, $rootScope, HookEvent, $http, $window, $state, $modal){
        var deferred            = $q.defer();
        var activeSlip          = ModelService2.fromCache('Transferslip')[0];
        $rootScope.hookEvent    = HookEvent;
        var activatingButton    = $rootScope.hookEvent.target;
        var servername          = $window.location.origin;

        ModelService2.get('SlipComment', {sid:activeSlip.slipcomment[0].sid, slipsid: activeSlip.sid}).then(function(result){
            var currentComment = result[0].comments;
            if(currentComment.toUpperCase() == 'NORMAL')
            {
                $http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
                    var x = [];
                    angular.forEach(res.data.data, function(value){
                        if(value.itemnote10 != '' && value.itemnote10 != null)
                        {
                            x.push('1');
                        }
                        else
                        {
                            x.push('0');
                        }
                    });
                    if(x.indexOf('0') === -1)
                    {
                        deferred.resolve();
                    }
                    else
                    {
                        NotificationService.addAlert('Item Carton Number is required. Please fill all Carton Number fields!', 'Carton Number Validation', 'static', false).then(function(){
                            $state.go($state.current, {}, {reload: true});
                            var modalOptions = {
                                backdrop: 'static',
                                windowClass: 'full',
                                // size: 'lg', // sm, md, lg
                                templateUrl: '/plugins/PLTransferSlip/cartonNumbers.htm',
                                controller: 'cartonNumberCtrl',
                                keyboard: false
                            };

                            $modal.open(modalOptions);
                        });
                        deferred.reject();
                    }
                });

            }
            else if(currentComment.toUpperCase() == 'DAMAGE')
            {
                $http.get(servername + '/api/backoffice/transferslip/slipitem?cols=*&filter=slipsid,eq,'+activeSlip.sid).then(function(res){
                    var x = [];
                    angular.forEach(res.data.data, function(value){
                        if(value.itemnote1 != '' && value.itemnote1 != null && value.itemnote2 != '' && value.itemnote2 != null && value.itemnote3 != '' && value.itemnote3 != null && value.itemnote10 != '' && value.itemnote10 != null)
                        {
                            x.push('1');
                        }
                        else
                        {
                            x.push('0');
                        }
                    });
                    if(x.indexOf('0') === -1)
                    {
                        deferred.resolve();
                    }
                    else
                    {
                        NotificationService.addAlert('Damage Reason 1, Damage Reason 2, Damage Comment and Carton Number is required. Please fill all required fields!', 'Carton Number Validation', 'static', false).then(function(){
                            $state.go($state.current, {}, {reload: true});
                            var modalOptions = {
                                backdrop: 'static',
                                windowClass: 'full',
                                // size: 'lg', // sm, md, lg
                                templateUrl: '/plugins/PLTransferSlip/cartonNumbers.htm',
                                controller: 'cartonNumberCtrl',
                                keyboard: false
                            };

                            $modal.open(modalOptions);
                        });
                        deferred.reject();
                    }
                });
            }
        });

        return deferred.promise;
    }]
);
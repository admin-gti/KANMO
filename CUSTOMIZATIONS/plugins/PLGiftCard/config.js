ButtonHooksManager.addHandler(['after_posGiftCard'],
    function(NotificationService, $modal, $stateParams, ModelService, $q, $state){
    	var deffered = $q.defer();

        var modalOptions = {
                backdrop: 'static',
                // windowClass: 'sm',
                size: 'md', // sm, md, lg
                templateUrl: '/plugins/PLGiftCard/gcType.htm',
                controller: 'giftCardCtrl',
                keyboard: false
            };

        $modal.open(modalOptions);
        deffered.resolve();

        return deffered.promise;
});

ButtonHooksManager.addHandler(['after_posCancelDocCancel', 'after_navPosTransactionCancel'], function($q){
    var deffered = $q.defer();

    sessionStorage.removeItem('cardItems');
    sessionStorage.removeItem('gcItem');
    sessionStorage.removeItem('activeCustomer');

    deffered.resolve();
    return deffered.promise;
});

ButtonHooksManager.addHandler(['before_posTransactionTenderTransaction'], function($q, NotificationService, $state, $stateParams, ModelService){
    var deffered = $q.defer();
    var activeDocumentSid = $stateParams.document_sid;
    var gcNoNumber = [];
    ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
        angular.forEach(items, function(value, key){
            if(value.dcs_code == '888800101' && value.note5 == '')
            {
                gcNoNumber.push(1);
            }
            else
            {
                gcNoNumber.push(0);
            }
        });
        $q.all(gcNoNumber).then(function(){
            if(gcNoNumber.indexOf(1) !== -1)
            {
                NotificationService.addAlert('Gift card items are required to have GC number!', 'Gift Card Validation', 'static', false).then(function(){
                    $state.go($state.current, {}, {reload: true});
                });
                deffered.reject();
            }
            else
            {
                deffered.resolve();
            }
        });
    });

    return deffered.promise;
});

ButtonHooksManager.addHandler(['before_posTenderTypesGiftCard'], function($q, $modal, NotificationService, $state, ModelService, $stateParams){
    var deffered = $q.defer();

    var modalOptions = {
            backdrop: 'static',
            // windowClass: 'sm',
            size: 'md', // sm, md, lg
            templateUrl: '/plugins/PLGiftCard/redeem.htm',
            controller: 'redeemCardCtrl',
            keyboard: false
        };
    ModelService.get('Document', {sid: $stateParams.document_sid}).then(function(doc){
        if(doc[0].ref_order_sid != '' || doc[0].document_number != '' || doc[0].order_document_number != '' || doc[0].notes_order != '')
        {
            NotificationService.addAlert('Unable to use this tender type in this transaction!', 'Gift Card Validation', 'static', false).then(function(){
                deffered.reject();
            });  
        }
        else
        {
            if(sessionStorage.getItem('hasCustomer') == 1)
            {
                $modal.open(modalOptions);
                deffered.resolve();
            }
            else
            {
                NotificationService.addAlert('Unable to use this tender type because this transaction has no customer!', 'Gift Card Validation', 'static', false).then(function(){
                    deffered.reject();
                });
            }
        }
    });
    return deffered.promise;
});

var giftCardCM = ['ModelEvent', '$stateParams', 'ModelService', '$http', '$window', '$state', 'NotificationService', 'LoadingScreen', function(ModelEvent, $stateParams, ModelService, $http, $window, $state, NotificationService, LoadingScreen){
    var servername = $window.location.origin;


    // var handlerBeforeItemInsert = function($q, item){
    //     var d = $q.defer();
    //     var activeDocumentSid = $stateParams.document_sid;
    //     ModelService.get('Inventory', {sid: item.invn_sbs_item_sid}).then(function(item_result){
    //         if(sessionStorage.getItem('gcItem') === null)
    //         {
    //             if(item_result[0].dcs_code.toUpperCase() == '888800101' && item_result[0].sbsinventoryprices.length == 0)
    //             {
    //                 sessionStorage.setItem('gcItem', JSON.stringify({dcsCode: item_result[0].dcs_code, itemTotal: 1, gcType: 'SINGLE'}));
    //                 d.resolve();
    //             }
    //             else if(item_result[0].dcs_code.toUpperCase() == '888800101' && item_result[0].sbsinventoryprices.length > 0)
    //             {
    //                 sessionStorage.setItem('gcItem', JSON.stringify({dcsCode: item_result[0].dcs_code, itemTotal: 1, gcType: 'MULTIPLE'}));
    //                 d.resolve();
    //             }
    //             else
    //             {
    //                 d.resolve();
    //             }
    //         }
    //         else
    //         {
    //             var json = JSON.parse(sessionStorage.getItem('gcItem'));
    //             var gcType = json.gcType;

    //             if(item_result[0].dcs_code.toUpperCase() == '888800101' && item_result[0].sbsinventoryprices.length == 0)
    //             {
    //                 NotificationService.addAlert('Only one item is allowed in this transaction.', 'Item Validation', 'static', false).then(function(){
    //                     $state.go($state.current, {}, {reload: true});
    //                 });
    //                 d.reject();
    //             }
    //             else if(item_result[0].dcs_code.toUpperCase() == '888800101' && item_result[0].sbsinventoryprices.length > 0)
    //             {
    //                 if(gcType == 'SINGLE')
    //                 {
    //                     NotificationService.addAlert('Only gift card item with open price is allowed in this transaction.', 'Item Validation', 'static', false).then(function(){
    //                         $state.go($state.current, {}, {reload: true});
    //                     });
    //                     d.reject();
    //                 }
    //                 else
    //                 {
    //                     d.resolve();
    //                 }

    //             }
    //             else
    //             {

    //                 NotificationService.addAlert('Only gift card item is allowed in this transaction.', 'Item Validation', 'static', false).then(function(){
    //                     $state.go($state.current, {}, {reload: true});
    //                 });
    //                 d.reject();
    //             }

    //         }
    //     });


    //     //return the deferred promise
    //     return d.promise;
    // };

    var handlerAfterTenderInsert = function($q, tender){
        var d = $q.defer();
        var activeDocumentSid = $stateParams.document_sid;

        if(tender.tender_type == 10)
        {
            if(sessionStorage.getItem('gcRedeemData') !== null)
            {
                var gcRedeemData    = JSON.parse(sessionStorage.getItem('gcRedeemData'));
                var x               = [];
                var availableAmt    = 0;
                angular.forEach(gcRedeemData, function(value, key){
                    if(value.cardNumber == sessionStorage.getItem('gcRedeemNumber'))
                    {
                        availableAmt = value.redeemableValue;
                        if(parseFloat(value.redeemableValue) < parseFloat(tender.amount))
                        {
                            x.push(1);
                        }
                        else
                        {
                            x.push(0);
                        }
                    }
                });

                $q.all(x).then(function(res){
                    if(x.indexOf(1) !== -1)
                    {
                        NotificationService.addAlert('Insufficient credits in card. Current card balance is : ' + availableAmt , 'Gift Card Validation', 'static', false);
                        ModelService.get('Tender',{sid: tender.sid, document_sid: activeDocumentSid}).then(function(data){
                            var tenderRemove = data[0];
                            tenderRemove.remove().then(function(){
                                d.resolve();
                            });
                        });

                    }
                    else
                    {
                        var gcRedeemData = JSON.parse(sessionStorage.getItem('gcRedeemData'));
                        angular.forEach(gcRedeemData, function(value, key){
                            if(value.cardNumber == sessionStorage.getItem('gcRedeemNumber'))
                            {
                                gcRedeemData[key].amount = tender.amount;
                                gcRedeemData[key].tender_sid = tender.sid;
                            }

                            if(key == gcRedeemData.length - 1)
                            {
                                sessionStorage.setItem('gcRedeemData', JSON.stringify(gcRedeemData));
                            }
                        });
                        d.resolve();
                    }
                });
            }
        }
        else
        {
           d.resolve();
        }

        //return the deferred promise
        return d.promise;
    };

    var handlerAfterTenderRemove = function($q, tender){
        var d = $q.defer();

        if(tender.tender_type == 10)
        {
            if(sessionStorage.getItem('gcRedeemData') !== null)
            {
                var gcRedeemData = JSON.parse(sessionStorage.getItem('gcRedeemData'));
                if(gcRedeemData.length == 1)
                {
                    sessionStorage.removeItem('gcRedeemNumber');
                    sessionStorage.removeItem('gcRedeemData');
                }
                else if(gcRedeemData.length > 1)
                {
                    angular.forEach(gcRedeemData, function(value, key){
                        if(value.tender_sid == tender.sid)
                        {
                            gcRedeemData.splice(key, 1);
                            sessionStorage.removeItem('gcRedeemNumber');
                            sessionStorage.setItem('gcRedeemData', JSON.stringify(gcRedeemData));
                        }
                    });
                }
            }
        }
        d.resolve();
        //return the deferred promise
        return d.promise;
    };

    // ModelEvent.addListener('item', ['onBeforeInsert'], handlerBeforeItemInsert);
    ModelEvent.addListener('tender', ['onAfterInsert'], handlerAfterTenderInsert);
    ModelEvent.addListener('tender', ['onAfterRemove'], handlerAfterTenderRemove);

}];

ConfigurationManager.addHandler(giftCardCM);

// ButtonHooksManager.addHandler(['before_navPosTenderPrintUpdate', 'before_navPosTenderUpdateOnly'],
//     function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService,$rootScope,HookEvent, $stateParams, $http, $window) {
//         var deferred = $q.defer();
//         //retrieve the active document from the cache
        
//         if(sessionStorage.getItem('gcRedeemData') !== null)
//         {
//             var notvalid = [];
//             var gcRedeemData = JSON.parse(sessionStorage.getItem('gcRedeemData'));
//             angular.forEach(gcRedeemData, function(value, key){
//                 if(value.amount == 0 || value.tender_sid == '')
//                 {
//                     notvalid.push(1);
//                 }
//                 else
//                 {
//                     notvalid.push(0);
//                 }
//             });

//             $q.all(notvalid).then(function(){
//                 if(notvalid.indexOf(1) !== -1)
//                 {
//                     NotificationService.addAlert('Please ensure that all gift card tender has value.', 'Gift Card Validation').then(function(){
//                         deferred.reject();
//                     });
                    
//                 }
//                 else
//                 {
//                     deferred.resolve();
//                 }
//             });
//         }
//         else
//         {
//             deferred.resolve();
//         }

//         return deferred.promise;
//     }
// );

ButtonHooksManager.addHandler(['after_navPosTenderReturnTo'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService,$rootScope,HookEvent, $stateParams, $http, $window) {
        var deferred = $q.defer();
        //retrieve the active document from the cache
        
        if(sessionStorage.getItem('gcRedeemData') !== null)
        {
            var notvalid = [];
            var gcRedeemData = JSON.parse(sessionStorage.getItem('gcRedeemData'));
            angular.forEach(gcRedeemData, function(value, key){
                if(value.amount == 0 || value.tender_sid == '')
                {
                    gcRedeemData.splice(key, 1);
                    notvalid.push(1);
                }
            });

            $q.all(notvalid).then(function(){
                if(gcRedeemData.length == 0)
                {
                    sessionStorage.removeItem('gcRedeemNumber');
                    sessionStorage.removeItem('gcRedeemData');
                    deferred.resolve();
                }
                else
                {
                    sessionStorage.removeItem('gcRedeemNumber');
                    sessionStorage.setItem('gcRedeemData', JSON.stringify(gcRedeemData));
                    deferred.resolve();
                }

            });
        }
        else
        {
            deferred.resolve();
        }

        return deferred.promise;
    }
);



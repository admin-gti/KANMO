 var changeMultipleOrderType = ['$scope', '$modalInstance', 'ModelService','$stateParams','prismSessionInfo','$http','$window','$location','ResourceNotificationService','$state','$q','$modal','LoadingScreen',
    function ($scope, $modalInstance, ModelService, $stateParams, prismSessionInfo, $http, $window, $location, NotificationService, $state, o, $modal, LoadingScreen) {
        'use strict';

         var sess = $http.defaults.headers.common['Auth-Session'];
         var servername = $window.location.origin;
         var deferred =o.defer();
         $scope.fulfillmentType = [
            {name:"IMMEDIATE",value:"IMMEDIATE"},
            {name:"MY STORE",value:"SAME STORE"},
            {name:"NAMED STORE",value:"DIFFERENT STORE"}
        ];
        
        ModelService.get('Item',{document_sid:$stateParams.document_sid,cols:'*'})
        .then(function(res){
            $scope.viewItem = res;
            $scope.viewItem.select = $scope.fulfillmentType;
            
//            console.log($scope.viewItem);
        });
        
        
        
        $scope.handler = function(a,cat){
//            console.log($('#all_ordertype').val());
            if(cat == 1){
                if($('#all_ordertype').val() == ''){
                     $('#all_fulfillmentype').empty().append($('<option>',{value: 'IMMEDIATE',text : 'IMMEDIATE'}));
                } else {
                     $('#all_fulfillmentype').empty().append($('<option>',{value: 'SAME STORE',text : 'MY STORE'})).append($('<option>',{value: 'DIFFERENT STORE',text : 'NAMED STORE'}));
                }
            }
            
            if($scope.checkboxValue){
                var populate_allordertype = $('#all_ordertype').val();
                var populate_allfulfilltype = $('#all_fulfillmentype').val();
                
                $('select[name="item_note8"]').val(populate_allordertype);
                if($('#all_ordertype').val() == ''){
                    $('select[name="item_note10"]').empty().append($('<option>',{value: 'IMMEDIATE',text : 'IMMEDIATE'})).val(populate_allfulfilltype);
                } else {
                    $('select[name="item_note10"]').empty().append($('<option>',{value: 'SAME STORE',text : 'MY STORE'})).append($('<option>',{value: 'DIFFERENT STORE',text : 'NAMED STORE'})).val(populate_allfulfilltype);
                }
            }
        }
        
        
        $scope.selectOrderType = function(a){
            if(($("#item_note8_"+a).val()) != ''){
                $('#item_note10_'+a).empty().append($('<option>',{value: 'SAME STORE',text : 'MY STORE'})).append($('<option>',{value: 'DIFFERENT STORE',text : 'NAMED STORE'}));
            } else {
                $('#item_note10_'+a).empty().append($('<option>',{value: 'IMMEDIATE',text : 'IMMEDIATE'}));
            }
        }
        $scope.selectFulfillmentType = function(a, arr){
            $("#note10"+a).val(arr.value);
        }
        
        $scope.close = function(a){
           $modalInstance.dismiss();
           $state.go($state.current, {}, {reload: true});
        }
         
         $scope.update =function(){
            var promises = [];
            var multipleRequest = o.when();
            LoadingScreen.Enable = 1;
            var modalOptions;
            modalOptions = {
                backdrop: 'static',
                windowClass: 'full',
                size: 'lg',
                templateUrl: '/plugins/PLFulfillmentAndFreight/fulfillmentandfreight.htm',
                controller: 'fulfillmentandFreightCtrl'
            };
            
            localStorage.removeItem('freightamt');
            localStorage.removeItem('isfullfilllocation');
            localStorage.removeItem('deliverydiffstore');
            
            ModelService.get('Item',{document_sid:$stateParams.document_sid,cols:'*'})
            .then(function(res){
                $scope.updateItem = res;
                angular.forEach($scope.updateItem, function(value, key) {
                    multipleRequest = multipleRequest.then(function(){
                        var items = value;
                        var note8 = $("#item_note8_"+key).val();
                        var note10 = $("#item_note10_"+key).val();
//                        var note10 = $("#note10"+key).val();
                        var item_alu = $("#items_"+key).val();

                        if(note8 != '') {
                            if(note10 != ''){
                                var infoData = "[{\"note7\":\"POS\",";
                                infoData += "\"note8\":\"" + note8 + "\",";
                                infoData += "\"note6\":\"PREPAID\",";
                                infoData += "\"note10\":\"" + note10 + "\",";
                                infoData += "\"item_type\":\"3\",";
                                infoData += "\"order_type\":\"0\",";
                                infoData += "\"so_deposit_amt\":\"0\"";
                                infoData += "}]";
                                return $http.put(items.link+"?filter=row_version,eq,"+items.row_version,infoData,{headers:{"Auth-Session":sess}}).success(function(){
                                    promises.push(deferred.promise);
                                });
                            } 
                        } 
                    });
                });
            });
            
            o.all(promises).then(function(){
                NotificationService.showSuccessfulMessage( 'Sucess!', 'Order and fulfillment type successfully updated!');
                setTimeout(function(){
                $state.go($state.current, {}, { reload: true }).then(function(){
                    $.ajax({
                        url: "/plugins/PLOverallValidation/validation.php"
                        ,type: "GET"
                        ,data: {type:4,sid:$stateParams.document_sid}
                        ,success: function(val1){
                            if(val1.homedelivery > 0 || val1.diffstore > 0){
                                LoadingScreen.Enable = !1;
//                                $modalInstance.dismiss();
                                $modal.open(modalOptions);
                                deferred.resolve(); 
                            } else {
                                LoadingScreen.Enable = !1;
                                $modalInstance.dismiss();
                                deferred.resolve();
                            }
                               
                        }
                    });
                });
                }, 3000);
            });
            
           
        };

    }
];


window.angular.module('prismPluginsSample.controller.changeMultipleOrderTypeCtrl', [])
    .controller('changeMultipleOrderTypeCtrl', changeMultipleOrderType);
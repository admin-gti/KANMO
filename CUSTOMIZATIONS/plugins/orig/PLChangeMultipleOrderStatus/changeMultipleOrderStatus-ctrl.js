 var changeMultipleOrderType = ['$scope', '$modalInstance', 'ModelService','$stateParams','prismSessionInfo','$http','$window','$location','ResourceNotificationService','$state','$q',
    function ($scope, $modalInstance, ModelService, $stateParams, prismSessionInfo, $http, $window, $location, NotificationService, $state, o) {
        'use strict';

         var sess = $http.defaults.headers.common['Auth-Session'];
         var servername = $window.location.origin;
         var deferred =o.defer();

        ModelService.get('Item',{document_sid:$stateParams.document_sid,cols:'*'})
        .then(function(res){
            $scope.viewItem = res;
        });
        
        $scope.handler = function(a){
            if($scope.checkboxValue){
                
                var populate_allordertype = $('#all_ordertype').val();
                var populate_allfulfilltype = $('#all_fulfillmentype').val();

                $('select[name="item_note8"]').val(populate_allordertype);
                $('select[name="item_note10"]').val(populate_allfulfilltype);
                
            }
        }
        
        $scope.close = function(a){
           $modalInstance.dismiss();
           $state.go($state.current, {}, {reload: true});
        }

//        $scope.getDataOrder =function(index){
//
//            var itemLink = $scope.viewItem[index].link;
//            var row      = $scope.viewItem[index].row_version;
//            var note8    = $scope.viewItem[index].note8;
//            var note10   = $scope.viewItem[index].note10;
//             
//            var itemUpdate = servername+itemLink+"?filter=row_version,eq,"+row;
//            
//            var infoData = "[{\"note7\":\"POS\",";
//                infoData += "\"note8\":\"" + note8 + "\",";
//                infoData += "\"note9\":\"PREPAID\",";
//                infoData += "\"note10\":\"" + note10 + "\",";
//                infoData += "\"item_type\":\"3\",";
//                infoData += "\"order_type\":\"0\",";
//                infoData += "\"so_deposit_amt\":\"0\"";
//                infoData += "}]";
//
//            $http.put(itemUpdate,infoData,{headers:{"Auth-Session":sess}})
//            .success(function(){
//                
//                NotificationService.showSuccessfulMessage( 'Updated!', 'Order type for ' + $scope.viewItem[index].alu + ' has been updated');
//
//            });
//
//         }
         
         $scope.update =function(){
//            console.log($scope.viewItem.length);
            var multipleRequest = o.when();
            
            var i = 0;
            var j = 0;
            console.log($scope.viewItem.length);
            
            angular.forEach($scope.viewItem, function(value, key) {
//                console.log(value);
                
                multipleRequest = multipleRequest.then(function(){
                    
                    var items = value;

                    var note8 = $("#item_note8_"+key).val();
                    var note10 = $("#item_note10_"+key).val();
                    var item_alu = $("#items_"+key).val();

//                    console.log(key);
//                    console.log(note10);
//                    console.log(item_alu);
                    
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
                            j = j + 1;
                            return $http.put(items.link+"?filter=row_version,eq,"+items.row_version,infoData,{headers:{"Auth-Session":sess}}).success(function(){

                                NotificationService.showSuccessfulMessage('SUCCESS!', 'Order and fulfillment type successfully updated!');
                            });


                        } else if(note10 == ''){

                                return NotificationService.showWarning( 'WARNING', 'Fulfillment Type for alu '+item_alu+' is blank');

                        }
                    
                    } else if(note8 == ''){

                        if(note10 != ''){

                                return NotificationService.showWarning( 'WARNING', 'Order Type for alu '+item_alu+' is blank');

                        }

                    }
                    
                    i = i + 1;
                    
                });
                
                
            });
            console.log(multipleRequest);
//            return false;
//            if(j > 0){
//                NotificationService.showSuccessfulMessage( 'Sucess!', 'Order and fulfillment type successfully updated!');
//            }
            deferred.resolve();
            
        };
         /*------------------------------------------------------------------------*/
        deferred.resolve();
        return deferred.promise;

    }
];


window.angular.module('prismPluginsSample.controller.changeMultipleOrderTypeCtrl', [])
    .controller('changeMultipleOrderTypeCtrl', changeMultipleOrderType);
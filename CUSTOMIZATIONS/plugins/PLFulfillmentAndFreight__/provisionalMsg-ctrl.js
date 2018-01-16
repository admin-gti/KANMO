 var provisionalMsg = ['$scope', '$modalInstance', 'ModelService','$stateParams','prismSessionInfo','$http','$window','$location','ResourceNotificationService','$state','$q','$modal','LoadingScreen',
    function ($scope, $modalInstance, ModelService, $stateParams, prismSessionInfo, $http, $window, $location, NotificationService, $state, o, $modal, LoadingScreen) {
        'use strict';

        var sess = $http.defaults.headers.common['Auth-Session'];
        var servername = $window.location.origin;
        var deferred =o.defer();
         
        $http.get("/plugins/PLFulfillmentAndFreight/getGroupProvisional.php?sid="+$stateParams.document_sid).then(function(res, status) { 
            $scope.prov = res.data;
            console.log(res.data);
            deferred.resolve();
        }); 
        
        $scope.handler = function(a){
            if($scope.checkboxValue){
                console.log($('#applyAllMessage').val());
                var populate_allmessage = $('#applyAllMessage').val();
                $('textarea[name="applyprovMessage"]').text(populate_allmessage);
//                $('textarea#applyprovMessage').val(populate_allmessage);
            }
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
            
            angular.forEach($scope.viewItem, function(value, key) {
                multipleRequest = multipleRequest.then(function(){
                    var items = value;
                    var note8 = $("#item_note8_"+key).val();
                    var note10 = $("#item_note10_"+key).val();
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
                                $modal.open(modalOptions);
                                deferred.resolve(); 
                            } else {
                                LoadingScreen.Enable = !1;
                                deferred.resolve();
                            }
                               
                        }
                    });
                });
                }, 1000);
            });
            
           
        };

    }
];


window.angular.module('prismPluginsSample.controller.provisionalMsgCtrl', [])
    .controller('provisionalMsgCtrl', provisionalMsg);
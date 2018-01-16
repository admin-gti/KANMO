 var provisionalMsg = ['$scope', '$modalInstance', 'ModelService','$stateParams','prismSessionInfo','$http','$window','$location','ResourceNotificationService','$state','$q','$modal','LoadingScreen',
    function ($scope, $modalInstance, ModelService, $stateParams, prismSessionInfo, $http, $window, $location, NotificationService, $state, o, $modal, LoadingScreen) {
        'use strict';

        var sess = $http.defaults.headers.common['Auth-Session'];
        var servername = $window.location.origin;
        var deferred =o.defer();
         
        $http.get("/plugins/PLFulfillmentAndFreight/getGroupProvisional.php?sid="+$stateParams.document_sid).then(function(res, status) { 
            $scope.prov = res.data;
            deferred.resolve();
        }); 
        
        $scope.handler = function(a){
            if($scope.checkboxValue){
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
           
           angular.forEach($scope.prov, function(val, key) {
               
            angular.forEach(val.items, function(vals, keys) {
//                console.log(vals);
                multipleRequest = multipleRequest.then(function(){
                    var items = vals;
                    var provM = $("#applyprovMessage"+key).val();
                    console.log(provM);
                    if(provM != '') {
                        var infoData = "[{";
                        infoData += "\"note2\":\"" + provM + "\"";
                        infoData += "}]";
                        return $http.put("/v1/rest/document/"+vals.doc_sid+"/item/"+vals.sid+"?filter=row_version,eq,"+vals.row_version,infoData,{headers:{"Auth-Session":sess}}).success(function(){
                            promises.push(deferred.promise);
                        });
                    } 
                });
               });
           });

           o.all(promises).then(function(){
               NotificationService.showSuccessfulMessage( 'Success!', 'Provision message/s successfully added!');
               setTimeout(function(){
               $state.go($state.current, {}, { reload: true }).then(function(){
                   LoadingScreen.Enable = !1;
                   deferred.resolve();
                   $modalInstance.dismiss();
               });
               }, 1000);
           });


        };

    }
];


window.angular.module('prismPluginsSample.controller.provisionalMsgCtrl', [])
    .controller('provisionalMsgCtrl', provisionalMsg);
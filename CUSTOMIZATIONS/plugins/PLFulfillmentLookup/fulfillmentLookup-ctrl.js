var fulfillmentLookup = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "LoadingScreen",
function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window,i) {
    
    var deferred = o.defer();
    var nDate = new Date();
    a.tDate = $filter('date')(nDate,'MM/dd/yyyy');
    a.Date = $filter('date')(nDate.setDate(nDate.getDate()-7),'MM/dd/yyyy');

    a.transType = [
      {name:"All",filter:""},
      {name:"Sale",filter:"(has_sale,eq,true)"},
      {name:"Orders",filter:"(order_document_number,nn)"}
    ];
    
    a.dateType = [
      {name:"Created Date",value:"invoice_posted_date"},
      {name:"Ordered Date",value:"ordered_date"},
      {name:"Ship Date",value:"ship_date"},
      {name:"Cancel Date",value:"cancel_date"}
    ];
//    dateList = a.dateType[0].name;
//    console.log(a.dateList.selectedOption);

    ms.get('Store',{cols:'sid,store_name,store_code',filter:'(subsidiary_sid,eq,' + c.get().subsidiarysid + ')AND(active,eq,true)&sort=store_code,asc'})
    .then(function(s){
      a.stores = s;
    });

    ms.get('Workstation',{cols:'sid,workstation_name,',filter:'(subsidiary_sid,eq,' + c.get().subsidiarysid + ')(store_sid,eq,' + c.get().storesid + ')AND(active,eq,true)&sort=workstation_name,asc'})
    .then(function(w){
      a.workstations = w;
    });
    
    a.store_uid = c.get().storesid;
    
    a.loadWorkstations = function() {
        var n;
        n = "" !== a.store_uid && null !== a.store_uid ? "(store_sid,eq," +a.store_uid + ")AND(active,eq,true)" : "(subsidiary_sid,eq," + c.get().subsidiarysid + ")AND(active,eq,true)", $http.get("Workstation", {
            cols: "sid,workstation_name",
            filter: n,
            sort: "workstation_name,asc"
        }).then(function(m) {
            a.workstationList = u.responseParser(m);
        })
    }
    
    a.closeModal = function(){
       e.dismiss();
    }

    a.previewBtn = function(){
        
        var deferred = o.defer();
        i.Enable = 1;
        
        var from_date = $filter('date')(new Date(a.search.fromDate),'yyyy-MM-dd');
        var dbDate = new Date(a.search.toDate);
        var to_date = $filter('date')(dbDate.setDate(dbDate.getDate()+1),'yyyy-MM-dd');
        
        var sFilter = "";

//        sFilter += "(document_number,NN)";
        if(a.search.notes_order){
            sFilter += "notes_general=" + a.search.notes_order;
        }
        if(!a.search.notes_order){
            sFilter += "category="+$("#dateList").val();
        }
        if(!a.search.notes_order){
            sFilter += "&datefrom=" + from_date;
            sFilter += "&dateto=" + to_date;
        }
        
        if(a.search.storeList){
            sFilter += "&store_uid=" + a.search.storeList.sid;
        }
        if(a.search.workstationList){
            sFilter += "&workstation_uid=" + a.search.workstationList.workstation_name;
        }
        if(a.search.notes_general){
            sFilter += "&notes_general=" + a.search.notes_general;
        }
        if(a.search.so_order){
            sFilter += "&order_doc_no=" + a.search.so_order;
        }
        if(a.search.last_name){
            sFilter += "&bt_last_name=" + a.search.last_name;
        }
//        sFilter += "&((status=4)OR(status=3))";
//        console.log(sFilter);
           
           i.Enable = 1;
        a.documents = '';
        b.get("/plugins/PLFulfillmentLookup/fulfillmentLookup.php?"+sFilter+"&type=1").then(function(data, status) {
//                a.shipGroup = data.data;    
//                            console.log(a.shipGroup);
            a.documents = data.data;
            i.Enable = !1;     
            deferred.resolve();
        });
        deferred.resolve();
    }
    
    a.viewTransaction = function(a){
//        console.log(a);
        b.get('v1/rest/document/'+localStorage.getItem('lookupSID')+'?cols=*',{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
            
            $.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:6,sid:localStorage.getItem('lookupSID')}
                ,success: function(val1){
                    
                    if(val1.payatstore > 0) {
                    
                        var doc = res.data[0];
                        var infoData = "[{\"order_changed_flag\":\"0\"";
//                            infoData += "\"order_changed_flag\":\"0\"";
                            infoData += "}]";

                        b.put(doc.link+"?filter=row_version,eq,"+doc.row_version,infoData,{headers:{"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
                            $.ajax({
                                url: "/plugins/PLWaitingforPickup/totalDep.php"
                                ,type: "GET"
                                ,data: {sid:a}
                                ,success: function(){
                                    window.location.href = "/prism.shtml#/register/pos/docs/"+a+"#searchTransactionItemsResult0";
                                    e.dismiss();
                                }
                            });
                            e.dismiss();
                            deferred.resolve();
                            return deferred.promise;

                        });
                    } else {
                        $.ajax({
                            url: "/plugins/PLWaitingforPickup/totalDep.php"
                            ,type: "GET"
                            ,data: {sid:a}
                            ,success: function(){
                                window.location.href = "/prism.shtml#/register/pos/docs/"+a+"#searchTransactionItemsResult0";
                                e.dismiss();
                            }
                        });
                        e.dismiss();
                        deferred.resolve();
                        return deferred.promise;
                    }
                }
            });
            
            
        });
        
    }
    
    a.selectTransaction = function(za){
        localStorage.removeItem('lookupSID');
        localStorage.setItem('lookupSID', za); //
    }
    
    a.viewModal = function() {
        b.get('v1/rest/document/'+localStorage.getItem('lookupSID')+'?cols=*',{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
            
            $.ajax({
                url: "/plugins/PLOverallValidation/validation.php"
                ,type: "GET"
                ,data: {type:6,sid:localStorage.getItem('lookupSID')}
                ,success: function(val1){
                    
                    if(val1.payatstore > 0) {
                    
                        var doc = res.data[0];
                        var infoData = "[{\"order_changed_flag\":\"0\"";
//                            infoData += "\"order_changed_flag\":\"0\"";
                            infoData += "}]";

                        b.put(doc.link+"?filter=row_version,eq,"+doc.row_version,infoData,{headers:{"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){
                            window.location.href = "/prism.shtml#/register/pos/docs/"+localStorage.getItem('lookupSID')+"#searchTransactionItemsResult0";
                            e.dismiss();
                            deferred.resolve();
                            return deferred.promise;

                        });
                    } else {
                        window.location.href = "/prism.shtml#/register/pos/docs/"+localStorage.getItem('lookupSID')+"#searchTransactionItemsResult0";
                        e.dismiss();
                        deferred.resolve();
                        return deferred.promise;
                    }
                }
            });
            
            
        });
        
    }
    


}];

window.angular.module('prismPluginsSample.controller.fulfillmentLookupCtrl', [])
   .controller('fulfillmentLookupCtrl', fulfillmentLookup);
   


var fulfillmentandFreightCtrl = ["$scope", "$http", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","ModelService","$filter","$window", "$stateParams","LoadingScreen","ResourceNotificationService","$modalInstance",
    function(a, b, c, d, e, f, k, m, n, o, ps, pu, ms, $filter,$window, $stateParams, i, NotificationService,$modalInstance) {
    var deferred =o.defer();
//    console.log(c.get());
    var nDate = new Date();
    a.tDate = $filter('date')(nDate,'MM/dd/yyyy');
    a.Date = $filter('date')(nDate.setDate(nDate.getDate()+7),'MM/dd/yyyy');
    
    /*----------------------------MAIN----------------------------------------*/
    //GENERATE SIGNATURE
    var vURL               = MARTJACK.URL;
    var urlAPI             = MARTJACK.FULFILLMENT;
    var merchantID         = MARTJACK.MERCHANTID;
    var consumer_key       = MARTJACK.CONSUMERKEY;
    var consumer_secretkey = MARTJACK.CONSUMER_SECRETKEY;
    
    var auth_nonce         = Math.random().toString(36).replace(/[^a-z]/, '').substr(2);
    var auth_timestamp     = Math.round((new Date()).getTime() / 1000.0);
    /*------------------------------------------------------------------------*/
    /*---FULFILLMENT LOCATIONS RETRIEVING RESULTS-----------------------------*/
    var httpMethod = 'POST',
        url = vURL+urlAPI+merchantID,
        parameters = {
        oauth_consumer_key : consumer_key,
        oauth_nonce : auth_nonce,
        oauth_timestamp : auth_timestamp,
        oauth_signature_method : 'HMAC-SHA1',
        oauth_version : '1.0'
    },
    consumerSecret = consumer_secretkey,
    encodedSignature = oauthSignature.generate(httpMethod, url, parameters, consumerSecret, '');
    //*---END-----------------------------------------------------------------*/
    
    i.Enable = 1;
    $.ajax({
        url: "/plugins/PLOverallValidation/validation.php"
        ,type: "GET"
        ,data: {type:4,sid:$stateParams.document_sid}
        ,success: function(val1){
//                console.log(val1);
            if(val1.homedelivery > 0 || val1.diffstore > 0){
                if(val1.homedelivery > 0){
                    $("#tbl_freight").fadeIn('fast');
                } else {
                    $("#tbl_freight").fadeOut('fast');
                }
                if(val1.diffstore > 0){
                    if(val1.homedeliverydiff > 0 && val1.pickupdiff <= 0){
                        $("#tbl_fulfillment").fadeOut('fast');
                    } else if(val1.homedeliverydiff > 0 && val1.pickupdiff > 0){
                        $("#tbl_fulfillment").fadeIn('fast');
                    } else if(val1.homedeliverydiff <= 0 && val1.pickupdiff > 0){
                        $("#tbl_fulfillment").fadeIn('fast');
                    }else {
                        $("#tbl_fulfillment").fadeIn('fast');
                    }
                } 
                a.samestore = val1.homesame;
                a.homedeliverydiff = val1.homedeliverydiff;
                a.type = val1;
                $.ajax({
                    url: "/plugins/PLFulfillmentAndFreight/fulfillmentLocation.php"
                    ,type: "GET"
                    ,data: {docsid:$stateParams.document_sid,su:c.get().subsidiarysid,type:1}
                    ,success: function(msg){
                        a.quantities = msg;
                        

                        /*---FOR TESTING PURPOSE ONLY-------------------------------------*/
            //            var infoData = "MerchantID="+MARTJACK.MERCHANTID+"&InputFormat=application/json&InputData={";
            //                infoData += "\"Latitude\":\"\",";
            //                infoData += "\"Longitude\":\"\",";
            //                infoData += "\"Products\":";
            //                infoData += "[";
            //                infoData += "{";
            //                infoData += "\"ProductId\":\"12341734\",";
            //                infoData += "\"VariantProductId\":\"0\",";
            //                infoData += "\"Quantity\":\"5\"";
            //                infoData += "}";
            //                infoData += "]";
            //                infoData += "}";
                        /*----------------------------------------------------------------*/    
                        var infoData = "MerchantID="+MARTJACK.MERCHANTID+"&InputFormat=application/json&InputData=";
                            infoData += a.quantities;

                        var reqfulfill = {
                            method: 'POST',
                            url: url+"?oauth_consumer_key="+consumer_key+"&oauth_nonce="+auth_nonce+"&oauth_signature="+encodedSignature+"&oauth_signature_method=HMAC-SHA1&oauth_timestamp="+auth_timestamp+"&oauth_version=1.0",
                            headers: {
                              'Content-Type': 'application/x-www-form-urlencoded',
                              "accept": "application/json"
                            },
                            data: infoData
                        }
                        
                        b(reqfulfill).then(function(g){

                            $("[name=fullfill_location]").removeAttr("checked");
                            if (typeof g.data.Locations == "undefined") {
                                a.NoStore = g.data.Message;
                            } else {
                                a.fulfillItem = g.data.Locations;
                            }
//                            console.log(g.data.Locations);
                            ms.get('Store',{cols:'sid,store_code',filter:'(subsidiary_sid,eq,' + c.get().subsidiarysid + ')AND(sid,eq,'+c.get().storesid+')AND(active,eq,true)&sort=store_code,asc'}).then(function(sto){
//                              console.log(sto[0].store_code);
                              a.currentStore = sto[0].store_code;
//                              a.currentStore = 'PIJ';
                            });
                            
//                            console.log(c.get());
                            i.Enable = !1;
                            deferred.resolve();
                        }, function(error) {
                            NotificationService.showError( 'Error!', 'Connection timeout to Martjack');
                            i.Enable = !1;
                            e.dismiss('cancel');
                            m.go(m.current, {}, {reload: true});
                        });
                    }
                });
                /*---END------------------------------------------------------------------*/

                /*---FREIGHT CHARGES------------------------------------------------------*/
                i.Enable = 1;
                $.ajax({
                    url: "/plugins/PLFulfillmentAndFreight/viewInventory.php"
                    ,type: "GET"
                    ,data: {type:1,sid:$stateParams.document_sid,sto:localStorage.getItem('locationID')}
                    ,success: function(rest){
                        a.inventory = rest;
                        deferred.resolve();

                    }
                });
                
                $.ajax({
                    url: "/plugins/PLFulfillmentAndFreight/fulfillmentLocation.php"
                    ,type: "GET"
                    ,data: {type:3,sid:$stateParams.document_sid}
                    ,success: function(rest){
                        a.customer_address = rest;
                        deferred.resolve();
                    }
                });
                
                $.ajax({
                    url: "/plugins/PLFulfillmentAndFreight/fulfillmentLocation.php"
                    ,type: "GET"
                    ,data: {type:4,sid:$stateParams.document_sid}
                    ,success: function(rest){
                        a.regions = rest;
                        deferred.resolve();
                    }
                });
                
                i.Enable = 1,$.ajax({
                    url: "/plugins/PLFulfillmentAndFreight/fulfillmentLocation.php"
                    ,type: "GET"
                    ,data: {an:auth_nonce,at:auth_timestamp,s:encodedSignature,type:7,sid:$stateParams.document_sid}
                    ,success: function(rest){
                        if (rest.Message == "No Common Location Found" || rest.messageCode == '1000') {
                            a.NoStoreHomeDelivery = "NOTE: NO STOCK AT ALL STORES";
                            a.HomeDeliveryItem = "";
                        } else {
                            a.NoStoreHomeDelivery = "";
                            a.HomeDeliveryItem = rest.Locations;
                        }
//                        ms.get('Store',{cols:'sid,store_code',filter:'(subsidiary_sid,eq,' + c.get().subsidiarysid + ')AND(sid,eq,'+c.get().storesid+')AND(active,eq,true)&sort=store_code,asc'}).then(function(sto){
//                            console.log(sto[0].store_code);
//                            a.currentStore = sto[0].store_code;
//                        });
                        
                        deferred.resolve();
                    }
                });
                
                var ShipmentRates = function(){

                    b.get("/plugins/PLFulfillmentAndFreight/getGroup.php?sid="+$stateParams.document_sid+"&ssid="+c.get().storesid).then(function(data, status) {
                            console.log(data.data);
                            a.shipGroup = data.data;    
                        i.Enable = !1;     
                        deferred.resolve();
                    });

                    return deferred.promise;
                };

                ShipmentRates().then(function(){
                    i.Enable = !1; 
                    deferred.resolve();
                });
                
                /*---END------------------------------------------------------------------*/
                deferred.resolve();
            } else {
                deferred.resolve();
            }
        }
    });
    
    
    
    a.update = function(){
//        console.log(a.shipGroup);  
        var fulfill = $('input[name=fullfill_location]:checked').val();
        console.log(parseInt(fulfill));
        var multipleRequest = o.when();
        var fulfillmentLocation = "";
        var freightMessage = "";
        var message = "";
        
        if(!isNaN(fulfill)){
        
            b.get('v1/rest/document/'+$stateParams.document_sid+'?cols=*',
                {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(res){

                    for (var i = 0; i < res.data[0].items.length; i++) {

                        b.get(res.data[0].items[i]['link']+'?cols=*',
                            {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(resp){

                                b.get('v1/rest/store?cols=*&filter=udf1_string,eq,'+fulfill,
                                    {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(st){

                                        multipleRequest = multipleRequest.then(function(){

                                            var store = "[{";
                                                store += "\"fulfill_store_sid\":\""+st.data[0].sid+"\",";
                                                store += "\"fulfill_store_no\":\""+st.data[0].store_number+"\",";
                                                store += "\"fulfill_store_sbs_no\":\""+st.data[0].subsidiary_number+"\"";
                                                store += "}]";
//                                            console.log(resp.data[0].note8);    
                                            if(resp.data[0].note10 == 'DIFFERENT STORE'){

                                                return b.put(resp.data[0].link+"?filter=row_version,eq,"+resp.data[0].row_version,store,{headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).success(function(){
                                                    deferred.resolve();
                                                });
                                            }

                                        });

                                });

                        });

                    }

            });
            
            fulfillmentLocation = "Fulfillment location successfully added";
        
        }
//        console.log(a.shipGroup);
        if(typeof a.shipGroup !== undefined) {
//            console.log(a.shipGroup);
            multipleRequest = o.when();
            angular.forEach(a.shipGroup,function(fr,keys){
                
//                console.log(a.shipGroup);
                
                angular.forEach(fr.items,function(fre, key){
                    
                    var freighttotal = parseInt($("#freight_total"+keys).val())/parseInt(fr.items.length);
                    multipleRequest = multipleRequest.then(function(){
                        
                        var infoData = "[{";
                            infoData += "\"quantity\":\"" + fre.qty + "\",";
                            if($("#customer_city"+keys).val() != ''){
                                infoData += "\"st_address_line1\":\"" + $("#address1"+keys).val() + "\",";
                                infoData += "\"st_address_line2\":\"" + $("#address2"+keys).val() + "\",";
                                infoData += "\"st_address_line3\":\"" + $("#address3"+keys).val() + "\",";
                                infoData += "\"st_address_line4\":\"" + $("#address4"+keys).val() + "\",";
                                infoData += "\"st_address_line5\":\"" + $("#address5"+keys).val() + "\",";
                                infoData += "\"st_address_line6\":\"" + $("#address6"+keys).val() + "\",";
                                infoData += "\"st_postal_code\":\"" + $("#postal_code"+keys).val() + "\",";
                                infoData += "\"st_country\":\"" + $("#country"+keys).val() + "\",";
                            } else {
                                infoData += "\"st_address_line1\":\"" + $("#custom_address1"+keys).val() + "\",";
                                infoData += "\"st_address_line2\":\"" + $("#custom_address2"+keys).val() + "\",";
                                infoData += "\"st_address_line3\":\"" + $("#custom_city_"+keys).val() + "\",";
                                infoData += "\"st_address_line4\":\"" + $("#custom_region_"+keys).val() + "\",";
                                infoData += "\"st_address_line5\":\"" + $("#custom_district_"+keys).val() + "\",";
                                infoData += "\"st_address_line6\":\"" + $("#custom_address6"+keys).val() + "\",";
                                infoData += "\"st_postal_code\":\"" + $("#custom_zipcode"+keys).val() + "\",";
                                infoData += "\"st_country\":\"" + $("#custom_country_"+keys).val() + "\",";
                            }
                            infoData += "\"shipping_amt\":\"" + freighttotal + "\"";
                            infoData += "\"lty_pgm_name\":\"" + $("#ETADate"+keys).val() + "\"";
//                            infoData += "\"shipping_amt\":\"" + fre.freight + "\"";
                            infoData += "}]";
                            
                        var rowv = parseInt(fre.row_version);

                        return b.put('/v1/rest/document/'+fre.doc_sid+'/item/'+fre.sid+'?filter=row_version,eq,'+rowv,infoData, {headers: {"Auth-Session": sessionStorage.getItem("PRISMAUTH")}}).then(function(transRes){
                            deferred.resolve();
                        });

                    });

                });

            });
//            return false;
            var freightMessage = "Freight Charges Added";
        }
//        return false;
        if(fulfillmentLocation != ""){
            message = fulfillmentLocation;
        }
        if(freightMessage != ""){
            message += " and "+freightMessage;
        }
        
        NotificationService.showSuccessfulMessage( 'Updated!', message);
        deferred.resolve();
//        e.dismiss();
//        m.go(m.current, {}, {reload: true});
    }
    
    a.close = function(a){
        $modalInstance.dismiss();
        m.go(m.current, {}, {reload: true});
    }
    
    a.viewInventory = function(a, b){
        var modalOptions = {
            backdrop: 'static',
            keyboard: false,
            size: 'lg',
            templateUrl: '/plugins/PLFulfillmentAndFreight/viewInventory.htm',
            controller: 'viewInventoryCtrl'
        };
        
        localStorage.removeItem('locationID');
        localStorage.removeItem('StoreName');
        
        localStorage.setItem('locationID', a);
        localStorage.setItem('StoreName', b);
        f.open(modalOptions);
    }
    
    a.SelectCity = function(index,custype,arr){
        console.log(index);
        $("#other_shipping").fadeIn('fast');
        $("#freight_total"+arr).val('');
//        i.Enable = 1;
        if(custype == 1){
            $("#address1"+arr).val(index.address_1);
            $("#address2"+arr).val(index.address_2);
            $("#address3"+arr).val(index.address_3);
            $("#address4"+arr).val(index.address_4);
            $("#address5"+arr).val(index.address_5);
            $("#address6"+arr).val(index.address_6);
            $("#customer_city"+arr).val(index.city);
            $("#country"+arr).val(index.country);
            $("#postal_code"+arr).val(index.postal_code);
            $("#state"+arr).val(index.state);
        } else { 
            $.ajax({
                url: "/plugins/PLFulfillmentAndFreight/fulfillmentLocation.php"
                ,type: "GET"
                ,data: {type:6,reg:index.city}
                ,success: function(rest){
                    a.districts = rest;
                    deferred.resolve();
//                    i.Enable = !1;
                }
            });
            $("#custom_city_"+arr).val(index.city);
        }
       
        var selectedCity = index.city;
        
        
        
        var ShipmentRates = function(){
            
            b.get("/plugins/PLFulfillmentAndFreight/getGroup.php?sid="+$stateParams.document_sid+"&ssid="+c.get().storesid+"&selc="+selectedCity).then(function(res, status) { 
                
                a.ShippingRates = res.data[0].freight.value.shippingRates;
                
//                i.Enable = !1;     
                deferred.resolve();
            });

            return deferred.promise;
        };

        ShipmentRates().then(function(){
//            i.Enable = !1; 
            deferred.resolve();
        });
        
    }
    
    a.SelectShippingOption = function(index,rec){
        
        var days = index.etd;
        var d = new Date(Date.now() + days*24*60*60*1000);
        var curr_date = d.getDate();
        var curr_month = (d.getMonth())+1;
        var curr_year = d.getFullYear();
        curr_year = curr_year.toString();

//        document.write(curr_date+"-"+curr_month+"-"+curr_year);
        
        console.log(curr_date+"-"+curr_month+"-"+curr_year); //val( Date.parse(AddDate).toString('yyyy-MM-dd'))
        
        $("#freight_total"+rec).val(index.shippingRate);
        
        if(index.shippingType != "NAME DAY"){
            $("#ETADate"+rec).val(curr_date+"/"+curr_month+"/"+curr_year); 
        } else {
            $("#ETADate"+rec).val(''); 
        }
        
        b.get("/plugins/PLFulfillmentAndFreight/getGroup.php?sid="+$stateParams.document_sid+"&ssid="+c.get().storesid+"&rate="+index.shippingRate).then(function(res, status) { 
            
            console.log(res.data[0].freight.value);
            a.SelectedShippingRates = res.data[0].freight.value.shippingRates;
            //            a.Date = $filter('date')(nDate.setDate(nDate.getDate()+7),'MM/dd/yyyy');
            
            i.Enable = !1;     
            deferred.resolve();
        });
    }
    
    a.SelectAddress = function(ret){
        if(ret == 1){
            $("#defaultadd").fadeIn('fast');
            $("#customadd").fadeOut('fast');
        }else if(ret == 2){
            $("#defaultadd").fadeOut('fast');
            $("#customadd").fadeIn('fast');
        }
        $("#other_shipping").fadeOut('fast');
    }
    
    a.SelectDistrict = function(ret,arr){
//        a.SelectedDistrict = ret.district;
        $("#custom_district_"+arr).val(ret.district);
    }
    
    a.SelectRegion = function(get, arr){
        $.ajax({
            url: "/plugins/PLFulfillmentAndFreight/fulfillmentLocation.php"
            ,type: "GET"
            ,data: {type:5,reg:get.region}
            ,success: function(rest){
                a.cities = rest;
                a.selectedRegion = get.region;
                deferred.resolve();
            }
        });
        $("#custom_region_"+arr).val(get.region);
    }
    
    /*------------------------------------------------------------------------*/
    return deferred.promise;
    
}];

window.angular.module('prismPluginsSample.controller.fulfillmentandFreightCtrl', [])
   .controller('fulfillmentandFreightCtrl', fulfillmentandFreightCtrl);

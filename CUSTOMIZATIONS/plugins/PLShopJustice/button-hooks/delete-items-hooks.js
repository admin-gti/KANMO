ButtonHooksManager.addHandler(['after_navPosTenderPrintUpdate', 'after_navPosTenderUpdateOnly'],
    function($q, DocumentPersistedData, NotificationService, $modal, Templates, ModelService,$rootScope,HookEvent, $stateParams, $http, $window) {
        var deferred = $q.defer();
        //retrieve the active document from the cache

        var sess = $http.defaults.headers.common['Auth-Session'];
        var servername = $window.location.origin;
        var root_url = "/plugins/PLShopJustice/transactions_request.php?action=";

        sessionStorage.removeItem('cartItemsALU');
        sessionStorage.removeItem('wishListItemsALU');
        sessionStorage.removeItem('giftRegistryItemsALU');

        // Method use to check existing items in Prism
        ModelService.get('Item',{document_sid:$stateParams.document_sid,cols:'*'})
        .then(function(result){
            var item_type = [];
            angular.forEach(result, function(value, key){
                item_type.push(value.item_type);
                switch(value.note1) {
                    case "CART":
                        $http.get(servername + root_url + "deleteCartItem&id="+ value.note2 +"&item_id="+ value.note3, {headers:{"Auth-Session": sess}})
                        .then(function(response){

                        });
                        break;
                    case "WISHLIST":
                        var qty = (parseInt(value.quantity) > parseInt(value.note4)) ? value.note4 : value.quantity;
                        $http.get(servername + root_url + "deleteWishListItem&id=" + value.note2 + "&item_id=" + value.note3 + "&item_qty=" + qty, {headers:{"Auth-Session": sess}})
                        .then(function(response){

                        });
                        break;
                    case "GIFTREGISTRY":
                        var qty = (parseInt(value.quantity) > parseInt(value.note4)) ? value.note4 : value.quantity;
                        $http.get(servername + root_url + "deleteGiftRegistryItem&id=" + value.note2 + "&item_id=" + value.note3 +"&item_qty=" + qty, {headers:{"Auth-Session": sess}})
                        .then(function(response){

                        });
                }
            });

            ModelService.get('Document', {sid: $stateParams.document_sid}).then(function(doc_result){
                if(doc_result[0].status == 4)
                {
                    ModelService.get('Item', {document_sid: $stateParams.document_sid}).then(function(items){
                        var chainRequest = $q.when();

                        var bill_no     = (doc_result[0].order_document_number != '') ? doc_result[0].order_document_number : doc_result[0].document_number;
                        var user_id     = '';
                        if(sessionStorage.getItem('activeCustomer') != null)
                        {
                            var activeCustomer = JSON.parse(sessionStorage.getItem('activeCustomer'));
                            user_id = activeCustomer.user_id;
                        }
                        else
                        {
                            user_id = doc_result[0].cashier_id;
                        }

                        angular.forEach(items, function(value, key){
                            
                            if(value.dcs_code == '888800101' && value.note5 != '')
                            {
                                chainRequest = chainRequest.then(function(){
                                    var date        = new Date();
                                    var dateToday   = date.toISOString().substring(0, 10);
                                    var timeToday   = date.toLocaleTimeString().substring(0, 8);
                                    var DateTimeToday = dateToday + ' ' + timeToday;
                                    var gc_data = '<?xml version="1.0" encoding="ISO-8859-1"?>';
                                        gc_data += '<root>';
                                        gc_data += '<gift_card>';
                                        gc_data += '<card_no>' + value.note5 + '</card_no>';
                                        gc_data += '<encoded_card_no>' + value.note5 + '</encoded_card_no>';
                                        gc_data += '<amount>' + value.original_price * value.quantity + '</amount>';
                                        gc_data += '<recharged_on>' + DateTimeToday + '</recharged_on>';
                                        gc_data += '<recharged_by>' + user_id + '</recharged_by>';
                                        gc_data += '<bill_no>' + bill_no + '</bill_no>';
                                        gc_data += '</gift_card>';
                                        gc_data += '</root>';

                                        var params = {action: 'rechargeGC', data: gc_data};
                                        return $http.post(servername + '/plugins/PLGiftCard/giftCard.php', params).then(function(res){
                                        
                                        });
                                });
                            }
                        });

                    });

                    var transactionData = [];
                    
                    if(doc_result[0].bt_cuid != '' && doc_result[0].bt_cuid != null)
                    {
                        // doc_result[0].receipt_type == 0 && doc_result[0].return_qty == 0
                        if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) !== -1 && item_type.indexOf(2) === -1 && item_type.indexOf(3) === -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {
                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLRegularLoyalty', transData: transactionData}).then(function(result){
                                            deferred.resolve();
                                        });
                                    });
                                });
                            }); 
                        }
                        else if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) === -1 && item_type.indexOf(2) === -1 && item_type.indexOf(3) !== -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {
                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLRegularLoyalty', transData: transactionData}).then(function(result){
                                            deferred.resolve();
                                        });
                                    });
                                });
                            }); 
                        }
                        // doc_result[0].receipt_type == 1 && doc_result[0].return_qty != 0
                        else if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) === -1 && item_type.indexOf(2) !== -1 && item_type.indexOf(3) === -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {
                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLReturnLoyalty', transData: transactionData}).then(function(result){
                                           deferred.resolve(); 
                                        });
                                    });
                                });
                            });
                        }
                        // doc_result[0].receipt_type == 0 && doc_result[0].return_qty != 0 && doc_result[0].doc_tender_type == -1
                        else if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) !== -1 && item_type.indexOf(2) !== -1 && item_type.indexOf(3) === -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {
                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLReturnExchangeLoyalty', transData: transactionData}).then(function(result){
                                           deferred.resolve(); 
                                        });
                                    });
                                });
                            });  
                        }

                        if(sessionStorage.getItem('voucherCode') !== null && sessionStorage.getItem('voucherCode') !== null)
                        {
                            var vCode = JSON.parse(sessionStorage.getItem('voucherCode'));
                            var chainRequest = $q.when();
                            angular.forEach(vCode.voucherCode, function(value, key){
                                chainRequest = chainRequest.then(function(){
                                    var params  = {action: 'redeemVoucher',
                                                    voucherData: {root: {
                                                                        coupon: [{
                                                                            code: value,
                                                                            customer: {mobile: doc_result[0].bt_primary_phone_no},
                                                                             transaction: [{
                                                                               number: (doc_result[0].order_document_number != '') ? doc_result[0].order_document_number : doc_result[0].document_number,
                                                                               amount: doc_result[0].transaction_total_amt
                                                                             }]
                                                                        }]
                                                                    }
                                                                }
                                                };
                                    return $http.post(servername + '/plugins/PLVoucher/redeemVoucher.php', params).then(function(res){
                                        sessionStorage.removeItem('voucherCode');
                                        var obj = JSON.parse(res.data);
                                        if(obj.response.status.success.toUpperCase() == 'FALSE' && obj.response.status.success.code != 200 && obj.response.status.message.toUpperCase() != 'SUCCESS')
                                        {
                                            RN.showError('Voucher '+vCode.voucherCode+' Redemption Error', obj.response.coupons.coupon.item_status.message);
                                        }
                                    });
                                });

                            });

                        }

                        if(sessionStorage.getItem('loyaltyItems') !== null)
                        {
                            var loyaltyItem = JSON.parse(sessionStorage.getItem('loyaltyItems'));

                            var chainRequest = $q.when();
                            angular.forEach(loyaltyItem.item, function(value, key){
                                chainRequest = chainRequest.then(function(){
                                    var date        = new Date();
                                    var dateToday   = date.toISOString().substring(0, 10);
                                    var timeToday   = date.toLocaleTimeString().substring(0, 8);
                                    var DateTimeToday = dateToday + ' ' + timeToday;
                                    var params = {action: 'redeemPoints',
                                                loyaltyData: {
                                                              root: {
                                                                redeem: [{
                                                                  points_redeemed: value.points,
                                                                  transaction_number: (doc_result[0].order_document_number != '') ? doc_result[0].order_document_number : doc_result[0].document_number,
                                                                  customer: {mobile: value.mobile },
                                                                  notes: "",
                                                                  validation_code: value.loyaltyCode,
                                                                  redemption_time: DateTimeToday
                                                                }]
                                                              }
                                                            }
                                                };
                                    return $http.post(servername + '/plugins/PLLoyalty/loyalty.php', params).then(function(){
                                        if(key == loyaltyItem.item.length - 1)
                                        {
                                            sessionStorage.removeItem('loyaltyItems');
                                            sessionStorage.removeItem('loyaltyCode');

                                        }
                                    });
                                });
                            });
                        }

                        if(sessionStorage.getItem('gcRedeemData') !== null)
                        {
                            var items = JSON.parse(sessionStorage.getItem('gcRedeemData'));
                            var chainRequest = $q.when();

                            var bill_no     = (doc_result[0].order_document_number != '') ? doc_result[0].order_document_number : doc_result[0].document_number;
                            var activeCustomer = JSON.parse(sessionStorage.getItem('activeCustomer'));
                            var user_id = activeCustomer.user_id;
                            
                            angular.forEach(items, function(value, key){
                                if(value.amount != 0 || value.tender_sid != '')
                                {
                                    chainRequest = chainRequest.then(function(){
                                        var date        = new Date();
                                        var dateToday   = date.toISOString().substring(0, 10);
                                        var timeToday   = date.toLocaleTimeString().substring(0, 8);
                                        var DateTimeToday = dateToday + ' ' + timeToday;
                                        var gc_data = '<?xml version="1.0" encoding="ISO-8859-1"?>';
                                            gc_data += '<root>';
                                            gc_data += '<gift_card>';
                                            gc_data += '<card_no>' + value.cardNumber + '</card_no>';
                                            gc_data += '<encoded_card_no>' + value.cardNumber + '</encoded_card_no>';
                                            gc_data += '<amount>' + value.amount + '</amount>';
                                            gc_data += '<redeemed_on>' + DateTimeToday + '</redeemed_on>';
                                            gc_data += '<redeemed_by>' + user_id + '</redeemed_by>';
                                            gc_data += '<bill_no>' + bill_no + '</bill_no>';
                                            gc_data += '</gift_card>';
                                            gc_data += '</root>';

                                            var params = {action: 'redeemGC', data: gc_data};
                                            return $http.post(servername + '/plugins/PLGiftCard/giftCard.php', params).then(function(res){
                                                if(key == items.length - 1)
                                                {
                                                    sessionStorage.removeItem('gcRedeemNumber');
                                                    sessionStorage.removeItem('gcRedeemData');
                                                    sessionStorage.removeItem('activeCustomer');
                                                }
                                            });
                                    });
                                }
                                else
                                {
                                    if(key == items.length - 1)
                                    {
                                        sessionStorage.removeItem('gcRedeemNumber');
                                        sessionStorage.removeItem('gcRedeemData');
                                        sessionStorage.removeItem('activeCustomer');
                                    }
                                }
                            });
                        }

                        deferred.resolve();
                    }
                    else
                    {
                        if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) !== -1 && item_type.indexOf(2) === -1 && item_type.indexOf(3) === -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {
                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLRegularNonLoyalty', transData: transactionData}).then(function(result){
                                            deferred.resolve();
                                        });
                                    });
                                });
                            });
                        }
                        else if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) === -1 && item_type.indexOf(2) !== -1 && item_type.indexOf(3) === -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {

                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLReturnNonLoyalty', transData: transactionData}).then(function(result){
                                           deferred.resolve(); 
                                        });
                                    });
                                });
                            });

                        }
                        else if(doc_result[0].ref_order_sid == '' && item_type.indexOf(1) !== -1 && item_type.indexOf(2) !== -1 && item_type.indexOf(3) === -1 && item_type.indexOf(4) === -1 && item_type.indexOf(5) === -1)
                        {

                            transactionData.push({'documents': doc_result[0]});

                            ModelService.get('Tender', {document_sid: doc_result[0].sid}).then(function(tender){
                                angular.forEach(tender, function(value){
                                    transactionData.push({'tenders': value});
                                });

                                ModelService.get('DocumentDiscount', {document_sid: doc_result[0].sid}).then(function(discount){
                                    angular.forEach(discount, function(value){
                                        transactionData.push({'discounts': value});
                                    });

                                    ModelService.get('Item', {document_sid: doc_result[0].sid}).then(function(item){
                                        angular.forEach(item, function(value){
                                            transactionData.push({'items':value});
                                        });
                                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'generateXMLReturnExchangeNonLoyalty', transData: transactionData}).then(function(result){
                                           deferred.resolve(); 
                                        });
                                    });
                                });
                            });

                        }
                    }
                }
            });
        });

        return deferred.promise;
    }
);

SideButtonsManager.addButton({
    label: 'Online Basket',
    sections: ['transactionRoot', 'transactionEdit', 'transactionedittender', 'transactionreturns', 'transactionview', 'transaction'],
    handler: ['$modal','ModelService', '$q', '$window', 'NotificationService', 'LoadingScreen', '$state', 'DocumentPersistedData', '$http', function($modal, ModelService, $q, $window, NotificationService, LoadingScreen, $state, DocumentPersistedData, $http) {

        var deferred = $q.defer();
        var modalOptions = {
            backdrop: 'static',
            // windowClass: 'sm',
            size: 'lg', // sm, md, lg
            templateUrl: '/plugins/PLShopJustice/index.htm',
            controller: 'shopJusticeCtrl',
            keyboard: false
        };

        var customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country;

        var servername          = $window.location.origin;
        var activeDocumentSid   = DocumentPersistedData.DocumentInformation.Sid;

        var sendCustomerSid = function(phoneNo)
        {
            ModelService.get('Customer', {filter: 'primary_phone_no,eq,'+phoneNo}).then(function(data){
                var customerData = {
                                    root:{
                                    customer:[
                                      {
                                        mobile:data[0].primary_phone_no,
                                        custom_fields:{
                                            field:[
                                                {
                                                    name: "cust_sid",
                                                    value: data[0].sid
                                                }
                                            ]
                                        }
                                      }
                                    ]
                                    }
                                };

               $http.post(servername + '/plugins/PLVoucher/redeemVoucher.php', {action: 'updateCutomerDetails', customerData: customerData}).then(function(res){
                    var jsonObject = JSON.parse(res.data);
                    if(!('status_code' in jsonObject))
                    {
                        var success_request = jsonObject.response.status.success;
                        var request_code    = jsonObject.response.status.code;
                        var request_message = jsonObject.response.status.message;

                        if(success_request.toUpperCase() != 'TRUE' && request_code != 200 && request_message.toUpperCase() != 'SUCCESS')
                        {
                            NotificationService.addAlert(jsonObject.response.customers.customer[0].item_status.message, 'Customer Detail Validation', 'static', false).then(function(){
                                $state.go($state.current, {}, {reload: true});
                                deferred.resolve();
                            });
                            LoadingScreen.Enable = !1;

                        }
                        else
                        {
                            $modal.open(modalOptions);
                            $state.go($state.current, {}, {reload: true});
                            LoadingScreen.Enable = !1;
                            deferred.resolve();
                        }
                    }
                    else if(jsonObject.status_code == 400)
                    {
                        NotificationService.addAlert(jsonObject.message, 'Bad Request', 'static', false).then(function(){
                            $state.go($state.current, {}, {reload: true});
                            deferred.resolve();
                        });
                        LoadingScreen.Enable = !1;
                    }
                    else if(jsonObject.status_code == 500)
                    {
                        NotificationService.addAlert(jsonObject.message, 'Network Error', 'static', false).then(function(){
                            $state.go($state.current, {}, {reload: true});
                            deferred.resolve();
                        });
                        LoadingScreen.Enable = !1;
                    }
               });
            });
        }

        var is_online = false;

        var checkNetworkStatus = function()
        {
            var d = $q.defer();

            $http.get(servername + '/plugins/PLShopJustice/transactions_request.php?action=checkNetworkStatus').then(function(result){
                var online   = JSON.parse(result.data);
                
                if(online.status_code == 200)
                {
                    is_online = true;
                    d.resolve();
                }
                else
                {
                    if(online.status_code == 408)
                    {
                        NotificationService.addAlert(online.message, 'Request Timeout', 'static', false).then(function(){
                            $state.go($state.current, {}, {reload: true});
                        });
                    }
                    else if(online.status_code == 500)
                    {
                        NotificationService.addAlert(online.message, 'Network Error', 'static', false).then(function(){
                            $state.go($state.current, {}, {reload: true});
                        });
                    }
                    LoadingScreen.Enable = !1;
                    d.resolve();
                }
            });
            return d.promise;
        }

        LoadingScreen.Enable = !0, checkNetworkStatus().then(function(){
            if(is_online)
            {
                ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){

                    if(doc[0].bt_cuid == '' || doc[0].bt_cuid == null)
                    {
                        $http.post(servername + '/plugins/PLCRM/crmFunctions.php', {action: 'retrieveCustomerData'}).then(function(result){

                            var jsonObj     = JSON.parse(result.data);
                            customerType    = jsonObj.recordType;
                            if(jsonObj.status == 200)
                            {
                                customerData    = JSON.parse(jsonObj.result);
                                mobileNo    = customerData.customer.mobile;
                                // mobileNo2    = customerData.customer.mobile_2;
                                cust_id     = customerData.customer.customer_id;
                                cust_sid    = customerData.customer.cust_sid;
                                customerName  = customerData.customer.name;
                                customerFName   = customerData.customer.customer_firstname;
                                customerLName   = customerData.customer.customer_lastname;
                                customerEmail   = customerData.customer.email;
                                customerGender  = customerData.customer.gender;
                                externalId    = customerData.customer.external_id;
                                address_one_one = customerData.customer.address_one_home_one;
                                address_one_two = customerData.customer.address_one_home_two;
                                address_two_one = customerData.customer.address_two_home_one;
                                address_two_two = customerData.customer.address_two_home_two;
                                address_three_one = customerData.customer.address_three_home_o;
                                address_three_two = customerData.customer.address_three_home_t;

                                city_one      = customerData.customer.city_one;
                                city_two      = customerData.customer.city_two;
                                city_three      = customerData.customer.city_three;
                                district_one    = customerData.customer.district_one;
                                district_two    = customerData.customer.district_two;
                                district_three    = customerData.customer.district_three;
                                province_one    = customerData.customer.province_one;
                                province_two    = customerData.customer.province_two;
                                province_three    = customerData.customer.province_three;
                                postal_code       = customerData.customer.zip_code;
                                country         = customerData.customer.country_origin;


                                   // NEW Customer Function
                                var newCustomerFunction = function(customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country)
                                {
                                  var newCustomer = ModelService.create('Customer');

                                  newCustomer.primary_phone_no  = mobileNo;
                                  newCustomer.full_name       = customerName;
                                  newCustomer.first_name      = customerFName;
                                  newCustomer.last_name       = customerLName;
                                  newCustomer.customer_id     = cust_id;
                                  newCustomer.udffield05      = externalId;
                                  newCustomer.insert().then(function(data){
                                    var customerSid = data.sid;
                                      var newCust   = data;
                                      // Add customer address
                                      var newAdd1 = ModelService.create('Address');
                                      newAdd1.customer_sid  = customerSid;
                                      newAdd1.address_line_1  = address_one_one;
                                      newAdd1.address_line_2  = address_one_two;
                                      newAdd1.address_line_3  = city_one;
                                      newAdd1.city      = city_one;
                                      newAdd1.country_name  = country;
                                      // newAdd.state       = province;
                                      newAdd1.postal_code     = postal_code;

                                      newAdd1.active      = 1;
                                      newAdd1.primary_flag  = 1;
                                      newAdd1.seq_no      = 1;
                                      ModelService.get('Addresstype', {filter: 'type_name,lk,Home'}).then(function(addr){
                                        newAdd1.address_type_sid = addr[0].sid;
                                        newCust.addAddress(newAdd1).then(function(currentAddress){
                                          var newAdd2 = ModelService.create('Address');
                                          newAdd2.customer_sid  = customerSid;
                                          newAdd2.address_line_1  = address_two_one;
                                          newAdd2.address_line_2  = address_two_two;
                                          newAdd2.address_line_3  = city_two;
                                          newAdd2.city      = city_two;
                                          newAdd2.country_name  = country;
                                          // newAdd.state       = province;
                                          newAdd2.postal_code   = postal_code;
                                          newAdd2.active      = 1;
                                          newAdd2.address_type_sid = addr[0].sid;
                                          newCust.addAddress(newAdd2).then(function(currentAddress){
                                            var newAdd3 = ModelService.create('Address');
                                            newAdd3.customer_sid  = customerSid;
                                            newAdd3.address_line_1  = address_three_one;
                                            newAdd3.address_line_2  = address_three_two;
                                            newAdd3.address_line_3  = city_three;
                                            newAdd3.city      = city_three;
                                            newAdd3.country_name  = country;
                                            // newAdd.state       = province;
                                            newAdd3.postal_code   = postal_code;
                                            newAdd3.active      = 1;
                                            newAdd3.address_type_sid = addr[0].sid;
                                            newCust.addAddress(newAdd3).then(function(currentAddress){
                                              // Add customer email address
                                              var newEmail = ModelService.create('Email');
                                              newEmail.customer_sid   = customerSid;
                                              newEmail.email_address  = customerEmail;
                                              newEmail.seq_no     = 1;
                                              newEmail.primary_flag   = 1;
                                              ModelService.get('Emailtype', {filter: 'email_type,lk,Home'}).then(function(email){
                                                newEmail.email_type_sid = email[0].sid;
                                                newCust.addEmail(newEmail).then(function(){
                                                  // Add customer phone no
                                                  var newPhone = ModelService.create('Phone');
                                                  newPhone.customer_sid   = customerSid;
                                                  newPhone.phone_no     = mobileNo;
                                                  newPhone.seq_no     = 2;
                                                  newPhone.primary_flag   = 1;
                                                  
                                                  ModelService.get('Phonetype', {filter: 'phone_type,lk,Mobile'}).then(function(phone){
                                                    newPhone.phone_type_sid = phone[0].sid;
                                                    newCust.addPhone(newPhone).then(function(){
                                                      ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
                                                        var currentDocData = doc[0];
                                                        currentDocData.addBillToCustomer(newCust).then(function(){
                                                          currentDocData.addShipToCustomer(newCust).then(function(){
                                                              ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
                                                                var chainRequest = $q.when();
                                                                var count = [];
                                                                angular.forEach(items, function(value, key){
                                                                  chainRequest = chainRequest.then(function(){
                                                                    var item = value;
                                                                    item.st_postal_code   = postal_code;
                                                                    item.st_address_line1   = address_one_one;
                                                                    item.st_address_line2   = address_one_two;
                                                                    item.st_address_line3   = city_one;
                                                                    item.st_cuid            = customerSid;
                                                                    item.st_id              = cust_id;
                                                                    item.st_first_name    = customerFName;
                                                                    item.st_last_name     = customerLName;
                                                                    item.st_primary_phone_no= mobileNo;
                                                                    item.st_email       = customerEmail;
                                                                    item.st_address_uid   = currentAddress.sid;
                                                                  return item.save().then(function(){
                                                                    count.push(1);
                                                                  });
                                                                  });
                                                                });

                                                                $q.all(count).then(function(){
                                                                  sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
                                                                  sendCustomerSid(mobileNo);
                                                                });
                                                              });
                                                          });
                                                        });
                                                      });
                                                    });
                                                  });
                                                });
                                              });
                                            });
                                          });
                                        });
                                      });

                                  }, function(error){
                                    LoadingScreen.Enable = !1;
                                    NotificationService.addAlert(error.data[0].errormsg, 'Customer Data Validation', 'static', false).then(function(){
                                      sessionStorage.setItem('hasCustomer', 0);
                                      deferred.resolve();
                                    });
                                  });

                                  // customerExist = false;
                                };

                                // Update Customer Function
                                var updateCustomerFunctionDefault = function(data, customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country)
                                {
                                var updateCustomer = data;

                                updateCustomer.primary_phone_no     = mobileNo;
                                updateCustomer.full_name        = customerName;
                                updateCustomer.first_name         = customerFName;
                                updateCustomer.last_name        = customerLName;
                                updateCustomer.customer_id        = cust_id;
                                updateCustomer.udffield05         = externalId;
                                updateCustomer.save().then(function(data){
                                  var customerSid = data.sid;

                                  // Add customer address
                                  var newAdd1 = ModelService.create('Address');
                                  newAdd1.customer_sid  = customerSid;
                                  newAdd1.address_line_1  = address_one_one;
                                  newAdd1.address_line_2  = address_one_two;
                                  newAdd1.address_line_3  = city_one;
                                  newAdd1.city      = city_one;
                                  newAdd1.country_name  = country;
                                  // newAdd1.state      = province_one;
                                  newAdd1.postal_code   = postal_code;
                                  newAdd1.active      = 1;
                                  newAdd1.primary_flag  = 1;
                                  ModelService.get('Addresstype', {filter: 'type_name,lk,Home'}).then(function(addr){
                                    newAdd1.address_type_sid = addr[0].sid;
                                    updateCustomer.addAddress(newAdd1).then(function(currentAddress){
                                      var newAdd2 = ModelService.create('Address');
                                      newAdd2.customer_sid  = customerSid;
                                      newAdd2.address_line_1  = address_two_one;
                                      newAdd2.address_line_2  = address_two_two;
                                      newAdd2.address_line_3  = city_two;
                                      newAdd2.city      = city_two;
                                      newAdd2.country_name  = country;
                                      // newAdd2.state      = province_two;
                                      newAdd2.postal_code   = postal_code;
                                      newAdd2.active      = 1;
                                      newAdd2.address_type_sid = addr[0].sid;
                                      updateCustomer.addAddress(newAdd2).then(function(currentAddress){
                                        var newAdd3 = ModelService.create('Address');
                                        newAdd3.customer_sid  = customerSid;
                                        newAdd3.address_line_1  = address_three_one;
                                        newAdd3.address_line_2  = address_three_two;
                                        newAdd1.address_line_3  = city_three;
                                        newAdd3.city      = city_three;
                                        newAdd3.country_name  = country;
                                        // newAdd3.state      = province_three;
                                        newAdd3.postal_code   = postal_code;
                                        newAdd3.active      = 1;
                                        newAdd3.address_type_sid = addr[0].sid;
                                        updateCustomer.addAddress(newAdd3).then(function(currentAddress){
                                          ModelService.get('Email',{customer_sid: customerSid,filter: 'primary_flag,eq,true'}).then(function(email){
                                                  var updatedEmail        = email[0];
                                                    updatedEmail.email_address  = customerEmail;
                                                    updatedEmail.save().then(function(){
                                                      ModelService.get('Phone', {customer_sid: customerSid, filter: 'primary_flag,eq,true'}).then(function(phone){
                                                        var updatedPhone      = phone[0];
                                                          updatedPhone.phone_no   = mobileNo;
                                                          updatedPhone.save().then(function(){
                                                            ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc){
                                                              var billToCustData = [{ "bt_cuid"         : customerSid,
                                                                          "bt_id"         : cust_id,
                                                                          "bt_first_name"     : customerFName,
                                                                          "bt_last_name"      : customerLName,
                                                                          "bt_primary_phone_no"   : mobileNo,
                                                                          "bt_address_line1"    : address_one_one,
                                                                          "bt_address_line2"    : address_one_two,
                                                                          "bt_postal_code"    : postal_code,
                                                                          "bt_email"        : customerEmail
                                                                        }];
                                                              $http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,'+ doc[0].row_version, billToCustData).then(function(data){
                                                                var shipToCustData = [{ "st_cuid"         : customerSid,
                                                                            "st_id"         : cust_id,
                                                                            "st_first_name"     : customerFName,
                                                                            "st_last_name"      : customerLName,
                                                                            "st_primary_phone_no"   : mobileNo,
                                                                            "st_address_line1"    : address_one_one,
                                                                            "st_address_line2"    : address_one_two,
                                                                            "st_postal_code"    : postal_code,
                                                                            "st_email"        : customerEmail
                                                                          }];
                                                                $http.put(servername + '/v1/rest/document/' + activeDocumentSid + '?filter=row_version,eq,' + data.data[0].row_version, shipToCustData).then(function(){
                                                                ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(items){
                                                                  var chainRequest = $q.when();
                                                                  var count = [];
                                                                  angular.forEach(items, function(value, key){
                                                                    chainRequest = chainRequest.then(function(){
                                                                      var item = value;
                                                                      item.st_postal_code   = postal_code;
                                                                      item.st_address_line1   = address_one_one,
                                                                      item.st_address_line2   = address_one_two,
                                                                      item.st_address_line3   = city_one;
                                                                      item.st_cuid      = customerSid;
                                                                      item.st_id        = cust_id;
                                                                      item.st_first_name    = customerFName;
                                                                      item.st_last_name     = customerLName;
                                                                      item.st_primary_phone_no= mobileNo;
                                                                      item.st_email       = customerEmail;
                                                                      item.st_address_uid   = currentAddress.sid;
                                                                    return item.save().then(function(){
                                                                      count.push(1);
                                                                    });
                                                                    });
                                                                  });

                                                                  $q.all(count).then(function(){
                                                                    sessionStorage.setItem('activeCustomer', JSON.stringify({user_id: cust_id, fullname: customerName}));
                                                                    sendCustomerSid(mobileNo);
                                                                  });
                                                                });
                                                                });
                                                              });
                                                            });
                                                          });
                                                      });
                                                    });
                                          });
                                        });
                                      });
                                    });
                                  });
                                });
                                // customerExist = true;
                                };

                                var filter;

                                filter = {filter: 'primary_phone_no,eq,' + mobileNo};

                                ModelService.get('Customer', filter).then(function(data){
                                    // if(data.length != 0 && customerType == 0)
                                    // {
                                    //    updateCustomerFunction(data[data.length - 1], customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
                                    // }
                                    if(data.length != 0)
                                    {
                                        updateCustomerFunctionDefault(data[data.length - 1], customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
                                    }
                                    else
                                    {
                                        newCustomerFunction(customerSid, customerData, mobileNo, mobileNo2, cust_id, customerName, customerFName, customerLName, customerEmail, customerGender, externalId, address_one_one, address_one_two, address_two_one, address_two_two, address_three_one, address_three_two, city_one, city_two, city_three, province_one, province_two, province_three, district_one, district_two, district_three, postal_code, customerType, customerExist, country);
                                    }
                                });
                                sessionStorage.setItem('hasCustomer', 1);
                            }
                            else
                            {
                                LoadingScreen.Enable = !1;
                                sessionStorage.setItem('hasCustomer', 0);
                                NotificationService.addAlert('Customer details is required in this transaction.', 'Customer Required', 'static', false).then(function(){
                                    $state.go($state.current, {}, {reload: true});
                                    deferred.reject();
                                });
                            }
                        });
                    }
                    else
                    {
                        // LoadingScreen.Enable = !1;
                        $modal.open(modalOptions);
                        deferred.resolve();
                    }
                });
            }
        });

        return deferred.promise;
    }]
});

ButtonHooksManager.addHandler(['after_posSelectItemMagento'],['$q', 'ModelService', '$window', '$http', '$timeout', '$stateParams', function($q, ModelService, $window, $http, $timeout, $stateParams) {
    var deferred = $q.defer();

    $timeout(function(){
        var activeDocumentSid  = '';
        if(typeof ModelService.fromCache('Document') === 'undefined')
        {
            activeDocumentSid = $stateParams.document_sid;
        }
        else
        {
            activeDocumentSid = ModelService.fromCache('Document')[0].sid;
        }

        var serverName      = $window.location.origin;

        ModelService.get('Document', {sid: activeDocumentSid}).then(function(doc_result){

            var item        = [];
            var fee         = {'fee_name': doc_result[0].fee_name1, 'fee_amount': doc_result[0].fee_amt1, 'record_flag': 2};
            var shipping    = {'shipping_method': doc_result[0].ship_method, 'shipping_amt': doc_result[0].shipping_amt, 'record_flag': 2};
            var discount    = {'disc_amt': doc_result[0].total_discount_amt, 'record_flag': 3};
            var total       = {'total_amt': doc_result[0].transaction_total_amt, 'record_flag': 4};
            var tender      = {'tender_name': doc_result[0].tender_name, 'taken_amt': doc_result[0].taken_amt, 'given_amt': doc_result[0].given_amt, 'record_flag': 5};

            ModelService.get('Item', {document_sid: activeDocumentSid}).then(function(item_result){
                angular.forEach(item_result,function(value){
                    var per_item = {};
                    var shipping_amt        = (value.shipping_amt != '' && value.shipping_amt != null) ? value.shipping_amt : 0;
                    per_item['item_sid']    = value.invn_sbs_item_sid;
                    per_item['item_desc']   = value.item_description1;
                    per_item['item_qty']    = value.quantity;
                    per_item['item_price']  = value.original_price;
                    per_item['item_ext_price']  = parseFloat(parseFloat(value.quantity) * parseFloat(value.price)) + parseFloat(shipping_amt);
                    per_item['item_alu']    = value.alu;
                    per_item['record_flag'] = 0;
                    item.push(per_item);
                });

                $http.post(serverName + '/plugins/PLCustomerDisplay/executeCustDisplay.php', {'item': item, 'fee': fee, 'shipping': shipping, 'discount': discount, 'total': total, 'tender': tender}).then(function(result){

                });
            });
        });

        //resolve the deferred operation
        deferred.resolve();
        //return the deferred promise
        return deferred.promise;
    },1000);
    
    }]
);

var shopJusticeCM = ['ModelEvent','$modal', 'ModelService2', 'NotificationService', '$window', '$http', '$state', function(ModelEvent, $modal, ModelService2, NotificationService, $window, $http, $state){
    var servername = $window.location.origin;
    var sess       = $http.defaults.headers.common['Auth-Session'];

    var handlerAfterItemRemove = function($q, item){
        var deferred = $q.defer();
        if(sessionStorage.getItem('cartItemsALU') !== null)
        {
            var itemsAlu = JSON.parse(sessionStorage.getItem('cartItemsALU'));
            var newItems = [];
            var hasDeletedItem = false;
            angular.forEach(itemsAlu, function(value, key){
                if(item.alu != value)
                {
                    newItems.push(value);
                }
                else
                {
                    hasDeletedItem = true;
                }

                if(key == itemsAlu.length - 1)
                {
                    if(newItems.length > 0)
                    {
                        sessionStorage.setItem('cartItemsALU', JSON.stringify(newItems));
                    }
                    else if(newItems.length == 0 && hasDeletedItem)
                    {
                        sessionStorage.removeItem('cartItemsALU');
                    }
                }
            });
        }

        if(sessionStorage.getItem('wishListItemsALU') !== null)
        {
            var itemsAlu = JSON.parse(sessionStorage.getItem('wishListItemsALU'));
            var newItems = [];
            var hasDeletedItem = false;
            angular.forEach(itemsAlu, function(value, key){
                if(item.alu != value)
                {
                    newItems.push(value);
                }
                else
                {
                    hasDeletedItem = true;
                }

                if(key == itemsAlu.length - 1)
                {
                    if(newItems.length > 0)
                    {
                        sessionStorage.setItem('wishListItemsALU', JSON.stringify(newItems));
                    }
                    else if(newItems.length == 0 && hasDeletedItem)
                    {
                        sessionStorage.removeItem('wishListItemsALU');
                    }
                }
            });
        }

        if(sessionStorage.getItem('giftRegistryItemsALU') !== null)
        {
            var itemsAlu = JSON.parse(sessionStorage.getItem('giftRegistryItemsALU'));
            var newItems = [];
            var hasDeletedItem = false;
            angular.forEach(itemsAlu, function(value, key){
                if(item.alu != value)
                {
                    newItems.push(value);
                }
                else
                {
                    hasDeletedItem = true;
                }

                if(key == itemsAlu.length - 1)
                {
                    if(newItems.length > 0)
                    {
                        sessionStorage.setItem('giftRegistryItemsALU', JSON.stringify(newItems));
                    }
                    else if(newItems.length == 0 && hasDeletedItem)
                    {
                        sessionStorage.removeItem('giftRegistryItemsALU');
                    }
                }
            });
        }

        deferred.resolve();

        //return the deferred promise
        return deferred.promise;
    };

    ModelEvent.addListener('item', ['onAfterRemove'], handlerAfterItemRemove);
}]

ConfigurationManager.addHandler(shopJusticeCM);
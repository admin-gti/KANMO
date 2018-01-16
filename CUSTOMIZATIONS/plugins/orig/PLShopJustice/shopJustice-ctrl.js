var shopJustice = ["$scope", "$http", "ModelService", "$stateParams", "prismSessionInfo", "$location", "$modalInstance", "$modal", "XZOutReporter", "$state", "base64","$q","PrintersService","PrismUtilities","$filter","$window", "ResourceNotificationService", "LoadingScreen", "NotificationService",
	function($scope, $http, ModelService, $stateParams, prismSessionInfo, $location, $modalInstance, $modal, XZOutReporter, $state, base64, $q, PrintersService, PrismUtilities, $filter, $window, rNS, LoadingScreen, NS) {
		'use strict';
		var sess = $http.defaults.headers.common['Auth-Session'];
		var servername = $window.location.origin;
		var activeCustomer = ModelService.fromCache('Customer')[0];

		var response_code = null;

		var root_url = "/plugins/PLShopJustice/transactions_request.php?action=";

		var sessionData = JSON.parse(sessionStorage.getItem('session'));

		var deferred = $q.defer();

		var magentoAPI = function(){
			// Method use to retrieve access token from the API
			$scope.getAccessToken = function(){
				$http.get(servername + root_url + "getAccessToken", {headers:{"Auth-Session": sess}})
				.success(function(response){
					var obj = JSON.parse(response);
					if(obj.status_code == "200")
					{
						sessionStorage.setItem("items_access_token", true);
						$scope.getCustomerDetails();
					}
					else
					{
						LoadingScreen.Enable = !1;
						var confirm = NS.addAlert('Unable to retrieve external resources!', 'External Error', 'static', false);
						confirm.then(function(){
							$modalInstance.dismiss();
							$state.go($state.current, {}, {reload: true});
						});
					}
				});
			}

			// Method use to retrieve access token from the API
			$scope.getCustomerDetails = function(){
				$http.get(servername + root_url + "getCustomerDetails&email=" + activeCustomer.email_address, {headers:{"Auth-Session": sess}})
				.success(function(response){
					var obj = JSON.parse(response);
					if(obj.status_code == "200" && obj.length > 0)
					{
						$scope.getCartItems(obj.result.items[0].id);
					}
					else
					{
						LoadingScreen.Enable = !1;
						var confirm = NS.addAlert('Unable to retrieve customer details!', 'External Error', 'static', false);
						confirm.then(function(){
							$modalInstance.dismiss();
							$state.go($state.current, {}, {reload: true});
						});
					}
				});
			}

			// Method use to retrieve cart items from the API
			$scope.getCartItems = function(customer_id){
				$http.get(servername + root_url + "getCartItems&customer_id="+customer_id, {headers:{"Auth-Session": sess}})
				.success(function(response){
					var obj = JSON.parse(response);
					if(obj.status_code == "200")
					{
						obj.length = 0;
						if(sessionStorage.getItem('cartItemsALU') !== null)
						{
							var items_selected = [];
							var existingItems = JSON.parse(sessionStorage.getItem('cartItemsALU'));
							angular.forEach(obj.result.items, function(value, key) {
								if(existingItems.indexOf(value.sku) !== -1) 
								{
									items_selected.push(key);
								}
							});

							if(items_selected.length != 0)
							{
								items_selected.forEach(x => delete obj.result.items[x]);
								obj.result.items = obj.result.items.filter(v=>v!=null);
							}
						}


						if(obj.result.items.length != 0)
						{
							var magentoItems = obj.result.items;
							obj.result.items = [];
							var chainRequest = $q.when();
							var chainRequest2 = $q.when();
							angular.forEach(magentoItems, function(value, key) {
								var filter = 'alu,eq,' + value.sku;
								chainRequest = chainRequest.then(function(){
									return ModelService.get('Inventory',{filter : filter})
									.then(function(res){
										if(res.length > 0)
										{
											obj.result.items.push({item_id : value.item_id, name: value.name, price: value.price, product_type: value.product_type, qty: value.qty, quote_id: value.quote_id, sku: value.sku, prism_item: res});
											obj.result.items[key].prism_item.item_origin 	= "CART";
											obj.result.items[key].prism_item.item_origin_id = obj.result.id;
											obj.result.items[key].prism_item.item_id 		= value.id;
											obj.result.items[key].prism_item.item_qty 		= value.qty;
											var filters = '&subsidiary_sid,eq,' + sessionData.subsidiarysid;
												filters += '&store_sid,eq,' + sessionData.storesid;
											chainRequest2 = chainRequest2.then(function(){
												return $http.get(servername + '/v1/rest/inventory/' + res[0].sid + '/sbsinventorystoreqty?cols=*&' + filters).then(function(str_qty_res){
													if(str_qty_res.data.length > 0)
													{
														obj.result.items[key].store_oh_qty 			= str_qty_res.data[0].quantity;
														obj.length +=1;
													}
													else
													{
														obj.result.items[key].store_oh_qty 			= 0;
														obj.length +=1;
													}
												});
											});
										}
										else
										{
											obj.result.items.splice(key, 1);
										}

									});
								});
							});
						}

						$scope.cartItems 								= obj;
						$scope.getWishListItems(customer_id);
					}
					else if(obj.status_code == "404")
					{
						obj.length = 0;
						$scope.cartItems = obj;
						$scope.getWishListItems(customer_id);
					}
					else
					{
						$scope.getAccessToken();
					}
				});
			}

			// Method use to retrieve wish lists items from the API
			$scope.getWishListItems = function(customer_id){
				$http.get(servername + root_url + "getWishListItems&customer_id="+customer_id, {headers:{"Auth-Session": sess}})
				.success(function(response){
					var obj = JSON.parse(response);
					if(obj.status_code == "200")
					{
						obj.length = 0;
						if(sessionStorage.getItem('wishListItemsALU') !== null)
						{
							var items_selected = [];
							var existingItems = JSON.parse(sessionStorage.getItem('wishListItemsALU'));
							angular.forEach(obj.result.items, function(value, key) {
								if(existingItems.indexOf(value.product.sku) !== -1) 
								{
									items_selected.push(key);
								}
							});

							if(items_selected.length != 0)
							{
								items_selected.forEach(x => delete obj.result.items[x]);
								obj.result.items = obj.result.items.filter(v=>v!=null);
							}
						}

						if(obj.result.items.length > 0)
						{
							var magentoItems = obj.result.items;
							obj.result.items = [];
							var chainRequest = $q.when();
							var chainRequest2 = $q.when();
							angular.forEach(magentoItems, function(value, key){
								var filter = 'alu,eq,' + value.product.sku;
								chainRequest = chainRequest.then(function(){
									return ModelService.get('Inventory',{filter : filter})
									.then(function(res){
										if(res.length == 1)
										{
											obj.result.items.push({added_at : value.added_at, description: value.description, id: value.id, product: value.product, product_id: value.product_id, qty: value.qty, store_id: value.store_id, wishlist_id: value.wishlist_id, prism_item: res});
											obj.result.items[key].prism_item.item_origin 	= "WISHLIST";
											obj.result.items[key].prism_item.item_origin_id = obj.result.id;
											obj.result.items[key].prism_item.item_id 		= value.id;
											obj.result.items[key].prism_item.item_qty 		= value.qty;
											var filters = '&subsidiary_sid,eq,' + sessionData.subsidiarysid;
												filters += '&store_sid,eq,' + sessionData.storesid;
											chainRequest2 = chainRequest2.then(function(){
												return $http.get(servername + '/v1/rest/inventory/' + res[0].sid + '/sbsinventorystoreqty?cols=*&' + filters).then(function(str_qty_res){
													if(str_qty_res.data.length > 0)
													{
														obj.result.items[key].store_oh_qty 			= str_qty_res.data[0].quantity;
														obj.length +=1;
													}
													else
													{
														obj.result.items[key].store_oh_qty 			= 0;
														obj.length +=1;
													}
												});
											});
											
										}
									});
								});
							});
						}

						$scope.wishListItems = obj;

						$scope.getGiftRegistryItems(customer_id);
					}
					else if(obj.status_code == "404")
					{
						obj.length = 0;
						$scope.wishListItems = obj;
						$scope.getGiftRegistryItems(customer_id);
					}
					else
					{
						$scope.getAccessToken();
					}
				});
			}

			// Method use to retrieve wish lists items from the API
			$scope.getGiftRegistryItems = function(customer_id){
				$http.get(servername + root_url + "getGiftRegistryItems&customer_id="+customer_id, {headers:{"Auth-Session": sess}})
				.success(function(response){
					var obj = JSON.parse(response);
					if(obj.status_code == "200")
					{
						if(obj.length > 0)
						{
							obj.length 			= 0;
							if(sessionStorage.getItem('giftRegistryItemsALU') !== null)
							{
								var items_selected = [];
								var existingItems = JSON.parse(sessionStorage.getItem('giftRegistryItemsALU'));
								angular.forEach(obj.result.items, function(value, key) {
									if(existingItems.indexOf(value.product_sku) !== -1) 
									{
										items_selected.push(key);
									}
								});

								if(items_selected.length != 0)
								{
									items_selected.forEach(x => delete obj.result.items[x]);
									obj.result.items = obj.result.items.filter(v=>v!=null);
								}
							}

							if(obj.result.items.length != 0)
							{
								var magentoItems = obj.result.items;
								obj.result.items = [];
								var chainRequest = $q.when();
								var chainRequest2 = $q.when();
								angular.forEach(magentoItems, function(value, key) {
									var filter = 'alu,eq,' + value.product_sku;
									chainRequest = chainRequest.then(function(){
										return ModelService.get('Inventory',{filter : filter})
										.then(function(res){
											if(res.length > 0)
											{
												var qty_old = value.qty;

												if(qty_old != value.qty_fulfilled && qty_old > value.qty_fulfilled)
												{
													obj.result.items.push({id : value.id, product_id: value.product_id, entity_id: value.entity_id, qty: value.qty, qty_fulfilled: value.qty_fulfilled, note: value.note, added_at: value.added_at, custom_options: value.custom_options, product_name: value.product_name, product_sku: value.product_sku, prism_item: res});
													obj.result.items[obj.length].prism_item.item_origin 	= "GIFTREGISTRY";
													obj.result.items[obj.length].prism_item.item_origin_id 	= obj.result.id;
													obj.result.items[obj.length].prism_item.item_id 		= value.id;
													obj.result.items[obj.length].prism_item.item_qty_old 	= value.qty;
													
													if(value.qty_fulfilled !== null && value.qty_fulfilled < obj.result.items[obj.length].prism_item.item_qty_old)
													{
														obj.result.items[obj.length].prism_item.item_qty 	= value.qty - value.qty_fulfilled
													}
													else
													{
														obj.result.items[obj.length].prism_item.item_qty 	= obj.result.items[obj.length].prism_item.item_qty_old;
													}

													var filters = '&subsidiary_sid,eq,' + sessionData.subsidiarysid;
														filters += '&store_sid,eq,' + sessionData.storesid;
													chainRequest2 = chainRequest2.then(function(){
														return $http.get(servername + '/v1/rest/inventory/' + res[0].sid + '/sbsinventorystoreqty?cols=*&' + filters).then(function(str_qty_res){
															if(str_qty_res.data.length > 0)
															{
																obj.result.items[obj.length].store_oh_qty 	= str_qty_res.data[0].quantity;
																obj.result.items[obj.length].qty 			= obj.result.items[obj.length].prism_item.item_qty;
																obj.length += 1;
															}
															else
															{
																obj.result.items[obj.length].store_oh_qty 	= 0;
																obj.result.items[obj.length].qty 			= obj.result.items[obj.length].prism_item.item_qty;
																obj.length += 1;
															}
														});
													});

												}
											}
										});
									});
								});
							}

							$scope.giftRegistryItems = obj;
							deferred.resolve();
						}
						else
						{
							obj.length = 0;
							$scope.giftRegistryItems = obj;
							deferred.resolve();
						}
					}
					else if(obj.status_code == "404")
					{
						obj.length = 0;
						$scope.giftRegistryItems = obj;
						deferred.resolve();
					}
					else
					{
						$scope.getAccessToken();
					}
				});
			}

			return deferred.promise;
		};

		// Method use to select all items in cart table
		$scope.checkAllCartItems = function () {
	        if ($scope.selectAllCartItems) {
	            $scope.selectAllCartItems = true;
	        } else {
	            $scope.selectAllCartItems = false;
	        }
	        angular.forEach($scope.cartItems.result.items, function (item) {
	            item.Selected = $scope.selectAllCartItems;
	        });

		};

		// Method use to select all items in wish list table
		$scope.checkAllWishListItems = function () {
	        if ($scope.selectAllWishListItems) {
	            $scope.selectAllWishListItems = true;
	        } else {
	            $scope.selectAllWishListItems = false;
	        }
	        angular.forEach($scope.wishListItems.result.items, function (item) {
	            item.Selected = $scope.selectAllWishListItems;
	        });
		};

		// Method use to select all items in gift registry table
		$scope.checkAllGiftRegistryItems = function () {
	        if ($scope.selectAllGiftRegistryItems) {
	            $scope.selectAllGiftRegistryItems = true;
	        } else {
	            $scope.selectAllGiftRegistryItems = false;
	        }
	        angular.forEach($scope.giftRegistryItems.result.items, function (item) {
	            item.Selected = $scope.selectAllGiftRegistryItems;
	        });

		};

		// Method use to insert selected items from API to Prism
		$scope.saveSelectedItems = function(){
			var selectedItems 	= [];
			if(typeof $scope.cartItems !== 'undefined')
			{
				var cartItemsALU = [];
				if(sessionStorage.getItem('cartItemsALU') !== null)
				{
					cartItemsALU = JSON.parse(sessionStorage.getItem('cartItemsALU'));
				}
				angular.forEach($scope.cartItems.result.items, function(item) {
					if (item.Selected) {
						selectedItems.push(item);
						cartItemsALU.push(item.sku)
					}
				});

				if(cartItemsALU.length > 0)
				{
					sessionStorage.setItem('cartItemsALU', JSON.stringify(cartItemsALU));
				}
			}

			if(typeof $scope.wishListItems !== 'undefined')
			{
				var wishListItemsALU = [];
				if(sessionStorage.getItem('wishListItemsALU') !== null)
				{
					wishListItemsALU = JSON.parse(sessionStorage.getItem('wishListItemsALU'));
				}
				angular.forEach($scope.wishListItems.result.items, function(item) {
					if (item.Selected) {
						selectedItems.push(item);
						wishListItemsALU.push(item.product.sku);
					}
				});

				if(wishListItemsALU.length > 0)
				{
					sessionStorage.setItem('wishListItemsALU', JSON.stringify(wishListItemsALU));
				}
			}

			if(typeof $scope.giftRegistryItems !== 'undefined')
			{
				var giftRegistryItemsALU = [];
				if(sessionStorage.getItem('giftRegistryItemsALU') !== null)
				{
					giftRegistryItemsALU = JSON.parse(sessionStorage.getItem('giftRegistryItemsALU'));
				}
				angular.forEach($scope.giftRegistryItems.result.items, function(item) {
					if (item.Selected) {
						selectedItems.push(item);
						giftRegistryItemsALU.push(item.product_sku);
					}
				});

				if(giftRegistryItemsALU.length > 0)
				{
					sessionStorage.setItem('giftRegistryItemsALU', JSON.stringify(giftRegistryItemsALU));
				}
			}

			ModelService.get('Document',{sid:$stateParams.document_sid,cols:'*'})
			.then(function(result){
				if(selectedItems.length > 0)
				{
					var itemsData = "[";
					angular.forEach(selectedItems, function(value, key){
						itemsData += "{\"document_sid\":\""+$stateParams.document_sid+"\",";
						itemsData += "\"fullfill_store_sid\":\""+result[0].store_uid+"\",";
						itemsData += "\"invn_sbs_item_sid\":\""+value.prism_item[0].sid+"\",";
						itemsData += "\"item_type\":1,";
						itemsData += "\"origin_application\":\"RproPrismWeb\",";
						itemsData += "\"note1\":\""+value.prism_item.item_origin+"\",";
						itemsData += "\"note2\":\""+value.prism_item.item_origin_id+"\",";
						itemsData += "\"note3\":\""+value.prism_item.item_id+"\",";
						itemsData += "\"note4\":\""+value.prism_item.item_qty+"\",";
						itemsData += "\"note5\":\""+parseInt(value.qty)+"\",";
						itemsData += "\"quantity\":"+value.qty+"}";
						if(key != selectedItems.length - 1){
						  itemsData += ",";
						}
					});
					itemsData += "]";

					$http.post(servername+"/v1/rest/document/"+$stateParams.document_sid+"/item",itemsData,{headers:{"Auth-Session":sess}})
					.then(function(res){
					  rNS.showSuccessfulMessage('Success', 'Item/s has been added successfully.');
					  $modalInstance.dismiss();
					  $state.go($state.current, {}, {reload: true});
					});

				}

				else
				{
					rNS.showError('Error','Please select atleast one item first !');
				}
			});
		};
		// Method use to select text in textbox
		$scope.selectAllText = function(element){
			element.target.select();
		};

		// Method use to validate item quantity
		$scope.validateItemQty = function(newValue, item, str_qty){
			var oldQty = parseInt(angular.element(item.target).data('qty'));
			var newQty = parseInt(newValue);
			 if(newQty == 0 || newQty < 0)
			{
				rNS.showError('Error','Item quantity must be greater than zero !');
				item.target.value = oldQty;
				item.target.select();
			}

			if(newQty > str_qty)
			{
				rNS.showError('Error','Insufficient store on-hand quantity!');
				item.target.value = oldQty;
				item.target.select();
			}
		};


		// Method use to close modal dialog
		$scope.closeModalDialog = function(){
		   $modalInstance.dismiss();
		   $state.go($state.current, {}, {reload: true});
		};

		LoadingScreen.Enable = !0, magentoAPI().then(function(){
			LoadingScreen.Enable = !1;
		});

		if(!sessionStorage.getItem("items_access_token")){
			$scope.getAccessToken();
		}
		else{
			$scope.getCustomerDetails();
		}

}];

window.angular.module('prismPluginsSample.controller.shopJusticeCtrl', [])
   .controller('shopJusticeCtrl', shopJustice);

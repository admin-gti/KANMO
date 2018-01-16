(function(ng) {
  var dependencies = [];

  /*DO NOT MODIFY ABOVE THIS LINE!*/

  //Usage example:
  //dependencies.push('dependencyName');

  // Add/Remove Comments to Enable/Disable Cayan/Genius EFT Plugin
//  dependencies.push('prismPluginsSample.module.cayanRouteModule');
//  dependencies.push('prismPluginsSample.service.eftCayanService');
//  dependencies.push('prismPluginsSample.controller.cayanDeviceController');
//  dependencies.push('prismPluginsSample.controller.cayanGiftCardController');
//  dependencies.push('prismPluginsSample.controller.cayanSigCapController');
//  dependencies.push('prismPluginsSample.controller.cayanCancelController');

  // dependencies.push('prismPluginsSample.controller.employeeSearchSampleCtrl');
  // dependencies.push('prismPluginsSample.controller.customerSearchSampleCtrl');
  // dependencies.push('prismPluginsSample.controller.changeMultipleOrderTypeCtrl');

 // CHANGE SHIPPING ADDRESS
  // dependencies.push('prismPluginsSample.controller.changeShippingAddCtrl');

  // RETRIEVE AND DELETE CART, WISHLIST AND GIFT REGISTRY ITEMS API
  dependencies.push('prismPluginsSample.controller.shopJusticeCtrl');
  dependencies.push('prismPluginsSample.controller.customerListCtrl');
  dependencies.push('prismPluginsSample.controller.giftRegistryCtrl');

  // INSTALMENT PLUGIN
  // dependencies.push('prismPluginsSample.controller.instalmentCtrl');

  // E-RECEIPT PLUGIN
  // dependencies.push('prismPluginsSample.controller.eReceiptCtrl');

  // NORMAL DAMAGE PLUGIN
  dependencies.push('prismPluginsSample.controller.slipReasonCtrl'); 
  dependencies.push('prismPluginsSample.controller.itemCartonNumberCtrl'); 
  dependencies.push('prismPluginsSample.controller.cartonNumberCtrl'); 

//SET ORDER TYPE MULTIPLE
dependencies.push('prismPluginsSample.controller.changeMultipleOrderTypeCtrl');   

//ADDING FULFILLMENT LOCATION
//dependencies.push('prismPluginsSample.controller.addFulfillmentLocationCtrl');   

//ADDING FREIGHT CHARGES
//dependencies.push('prismPluginsSample.controller.applyFreightCtrl');   

//ADDING FREIGHT CHARGES
//dependencies.push('prismPluginsSample.controller.addAWBCtrl');   

//VOUCHER PLUGIN
dependencies.push('prismPluginsSample.controller.voucherCtrl');

//GIFT CARD PLUGIN  
dependencies.push('prismPluginsSample.controller.giftCardCtrl');
dependencies.push('prismPluginsSample.controller.redeemCardCtrl');
dependencies.push('prismPluginsSample.controller.validateCardCtrl');
dependencies.push('prismPluginsSample.controller.gcDenominationsCtrl');

//LOYALTY PLUGIN  
dependencies.push('prismPluginsSample.controller.loyaltyCtrl'); 

//CANCEL ORDER
dependencies.push('prismPluginsSample.controller.addCancelOrderCtrl');  

//FULFILLMENT AND FREIGHT
dependencies.push('prismPluginsSample.controller.fulfillmentandFreightCtrl');  

//CUSTOM LOOKUP
dependencies.push('prismPluginsSample.controller.customLookupCtrl');

//VIEW INVENTORY
// dependencies.push('prismPluginsSample.controller.viewInventoryCtrl');   

dependencies.push('prismPluginsSample.controller.changeShipmentStatusCtrl');   
dependencies.push('prismPluginsSample.controller.FAandDispatchPrintOutCtrl');   

//TRANSMARCO PLUGIN
//dependencies.push('prismPluginsSample.controller.transmarcoCtrl');

//SUMMARY REPORTS PLUGIN
//dependencies.push('prismPluginsSample.controller.summaryReportsCtrl');
  
  /*DO NOT MODIFY BELOW THIS LINE!*/
  ng.module('prismApp.customizations', dependencies, null);
})(angular);

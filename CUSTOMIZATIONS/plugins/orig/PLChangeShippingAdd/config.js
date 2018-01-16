SideButtonsManager.addButton({
    label: 'Change Shipping Address',
    sections: ['transactionRoot', 'transactionEdit', 'transactionedittender', 'transactionreturns', 'transactionview', 'transaction'],
    handler: ['$modal', function($modal) {

        var modalOptions = {
            backdrop: 'static',
            size: 'lg',
            // windowClass: 'width: 5000px',
            templateUrl: '/plugins/PLChangeShippingAdd/index.htm',
            controller: 'changeShippingAddCtrl'
        };

        $modal.open(modalOptions);
    }]
});

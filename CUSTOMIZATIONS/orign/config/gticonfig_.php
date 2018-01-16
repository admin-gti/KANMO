<?php 

    /**************************************************************************/
    /* CONFIG FILE FOR GTI USE                                                */
    /*                                                                        */
    /* CREATED BY: @ROCEE                                                     */
    /*MODIFIED BY:                                                            */
    /*                                                                        */
    /*                                                                        */
    /*--------------------------------END-------------------------------------*/

    error_reporting(E_ALL & ~E_NOTICE);
    
    require_once('adodb5/adodb.inc.php');
    
    define('SYSCONFIG_DBHOST','localhost');
    define('SYSCONFIG_DBUSER','root');
    define('SYSCONFIG_DBPASS','sysadmin');
    define('SYSCONFIG_DBNAME','rpsods');
    
    define('SYSCONFIG_DBUSER_ORACLE','reportuser');
    define('SYSCONFIG_DBPASS_ORACLE','report');
    define('SYSCONFIG_DBNAME_ORACLE','rproods');
    
    /*KANMO DIRECT ACCESS CONFIG----------------------------------------------*/
    define('SYSCONFIG_KANMO_USER','krgapi');
    define('SYSCONFIG_KANMO_PASS','Krg*yYdx%3Fegd!');
    define('SYSCONFIG_KANMO_GRANT','password');
    define('SYSCONFIG_KANMO_URL','https://kanmoapps.kanmoretail.com/boostAPI/');
    define('SYSCONFIG_KANMO_API_TOKEN','token');
    define('SYSCONFIG_KANMO_API_SHIPMENTRATE','api/shipment/ShipmentRate');
    /*------------------------------------------------------------------------*/
    
    /*OMS API ACCESS CONFIG---------------------------------------------------*/
    define('SYSCONFIG_OMS_URL','https://www.martjack.com/developerapi/');
    define('SYSCONFIG_OMS_MERCHANTID','1b3420ce-002f-4f66-bbda-cd0828aa2af8');
    define('SYSCONFIG_OMS_CONSUMERKEY','S9SUCAUJ');
    define('SYSCONFIG_OMS_CONSUMER_SECRETKEY','MPR6R3JEZOB1RMCPZKTV6SE8');
    define('SYSCONFIG_OMS_CREATESHIPMENT','Order/ship');
    define('SYSCONFIG_OMS_CREATEMANIFEST','Order/AddManifest/');
    define('SYSCONFIG_OMS_SAVEMERCHANT','order/SaveMerchantTransaction/');
    define('SYSCONFIG_OMS_AUTHORIZEORDER','order/Authorize');
    define('SYSCONFIG_OMS_UPDATESHIPMENTSTATUS','order/UpdateShipmentStatus/');
    define('SYSCONFIG_OMS_GETHISTORY','Order/History/');
    define('SYSCONFIG_OMS_GETINFO','order/');
    define('SYSCONFIG_OMS_CANCELORDER','Order/Cancel');
    define('SYSCONFIG_OMS_UPDATECOURIER','Order/UpdateShipmentCourierDetails/');
    define('SYSCONFIG_OMS_CHANGESUBSTATUS','order/ChangeSubStatus/');
    define('SYSCONFIG_OMS_FULFILLMENTLOCATION','Carts/GetCommonStoresforCartItems/');
    /*------------------------------------------------------------------------*/

    /*MAGENTO API ACCESS CONFIG---------------------------------------------------*/
    define('SYSCONFIG_MAGENTO_URL','http://staging.shopjustice.id/rest/V1/');
    define('SYSCONFIG_MAGENTO_DOMAIN','staging.shopjustice.id');
    define('SYSCONFIG_MAGENTO_USERID','pos');
    define('SYSCONFIG_MAGENTO_PASSKEY','abc12345');
    define('SYSCONFIG_MAGENTO_GETADMINTOKEN','integration/admin/token/');
    define('SYSCONFIG_MAGENTO_GETCUSTOMERDETAILS','customers/search/');
    define('SYSCONFIG_MAGENTO_ITEMS','customers/');
    define('SYSCONFIG_MAGENTO_GETCARTITEMS','carts/');
    define('SYSCONFIG_MAGENTO_GETWISHLISTITEMS','wishlists/');
    define('SYSCONFIG_MAGENTO_GETGIFTREGISTRYITEMS','gift-registries/');
    define('SYSCONFIG_MAGENTO_DELETEGIFTREGISTRYITEMS','gift-registry/fulfill-item/');
    /*------------------------------------------------------------------------*/

    /*CRM API ACCESS CONFIG---------------------------------------------------*/
    define('SYSCONFIG_CRM_URL','https://apac2.api.capillarytech.com/v1.1/');
    define('SYSCONFIG_CRM_DOMAIN','apac2.api.capillarytech.com');
    define('SYSCONFIG_CRM_USERID','kanmo.admin');
    define('SYSCONFIG_CRM_PASSKEY', md5('kanmo123'));
    define('SYSCONFIG_CRM_UPDATECUSTOMERDETAILS','customer/update?format=json');
    define('SYSCONFIG_CRM_CHECKVOUCHERSTATUS','coupon/isredeemable?format=json');
    define('SYSCONFIG_CRM_REDEEMVOUCHER','coupon/redeem?format=json');
    define('SYSCONFIG_CRM_CHECKPOINTSSTATUS','points/isredeemable?format=json');
    define('SYSCONFIG_CRM_ISSUEVALIDATIONCODE','points/validationcode?format=json');
    define('SYSCONFIG_CRM_REDEEMPOINTS','points/redeem?format=json');
    /*------------------------------------------------------------------------*/

    /*GIFT CARD API ACCESS CONFIG---------------------------------------------------*/
    define('SYSCONFIG_GIFTCARD_URL','https://apac2.api.capillarytech.com/api/loyalty/');
    define('SYSCONFIG_GIFTCARD_DOMAIN','apac2.api.capillarytech.com');
    define('SYSCONFIG_GIFTCARD_USERID','kanmo.admin');
    define('SYSCONFIG_GIFTCARD_PASSKEY', md5('kanmo123'));
    define('SYSCONFIG_GIFTCARD_RECHARGE_GC','rechargegiftcard.xml');
    define('SYSCONFIG_GIFTCARD_REDEEM_GC','redeemgiftcard.xml');
    define('SYSCONFIG_GIFTCARD_FETCH_GC','getgiftcardinfo.xml/');
    define('SYSCONFIG_GIFTCARD_GET_CUST_ID','customer/get?format=json&user_id=true');
    /*------------------------------------------------------------------------*/

    /*CUSTOMER DISPLAY CONFIG---------------------------------------------------*/
    define('SYSCONFIG_CUSTOMERDISPLAY_FILE_PATH','C:\\');
    /*------------------------------------------------------------------------*/
    
    define('SYSCONFIG_ENGINEDBPATH','C:\Program Files (x86)\Genie Technologies Inc\Kanmo Prism Interface\DB\prismSO_db.db');
    
    $conn  = NewADOConnection('mysqli');
    $sconn = NewADOConnection('sqlite3');
    $oconn = NewADOConnection('oci8');
    
    $sconn->PConnect(SYSCONFIG_ENGINEDBPATH);
    $conn->Connect(SYSCONFIG_DBHOST, SYSCONFIG_DBUSER, SYSCONFIG_DBPASS, SYSCONFIG_DBNAME);
    if (TRUE != $conn){
        $oconn->Connect(SYSCONFIG_DBHOST, SYSCONFIG_DBUSER_ORACLE, SYSCONFIG_DBPASS_ORACLE, SYSCONFIG_DBNAME_ORACLE);
    }
    
    date_default_timezone_set('Asia/Manila'); //TIMEZONE
    
    header('Access-Control-Allow-Origin: *'); 
?>
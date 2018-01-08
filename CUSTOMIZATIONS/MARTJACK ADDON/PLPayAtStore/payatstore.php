<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
//    echo "<pre>";
    if($_REQUEST['type'] == 1){
        
        $jsonVal = getJSONSaveMerchant($_REQUEST, $conn);
        header( 'Content-Type: application/json' );
        print json_encode(doSaveMerchantAPI($_REQUEST,$jsonVal));
        
    } else if($_REQUEST['type'] == 2){
        
        $jsonVal = doJSONAuthorizeOrder($_REQUEST, $conn);
        header( 'Content-Type: application/json' );
        print json_encode(doAuthorizeOrderAPI($_REQUEST,$jsonVal));
        
    }
    
    
    function getJSONSaveMerchant($data = null, &$conn){
        
        $sql = "SELECT "
            . "a.notes_general as OrderID "
            . ", a.tender_name "
            . ", a.due_amt "
            . "FROM "
            . "document a "
            . "WHERE a.sid = '".$data['sid']."'";
//    $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            $orderID = $rsResult->fields['OrderID'];
            $tender_name = $rsResult->fields['tender_name'];
            $Amount = $rsResult->fields['due_amt'];
        }

        /*-----------------GETTING SHIPMENT DETAILS-------------------------------*/
        
        $a['OrderId'] = $orderID;
        $a['PaymentOption'] = $tender_name;
        $a['PaymentType'] = "TPG";
        $a['ChannelType'] = "";
        $a['ResponseCode'] = "";
        $a['ResponseMessage'] = "";
        $a['pGReferenceID'] = "0255";
        $a['Amount'] = number_format($Amount, 0,'','');
        $a['Status'] = "A";
        
        $d['PaymentTransaction'] = $a;
        return json_encode($d, JSON_UNESCAPED_SLASHES);
        
    }
    
    function doSaveMerchantAPI($data = null, $infoData = null){
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
         CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_SAVEMERCHANT.SYSCONFIG_OMS_MERCHANTID."?oauth_consumer_key=".SYSCONFIG_OMS_CONSUMERKEY."&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "MerchantId=".SYSCONFIG_OMS_MERCHANTID."&InputFormat=application/json&InputData=".$infoData,
          CURLOPT_HTTPHEADER => array(
             "accept: application/json",
             "content-type: application/x-www-form-urlencoded"
          ),

        ));
        
        $err = curl_error($curl);
        $header = curl_getinfo($curl);
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
        
        $resp = json_decode($response, true);
        $info = json_decode($infoData, true);
        
        $resp['OrderID'] += $info['PaymentTransaction']['OrderId'];
        
        
        return $resp;
    }
    
    function doJSONAuthorizeOrder($data = NULL, &$conn){
        
        $a['merchantId'] = SYSCONFIG_OMS_MERCHANTID;
        $a['OrderId'] = $data['ustr'];
        $a['Date'] = date("Y-m-d", strtotime('now'));
        $a['Comment'] = "PRISM Testing";
        $a['PaymentType'] = "";
        $a['BankInstrumentNumber'] = "";
        $a['BankName'] = "";
        $a['PGResponse'] = "";
        
        $d['AuthorizeOrder'] = $a;
        return json_encode($d, JSON_UNESCAPED_SLASHES);
        
    }
    
    function doAuthorizeOrderAPI($data = null, $infoData = null){
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
         CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_AUTHORIZEORDER."?oauth_consumer_key=".SYSCONFIG_OMS_CONSUMERKEY."&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "MerchantId=".SYSCONFIG_OMS_MERCHANTID."&InputFormat=application/json&InputData=".$infoData,
          CURLOPT_HTTPHEADER => array(
             "accept: application/json",
             "content-type: application/x-www-form-urlencoded"
          ),

        ));
        
        $err = curl_error($curl);
        $header = curl_getinfo($curl);
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
        
        $resp = json_decode($response, true);
        $info = json_decode($infoData, true);
        
        $resp['OrderID'] += $info['PaymentTransaction']['OrderId'];
        
        return $resp;
    }
    
?>
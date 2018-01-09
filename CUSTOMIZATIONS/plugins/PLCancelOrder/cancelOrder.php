<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
//    echo "<pre>";
    if($_REQUEST['type'] == 1){
        
        header( 'Content-Type: application/json' );
        print json_encode(doGetHistory($_REQUEST));
        
    } else if($_REQUEST['type'] == 2){
        
        header( 'Content-Type: application/json' );
        print json_encode(doCancelOrder($_REQUEST));
        
    } 
    
    function doGetHistory($data = null){
        
        $infoData = SYSCONFIG_OMS_MERCHANTID."&OrderReferenceNo=".$data['sid']."&ToDate=".date('m/d/Y', strtotime('now'));
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_GETHISTORY.SYSCONFIG_OMS_MERCHANTID."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
        
        return json_decode($response,TRUE);
    }
    
    
    function doCancelOrder($data = null){
        
        $b = array();
        
        $a['merchantId'] = SYSCONFIG_OMS_MERCHANTID;
        $a['OrderId'] = $data['oid'];
        $a['Comment'] = $data['cc'];
        $a['PGResponse'] = "";
        $a['DisplayCommentToUser'] = "false";
        $a['date'] = date('Y-m-d g:i:s A');
        $a['CancelReason'] = $data['cr'];
        $a['CPUserID'] = SYSCONFIG_OMS_MERCHANTID;
        
        $infoData = json_encode($a, JSON_UNESCAPED_SLASHES);
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_CANCELORDER."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
        
        return json_decode($response, true);
    }
    
?>
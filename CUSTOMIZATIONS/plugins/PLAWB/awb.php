<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
//    echo "<pre>";
    if($_REQUEST['type'] == 1){
        
        header( 'Content-Type: application/json' );
        print json_encode(doFetchShipment($_REQUEST));
        
    } else if($_REQUEST['type'] == 2){
        
        header( 'Content-Type: application/json' );
        print json_encode(doUpdateCourier($_REQUEST));
        
    } else if($_REQUEST['type'] == 3){
        
        header( 'Content-Type: application/json' );
        print json_encode(doUpdateShipmentStat($_REQUEST));
        
    } else if($_REQUEST['type'] == 4){
        
        header( 'Content-Type: application/json' );
        print json_encode(CreateManifest($_REQUEST, $conn));
        
        
    }
    
    function doFetchShipment($data = null){
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
          
          CURLOPT_URL => SYSCONFIG_OMS_URL."Order/".SYSCONFIG_OMS_MERCHANTID."/".$data['id']."/Shipments?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
             "accept: application/json",
             "content-type: application/x-www-form-urlencoded"
          ),

        ));
        
        $err = curl_error($curl);
        $header = curl_getinfo($curl);
//        echo "<pre>"; 
//        print_r($header); 
//        echo "</pre>";
//        exit;
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
        
        return json_decode($response, true);
    }
    
    function doUpdateCourier($data = null){
        
        $a['ShipmentId'] = $data['sid'];
        $a['MerchantId'] = SYSCONFIG_OMS_MERCHANTID;
        $a['UserId'] = '00000000-0000-0000-0000-000000000000';
        $a['AWBNumber'] = $data['aw'];
        $a['IslabelReady'] = "false";
        $a['ProviderId'] = $data['cn'];
        
        $b = array();
        $b['Shipment'] = $a;
        
        $infoData = json_encode($b, JSON_UNESCAPED_SLASHES);
        
//        echo "<pre>";
//        print_r($infoData);
//        echo "</pre>";
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_UPDATECOURIER.SYSCONFIG_OMS_MERCHANTID."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
    
    function CreateManifest($data = null, &$conn = null){
        
        $sql = "SELECT * from store where sid = '".$data['store']."'";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            $locationID = $rsResult->fields['udf1_string'];
        }
        
        $c['ShipmentId'] = $data['sid'];
        
        $a['MerchantId'] = SYSCONFIG_OMS_MERCHANTID;
        $a['ShippingProvider'] = $data['cn'];
        $a['UserId'] = '';
        $a['LocationId'] = $locationID;
        $a['ChannelId'] = '0';
        $a['TripId'] = '';
        $a['items'] = $c;
        
        $d['Manifest'] = $a;
        
        $infoData =  json_encode($d, JSON_UNESCAPED_SLASHES);
        
//        echo "<pre>";
//        print_r($infoData);
//        echo "</pre>";
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_CREATEMANIFEST.SYSCONFIG_OMS_MERCHANTID."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
    
    function doUpdateShipmentStat($data = null){
        
        $a['ShipmentId'] = $data['data'];
        $a['Shippingstatus'] = 'R';
        $a['ShippingMessage'] = 'SO is ready for shipping';
        
        $d['UpdateShipmentStatus'] = $a;
        
        $infoData =  json_encode($d, JSON_UNESCAPED_SLASHES);
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_UPDATESHIPMENTSTATUS.SYSCONFIG_OMS_MERCHANTID."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
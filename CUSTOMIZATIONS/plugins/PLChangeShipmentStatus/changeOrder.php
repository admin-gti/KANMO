<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
//    echo "<pre>";
    if($_REQUEST['type'] == 1){
        
        $jsonVal = getJSONShipmentDetails($_REQUEST, $conn);
//        echo "<pre>";
//        print_r($jsonVal);
//        echo "</pre>";        
        header( 'Content-Type: application/json' );
        print json_encode(doCreateShipment($_REQUEST,$jsonVal));
        
    } else if($_REQUEST['type'] == 2){
        
        header( 'Content-Type: application/json' );
        print json_encode(doFetchShipment($_REQUEST));
        
    }  else if($_REQUEST['type'] == 3){
        
        $json_data = $_REQUEST['data'];
        $jsonVal = JSONUpdateShipmentStatus($json_data,$_REQUEST);
        
        header( 'Content-Type: application/json' );
        print json_encode(doUpdateShipmentStat($_REQUEST,$jsonVal));
        
    } else if($_REQUEST['type'] == 4){
        
        print doValidateData($_REQUEST, $conn);
        
    } else if($_REQUEST['type'] == 5){
        
        header( 'Content-Type: application/json' );
        print json_encode(doUpdateSubStatus($_REQUEST,$jsonVal,$conn));
    }
        
//    else if($_REQUEST['type'] == 2){
//        
//        $jsonVal = doJSONAuthorizeOrder($_REQUEST, $conn);
//        header( 'Content-Type: application/json' );
//        print json_encode(doAuthorizeOrderAPI($_REQUEST,$jsonVal));
//        
//    }
    
    function getJSONShipmentDetails($data = null, &$conn){
        
        $sql = "SELECT "
            . "a.notes_general as OrderID "
            . ", d.store_code LocationCode "
            . ", a.ref_order_sid reference_sid "
            . "FROM "
            . "document a "
            . "LEFT JOIN document_item c ON (a.sid = c.doc_sid) "
            . "LEFT JOIN store d ON (c.fulfill_store_sid = d.sid) "
            . "WHERE note8 = 'PICKUP' AND a.sid = '".$data['sid']."' group by a.sid";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            $a['OrderId'] = $rsResult->fields['OrderID'];
            $a['LocationRefCode'] = $rsResult->fields['LocationCode'];
            $lineSID = $rsResult->fields['reference_sid'];
        }

        /*-----------------GETTING SHIPMENT DETAILS-------------------------------*/
        $a['ShipDate'] = date('Y/m/d', strtotime('now'));
        $a['ShipmentType'] = "normal";
        $a['CourierName'] = 'Custom';
        $a['AWBNumber'] = 0;
            

        $sql1 = "SELECT "
                . "a.note9 OrderLineID "
                . ", b.text2 Weight "
                . ", a.qty "
                . "FROM "
                . "document_item a "
                . "LEFT JOIN invn_sbs_item b ON (a.invn_sbs_item_sid = b.sid) "
                . "where a.doc_sid = '".$data['sid']."'";
//        echo $sql1."\r\n";
        $rsResult1 = $conn->Execute($sql1);
        while(!$rsResult1->EOF){
            $b['OrderLineId'] = $rsResult1->fields['OrderLineID'];
//            $b['Weight'] = $rsResult1->fields['Weight'];
            $b['Weight'] = '60';
            $b['Quantity'] = number_format($rsResult1->fields['qty'], 0,'','');
            
            $rsResult1->MoveNext();
        }


        $e = array();
        $e[] = $b;

        $a['lineitems'] = $e;

        $d['shipment'] = $a;

        $c['MerchantId'] = SYSCONFIG_OMS_MERCHANTID;

        $d['shipment'] += $c;
//        
//        echo "<pre>";
//        print_r($d);
//        echo "</pre>";
        return json_encode($d, JSON_UNESCAPED_SLASHES);
        
    }
    
     function doCreateShipment($data = null, $infoData = null){
        
//        echo $infoData."</br>";  
         
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
         CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_CREATESHIPMENT."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
        
        $resp['OrderID'] += $info['shipment']['OrderId'];
        
        
        return $resp;
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
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
//        echo "<pre>";
//        print_r($response);
//        echo "</pre>";
        return json_decode($response, true);
    }
    
    function JSONUpdateShipmentStatus($data = NULL,$req = NULL){
        
        $a['ShipmentId'] = $data;
        if($req['stat'] == 'DP'){
            $a['Shippingstatus'] = 'R';
            $a['ShippingMessage'] = 'Item is ready to shipped';
        } else {
            $a['Shippingstatus'] = 'D';
            $a['ShippingMessage'] = 'Item is delivered';
        }
        
        
        $d['UpdateShipmentStatus'] = $a;
        
        return json_encode($d, JSON_UNESCAPED_SLASHES);
        
    }
    
    function doUpdateShipmentStat($data = null, $infoData = null){
        
        
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
    
    function doUpdateSubStatus($data = null, $infoData = null, &$conn){
        
        $a['SubStatus'] = $data['stat'];
        $a['OperatorId'] = '';
        
        $d['ChangeSubStatus'] = $a;
        print_r($data);
        $infoData = json_encode($d, JSON_UNESCAPED_SLASHES);
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_CHANGESUBSTATUS.SYSCONFIG_OMS_MERCHANTID.'/'.$data['id']."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
//        echo SYSCONFIG_OMS_URL.SYSCONFIG_OMS_CHANGESUBSTATUS.SYSCONFIG_OMS_MERCHANTID.'/'.$data['id']."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0";
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
        
        print_r($response);
        
        $conn->debug = TRUE;
        $sql = "SELECT sid from document where notes_general LIKE '%".$data['id']."%'";
        $rsResult = $conn->Execute($sql);
        
        if(!$rsResult->EOF){
        
            $sql = "UPDATE "
                    . "document SET "
                    . "udf2_string = '".$data['stat']."' "
                    . "WHERE sid = '".$rsResult->fields['sid']."'";
            $conn->debug = TRUE;
            $conn->Execute($sql);
        }
        
        
        return json_decode($response, true);
    }
    
    function doValidateData($data = NULL, &$conn){
        
        $sql = "SELECT COUNT(*) cnt FROM document_item WHERE UCASE(note8) = 'PICKUP' AND doc_sid = '{$data['sid']}' GROUP BY doc_sid";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['cnt'] > 0) {
                return '1';
            } else {
                return '0';
            }
        } else {
            return '0';
        }
        
    }
    
?>
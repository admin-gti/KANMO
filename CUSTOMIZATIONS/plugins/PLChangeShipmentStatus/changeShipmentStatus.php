<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
    if($_REQUEST['type'] == 1){
          
        header( 'Content-Type: application/json' );
        print json_encode(doGetHistory($_REQUEST));
        
    }else if($_REQUEST['type'] == 2){
          
        $data = doGetInfo($_REQUEST);
        doSaveOMSInfo($data, $_REQUEST, $conn);
        
    }else if($_REQUEST['type'] == 3){
          
        $jsonVal = getJSONShipmentDetails($_REQUEST, $conn);
//        echo "<pre>";
//        print_r($jsonVal);
//        echo "</pre>";
        header( 'Content-Type: application/json' );
        print json_encode(doCreateShipment($_REQUEST,$jsonVal));
        
    }else if($_REQUEST['type'] == 4){
          
        header( 'Content-Type: application/json' );
        print json_encode(doFetchShipment($_REQUEST));
        
    }else if($_REQUEST['type'] == 5){
          
        $json_data = $_REQUEST['data'];
        $jsonVal = JSONUpdateShipmentStatus($json_data);
        
        header( 'Content-Type: application/json' );
        print json_encode(doUpdateShipmentStat($_REQUEST,$jsonVal));
        
    } else if($_REQUEST['type'] == 6){
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidateSO($_REQUEST,$jsonVal));
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
    
    function doGetInfo($data = null){
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_GETINFO.SYSCONFIG_OMS_MERCHANTID.'/'.$data['oid']."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
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
        $response = curl_exec($curl);
        echo $response;
        
        curl_close($curl);
        
        if ($err) {
          $response = "cURL Error #:" . $err;
        } else {
          $response;
        }
        
        return json_decode($response,TRUE);
    }
    
    function doSaveOMSInfo($data = NULL, $req = null, &$conn = null){
        
        $sql = "UPDATE "
                . "document SET "
                . "notes_general = '".$data['Orders'][0]['OrderId']."' "
                . ",udf2_string = 'WP' "
                . "WHERE sid = '".$req['sid']."'";
//        $conn->debug = TRUE;
        $conn->Execute($sql);
        
        foreach($data['Orders'][0]['OrderLineId'] as $key => $value) {
            
            $sql = "UPDATE "
                    . "document_item SET "
                    . "note9 = '".$value['OrderLineId']."' "
                    . "WHERE alu = '".$value['SKU']."' "
                    . "and doc_sid = '".$req['sid']."'";
            $conn->Execute($sql);
            
        }
    }
    
    function getJSONShipmentDetails($data = null, &$conn){
        
        $sql = "SELECT "
            . "a.notes_general as OrderID "
            . ", d.store_code LocationCode "
            . ", a.ref_order_sid reference_sid "
            . "FROM "
            . "document a "
            . "LEFT JOIN document_item c ON (a.sid = c.doc_sid) "
            . "LEFT JOIN store d ON (c.fulfill_store_sid = d.sid) "
            . "WHERE 1=1 "
            . "AND a.sid = '".$data['sid']."' "
            . "group by a.sid";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['OrderID'] != ''){
                $a['OrderId'] = $rsResult->fields['OrderID'];
            } else {
                $a['OrderId'] = $data['oid'];
            }
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
            $b['Weight'] = 60;
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
        
//        echo "<pre>";
//        print_r(SYSCONFIG_OMS_URL.SYSCONFIG_OMS_CREATESHIPMENT."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0");
//        echo "</pre>";
//        echo "<br/>";
//        echo "<br/>";
//        echo "<br/>";
//        echo "<pre>";
//        print_r("MerchantId=".SYSCONFIG_OMS_MERCHANTID."&InputFormat=application/json&InputData=".$infoData);
//        echo "</pre>";
//        exit;
        
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
        
        return json_decode($response, true);
    }
    
    function JSONUpdateShipmentStatus($data = NULL){
        
        $a['ShipmentId'] = $data;
        $a['Shippingstatus'] = 'D';
        $a['ShippingMessage'] = 'Item has already pickup by customer';
        
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
    
    function doValidateData($data = NULL, &$conn){
        
        $sql = "SELECT COUNT(*) cnt FROM document_item WHERE note8 = 'PICKUP' AND doc_sid = '{$data['sid']}' GROUP BY doc_sid";
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
    
    function doValidateSO($data = NULL, &$conn){
        
        $sql = "SELECT COUNT(*) cnt FROM document WHERE 1=1 AND ref_doc_sid = '{$data['sid']}' GROUP BY doc_sid";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['cnt'] > 0) {
                $response['isref'] = '1';
            } else {
                $response['isref'] = '0';
            }
        } else {
            $response['isref'] = '0';
        }
        return $response;
        
    }
    
?>
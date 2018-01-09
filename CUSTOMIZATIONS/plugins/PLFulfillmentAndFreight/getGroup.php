<?php
    error_reporting( E_ALL & ~E_NOTICE );
    
    require_once('../../config/adodb5/adodb.inc.php');
    session_start();
    require_once('../../config/gticonfig.php');
    
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => SYSCONFIG_KANMO_URL.SYSCONFIG_KANMO_API_TOKEN,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "username=".SYSCONFIG_KANMO_USER."&password=".SYSCONFIG_KANMO_PASS."&grant_type=".SYSCONFIG_KANMO_GRANT,
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          $resp = json_decode($response);
        }
        $_SESSION['kanmo_acctoken'] = $resp->access_token;
        $_SESSION['kanmo_expires'] = strtotime($resp->{".expires"});
        
//    }
//    echo "</pre>";
//    print_r($_REQUEST);
//    echo "</pre>";
//    exit;
    
    if (TRUE == $conn){
       $sql = "select "
               . "a.* "
               . ", text1 height "
               . ", text2 weight "
               . ", text3 width "
               . ", text4 length "
               . ", a.note8 "
               . ", a.note10 "
               . "from document_item a "
               . "LEFT JOIN document c ON (a.doc_sid = c.sid) "
               . "LEFT JOIN invn_sbs_item b on (a.invn_sbs_item_sid = b.sid and b.sbs_sid = c.subsidiary_sid) "
               . "where 1=1 "
               . "and item_type = '3' "
               . "and note8 = 'HOME DELIVERY' "
               . "and doc_sid = '{$_GET['sid']}'";
//    $conn->debug = true;
        $arr = array();
        $ctr = 0;
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['note8']            = $rsResult->fields['note8']; 
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['note10']           = $rsResult->fields['note10']; 
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['city']             = $rsResult->fields['st_address_line3']; //CONSIDER AS CITY; GROUP AS CITY
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line1'] = $rsResult->fields['st_address_line1'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line2'] = $rsResult->fields['st_address_line2']; 
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line4'] = $rsResult->fields['st_address_line4'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line5'] = $rsResult->fields['st_address_line5'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line6'] = $rsResult->fields['st_address_line6'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_country']       = $rsResult->fields['st_country'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_postal_code']   = $rsResult->fields['st_postal_code'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['alu']          = $rsResult->fields['alu'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['description1'] = $rsResult->fields['description1'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['description2'] = $rsResult->fields['description2'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['qty']          = number_format($rsResult->fields['qty'],0);
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['sid']          = $rsResult->fields['sid'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['doc_sid']      = $rsResult->fields['doc_sid'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['height']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['height']       = $rsResult->fields['height'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['weight']       = $rsResult->fields['weight'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['width']        = $rsResult->fields['width'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['length']       = $rsResult->fields['length'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['note8']        = $rsResult->fields['note8'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['note10']       = $rsResult->fields['note10'];
            $rsResult->Movenext();
            $ctr++;
    //       $arr;

        }
        unset($sql);
        unset($rsResult);
        $sql = "SELECT address2 FROM store WHERE sid = '".$_GET['ssid']."'";
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            $store = $rsResult->fields['address2'];
        }
    } else {
        
        $sql = "select "
               . "a.* "
               . ", text1 height "
               . ", text2 weight "
               . ", text3 width "
               . ", text4 length "
               . ", a.note8 "
               . ", a.note10 "
               . "from rps.document_item a "
               . "LEFT JOIN rps.document c ON (a.doc_sid = c.sid) "
               . "LEFT JOIN rps.invn_sbs_item b on (a.invn_sbs_item_sid = b.sid and b.sbs_sid = c.subsidiary_sid) "
               . "where 1=1 "
               . "and item_type = '3' "
               . "and note8 = 'HOME DELIVERY' "
               . "and doc_sid = '{$_GET['sid']}'";
//    $conn->debug = true;
        $arr = array();
        $ctr = 0;
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['note8']            = $rsResult->fields['note8']; 
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['note10']           = $rsResult->fields['note10']; 
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['city']             = $rsResult->fields['st_address_line3']; //CONSIDER AS CITY; GROUP AS CITY
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line1'] = $rsResult->fields['st_address_line1'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line2'] = $rsResult->fields['st_address_line2']; 
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line4'] = $rsResult->fields['st_address_line4'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line5'] = $rsResult->fields['st_address_line5'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_address_line6'] = $rsResult->fields['st_address_line6'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_country']       = $rsResult->fields['st_country'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['st_postal_code']   = $rsResult->fields['st_postal_code'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['alu']          = $rsResult->fields['alu'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['description1'] = $rsResult->fields['description1'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['description2'] = $rsResult->fields['description2'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['qty']          = number_format($rsResult->fields['qty'],0);
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['sid']          = $rsResult->fields['sid'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['doc_sid']      = $rsResult->fields['doc_sid'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['height']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['height']       = $rsResult->fields['height'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['weight']       = $rsResult->fields['weight'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['width']        = $rsResult->fields['width'];
            $arr[$rsResult->fields['note10']][$rsResult->fields['st_address_line3']]['items'][$ctr]['length']       = $rsResult->fields['length'];
            $rsResult->Movenext();
            $ctr++;
    //       $arr;

        }
        unset($sql);
        unset($rsResult);
        $sql = "SELECT address2 FROM rps.store WHERE sid = '".$_GET['ssid']."'";
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            $store = $rsResult->fields['address2'];
        }
    }
    
//    echo "<pre>";
//    print_r($arr);
//    echo "</pre>";
//    exit;
    
    $a=0;
    $ary = array();
    foreach($arr as $key => $value){
        $b=0;
        
        foreach($value as $keyA => $valueA){
            
            $ary[$a]['city']   = $_REQUEST['selc'];
            $ary[$a]['note8']  = $valueA['note8'];
            $ary[$a]['note10'] = $valueA['note10'];
           
            foreach($valueA['items'] as $keyD => $valueD){
                
                $height += $height + $valueC['height'];
                $weight += $weight + $valueC['weight'];
                $width  += $width + $valueC['width'];
                $length += $length + $valueC['length'];
            }

            $json = "{"; 
            $json .= "\"shipFrom\": \"".$store."\","; 
            $json .= "\"shipTo\": \"".$_REQUEST['selc']."\",";
            $json .= "\"itemDimension\": {";
            $json .= "\"width\": \"".$width."\","; 
            $json .= "\"height\": \"".$height."\","; 
            $json .= "\"length\": \"".$length."\","; 
            $json .= "\"weight\": \"".$weight."\""; 
            $json .= "}}"; 
            
            $curlA = curl_init();
            
//            echo "<pre>";
//            print_r($json);
//            echo "</pre>";
//            exit;
            
            curl_setopt_array($curlA, array(
              CURLOPT_URL => SYSCONFIG_KANMO_URL.SYSCONFIG_KANMO_API_SHIPMENTRATE,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
              CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $json,
//              CURLOPT_POSTFIELDS => "{\"shipFrom\": \"Jakarta\",\"shipTo\": \"Bandung\",\"itemDimension\": {\"width\": \"100.0\",\"height\": \"120.0\",\"length\": \"70.0\",\"weight\": \"250.0\"}}",
              CURLOPT_HTTPHEADER => array(
                "authorization: Bearer ".$_SESSION['kanmo_acctoken'],
                "cache-control: no-cache",
                "content-type: application/json"
              ),
            ));

            $responses = curl_exec($curlA);
            $errs = curl_error($curlA);

            curl_close($curlA);

            if ($errs) {
//              echo "cURL Error #:" . $errs;
                $ary['error'] = 'Connection Timeout';
            } else {
                $shiprate = json_decode($responses,TRUE);
                
//                var_dump($responses);
            }
           
//            echo "<pre>";
//            print_r($responses);
//            echo "</pre>";
            $ary[$a]['freight'] = $shiprate;
            if($_REQUEST['rate'] != ''){
                $totalfreight = ($_REQUEST['rate'])/(count($valueA['items']));
            }
            
            $d=0;
            $str_ = 0;
            
            
            foreach($valueA['items'] as $keyC => $valueC){
                
                $ary[$a]['items'][$d]['alu']          = $valueC['alu'];
                $ary[$a]['items'][$d]['description1'] = $valueC['description1'];
                $ary[$a]['items'][$d]['description2'] = $valueC['description2'];
                $ary[$a]['items'][$d]['qty']          = $valueC['qty'];
                $ary[$a]['items'][$d]['sid']          = $valueC['sid'];
                $ary[$a]['items'][$d]['doc_sid']      = $valueC['doc_sid'];
                $ary[$a]['items'][$d]['row_version']  = $valueC['row_version'];
//                $ary[$a]['items'][$d]['freight']      = $totalfreight;
                $ary[$a]['items'][$d]['freight']      = $totalfreight;
                
                if($str_ == $b){
                    $d = $d + 1;
                } else {
                    $d = 0;
                }
                
                $str_ = $b;
                
                
            }
            $ary[$a]['freight_total'] = ($shiprate['value']['shippingRate']);
            $a++;
        }
        
    }
    
    header( 'Content-Type: application/json' );
    echo json_encode($ary);
?>
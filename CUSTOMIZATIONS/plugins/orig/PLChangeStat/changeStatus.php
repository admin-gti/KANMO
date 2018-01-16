<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
    if($_REQUEST['type'] == 1){
          
        $jsonVal = getJSONShipmentDetails($_REQUEST, $conn, $sconn);
        
    }else if($_REQUEST['type'] == 4){
          
        header( 'Content-Type: application/json' );
        print json_encode(doFetchShipment($_REQUEST));
        
    }else if($_REQUEST['type'] == 5){
          
        $json_data = $_REQUEST['data'];
        $jsonVal = JSONUpdateShipmentStatus($json_data);
        
        header( 'Content-Type: application/json' );
        print json_encode(doUpdateShipmentStat($_REQUEST,$jsonVal));
        
    } 
    
    function getJSONShipmentDetails($data = null, &$conn, &$sconn){
        
         $sql = "SELECT "
            . "a.notes_general as OrderID "
//            . ", a.orig_store_code LocationCode "
            . ", a.store_code LocationCode "
            . ", a.sid reference_sid "
            . ", c.note8 "
            . ", c.note10 "
            . ", a.notes_order "
            . "FROM "
            . "document a "
            . "LEFT JOIN document_item c ON (a.sid = c.doc_sid) "
            . "LEFT JOIN store d ON (c.fulfill_store_sid = d.sid) "
            . "WHERE 1=1 AND a.sid = '".$data['sid']."' group by a.sid";
//        $conn->debug = true;
         $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['note10'] == 'SAME STORE'){
                $a['OrderId'] = NULL;
            } else {
                $a['OrderId'] = $rsResult->fields['OrderID'];
            }
            $a['LocationRefCode'] = $rsResult->fields['LocationCode'];
            $a['OrderReferenceNo'] = $rsResult->fields['notes_order'];
            $type = $rsResult->fields['note8'];
            $lineSID = $rsResult->fields['reference_sid'];
        }

        /*-----------------GETTING SHIPMENT DETAILS-------------------------------*/
            

        $sql1 = "SELECT "
                . "a.note9 OrderLineID "
                . ", b.text2 Weight "
                . ", a.qty "
                . "FROM "
                . "document_item a "
                . "LEFT JOIN invn_sbs_item b ON (a.invn_sbs_item_sid = b.sid) "
                . "where a.doc_sid = '".$lineSID."'";
//        echo $sql1."\r\n";
        $rsResult1 = $conn->Execute($sql1);
        $b = array();
        while(!$rsResult1->EOF){
            $b['OrderLineId'] = $rsResult1->fields['OrderLineID'];
            $b['Weight'] = $rsResult1->fields['Weight'];
//            $b['Weight'] = '60';
            $b['Quantity'] = number_format($rsResult1->fields['qty'], 0,'','');
            
            $rsResult1->MoveNext();
            
            $e[] = $b;
        }


//        $e = array();
//        $e[] = $e;

        $a['lineitems'] = $e;

        $d['shipment'] = $a;

        $c['MerchantId'] = SYSCONFIG_OMS_MERCHANTID;

        $d['shipment'] += $c;
        
//        echo "<pre>";
//        print_r($a);
//        echo "</pre>";
        $qlite = "INSERT INTO pd_upload ("
               . "pdu_type, "
               . "pdu_header, "
               . "pdu_body) "
               . "VALUES ( "
               . "'WP', "
               . "'".$type."', "
               . "'".json_encode($a)."'"
               . ")";
//            echo $qlite;
          $sconn->debug = true;  
        $sconn->Execute($qlite);
        
        
        $sql = "UPDATE document SET udf2_string = 'WP' WHERE sid = '".$lineSID."'";
        $conn->Execute($sql);
//        return json_encode($d);
        
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
    
?>
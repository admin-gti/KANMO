<?php
    session_start();
    require_once('../../config/gticonfig.php');
//    $conn->debug = TRUE;
    
    if($_REQUEST['type'] == 1) { //VALIDATE SHIPMENT STATUS
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidateShipmentStat($_REQUEST,1,$conn));
        
    } else if($_REQUEST['type'] == 2) { //VALIDATE SOURCE SO
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidateSourceSO($_REQUEST, $conn));    
    
    } else if($_REQUEST['type'] == 3) { //VALIDATE COUNT SO
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidateCountSO($_REQUEST, $conn));
        
    } else if($_REQUEST['type'] == 4) { 
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidateForFulfillmentandFreight($_REQUEST, $conn));
        
    } else if($_REQUEST['type'] == 5) { 
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidateOrderFulfill($_REQUEST, $conn));
        
    } else if($_REQUEST['type'] == 6) { 
        
        header( 'Content-Type: application/json' );
        print json_encode(doValidatePayAtStore($_REQUEST, $conn));
        
    }
    
    function doValidateShipmentStat($data = null, $category = null, &$conn) {
        
        $response = array();
        
        if($category == 1) {// BEFORE DISPATCHED, NOT ALLOWING INVOICE TO CONTINUE ONCE AWB IS NOT SUPPLIED (HOME DELIVERY => SAME STORE)
            
            $sql = "select 
                    COUNT(b.sid) cnt
                    from
                    document a 
                    left join document b on (a.ref_order_sid = b.sid)
                    left join document_item c on (b.sid = c.doc_sid)
                    where 1=1
                    and a.sid = '{$data['sid']}'
                    and note8 = 'HOME DELIVERY'
                    and note10 in ('SAME STORE', 'DIFFERENT STORE')
                    and b.udf2_string = 'WAITING FOR PICKUP'
                    and a.udf2_string != 'DISPATCHED'
                    "
                    ;
//            $conn->debug = TRUE;   
            $rsResult = $conn->Execute($sql);
            if(!$rsResult->EOF){
                if($rsResult->fields['cnt'] > 0){
                $response['dispatched'] = "Please provide the shipment details since this is for home delivery.";
                } else {
                    $response['dispatched'] = '';
                }
            }
            
        }
        return $response;
        
    }
    
    function doValidateSourceSO($data = null, &$conn) {
        
        $response = array();
            
        $sql = "select 
                COUNT(a.sid) cnt
                from
                document a 
                where 1=1
                and a.sid = '{$data['sid']}'
                and a.notes_order = 'SOURCE'
                ";
//            $conn->debug = TRUE;   
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['cnt'] > 0){
            $response['source'] = "This is source SO and cannot be fulfilled.";
            } else {
                $response['source'] = '';
            }
        }
            
        return $response;
        
    }
    
    function doValidateCountSO($data = null, &$conn) {
        
        $response = array();
            
        $sql = "select 
                COUNT(a.sid) cnt
                from
                document_item a 
                where 1=1
                and a.doc_sid = '{$data['sid']}'
                ";
//            $conn->debug = TRUE;   
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['cnt'] <= 0){
                $response['cnt'] = "Add items first";
            } else {
                $response['cnt'] = '';
            }
        }
            
        return $response;
        
    }
    
    function doValidateForFulfillmentandFreight($data = null, &$conn) {
        
        $response = array();
            
        $sql = "SELECT 
                (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'HOME DELIVERY') homedelivery
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'HOME DELIVERY' and b.note10 = 'DIFFERENT STORE') homedeliverydiff
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'PICKUP' and b.note10 = 'DIFFERENT STORE') pickupdiff
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'HOME DELIVERY' and b.note10 = 'SAME STORE') homesame
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note10 = 'DIFFERENT STORE') diffstore
                FROM 
                document a
                WHERE 1=1
                and a.status = 3
                and a.sid = '{$_GET['sid']}'";
//        $conn->debug = TRUE;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['homedelivery'] > 0){
                $arr['homedelivery'] = 1;
            } else {
                $arr['homedelivery'] = 0;
            }
            if($rsResult->fields['diffstore'] > 0){
                $arr['diffstore'] = 1;
            } else {
                $arr['diffstore'] = 0;
            }
            if($rsResult->fields['homedeliverydiff'] > 0){
                $arr['homedeliverydiff'] = 1;
            } else {
                $arr['homedeliverydiff'] = 0;
            }
            if($rsResult->fields['pickupdiff'] > 0){
                $arr['pickupdiff'] = 1;
            } else {
                $arr['pickupdiff'] = 0;
            }
            if($rsResult->fields['homesame'] > 0){
                $arr['homesame'] = 1;
            } else {
                $arr['homesame'] = 0;
            }
            
        } else {
            $arr['homedelivery'] = 0; 
            $arr['diffstore'] = 0; 
            $arr['homedeliverydiff'] = 0; 
            $arr['pickupdiff'] = 0; 
            $arr['homesame'] = 0; 
        }
        return $arr;
    }
    
    function doValidateOrderFulfill($data = null, &$conn) {
        
        $response = array();
            
        $sql = "select 
                count(sid) cnt 
                from 
                document_item 
                where 1=1
                and item_type = 3
                and (note8 is null AND note10 is null)
                and doc_sid = '{$data['sid']}'
                ";
//            $conn->debug = TRUE;   
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['cnt'] > 0){
                $response['ofull'] = "Item/s with SO type should have order and fulfillment type";
            } else {
                $response['ofull'] = '';
            }
        }
            
        return $response;
        
    }
    
    function doValidatePayAtStore($data = null, &$conn){
        $sql = "select count(sid) cnt from document_item where note6 = 'PayAtStore' and doc_sid = '{$data['sid']}'";
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['cnt'] > 0) {
                $response['payatstore'] = 1;
            } else {
                $response['payatstore'] = 0;
            }
        } else {
            $response['payatstore'] = 0;
        }
        return $response;
    }
    
?>
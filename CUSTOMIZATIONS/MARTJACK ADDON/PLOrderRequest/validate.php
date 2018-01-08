<?php
//. "and a.sid = '{$_GET['docsid']}'";
    session_start();
    require_once('../../config/gticonfig.php');
    
    if (TRUE == $conn){

        $sql = "SELECT 
                (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'PICKUP' and b.note10 = 'SAME STORE') psamestore
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'HOME DELIVERY' and b.note10 = 'SAME STORE') hsamestore
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'PICKUP' and b.note10 = 'DIFFERENT STORE') pdiffstore
                , (SELECT count(sid) cnt FROM document_item b WHERE a.sid = b.doc_sid and b.item_type = 3 and b.note8 = 'HOME DELIVERY' and b.note10 = 'DIFFERENT STORE') hdiffstore
                FROM 
                document a
                WHERE 1=1
                and a.status = 3
                and a.sid = '{$_GET['docsid']}'";
        $ctr = 0;
        $ordertype   = '';
        $fulfilltype = '';

        $arr = array();
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        if(!$rsResult->EOF){
            if($rsResult->fields['psamestore'] > 0 && $rsResult->fields['hsamestore'] == 0 && $rsResult->fields['pdiffstore'] == 0 && $rsResult->fields['hdiffstore'] == 0){
                $arr['val'] = 0; //SINGLE ---IGNORE
            } else if($rsResult->fields['psamestore'] == 0 && $rsResult->fields['hsamestore'] > 0 && $rsResult->fields['pdiffstore'] == 0 && $rsResult->fields['hdiffstore'] == 0){
                $arr['val'] = 0; //SINGLE ---IGNORE
            }else if($rsResult->fields['psamestore'] == 0 && $rsResult->fields['hsamestore'] == 0 && $rsResult->fields['pdiffstore'] == 0 && $rsResult->fields['hdiffstore'] == 0){
                $arr['val'] = 0; //SINGLE ---IGNORE
            }else {
                $arr['val'] = 1; //MULTIPLE
            }
        } else {
            $arr['val'] = 0; //IGNORE
        }
    } 
    
    header( 'Content-Type: application/json' );
    print json_encode($arr);
?>
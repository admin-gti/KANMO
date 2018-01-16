<?php
    session_start();
    require_once('../../config/gticonfig.php');

    if($_REQUEST['type'] == 1){

        header( 'Content-Type: application/json' );
        print json_encode(doCustomLookup($_REQUEST, $conn));

    }

    function doCustomLookup($data = null, &$conn){

        $sFilter .= "status IN (3, 4)";
        if($data['notes_order']!=''){
            $sFilter .= " AND notes_general like '%".$data['notes_order']."%' ";
        }
        if($data['notes_order']==''&&$data['notes_general']==''){
            $sFilter .= " AND  (date_format(".$data['category'].",'%Y-%m-%d') BETWEEN '". date('Y-m-d',strtotime($data['datefrom']))."' AND '". date('Y-m-d',strtotime($data['dateto']))."') ";
        }

        if($data['store_uid']!=''){
            $sFilter .= " AND store_uid=".$data['store_uid'];
        }
        if($data['workstation_uid']!=''){
            $sFilter .= " AND workstation_uid=".$data['workstation_uid'];
        }
        if($data['notes_general']!=''){
            $sFilter .= "AND notes_general like '%".$data['notes_general']."%'";
        }
        if($data['order_doc_no']!=''){
            $sFilter .= " AND order_doc_no=".$data['order_doc_no'];
        }
        if($data['bt_last_name']!=''){
            $sFilter .= " AND bt_last_name=".$data['bt_last_name'];
        }

         $sql = "SELECT "
            . "* "
            . "FROM "
            . "document a "
            . "LEFT JOIN document_item c ON (a.sid = c.doc_sid) "
            . "WHERE 1=1 AND ".html_entity_decode($sFilter)." ORDER BY a.created_datetime desc ";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        $a = 0;
        while(!$rsResult->EOF){
            $arr[$a]['sid'] = $rsResult->fields['doc_sid'];
            $decode = json_decode($rsResult->fields['notes_general'],TRUE);
            
            $arr[$a]['notes_order']           = $rsResult->fields['notes_order'];
            $arr[$a]['ordered_date']          = $rsResult->fields['ordered_date'];
            $arr[$a]['notes_order']           = $decode['referenceNo'];
            $arr[$a]['document_number']       = $rsResult->fields['doc_no'];
            $arr[$a]['bt_first_name']         = $rsResult->fields['bt_first_name'];
            $arr[$a]['bt_last_name']          = $rsResult->fields['bt_last_name'];
            $arr[$a]['order_document_number'] = $rsResult->fields['order_doc_no'];
            $arr[$a]['notes_general']         = $decode['omsID'];
            $arr[$a]['store_number']          = $rsResult->fields['store_no'];
            $arr[$a]['store_code']            = $rsResult->fields['store_code'];
            $arr[$a]['transaction_total_amt'] = $rsResult->fields['transaction_total_amt'];
            $arr[$a]['original_store_code']   = $rsResult->fields['orig_store_code'];
            $arr[$a]['udf_string2']           = $decode['status'];
            $rsResult->MoveNext();
            $a++;
        }
        return $arr;

    }

?>
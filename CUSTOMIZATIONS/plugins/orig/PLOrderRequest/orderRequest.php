<?php
//. "and a.sid = '{$_GET['docsid']}'";
    session_start();
    require_once('../../config/gticonfig.php');
    
//    $conn->debug = true;
    
    if (TRUE == $conn){
        $flds[] = "a.order_doc_no";
        $flds[] = "a.created_datetime ordered_date";
        $flds[] = "a.bt_cuid bt_cuid";
        $flds[] = "a.bt_id bt_id";
        $flds[] = "a.bt_first_name bt_firstname";
        $flds[] = "a.bt_last_name bt_lastname";
        $flds[] = "a.bt_address_line1 bt_address1";
        $flds[] = "a.bt_address_line2 bt_address2";
        $flds[] = "a.bt_address_line3 bt_city";
        $flds[] = "a.bt_address_line4 bt_state";
        $flds[] = "a.bt_address_line5 bt_country";
        $flds[] = "a.bt_postal_code bt_zipcode";
        $flds[] = "a.bt_primary_phone_no bt_phoneno";
        $flds[] = "(SELECT phone_no FROM customer_phone d WHERE (a.bt_cuid = d.cust_sid and d.seq_no = 2)) bt_mobile";
        $flds[] = "a.bt_email";
        $flds[] = "b.st_cuid st_cuid";
        $flds[] = "b.st_id st_id";
        $flds[] = "b.st_first_name st_firstname";
        $flds[] = "b.st_last_name st_lastname";
        $flds[] = "b.st_address_line1 st_address1";
        $flds[] = "b.st_address_line2 st_address2";
        $flds[] = "b.st_address_line3 st_city";
        $flds[] = "b.st_address_line4 st_state";
        $flds[] = "b.st_country st_country";
        $flds[] = "b.st_postal_code st_zipcode";
        $flds[] = "b.st_primary_phone_no st_phoneno";
        $flds[] = "(SELECT phone_no FROM customer_phone e WHERE (b.st_cuid = e.cust_sid and e.seq_no = 2)) st_mobile";
        $flds[] = "b.st_email";
        $flds[] = "a.ship_date";
        $flds[] = "b.fulfill_store_sid";
        $flds[] = "b.fulfill_store_no";
        $flds[] = "b.fulfill_sbs_no";
        $flds[] = "b.alu";
        $flds[] = "b.price";
        $flds[] = "b.orig_price";
        $flds[] = "b.ship_amt";
        $flds[] = "b.tax_amt";
        $flds[] = "b.orig_tax_amt";
        $flds[] = "b.disc_amt";
        $flds[] = "b.note8";
        $flds[] = "b.note10";
        $flds[] = "b.qty";
        $flds[] = "a.sbs_no";
        $flds[] = "a.store_no";
        $flds[] = "a.workstation_no";
        $flds[] = "d.store_name";
        $flds[] = "e.store_name original_store_name";
        $flds[] = "a.tender_name";
        $flds[] = "d.store_code";
        $flds[] = "e.store_code source_storecode";
        $flds[] = "a.sid sid_";
        $flds[] = "b.sid item_sid";
        $flds[] = "f.detax";
        $flds[] = "b.lty_pgm_name";


        $fields = implode(", ", $flds);

        $sql = "SELECT "
                . "$fields "
                . "FROM "
                . "document a "
                . "LEFT JOIN document_item b ON (a.sid = b.doc_sid) "
                . "LEFT JOIN store d ON (b.fulfill_store_sid = d.sid) "
                . "LEFT JOIN store e ON (a.store_sid = e.sid) "
                . "LEFT JOIN customer f ON (a.bt_cuid = f.sid) "
                . "WHERE 1=1 "
                . "and b.note10 IN ('DIFFERENT STORE', 'SAME STORE') "
                . "and b.item_type = '3' "
                . "and a.sid = '{$_GET['docsid']}'";
        $ctr = 0;
        $ordertype   = '';
        $fulfilltype = '';

        $arr = array();
    //    $conn->debug = TRUE;
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){

            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_doc_no']      = $rsResult->fields['order_doc_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ordered_date']      = $rsResult->fields['ordered_date'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_cuid']           = $rsResult->fields['bt_cuid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_firstname']      = $rsResult->fields['bt_firstname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_lastname']       = $rsResult->fields['bt_lastname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address1']       = $rsResult->fields['bt_address1'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address2']       = $rsResult->fields['bt_address2'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_city']           = $rsResult->fields['bt_city'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_state']          = $rsResult->fields['bt_state'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_country']        = $rsResult->fields['bt_country'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_zipcode']        = $rsResult->fields['bt_zipcode'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_phoneno']        = $rsResult->fields['bt_phoneno'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_mobile']         = $rsResult->fields['bt_mobile'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_email']          = $rsResult->fields['bt_email'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_cuid']           = $rsResult->fields['st_cuid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_firstname']      = $rsResult->fields['st_firstname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_lastname']       = $rsResult->fields['st_lastname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address1']       = $rsResult->fields['st_address1'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address2']       = $rsResult->fields['st_address2'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_city']           = $rsResult->fields['st_city'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_state']          = $rsResult->fields['st_state'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_country']        = $rsResult->fields['st_country'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_zipcode']        = $rsResult->fields['st_zipcode'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_phoneno']        = $rsResult->fields['st_phoneno'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_mobile']         = $rsResult->fields['st_mobile'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_email']          = $rsResult->fields['st_email'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_date']         = $rsResult->fields['ship_date'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fulfill_store_sid'] = $rsResult->fields['fulfill_store_sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_name']        = $rsResult->fields['store_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store']    = $rsResult->fields['original_store_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fulfill_store_no']  = $rsResult->fields['fulfill_store_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fulfill_sbs_no']    = $rsResult->fields['fulfill_sbs_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_pgm_name']      = $rsResult->fields['lty_pgm_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['alu']          = $rsResult->fields['alu'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['price']        = $rsResult->fields['price'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['orig_price']   = $rsResult->fields['orig_price'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['orig_tax_amt'] = $rsResult->fields['orig_tax_amt'];
            if($rsResult->fields['note8'] == 'PICKUP'){
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['deliverymode']   = 'S';
            } else if($rsResult->fields['note8'] == 'HOME DELIVERY'){
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['deliverymode']   = 'H';
            }
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['ship_amt']     = $rsResult->fields['ship_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['tax_amt']      = $rsResult->fields['tax_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['disc_amt']     = $rsResult->fields['disc_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['note8']        = $rsResult->fields['note8'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['note10']       = $rsResult->fields['note10'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['qty']          = $rsResult->fields['qty'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sbs_no']            = $rsResult->fields['sbs_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_no']          = $rsResult->fields['store_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_code']        = $rsResult->fields['store_code'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['source_storecode']  = $rsResult->fields['source_storecode'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_no']    = $rsResult->fields['workstation_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tender_name']       = $rsResult->fields['tender_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sid']               = $rsResult->fields['sid_'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['detax']             = $rsResult->fields['detax'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['item_sid']               = $rsResult->fields['item_sid'];
//            if($rsResult->fields['note8'] == 'PICKUP'){
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['orderstatus']   = 'Authorized';
//            } else if($rsResult->fields['note8'] == 'HOME DELIVERY'){
//                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['orderstatus']   = 'Pending';
//            }
            

            $ctr = $ctr + 1;

            $rsResult->Movenext();

        }
    } else {
        $flds[] = "a.order_doc_no";
        $flds[] = "a.created_datetime ordered_date";
        $flds[] = "a.bt_cuid bt_cuid";
        $flds[] = "a.bt_id bt_id";
        $flds[] = "a.bt_first_name bt_firstname";
        $flds[] = "a.bt_last_name bt_lastname";
        $flds[] = "a.bt_address_line1 bt_address1";
        $flds[] = "a.bt_address_line2 bt_address2";
        $flds[] = "a.bt_address_line3 bt_city";
        $flds[] = "a.bt_address_line4 bt_state";
        $flds[] = "a.bt_address_line5 bt_country";
        $flds[] = "a.bt_postal_code bt_zipcode";
        $flds[] = "a.bt_primary_phone_no bt_phoneno";
        $flds[] = "(SELECT phone_no FROM customer_phone d WHERE (a.bt_cuid = d.cust_sid and d.seq_no = 2)) bt_mobile";
        $flds[] = "a.bt_email";
        $flds[] = "b.st_cuid st_cuid";
        $flds[] = "b.st_id st_id";
        $flds[] = "b.st_first_name st_firstname";
        $flds[] = "b.st_last_name st_lastname";
        $flds[] = "b.st_address_line1 st_address1";
        $flds[] = "b.st_address_line2 st_address2";
        $flds[] = "b.st_address_line3 st_city";
        $flds[] = "b.st_address_line4 st_state";
        $flds[] = "b.st_country st_country";
        $flds[] = "b.st_postal_code st_zipcode";
        $flds[] = "b.st_primary_phone_no st_phoneno";
        $flds[] = "(SELECT phone_no FROM customer_phone e WHERE (b.st_cuid = e.cust_sid and e.seq_no = 2)) st_mobile";
        $flds[] = "b.st_email";
        $flds[] = "a.ship_date";
        $flds[] = "b.fulfill_store_sid";
        $flds[] = "b.fulfill_store_no";
        $flds[] = "b.fulfill_sbs_no";
        $flds[] = "b.alu";
        $flds[] = "b.price";
        $flds[] = "b.orig_price";
        $flds[] = "b.ship_amt";
        $flds[] = "b.tax_amt";
        $flds[] = "b.orig_tax_amt";
        $flds[] = "b.disc_amt";
        $flds[] = "b.note8";
        $flds[] = "b.note10";
        $flds[] = "b.qty";
        $flds[] = "a.sbs_no";
        $flds[] = "a.store_no";
        $flds[] = "a.workstation_no";
        $flds[] = "d.store_name";
        $flds[] = "e.store_name original_store_name";
        $flds[] = "a.tender_name";
        $flds[] = "d.store_code";
        $flds[] = "e.store_code";
        $flds[] = "a.sid sid_";
        $flds[] = "b.sid item_sid";
        $flds[] = "f.detax";
        $flds[] = "b.lty_pgm_name";


        $fields = implode(", ", $flds);

        $sql = "SELECT "
                . "$fields "
                . "FROM "
                . "rps.document a "
                . "LEFT JOIN rps.document_item b ON (a.sid = b.doc_sid) "
                . "LEFT JOIN rps.store d ON (b.fulfill_store_sid = d.sid) "
                . "LEFT JOIN rps.store e ON (a.store_sid = e.sid) "
                . "LEFT JOIN rps.customer f ON (a.bt_cuid = f.sid) "
                . "WHERE 1=1 "
                . "and b.note10 IN ('DIFFERENT STORE', 'SAME STORE') "
                . "and b.item_type = '3' "
                . "and a.sid = '{$_GET['docsid']}'";
        $ctr = 0;
        $ordertype   = '';
        $fulfilltype = '';

        $arr = array();
    //    $conn->debug = TRUE;
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){

            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_doc_no']      = $rsResult->fields['order_doc_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ordered_date']      = $rsResult->fields['ordered_date'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_cuid']           = $rsResult->fields['bt_cuid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_firstname']      = $rsResult->fields['bt_firstname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_lastname']       = $rsResult->fields['bt_lastname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address1']       = $rsResult->fields['bt_address1'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address2']       = $rsResult->fields['bt_address2'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_city']           = $rsResult->fields['bt_city'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_state']          = $rsResult->fields['bt_state'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_country']        = $rsResult->fields['bt_country'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_zipcode']        = $rsResult->fields['bt_zipcode'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_phoneno']        = $rsResult->fields['bt_phoneno'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_mobile']         = $rsResult->fields['bt_mobile'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_email']          = $rsResult->fields['bt_email'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_cuid']           = $rsResult->fields['st_cuid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_firstname']      = $rsResult->fields['st_firstname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_lastname']       = $rsResult->fields['st_lastname'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address1']       = $rsResult->fields['st_address1'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address2']       = $rsResult->fields['st_address2'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_city']           = $rsResult->fields['st_city'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_state']          = $rsResult->fields['st_state'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_country']        = $rsResult->fields['st_country'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_zipcode']        = $rsResult->fields['st_zipcode'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_phoneno']        = $rsResult->fields['st_phoneno'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_mobile']         = $rsResult->fields['st_mobile'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_email']          = $rsResult->fields['st_email'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_date']         = $rsResult->fields['ship_date'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fulfill_store_sid'] = $rsResult->fields['fulfill_store_sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_name']        = $rsResult->fields['store_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store']    = $rsResult->fields['original_store_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fulfill_store_no']  = $rsResult->fields['fulfill_store_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fulfill_sbs_no']    = $rsResult->fields['fulfill_sbs_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_pgm_name']      = $rsResult->fields['lty_pgm_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['alu']        = $rsResult->fields['alu'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['price']      = $rsResult->fields['price'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['orig_price'] = $rsResult->fields['orig_price'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['orig_tax_amt'] = $rsResult->fields['orig_tax_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['detax'] = $rsResult->fields['detax'];
            if($rsResult->fields['note8'] == 'PICKUP'){
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['deliverymode']   = 'S';
            } else if($rsResult->fields['note8'] == 'HOME DELIVERY'){
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['deliverymode']   = 'H';
            }
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['ship_amt']   = $rsResult->fields['ship_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['tax_amt']    = $rsResult->fields['tax_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['disc_amt']   = $rsResult->fields['disc_amt'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['note8']      = $rsResult->fields['note8'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['note10']     = $rsResult->fields['note10'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['qty']        = $rsResult->fields['qty'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sbs_no']            = $rsResult->fields['sbs_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_no']          = $rsResult->fields['store_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_code']        = $rsResult->fields['store_code'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['source_storecode']  = $rsResult->fields['source_storecode'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_no']    = $rsResult->fields['workstation_no'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tender_name']       = $rsResult->fields['tender_name'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sid']               = $rsResult->fields['sid_'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['item_sid'] = $rsResult->fields['item_sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['detax']             = $rsResult->fields['detax'];
//            if($rsResult->fields['note8'] == 'PICKUP'){
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['orderstatus']   = 'Authorized';
//            } else if($rsResult->fields['note8'] == 'HOME DELIVERY'){
//                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['orderstatus']   = 'Pending';
//            }
            

            $ctr = $ctr + 1;

            $rsResult->Movenext();

        }
    }
    
//    echo "<pre>";
//    print_r($_REQUEST);
//    echo "</pre>";
//    exit;
    
    $sql = "SELECT * FROM prism_sequence WHERE workstation_sid = '{$_REQUEST['ws']}' AND doc_seq_type = 0";
    $rsResult = $conn->Execute($sql);
    if(!$rsResult->EOF){
        $sequence = ($rsResult->fields['previous_value']+1);
    }
    
    $a = array();
    $ctr = 0;
    foreach($arr as $key => $value){
        
        $reference_no = "";
        $sequence = $sequence;
        
        foreach ($value as $keyA => $valueA) {
            
            $totalamt = 0;
            $shipping_total = 0;
            $shipping_total_tax = 0;
            $n = 0;
            $b = array();
            
            $reference_no = str_pad($valueA['sbs_no'], 3, "0", STR_PAD_LEFT).'-'.$valueA['source_storecode'].'-'.str_pad($sequence, 9, "0", STR_PAD_LEFT).'-'.str_pad($valueA['workstation_no'], 3, "0", STR_PAD_LEFT);
            
            
            
            $sequence = $sequence + 1;
            
            foreach($valueA['items'] as $keyB => $valueB){
                
//                $totalamt = 0;
//                if($keyA == 'SAME STORE') {
//                    $b['orderrefno']   =  $valueA['sid'];
//                }else {
//                $b['orderrefno']   =  str_pad($valueA['sbs_no'], 3, "0", STR_PAD_LEFT).'-'.$valueA['source_storecode'].'-'.str_pad($ctr, 9, "0", STR_PAD_LEFT).'-'.str_pad($valueA['workstation_no'], 3, "0", STR_PAD_LEFT);    
                $b['orderrefno']   =  $reference_no;    
//                }
                
                $price = $valueB['price'] - $valueB['tax_amt'];
//                $price = $valueB['orig_price'] - $valueB['orig_tax_amt'];
                $taxamount = $valueB['tax_amt'] * $valueB['qty'];
//                $taxamount = $valueB['orig_tax_amt'] * $valueB['qty'];
                
                $total_price += ($valueB['price'] * $valueB['qty']);
                
                $b['sku']              = $valueB['alu']; 
                $b['variantsku']       = ''; 
                $b['qty']              = number_format($valueB['qty'], 0,'','');
                $b['unitprice']        = number_format($price, 6,'.','');
                $b['shippingamount']   = number_format($valueB['ship_amt'], 6,'.','');
                $b['deliverymode']     = $valueB['deliverymode'];
                $b['tax']              = number_format($taxamount, 6,'.','');
                $b['shippingdiscount'] = '0'; 
                $b['linediscount']     = number_format($valueB['disc_amt'], 6,'.',''); 
//                $b['linediscount']     = ''; 
                
                /*----------------TOTAL SHIPPING------------------------------*/
                $shipping_total = $shipping_total + $valueB['ship_amt'];
                
                /*----------------CUSTOM FIELDS [ITEM]----------------------------*/
                //DISCOUNT TYPE
                $customItemfield1['name'] = "ItemLevelDiscountType";
                $customItemfield1['value'] = "";

                //DISCOUNT TYPE
                $customItemfield2['name'] = "PRICELEVEL";
                $customItemfield2['value'] = "";
                
                $i = array();
                $i[] = $customItemfield1;
                $i[] = $customItemfield2;
                
                
                $b['itemcustomfields']['itemcustomfield']     = $i; 
                unset($i);
                /*----------------------------------------------------------------*/
                
                $c[] = $b;
                
                $n++;
                
                $totalamt = $totalamt + ($valueB['qty'] * $valueB['price']);
                
                if($_GET['ism'] == 1){
                
                    $sql = "UPDATE "
                            . "document_item SET "
                            . "note5 = '".$reference_no."' "
                            . "WHERE sid='".$valueB['item_sid']."'";
//                    $conn->debug = true;
                    $conn->Execute($sql);
                } else {
                   $sql = "UPDATE "
                            . "document SET "
                            . "notes_order = '".$reference_no."', "
                            . "udf2_string = 'SE' "
                            . "WHERE sid='".$valueA['sid']."'";
//                    $conn->debug = true;
                    $conn->Execute($sql); 
                }
                
            }
            
            $d = array();
            
            if (TRUE == $conn){
            
                $sql1 = "SELECT "
                        . "a.*, "
                        . "b.card_type_name "
                        . "FROM "
                        . "tender a "
                        . "LEFT JOIN tender_credit_card b ON (a.sid = b.tender_sid) "
                        . "WHERE 1=1 "
                        . "and a.doc_sid = '{$_GET['docsid']}'";
//                $conn->debug = TRUE;
                $rsResult1 = $conn->Execute($sql1);
                while(!$rsResult1->EOF){

//                    if($keyA == 'SAME STORE') {
//                        $d['orderrefno']   =  $valueA['sid'];
//                    }else {
                        $d['orderrefno']   =  $reference_no;    
//                    }
                    if(strtoupper($rsResult1->fields['card_type_name']) != ''){
                        if(strtoupper($rsResult1->fields['card_type_name']) == 'GIFT CARD'){
                            $d['checkouttype']   = 'GCARD'; 
                        } else {
                            $d['checkouttype']   = strtoupper($rsResult1->fields['card_type_name']); 
                        }
                    } else {
                        if(strtoupper($rsResult1->fields['tender_name']) == 'GIFT CARD'){
                            $d['checkouttype']   = 'GCARD'; 
                        } else {
                            $d['checkouttype']   = strtoupper($rsResult1->fields['tender_name']); 
                        }
                    }
                    $d['paymentno']      = ''; 
                    
                    $d['amount']         = number_format($total_price, 6, '.','');
                    $d['transaciondate'] = date('m/d/Y', strtotime($valueA['ordered_date']));
                    $d['paymentstatus']  = $valueA['orderstatus'];
                    $d['gvcode']         = '0';

                    $rsResult1->Movenext();

                    $e[] = $d;

                }

            } else {
                
                $sql1 = "SELECT "
                        . "a.*, "
                        . "b.card_type_name "
                        . "FROM "
                        . "rps.tender a "
                        . "LEFT JOIN rps.tender_credit_card b ON (a.sid = b.tender_sid) "
                        . "WHERE 1=1 "
                        . "and a.doc_sid = '{$_GET['docsid']}'";
    //            $conn->debug = TRUE;
                $rsResult1 = $conn->Execute($sql1);
                while(!$rsResult1->EOF){

//                    if($keyA == 'SAME STORE') {
//                        $d['orderrefno']   =  $valueA['sid'];
//                    }else {
                        $d['orderrefno']   =  $reference_no;    
//                    }
                    $d['checkouttype']   = strtoupper($rsResult1->fields['card_type_name']); 
                    
                    $d['paymentno']      = ''; 
                    $d['amount']         = number_format($rsResult1->fields['amount'], 6, '.','');
                    $d['transaciondate'] = date('m/d/Y', strtotime($valueA['ordered_date']));
                    $d['paymentstatus']  = $valueA['orderstatus'];
                    $d['gvcode']         = '0';

                    $rsResult1->Movenext();

                    $e[] = $d;

                }
                
            }
            
//            if($keyA == 'SAME STORE') {
//                $a['orderrefno']   =  $valueA['sid'];
//            }else {
                $a['orderrefno']   =  $reference_no;    
//            }
            $a['orderdate'] = date('m/d/Y', strtotime($valueA['ordered_date']));
            $a['customertype'] = 'Guest User';
             $a['userid'] =  $valueA['bt_email'];
            $a['orderamountstatus'] = 'NO';
            $a['ordervalue'] = number_format($totalamt, 6,'.','');
            $a['orderstatus'] = $valueA['orderstatus'];
            $a['orderconfirmationmail'] = 'NO';
            $a['paymentlinkstatus'] = 'NO';
            $a['calculateshippingtax'] = 'NO';
//            $a['deliverymode'] = empty($valueA['deliverymode']) ? '' : $valueA['deliverymode'];
            $a['vouchercode'] = '';
            $a['shipfirstname'] = empty($valueA['st_firstname']) ? '' : $valueA['st_firstname'];
            $a['shiplastname'] = empty($valueA['st_lastname']) ? '' : $valueA['st_lastname'];
            $a['shipaddress1'] = empty($valueA['st_address1']) ? '' : $valueA['st_address1'];
            $a['shipaddress2'] = empty($valueA['st_address2']) ? '' : $valueA['st_address2'];
            $a['shipcity'] = empty($valueA['st_city']) ? '' : $valueA['st_city'];
            $a['shipstate'] = empty($valueA['st_state']) ? 'Jakarta' : $valueA['st_state'];
            $a['shipcountry'] = empty($valueA['st_country']) ? 'Indonesia' : $valueA['st_country'];
            $a['shipzip'] =empty($valueA['st_zipcode']) ? '' : $valueA['st_zipcode'];
            $a['shiplandline'] = empty($valueA['st_phoneno']) ? '' : $valueA['st_phoneno'];
            $a['shipmobile'] = empty($valueA['st_mobile']) ? '' : $valueA['st_mobile'];
            $a['shipemail'] = empty($valueA['st_email']) ? '' : $valueA['st_email']; //st_email
            $a['billfirstname'] = empty($valueA['bt_firstname']) ? '' : $valueA['bt_firstname'];
            $a['billlastname'] = empty($valueA['bt_lastname']) ? '' : $valueA['bt_lastname'];
            $a['billaddress1'] = empty($valueA['bt_address1']) ? '' : $valueA['bt_address1'];
            $a['billaddress2'] = empty($valueA['bt_address2']) ? '' : $valueA['bt_address2'];
            $a['billcity'] = empty($valueA['bt_city']) ? '' : $valueA['bt_city'];
            $a['billstate'] = empty($valueA['bt_state']) ? 'Jakarta' : $valueA['bt_state'];
            $a['billcountry'] = empty($valueA['bt_country']) ? 'Indonesia' : $valueA['bt_country'];
            $a['billzip'] = empty($valueA['bt_zipcode']) ? '' : $valueA['bt_zipcode'];
            $a['billlandline'] = empty($valueA['bt_phoneno']) ? '' : $valueA['bt_phoneno'];
            $a['billmobile'] = empty($valueA['bt_mobile']) ? '' : $valueA['bt_mobile'];
            $a['billemail'] = empty($valueA['bt_email']) ? '' : $valueA['bt_email'];
            $a['giftmsg'] = '';
            $a['shippingmode'] = '';
            $a['deliveredon'] = empty($valueA['lty_pgm_name']) ? '' : date('m/d/Y', strtotime($valueA['lty_pgm_name']));
            
//            $a['locationcode'] = $valueA['fulfill_store_no'];
            $a['deliveryslotcode'] = '';
            
            if($key == 'PICKUP'){
                
                $a['locationcode'] = $valueA['store_code'];
                $a['storelocationcode'] = $valueA['store_code'];
                
            } else if($key == 'HOME DELIVERY'){
                
                if($keyA == 'SAME STORE'){
                    
                    $a['locationcode'] = $valueA['source_storecode'];
                    $a['storelocationcode'] = $valueA['source_storecode'];
                    
                } else {
                    
                    $a['locationcode'] = '';
                    $a['storelocationcode'] = '';
                }
                
            } 
//            $a['locationcode'] = $valueA['store_name'];
//            $a['storelocationcode'] = $valueA['store_name'];
            
            /*----------------CUSTOM FIELDS [HEADER]--------------------------*/
            //SBS
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield1['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield1['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield1['id'] = "0";
            $customHeaderfield1['name'] = "SubsidiaryNo";
            $customHeaderfield1['value'] = $valueA['sbs_no'];
            
            //PRISM SO SID
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield2['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield2['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield2['id'] = "0";
            $customHeaderfield2['name'] = "SourceSOSID";
            $customHeaderfield2['value'] = $valueA['sid'];
            
            //PRISM ORDER TYPE
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield3['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield3['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield3['id']   = "0";
            $customHeaderfield3['name'] = "OrderType";
            if($key == 'PICKUP'){
                $customHeaderfield3['value'] = 'POS-'.ucwords($key).'-Prepaid';
            } ELSE {
                $customHeaderfield3['value'] = 'POS-Ship-Prepaid';
            }
            
            //FULFILLMENT TYPE
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield4['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield4['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield4['id']    = "0";
            $customHeaderfield4['name']  = "FulfillmentType";
            $customHeaderfield4['value'] = $keyA;
            
            //SHIPPING AMOUNT
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield5['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield5['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield5['id']    = "0";
            $customHeaderfield5['name']  = "OrderShippingAmt";
            $customHeaderfield5['value'] = empty($shipping_total) ? '0' : number_format($shipping_total, 6,'.','');
            
            //SHIPPING AMOUNT TAX AMOUNT
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield6['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield6['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield6['id']    = "0";
            $customHeaderfield6['name']  = "OrderShippingAmwithTax";
            $customHeaderfield6['value'] = empty($shipping_total) ? '0' : number_format($shipping_total, 6,'.','');
            
            //REDEMPTION DISCOUNT AMOUNT
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield7['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield7['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield7['id'] = "0";
            $customHeaderfield7['name'] = "RdemptionDiscountAmount";
            $customHeaderfield7['value'] = '0';
            
            //SHIP TO CUSTOMER ID
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield8['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield8['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield8['id'] = "0";
            $customHeaderfield8['name'] = "CustomerShiptoAddressID";
            $customHeaderfield8['value'] = $valueA['st_id'];
            
            //SHIP TO CUSTOMER ID
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield9['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield9['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield9['id'] = "0";
            $customHeaderfield9['name'] = "CustomerBillToAddressID";
            $customHeaderfield9['value'] = $valueA['bt_id'];
            
            //SOURCE STORE
//            if($keyA == 'SAME STORE') {
//                $customHeaderfield10['orderrefno']   =  $valueA['sid'];
//            }else {
                $customHeaderfield10['orderrefno']   =  $reference_no;    
//            }
            $customHeaderfield10['id'] = "0";
            $customHeaderfield10['name'] = "SourceStore";
            $customHeaderfield10['value'] = $valueA['original_store'];
            
            $customHeaderfield11['orderrefno']   =  $reference_no;    
            $customHeaderfield11['id'] = "0";
            $customHeaderfield11['name'] = "OrderDeTax";
            $customHeaderfield11['value'] = $valueA['detax'];
            
            $g = array();
            $g[] = $customHeaderfield1;
            $g[] = $customHeaderfield2;
            $g[] = $customHeaderfield3;
            $g[] = $customHeaderfield4;
            $g[] = $customHeaderfield5;
            $g[] = $customHeaderfield6;
            $g[] = $customHeaderfield7;
            $g[] = $customHeaderfield8;
            $g[] = $customHeaderfield9;
            $g[] = $customHeaderfield10;
            $g[] = $customHeaderfield11;
            
            $a['customfields']['customfield'] = $g;
            unset($g);
            
            /*----------------------------------------------------------------*/
            
            $a['items']['item'] = $c;
            
            $a['payments']['payment'] = $e;
            
//            echo "<pre>";
            $f = json_encode($a);
//            print_r($f);
//            echo "</pre>";
//            exit;
            
            $qlite = "INSERT INTO pd_upload ("
               . "pdu_raw, "
               . "pdu_status, "
               . "pdu_date) "
               . "VALUES ( "
               . "'".$f."', "
               . "'', "
               . "'".date('Y-m-d', strtotime('now'))."'"
               . ")";
//            echo $qlite;
//          $sconn->debug = true;  
            $sconn->Execute($qlite);
            
            
            $c = "";
            $e = "";
            $ctr++;
            
        }
        
    }
    $sql = "UPDATE prism_sequence SET previous_value = '".($sequence-1)."' WHERE workstation_sid = '{$_REQUEST['ws']}' AND doc_seq_type in (0)";
    $conn->Execute($sql);
//    unset($arr);
//    unset($sql);
//    unset($rsResult);
//    unset($ctr);
//    exit;
    unset($arr);
    unset($sql);
    unset($rsResult);
    //** CREATION OF SO FOR SAME STORES
    if($_GET['ism'] == '1'){
        if (TRUE == $conn){

            $fields = implode(", ", $flds);

            $sql = "SELECT "
                    . "a.*, b.*, a.sid source_sid "
                    . "FROM "
                    . "document a "
                    . "LEFT JOIN document_item b ON (a.sid = b.doc_sid) "
                    . "LEFT JOIN store d ON (b.fulfill_store_sid = d.sid) "
                    . "LEFT JOIN store e ON (a.store_sid = e.sid) "
                    . "WHERE 1=1 "
                    . "and b.note10 IN ('SAME STORE') "
                    . "and b.item_type = '3' "
                    . "and a.sid = '{$_GET['docsid']}'";
            $ctr = 0;
            $ordertype   = '';
            $fulfilltype = '';

            $arr = array();
//            $conn->debug = TRUE;
            $rsResult = $conn->Execute($sql);
//            exit;
            while(!$rsResult->EOF){

                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['created_by']=$rsResult->fields['created_by'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['created_datetime']=$rsResult->fields['created_datetime'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['modified_by']=$rsResult->fields['modified_by'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['modified_datetime']=$rsResult->fields['modified_datetime'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['origin_application']=$rsResult->fields['origin_application'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['post_date']=$rsResult->fields['post_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['invoice_posted_date']=$rsResult->fields['invoice_posted_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tracking_number']=$rsResult->fields['tracking_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['use_vat']=$rsResult->fields['use_vat'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['vat_options']=$rsResult->fields['vat_options'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['customer_po_number']=$rsResult->fields['customer_po_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_percent']=$rsResult->fields['tax_rebate_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_amt']=$rsResult->fields['tax_rebate_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['over_tax_percent']=$rsResult->fields['over_tax_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['over_tax_percent2']=$rsResult->fields['over_tax_percent2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['rounding_offset']=$rsResult->fields['rounding_offset'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['was_audited']=$rsResult->fields['was_audited'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_sequence_number']=$rsResult->fields['workstation_sequence_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['customer_field']=$rsResult->fields['customer_field'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['is_held']=$rsResult->fields['is_held'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['drawer_number']=$rsResult->fields['drawer_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['elapsed_time']=$rsResult->fields['elapsed_time'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity_percent']=$rsResult->fields['activity_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity2_percent']=$rsResult->fields['activity2_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity3_percent']=$rsResult->fields['activity3_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity4_percent']=$rsResult->fields['activity4_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity5_percent']=$rsResult->fields['activity5_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['eft_invoice_number']=$rsResult->fields['eft_invoice_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['detax_flag']=$rsResult->fields['detax_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_percentage']=$rsResult->fields['shipping_percentage'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt']=$rsResult->fields['shipping_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_tax_included']=$rsResult->fields['shipping_tax_included'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_tax_percentage']=$rsResult->fields['shipping_tax_percentage'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_tax_amt']=$rsResult->fields['shipping_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_method']=$rsResult->fields['ship_method'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fiscal_document_number']=$rsResult->fields['fiscal_document_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string1']="SE";
//                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string1']=$rsResult->fields['udf_string1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string2']=$rsResult->fields['udf_string2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string3']=$rsResult->fields['udf_string3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string4']=$rsResult->fields['udf_string4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string5']=$rsResult->fields['udf_string5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float1']=$rsResult->fields['udf_float1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float2']=$rsResult->fields['udf_float2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float3']=$rsResult->fields['udf_float3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float4']=$rsResult->fields['udf_float4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float5']=$rsResult->fields['udf_float5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_date1']=$rsResult->fields['udf_date1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_date2']=$rsResult->fields['udf_date2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_date3']=$rsResult->fields['udf_date3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_clob1']=$rsResult->fields['udf_clob1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['archived']=$rsResult->fields['archived'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_fee_amt']=$rsResult->fields['total_fee_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sale_total_tax_amt']=$rsResult->fields['sale_total_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sale_total_amt']=$rsResult->fields['sale_total_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sale_subtotal']=$rsResult->fields['sale_subtotal'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_amt_required']=$rsResult->fields['deposit_amt_required'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['due_amt']=$rsResult->fields['due_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sold_qty']=$rsResult->fields['sold_qty'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_qty']=$rsResult->fields['return_qty'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['doc_tender_type']=$rsResult->fields['doc_tender_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['subsidiary_number']=$rsResult->fields['subsidiary_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_number']=$rsResult->fields['store_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_code']=$rsResult->fields['store_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_name']=$rsResult->fields['workstation_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store_number']=$rsResult->fields['original_store_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store_code']=$rsResult->fields['original_store_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_name']=$rsResult->fields['tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_name']=$rsResult->fields['tax_area2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_login_name']=$rsResult->fields['employee1_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_full_name']=$rsResult->fields['employee1_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_login_name']=$rsResult->fields['employee2_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_full_name']=$rsResult->fields['employee2_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_login_name']=$rsResult->fields['employee3_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_full_name']=$rsResult->fields['employee3_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_login_name']=$rsResult->fields['employee4_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_full_name']=$rsResult->fields['employee4_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_login_name']=$rsResult->fields['employee5_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_full_name']=$rsResult->fields['employee5_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_login_name']=$rsResult->fields['cashier_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_full_name']=$rsResult->fields['cashier_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type1']=$rsResult->fields['fee_type1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name1']=$rsResult->fields['fee_name1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1']=$rsResult->fields['fee_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included1']=$rsResult->fields['fee_tax_included1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_amt1']=$rsResult->fields['fee_tax_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_perc1']=$rsResult->fields['fee_tax_perc1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type2']=$rsResult->fields['fee_type2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name2']=$rsResult->fields['fee_name2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included2']=$rsResult->fields['fee_tax_included2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type3']=$rsResult->fields['fee_type3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name3']=$rsResult->fields['fee_name3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included3']=$rsResult->fields['fee_tax_included3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_perc3']=$rsResult->fields['fee_tax_perc3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type4']=$rsResult->fields['fee_type4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name4']=$rsResult->fields['fee_name4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included4']=$rsResult->fields['fee_tax_included4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type5']=$rsResult->fields['fee_type5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name5']=$rsResult->fields['fee_name5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt5']=$rsResult->fields['fee_amt5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included5']=$rsResult->fields['fee_tax_included5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt1']=$rsResult->fields['hist_discount_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc1']=$rsResult->fields['hist_discount_perc1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason1']=$rsResult->fields['hist_discount_reason1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt2']=$rsResult->fields['hist_discount_amt2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc2']=$rsResult->fields['hist_discount_perc2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason2']=$rsResult->fields['hist_discount_reason2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt3']=$rsResult->fields['hist_discount_amt3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc3']=$rsResult->fields['hist_discount_perc3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason3']=$rsResult->fields['hist_discount_reason3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt4']=$rsResult->fields['hist_discount_amt4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc4']=$rsResult->fields['hist_discount_perc4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason4']=$rsResult->fields['hist_discount_reason4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt5']=$rsResult->fields['hist_discount_amt5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc5']=$rsResult->fields['hist_discount_perc5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason5']=$rsResult->fields['hist_discount_reason5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_cuid']=$rsResult->fields['bt_cuid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_id']=$rsResult->fields['bt_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_last_name']=$rsResult->fields['bt_last_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_first_name']=$rsResult->fields['bt_first_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_company_name']=$rsResult->fields['bt_company_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_title']=$rsResult->fields['bt_title'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_tax_area_name']=$rsResult->fields['bt_tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_tax_area2_name']=$rsResult->fields['bt_tax_area2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_detax_flag']=$rsResult->fields['bt_detax_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_price_lvl_name']=$rsResult->fields['bt_price_lvl_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_price_lvl']=$rsResult->fields['bt_price_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_security_lvl']=$rsResult->fields['bt_security_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_primary_phone_no']=$rsResult->fields['bt_primary_phone_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line1']=$rsResult->fields['bt_address_line1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line2']=$rsResult->fields['bt_address_line2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line3']=$rsResult->fields['bt_address_line3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line4']=$rsResult->fields['bt_address_line4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line5']=$rsResult->fields['bt_address_line5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line6']=$rsResult->fields['bt_address_line6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_country']=$rsResult->fields['bt_country'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_postal_code']=$rsResult->fields['bt_postal_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_postal_code_extension']=$rsResult->fields['bt_postal_code_extension'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_primary']=$rsResult->fields['bt_primary'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_email']=$rsResult->fields['bt_email'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_customer_lookup']=$rsResult->fields['bt_customer_lookup'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_cuid']=$rsResult->fields['st_cuid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_id']=$rsResult->fields['st_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_last_name']=$rsResult->fields['st_last_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_first_name']=$rsResult->fields['st_first_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_company_name']=$rsResult->fields['st_company_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_title']=$rsResult->fields['st_title'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_tax_area_name']=$rsResult->fields['st_tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_tax_area2_name']=$rsResult->fields['st_tax_area2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_detax_flag']=$rsResult->fields['st_detax_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_price_lvl_name']=$rsResult->fields['st_price_lvl_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_price_lvl']=$rsResult->fields['st_price_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_security_lvl']=$rsResult->fields['st_security_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_primary_phone_no']=$rsResult->fields['st_primary_phone_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line1']=$rsResult->fields['st_address_line1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line2']=$rsResult->fields['st_address_line2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line3']=$rsResult->fields['st_address_line3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line4']=$rsResult->fields['st_address_line4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line5']=$rsResult->fields['st_address_line5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line6']=$rsResult->fields['st_address_line6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_country']=$rsResult->fields['st_country'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_postal_code']=$rsResult->fields['st_postal_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_postal_code_extension']=$rsResult->fields['st_postal_code_extension'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_primary']=$rsResult->fields['st_primary'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_email']=$rsResult->fields['st_email'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_customer_lookup']=$rsResult->fields['st_customer_lookup'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tender_name']=$rsResult->fields['tender_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['currency_name']=$rsResult->fields['currency_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['till_name']=$rsResult->fields['till_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tenders: [],']=$rsResult->fields['tenders: [],'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['given_amt']=$rsResult->fields['given_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['price_lvl']=$rsResult->fields['price_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['price_lvl_name']=$rsResult->fields['price_lvl_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_uid']=$rsResult->fields['workstation_uid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['subsidiary_name']=$rsResult->fields['subsidiary_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_name']=$rsResult->fields['store_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_percent']=$rsResult->fields['tax_area_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_percent']=$rsResult->fields['tax_area2_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_amt']=$rsResult->fields['tax_area_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_amt']=$rsResult->fields['tax_area2_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_sales_tax_amt']=$rsResult->fields['tax_area_sales_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_sales_tax_amt']=$rsResult->fields['tax_area2_sales_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_order_tax_amt']=$rsResult->fields['tax_area_order_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_order_tax_amt']=$rsResult->fields['tax_area2_order_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['reason_code']=$rsResult->fields['reason_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['reason_description']=$rsResult->fields['reason_description'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['pos_flag1']=$rsResult->fields['pos_flag1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['pos_flag2']=$rsResult->fields['pos_flag2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['pos_flag3']=$rsResult->fields['pos_flag3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['comment1']=$rsResult->fields['comment1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['comment2']=$rsResult->fields['comment2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_amt_taken']=$rsResult->fields['deposit_amt_taken'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_ref_doc_sid']=$rsResult->fields['deposit_ref_doc_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_deposit_taken']=$rsResult->fields['total_deposit_taken'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_general']=$rsResult->fields['notes_general'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_lostdoc']=$rsResult->fields['notes_lostdoc'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_order']=$rsResult->fields['note5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_return']=$rsResult->fields['notes_return'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_sale']=$rsResult->fields['notes_sale'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['custom_flag']=$rsResult->fields['custom_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_cash']=$rsResult->fields['allow_tender_cash'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['docallow_tender_check']=$rsResult->fields['docallow_tender_check'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_credit_card']=$rsResult->fields['allow_tender_credit_card'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_debit_card']=$rsResult->fields['allow_tender_debit_card'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_foreign_check']=$rsResult->fields['allow_tender_foreign_check'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_foreign_currency']=$rsResult->fields['allow_tender_foreign_currency'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_gift_card']=$rsResult->fields['allow_tender_gift_card'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_gift_certificate']=$rsResult->fields['allow_tender_gift_certificate'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_traveler_check']=$rsResult->fields['allow_tender_traveler_check'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['controller_number']=$rsResult->fields['controller_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['subsidiary_uid']=$rsResult->fields['subsidiary_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_uid']=$rsResult->fields['store_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ordered_date']=$rsResult->fields['ordered_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_date']=$rsResult->fields['ship_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cancel_date']=$rsResult->fields['cancel_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_partial']=$rsResult->fields['ship_partial'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_priority']=$rsResult->fields['ship_priority'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['till_sid']=$rsResult->fields['till_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_sid']=$rsResult->fields['employee1_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_sid']=$rsResult->fields['employee2_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_sid']=$rsResult->fields['employee3_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_sid']=$rsResult->fields['employee4_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_sid']=$rsResult->fields['employee5_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_sid']=$rsResult->fields['cashier_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['exchange_qty']=$rsResult->fields['exchange_qty'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['createdby_employeeid']=$rsResult->fields['createdby_employeeid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['modifiedby_employeeid']=$rsResult->fields['modifiedby_employeeid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_id']=$rsResult->fields['employee1_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_id']=$rsResult->fields['employee2_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_order_sid']=$rsResult->fields['ref_order_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_number']=$rsResult->fields['workstation_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_id']=$rsResult->fields['employee3_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_id']=$rsResult->fields['employee4_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_id']=$rsResult->fields['employee5_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_id']=$rsResult->fields['cashier_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['so_cancel_flag']=$rsResult->fields['so_cancel_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_deposit_used']=$rsResult->fields['total_deposit_used'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_available_amt']=$rsResult->fields['deposit_available_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1']=$rsResult->fields['order_fee_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_name1']=$rsResult->fields['order_fee_name1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_tax_amt1']=$rsResult->fields['order_fee_tax_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_tax_included1']=$rsResult->fields['order_fee_tax_included1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_tax_perc1']=$rsResult->fields['order_fee_tax_perc1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_type1']=$rsResult->fields['order_fee_type1'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt']=$rsResult->fields['order_shipping_amt'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_tax_amt']=$rsResult->fields['order_shipping_tax_amt'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_tax_included']=$rsResult->fields['order_shipping_tax_included'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_tax_perc']=$rsResult->fields['order_shipping_tax_perc'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type1_sid']=$rsResult->fields['fee_type1_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_sid']=$rsResult->fields['shipping_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_type1_sid']=$rsResult->fields['order_fee_type1_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_sid']=$rsResult->fields['order_shipping_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_percentage']=$rsResult->fields['order_shipping_percentage'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1_no_tax']=$rsResult->fields['fee_amt1_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1_with_tax']=$rsResult->fields['fee_amt1_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1_no_tax']=$rsResult->fields['order_fee_amt1_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1_with_tax']=$rsResult->fields['order_fee_amt1_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_no_tax']=$rsResult->fields['order_shipping_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_with_tax']=$rsResult->fields['order_shipping_amt_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_no_tax']=$rsResult->fields['shipping_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_with_tax']=$rsResult->fields['shipping_amt_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_subtotal']=$rsResult->fields['used_subtotal'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_tax']=$rsResult->fields['used_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_fee_amt1']=$rsResult->fields['used_fee_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_shipping_amt']=$rsResult->fields['used_shipping_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_changed_flag']=$rsResult->fields['order_changed_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['promo_gd_modifiedmanually']=$rsResult->fields['promo_gd_modifiedmanually'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_fee_amt_no_tax']=$rsResult->fields['total_fee_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_fee_tax_amt']=$rsResult->fields['total_fee_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_balance_due']=$rsResult->fields['order_balance_due'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store_uid']=$rsResult->fields['original_store_uid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line1']=$rsResult->fields['store_address_line1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line2']=$rsResult->fields['store_address_line2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line3']=$rsResult->fields['store_address_line3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line4']=$rsResult->fields['store_address_line4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line5']=$rsResult->fields['store_address_line5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_zip']=$rsResult->fields['store_address_zip'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_phone1']=$rsResult->fields['store_phone1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_phone2']=$rsResult->fields['store_phone2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['has_sale']=$rsResult->fields['has_sale'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['has_return']=$rsResult->fields['has_return'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['has_deposit']=$rsResult->fields['has_deposit'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['receipt_type']=$rsResult->fields['receipt_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_type']=$rsResult->fields['order_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_method_id']=$rsResult->fields['ship_method_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_method_sid']=$rsResult->fields['ship_method_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_ship_method']=$rsResult->fields['order_ship_method'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_ship_method_id']=$rsResult->fields['order_ship_method_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_ship_method_sid']=$rsResult->fields['order_ship_method_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['from_centrals']=$rsResult->fields['from_centrals'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_copy']=$rsResult->fields['send_sale_copy'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_status']=$rsResult->fields['send_sale_status'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_last_event']=$rsResult->fields['send_sale_last_event'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_last_error']=$rsResult->fields['send_sale_last_error'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_fulfillment']=$rsResult->fields['send_sale_fulfillment'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['detax_amount']=$rsResult->fields['detax_amount'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_name']=$rsResult->fields['employee1_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_name']=$rsResult->fields['employee2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_name']=$rsResult->fields['employee3_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_name']=$rsResult->fields['employee4_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_name']=$rsResult->fields['employee5_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_name']=$rsResult->fields['cashier_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_manual']=$rsResult->fields['order_shipping_amt_manual'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_persisted']=$rsResult->fields['tax_rebate_persisted'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_status']=$rsResult->fields['order_status'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_manual']=$rsResult->fields['shipping_amt_manual'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_manual_used']=$rsResult->fields['order_shipping_amt_manual_used'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_tracking_number']=$rsResult->fields['order_tracking_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['transaction_total_shipping_tax']=$rsResult->fields['transaction_total_shipping_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['transaction_total_shipping_amt_no_tax']=$rsResult->fields['transaction_total_shipping_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['transaction_total_shipping_amt_with_tax']=$rsResult->fields['transaction_total_shipping_amt_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_subtotal']=$rsResult->fields['return_subtotal'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_subtotal_with_tax']=$rsResult->fields['return_subtotal_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_tax1_amt']=$rsResult->fields['return_tax1_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_tax2_amt']=$rsResult->fields['return_tax2_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_total_tax_amt']=$rsResult->fields['return_total_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1_bdt']=$rsResult->fields['fee_amt1_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_manual_bdt']=$rsResult->fields['shipping_amt_manual_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1_bdt']=$rsResult->fields['order_fee_amt1_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_manual_bdt']=$rsResult->fields['order_shipping_amt_manual_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fulfilling_amt']=$rsResult->fields['order_fulfilling_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_available']=$rsResult->fields['tax_rebate_available'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['refund_document_sid']=$rsResult->fields['refund_document_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['due_amt_current']=$rsResult->fields['due_amt_current'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_order_order_doc_no']=$rsResult->fields['ref_order_order_doc_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_order_balance_due']=$rsResult->fields['ref_order_balance_due'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['coupons: [],']=$rsResult->fields['coupons: [],'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_total_redeem_pgm_sid']=$rsResult->fields['lty_total_redeem_pgm_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_total_redeem_pgm_name']=$rsResult->fields['lty_total_redeem_pgm_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_start_balance']=$rsResult->fields['lty_start_balance'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_end_balance']=$rsResult->fields['lty_end_balance'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_redeem_amt']=$rsResult->fields['lty_redeem_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt_returned1']=$rsResult->fields['fee_amt_returned1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_manual_returned']=$rsResult->fields['shipping_amt_manual_returned'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_item_earn_pgm_sid']=$rsResult->fields['lty_item_earn_pgm_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_item_earn_pgm_name']=$rsResult->fields['lty_item_earn_pgm_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_sid']=$rsResult->fields['ref_sale_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_doc_no']=$rsResult->fields['ref_sale_doc_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_created_datetime']=$rsResult->fields['ref_sale_created_datetime'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_tax_area_name']=$rsResult->fields['ref_sale_tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['gift_receipt_type']=$rsResult->fields['gift_receipt_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf1']=$rsResult->fields['bt_udf1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf2']=$rsResult->fields['bt_udf2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf3']=$rsResult->fields['bt_udf3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf4']=$rsResult->fields['bt_udf4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf5']=$rsResult->fields['bt_udf5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf1']=$rsResult->fields['st_udf1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf2']=$rsResult->fields['st_udf2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf3']=$rsResult->fields['st_udf3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf4']=$rsResult->fields['st_udf4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf5']=$rsResult->fields['st_udf5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf6']=$rsResult->fields['st_udf6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf7']=$rsResult->fields['st_udf7'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf8']=$rsResult->fields['st_udf8'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf9']=$rsResult->fields['st_udf9'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf10']=$rsResult->fields['st_udf10'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf11']=$rsResult->fields['st_udf11'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf12']=$rsResult->fields['st_udf12'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf13']=$rsResult->fields['st_udf13'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf14']=$rsResult->fields['st_udf14'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf15']=$rsResult->fields['st_udf15'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf6']=$rsResult->fields['bt_udf6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf7']=$rsResult->fields['bt_udf7'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf8']=$rsResult->fields['bt_udf8'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf9']=$rsResult->fields['bt_udf9'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf10']=$rsResult->fields['bt_udf10'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf11']=$rsResult->fields['bt_udf11'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf12']=$rsResult->fields['bt_udf12'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf13']=$rsResult->fields['bt_udf13'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf14']=$rsResult->fields['bt_udf14'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf15']=$rsResult->fields['bt_udf15'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf16']=$rsResult->fields['bt_udf16'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf17']=$rsResult->fields['bt_udf17'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf18']=$rsResult->fields['bt_udf18'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf16']=$rsResult->fields['st_udf16'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf17']=$rsResult->fields['st_udf17'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf18']=$rsResult->fields['st_udf18'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cancel_order']=$rsResult->fields['cancel_order'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line6']=$rsResult->fields['store_address_line6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['rounded_due_amt']=$rsResult->fields['rounded_due_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_quantity_filled']=$rsResult->fields['order_quantity_filled'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tenders']="[]";




                $ctr = $ctr + 1;

                $rsResult->Movenext();

            }
        } ELSE {

            $fields = implode(", ", $flds);

            $sql = "SELECT "
                    . "a.*, b.*, a.sid source_sid "
                    . "FROM "
                    . "rps.document a "
                    . "LEFT JOIN rps.document_item b ON (a.sid = b.doc_sid) "
                    . "LEFT JOIN rps.store d ON (b.fulfill_store_sid = d.sid) "
                    . "LEFT JOIN rps.store e ON (a.store_sid = e.sid) "
                    . "WHERE 1=1 "
                    . "and b.note10 IN ('SAME STORE') "
                    . "and b.item_type = '3' "
                    . "and a.sid = '{$_GET['docsid']}'";
            $ctr = 0;
            $ordertype   = '';
            $fulfilltype = '';

            $arr = array();
            $rsResult = $conn->Execute($sql);
            while(!$rsResult->EOF){

                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['created_by']=$rsResult->fields['created_by'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['created_datetime']=$rsResult->fields['created_datetime'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['modified_by']=$rsResult->fields['modified_by'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['modified_datetime']=$rsResult->fields['modified_datetime'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['origin_application']=$rsResult->fields['origin_application'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['post_date']=$rsResult->fields['post_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['invoice_posted_date']=$rsResult->fields['invoice_posted_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tracking_number']=$rsResult->fields['tracking_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['use_vat']=$rsResult->fields['use_vat'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['vat_options']=$rsResult->fields['vat_options'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['customer_po_number']=$rsResult->fields['customer_po_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_percent']=$rsResult->fields['tax_rebate_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_amt']=$rsResult->fields['tax_rebate_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['over_tax_percent']=$rsResult->fields['over_tax_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['over_tax_percent2']=$rsResult->fields['over_tax_percent2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['rounding_offset']=$rsResult->fields['rounding_offset'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['was_audited']=$rsResult->fields['was_audited'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_sequence_number']=$rsResult->fields['workstation_sequence_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['customer_field']=$rsResult->fields['customer_field'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['is_held']=$rsResult->fields['is_held'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['drawer_number']=$rsResult->fields['drawer_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['elapsed_time']=$rsResult->fields['elapsed_time'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity_percent']=$rsResult->fields['activity_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity2_percent']=$rsResult->fields['activity2_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity3_percent']=$rsResult->fields['activity3_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity4_percent']=$rsResult->fields['activity4_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['activity5_percent']=$rsResult->fields['activity5_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['eft_invoice_number']=$rsResult->fields['eft_invoice_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['detax_flag']=$rsResult->fields['detax_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_percentage']=$rsResult->fields['shipping_percentage'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt']=$rsResult->fields['shipping_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_tax_included']=$rsResult->fields['shipping_tax_included'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_tax_percentage']=$rsResult->fields['shipping_tax_percentage'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_tax_amt']=$rsResult->fields['shipping_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_method']=$rsResult->fields['ship_method'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fiscal_document_number']=$rsResult->fields['fiscal_document_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string1']="SE";
//                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string1']=$rsResult->fields['udf_string1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string2']=$rsResult->fields['udf_string2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string3']=$rsResult->fields['udf_string3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string4']=$rsResult->fields['udf_string4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_string5']=$rsResult->fields['udf_string5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float1']=$rsResult->fields['udf_float1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float2']=$rsResult->fields['udf_float2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float3']=$rsResult->fields['udf_float3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float4']=$rsResult->fields['udf_float4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_float5']=$rsResult->fields['udf_float5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_date1']=$rsResult->fields['udf_date1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_date2']=$rsResult->fields['udf_date2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_date3']=$rsResult->fields['udf_date3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['udf_clob1']=$rsResult->fields['udf_clob1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['archived']=$rsResult->fields['archived'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_fee_amt']=$rsResult->fields['total_fee_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sale_total_tax_amt']=$rsResult->fields['sale_total_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sale_total_amt']=$rsResult->fields['sale_total_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sale_subtotal']=$rsResult->fields['sale_subtotal'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_amt_required']=$rsResult->fields['deposit_amt_required'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['due_amt']=$rsResult->fields['due_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['sold_qty']=$rsResult->fields['sold_qty'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_qty']=$rsResult->fields['return_qty'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['doc_tender_type']=$rsResult->fields['doc_tender_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['subsidiary_number']=$rsResult->fields['subsidiary_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_number']=$rsResult->fields['store_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_code']=$rsResult->fields['store_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_name']=$rsResult->fields['workstation_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store_number']=$rsResult->fields['original_store_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store_code']=$rsResult->fields['original_store_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_name']=$rsResult->fields['tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_name']=$rsResult->fields['tax_area2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_login_name']=$rsResult->fields['employee1_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_full_name']=$rsResult->fields['employee1_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_login_name']=$rsResult->fields['employee2_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_full_name']=$rsResult->fields['employee2_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_login_name']=$rsResult->fields['employee3_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_full_name']=$rsResult->fields['employee3_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_login_name']=$rsResult->fields['employee4_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_full_name']=$rsResult->fields['employee4_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_login_name']=$rsResult->fields['employee5_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_full_name']=$rsResult->fields['employee5_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_login_name']=$rsResult->fields['cashier_login_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_full_name']=$rsResult->fields['cashier_full_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type1']=$rsResult->fields['fee_type1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name1']=$rsResult->fields['fee_name1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1']=$rsResult->fields['fee_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included1']=$rsResult->fields['fee_tax_included1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_amt1']=$rsResult->fields['fee_tax_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_perc1']=$rsResult->fields['fee_tax_perc1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type2']=$rsResult->fields['fee_type2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name2']=$rsResult->fields['fee_name2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included2']=$rsResult->fields['fee_tax_included2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type3']=$rsResult->fields['fee_type3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name3']=$rsResult->fields['fee_name3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included3']=$rsResult->fields['fee_tax_included3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_perc3']=$rsResult->fields['fee_tax_perc3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type4']=$rsResult->fields['fee_type4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name4']=$rsResult->fields['fee_name4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included4']=$rsResult->fields['fee_tax_included4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type5']=$rsResult->fields['fee_type5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_name5']=$rsResult->fields['fee_name5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt5']=$rsResult->fields['fee_amt5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_tax_included5']=$rsResult->fields['fee_tax_included5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt1']=$rsResult->fields['hist_discount_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc1']=$rsResult->fields['hist_discount_perc1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason1']=$rsResult->fields['hist_discount_reason1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt2']=$rsResult->fields['hist_discount_amt2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc2']=$rsResult->fields['hist_discount_perc2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason2']=$rsResult->fields['hist_discount_reason2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt3']=$rsResult->fields['hist_discount_amt3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc3']=$rsResult->fields['hist_discount_perc3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason3']=$rsResult->fields['hist_discount_reason3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt4']=$rsResult->fields['hist_discount_amt4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc4']=$rsResult->fields['hist_discount_perc4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason4']=$rsResult->fields['hist_discount_reason4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_amt5']=$rsResult->fields['hist_discount_amt5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_perc5']=$rsResult->fields['hist_discount_perc5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['hist_discount_reason5']=$rsResult->fields['hist_discount_reason5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_cuid']=$rsResult->fields['bt_cuid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_id']=$rsResult->fields['bt_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_last_name']=$rsResult->fields['bt_last_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_first_name']=$rsResult->fields['bt_first_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_company_name']=$rsResult->fields['bt_company_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_title']=$rsResult->fields['bt_title'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_tax_area_name']=$rsResult->fields['bt_tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_tax_area2_name']=$rsResult->fields['bt_tax_area2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_detax_flag']=$rsResult->fields['bt_detax_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_price_lvl_name']=$rsResult->fields['bt_price_lvl_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_price_lvl']=$rsResult->fields['bt_price_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_security_lvl']=$rsResult->fields['bt_security_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_primary_phone_no']=$rsResult->fields['bt_primary_phone_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line1']=$rsResult->fields['bt_address_line1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line2']=$rsResult->fields['bt_address_line2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line3']=$rsResult->fields['bt_address_line3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line4']=$rsResult->fields['bt_address_line4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line5']=$rsResult->fields['bt_address_line5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_address_line6']=$rsResult->fields['bt_address_line6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_country']=$rsResult->fields['bt_country'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_postal_code']=$rsResult->fields['bt_postal_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_postal_code_extension']=$rsResult->fields['bt_postal_code_extension'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_primary']=$rsResult->fields['bt_primary'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_email']=$rsResult->fields['bt_email'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_customer_lookup']=$rsResult->fields['bt_customer_lookup'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_cuid']=$rsResult->fields['st_cuid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_id']=$rsResult->fields['st_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_last_name']=$rsResult->fields['st_last_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_first_name']=$rsResult->fields['st_first_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_company_name']=$rsResult->fields['st_company_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_title']=$rsResult->fields['st_title'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_tax_area_name']=$rsResult->fields['st_tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_tax_area2_name']=$rsResult->fields['st_tax_area2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_detax_flag']=$rsResult->fields['st_detax_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_price_lvl_name']=$rsResult->fields['st_price_lvl_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_price_lvl']=$rsResult->fields['st_price_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_security_lvl']=$rsResult->fields['st_security_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_primary_phone_no']=$rsResult->fields['st_primary_phone_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line1']=$rsResult->fields['st_address_line1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line2']=$rsResult->fields['st_address_line2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line3']=$rsResult->fields['st_address_line3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line4']=$rsResult->fields['st_address_line4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line5']=$rsResult->fields['st_address_line5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_address_line6']=$rsResult->fields['st_address_line6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_country']=$rsResult->fields['st_country'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_postal_code']=$rsResult->fields['st_postal_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_postal_code_extension']=$rsResult->fields['st_postal_code_extension'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_primary']=$rsResult->fields['st_primary'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_email']=$rsResult->fields['st_email'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_customer_lookup']=$rsResult->fields['st_customer_lookup'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tender_name']=$rsResult->fields['tender_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['currency_name']=$rsResult->fields['currency_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['till_name']=$rsResult->fields['till_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tenders: [],']=$rsResult->fields['tenders: [],'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['given_amt']=$rsResult->fields['given_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['price_lvl']=$rsResult->fields['price_lvl'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['price_lvl_name']=$rsResult->fields['price_lvl_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_uid']=$rsResult->fields['workstation_uid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['subsidiary_name']=$rsResult->fields['subsidiary_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_name']=$rsResult->fields['store_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_percent']=$rsResult->fields['tax_area_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_percent']=$rsResult->fields['tax_area2_percent'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_amt']=$rsResult->fields['tax_area_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_amt']=$rsResult->fields['tax_area2_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_sales_tax_amt']=$rsResult->fields['tax_area_sales_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_sales_tax_amt']=$rsResult->fields['tax_area2_sales_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area_order_tax_amt']=$rsResult->fields['tax_area_order_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_area2_order_tax_amt']=$rsResult->fields['tax_area2_order_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['reason_code']=$rsResult->fields['reason_code'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['reason_description']=$rsResult->fields['reason_description'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['pos_flag1']=$rsResult->fields['pos_flag1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['pos_flag2']=$rsResult->fields['pos_flag2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['pos_flag3']=$rsResult->fields['pos_flag3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['comment1']=$rsResult->fields['comment1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['comment2']=$rsResult->fields['comment2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_amt_taken']=$rsResult->fields['deposit_amt_taken'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_ref_doc_sid']=$rsResult->fields['deposit_ref_doc_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_deposit_taken']=$rsResult->fields['total_deposit_taken'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_general']=$rsResult->fields['notes_general'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_lostdoc']=$rsResult->fields['notes_lostdoc'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_order']=$rsResult->fields['note5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_return']=$rsResult->fields['notes_return'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['notes_sale']=$rsResult->fields['notes_sale'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['custom_flag']=$rsResult->fields['custom_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_cash']=$rsResult->fields['allow_tender_cash'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['docallow_tender_check']=$rsResult->fields['docallow_tender_check'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_credit_card']=$rsResult->fields['allow_tender_credit_card'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_debit_card']=$rsResult->fields['allow_tender_debit_card'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_foreign_check']=$rsResult->fields['allow_tender_foreign_check'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_foreign_currency']=$rsResult->fields['allow_tender_foreign_currency'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_gift_card']=$rsResult->fields['allow_tender_gift_card'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_gift_certificate']=$rsResult->fields['allow_tender_gift_certificate'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['allow_tender_traveler_check']=$rsResult->fields['allow_tender_traveler_check'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['controller_number']=$rsResult->fields['controller_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['subsidiary_uid']=$rsResult->fields['subsidiary_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_uid']=$rsResult->fields['store_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ordered_date']=$rsResult->fields['ordered_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_date']=$rsResult->fields['ship_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cancel_date']=$rsResult->fields['cancel_date'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_partial']=$rsResult->fields['ship_partial'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_priority']=$rsResult->fields['ship_priority'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['till_sid']=$rsResult->fields['till_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_sid']=$rsResult->fields['employee1_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_sid']=$rsResult->fields['employee2_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_sid']=$rsResult->fields['employee3_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_sid']=$rsResult->fields['employee4_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_sid']=$rsResult->fields['employee5_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_sid']=$rsResult->fields['cashier_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['exchange_qty']=$rsResult->fields['exchange_qty'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['createdby_employeeid']=$rsResult->fields['createdby_employeeid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['modifiedby_employeeid']=$rsResult->fields['modifiedby_employeeid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_id']=$rsResult->fields['employee1_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_id']=$rsResult->fields['employee2_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_order_sid']=$rsResult->fields['ref_order_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['workstation_number']=$rsResult->fields['workstation_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_id']=$rsResult->fields['employee3_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_id']=$rsResult->fields['employee4_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_id']=$rsResult->fields['employee5_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_id']=$rsResult->fields['cashier_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['so_cancel_flag']=$rsResult->fields['so_cancel_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_deposit_used']=$rsResult->fields['total_deposit_used'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['deposit_available_amt']=$rsResult->fields['deposit_available_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1']=$rsResult->fields['order_fee_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_name1']=$rsResult->fields['order_fee_name1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_tax_amt1']=$rsResult->fields['order_fee_tax_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_tax_included1']=$rsResult->fields['order_fee_tax_included1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_tax_perc1']=$rsResult->fields['order_fee_tax_perc1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_type1']=$rsResult->fields['order_fee_type1'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt']=$rsResult->fields['order_shipping_amt'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_tax_amt']=$rsResult->fields['order_shipping_tax_amt'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_tax_included']=$rsResult->fields['order_shipping_tax_included'];
    //            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_tax_perc']=$rsResult->fields['order_shipping_tax_perc'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_type1_sid']=$rsResult->fields['fee_type1_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_sid']=$rsResult->fields['shipping_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_type1_sid']=$rsResult->fields['order_fee_type1_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_sid']=$rsResult->fields['order_shipping_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_percentage']=$rsResult->fields['order_shipping_percentage'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1_no_tax']=$rsResult->fields['fee_amt1_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1_with_tax']=$rsResult->fields['fee_amt1_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1_no_tax']=$rsResult->fields['order_fee_amt1_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1_with_tax']=$rsResult->fields['order_fee_amt1_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_no_tax']=$rsResult->fields['order_shipping_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_with_tax']=$rsResult->fields['order_shipping_amt_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_no_tax']=$rsResult->fields['shipping_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_with_tax']=$rsResult->fields['shipping_amt_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_subtotal']=$rsResult->fields['used_subtotal'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_tax']=$rsResult->fields['used_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_fee_amt1']=$rsResult->fields['used_fee_amt1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['used_shipping_amt']=$rsResult->fields['used_shipping_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_changed_flag']=$rsResult->fields['order_changed_flag'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['promo_gd_modifiedmanually']=$rsResult->fields['promo_gd_modifiedmanually'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_fee_amt_no_tax']=$rsResult->fields['total_fee_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['total_fee_tax_amt']=$rsResult->fields['total_fee_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_balance_due']=$rsResult->fields['order_balance_due'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['original_store_uid']=$rsResult->fields['original_store_uid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line1']=$rsResult->fields['store_address_line1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line2']=$rsResult->fields['store_address_line2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line3']=$rsResult->fields['store_address_line3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line4']=$rsResult->fields['store_address_line4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line5']=$rsResult->fields['store_address_line5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_zip']=$rsResult->fields['store_address_zip'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_phone1']=$rsResult->fields['store_phone1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_phone2']=$rsResult->fields['store_phone2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['has_sale']=$rsResult->fields['has_sale'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['has_return']=$rsResult->fields['has_return'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['has_deposit']=$rsResult->fields['has_deposit'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['receipt_type']=$rsResult->fields['receipt_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_type']=$rsResult->fields['order_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_method_id']=$rsResult->fields['ship_method_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ship_method_sid']=$rsResult->fields['ship_method_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_ship_method']=$rsResult->fields['order_ship_method'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_ship_method_id']=$rsResult->fields['order_ship_method_id'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_ship_method_sid']=$rsResult->fields['order_ship_method_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['from_centrals']=$rsResult->fields['from_centrals'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_copy']=$rsResult->fields['send_sale_copy'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_status']=$rsResult->fields['send_sale_status'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_last_event']=$rsResult->fields['send_sale_last_event'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_last_error']=$rsResult->fields['send_sale_last_error'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['send_sale_fulfillment']=$rsResult->fields['send_sale_fulfillment'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['detax_amount']=$rsResult->fields['detax_amount'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee1_name']=$rsResult->fields['employee1_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee2_name']=$rsResult->fields['employee2_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee3_name']=$rsResult->fields['employee3_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee4_name']=$rsResult->fields['employee4_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['employee5_name']=$rsResult->fields['employee5_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cashier_name']=$rsResult->fields['cashier_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_manual']=$rsResult->fields['order_shipping_amt_manual'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_persisted']=$rsResult->fields['tax_rebate_persisted'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_status']=$rsResult->fields['order_status'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_manual']=$rsResult->fields['shipping_amt_manual'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_manual_used']=$rsResult->fields['order_shipping_amt_manual_used'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_tracking_number']=$rsResult->fields['order_tracking_number'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['transaction_total_shipping_tax']=$rsResult->fields['transaction_total_shipping_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['transaction_total_shipping_amt_no_tax']=$rsResult->fields['transaction_total_shipping_amt_no_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['transaction_total_shipping_amt_with_tax']=$rsResult->fields['transaction_total_shipping_amt_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_subtotal']=$rsResult->fields['return_subtotal'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_subtotal_with_tax']=$rsResult->fields['return_subtotal_with_tax'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_tax1_amt']=$rsResult->fields['return_tax1_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_tax2_amt']=$rsResult->fields['return_tax2_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['return_total_tax_amt']=$rsResult->fields['return_total_tax_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt1_bdt']=$rsResult->fields['fee_amt1_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_manual_bdt']=$rsResult->fields['shipping_amt_manual_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fee_amt1_bdt']=$rsResult->fields['order_fee_amt1_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_shipping_amt_manual_bdt']=$rsResult->fields['order_shipping_amt_manual_bdt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_fulfilling_amt']=$rsResult->fields['order_fulfilling_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tax_rebate_available']=$rsResult->fields['tax_rebate_available'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['refund_document_sid']=$rsResult->fields['refund_document_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['due_amt_current']=$rsResult->fields['due_amt_current'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_order_order_doc_no']=$rsResult->fields['ref_order_order_doc_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_order_balance_due']=$rsResult->fields['ref_order_balance_due'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['coupons: [],']=$rsResult->fields['coupons: [],'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_total_redeem_pgm_sid']=$rsResult->fields['lty_total_redeem_pgm_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_total_redeem_pgm_name']=$rsResult->fields['lty_total_redeem_pgm_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_start_balance']=$rsResult->fields['lty_start_balance'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_end_balance']=$rsResult->fields['lty_end_balance'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_redeem_amt']=$rsResult->fields['lty_redeem_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['fee_amt_returned1']=$rsResult->fields['fee_amt_returned1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['shipping_amt_manual_returned']=$rsResult->fields['shipping_amt_manual_returned'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_item_earn_pgm_sid']=$rsResult->fields['lty_item_earn_pgm_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['lty_item_earn_pgm_name']=$rsResult->fields['lty_item_earn_pgm_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_sid']=$rsResult->fields['ref_sale_sid'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_doc_no']=$rsResult->fields['ref_sale_doc_no'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_created_datetime']=$rsResult->fields['ref_sale_created_datetime'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['ref_sale_tax_area_name']=$rsResult->fields['ref_sale_tax_area_name'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['gift_receipt_type']=$rsResult->fields['gift_receipt_type'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf1']=$rsResult->fields['bt_udf1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf2']=$rsResult->fields['bt_udf2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf3']=$rsResult->fields['bt_udf3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf4']=$rsResult->fields['bt_udf4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf5']=$rsResult->fields['bt_udf5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf1']=$rsResult->fields['st_udf1'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf2']=$rsResult->fields['st_udf2'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf3']=$rsResult->fields['st_udf3'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf4']=$rsResult->fields['st_udf4'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf5']=$rsResult->fields['st_udf5'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf6']=$rsResult->fields['st_udf6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf7']=$rsResult->fields['st_udf7'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf8']=$rsResult->fields['st_udf8'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf9']=$rsResult->fields['st_udf9'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf10']=$rsResult->fields['st_udf10'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf11']=$rsResult->fields['st_udf11'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf12']=$rsResult->fields['st_udf12'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf13']=$rsResult->fields['st_udf13'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf14']=$rsResult->fields['st_udf14'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf15']=$rsResult->fields['st_udf15'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf6']=$rsResult->fields['bt_udf6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf7']=$rsResult->fields['bt_udf7'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf8']=$rsResult->fields['bt_udf8'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf9']=$rsResult->fields['bt_udf9'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf10']=$rsResult->fields['bt_udf10'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf11']=$rsResult->fields['bt_udf11'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf12']=$rsResult->fields['bt_udf12'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf13']=$rsResult->fields['bt_udf13'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf14']=$rsResult->fields['bt_udf14'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf15']=$rsResult->fields['bt_udf15'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf16']=$rsResult->fields['bt_udf16'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf17']=$rsResult->fields['bt_udf17'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['bt_udf18']=$rsResult->fields['bt_udf18'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf16']=$rsResult->fields['st_udf16'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf17']=$rsResult->fields['st_udf17'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['st_udf18']=$rsResult->fields['st_udf18'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['cancel_order']=$rsResult->fields['cancel_order'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['store_address_line6']=$rsResult->fields['store_address_line6'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['rounded_due_amt']=$rsResult->fields['rounded_due_amt'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['order_quantity_filled']=$rsResult->fields['order_quantity_filled'];
                $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['tenders']="[]";




                $ctr = $ctr + 1;

                $rsResult->Movenext();

            }
        }

        $k = array();
        
        foreach ($arr as $keyB => $valueB){
            echo "<pre>";
            print_r($keyB);
            echo "</pre>";
            $data = "";
            foreach($valueB as $keyD => $valueD){

                if (TRUE == $conn){
                     $sql = "SELECT "
                        . "a.* "
                        . "FROM "
                        . "document_item a "
                        . "WHERE 1=1 "
                        . "and a.note10 IN ('SAME STORE') "
                        . "and a.note8 IN ('".$keyB."') "
                        . "and a.item_type = '3' "
                        . "and a.doc_sid = '{$_GET['docsid']}'";
                    $ctr = 0;
                    
                    $m = array();
                    $conn->debug = true;
                    $rsResult = $conn->Execute($sql);
                    while(!$rsResult->EOF){
                        $m['created_by']=$rsResult->fields['created_by'];
                        $m['created_datetime']=$rsResult->fields['created_datetime'];
                        $m['modified_by']=$rsResult->fields['modified_by'];
                        $m['modified_datetime']=$rsResult->fields['modified_datetime'];
                        $m['origin_application']=$rsResult->fields['origin_application'];
                        $m['post_date']=$rsResult->fields['post_date'];
                        $m['quantity']=$rsResult->fields['quantity'];
                        $m['detax_flag']=$rsResult->fields['detax_flag'];
                        $m['price_before_detax']=$rsResult->fields['price_before_detax'];
                        $m['original_price_before_detax']=$rsResult->fields['original_price_before_detax'];
                        $m['spif']=$rsResult->fields['spif'];
                        $m['schedule_number']=$rsResult->fields['schedule_number'];
                        $m['kit_flag']=$rsResult->fields['kit_flag'];
                        $m['user_discount_percent']=$rsResult->fields['user_discount_percent'];
                        $m['package_sequence_number']=$rsResult->fields['package_sequence_number'];
                        $m['lot_number']=$rsResult->fields['lot_number'];
                        $m['activity_percent']=$rsResult->fields['activity_percent'];
                        $m['activity2_percent']=$rsResult->fields['activity2_percent'];
                        $m['activity3_percent']=$rsResult->fields['activity3_percent'];
                        $m['activity4_percent']=$rsResult->fields['activity4_percent'];
                        $m['activity5_percent']=$rsResult->fields['activity5_percent'];
                        $m['commission_amount']=$rsResult->fields['commission_amount'];
                        $m['commission2_amount']=$rsResult->fields['commission2_amount'];
                        $m['commission3_amount']=$rsResult->fields['commission3_amount'];
                        $m['commission4_amount']=$rsResult->fields['commission4_amount'];
                        $m['commission5_amount']=$rsResult->fields['commission5_amount'];
                        $m['item_origin']=$rsResult->fields['item_origin'];
                        $m['package_number']=$rsResult->fields['package_number'];
                        $m['ship_id']=$rsResult->fields['ship_id'];
                        $m['ship_method']=$rsResult->fields['ship_method'];
                        $m['shipping_amt']=$rsResult->fields['shipping_amt'];
                        $m['tracking_number']=$rsResult->fields['tracking_number'];
                        $m['promotion_flag']=$rsResult->fields['promotion_flag'];
                        $m['gift_activation_code']=$rsResult->fields['gift_activation_code'];
                        $m['gift_transaction_id']=$rsResult->fields['gift_transaction_id'];
                        $m['gift_add_value']=$rsResult->fields['gift_add_value'];
                        $m['customer_field']=$rsResult->fields['customer_field'];
                        $m['udf_string01']=$rsResult->fields['udf_string01'];
                        $m['udf_string02']=$rsResult->fields['udf_string02'];
                        $m['udf_string03']=$rsResult->fields['udf_string03'];
                        $m['udf_string04']=$rsResult->fields['udf_string04'];
                        $m['udf_string05']=$rsResult->fields['udf_string05'];
                        $m['udf_date01']=$rsResult->fields['udf_date01'];
                        $m['udf_float01']=$rsResult->fields['udf_float01'];
                        $m['udf_float02']=$rsResult->fields['udf_float02'];
                        $m['udf_float03']=$rsResult->fields['udf_float03'];
                        $m['archived']=$rsResult->fields['archived'];
                        $m['total_discount_percent']=$rsResult->fields['total_discount_percent'];
                        $m['note1']=$rsResult->fields['note1'];
                        $m['note2']=$rsResult->fields['note2'];
                        $m['note3']=$rsResult->fields['note3'];
                        $m['note4']=$rsResult->fields['note4'];
                        $m['note5']=$rsResult->fields['note5'];
                        $m['note6']=$rsResult->fields['note6'];
                        $m['note7']=$rsResult->fields['note7'];
                        $m['note8']=$rsResult->fields['note8'];
                        $m['note9']=$rsResult->fields['note9'];
                        $m['note10']=$rsResult->fields['note10'];
                        $m['item_type']=$rsResult->fields['item_type'];
                        $m['commission_code']=$rsResult->fields['commission_code'];
                        $m['commission_level']=$rsResult->fields['commission_level'];
                        $m['commission_percent']=$rsResult->fields['commission_percent'];
                        $m['st_cuid']=$rsResult->fields['st_cuid'];
                        $m['st_id']=$rsResult->fields['st_id'];
                        $m['st_last_name']=$rsResult->fields['st_last_name'];
                        $m['st_first_name']=$rsResult->fields['st_first_name'];
                        $m['st_company_name']=$rsResult->fields['st_company_name'];
                        $m['st_title']=$rsResult->fields['st_title'];
                        $m['st_tax_area_name']=$rsResult->fields['st_tax_area_name'];
                        $m['st_tax_area2_name']=$rsResult->fields['st_tax_area2_name'];
                        $m['st_detax_flag']=$rsResult->fields['st_detax_flag'];
                        $m['st_price_lvl_name']=$rsResult->fields['st_price_lvl_name'];
                        $m['st_price_lvl']=$rsResult->fields['st_price_lvl'];
                        $m['st_security_lvl']=$rsResult->fields['st_security_lvl'];
                        $m['st_primary_phone_no']=$rsResult->fields['st_primary_phone_no'];
                        $m['st_country']=$rsResult->fields['st_country'];
                        $m['st_postal_code']=$rsResult->fields['st_postal_code'];
                        $m['st_postal_code_extension']=$rsResult->fields['st_postal_code_extension'];
                        $m['st_primary']=$rsResult->fields['st_primary'];
                        $m['st_address_line1']=$rsResult->fields['st_address_line1'];
                        $m['st_address_line2']=$rsResult->fields['st_address_line2'];
                        $m['st_address_line3']=$rsResult->fields['st_address_line3'];
                        $m['st_address_line4']=$rsResult->fields['st_address_line4'];
                        $m['st_address_line5']=$rsResult->fields['st_address_line5'];
                        $m['st_address_line6']=$rsResult->fields['st_address_line6'];
                        $m['st_email']=$rsResult->fields['st_email'];
                        $m['st_customer_lookup']=$rsResult->fields['st_customer_lookup'];
                        $m['employee1_login_name']=$rsResult->fields['employee1_login_name'];
                        $m['employee1_full_name']=$rsResult->fields['employee1_full_name'];
                        $m['employee2_login_name']=$rsResult->fields['employee2_login_name'];
                        $m['employee2_full_name']=$rsResult->fields['employee2_full_name'];
                        $m['employee3_login_name']=$rsResult->fields['employee3_login_name'];
                        $m['employee3_full_name']=$rsResult->fields['employee3_full_name'];
                        $m['employee4_login_name']=$rsResult->fields['employee4_login_name'];
                        $m['employee4_full_name']=$rsResult->fields['employee4_full_name'];
                        $m['employee5_login_name']=$rsResult->fields['employee5_login_name'];
                        $m['employee5_full_name']=$rsResult->fields['employee5_full_name'];
                        $m['hist_discount_amt1']=$rsResult->fields['hist_discount_amt1'];
                        $m['hist_discount_perc1']=$rsResult->fields['hist_discount_perc1'];
                        $m['hist_discount_reason1']=$rsResult->fields['hist_discount_reason1'];
                        $m['hist_discount_amt2']=$rsResult->fields['hist_discount_amt2'];
                        $m['hist_discount_perc2']=$rsResult->fields['hist_discount_perc2'];
                        $m['hist_discount_reason2']=$rsResult->fields['hist_discount_reason2'];
                        $m['hist_discount_amt3']=$rsResult->fields['hist_discount_amt3'];
                        $m['hist_discount_perc3']=$rsResult->fields['hist_discount_perc3'];
                        $m['hist_discount_reason3']=$rsResult->fields['hist_discount_reason3'];
                        $m['hist_discount_amt4']=$rsResult->fields['hist_discount_amt4'];
                        $m['hist_discount_perc4']=$rsResult->fields['hist_discount_perc4'];
                        $m['hist_discount_reason4']=$rsResult->fields['hist_discount_reason4'];
                        $m['hist_discount_amt5']=$rsResult->fields['hist_discount_amt5'];
                        $m['hist_discount_perc5']=$rsResult->fields['hist_discount_perc5'];
                        $m['hist_discount_reason5']=$rsResult->fields['hist_discount_reason5'];
                        $m['store_number']=$rsResult->fields['store_number'];
                        $m['order_type']=$rsResult->fields['order_type'];
                        $m['so_number']=$rsResult->fields['so_number'];
                        $m['subsidiary_number']=$rsResult->fields['subsidiary_number'];
                        $m['returned_item_qty']=$rsResult->fields['returned_item_qty'];
                        $m['returned_item_invoice_sid']=$rsResult->fields['returned_item_invoice_sid'];
                        $m['delete_discount']=$rsResult->fields['delete_discount'];
                        $m['invn_sbs_item_sid']=$rsResult->fields['invn_sbs_item_sid'];
                        $m['custom_flag']=$rsResult->fields['custom_flag'];
                        $m['return_reason']=$rsResult->fields['return_reason'];
                        $m['price_lvl']=$rsResult->fields['price_lvl'];
                        $m['order_quantity_filled']=$rsResult->fields['order_quantity_filled'];
                        $m['gift_quantity']=$rsResult->fields['gift_quantity'];
                        $m['so_deposit_amt']=$rsResult->fields['so_deposit_amt'];
                        $m['invn_item_uid']=$rsResult->fields['invn_item_uid'];
                        $m['ref_order_item_sid']=$rsResult->fields['ref_order_item_sid'];
                        $m['tax_area_name']=$rsResult->fields['tax_area_name'];
                        $m['tax_area2_name']=$rsResult->fields['tax_area2_name'];
                        $m['serial_type']=$rsResult->fields['serial_type'];
                        $m['lot_type']=$rsResult->fields['lot_type'];
                        $m['price_lvl_sid']=$rsResult->fields['price_lvl_sid'];
                        $m['price_lvl_name']=$rsResult->fields['price_lvl_name'];
                        $m['so_cancel_flag']=$rsResult->fields['so_cancel_flag'];
                        $m['ref_sale_doc_sid']=$rsResult->fields['ref_sale_doc_sid'];
                        $m['fulfill_store_sid']=$rsResult->fields['fulfill_store_sid'];
                        $m['fulfill_store_no']=$rsResult->fields['fulfill_store_no'];
                        $m['fulfill_store_sbs_no']=$rsResult->fields['fulfill_store_sbs_no'];
                        $m['central_document_sid']=$rsResult->fields['central_document_sid'];
                        $m['central_item_pos']=$rsResult->fields['central_item_pos'];
                        $m['ref_order_doc_sid']=$rsResult->fields['ref_order_doc_sid'];
                        $m['employee1_sid']=$rsResult->fields['employee1_sid'];
                        $m['employee2_sid']=$rsResult->fields['employee2_sid'];
                        $m['employee3_sid']=$rsResult->fields['employee3_sid'];
                        $m['employee4_sid']=$rsResult->fields['employee4_sid'];
                        $m['employee5_sid']=$rsResult->fields['employee5_sid'];
                        $m['employee1_id']=$rsResult->fields['employee1_id'];
                        $m['employee2_id']=$rsResult->fields['employee2_id'];
                        $m['employee3_id']=$rsResult->fields['employee3_id'];
                        $m['employee4_id']=$rsResult->fields['employee4_id'];
                        $m['employee5_id']=$rsResult->fields['employee5_id'];
                        $m['ref_sale_item_pos']=$rsResult->fields['ref_sale_item_pos'];
                        $m['item_status']=$rsResult->fields['item_status'];
                        $m['enhanced_item_pos']=$rsResult->fields['enhanced_item_pos'];
                        $m['is_competing_component']=$rsResult->fields['is_competing_component'];
                        $m['package_item_uid']=$rsResult->fields['package_item_uid'];
                        $m['original_component_item_uid']=$rsResult->fields['original_component_item_uid'];
                        $m['promo_disc_modifiedmanually']=$rsResult->fields['promo_disc_modifiedmanually'];
                        $m['ship_method_sid']=$rsResult->fields['ship_method_sid'];
                        $m['ship_method_id']=$rsResult->fields['ship_method_id'];
                        $m['order_ship_method_sid']=$rsResult->fields['order_ship_method_sid'];
                        $m['order_ship_method_id']=$rsResult->fields['order_ship_method_id'];
                        $m['order_ship_method']=$rsResult->fields['order_ship_method'];
                        $m['from_centrals']=$rsResult->fields['from_centrals'];
                        $m['tax_perc_lock']=$rsResult->fields['tax_perc_lock'];
                        $m['employee1_name']=$rsResult->fields['employee1_name'];
                        $m['employee2_name']=$rsResult->fields['employee2_name'];
                        $m['employee3_name']=$rsResult->fields['employee3_name'];
                        $m['employee4_name']=$rsResult->fields['employee4_name'];
                        $m['employee5_name']=$rsResult->fields['employee5_name'];
                        $m['qty_available_for_return']=$rsResult->fields['qty_available_for_return'];
                        $m['central_return_commit_state']=$rsResult->fields['central_return_commit_state'];
                        $m['inventory_use_quantity_decimals']=$rsResult->fields['inventory_use_quantity_decimals'];
                        $m['shipping_amt_bdt']=$rsResult->fields['shipping_amt_bdt'];
                        $m['tax_code_rule_sid']=$rsResult->fields['tax_code_rule_sid'];
                        $m['tax_code_rule2_sid']=$rsResult->fields['tax_code_rule2_sid'];
                        $m['st_address_uid']=$rsResult->fields['st_address_uid'];
                        $m['tax_char']=$rsResult->fields['tax_char'];
                        $m['tax_char2']=$rsResult->fields['tax_char2'];
                        $m['apply_type_to_all_items']=$rsResult->fields['apply_type_to_all_items'];
                        $m['orig_sale_price']=$rsResult->fields['orig_sale_price'];
                        $m['orig_document_number']=$rsResult->fields['orig_document_number'];
                        $m['orig_store_number']=$rsResult->fields['orig_store_number'];
                        $m['orig_subsidiary_number']=$rsResult->fields['orig_subsidiary_number'];
                        $m['shipping_amt']=$rsResult->fields['ship_amt'];

                        $rsResult->Movenext();
                        $z[] = $m;
                    }
                } ELSE {
                     $sql = "SELECT "
                        . "a.* "
                        . "FROM "
                        . "rps.document_item a "
                        . "WHERE 1=1 "
                        . "and a.note10 IN ('SAME STORE') "
                        . "and a.note8 IN ('".$keyB."') "
                        . "and a.item_type = '3' "
                        . "and a.doc_sid = '{$_GET['docsid']}'";
                    $ctr = 0;

                    $m = array();
                    $rsResult = $conn->Execute($sql);
                    while(!$rsResult->EOF){
                        $m['created_by']=$rsResult->fields['created_by'];
                        $m['created_datetime']=$rsResult->fields['created_datetime'];
                        $m['modified_by']=$rsResult->fields['modified_by'];
                        $m['modified_datetime']=$rsResult->fields['modified_datetime'];
                        $m['origin_application']=$rsResult->fields['origin_application'];
                        $m['post_date']=$rsResult->fields['post_date'];
                        $m['quantity']=$rsResult->fields['quantity'];
                        $m['detax_flag']=$rsResult->fields['detax_flag'];
                        $m['price_before_detax']=$rsResult->fields['price_before_detax'];
                        $m['original_price_before_detax']=$rsResult->fields['original_price_before_detax'];
                        $m['spif']=$rsResult->fields['spif'];
                        $m['schedule_number']=$rsResult->fields['schedule_number'];
                        $m['kit_flag']=$rsResult->fields['kit_flag'];
                        $m['user_discount_percent']=$rsResult->fields['user_discount_percent'];
                        $m['package_sequence_number']=$rsResult->fields['package_sequence_number'];
                        $m['lot_number']=$rsResult->fields['lot_number'];
                        $m['activity_percent']=$rsResult->fields['activity_percent'];
                        $m['activity2_percent']=$rsResult->fields['activity2_percent'];
                        $m['activity3_percent']=$rsResult->fields['activity3_percent'];
                        $m['activity4_percent']=$rsResult->fields['activity4_percent'];
                        $m['activity5_percent']=$rsResult->fields['activity5_percent'];
                        $m['commission_amount']=$rsResult->fields['commission_amount'];
                        $m['commission2_amount']=$rsResult->fields['commission2_amount'];
                        $m['commission3_amount']=$rsResult->fields['commission3_amount'];
                        $m['commission4_amount']=$rsResult->fields['commission4_amount'];
                        $m['commission5_amount']=$rsResult->fields['commission5_amount'];
                        $m['item_origin']=$rsResult->fields['item_origin'];
                        $m['package_number']=$rsResult->fields['package_number'];
                        $m['ship_id']=$rsResult->fields['ship_id'];
                        $m['ship_method']=$rsResult->fields['ship_method'];
                        $m['shipping_amt']=$rsResult->fields['shipping_amt'];
                        $m['tracking_number']=$rsResult->fields['tracking_number'];
                        $m['promotion_flag']=$rsResult->fields['promotion_flag'];
                        $m['gift_activation_code']=$rsResult->fields['gift_activation_code'];
                        $m['gift_transaction_id']=$rsResult->fields['gift_transaction_id'];
                        $m['gift_add_value']=$rsResult->fields['gift_add_value'];
                        $m['customer_field']=$rsResult->fields['customer_field'];
                        $m['udf_string01']=$rsResult->fields['udf_string01'];
                        $m['udf_string02']=$rsResult->fields['udf_string02'];
                        $m['udf_string03']=$rsResult->fields['udf_string03'];
                        $m['udf_string04']=$rsResult->fields['udf_string04'];
                        $m['udf_string05']=$rsResult->fields['udf_string05'];
                        $m['udf_date01']=$rsResult->fields['udf_date01'];
                        $m['udf_float01']=$rsResult->fields['udf_float01'];
                        $m['udf_float02']=$rsResult->fields['udf_float02'];
                        $m['udf_float03']=$rsResult->fields['udf_float03'];
                        $m['archived']=$rsResult->fields['archived'];
                        $m['total_discount_percent']=$rsResult->fields['total_discount_percent'];
                        $m['note1']=$rsResult->fields['note1'];
                        $m['note2']=$rsResult->fields['note2'];
                        $m['note3']=$rsResult->fields['note3'];
                        $m['note4']=$rsResult->fields['note4'];
                        $m['note5']=$rsResult->fields['note5'];
                        $m['note6']=$rsResult->fields['note6'];
                        $m['note7']=$rsResult->fields['note7'];
                        $m['note8']=$rsResult->fields['note8'];
                        $m['note9']=$rsResult->fields['note9'];
                        $m['note10']=$rsResult->fields['note10'];
                        $m['item_type']=$rsResult->fields['item_type'];
                        $m['commission_code']=$rsResult->fields['commission_code'];
                        $m['commission_level']=$rsResult->fields['commission_level'];
                        $m['commission_percent']=$rsResult->fields['commission_percent'];
                        $m['st_cuid']=$rsResult->fields['st_cuid'];
                        $m['st_id']=$rsResult->fields['st_id'];
                        $m['st_last_name']=$rsResult->fields['st_last_name'];
                        $m['st_first_name']=$rsResult->fields['st_first_name'];
                        $m['st_company_name']=$rsResult->fields['st_company_name'];
                        $m['st_title']=$rsResult->fields['st_title'];
                        $m['st_tax_area_name']=$rsResult->fields['st_tax_area_name'];
                        $m['st_tax_area2_name']=$rsResult->fields['st_tax_area2_name'];
                        $m['st_detax_flag']=$rsResult->fields['st_detax_flag'];
                        $m['st_price_lvl_name']=$rsResult->fields['st_price_lvl_name'];
                        $m['st_price_lvl']=$rsResult->fields['st_price_lvl'];
                        $m['st_security_lvl']=$rsResult->fields['st_security_lvl'];
                        $m['st_primary_phone_no']=$rsResult->fields['st_primary_phone_no'];
                        $m['st_country']=$rsResult->fields['st_country'];
                        $m['st_postal_code']=$rsResult->fields['st_postal_code'];
                        $m['st_postal_code_extension']=$rsResult->fields['st_postal_code_extension'];
                        $m['st_primary']=$rsResult->fields['st_primary'];
                        $m['st_address_line1']=$rsResult->fields['st_address_line1'];
                        $m['st_address_line2']=$rsResult->fields['st_address_line2'];
                        $m['st_address_line3']=$rsResult->fields['st_address_line3'];
                        $m['st_address_line4']=$rsResult->fields['st_address_line4'];
                        $m['st_address_line5']=$rsResult->fields['st_address_line5'];
                        $m['st_address_line6']=$rsResult->fields['st_address_line6'];
                        $m['st_email']=$rsResult->fields['st_email'];
                        $m['st_customer_lookup']=$rsResult->fields['st_customer_lookup'];
                        $m['employee1_login_name']=$rsResult->fields['employee1_login_name'];
                        $m['employee1_full_name']=$rsResult->fields['employee1_full_name'];
                        $m['employee2_login_name']=$rsResult->fields['employee2_login_name'];
                        $m['employee2_full_name']=$rsResult->fields['employee2_full_name'];
                        $m['employee3_login_name']=$rsResult->fields['employee3_login_name'];
                        $m['employee3_full_name']=$rsResult->fields['employee3_full_name'];
                        $m['employee4_login_name']=$rsResult->fields['employee4_login_name'];
                        $m['employee4_full_name']=$rsResult->fields['employee4_full_name'];
                        $m['employee5_login_name']=$rsResult->fields['employee5_login_name'];
                        $m['employee5_full_name']=$rsResult->fields['employee5_full_name'];
                        $m['hist_discount_amt1']=$rsResult->fields['hist_discount_amt1'];
                        $m['hist_discount_perc1']=$rsResult->fields['hist_discount_perc1'];
                        $m['hist_discount_reason1']=$rsResult->fields['hist_discount_reason1'];
                        $m['hist_discount_amt2']=$rsResult->fields['hist_discount_amt2'];
                        $m['hist_discount_perc2']=$rsResult->fields['hist_discount_perc2'];
                        $m['hist_discount_reason2']=$rsResult->fields['hist_discount_reason2'];
                        $m['hist_discount_amt3']=$rsResult->fields['hist_discount_amt3'];
                        $m['hist_discount_perc3']=$rsResult->fields['hist_discount_perc3'];
                        $m['hist_discount_reason3']=$rsResult->fields['hist_discount_reason3'];
                        $m['hist_discount_amt4']=$rsResult->fields['hist_discount_amt4'];
                        $m['hist_discount_perc4']=$rsResult->fields['hist_discount_perc4'];
                        $m['hist_discount_reason4']=$rsResult->fields['hist_discount_reason4'];
                        $m['hist_discount_amt5']=$rsResult->fields['hist_discount_amt5'];
                        $m['hist_discount_perc5']=$rsResult->fields['hist_discount_perc5'];
                        $m['hist_discount_reason5']=$rsResult->fields['hist_discount_reason5'];
                        $m['store_number']=$rsResult->fields['store_number'];
                        $m['order_type']=$rsResult->fields['order_type'];
                        $m['so_number']=$rsResult->fields['so_number'];
                        $m['subsidiary_number']=$rsResult->fields['subsidiary_number'];
                        $m['returned_item_qty']=$rsResult->fields['returned_item_qty'];
                        $m['returned_item_invoice_sid']=$rsResult->fields['returned_item_invoice_sid'];
                        $m['delete_discount']=$rsResult->fields['delete_discount'];
                        $m['invn_sbs_item_sid']=$rsResult->fields['invn_sbs_item_sid'];
                        $m['custom_flag']=$rsResult->fields['custom_flag'];
                        $m['return_reason']=$rsResult->fields['return_reason'];
                        $m['price_lvl']=$rsResult->fields['price_lvl'];
                        $m['order_quantity_filled']=$rsResult->fields['order_quantity_filled'];
                        $m['gift_quantity']=$rsResult->fields['gift_quantity'];
                        $m['so_deposit_amt']=$rsResult->fields['so_deposit_amt'];
                        $m['invn_item_uid']=$rsResult->fields['invn_item_uid'];
                        $m['ref_order_item_sid']=$rsResult->fields['ref_order_item_sid'];
                        $m['tax_area_name']=$rsResult->fields['tax_area_name'];
                        $m['tax_area2_name']=$rsResult->fields['tax_area2_name'];
                        $m['serial_type']=$rsResult->fields['serial_type'];
                        $m['lot_type']=$rsResult->fields['lot_type'];
                        $m['price_lvl_sid']=$rsResult->fields['price_lvl_sid'];
                        $m['price_lvl_name']=$rsResult->fields['price_lvl_name'];
                        $m['so_cancel_flag']=$rsResult->fields['so_cancel_flag'];
                        $m['ref_sale_doc_sid']=$rsResult->fields['ref_sale_doc_sid'];
                        $m['fulfill_store_sid']=$rsResult->fields['fulfill_store_sid'];
                        $m['fulfill_store_no']=$rsResult->fields['fulfill_store_no'];
                        $m['fulfill_store_sbs_no']=$rsResult->fields['fulfill_store_sbs_no'];
                        $m['central_document_sid']=$rsResult->fields['central_document_sid'];
                        $m['central_item_pos']=$rsResult->fields['central_item_pos'];
                        $m['ref_order_doc_sid']=$rsResult->fields['ref_order_doc_sid'];
                        $m['employee1_sid']=$rsResult->fields['employee1_sid'];
                        $m['employee2_sid']=$rsResult->fields['employee2_sid'];
                        $m['employee3_sid']=$rsResult->fields['employee3_sid'];
                        $m['employee4_sid']=$rsResult->fields['employee4_sid'];
                        $m['employee5_sid']=$rsResult->fields['employee5_sid'];
                        $m['employee1_id']=$rsResult->fields['employee1_id'];
                        $m['employee2_id']=$rsResult->fields['employee2_id'];
                        $m['employee3_id']=$rsResult->fields['employee3_id'];
                        $m['employee4_id']=$rsResult->fields['employee4_id'];
                        $m['employee5_id']=$rsResult->fields['employee5_id'];
                        $m['ref_sale_item_pos']=$rsResult->fields['ref_sale_item_pos'];
                        $m['item_status']=$rsResult->fields['item_status'];
                        $m['enhanced_item_pos']=$rsResult->fields['enhanced_item_pos'];
                        $m['is_competing_component']=$rsResult->fields['is_competing_component'];
                        $m['package_item_uid']=$rsResult->fields['package_item_uid'];
                        $m['original_component_item_uid']=$rsResult->fields['original_component_item_uid'];
                        $m['promo_disc_modifiedmanually']=$rsResult->fields['promo_disc_modifiedmanually'];
                        $m['ship_method_sid']=$rsResult->fields['ship_method_sid'];
                        $m['ship_method_id']=$rsResult->fields['ship_method_id'];
                        $m['order_ship_method_sid']=$rsResult->fields['order_ship_method_sid'];
                        $m['order_ship_method_id']=$rsResult->fields['order_ship_method_id'];
                        $m['order_ship_method']=$rsResult->fields['order_ship_method'];
                        $m['from_centrals']=$rsResult->fields['from_centrals'];
                        $m['tax_perc_lock']=$rsResult->fields['tax_perc_lock'];
                        $m['employee1_name']=$rsResult->fields['employee1_name'];
                        $m['employee2_name']=$rsResult->fields['employee2_name'];
                        $m['employee3_name']=$rsResult->fields['employee3_name'];
                        $m['employee4_name']=$rsResult->fields['employee4_name'];
                        $m['employee5_name']=$rsResult->fields['employee5_name'];
                        $m['qty_available_for_return']=$rsResult->fields['qty_available_for_return'];
                        $m['central_return_commit_state']=$rsResult->fields['central_return_commit_state'];
                        $m['inventory_use_quantity_decimals']=$rsResult->fields['inventory_use_quantity_decimals'];
                        $m['shipping_amt_bdt']=$rsResult->fields['shipping_amt_bdt'];
                        $m['tax_code_rule_sid']=$rsResult->fields['tax_code_rule_sid'];
                        $m['tax_code_rule2_sid']=$rsResult->fields['tax_code_rule2_sid'];
                        $m['st_address_uid']=$rsResult->fields['st_address_uid'];
                        $m['tax_char']=$rsResult->fields['tax_char'];
                        $m['tax_char2']=$rsResult->fields['tax_char2'];
                        $m['apply_type_to_all_items']=$rsResult->fields['apply_type_to_all_items'];
                        $m['orig_sale_price']=$rsResult->fields['orig_sale_price'];
                        $m['orig_document_number']=$rsResult->fields['orig_document_number'];
                        $m['orig_store_number']=$rsResult->fields['orig_store_number'];
                        $m['orig_subsidiary_number']=$rsResult->fields['orig_subsidiary_number'];
                        $m['shipping_amt']=$rsResult->fields['ship_amt'];

                        $rsResult->Movenext();
                        $z[] = $m;
                    }
                } 

                $k = $valueD;
                $k['items'] = $z;
                $z = "";

                $data =  "[".json_encode($k, JSON_UNESCAPED_SLASHES)."]";
                
                print_r($data);
                
                $curl = curl_init();
                $var = "";
                curl_setopt_array($curl, array(
                 CURLOPT_URL => "https://".$_SERVER['SERVER_NAME']."/v1/rest/document",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
                  CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $data,
                  CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "content-type: application/json; charset=UTF-8",
                    "Auth-Session: ".$_GET['auth']
                  ),

                ));

                $err = curl_error($curl);
                $header = curl_getinfo($curl);
                $response = curl_exec($curl);
                
                if ($err) {
                  echo $response = "cURL Error #:" . $err;
                } else {
                  echo $response;
                }
//                echo "<br/>";

                curl_close($curl);

                $decodeResp = json_decode($response, true);
                
//                echo "<pre>";
//                print_r($decodeResp);
//                echo "</pre>";
//                $sql = "UPDATE document SET status = 4 WHERE sid = '".$decodeResp[0]['sid']."'";
//                $conn->Execute($sql);
//                $conn->debug = true;
                $y['total_deposit_taken'] = '0';
                $y['status'] = '4';
    //            
                $dataA =  "[".json_encode($y, JSON_UNESCAPED_SLASHES)."]";
                $curlA = curl_init();

                curl_setopt_array($curlA, array(
                    CURLOPT_URL => "https://".$_SERVER['SERVER_NAME']."/v1/rest/document/".$decodeResp[0]['sid']."?filter=row_version,eq,".$decodeResp[0]['row_version'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
                    CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_POSTFIELDS => $dataA,
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "content-type: application/json; charset=UTF-8",
                        "Auth-Session: ".$_GET['auth']
                  ),

                ));

                $err = curl_error($curlA);
                $header = curl_getinfo($curlA);
                $res = curl_exec($curlA);

                curl_close($curlA);

                if ($err) {
                  echo $res = "cURL Error #:" . $err;
                } else {
                  echo $res;
                }
                echo $res;
                $data = "";
                $k = "";
            }
        }
    }
    
    
?>
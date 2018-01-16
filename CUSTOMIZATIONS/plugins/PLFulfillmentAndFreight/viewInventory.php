<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
    if($_REQUEST['type'] == 1){
        header( 'Content-Type: application/json' );
        print json_encode(populateInventory($_REQUEST,$conn));
    } 
    
    function populateInventory($data = null, &$conn){
       if (TRUE == $conn){
            $sql = "select 
                        a.sid header,
                        b.sid detail, 
                        b.invn_sbs_item_sid item,
                        b.description1,
                        b.description2,
                        b.description3,
                        b.description4,
                        b.alu,
                        c.store_code store,
                        b.item_pos pos,
                        b.qty,
                        CASE WHEN d.qty IS NULL THEN 0 ELSE d.qty END AS onhand
                        from
                        document a
                        left join document_item b 
                        on (a.sid = b.doc_sid)

                        left join store c 
                        on (c.udf1_string = '".$data['sto']."')

                        left join invn_sbs_item_qty d 
                        on (b.invn_sbs_item_sid = d.invn_sbs_item_sid 
                        and c.sbs_sid = d.sbs_sid
                        and c.sid = d.store_sid)

                        where a.sid = '".$data['sid']."' 
                        and b.note8 = 'PICKUP'
                        and b.note10 = 'DIFFERENT STORE'
                        and c.udf1_string = '".$data['sto']."'";
     //        $conn->debug = TRUE;
             $arr = array();
             $rsResult = $conn->Execute($sql);
             while(!$rsResult->EOF){
                $arr['pos']          = $rsResult->fields['pos'];
                $arr['alu']          = $rsResult->fields['alu'];
                $arr['description1'] = $rsResult->fields['description1'];
                $arr['description2'] = $rsResult->fields['description2'];
                $arr['onhand']       = number_format($rsResult->fields['onhand'],0);
                $arr['qty']          = number_format($rsResult->fields['qty'],0);
                $rsResult->Movenext();

                $a[] = $arr;

             }
         } else {

                $sql = "select 
                        a.sid header,
                        b.sid detail, 
                        b.invn_sbs_item_sid item,
                        b.description1,
                        b.description2,
                        b.description3,
                        b.description4,
                        b.alu,
                        c.store_code store,
                        b.item_pos pos,
                        b.qty,
                        CASE WHEN d.qty IS NULL THEN 0 ELSE d.qty END AS onhand
                        from
                        rps.document a
                        left join rps.document_item b 
                        on (a.sid = b.doc_sid)

                        left join rps.store c 
                        on (c.udf1_string = '".$data['sto']."')

                        left join rps.invn_sbs_item_qty d 
                        on (b.invn_sbs_item_sid = d.invn_sbs_item_sid 
                        and c.sbs_sid = d.sbs_sid
                        and c.sid = d.store_sid)

                        where a.sid = '".$data['sid']."' 
                        and c.udf1_string = '".$data['sto']."'";
     //        $conn->debug = TRUE;
             $arr = array();
             $rsResult = $conn->Execute($sql);
             while(!$rsResult->EOF){
                $arr['pos']          = $rsResult->fields['pos'];
                $arr['alu']          = $rsResult->fields['alu'];
                $arr['description1'] = $rsResult->fields['description1'];
                $arr['description2'] = $rsResult->fields['description2'];
                $arr['onhand']       = number_format($rsResult->fields['onhand'],0);
                $arr['qty']          = number_format($rsResult->fields['qty'],0);
                $rsResult->Movenext();

                $a[] = $arr;

            }
        }

        /*------------------------------------------------------------------------*/
        return $a;
    }
    
    
?>
<?php
    error_reporting( E_ALL & ~E_NOTICE );
    session_start();
    require_once('../../config/gticonfig.php');
    
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
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['note8']            = $rsResult->fields['note8']; 
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['note10']           = $rsResult->fields['note10']; 
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['alu']          = $rsResult->fields['alu'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['description1'] = $rsResult->fields['description1'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['description2'] = $rsResult->fields['description2'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['qty']          = number_format($rsResult->fields['qty'],0);
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['sid']          = $rsResult->fields['sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['doc_sid']      = $rsResult->fields['doc_sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $rsResult->Movenext();
            $ctr++;
    //       $arr;

        }
        unset($sql);
        unset($rsResult);
        
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
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['note8']            = $rsResult->fields['note8']; 
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['note10']           = $rsResult->fields['note10']; 
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['alu']          = $rsResult->fields['alu'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['description1'] = $rsResult->fields['description1'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['description2'] = $rsResult->fields['description2'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['qty']          = number_format($rsResult->fields['qty'],0);
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['sid']          = $rsResult->fields['sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['doc_sid']      = $rsResult->fields['doc_sid'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $arr[$rsResult->fields['note8']][$rsResult->fields['note10']]['items'][$ctr]['row_version']  = $rsResult->fields['row_version'];
            $rsResult->Movenext();
            $ctr++;
    //       $arr;

        }
    }
    
    $a=0;
    $ary = array();
    foreach($arr as $key => $value){
        $b=0;
        
        foreach($value as $keyA => $valueA){
            
            $ary[$a]['note8']  = $valueA['note8'];
            $ary[$a]['note10'] = $valueA['note10'];
            
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
                
                if($str_ == $b){
                    $d = $d + 1;
                } else {
                    $d = 0;
                }
                
                $str_ = $b;
                
            }
            $a++;
        }
        
    }
    
    header( 'Content-Type: application/json' );
    echo json_encode($ary);
?>
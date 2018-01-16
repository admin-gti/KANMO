<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
    if($_REQUEST['type'] == 1){
        populateFulfillmentLocation($_REQUEST, $conn);
    } else if($_REQUEST['type'] == 2) {
        NetworkStatus();
        header( 'Content-Type: application/json' );
        print json_encode(NetworkStatus());
    } else if($_REQUEST['type'] == 3){
        header( 'Content-Type: application/json' );
        print json_encode(doPopulateShippingAddress($_REQUEST,$conn));
    } else if($_REQUEST['type'] == 4){
        header( 'Content-Type: application/json' );
        print json_encode(doPopulateRegion($_REQUEST,$conn));
    } else if($_REQUEST['type'] == 5){
        header( 'Content-Type: application/json' );
        print json_encode(doPopulateCity($_REQUEST,$conn));
    } else if($_REQUEST['type'] == 6){
        header( 'Content-Type: application/json' );
        print json_encode(doPopulateDistrict($_REQUEST,$conn));
    } else if($_REQUEST['type'] == 7){ //populateFulfillmentLocationHomeDelivery
        header( 'Content-Type: application/json' );
        print json_encode(populateFulfillmentLocationHomeDelivery($_REQUEST,$conn));
    }
    
    function NetworkStatus(){
        $check_if_online = checkdnsrr('google.com', 'ANY');
	    $check_if_api_connected = checkdnsrr(SYSCONFIG_OMS_URL, 'ANY');
	    $i = 0;
	    if(!$check_if_online AND !$check_if_api_connected):
		    while(!$check_if_online AND !$check_if_api_connected):
		    	if($i == 10):
		    		if($check_if_online AND !$check_if_api_connected):
		    			die(json_encode(array('status_code' => 408, 'message' => 'Unable to retrieve external resources. Your network connection is too slow!')));
		    		elseif(!$check_if_online AND !$check_if_api_connected):
		    			die(json_encode(array('status_code' => 500, 'message' => 'Unable to connect to the internet. Please check your internet connection!')));
		    		else:
		    			echo json_encode(array('status_code' => 200, 'message' => 'SUCCESS'));
		    		endif;
		    	endif;
		        if(!$check_if_online):
		        	$check_if_online = checkdnsrr('google.com', 'ANY');
		        endif;
		        if(!$check_if_api_connected):
		        	$check_if_api_connected = checkdnsrr(SYSCONFIG_MAGENTO_DOMAIN, 'ANY');
		        endif;
		        $i++;
		        usleep(500000);
		    endwhile;
		else:
			echo json_encode(array('status_code' => 200, 'message' => 'SUCCESS'));
	   	endif;
    }
    
    function populateFulfillmentLocation($data = null, &$conn){
       if (TRUE == $conn){
            $sql = "select "
                    . "a.* "
     //               . ", b.sid productID "
                    . ", b.udf1_string productID "
                    . "from "
                    . "document_item a "
                    . "left join document c ON (a.doc_sid = c.sid) "
                    . "left join invn_sbs_item b on (a.invn_sbs_item_sid = b.sid and c.subsidiary_sid = b.sbs_sid) "
                    . "where 1=1 "
                    . "and note10 = 'DIFFERENT STORE' "
                    . "and doc_sid = '{$data['docsid']}' ";
     //        $conn->debug = TRUE;
             $arr = array();
             $rsResult = $conn->Execute($sql);
             while(!$rsResult->EOF){
     //           $arr['ProductId']        = $rsResult->fields['alu'];
                $arr['ProductId']        = $rsResult->fields['productID'];
     //           $arr['VariantProductId'] = $rsResult->fields['alu'];
                $arr['VariantProductId'] = 0;
                $arr['Quantity']         = number_format($rsResult->fields['qty'],0);
                $rsResult->Movenext();

                $a[] = $arr;

             }
         } else {

             $sql = "select "
                    . "a.* "
     //               . ", b.sid productID "
                    . ", b.udf1_string productID "
                    . "from "
                    . "rps.document_item a "
                    . "left join rps.document c ON (a.doc_sid = c.sid) "
                    . "left join rps.invn_sbs_item b on (a.invn_sbs_item_sid = b.sid and c.subsidiary_sid = b.sbs_sid) "
                    . "where 1=1 "
                    . "and note10 = 'DIFFERENT STORE' "
                    . "and doc_sid = '{$data['docsid']}' ";
     //        $conn->debug = TRUE;
             $arr = array();
             $rsResult = $conn->Execute($sql);
             while(!$rsResult->EOF){
     //           $arr['ProductId']        = $rsResult->fields['alu'];
                $arr['ProductId']        = $rsResult->fields['productID'];
     //           $arr['VariantProductId'] = $rsResult->fields['alu'];
                $arr['VariantProductId'] = 0;
                $arr['Quantity']         = number_format($rsResult->fields['qty'],0);
                $rsResult->Movenext();

                $a[] = $arr;

             }
         }

         /*------------------------------------------------------------------------*/
         $cnt = array();
         if(count($arr) > 0){
             $cnt['Latitude'] = "";
             $cnt['Longitude'] = "";
             $cnt['Products'] = $a;
         } else {
             $cnt['Latitude'] = "";
             $cnt['Longitude'] = "";
             $cnt['Products'] = "";
         }

         echo json_encode($cnt,JSON_UNESCAPED_SLASHES); 
    }
    
    function doPopulateShippingAddress($data = null, $conn){
        $sql = "select 
                a.sid,
                a.address_1, 
                a.address_2, 
                a.address_3, 
                a.city,
                b.country_name,
                a.postal_code
                from 
                customer_address a 
                left join country b on (a.country_sid = b.sid)
                left join document c on (a.cust_sid = c.bt_cuid)
                where 1=1
                and c.sid = '".$data['sid']."'
                group by address_1, address_2, address_3, address_4, address_5, address_6, city, country_name, postal_code, state";
//        $conn->debug = true;
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){
            $arr['sid']             = $rsResult->fields['sid'];
            $arr['address_1']       = $rsResult->fields['address_1'];
            $arr['address_2']       = $rsResult->fields['address_2'];
            $arr['address_3']       = $rsResult->fields['address_3'];
            $arr['address_4']       = $rsResult->fields['address_4'];
            $arr['address_5']       = $rsResult->fields['address_5'];
            $arr['address_6']       = $rsResult->fields['address_6'];
            $arr['city']            = $rsResult->fields['city'];
            $arr['company']         = $rsResult->fields['company'];
            $arr['country_name']    = $rsResult->fields['country_name'];
            $arr['postal_code']     = $rsResult->fields['postal_code'];
            $arr['state']           = $rsResult->fields['state'];
            $arr['completeaddess']  = $rsResult->fields['address_1'].' '.$rsResult->fields['address_2'].' '.$rsResult->fields['address_3'].' '.$rsResult->fields['city']. ' '.$rsResult->fields['country_name'].' '.$rsResult->fields['postal_code'];
            
            $rsResult->Movenext();
            $a[] = $arr;
        }
        
        return $a;
    }
    
    function doPopulateRegion($data = null, $conn){
        $sql = "SELECT * FROM ksetup_city.directory_country_region where country_id = 'ID'";
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){
            $arr['region']           = $rsResult->fields['default_name'];
            
            $rsResult->Movenext();
            $a[] = $arr;
        }
        
        return $a;
    }
    
    function doPopulateCity($data = null, $conn){
        $sql = "SELECT "
                . "a.default_name "
                . "FROM "
                . "ksetup_city.directory_country_region_city a "
                . "LEFT JOIN ksetup_city.directory_country_region b ON (a.reg_id = region_id) "
                . "where b.default_name = '{$data['reg']}'";
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){
            $arr['city']           = $rsResult->fields['default_name'];
            
            $rsResult->Movenext();
            $a[] = $arr;
        }
        return $a;
    }
    function doPopulateDistrict($data = null, $conn){
        $sql = "SELECT "
                . "default_name "
                . "FROM "
                . "ksetup_city.directory_country_region_district "
                . "where city_id = '{$data['reg']}'";
        $rsResult = $conn->Execute($sql);
        while(!$rsResult->EOF){
            $arr['district']           = $rsResult->fields['default_name'];
            
            $rsResult->Movenext();
            $a[] = $arr;
        }
        
        return $a;
    }
    
    function populateFulfillmentLocationHomeDelivery($data = null, &$conn){
        
       if (TRUE == $conn){
            $sql = "select "
                    . "a.* "
     //               . ", b.sid productID "
                    . ", b.udf1_string productID "
                    . "from "
                    . "document_item a "
                    . "left join document c ON (a.doc_sid = c.sid) "
                    . "left join invn_sbs_item b on (a.invn_sbs_item_sid = b.sid and c.subsidiary_sid = b.sbs_sid) "
                    . "where 1=1 "
                    . "and note8 = 'HOME DELIVERY' "
                    . "and note10 = 'DIFFERENT STORE' "
                    . "and doc_sid = '{$data['sid']}' ";
//             $conn->debug = TRUE;
             $arr = array();
             $rsResult = $conn->Execute($sql);
             while(!$rsResult->EOF){
     //           $arr['ProductId']        = $rsResult->fields['alu'];
                $arr['ProductId']        = $rsResult->fields['productID'];
     //           $arr['VariantProductId'] = $rsResult->fields['alu'];
                $arr['VariantProductId'] = 0;
                $arr['Quantity']         = number_format($rsResult->fields['qty'],0);
                $rsResult->Movenext();

                $a[] = $arr;

             }
         } else {

             $sql = "select "
                    . "a.* "
     //               . ", b.sid productID "
                    . ", b.udf1_string productID "
                    . "from "
                    . "rps.document_item a "
                    . "left join rps.document c ON (a.doc_sid = c.sid) "
                    . "left join rps.invn_sbs_item b on (a.invn_sbs_item_sid = b.sid and c.subsidiary_sid = b.sbs_sid) "
                    . "where 1=1 "
                    . "and note10 = 'DIFFERENT STORE' "
                    . "and doc_sid = '{$data['docsid']}' ";
     //        $conn->debug = TRUE;
             $arr = array();
             $rsResult = $conn->Execute($sql);
             while(!$rsResult->EOF){
     //           $arr['ProductId']        = $rsResult->fields['alu'];
                $arr['ProductId']        = $rsResult->fields['productID'];
     //           $arr['VariantProductId'] = $rsResult->fields['alu'];
                $arr['VariantProductId'] = 0;
                $arr['Quantity']         = number_format($rsResult->fields['qty'],0);
                $rsResult->Movenext();

                $a[] = $arr;

             }
         }

         /*------------------------------------------------------------------------*/
         $cnt = array();
         if(count($arr) > 0){
             $cnt['Latitude'] = "";
             $cnt['Longitude'] = "";
             $cnt['Products'] = $a;
         } else {
             $cnt['Latitude'] = "";
             $cnt['Longitude'] = "";
             $cnt['Products'] = "";
         }

        $store = json_encode($cnt,JSON_UNESCAPED_SLASHES); 
        
        
        $curl = curl_init();
        $var = "";
        curl_setopt_array($curl, array(
//          vURL+addManifest+merchantID
          CURLOPT_URL => SYSCONFIG_OMS_URL.SYSCONFIG_OMS_FULFILLMENTLOCATION.SYSCONFIG_OMS_MERCHANTID."?oauth_consumer_key=S9SUCAUJ&oauth_nonce=".$data['an']."&oauth_signature=".$data['s']."&oauth_signature_method=HMAC-SHA1&oauth_timestamp=".$data['at']."&oauth_version=1.0",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "MerchantId=".SYSCONFIG_OMS_MERCHANTID."&InputFormat=application/json&InputData=".$store,
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
        
        return json_decode($response,TRUE);
        
        
    }
    
    
?>
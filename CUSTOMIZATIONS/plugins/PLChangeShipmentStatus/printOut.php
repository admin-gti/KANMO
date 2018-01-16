<?php
    error_reporting( E_ALL & ~E_NOTICE );
    
    require_once('../../config/adodb5/adodb.inc.php');
    session_start();
    require_once('../../config/gticonfig.php');
    
    $curl = curl_init();
    
    unset($_SESSION['kanmo_acctoken']);
    unset($_SESSION['kanmo_expires']);
    
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

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $resp = json_decode($response);
    }
    $_SESSION['kanmo_acctoken'] = $resp->access_token;
    $_SESSION['kanmo_expires'] = strtotime($resp->{".expires"});


    
    
    
//    curl_setopt_array($curl, array(
//      CURLOPT_URL => SYSCONFIG_KANMO_URL.SYSCONFIG_KANMO_API_SHIPPINGLABEL.$_REQUEST['oid'],
////      CURLOPT_HEADER => 0,
//      CURLOPT_RETURNTRANSFER => true,
//      CURLOPT_BINARYTRANSFER => true,
//      CURLOPT_ENCODING => "",
//      CURLOPT_MAXREDIRS => 10,
//      CURLOPT_SSL_VERIFYHOST => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
//      CURLOPT_SSL_VERIFYPEER => 0, //NEED TO REMOVE THIS AFTER DEPLOYMENT OF LIVE
////      CURLOPT_CONNECTTIMEOUT => 3000,
//      CURLOPT_TIMEOUT => 30,
//      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//      CURLOPT_CUSTOMREQUEST => "GET",
//      CURLOPT_HTTPHEADER => array(
//        "authorization: Bearer ".$_SESSION['kanmo_acctoken'],
//        "cache-control: no-cache"
//      ),
//    ));
//
//    $responses = curl_exec($curl);
//    $errs = curl_error($curl);
//    
//    curl_close($curl);
//
//    if ($errs) {
//        $ary['error'] = 'Connection Timeout';
//    } else {
//        $shiprate = json_decode($responses,TRUE);
//    }
    
//    header('Content-type: image/jpg');
//    header('Content-Length: ' . filesize($responses));

//    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => SYSCONFIG_KANMO_URL.SYSCONFIG_KANMO_API_SHIPPINGLABEL.$_REQUEST['oid']."&ShipmentType="+$_REQUEST['stype'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "authorization: Bearer ".$_SESSION['kanmo_acctoken'],
        "cache-control: no-cache",
        "postman-token: c81a4952-aceb-a797-3e92-f3c80819a469"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo base64_encode($response);
    }
//    echo base64_encode($responses);
?>
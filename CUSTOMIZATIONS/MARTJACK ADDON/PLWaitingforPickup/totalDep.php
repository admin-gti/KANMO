<?php
    session_start();
    require_once('../../config/gticonfig.php');
    
    $sql = "UPDATE document SET total_deposit_taken = so_deposit_amt_paid, transaction_total_amt = order_total_amt WHERE sid = '".$_REQUEST['sid']."'";
    $conn->Execute($sql);
    
?>
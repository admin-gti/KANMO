<?php
	
	require_once('../../config/gticonfig.php');

	$temp_name = tempnam(SYSCONFIG_CUSTOMERDISPLAY_FILE_PATH, 'CRDY');
	if($_SERVER['REQUEST_METHOD'] == 'POST'):
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);
		$item 		= $request->item;
		$fee 		= $request->fee;
		$shipping 	= $request->shipping;
		$discount 	= $request->discount;
		$total 		= $request->total;
		$tender 	= $request->tender;

		$file 		= fopen($temp_name, 'w');
		if(count($item) > 0):
			foreach($item as $a):
				fwrite($file, $a->item_sid.'|'.$a->item_desc.'|'.$a->item_qty.'|'.$a->item_price.'|'.$a->item_ext_price.'|'.$a->item_alu.'|'.$a->record_flag."\r\n");
			endforeach;
		endif;

		if(!empty($fee->fee_name) AND $fee->fee_name != '' AND !empty($fee->fee_amount) AND $fee->fee_amount != '' AND $fee->fee_amount != 0):
			fwrite($file, '|'.$fee->fee_name.'|1|'.$fee->fee_amount.'|'.$fee->fee_amount.'||'.$fee->record_flag."\r\n");
		endif;

		if(!empty($shipping->shipping_method) AND $shipping->shipping_method != '' AND !empty($shipping->shipping_amt) AND $shipping->shipping_amt != '' AND $shipping->shipping_amt != 0):
			fwrite($file, '|'.$shipping->shipping_method.'|1|'.$shipping->shipping_amt.'|'.$shipping->shipping_amt.'||'.$shipping->record_flag."\r\n");
		endif;

		if($discount->disc_amt != 0):
			fwrite($file, '|GLOBAL DISCOUNT|1|'.$discount->disc_amt.'|'.$discount->disc_amt.'||'.$discount->record_flag."\r\n");
		endif;

		fwrite($file, '|TOTAL|1|'.$total->total_amt.'|'.$total->total_amt.'||'.$total->record_flag."\r\n");

		if($tender->taken_amt != 0 OR $tender->given_amt != 0 AND $tender->tender_name != '' AND !empty($tender->tender_name)):
			fwrite($file, '|'.$tender->tender_name.'|1|'.$tender->taken_amt.'|'.$tender->given_amt.'||'.$tender->record_flag."\r\n");
		endif;
		fclose($file);
		echo json_encode(array('status' => 200, 'statusText'=> 'OK'));
	else:
		echo json_encode(array('status' => 400, 'statusText'=> 'Bad Request'));
	endif;
	shell_exec(getcwd().'\CryptoFile.exe '.$temp_name);

	do {
	    unlink($temp_name);
	} while (file_exists($temp_name));

?>
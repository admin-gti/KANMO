<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST'):
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);
		date_default_timezone_set("Asia/Manila");

		if($request->action == 'retrieveCustomerData'):
			echo retrieveCustomerData();
		elseif($request->action == 'generateXMLRegularLoyalty'):
			echo generateXMLRegularLoyalty();
		elseif($request->action == 'generateXMLRegularNonLoyalty'):
			echo generateXMLRegularNonLoyalty();
		elseif($request->action == 'generateXMLReturnLoyalty'):
			echo generateXMLReturnLoyalty();
		elseif($request->action == 'generateXMLReturnNonLoyalty'):
			echo generateXMLReturnNonLoyalty();
		elseif($request->action == 'generateXMLReturnExchangeLoyalty'):
			echo generateXMLReturnExchangeLoyalty();
		elseif($request->action == 'generateXMLReturnExchangeNonLoyalty'):
			echo generateXMLReturnExchangeNonLoyalty();
		endif;
	endif;

	function deleteCustomerFiles()
	{
		if(file_exists(GetEnv("SystemDrive").'\\capillary\customer_register.txt')):
			do {
				unlink(GetEnv("SystemDrive").'\\capillary\customer_register.txt');
			} while ( file_exists(GetEnv("SystemDrive").'\\capillary\customer_register.txt'));
		endif;

		if(file_exists(GetEnv("SystemDrive").'\\capillary\customer_update.txt')):
			do {
				unlink(GetEnv("SystemDrive").'\\capillary\customer_update.txt');
			} while ( file_exists(GetEnv("SystemDrive").'\\capillary\customer_update.txt'));
		endif;

		if(file_exists(GetEnv("SystemDrive").'\\capillary\customer_search.txt')):
			do {
				unlink(GetEnv("SystemDrive").'\\capillary\customer_search.txt');
			} while ( file_exists(GetEnv("SystemDrive").'\\capillary\customer_search.txt'));
		endif;
	}

	function retrieveCustomerData()
	{
		$customer_register	= GetEnv("SystemDrive").'\\capillary\customer_register.txt';
		$customer_search 	= GetEnv("SystemDrive").'\\capillary\customer_search.txt';
		$customer_update 	= GetEnv("SystemDrive").'\\capillary\customer_update.txt';
		// if(file_exists($customer_register)):
		// 	$result = json_encode(array('status' => 200, 'statusText' => 'OK', 'recordType' => 1, 'result' => json_encode(simplexml_load_string(file_get_contents($customer_register)), JSON_FORCE_OBJECT)));
		// 	deleteCustomerFiles();
		// 	return $result;

		if(file_exists($customer_update)):
			
			$result = json_encode(array('status' => 200, 'statusText' => 'OK', 'recordType' => 0, 'result' => json_encode(simplexml_load_string(file_get_contents($customer_update)), JSON_FORCE_OBJECT)));
			deleteCustomerFiles();
			
			return $result;
		elseif(file_exists($customer_search)):
			
			$result = json_encode(array('status' => 200, 'statusText' => 'OK', 'recordType' => 1, 'result' => json_encode(simplexml_load_string(file_get_contents($customer_search)), JSON_FORCE_OBJECT)));
			deleteCustomerFiles();
			
			return $result;
		else:
			
			return json_encode(array('status' => 404, 'statusText' => 'Unable to load customer data'));
		
		endif;

	}

	// XML File Creation For Regular Loyalty
	function generateXMLRegularLoyalty()
	{
		// Set the content type to be XML, so that the browser will recognise it as XML.
		header( "content-type: application/xml; charset=ISO-8859-15" );

		// "Create" the document.
		$xml = new DOMDocument('1.0', 'UTF-8');
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);

		$documentData 	= NULL;

		foreach($request->transData as $value):

			if(array_key_exists('documents', $value)):

				$documentData = $value->documents;

			endif;

		endforeach;
		
		$number = (!empty($documentData->order_document_number)) ? $documentData->order_document_number : $documentData->document_number;

		// Create some elements.

		$xml_root 			= $xml->createElement("root");
		$xml_transaction 	= $xml->createElement("transaction");
		$xml_bill_client_id = $xml->createElement("bill_client_id", '');
		$xml_type 			= $xml->createElement("type", 'regular');
		$xml_number 		= $xml->createElement("number", $number);
		$xml_amount 		= $xml->createElement("amount", $documentData->transaction_total_amt);
		
		$xml_billing_time 	= $xml->createElement("billing_time", date('Y-m-d H:i:s'));
		$xml_gross_amount 	= $xml->createElement("gross_amount", $documentData->transaction_subtotal);
		$xml_discount 		= $xml->createElement("discount", $documentData->total_discount_amt);
		$xml_customer 		= $xml->createElement("customer");

		$xml_mobile 		= $xml->createElement("mobile", $documentData->bt_primary_phone_no);
		$xml_email 			= $xml->createElement("email", $documentData->bt_email);
		$xml_external_id 	= $xml->createElement("external_id", $documentData->bt_udf5);
		$xml_firstname 		= $xml->createElement("firstname", $documentData->bt_first_name);
		$xml_lastname 		= $xml->createElement("lastname", $documentData->bt_last_name);

		$xml_customer->appendChild($xml_mobile);
		$xml_customer->appendChild($xml_email);
		$xml_customer->appendChild($xml_external_id);
		$xml_customer->appendChild($xml_firstname);
		$xml_customer->appendChild($xml_lastname);

		$xml_payment_details = $xml->createElement("payment_details");

		$xml_custom_fields 	= $xml->createElement('custom_fields');
		$xml_field_custom 	= $xml->createElement('field');
		$xml_field_name 	= $xml->createElement('name');
		$xml_field_value 	= $xml->createElement('value');

		$xml_field_custom->appendChild($xml_field_name);
		$xml_field_custom->appendChild($xml_field_value);
		$xml_custom_fields->appendChild($xml_field_custom);

		$xml_associate_details	= $xml->createElement("associate_details");
		$xml_associate_code 	= $xml->createElement("code", $documentData->cashier_sid);
		$xml_associate_name 	= $xml->createElement("name", $documentData->cashier_full_name);
		$xml_associate_details->appendChild($xml_associate_code);
		$xml_associate_details->appendChild($xml_associate_name);

		$xml_line_items = $xml->createElement("line_items");

		foreach($request->transData as $value):

			if(array_key_exists('tenders', $value)):

				$tendersData = $value->tenders;

				if(count($tendersData) > 0):

					$xml_payment 		= $xml->createElement("payment");
					$xml_payment_mode 	= $xml->createElement("mode", $tendersData->tender_name);
					$xml_payment_value 	= $xml->createElement("value", $tendersData->amount);

					$xml_attributes 	= $xml->createElement('attributes');
					$xml_attribute 		= $xml->createElement('attribute');
					$xml_attribute_name = $xml->createElement('name');
					$xml_attribute_value = $xml->createElement('value');

					$xml_attribute->appendChild($xml_attribute_name);
					$xml_attribute->appendChild($xml_attribute_value);
					$xml_attributes->appendChild($xml_attribute);

					$xml_payment->appendChild($xml_payment_mode);
					$xml_payment->appendChild($xml_payment_value);
					$xml_payment->appendChild($xml_attributes);
					$xml_payment_details->appendChild($xml_payment);

				endif;

			elseif(array_key_exists('discounts', $value)):

				$discountsData = $value->discounts;

			elseif(array_key_exists('items', $value)):

				$itemsData = $value->items;

				if(count($itemsData) > 0):
					$xml_line_item 				= $xml->createElement("line_item");
					$xml_extended_fields 		= $xml->createElement("extended_fields");
					$xml_field_extended 		= $xml->createElement("field");
					$xml_field_name_extended	= $xml->createElement("name", 'ItemSize');
					$xml_field_value_extended	= $xml->createElement("value", $itemsData->item_size);
					$xml_field_extended->appendChild($xml_field_name_extended);
					$xml_field_extended->appendChild($xml_field_value_extended);
					$xml_extended_fields->appendChild($xml_field_extended);

					$xml_item_serial 		= $xml->createElement("serial", $itemsData->item_pos);
					$xml_item_amount 		= $xml->createElement("amount", ($itemsData->original_price * $itemsData->quantity) - $itemsData->total_discount_amount);
					$xml_item_description 	= $xml->createElement("description", $itemsData->item_description1.' '.$itemsData->item_description2.' '.$itemsData->item_description3.' '.$itemsData->item_description4);
					$xml_item_code 			= $xml->createElement("item_code", $itemsData->alu);
					$xml_item_qty 			= $xml->createElement("qty", $itemsData->quantity);
					$xml_item_rate 			= $xml->createElement("rate", $itemsData->original_price);
					$xml_item_value 		= $xml->createElement("value", $itemsData->original_price * $itemsData->quantity);
					$xml_item_discount 		= $xml->createElement("discount", $itemsData->total_discount_amount);

					$xml_item_attributes 		= $xml->createElement("attributes");
					$xml_item_attribute 		= $xml->createElement("attribute");
					$xml_item_attribute_name 	= $xml->createElement("name", 'Brand');
					$xml_item_attribute_value	= $xml->createElement("value", $itemsData->vendor_code);
					$xml_item_attribute->appendChild($xml_item_attribute_name);
					$xml_item_attribute->appendChild($xml_item_attribute_value);
					$xml_item_attributes->appendChild($xml_item_attribute);

					$xml_line_item->appendChild($xml_extended_fields);
					$xml_line_item->appendChild($xml_item_serial);
					$xml_line_item->appendChild($xml_item_amount);
					$xml_line_item->appendChild($xml_item_description);
					$xml_line_item->appendChild($xml_item_code);
					$xml_line_item->appendChild($xml_item_qty);
					$xml_line_item->appendChild($xml_item_rate);
					$xml_line_item->appendChild($xml_item_value);
					$xml_line_item->appendChild($xml_item_discount);
					$xml_line_item->appendChild($xml_item_attributes);
					$xml_line_items->appendChild($xml_line_item);

				endif;

			endif;

		endforeach;

		$xml_notes 			= $xml->createElement("notes", $documentData->total_line_item.' line items'); 

		$xml->appendChild($xml_root);
		$xml_root->appendChild($xml_transaction);
		$xml_transaction->appendChild($xml_bill_client_id);
		$xml_transaction->appendChild($xml_type);
		$xml_transaction->appendChild($xml_number);
		$xml_transaction->appendChild($xml_amount);
		$xml_transaction->appendChild($xml_notes);
		$xml_transaction->appendChild($xml_billing_time);
		$xml_transaction->appendChild($xml_gross_amount);
		$xml_transaction->appendChild($xml_discount);
		$xml_transaction->appendChild($xml_customer);
		$xml_transaction->appendChild($xml_payment_details);
		$xml_transaction->appendChild($xml_custom_fields);
		$xml_transaction->appendChild($xml_line_items);
		$xml_transaction->appendChild($xml_associate_details);

		
		if(!is_dir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload')):
			
			mkdir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload', 0777, TRUE);
		
		endif;

		// Save the XML.
		//trans_noYYYYMMDDHHmmss.xml
		// $xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'-RegularLoyalty.xml');
		$xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'.xml');

	}

/*======================= XML File Creation for Regular Non Loyalty =======================*/
	function generateXMLRegularNonLoyalty()
	{
		// Set the content type to be XML, so that the browser will recognise it as XML.
		header( "content-type: application/xml; charset=ISO-8859-15" );

		// "Create" the document.
		$xml = new DOMDocument('1.0', 'UTF-8');
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);

		$documentData 	= NULL;

		foreach($request->transData as $value):

			if(array_key_exists('documents', $value)):

				$documentData = $value->documents;

			endif;

		endforeach;

		$number = (!empty($documentData->order_document_number)) ? $documentData->order_document_number : $documentData->document_number;

		$xml_root 			= $xml->createElement("root");
		$xml_transaction 	= $xml->createElement("transaction");
		$xml_bill_client_id = $xml->createElement("bill_client_id", '');
		$xml_type 			= $xml->createElement("type", 'not_interested');
		$xml_number 		= $xml->createElement("number", $number);
		$xml_amount 		= $xml->createElement("amount", $documentData->transaction_total_amt);
		 
		$xml_billing_time 	= $xml->createElement("billing_time", date('Y-m-d H:i:s'));
		$xml_gross_amount 	= $xml->createElement("gross_amount", $documentData->transaction_subtotal);
		$xml_discount 		= $xml->createElement("discount", $documentData->total_discount_amt);
		$xml_not_interested_reason 		= $xml->createElement("not_interested_reason", '');

		$xml_payment_details = $xml->createElement("payment_details");

		$xml_custom_fields 	= $xml->createElement('custom_fields');
		$xml_field_custom 	= $xml->createElement('field');
		$xml_field_name 	= $xml->createElement('name');
		$xml_field_value 	= $xml->createElement('value');

		$xml_field_custom->appendChild($xml_field_name);
		$xml_field_custom->appendChild($xml_field_value);
		$xml_custom_fields->appendChild($xml_field_custom);

		$xml_associate_details	= $xml->createElement("associate_details");
		$xml_associate_code 	= $xml->createElement("code", $documentData->cashier_sid);
		$xml_associate_name 	= $xml->createElement("name", $documentData->cashier_full_name);
		$xml_associate_details->appendChild($xml_associate_code);
		$xml_associate_details->appendChild($xml_associate_name);

		$xml_line_items = $xml->createElement("line_items");

		foreach($request->transData as $value):

			if(array_key_exists('tenders', $value)):

				$tendersData = $value->tenders;

				if(count($tendersData) > 0):

					$xml_payment 		= $xml->createElement("payment");
					$xml_payment_mode 	= $xml->createElement("mode", $tendersData->tender_name);
					$xml_payment_value 	= $xml->createElement("value", $tendersData->amount);

					$xml_attributes 	= $xml->createElement('attributes');
					$xml_attribute 		= $xml->createElement('attribute');
					$xml_attribute_name = $xml->createElement('name');
					$xml_attribute_value = $xml->createElement('value');

					$xml_attribute->appendChild($xml_attribute_name);
					$xml_attribute->appendChild($xml_attribute_value);
					$xml_attributes->appendChild($xml_attribute);

					$xml_payment->appendChild($xml_payment_mode);
					$xml_payment->appendChild($xml_payment_value);
					$xml_payment->appendChild($xml_attributes);
					$xml_payment_details->appendChild($xml_payment);

				endif;

			elseif(array_key_exists('discounts', $value)):

				$discountsData = $value->discounts;

			elseif(array_key_exists('items', $value)):

				$itemsData = $value->items;

				if(count($itemsData) > 0):

					$xml_line_item 				= $xml->createElement("line_item");
					$xml_extended_fields 		= $xml->createElement("extended_fields");
					$xml_field_extended 		= $xml->createElement("field");
					$xml_field_name_extended	= $xml->createElement("name", 'ItemSize');
					$xml_field_value_extended	= $xml->createElement("value", $itemsData->item_size);
					$xml_field_extended->appendChild($xml_field_name_extended);
					$xml_field_extended->appendChild($xml_field_value_extended);
					$xml_extended_fields->appendChild($xml_field_extended);

					$xml_item_serial 		= $xml->createElement("serial", $itemsData->item_pos);
					$xml_item_amount 		= $xml->createElement("amount", ($itemsData->original_price * $itemsData->quantity) - $itemsData->total_discount_amount);
					$xml_item_description 	= $xml->createElement("description", $itemsData->item_description1.' '.$itemsData->item_description2.' '.$itemsData->item_description3.' '.$itemsData->item_description4);
					$xml_item_code 			= $xml->createElement("item_code", $itemsData->alu);
					$xml_item_qty 			= $xml->createElement("qty", $itemsData->quantity);
					$xml_item_rate 			= $xml->createElement("rate", $itemsData->original_price);
					$xml_item_value 		= $xml->createElement("value", $itemsData->original_price * $itemsData->quantity);
					$xml_item_discount 		= $xml->createElement("discount", $itemsData->total_discount_amount);

					$xml_item_attributes 		= $xml->createElement("attributes");
					$xml_item_attribute 		= $xml->createElement("attribute");
					$xml_item_attribute_name 	= $xml->createElement("name", 'Brand');
					$xml_item_attribute_value	= $xml->createElement("value", $itemsData->vendor_code);
					$xml_item_attribute->appendChild($xml_item_attribute_name);
					$xml_item_attribute->appendChild($xml_item_attribute_value);
					$xml_item_attributes->appendChild($xml_item_attribute);

					$xml_line_item->appendChild($xml_extended_fields);
					$xml_line_item->appendChild($xml_item_serial);
					$xml_line_item->appendChild($xml_item_amount);
					$xml_line_item->appendChild($xml_item_description);
					$xml_line_item->appendChild($xml_item_code);
					$xml_line_item->appendChild($xml_item_qty);
					$xml_line_item->appendChild($xml_item_rate);
					$xml_line_item->appendChild($xml_item_value);
					$xml_line_item->appendChild($xml_item_discount);
					$xml_line_item->appendChild($xml_item_attributes);
					$xml_line_items->appendChild($xml_line_item);

				endif;

			endif;

		endforeach;

		$xml_notes 			= $xml->createElement("notes", $documentData->total_line_item.' line items');

		$xml->appendChild($xml_root);
		$xml_root->appendChild($xml_transaction);
		$xml_transaction->appendChild($xml_bill_client_id);
		$xml_transaction->appendChild($xml_type);
		$xml_transaction->appendChild($xml_number);
		$xml_transaction->appendChild($xml_amount);
		$xml_transaction->appendChild($xml_notes);
		$xml_transaction->appendChild($xml_billing_time);
		$xml_transaction->appendChild($xml_gross_amount);
		$xml_transaction->appendChild($xml_discount);
		$xml_transaction->appendChild($xml_not_interested_reason);
		$xml_transaction->appendChild($xml_payment_details);
		$xml_transaction->appendChild($xml_custom_fields);
		$xml_transaction->appendChild($xml_line_items);
		$xml_transaction->appendChild($xml_associate_details);

		
		if(!is_dir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload')):
			
			mkdir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload', 0777, TRUE);
		
		endif;

		// Save the XML.
		//trans_noYYYYMMDDHHmmss.xml
		// $xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'-RegularNonLoyalty.xml');
		$xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'.xml');

	}


/*======================== XML Creation For Return Transaction Loyalty ==============================*/
	function generateXMLReturnLoyalty()
	{
		// Set the content type to be XML, so that the browser will recognise it as XML.
		header( "content-type: application/xml; charset=ISO-8859-15" );

		// "Create" the document.
		$xml = new DOMDocument('1.0', 'UTF-8');
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);

		$documentData 	= NULL;

		foreach($request->transData as $value):

			if(array_key_exists('documents', $value)):

				$documentData = $value->documents;

			endif;

		endforeach;

		$number = (!empty($documentData->order_document_number)) ? $documentData->order_document_number : $documentData->document_number;

		$xml_root 			= $xml->createElement("root");
		$xml_transaction 	= $xml->createElement("transaction");
		$xml_type 			= $xml->createElement("type", 'return');
		$xml_return_type 	= $xml->createElement("return_type", 'LINE_ITEM');
		$xml_number 		= $xml->createElement("number", $documentData->ref_sale_doc_no);
		$xml_amount 		= $xml->createElement("amount", $documentData->return_subtotal);
		$xml_notes 			= $xml->createElement("notes", $number);
		$xml_credit_note 	= $xml->createElement("credit_note", $number);
		$xml_billing_time 	= $xml->createElement("billing_time", date('Y-m-d H:i:s'));
		$xml_purchase_time 	= $xml->createElement("purchase_time", ($documentData->ref_sale_created_datetime != '' OR !is_null($documentData->ref_sale_created_datetime)) ? date('Y-m-d H:i:s', strtotime($documentData->ref_sale_created_datetime)) : '');
		$xml_discount 		= $xml->createElement("discount", $documentData->total_discount_amt);
		$xml_customer 		= $xml->createElement("customer");

		$xml_mobile 		= $xml->createElement("mobile", $documentData->bt_primary_phone_no);
		$xml_email 			= $xml->createElement("email", $documentData->bt_email);
		$xml_external_id 	= $xml->createElement("external_id", $documentData->bt_udf5);
		$xml_firstname 		= $xml->createElement("firstname", $documentData->bt_first_name);
		$xml_lastname 		= $xml->createElement("lastname", $documentData->bt_last_name);

		$xml_customer->appendChild($xml_mobile);
		$xml_customer->appendChild($xml_email);
		$xml_customer->appendChild($xml_external_id);
		$xml_customer->appendChild($xml_firstname);
		$xml_customer->appendChild($xml_lastname);

		$xml_associate_details	= $xml->createElement("associate_details");
		$xml_associate_code 	= $xml->createElement("code", $documentData->cashier_sid);
		$xml_associate_name 	= $xml->createElement("name", $documentData->cashier_full_name);
		$xml_associate_details->appendChild($xml_associate_code);
		$xml_associate_details->appendChild($xml_associate_name);

		$xml_line_items = $xml->createElement("line_items");

		foreach($request->transData as $value):

			if(array_key_exists('items', $value)):

				$itemsData = $value->items;

				if(count($itemsData) > 0):

					$xml_line_item 				= $xml->createElement("line_item");

					$xml_item_serial 		= $xml->createElement("serial", $itemsData->item_pos);
					$xml_item_amount 		= $xml->createElement("amount", ($itemsData->original_price * $itemsData->quantity) - $itemsData->total_discount_amount);
					$xml_item_description 	= $xml->createElement("description", $itemsData->item_description1.' '.$itemsData->item_description2.' '.$itemsData->item_description3.' '.$itemsData->item_description4);
					$xml_item_code 			= $xml->createElement("item_code", $itemsData->alu);
					$xml_item_qty 			= $xml->createElement("qty", $itemsData->quantity);
					$xml_item_rate 			= $xml->createElement("rate", $itemsData->original_price);
					$xml_item_value 		= $xml->createElement("value", $itemsData->original_price * $itemsData->quantity);
					$xml_item_discount 		= $xml->createElement("discount", $itemsData->total_discount_amount);

					$xml_line_item->appendChild($xml_item_serial);
					$xml_line_item->appendChild($xml_item_amount);
					$xml_line_item->appendChild($xml_item_description);
					$xml_line_item->appendChild($xml_item_code);
					$xml_line_item->appendChild($xml_item_qty);
					$xml_line_item->appendChild($xml_item_rate);
					$xml_line_item->appendChild($xml_item_value);
					$xml_line_item->appendChild($xml_item_discount);
					$xml_line_items->appendChild($xml_line_item);

				endif;

			endif;

		endforeach;

		$xml->appendChild($xml_root);
		$xml_root->appendChild($xml_transaction);
		$xml_transaction->appendChild($xml_type);
		$xml_transaction->appendChild($xml_return_type);
		$xml_transaction->appendChild($xml_number);
		$xml_transaction->appendChild($xml_amount);
		$xml_transaction->appendChild($xml_notes);
		$xml_transaction->appendChild($xml_credit_note);
		$xml_transaction->appendChild($xml_billing_time);
		$xml_transaction->appendChild($xml_purchase_time);
		$xml_transaction->appendChild($xml_discount);
		$xml_transaction->appendChild($xml_customer);
		$xml_transaction->appendChild($xml_line_items);
		$xml_transaction->appendChild($xml_associate_details);

		
		if(!is_dir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload')):
			
			mkdir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload', 0777, TRUE);
		
		endif;

		// Save the XML.
		//trans_noYYYYMMDDHHmmss.xml
		// $xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'-ReturnLoyalty.xml');
		$xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'.xml');
	}

/*===================== XML Creation For Return Transaction Non Loyalty ==================== */
	function generateXMLReturnNonLoyalty()
	{
		// Set the content type to be XML, so that the browser will recognise it as XML.
		header( "content-type: application/xml; charset=ISO-8859-15" );

		// "Create" the document.
		$xml = new DOMDocument('1.0', 'UTF-8');
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);

		$documentData 	= NULL;

		foreach($request->transData as $value):

			if(array_key_exists('documents', $value)):

				$documentData = $value->documents;

			endif;

		endforeach;

		$number = (!empty($documentData->order_document_number)) ? $documentData->order_document_number : $documentData->document_number;

		$xml_root 			= $xml->createElement("root");
		$xml_transaction 	= $xml->createElement("transaction");
		$xml_type 			= $xml->createElement("type", 'not_interested_return');
		$xml_return_type 	= $xml->createElement("return_type", 'LINE_ITEM');
		$xml_number 		= $xml->createElement("number", $documentData->ref_sale_doc_no);
		$xml_amount 		= $xml->createElement("amount", $documentData->return_subtotal);
		$xml_billing_time 	= $xml->createElement("billing_time", date('Y-m-d H:i:s'));
		$xml_purchase_time 	= $xml->createElement("purchase_time", ($documentData->ref_sale_created_datetime != '' OR !is_null($documentData->ref_sale_created_datetime)) ? date('Y-m-d H:i:s', strtotime($documentData->ref_sale_created_datetime)) : '');
		$xml_discount 		= $xml->createElement("discount", $documentData->total_discount_amt);
		$xml_notes 			= $xml->createElement("notes", $number);

		$xml_associate_details	= $xml->createElement("associate_details");
		$xml_associate_code 	= $xml->createElement("code", $documentData->cashier_sid);
		$xml_associate_name 	= $xml->createElement("name", $documentData->cashier_full_name);
		$xml_associate_details->appendChild($xml_associate_code);
		$xml_associate_details->appendChild($xml_associate_name);

		$xml_line_items = $xml->createElement("line_items");
		

		foreach($request->transData as $value):

			if(array_key_exists('items', $value)):

				$itemsData = $value->items;

				if(count($itemsData) > 0):

					$xml_line_item 			= $xml->createElement("line_item");

					$xml_item_serial 		= $xml->createElement("serial", $itemsData->item_pos);
					$xml_item_amount 		= $xml->createElement("amount", ($itemsData->original_price * $itemsData->quantity) - $itemsData->total_discount_amount);
					$xml_item_description 	= $xml->createElement("description", $itemsData->item_description1.' '.$itemsData->item_description2.' '.$itemsData->item_description3.' '.$itemsData->item_description4);
					$xml_item_code 			= $xml->createElement("item_code", $itemsData->alu);
					$xml_item_qty 			= $xml->createElement("qty", $itemsData->quantity);
					$xml_item_rate 			= $xml->createElement("rate", $itemsData->original_price);
					$xml_item_value 		= $xml->createElement("value", $itemsData->original_price * $itemsData->quantity);
					$xml_item_discount 		= $xml->createElement("discount", $itemsData->total_discount_amount);

					$xml_line_item->appendChild($xml_item_serial);
					$xml_line_item->appendChild($xml_item_amount);
					$xml_line_item->appendChild($xml_item_description);
					$xml_line_item->appendChild($xml_item_code);
					$xml_line_item->appendChild($xml_item_qty);
					$xml_line_item->appendChild($xml_item_rate);
					$xml_line_item->appendChild($xml_item_value);
					$xml_line_item->appendChild($xml_item_discount);
					$xml_line_items->appendChild($xml_line_item);

				endif;

			endif;

		endforeach;

		$xml->appendChild($xml_root);
		$xml_root->appendChild($xml_transaction);
		$xml_transaction->appendChild($xml_type);
		$xml_transaction->appendChild($xml_return_type);
		$xml_transaction->appendChild($xml_number);
		$xml_transaction->appendChild($xml_amount);
		$xml_transaction->appendChild($xml_billing_time);
		$xml_transaction->appendChild($xml_purchase_time);
		$xml_transaction->appendChild($xml_discount);
		$xml_transaction->appendChild($xml_notes);
		$xml_transaction->appendChild($xml_line_items);
		$xml_transaction->appendChild($xml_associate_details);

		if(!is_dir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload')):
			
			mkdir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload', 0777, TRUE);
		
		endif;

		// Save the XML.
		//trans_noYYYYMMDDHHmmss.xml
		// $xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'-ReturnNonLoyalty.xml');
		$xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'.xml');
	}

/*========================== XML Function for Return Exchange Loyalty ======================= */
	function generateXMLReturnExchangeLoyalty()
	{
		// Set the content type to be XML, so that the browser will recognise it as XML.
		header( "content-type: application/xml; charset=ISO-8859-15" );

		// "Create" the document.
		$xml = new DOMDocument('1.0', 'UTF-8');
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);

		$documentData 	= NULL;

		foreach($request->transData as $value):

			if(array_key_exists('documents', $value)):

				$documentData = $value->documents;

			endif;

		endforeach;

		$number = (!empty($documentData->order_document_number)) ? $documentData->order_document_number : $documentData->document_number;

		$xml_root 			= $xml->createElement("root");
		$xml_transaction 	= $xml->createElement("transaction");
		$xml_type 			= $xml->createElement("type", 'regular');
		$xml_number 		= $xml->createElement("number", $number);
		$xml_amount 		= $xml->createElement("amount", $documentData->sale_total_amt);
		$xml_billing_time 	= $xml->createElement("billing_time", date('Y-m-d H:i:s'));
		$xml_gross_amount 	= $xml->createElement("gross_amount", $documentData->return_subtotal);
		$xml_discount 		= $xml->createElement("discount", $documentData->total_discount_amt);
		$xml_customer 		= $xml->createElement("customer");

		$xml_mobile 		= $xml->createElement("mobile", $documentData->bt_primary_phone_no);
		$xml_email 			= $xml->createElement("email", $documentData->bt_email);
		$xml_external_id 	= $xml->createElement("external_id", $documentData->bt_udf5);
		$xml_firstname 		= $xml->createElement("firstname", $documentData->bt_first_name);
		$xml_lastname 		= $xml->createElement("lastname", $documentData->bt_last_name);

		$xml_customer->appendChild($xml_mobile);
		$xml_customer->appendChild($xml_email);
		$xml_customer->appendChild($xml_external_id);
		$xml_customer->appendChild($xml_firstname);
		$xml_customer->appendChild($xml_lastname);

		$xml_payment_details = $xml->createElement("payment_details");

		$xml_credit_note 	= $xml->createElement('credit_note');
		$xml_credit_number 	= $xml->createElement('number', 001);
		$xml_credit_notes 	= $xml->createElement('notes', $documentData->notes_return);
		$xml_credit_amount 	= $xml->createElement('amount', $documentData->given_amt);

		$xml_credit_note->appendChild($xml_credit_number);
		$xml_credit_note->appendChild($xml_credit_notes);
		$xml_credit_note->appendChild($xml_credit_amount);

		$xml_associate_details	= $xml->createElement("associate_details");
		$xml_associate_code 	= $xml->createElement("code", $documentData->cashier_sid);
		$xml_associate_name 	= $xml->createElement("name", $documentData->cashier_full_name);
		$xml_associate_details->appendChild($xml_associate_code);
		$xml_associate_details->appendChild($xml_associate_name);

		$xml_line_items = $xml->createElement("line_items");

		foreach($request->transData as $value):

			if(array_key_exists('tenders', $value)):

				$tendersData = $value->tenders;

				if(count($tendersData) > 0):

					$xml_payment 		= $xml->createElement("payment");
					$xml_payment_mode 	= $xml->createElement("mode", $tendersData->tender_name);
					$xml_payment_value 	= $xml->createElement("value", $tendersData->amount);
					$xml_payment_notes 	= $xml->createElement("notes", '');

					$xml_attributes 	= $xml->createElement('attributes');
					$xml_attribute 		= $xml->createElement('attribute');
					$xml_attribute_name = $xml->createElement('name');
					$xml_attribute_value = $xml->createElement('value');

					$xml_attribute->appendChild($xml_attribute_name);
					$xml_attribute->appendChild($xml_attribute_value);
					$xml_attributes->appendChild($xml_attribute);

					$xml_payment->appendChild($xml_payment_mode);
					$xml_payment->appendChild($xml_payment_value);
					$xml_payment->appendChild($xml_payment_notes);
					$xml_payment->appendChild($xml_attributes);
					$xml_payment_details->appendChild($xml_payment);

				endif;
			elseif(array_key_exists('items', $value)):

				$itemsData = $value->items;

				if(count($itemsData) > 0):

					$xml_line_item 			= $xml->createElement("line_item");
					$xml_item_discount 		= '';

					if($itemsData->item_type == 1):

						$xml_item_type = $xml->createElement('type', 'regular');
						$xml_line_item->appendChild($xml_item_type);
						$xml_item_discount 		= $xml->createElement("discount", $itemsData->total_discount_amount);
					
					elseif($itemsData->item_type == 2):

						$xml_item_type = $xml->createElement('type', 'return');
						$xml_item_return_type = $xml->createElement('return_type', 'LINE_ITEM');
						$xml_line_item->appendChild($xml_item_type);
						$xml_line_item->appendChild($xml_item_return_type);

						$xml_item_discount 		= $xml->createElement("discount_value", $itemsData->total_discount_amount);
						$xml_item_trans_number 	= $xml->createElement("transaction_number", $documentData->ref_sale_doc_no);
						$xml_item_trans_date 	= $xml->createElement("transaction_date", ($documentData->ref_sale_created_datetime != '' OR !is_null($documentData->ref_sale_created_datetime)) ? date('Y-m-d H:i:s', strtotime($documentData->ref_sale_created_datetime)) : '');
						$xml_line_item->appendChild($xml_item_trans_number);
						$xml_line_item->appendChild($xml_item_trans_date);

					endif;

					$xml_item_serial 		= $xml->createElement("serial", $itemsData->item_pos);
					$xml_item_amount 		= $xml->createElement("amount", ($itemsData->original_price * $itemsData->quantity) - $itemsData->total_discount_amount);
					$xml_item_description 	= $xml->createElement("description", $itemsData->item_description1.' '.$itemsData->item_description2.' '.$itemsData->item_description3.' '.$itemsData->item_description4);
					$xml_item_code 			= $xml->createElement("item_code", $itemsData->alu);
					$xml_item_qty 			= $xml->createElement("qty", $itemsData->quantity);
					$xml_item_rate 			= $xml->createElement("rate", $itemsData->original_price);
					$xml_item_value 		= $xml->createElement("value", $itemsData->original_price * $itemsData->quantity);
					$xml_item_notes			= $xml->createElement("notes", $itemsData->return_reason);

					$xml_line_item->appendChild($xml_item_serial);
					$xml_line_item->appendChild($xml_item_amount);
					$xml_line_item->appendChild($xml_item_description);
					$xml_line_item->appendChild($xml_item_code);
					$xml_line_item->appendChild($xml_item_qty);
					$xml_line_item->appendChild($xml_item_rate);
					$xml_line_item->appendChild($xml_item_discount);
					$xml_line_item->appendChild($xml_item_value);
					$xml_line_item->appendChild($xml_item_notes);
					$xml_line_items->appendChild($xml_line_item);

				endif;

			endif;

		endforeach;

		$xml_notes 			= $xml->createElement("notes", $documentData->total_line_item.' line items');
		$xml->appendChild($xml_root);
		$xml_root->appendChild($xml_transaction);
		$xml_transaction->appendChild($xml_type);
		$xml_transaction->appendChild($xml_number);
		$xml_transaction->appendChild($xml_amount);
		$xml_transaction->appendChild($xml_notes);
		$xml_transaction->appendChild($xml_billing_time);
		$xml_transaction->appendChild($xml_gross_amount);
		$xml_transaction->appendChild($xml_discount);
		$xml_transaction->appendChild($xml_customer);
		$xml_transaction->appendChild($xml_payment_details);
		$xml_transaction->appendChild($xml_line_items);
		$xml_transaction->appendChild($xml_associate_details);
		$xml_transaction->appendChild($xml_credit_note);

		
		if(!is_dir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload')):
			
			mkdir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload', 0777, TRUE);
		
		endif;

		// Save the XML.
		//trans_noYYYYMMDDHHmmss.xml
		// $xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'-ExchangeLoyalty.xml');
		$xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'.xml');
	}


/*====================== XML Function for Return Exchange Non Loyalty =======================*/
	function generateXMLReturnExchangeNonLoyalty()
	{
		// Set the content type to be XML, so that the browser will recognise it as XML.
		header( "content-type: application/xml; charset=ISO-8859-15" );

		// "Create" the document.
		$xml = new DOMDocument('1.0', 'UTF-8');
		$postdata 	= file_get_contents("php://input");
		$request 	= json_decode($postdata);

		$documentData 	= NULL;

		foreach($request->transData as $value):

			if(array_key_exists('documents', $value)):

				$documentData = $value->documents;

			endif;

		endforeach;

		$number = (!empty($documentData->order_document_number)) ? $documentData->order_document_number : $documentData->document_number;

		$xml_root 			= $xml->createElement("root");
		$xml_transaction 	= $xml->createElement("transaction");
		$xml_type 			= $xml->createElement("type", 'not_interested');
		$xml_number 		= $xml->createElement("number", $number);
		$xml_amount 		= $xml->createElement("amount", $documentData->sale_total_amt);
		$xml_billing_time 	= $xml->createElement("billing_time", date('Y-m-d H:i:s'));
		$xml_gross_amount 	= $xml->createElement("gross_amount", $documentData->return_subtotal);
		$xml_discount 		= $xml->createElement("discount", $documentData->total_discount_amt);

		$xml_credit_note 	= $xml->createElement('credit_note');
		$xml_credit_number 	= $xml->createElement('number', 001);
		$xml_credit_notes 	= $xml->createElement('notes', $documentData->notes_return);
		$xml_credit_amount 	= $xml->createElement('amount', $documentData->given_amt);

		$xml_credit_note->appendChild($xml_credit_number);
		$xml_credit_note->appendChild($xml_credit_notes);
		$xml_credit_note->appendChild($xml_credit_amount);

		$xml_associate_details	= $xml->createElement("associate_details");
		$xml_associate_code 	= $xml->createElement("code", $documentData->cashier_sid);
		$xml_associate_name 	= $xml->createElement("name", $documentData->cashier_full_name);
		$xml_associate_details->appendChild($xml_associate_code);
		$xml_associate_details->appendChild($xml_associate_name);

		$xml_line_items = $xml->createElement("line_items");

		foreach($request->transData as $value):

			if(array_key_exists('items', $value)):

				$itemsData = $value->items;

				if(count($itemsData) > 0):

					$xml_line_item 			= $xml->createElement("line_item");
					$xml_item_discount 		= '';

					if($itemsData->item_type == 1):

						$xml_item_type = $xml->createElement('type', 'regular');
						$xml_line_item->appendChild($xml_item_type);

					elseif($itemsData->item_type == 2):

						$xml_item_type = $xml->createElement('type', 'not_interested_return');
						$xml_item_return_type = $xml->createElement('return_type', 'LINE_ITEM');
						$xml_line_item->appendChild($xml_item_type);
						$xml_line_item->appendChild($xml_item_return_type);

						$xml_item_trans_number 	= $xml->createElement("transaction_number", $documentData->ref_sale_doc_no);
						$xml_item_purchase_time = $xml->createElement("purchase_time", ($documentData->ref_sale_created_datetime != '' OR !is_null($documentData->ref_sale_created_datetime)) ? date('Y-m-d H:i:s', strtotime($documentData->ref_sale_created_datetime)) : '');
						$xml_line_item->appendChild($xml_item_trans_number);
						$xml_line_item->appendChild($xml_item_purchase_time);

					endif;

					$xml_item_serial 		= $xml->createElement("serial", $itemsData->item_pos);
					$xml_item_amount 		= $xml->createElement("amount", ($itemsData->original_price * $itemsData->quantity) - $itemsData->total_discount_amount);
					$xml_item_description 	= $xml->createElement("description", $itemsData->item_description1.' '.$itemsData->item_description2.' '.$itemsData->item_description3.' '.$itemsData->item_description4);
					$xml_item_code 			= $xml->createElement("item_code", $itemsData->alu);
					$xml_item_qty 			= $xml->createElement("qty", $itemsData->quantity);
					$xml_item_rate 			= $xml->createElement("rate", $itemsData->original_price);
					$xml_item_value 		= $xml->createElement("value", $itemsData->original_price * $itemsData->quantity);
					$xml_item_notes			= $xml->createElement("notes", $itemsData->return_reason);
					$xml_item_discount 		= $xml->createElement("discount", $itemsData->total_discount_amount);

					$xml_line_item->appendChild($xml_item_serial);
					$xml_line_item->appendChild($xml_item_amount);
					$xml_line_item->appendChild($xml_item_description);
					$xml_line_item->appendChild($xml_item_code);
					$xml_line_item->appendChild($xml_item_qty);
					$xml_line_item->appendChild($xml_item_rate);
					$xml_line_item->appendChild($xml_item_discount);
					$xml_line_item->appendChild($xml_item_value);
					$xml_line_item->appendChild($xml_item_notes);
					$xml_line_items->appendChild($xml_line_item);

				endif;

			endif;

		endforeach;

		$xml->appendChild($xml_root);
		$xml_root->appendChild($xml_transaction);
		$xml_transaction->appendChild($xml_type);
		$xml_transaction->appendChild($xml_number);
		$xml_transaction->appendChild($xml_amount);
		$xml_transaction->appendChild($xml_billing_time);
		$xml_transaction->appendChild($xml_gross_amount);
		$xml_transaction->appendChild($xml_discount);
		$xml_transaction->appendChild($xml_line_items);
		$xml_transaction->appendChild($xml_credit_note);
		$xml_transaction->appendChild($xml_associate_details);

		
		if(!is_dir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload')):
			
			mkdir(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload', 0777, TRUE);
		
		endif;
		// Save the XML.
		//trans_noYYYYMMDDHHmmss.xml
		// $xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'-ExchangeNonLoyalty.xml');
		$xml->save(GetEnv("SystemDrive").'\Program Files (x86)\Genie Technologies Inc\CRM\upload\\'.$number.date('YmdHis').'.xml');
	}
?>
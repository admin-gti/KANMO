<?php

require_once('../../config/gticonfig.php');

	if($_SERVER['REQUEST_METHOD'] == 'GET'):
		switch ($_GET['action']):
			case 'getCommittedQty':
				echo getCommittedQty($_GET['alu'], $_GET['store_code'], $conn);
				break;
		endswitch;
	endif;


	function getCommittedQty($alu, $store_code, &$conn){
		if($conn):
			$sql = "SELECT "
                        ."AB.SUBSIDIARY_SID, "
                        ."AB.STORE_SID, "
                        ."AB.INVN_SBS_ITEM_SID, "
                        ."AB.ALU, "
                        ."SUM(IFNULL(AB.QTY, 0)) QTY, "
                        ."AB.LOCATION "
                        ."FROM "                                        
                        ."( "
                        ."SELECT "
                        ."SUBSIDIARY_SID, "
                        ."STORE_SID, "
                        ."INVN_SBS_ITEM_SID, "
                        ."ALU, "
                        ."SUM(QTY) QTY, "
                        ."LOCATION "
                        ."FROM ( "
                        ."SELECT  "
                        ."A.SUBSIDIARY_SID SUBSIDIARY_SID,  "
                        ."A.STORE_SID STORE_SID,  "
                        ."B.INVN_SBS_ITEM_SID INVN_SBS_ITEM_SID,  "
                        ."B.ALU ALU,  "
                        ."(IFNULL(B.QTY, 0) - IFNULL(B.ORDER_QUANTITY_FILLED, 0)) QTY,  "
                        ."C.STORE_CODE AS LOCATION  "
                        ."FROM DOCUMENT A "
                        ."LEFT JOIN DOCUMENT_ITEM B "
                        ."ON A.SID = B.DOC_SID "
                        ."LEFT JOIN STORE C  "
                        ."ON A.SUBSIDIARY_SID = C.SBS_SID AND A.STORE_SID = C.SID  "
                        ."WHERE 1=1 "
                        ."AND A.DOC_NO IS NOT NULL "
                        ."AND (A.SO_CANCEL_FLAG IS NULL OR A.SO_CANCEL_FLAG = 0) "
                        ."AND A.STATUS = 4 "
                        ."AND (A.NOTES_ORDER IS NULL OR UPPER(A.NOTES_ORDER) NOT LIKE '%SOURCE%' OR UPPER(A.NOTES_ORDER) NOT LIKE '%PAYATSTORE%') "
                        ."AND B.INVN_SBS_ITEM_SID IS NOT NULL "
                        ."AND B.ITEM_TYPE = 3 "
                        ."AND (B.NOTE6 IS NULL OR UPPER(B.NOTE6) <> 'PAYATSTORE') "
                        ."AND B.ALU = '". $alu ."' "
                        ."AND C.STORE_CODE = '". $store_code ."' "
                        .") A "
                        ."GROUP BY SUBSIDIARY_SID, STORE_SID, INVN_SBS_ITEM_SID, ALU, LOCATION "
                       	.") AB ";
			$query = $conn->Execute($sql);
			$result_array = array();
			while(!$query->EOF):
				$result_array['SUBSIDIARY_SID'] 	= $query->fields['SUBSIDIARY_SID'];
				$result_array['STORE_SID'] 			= $query->fields['STORE_SID'];
				$result_array['INVN_SBS_ITEM_SID'] 	= $query->fields['INVN_SBS_ITEM_SID'];
				$result_array['INVN_SBS_ITEM_SID'] 	= $query->fields['INVN_SBS_ITEM_SID'];
				$result_array['ALU'] 				= $query->fields['ALU'];
				$result_array['QTY'] 				= number_format($query->fields['QTY']);
				$result_array['LOCATION'] 			= $query->fields['LOCATION'];
				$query->Movenext();
			endwhile;
			return json_encode($result_array);
		else:
			return 'false';
		endif;
	}

?>
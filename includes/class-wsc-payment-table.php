<?php

class Wsc_Payment_Table{
	
	function __construct(){
		
    
	}
  //https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
	//ALTER TABLE wp_wc_simple_commission_payment AUTO_INCREMENT = 00001;
	public static function get_records( /*$per_page, $page_number = 1*/ $start_date, $end_date ){
  		
		/*echo $start_date;
		echo "<br>";
		echo $end_date;*/
		global $wpdb;
	     $tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		//echo "<br>";
		 $tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
		//exit;
		$args = array(
			'date_before' => $end_date,
			'date_after'  => $start_date
 		);
		 $date_before = false;
  		 $date_after  = false;
  		 $date_created = '';
  		 if ( ! empty( $args['date_before'] ) ) {
		    $datetime    = wc_string_to_datetime( $args['date_before'] );
		    $date_before = strpos( $args['date_before'], ':' ) ? $datetime->getOffsetTimestamp() : $datetime->date( 'Y-m-d' );
		  }
		  if ( ! empty( $args['date_after'] ) ) {
		    $datetime   = wc_string_to_datetime( $args['date_after'] );
		    $date_after = strpos( $args['date_after'], ':' ) ? $datetime->getOffsetTimestamp() : $datetime->date( 'Y-m-d' );
		  }

		  if ( $date_before && $date_after ) {
		    $date_created = $date_after . '...' . $date_before;
		  } 
		  //echo '<br>'.$date_created;
	 	
		$final_order = array();

		$query = new WC_Order_Query( array(
				'limit' => -1,
				'status' => 'completed',
				'orderby' => 'date',
				'order' => 'DESC',
				'date_created' => $date_created,
				'return' => 'ids',
			) );
		 $orders    = $query->get_orders();

		 if(empty($orders)){
		 	return $final_order;
		 }
		 
		 // first check order_id exist in table order
		//print_r($orders);
		  $orderIDs = implode(',', $orders);
		$sql  = "SELECT order_id FROM $tableOrders WHERE order_id IN ($orderIDs) ";
		$existOrders = $wpdb->get_results($sql,ARRAY_A);
		$count = $wpdb->num_rows;
		//print($existOrders);exit;
		 if($count > 0 && !empty($existOrders)){
		 	foreach ($existOrders as $key => $value) {
		 		$final_order[] = $value['order_id'];
		 	}
		 	$final_order = array_diff($orders,$final_order);
		 }else{
		 	$final_order = $orders;
		 }
		//print_r($final_order);

		//exit;
		$items_data = array();
		$comm_amount = 0;
		$payment_data = array();
		$order_date = '';
		if(!empty($final_order)){

			 foreach($final_order as $key => $order_id){
				
				$order = wc_get_order( $order_id );
				$items = $order->get_items();
				$order_date = $order->get_date_created()->getOffsetTimestamp();

				if($order->get_status()=='completed'){

					foreach($items as $item){
	 
						/*if($item->get_variation_id()){ 
							$product = new WC_Product_Variation($item->get_variation_id());
						} else{
							$product = new WC_Product($item->get_product_id());
						}*/
						$comm_per = intval(get_post_meta( $item->get_variation_id(), '_commission_rate', true ));
						
						if($comm_per){
							$comm_amount += ( $item->get_total() * $comm_per)/100;
						}
					}	
				}
			}
			// check for any cancelled orders and deduct the amount from total commission
			
			$tableOrdersCancel  = $wpdb->prefix.'wc_simple_commission_orders_cancel';
				
			$sql = "SELECT * FROM $tableOrdersCancel ORDER BY id DESC";
			$cancelledItems = $wpdb->get_results($sql,ARRAY_A);
				
            $CancelAmount = 0;
            if(!empty($cancelledItems)){
            	foreach($cancelledItems as $items){
              		$order_id = $items['order_id'];
              		$order = wc_get_order( $order_id );
					$CancelAmount += $items['amount'];		 	
					$del_sql = "DELETE FROM $tableOrdersCancel WHERE `order_id` = '".$order_id."'";
                    $wpdb->query($del_sql);				
              	}
            }

			$payment_data = array(
				'start_date'  => $start_date,
				'end_date'    => $end_date,
				'total_commission' => $comm_amount - $CancelAmount
			);
			//print_r($payment_data);
			if($wpdb->insert($tablePayment ,$payment_data)){
				 $payment_ID = $wpdb->insert_id;
				//exit;
				$payment_data['payment_id'] = $payment_ID;
				$payment_data['order_date'] = $order_date;
				foreach($final_order as $key => $order_id){
					$order = wc_get_order( $order_id );
					if($order->get_status()=='completed'){
						$orderItemData = array(
							'payment_id' => $payment_ID,
							'order_id'   => $order_id
						);
						$wpdb->insert($tableOrders ,$orderItemData);
					}
				}
			}else{
				echo 'error';exit;
			}
			

		}
		
		return true;
				
		//$result = array_slice( $result, ( ( $page_number-1 )* $per_page ), $per_page );
		
		/*if( ! empty( $_REQUEST['orderby'] ) ){
			$order = ! empty( $_REQUEST['order'] && $_REQUEST['order']=='asc' ) ? 'SORT_ASC':'SORT_DESC';
			//echo $_REQUEST['orderby'];
			//echo $order;
			
			$result = self::array_sort($result , $_REQUEST['orderby'],$order); 
		}
		return $result;*/

	}
	public static function array_sort($array, $on, $order=SORT_ASC){
		// print_r($array);
		$new_array = array();
		$sortable_array = array();

		if(count($array) > 0){
			foreach($array as $k => $v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						if($k2 == $on){
							$sortable_array[$k] = $v2;
						}
					}
				} else{
					$sortable_array[$k] = $v;
				}
			}

			switch($order){
				case 'SORT_ASC':
				asort($sortable_array);
				break;
				case 'SORT_DESC':
				arsort($sortable_array);
				break;
			}
			foreach($sortable_array as $k => $v){
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}
	

	
}

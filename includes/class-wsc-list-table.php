 <?php
if( ! class_exists( 'WP_List_Table' ) ){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Wsc_List_Table extends WP_List_Table{
	
	function __construct(){
		global $status, $page;

		parent::__construct( array(
				'singular'  => __( 'search form', $this->plugin_name ),     //singular name of the listed records
				'plural'    => __( 'search forms', $this->plugin_name ),   //plural name of the listed records
				'ajax'      => false        //does this table support ajax?

			) );
		//print_r($_REQUEST);
		add_action( 'admin_head', array( &$this, 'admin_header' ) ); 
    
    
	}

	function admin_header(){
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'search-pages' != $page )
		return;
		echo '<style type="text/css">';
		echo '.wp-list-table .column-id { width: 5%; }';
		echo '.wp-list-table .column-booktitle { width: 40%; }';
		echo '.wp-list-table .column-author { width: 35%; }';
		echo '.wp-list-table .column-isbn { width: 20%;}';
		echo '</style>';
	}
  
	public static function get_records( $per_page, $page_number = 1 ){
  		
		//echo $per_page;
		// now use $per_page to set the number of items displayed
		global $wpdb;
		$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	 	
		$result = array();

		$query = new WC_Order_Query( array(
				'limit' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
				'return' => 'ids',
			) );
		$orders = $query->get_orders();
		$i=1;
		foreach($orders as $key => $order_id){
			
			$order = wc_get_order( $order_id );
			$items = $order->get_items();
			$order_date = $order->get_date_created()->getOffsetTimestamp();
			foreach($items as $item_id => $item){
 			 //print_r(expression)
				if($item->get_variation_id()){ 
					$product = new WC_Product_Variation($item->get_variation_id());
				} else{
					$product = new WC_Product($item->get_product_id());
				}
				$comm_per = intval(get_post_meta( $item->get_variation_id(), '_commission_rate', true ));
				$comm_amount = 0;
				if($comm_per){
					$comm_amount = ( $item->get_total() * $comm_per)/100;
				}
				 $product_name = (get_post_meta($item->get_product_id(),'en_productname',true))?get_post_meta($item->get_product_id(),'en_productname',true):$item->get_name();    
				$info = array(
					'id' => $order_id,
					'order_id' => $order_id,
					'order_date' => date("Y-m-d" ,$order_date),
					'approved' => ($order->get_status()=='completed')?'Approved':'-', 
					'sku' => $product->get_sku(),
					'product' => $product_name,
					'qty' => $item->get_quantity(),
					'sales_ttl' => get_woocommerce_currency_symbol().' '.$item->get_total(),
					'store_price' => get_woocommerce_currency_symbol().' '.$product->get_price(),
					'discount' =>  get_woocommerce_currency_symbol().' '.($product->get_price() - $item->get_total()),  
					'commission_per' => $comm_per,
					'commission_ttl' => get_woocommerce_currency_symbol().' '.$comm_amount,
					/*'payment_id'  => self::getPaymentId($order_id),*/
				);
				array_push($result, $info);
				$i++;
			}
		}
				
		$result = array_slice( $result, ( ( $page_number-1 )* $per_page ), $per_page );
		
		if( ! empty( $_REQUEST['orderby'] ) ){
			$order = ! empty( $_REQUEST['order'] && $_REQUEST['order']=='asc' ) ? 'SORT_ASC':'SORT_DESC';
			//echo $_REQUEST['orderby'];
			//echo $order;
			
			$result = self::array_sort($result , $_REQUEST['orderby'],$order); 
		}
		return $result;

	}
	public static function getPaymentId($order_id){
		global $wpdb;
		 $tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
		 $sql = "SELECT payment_id FROM $tableOrders WHERE order_id= '".$order_id."' LIMIT 1";
		  $res = $wpdb->get_results($sql,ARRAY_A);
		  //print_r($res);
		  return (!empty($res))? $res[0]['payment_id']:'-';
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
	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count(){
		
		$query = new WC_Order_Query( array(
				'limit' => -1,
				'return' => 'ids',
			) );
		$orders = $query->get_orders();
		$items = 0;
		foreach($orders as $key => $order_id){
		
			$order = wc_get_order( $order_id );
			$items += count($order->get_items());
		}
		return $items;		
	}
	
	/**
	* Delete a customer record.
	*
	* @param int $id customer ID
	*/
	public static function delete_record( $id ){
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}search_forms",
			[ 'id' => $id ],
			[ '%d' ]
		);
		
		$wpdb->delete(
			"{$wpdb->prefix}search_form_products",
			[ 'form_id' => $id ],
			[ '%d' ]
		);
	}

	function no_items(){
		_e( 'No order found.' );
	}

	function column_default( $item, $column_name ){
		switch( $column_name ){ 
			case 'order_id':
			case 'order_date':
			case 'approved':
			case 'sku':
			case 'product':
			case 'qty':
			case 'sales_ttl':
			case 'store_price':
			case 'discount':
			case 'commission_per':
			case 'commission_ttl':
			//case 'payment_id':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	function get_sortable_columns(){
		$sortable_columns = array(
			'order_id'  => array('order_id',false),
			'order_date' => array('order_date',false),
			'sku'   => array('sku',false)
		);
		return $sortable_columns;
	}

	function get_columns(){
		$columns = array(
			/*'cb'        => '<input type="checkbox" />',*/
			'order_id' => __( 'Order No', $this->plugin_name ),
			'order_date'    => __( 'Order date', $this->plugin_name ),
			'approved'      => __( 'Approved', $this->plugin_name ),
			'sku'      => __( 'Sku', $this->plugin_name ),
			'product'      => __( 'Product', $this->plugin_name ),
			'qty'      => __( 'Quantity', $this->plugin_name ),
			'store_price'      => __( 'Store Price', $this->plugin_name ),
			'discount'      => __( 'Discount', $this->plugin_name ),
			'sales_ttl'      => __( 'Sales TTL', $this->plugin_name ),
			'commission_per'   => __( 'Commission %', $this->plugin_name ),
			'commission_ttl'      => __( 'Commission TTL', $this->plugin_name ),
			//'payment_id'      => __( 'Payment Id', $this->plugin_name ),
			
		);
		return $columns;
	}

	
	/*function column_keyword($item){
	$delete_nonce = wp_create_nonce( 'cs_delete_record' );
	$actions = array(
	'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>','new-search-page','edit',$item['id']),
	'delete' => sprintf( '<a href="?page=%s&action=%s&searchId=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
	'view'      => sprintf('<a href="%s/s/%s" target="_blank">View Page</a>', site_url(),$item['keyword'])
	);

	return sprintf('%1$s %2$s', $item['keyword'], $this->row_actions($actions) );
	}
	
	function column_add_date($item){
	$date=date_create($item['add_date']);
	return 'Published'.'<br>'.'<abbr title="'.date_format($date,"Y/m/d H:i:s A").'">'.date_format($date,"Y/m/d ").'</abbr>';
	}*/

	/**
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions(){
		$actions = [
			//'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	/*function column_approved($item) {
	return sprintf(
	'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
	);    
	}*/

	function prepare_items(){
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		/** Process bulk action */
		$this->process_bulk_action();

		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option('per_page', 'option');	 
		$per_page = get_user_meta($user, $option, true);
		if( empty ( $per_page) || $per_page < 1 ){
		 
			$per_page = $screen->get_option( 'per_page', 'default' );
		 
		}

		$current_page = $this->get_pagenum();
		$total_items = self::record_count();

		
		$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page                     //WE have to determine how many items to show on a page
			) );
		
		$this->items = self::get_records( $per_page, $current_page );
	}
	
	public function process_bulk_action(){


		//Detect when a bulk action is being triggered...
		if( 'delete' === $this->current_action() ){

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if( ! wp_verify_nonce( $nonce, 'cs_delete_record' ) ){
				die( 'Go get a life script kiddies' );
			}
			else{
				self::delete_record( absint( $_GET['searchId'] ) );
				$this->wsc_add_notice2("Form deleted Successfully",'note');
				wp_redirect("admin.php?page=afilliate");
				exit;
			}

		}
		//print_r($_GET);

		// If the delete bulk action is triggered
		if( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
			|| ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		){

			$delete_ids = esc_sql( $_GET['bulk-delete'] );



			// loop over the array of record IDs and delete them
			foreach( $delete_ids as $id ){
				self::delete_record( $id );

			}

			$this->wsc_add_notice2("Form deleted Successfully",'note');
			wp_redirect("admin.php?page=afilliate");
			exit;
		}
	}
	public function wsc_add_notice2($notice, $type = 'error'){
		$types = array(
			'error' => 'error',
			'warning' => 'update-nag',
			'info' => 'check-column',
			'note' => 'updated',
			'none' => '',
		);
		if(!array_key_exists($type, $types))
		$type = 'none';

		$notice_data = array('class' => $types[$type], 'message' => $notice);

		$key = 'wsc_admin_notices_' . get_current_user_id();
		$notices = get_transient($key);

		if(FALSE === $notices)
		$notices = array($notice_data);

		// only add the message if it's not already there
		$found = FALSE;
		foreach($notices as $notice){
			if($notice_data['message'] === $notice['message'])
			$found = TRUE;
		}
		if(!$found)
		$notices[] = $notice_data;

		set_transient($key, $notices, 3600);
	}

}

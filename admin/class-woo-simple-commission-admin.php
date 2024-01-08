<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://compactimpact.com
 * @since      1.0.0
 *
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/admin
 * @author     Jade-eCommerce <support@j-e.com.hk>
 */
class Woo_Simple_Commission_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Simple_Commission_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Simple_Commission_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-simple-commission-admin.css', array(), '', 'all' );
		
		wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Simple_Commission_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Simple_Commission_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-simple-commission-admin.js', array( 'jquery' ), $this->version, false );
		
		
		wp_enqueue_script( 'jquery-ui-datepicker' );

	}
	public function wsc_admin_menu(){
		$wsc_screen_page = add_menu_page('Affilliate Reports', 'Affilliate Reports', 'administrator', 'afilliate',array(&$this, 'all_commission'),'dashicons-admin-site',6);
		 //add_submenu_page( 'search-pages', 'New Search Page', 'Add New', 'edit-posts', 'new-search-page', array(&$this, 'add_search_page') );
		 //add_submenu_page( 'search-pages', 'Options Page', 'Options', 'edit-posts', 'option-page', array(&$this, 'option_search_page') );
		 add_action("load-$wsc_screen_page", array(&$this, 'wsc_sample_screen_options'));

	}
	function wsc_sample_screen_options(){
 
		$screen = get_current_screen();
		//echo $screen->id;
		// get out of here if we are not on our settings page
		if(!is_object($screen) || $screen->id != 'toplevel_page_afilliate')
			return;
	 
		$args = array(
			'label' => __('Commission per page', $this->plugin_name),
			'default' => 10,
			'option' => 'commission_per_page'
		);
		add_screen_option( 'per_page', $args );

	}
	function wsc_set_screen_option($status, $option, $value) {
		if ( 'commission_per_page' == $option ) return $value;
	}

	function all_commission(){
		if( isset($_REQUEST['tab']) && $_REQUEST['tab']=='payment'){
			include_once 'partials/payment.php';
		}elseif(isset($_REQUEST['tab']) && $_REQUEST['tab']=='cancelled'){
			
			include_once 'partials/cancel.php';
		}elseif(isset($_REQUEST['tab']) && $_REQUEST['tab']=='export'){
			
			 if( isset($_REQUEST['start']) && $_REQUEST['start']!='' && isset($_REQUEST['end']) && $_REQUEST['end']!=''){
                     $this->export_affilliate($_REQUEST['start'] , $_REQUEST['end']);
                }
		}
		else{
			include_once 'partials/woo-simple-commission-admin-display.php';
		}
			
	}
	public function export_affilliate($start_date, $end_date){
		//echo $start_date.' = ' .$end_date;
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
		 // echo '<br>'.$date_created;
	 	
		$final_order = array();

		$query = new WC_Order_Query( array(
				'limit' => -1,
				//'status' => 'completed',
				'orderby' => 'date',
				'order' => 'ASC',
				'date_created' => $date_created,
				'return' => 'ids',
			) );
		 $orders    = $query->get_orders();
		 //print_r($orders);
		 if(empty($orders)){
		 	return $final_order;
		 }
		$ttl_sales = 0;
		$ttl_commission = 0;
		$ttl_unapproved = 0;
		$ttl_approved = 0;
		$ttl_paid       = 0;
		$ttl_unpaid     = 0;
		 $header = array(
		 	'Order No','Order date','Approved','Sku','Product','Quantity','Store Price','Discount','Sales TTL','Commission %','Commission TTL'
		 );
		ob_end_clean();
		$filename = "affilliate_report_".$start_date."_".$end_date.".csv";
		$fp = fopen('php://output', 'w');
		
		fputcsv($fp, $header);
		$currencySymbol = html_entity_decode(get_woocommerce_currency_symbol());
		$currencySymbol = str_replace("Â"," ",$currencySymbol);
		//echo $currencySymbol;
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
					$order_id,
					date("Y-m-d" ,$order_date),
					 ($order->get_status()=='completed')?'Approved':'-', 
					 $product->get_sku(),
					 $product_name,
					 $item->get_quantity(),
					 $currencySymbol.' '.$product->get_price(),
					 $currencySymbol.' '.($product->get_price() - $item->get_total()), 
					 $currencySymbol.' '.$item->get_total(),
					 $comm_per,
					 $currencySymbol.' '.$comm_amount,
				);

				fputcsv($fp, $info);
				
			}
		}
		fputcsv($fp, []);
		$summary_header = array(
			'Total Sales','Pending','Completed','Total Commission','Paid','Balance'
		);	
		fputcsv($fp, $summary_header);	
		$summary_data = $this->wsc_affilliate_stats_action($date_created);
		fputcsv($fp, $summary_data);

		 header('Content-Type: text/csv; charset=utf-8');
         header('Content-Disposition: attachment; filename='.$filename);
         exit;

	}
	public function wsc_payment_commission_action(){
		//print_r($_POST);
		$payment_id = $_POST['payment_id'];
		global $wpdb;
		$tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		$tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
		$sql  = "SELECT order_id FROM $tableOrders WHERE payment_id ='".$payment_id."' ";
		$existOrders = $wpdb->get_results($sql,ARRAY_A);
		$info = array();
		if($wpdb->num_rows > 0 && !empty($existOrders)){
			//print_r($existOrders);
			foreach($existOrders as $key => $order){
                     $order_id = $order['order_id'];
                    $order = wc_get_order( $order_id );
                    $items = $order->get_items();
                    $order_date = $order->get_date_created()->getOffsetTimestamp();
                    
                    foreach($items as $item){
                      
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

                      if($order->get_status()=='completed'){ 
                        $data = array(
                         'order_id'   => $order_id,
                         'order_date' => date("Y-m-d" ,$order_date),
                         //'status'     => 'Approved',
                         'sku'        => $product->get_sku(),
                         'name'       => $product_name,
                         'qty'        => $item->get_quantity(),
                         'sales_ttl'  => get_woocommerce_currency_symbol().' '.$item->get_total(),
                         'comm_per'   => $comm_per,
                         'comm_ttl'   => get_woocommerce_currency_symbol().' '.$comm_amount,
                        );
                       array_push($info ,$data);
                      }  
                }
                  
              }
              echo json_encode($info);
		      wp_die();
		}else{
			echo 'false';
			wp_die();
		}
		
	}
	public function wsc_edit_payment_mode_action(){
		//print_r($_POST);
		$payment_id = $_POST['payment_id'];
		$field      = $_POST['field'];
		$val        = $_POST['value'];
		global $wpdb;
		$tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		$sql = "UPDATE $tablePayment SET $field = '".$val."' WHERE payment_id='".$payment_id."' ";
		$query = $wpdb->query($sql);
		wp_die();
	}
	public function wsc_affilliate_stats_action($date_range=''){
		//print_r($_POST);
		global $wpdb;
		$tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		$tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
		
		$ttl_sales = 0;
		$ttl_commission = 0;
		$ttl_unapproved = 0;
		$ttl_approved = 0;
		$ttl_paid       = 0;
		$ttl_unpaid     = 0;

		$args = array(
				'limit' => -1,
				'orderby' => 'date',
				'order' => 'ASC',
				'return' => 'ids',
		);
		if($date_range!=''){
			$args['date_created'] = $date_range;
		}
		
		$query = new WC_Order_Query($args);
		$allorders = $query->get_orders();
					
		$paid_sql = "SELECT sum(total_commission) AS ttl_paid FROM $tablePayment WHERE paid_date != 'null'";
		$paidRes = $wpdb->get_results($paid_sql,ARRAY_A);
		//print_r($paidRes);
		$ttl_paid = ($paidRes[0]['ttl_paid']!='')?$paidRes[0]['ttl_paid']:'0';

		//$ttl_commission = $res[0]['ttl_commission'];
			
			foreach($allorders as $allorders){
				
				$order = wc_get_order( $allorders );
                $items = $order->get_items();
                foreach($items as $item){

                	$comm_per = intval(get_post_meta( $item->get_variation_id(), '_commission_rate', true ));
					
					if($comm_per){
						$ttl_commission += ( $item->get_total() * $comm_per)/100;
					}
                	
                	$ttl_sales += $item->get_total();
                	
                	if($order->get_status()=='completed'){
					   
					   $ttl_approved += $item->get_total();
					}else{
						$ttl_unapproved += $item->get_total();
					}
                	
                }
				
			}
			$ttl_unpaid = $ttl_commission - $ttl_paid;
		if($date_range!=''){
			$currencySymbol = html_entity_decode(get_woocommerce_currency_symbol());
		}else{
			$currencySymbol = get_woocommerce_currency_symbol();
		}
	
		$data = array('ttl_sales' => $currencySymbol.' '.$ttl_sales,'ttl_unapproved' => $currencySymbol.' '.$ttl_unapproved,'ttl_approved' =>$currencySymbol.' '.$ttl_approved,'ttl_commission' => $currencySymbol.' '.$ttl_commission,'ttl_paid' =>$currencySymbol.' '.$ttl_paid,'ttl_unpaid' =>$currencySymbol.' '.$ttl_unpaid);
		if($date_range!=''){
			return $data;

		}else{
			echo json_encode($data);
		     wp_die();
		}
		
	}
	public function wsc_payment_stats_action(){
		//print_r($_POST);
		global $wpdb;
		$tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		$tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
		
		$ttl_sales = 0;
		$ttl_commission = 0;
		$ttl_unapproved = 0;
		$ttl_approved = 0;
		$ttl_paid       = 0;
		$ttl_unpaid     = 0;
		
		$query = new WC_Order_Query( array(
				'limit' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
				'return' => 'ids',
			) );
		$allorders = $query->get_orders();
		
		 $sql = "SELECT SUM(t1.total_commission) AS ttl_commission ,(SELECT GROUP_CONCAT(t2.order_id SEPARATOR ',') FROM $tableOrders AS t2 ) AS order_ids FROM $tablePayment AS t1";
		$res = $wpdb->get_results($sql,ARRAY_A);
		//print_r($res);
		if(!empty($res)){
			
		$paid_sql = "SELECT sum(total_commission) AS ttl_paid FROM $tablePayment WHERE paid_date != 'null'";
		$paidRes = $wpdb->get_results($paid_sql,ARRAY_A);
		//print_r($paidRes);
		$ttl_paid = ($paidRes[0]['ttl_paid']!='')?$paidRes[0]['ttl_paid']:'0';

		$ttl_commission = $res[0]['ttl_commission'];
		
		$ttl_unpaid = $ttl_commission - $ttl_paid;
		$orders         = $res[0]['order_ids'];
		if($orders!=''){
			$orders = explode(',',$orders);
			//print_r($orders);
			//print_r($allorders);
			foreach($allorders as $allorders){
				
				$order = wc_get_order( $allorders );
                $items = $order->get_items();
                foreach($items as $item){
                	
                	
                	$ttl_sales += $item->get_total();
                	
                	if($order->get_status()=='completed'){
					   
					   $ttl_approved += $item->get_total();
					}else{
						$ttl_unapproved += $item->get_total();
					}
                	
                }
				
			}
		}
	 }
		echo json_encode(array('ttl_sales' => get_woocommerce_currency_symbol().' '.$ttl_sales,'ttl_commission' => get_woocommerce_currency_symbol().' '.$ttl_commission,'ttl_unapproved' => get_woocommerce_currency_symbol().' '.$ttl_unapproved,'ttl_approved' =>get_woocommerce_currency_symbol().' '.$ttl_approved,'ttl_paid' =>get_woocommerce_currency_symbol().' '.$ttl_paid,'ttl_unpaid' =>get_woocommerce_currency_symbol().' '.$ttl_unpaid));
		wp_die();
	}
	
	public function wsc_woocommerce_order_status_cancelled($order_id){
		global $wpdb;
		$tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		$tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
		$tableOrdersCancel  = $wpdb->prefix.'wc_simple_commission_orders_cancel';
		
		$sql = "SELECT t1.payment_id,t1.total_commission,t1.paid_date,t2.payment_id,t2.order_id FROM $tablePayment t1 JOIN $tableOrders t2 ON t1.payment_id = t2.payment_id WHERE t2.order_id = '".$order_id."' LIMIT 1";
		$res = $wpdb->get_results($sql,ARRAY_A);
		if(!empty($res) && $wpdb->num_rows > 0){
			
			$payment_id = $res[0]['payment_id'];
			$total_commission = $res[0]['total_commission'];
			$paid_date = $res[0]['paid_date'];
			
			$order = wc_get_order( $order_id );
            $items = $order->get_items();
            $comm_amount = 0;
            foreach($items as $item){
            	$comm_per = intval(get_post_meta( $item->get_variation_id(), '_commission_rate', true ));
                  if($comm_per){
                    $comm_amount += ( $item->get_total() * $comm_per)/100;
                  }
            }
            //deduct item commission from total commission
            $final_commission = $total_commission - $comm_amount;
            $update_sql = "UPDATE $tablePayment SET total_commission='".$final_commission."' WHERE payment_id = '".$payment_id."' ";
            $wpdb->query($update_sql);
            
            if($paid_date !='null'){
            	
			  $cancelledItems= array('order_id' => $order_id,'amount'=>$comm_amount);
			  $wpdb->insert($tableOrdersCancel,$cancelledItems);
			}
			//delete order from commsion order table
            $del_sql = "DELETE FROM $tableOrders WHERE `order_id` = '".$order_id."'";
            $wpdb->query($del_sql);
            
                
		}
		
	}
	
	public function wsc_add_notice($notice, $type = 'error')
	{
		$types = array(
			'error' => 'error',
			'warning' => 'update-nag',
			'info' => 'check-column',
			'note' => 'updated',
			'none' => '',
		);
		if (!array_key_exists($type, $types))
			$type = 'none';

		$notice_data = array('class' => $types[$type], 'message' => $notice);

		$key = 'wsc_admin_notices_' . get_current_user_id();
		$notices = get_transient($key);

		if (FALSE === $notices)
			$notices = array($notice_data);

		// only add the message if it's not already there
		$found = FALSE;
		foreach ($notices as $notice) {
			if ($notice_data['message'] === $notice['message'])
				$found = TRUE;
		}
		if (!$found)
			$notices[] = $notice_data;

		set_transient($key, $notices, 3600);
	}
	
	/**
	 * Create new fields for variations
	 *
	*/
	function wsc_variation_settings_fields( $loop, $variation_data, $variation ) {
		// Commission Rate
		if ( ! function_exists( 'variation_settings_fields' ) ) {
		woocommerce_wp_text_input( 
			array( 
				'id'          => '_commission_rate[' . $variation->ID . ']',
				'class'       => 'custom-variation-commission-rate',
				'label'       => __( 'Commission Rate(%)', 'woocommerce' ), 
				'desc_tip'    => 'true',
				'description' => __( 'Enter the Commission Rate here in percent.', 'woocommerce' ),
				'value'       => get_post_meta( $variation->ID, '_commission_rate', true ),
				'data_type'   => 'decimal',
				'wrapper_class' => 'form-row form-row-first',
				'custom_attributes' => array(
								'step' 	=> 'any',
								'min'	=> '0',
								'onkeyup' => "calculate_commission(this.value,'_commission_amount[" . $variation->ID . "]','".$variation_data['_regular_price'][0]."');",
							) 
			)
		);
	  }
	}
	/**
	 * Save new fields for variations
	 *
	*/
	function wsc_save_variation_settings_fields( $post_id ) {
		// Number Field
		if ( ! function_exists( 'save_variation_settings_fields' ) ) {
			$commission_rate = $_POST['_commission_rate'][ $post_id ];
			if( ! empty( $commission_rate ) ) {
				update_post_meta( $post_id, '_commission_rate', esc_attr( $commission_rate ) );
			}
	    }
	}

}

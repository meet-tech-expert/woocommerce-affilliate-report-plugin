<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://compactimpact.com
 * @since      1.0.0
 *
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/admin/partials
 */
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<style>
.table-striped tbody td.payment-id:hover{
  cursor: pointer;
}
.table-dark{
	background-color: #0e365f;
}
.table-dark td, .table-dark th, .table-dark thead th {
    border-color: #ffffff;
}
.table-responsive {
    max-height:300px;
}
img.wsc-loader , img.stats-loader {
    margin-left: 40%;margin-bottom: 10px;
}
span.dashicons:hover {
    cursor: pointer;
}
span.dashicons {
    font-size: 30px;
   /* vertical-align: middle;*/
    margin-left: 5px;
}
span.text {
    vertical-align: -webkit-baseline-middle;
}span.stats {
    border-right: 1px solid;
    padding-right: 5px;
    font-weight: 700;
}
span.stats:last-child {
    border: none;
}
</style>
<div class="wrap">
<?php 
include_once('notification.php'); ?>

	<div id="poststuff" class="">

        <div id="post-body">
            <div id="post-body-content">
             <!--<h1 class="wp-heading-inline">Payments Table</h1>-->
             <fieldset id="stats" style="display: none;">
			  <legend></legend>
				  Total Sales: <span class="stats ttl_sales">0</span>
				  Total Commission: <span class="stats ttl_commission">0</span>
				  Total Unapproved: <span class="stats ttl_unapproved">0</span>
				  Total Approved: <span class="stats ttl_approved">0</span>
				  Total Paid: <span class="stats ttl_paid">0</span>
				  Total Unpaid: <span class="stats ttl_unpaid">0</span>
			 
			 </fieldset>
			 <img src="<?php echo admin_url('/images/wpspin_light-2x.gif'); ?>" class="stats-loader" style="display: none;" />
             <br>
             
            <hr class="wp-header-end">
             <?php
                
              ?>
              <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover" >
                <thead>
                  <th>Order Id</th>
                  <th>Status</th>
                  <th>Amount</th>
                </thead>
                <tbody>
                <?php
                global $wpdb;
				$tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
				$tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
				$tableOrdersCancel  = $wpdb->prefix.'wc_simple_commission_orders_cancel';
				
				$sql = "SELECT * FROM $tableOrdersCancel ORDER BY id DESC";
				$cancelledItems = $wpdb->get_results($sql,ARRAY_A);
				
               /* echo "<pre>";
                print_r($cancelledItems);
                echo "</pre>";*/
                if(!empty($cancelledItems)&& $wpdb->num_rows > 0){
              	
              		foreach($cancelledItems as $items){ 
              		$order_id = $items['order_id'];
              		$order = wc_get_order( $order_id );
              		?>
	                  <tr class="payment-row">
	                    <td class="payment-id" data-id=""><?php echo $order_id; ?></td>
	                    <td><?php echo ucfirst($order->get_status()); ?></td>
	                    <td><?php echo get_woocommerce_currency_symbol().' '.$items['amount']; ?></td>
	                   
	                  </tr>
                   <?php } }else{
                   	echo "<tr><td colspan='3'>No items found</td></tr>";
                   	} ?>
              </tbody>
              </table>  
              </div>
             
            </div>
        </div>
    </div>        
</div>
<script>
</script>
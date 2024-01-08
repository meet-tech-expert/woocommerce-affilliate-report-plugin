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
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url(). '/woo-simple-commission/admin/css/woo-simple-commission-admin.css';?>" />
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
.table td, .table th{
  text-align: right;
}
</style>
<div class="wrap">
<?php 
include_once('notification.php'); ?>

	<div id="poststuff" class="">

        <div id="post-body">
            <div id="post-body-content">
              <div class="sendgrid-container wordpress-new" id="stats"  style="display: none;">
  <div class="widget others" id="deliveries">
    <div class="widget-top">
      <div class="widget-title"><h4>Overview</h4></div>
    </div>
    <div class="widget-inside">
      <div class="row clearfix">
        <div class="pull-left">
          <span class="square" style="background-color: rgb(50,135,1);"></span><span>Total Sales:</span>
        </div>
        <div id="" class="ttl_sales pull-right">0</div>
      </div>
      <div class="row clearfix">
        <div class="pull-left">
          <span class="square" style="background-color: rgb(188,213,22);"></span><span>Pending:</span>
        </div>
        <div id="" class="ttl_unapproved pull-right">0</div>
      </div>
      <div class="row clearfix">
        <div class="pull-left">
          <span class="square" style="background-color: rgb(251,166,23);"></span><span>Completed:</span>
        </div>
        <div id="" class="ttl_approved pull-right">0</div>
      </div> 
	 <div class="row clearfix">
        <div class="pull-left">
          <span class="square" style="background-color: rgb(23, 146, 251);"></span><span>Total Commission:</span>
        </div>
        <div id="" class="ttl_commission pull-right">0</div>
      </div>
      <div class="row clearfix">
        <div class="pull-left">
          <span class="square" style="background-color: rgb(230, 2, 65);"></span><span>Paid:</span>
        </div>
        <div id="" class="ttl_paid pull-right">0</div>
      </div>
      <div class="row clearfix">
        <div class="pull-left">
          <span class="square" style="background-color: rgb(156, 19, 181);"></span><span>Balance:</span>
        </div>
        <div id="" class="ttl_unpaid pull-right">0</div>
      </div>
      <br class="clearfix-clear">
    </div>
  </div>
  
  <br class="clearfix-clear">

  </div>
         
             <fieldset id="" style="display: none;">
            <legend></legend>
              Total Sales: <span class="stats ttl_sales">0</span>
              Pending: <span class="stats ttl_unapproved">0</span>
              Completed: <span class="stats ttl_approved">0</span>
              Total Commission: <span class="stats ttl_commission">0</span>
              Paid: <span class="stats ttl_paid">0</span>
              Balance: <span class="stats ttl_unpaid">0</span>
           
           </fieldset>
			 <img src="<?php echo admin_url('/images/wpspin_light-2x.gif'); ?>" class="stats-loader" style="display: none;" />
             <br>
             <?php 
                if( isset($_REQUEST['start']) && $_REQUEST['start']!='' && isset($_REQUEST['end']) && $_REQUEST['end']!=''){
                    //echo "<div class='update-nag'>Display Records FROM: ".$_REQUEST['start']." TO: ".$_REQUEST['end']."</div>";
                  $myListTable = new Wsc_Payment_Table();
		          $rows =  $myListTable::get_records($_REQUEST['start'] , $_REQUEST['end']);
                }
             ?>
            <hr class="wp-header-end">
             <?php
             global $wpdb;
              $tablePayment = $wpdb->prefix.'wc_simple_commission_payment';
		      $tableOrders  = $wpdb->prefix.'wc_simple_commission_orders';
              //
              /*echo "<pre>";
              print_r($rows);
               echo "</pre>";*/  
                $sql_query = "SELECT * ,(SELECT GROUP_CONCAT(co.order_id SEPARATOR ',') AS order_ids  FROM $tableOrders co WHERE co.payment_id = cp.payment_id) AS order_ids FROM $tablePayment cp ORDER BY cp.payment_id DESC";
                

			     $allPayments = $wpdb->get_results($sql_query,ARRAY_A);
			      /*echo "<pre>";
	               print_r($allPayments);
	               echo "</pre>";*/
	               //exit;
              ?>
              <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover" >
                <thead>
                  <th>Payment Id</th>
                  <th>From Date</th>
                  <th>End Date</th>
                  <th>Amount</th>
                  <th>Date Paid</th>
                  <th>Memo</th>
                </thead>
                <tbody>
                <?php
                if($wpdb->num_rows > 0 && !empty($allPayments)){
              	
              		foreach($allPayments as $payments){ ?>
	                  <tr class="payment-row">
	                    <td class="payment-id" data-id="<?php echo $payments['payment_id']; ?>"><?php echo $payments['payment_id']; ?></td>
	                    <td><?php echo $payments['start_date']; ?></td>
	                    <td><?php echo $payments['end_date']; ?></td>
	                    <td><?php echo get_woocommerce_currency_symbol().' '.$payments['total_commission']; ?></td>
	                    <td class="save-mode" data-type="date"><span class='text'><?php echo $payments['paid_date'];?></span><span data-id="<?php echo $payments['payment_id']; ?>" class="dashicons dashicons-edit"></span>
	                    </td>
	                    
	                    <td class="save-mode" data-type="text"><span class='text'><?php echo $payments['memo'];?></span><span data-id="<?php echo $payments['payment_id']; ?>" class="dashicons dashicons-edit"></span>
	                    </td>
	                  </tr>
                   <?php } } ?>
              </tbody>
              </table>  
              </div>
              <img src="<?php echo admin_url('/images/wpspin_light-2x.gif'); ?>" class="wsc-loader" style="display: none;" />
              <hr>
              <div class="table-responsive">
            	<table id="payment_table" class="table table-striped table-bordered table-hover table-dark" style="display: none;">
            	<input type="hidden" value="0" id="activePaymentId" >
              	<thead>
              		<th>Order Id</th>
              		<th>Order Date</th>
              		<th>Approved</th>
              		<th>Sku</th>
              		<th>Product</th>
                    <th>Quantity</th>
                    <th>Sales TTL</th>
                    <th>Commission %</th>
                    <th>Commission TTL</th>
              	</thead>
              	<tbody id="tbodyData">
              		
              	</tbody>
              	</table>
              	</div>
            </div>
        </div>
    </div>        
</div>
<script>
 jQuery(".dashicons").on('click',function(){
 	var mode = jQuery(this).closest('td').attr('class');
 	var type = jQuery(this).closest('td').data('type');
 	var span = jQuery(this).closest('td').find('span.text');
 	var id   = jQuery(this).data('id');
 	console.log(id);
 	if(mode == 'save-mode'){
		jQuery(this).removeClass('dashicons-edit').addClass('dashicons-yes');
		jQuery(this).closest('td').attr('class','edit-mode');
		jQuery(span).html("<input type='"+type+"' value='"+span.text()+"'>");
		jQuery("input").focus();
		
	}else if(mode == 'edit-mode'){
		jQuery(this).removeClass('dashicons-yes').addClass('dashicons-edit');
		jQuery(this).closest('td').attr('class','save-mode');
		var vals = jQuery(span).find('input').val();
		jQuery(span).html(vals);
		var field = (type=='date')? 'paid_date' :'memo';
		console.log(vals);
		if(vals!=''){
			jQuery.ajax({
	     	url: "<?php echo admin_url('admin-ajax.php'); ?>",
	     	type: "POST",
	     	data: {
				'action' : 'edit_payment_mode',
				'payment_id':id,
				'value':vals,
				'field':field,
			},
			success:function(res){
				console.log(res);
			}	
	   		});
		}
		
	}
 });	
  

 jQuery(".payment-id").on('click',function(){
 	jQuery(".payment-row").removeClass('table-primary');
 	jQuery(this).closest('.payment-row').addClass('table-primary');
    var id = jQuery(this).data('id');
     if(id == jQuery("#activePaymentId").val()){
	 	jQuery("#payment_table").show();
	 	return;
	 }else{
	 	jQuery("img.wsc-loader").show();
	 	jQuery("#payment_table").hide();
	 }
     var tbodyHtml = '';
     jQuery.ajax({
     	url: "<?php echo admin_url('admin-ajax.php'); ?>",
     	type: "POST",
     	data: {
			'action' : 'payment_commission',
			'payment_id':id
		},
		success:function(res){
			//console.log(res);
			if(res !='false'){
				jQuery.each(jQuery.parseJSON(res), function(keys,rows){
					tbodyHtml += "<tr>";
					tbodyHtml += "<td>"+rows.order_id+"</td>";
					tbodyHtml += "<td>"+rows.order_date+"</td>";
					tbodyHtml += "<td>Approved</td>";
					tbodyHtml += "<td>"+rows.sku+"</td>";
					tbodyHtml += "<td>"+rows.name+"</td>";
					tbodyHtml += "<td>"+rows.qty+"</td>";
					tbodyHtml += "<td>"+rows.sales_ttl+"</td>";
					tbodyHtml += "<td>"+rows.comm_per+"</td>";
					tbodyHtml += "<td>"+rows.comm_ttl+"</td>";
					tbodyHtml += "</tr>";	
				});
			}else{
				tbodyHtml += "<tr><td colspan='9'>No items found</td></tr>";
			}
			jQuery("#activePaymentId").val(id);
			jQuery("#tbodyData").html(tbodyHtml);
			jQuery("#payment_table").show();
			jQuery("img.wsc-loader").hide();
		},
		error:function(){
			console.log('error');
		}
     });
      
    });

 jQuery("document").ready(function(){
 	setTimeout(function(){
 		jQuery("img.stats-loader").show();
 		jQuery.ajax({
     	url: "<?php echo admin_url('admin-ajax.php'); ?>",
     	type: "POST",
     	data: {
			'action' : 'payment_stats',
		},
		success:function(res){
			//console.log(res);
			var response = jQuery.parseJSON(res);
			jQuery('.ttl_sales').html(response.ttl_sales);
			jQuery('.ttl_commission').html(response.ttl_commission);
			jQuery('.ttl_unapproved').html(response.ttl_unapproved);
			jQuery('.ttl_approved').html(response.ttl_approved);
			jQuery('.ttl_paid').html(response.ttl_paid);
			jQuery('.ttl_unpaid').html(response.ttl_unpaid);
			jQuery("img.stats-loader").hide();
			jQuery("#stats").show();
		}	
 	   });
 	},1000);
 });	

</script>
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

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url(). '/woo-simple-commission/admin/css/woo-simple-commission-admin.css';?>" />


<style type="text/css">
    input.date-range{
        padding: 3px 8px;
    font-size: 1.7em;
    line-height: 100%;
    height: 1.7em;
    width: 30%;
    outline: 0;
    margin: 0 0 3px;
    background-color: #fff;
    }
    span.stats {
    border-right: 1px solid;
    padding-right: 5px;
    font-weight: 700;
}
span.stats:last-child {
    border: none;
}
fieldset{
  font-size: 15px;
  margin: 10px 0px 10px;
}
</style>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
<?php include_once('notification.php'); ?>

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
           <img src="<?php echo admin_url('/images/wpspin_light-2x.gif'); ?>" class="stats-loader" style="display: none; margin-top: 10px;" />
            <hr class="wp-header-end">
             <?php
              $myListTable = new Wsc_List_Table();
		          $myListTable->prepare_items(); ?>
              <form method="get">
              <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> 
                <?php
                //$myListTable->search_box( 'search', 'search_id' );
                $myListTable->display(); 
                echo '</form>'; 
             ?>
             <input type="text" name="dates" class="date-range">
             <button class="button button-primary button-large" id="create_payment">Create Payment</button>
             <button class="button button-large" id="export-affiliate">Export Affilliate Report</button>
            </div>
            
        </div>
    </div>        
</div>

<script>
var paymentURL ='';
var exportURL = '';
    jQuery('input[name="dates"]').daterangepicker({
    	locale: { cancelLabel: 'Clear' }
    });
    jQuery('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
    	paymentURL ='';
      console.log(picker.startDate.format('YYYY-MM-DD'));
      console.log(picker.endDate.format('YYYY-MM-DD'));
      var startDate = picker.startDate.format('YYYY-MM-DD');
      var endDate = picker.endDate.format('YYYY-MM-DD');
       paymentURL = '<?php echo get_admin_url(NULL ,'/admin.php?page=afilliate&tab=payment' ); ?>'+"&start="+startDate+"&end="+endDate;
       exportURL = '<?php echo get_admin_url(NULL ,'/admin.php?page=afilliate&tab=export' ); ?>'+"&start="+startDate+"&end="+endDate;
      
    });
    
    jQuery("#create_payment").on('click',function(){
    	if(paymentURL !=''){
			window.open(
	          paymentURL,
	          '_blank' // <- This is what makes it open in a new window.
	        );
		}
    	
    });

     jQuery("#export-affiliate").on('click',function(){
      if(exportURL !=''){
      window.open(
            exportURL,
            '_blank' // <- This is what makes it open in a new window.
          );
    }
      
    });

    jQuery("document").ready(function(){
  setTimeout(function(){
    jQuery("img.stats-loader").show();
    jQuery.ajax({
      url: "<?php echo admin_url('admin-ajax.php'); ?>",
      type: "POST",
      data: {
      'action' : 'affilliate_stats',
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
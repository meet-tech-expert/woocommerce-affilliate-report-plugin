<?php
class Woo_Simple_Commission_Activator{

	public static function activate(){
        ob_start();
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$tablePayment         = $wpdb->prefix . 'wc_simple_commission_payment';
		$tableOrders = $wpdb->prefix . 'wc_simple_commission_orders';
        $tableOrderCancel = $wpdb->prefix . 'wc_simple_commission_orders_cancel';
		/* 1. 
         * CREATE Payments TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tablePayment'") != $tablePayment){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tablePayment (
                 `payment_id` int(11) NOT NULL AUTO_INCREMENT,
				  `start_date` date NOT NULL,
				  `end_date` date NOT NULL,
				  `total_commission` int(11) NOT NULL,
				  `paid_date` date DEFAULT NULL,
				  `memo` text,
                 PRIMARY KEY (payment_id)) $charset_collate;";
            dbDelta($sql);
		
    	}
    	/* 2. 
         * CREATE Form orders TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableOrders'") != $tableOrders){ 
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql2 = "CREATE TABLE IF NOT EXISTS $tableOrders (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
  				 `payment_id` int(11) NOT NULL,
  				 `order_id` int(11) NOT NULL,
                  PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql2);
    	}   
        /* 3. 
         * CREATE Form ordersCancel TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableOrderCancel'") != $tableOrderCancel){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql3 = "CREATE TABLE IF NOT EXISTS $tableOrderCancel (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `order_id` int(11) NOT NULL,
                  `amount` int(11) NOT NULL,
                  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                   PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql3);
        }
       ob_flush();
	}
}
?>
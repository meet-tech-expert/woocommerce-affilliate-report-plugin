<?php
$key = 'wsc_admin_notices_'. get_current_user_id();
$notices = get_transient($key);
//print_r($notices);
if ($notices) {
    foreach ($notices as $notice)
        echo '<div class="', $notice['class'], '" style="padding:11px 15px; margin:5px 15px 2px 0;">', $notice['message'], '</div>' . PHP_EOL;
}
delete_transient( $key );

?>
<h2 class="nav-tab-wrapper">
					<a href="<?php echo get_admin_url(NULL , 'admin.php?page=afilliate' ); ?>" class="nav-tab <?php echo ( !isset($_REQUEST['tab']))?'nav-tab-active':''; ?>">Affilliate Reports</a>
					<a href="<?php echo get_admin_url(NULL ,'/admin.php?page=afilliate&tab=payment' ); ?>" class="nav-tab <?php echo ( isset($_REQUEST['tab']) && $_REQUEST['tab']=='payment')?'nav-tab-active':''; ?>">Payments</a>
					<a href="<?php echo get_admin_url(NULL ,'/admin.php?page=afilliate&tab=cancelled' ); ?>" class="nav-tab <?php echo ( isset($_REQUEST['tab']) && $_REQUEST['tab']=='cancelled')?'nav-tab-active':''; ?>">Cancelled Orders</a>
			
</h2>
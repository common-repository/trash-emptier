<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}
 
delete_option('mk_et_interval');
?>
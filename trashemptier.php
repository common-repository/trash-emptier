<?php
/*
Plugin Name: Trash emptier
Plugin URI: http://eng.marksw.com/2012/12/06/trash-emptier-wordpress-plugin/
Description: Provides control over trash emptying 
Author: Mark Kaplun
Version: 0.9
Author URI: http://eng.marksw.com
Text Domain: trash-emptier
Domain Path: /lang
*/

// no direct access
if ( !function_exists( 'add_action' ) ) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}
	
/*   
  Define EMPTY_TRASH_DAYS based on the value which was configured in the settings page,
  Skip it for the tools page as it might need to define different value for the manual delete
*/  
function mk_et_setconstant() {
	if ( defined( 'DOING_CRON' ) || is_admin()) {
		if (isset($_POST['mk_et_interval']) && ($_GET['page'] == 'trashemptier')) {
		   if (isset($_POST['mk_et_clearall'])) {
			 define('EMPTY_TRASH_DAYS', -1);
		   } else
			 define('EMPTY_TRASH_DAYS', (int) trim( $_POST['mk_et_interval']));
		} else {
		  define('EMPTY_TRASH_DAYS', get_option('mk_et_interval'));
		}
	}
}

add_action('plugins_loaded','mk_et_setconstant',1);

/*
  initialize option on activation.
*/  
function mk_et_activate() {
	if (get_option('mk_et_interval') == false) { // check if option is not already set from previos time the plugin was active
	  if (!defined(EMPTY_TRASH_DAYS)) { 
	    add_option('mk_et_interval',30,'','no'); // set to the defualt value when constant not defined
	  } else 
    	add_option('mk_et_interval',EMPTY_TRASH_DAYS,'','no');
	}
}

register_activation_hook( __FILE__, 'mk_et_activate' );
/*
  Load translation
*/  
function mk_et_textdomain() {

	load_plugin_textdomain( 'trash-emptier', false, dirname( plugin_basename(__FILE__) ) . '/lang');
	if (false) { // help poedit find translatable strings without actually executing anything
	  __('Trash emptier','trash-emptier');
	  __('Provides control over trash emptying','trash-emptier');
    }	  
}

/*
  Add links to the settings and tools page in the plugins management page
*/  
function mk_et_plugin_actions($links) {
	$settings_link = '<a href="tools.php?page=trashemptier">' . __('Automatic settings','trash-emptier') . '</a>';
	array_unshift( $links, $settings_link );
	$settings_link = '<a href="options-general.php?page=trashemptier">' . __('Manual','trash-emptier') . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

function mk_et_init() {
	
	add_action('admin_init', 'mk_et_textdomain');
	add_action('admin_menu', 'mk_et_add_settings_page');
}
add_action( 'init', 'mk_et_init' );

function mk_et_add_settings_page() {

    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'mk_et_plugin_actions',10,4 );
	add_options_page( __( 'Empty trash settings', 'trash-emptier' ), __( 'Empty trash', 'trash-emptier' ), 'manage_options', dirname( plugin_basename(__FILE__) ), 'mk_et_page', '' );
	add_management_page( __( 'Empty trash', 'trash-emptier' ), __( 'Empty trash', 'trash-emptier' ), 'manage_options', dirname( plugin_basename(__FILE__) ), 'mk_et_tool', '' );
}

/*
  Options page
*/  
function mk_et_page() {
?>
	<div class="warp">
	  <h2><?php _e('Empty trash scheduler','trash-emptier');?></h2>
<?php
    if (isset($_POST['mk_et_interval'])) {
	  if (check_admin_referer('mk_et_emptytrashinterval')) {
	    $message=true;
	    if (isset($_POST['mk_et_never']))
		  update_option('mk_et_interval',99999);
		else if (trim($_POST['mk_et_interval']) != '')
		  update_option('mk_et_interval',(int) trim($_POST['mk_et_interval']));
		else
		  $message = false;
	    if ($message) {
	      ?><div id="message" class="updated"><p><?php _e('Trash cleaning interval updated','trash-emptier'); ?></p></div><?php 
		}
	  }
	}
    $config = false;
	if ( file_exists( ABSPATH . 'wp-config.php') ) {
		$config = file_get_contents ( ABSPATH . 'wp-config.php' );
	} elseif ( file_exists( dirname(ABSPATH) . '/wp-config.php' ) && ! file_exists( dirname(ABSPATH) . '/wp-settings.php' ) ) {
		/** The config file resides one level above ABSPATH but is not part of another install */
		$config = file_get_contents ( dirname(ABSPATH) . '/wp-config.php' );
	} 
	$configset = false;
	if (EMPTY_TRASH_DAYS == 0) {
      echo '<p>'.__('Trash functionality is disbaled','trash-emptier').'<br>';
	} 
	if ($config)  {
	  if (strpos($config,'EMPTY_TRASH_DAYS') !== false) {
	    echo '<p>'.sprintf(__("This is configured in your wp-config.php file. You need to delete from it the line that looks like define('EMPTY_TRASH_DAYS',... if you want to manage the interval from here.",'trash-emptier'),EMPTY_TRASH_DAYS).'</p>';
		return;
	  } 
    }
?>
	  <form action="" method="post">
	    <?php
		  wp_nonce_field( 'mk_et_emptytrashinterval');
		  if (get_option('mk_et_interval') == 99999) {
		    $check = 'checked="checked"';
			$val = '';
		  } else {
		    $check = '';
			$val = get_option('mk_et_interval');
		  }
		?>
		<p><?php _e('Items which where in the trash for more then','trash-emptier')?> <input name="mk_et_interval" type="text" value="<?php echo esc_attr($val);?>"> <?php _e('days will be automatticaly deleted every day','trash-emptier')?></p>
		<p><input type="checkbox" name="mk_et_never" <?php echo $check;?>> <?php _e('Never','trash-emptier');?></p>
	    <p><button class="button-primary" name="setinterval" type="submit"><?php _e('Set interval','trash-emptier');?></button></p>
	  </form>	  
	  
	</div>
<?php
}

/*
   Tools page
*/   
function mk_et_tool() {
?>
	<div class="warp">
	  <h2><?php _e('Empty trash','trash-emptier');?></h2>
<?php
	$days = get_option('mk_et_interval');
	if (!$days)
	  $days = 30;
    if ('EMPTY_TRASH_DAYS' != (int) trim($_POST['mk_et_interval'])) {
	  echo '<p>'.__("Can't perform manual delete because EMPTY_TRASH_DAYS is defined in your wp-config.php file. You need to delete it from there (the line that looks like define('EMPTY_TRASH_DAYS',...) if you want to be able to manually delete items here.",'trash-emptier').'</p>';
      echo '</div>';
      return;
	}

   if (isset($_POST['mk_et_interval'])) {
	  if (check_admin_referer('mk_et_emptytrash')) {
	    $message=true;
	    if (isset($_POST['mk_et_clearall'])) {
		  wp_scheduled_delete();
		} else if (trim($_POST['mk_et_interval']) != '') {
		  wp_scheduled_delete();
		} else
		  $message = false;
	    if ($message) {
	      ?><div id="message" class="updated"><p><?php _e('Trash cleaned','trash-emptier'); ?></p></div><?php 
		}
	  }
	}
	if ($days<1000)
	  echo '<p>'.sprintf(__('Currently items that have been more then %d days in the trash are automaticaly deleted.','trash-emptier'),$days).'<br>';
	else
  	  echo '<p>'.sprintf(__('Currently items practically never being deleted from the trash.','trash-emptier'),$days).'<br>';
?>
	  
	  <form action="" method="post">
	    <?php
		  wp_nonce_field( 'mk_et_emptytrash');
		?>
		<p><?php _e('Delete items which where in the trash for more then','trash-emptier')?> <input name="mk_et_interval" type="text" value=""> <?php _e('days','trash-emptier')?></p>
		<p><input type="checkbox" name="mk_et_clearall"> <?php _e('Delete all of the trashed items');?></p>
	    <p><button class="button-primary" type="submit"><?php _e('Empty trash','trash-emptier');?></button></p>
	  </form>
	  
	</div>
<?php
}


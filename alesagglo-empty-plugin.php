<?php
/*
Plugin Name: Ales Agglo Empty Plugin
Plugin URI: 
Description: Empty Plugin by Ales Agglomeration
Version: 1.0.0
Author: Ales Agglomeration
Author URI: https://www.ales.fr/
Author EMail: contact@alesagglo.fr
Text Domain: alesagglo-empty-plugin
Domain Path: /languages
*/

defined('ABSPATH') || die();

define('AEP_SLUG', 'alesagglo-empty-plugin');

define('AEP_PATH', plugin_dir_path(__FILE__));
define('AEP_URL', plugin_dir_url(__FILE__));

define('AEP_CRON', false);
define('AEP_DEBUG', false);


// datas to admin setting options
const AEP_OPTIONS = array(
	'aep_cron_interval' => array(
		'label' => 'Cron Frequency',
		'type' => 'number',
		'default' => 900,
		'attributes' => 'readonly'
	),
);
// datas to front js var
const AEP_JSVAR = array(
	'aep_jsvar_name' => 'Hello from PHP to JS Var',
);
// datas to front cookie
const AEP_COOKIE = array(
	'aep_cookie_name' => 'Hello from PHP to Cookie',
);


/**
 *	activate / deactivate
 */
register_activation_hook(__FILE__, 'aep_activate');
function aep_activate() {
	aep_set_options();
	aep_set_cookie();
	if (AEP_CRON) aep_activate_cron();
	flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'aep_deactivate');
function aep_deactivate() {
	if (AEP_CRON) aep_deactivate_cron();
	flush_rewrite_rules();
}


/**
 *	uninstall
 */
register_uninstall_hook(__FILE__, 'aep_uninstall');
function aep_uninstall() {
	aep_unset_options();
	delete_transient('aep_data_transient');
}


/**
 *	load dependencies
 */
add_action('plugins_loaded', 'aep_init');
function aep_init() {
		aep_load_dependencies();
}
function aep_load_dependencies() {
	aep_load_textdomain();
	require_once AEP_PATH . 'vendor/autoload.php';
	require_once AEP_PATH . 'inc/tools.php';
	require_once AEP_PATH . 'inc/EmptyClass.php';
	add_action('wp_enqueue_scripts', 'aep_register_scripts');
	if (is_admin()) {
		require_once AEP_PATH . 'inc/tools-admin.php';
		add_action('admin_enqueue_scripts', 'aep_register_scripts');
		add_action('admin_enqueue_scripts', 'aep_register_admin_scripts');
	}
}
function aep_register_scripts() {
	wp_enqueue_script('aep-scripts', AEP_URL . 'assets/js/scripts.js');
	wp_enqueue_style('aep-styles', AEP_URL . 'assets/css/styles.css');
	wp_localize_script('aep-scripts', 'aep_data_jsvar', AEP_JSVAR);
}
function aep_register_admin_scripts() {
	// wp_enqueue_media();
	wp_enqueue_script('aep-scripts-admin', AEP_URL . 'assets/js/scripts-admin.js');
	wp_enqueue_style('aep-styles-admin', AEP_URL . 'assets/css/styles-admin.css');
}


/**
 *	I18
 */
function aep_load_textdomain() {
	$loaded = load_plugin_textdomain(AEP_SLUG, false, dirname(plugin_basename(__FILE__)) . '/languages');
}


/**
 *	set / unset options
 */
function aep_set_options() {
	foreach (AEP_OPTIONS as $option => $param) {
		if (get_option($option) === false) {
			add_option($option, $param['default']);
		}
	}
}
function aep_unset_options() {
	foreach (AEP_OPTIONS as $option => $param) {
		delete_option($option);
	}
}
add_action('admin_init', 'aep_init_options');
function aep_init_options() {
	add_settings_section(
		'aep_options_section',
		'AlesAgglo Empty Plugin',
		function () { echo '<p>'.__('Welcome on settings page.', AEP_SLUG).'</p>'; },
		'aep-settings'
	);

	foreach (AEP_OPTIONS as $option => $param) {
		register_setting('aep_options_group', $option);

		add_settings_field(
			$option,
			__($param['label'], AEP_SLUG),
			'display_plugin_option',
			'aep-settings',
			'aep_options_section',
			array(
				'name' => $option,
				'type' => $param['type'],
				'options' => $param['options'] ?? null,
				'attributes' => $param['attributes'] ?? null
			)
		);
	}
}


/**
 *	define menu
 */
add_action('admin_menu', 'aep_set_settings_page');
function aep_set_settings_page() {
	add_menu_page(
		'AlesAgglo Empty Plugin Menu',
		'AlesAgglo Empty Plugin',
		'manage_options',
		'aep-main-menu',
		'aep_display_settings_page',
		AEP_URL . 'assets/img/menu-icon.png'
	);
	add_submenu_page(
		'aep-main-menu',
		'AEP Settings',
		__('Settings', AEP_SLUG),
		'manage_options',
		'aep-settings',
		'aep_display_settings_page'
	);
	remove_submenu_page('aep-main-menu', 'aep-main-menu');
}
add_action('admin_head', 'aep_settings_icon_size');
function aep_settings_icon_size() {
	?><style>#toplevel_page_aep-main-menu .wp-menu-image img { width: 20px !important; height: 20px !important; }</style><?php
}


/**
 *	define and display settings page
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'aep_settings_link', 10, 4);
function aep_settings_link($actions, $file, $data, $context) {
	$actions['settings'] = sprintf(
		'<a href="%s">%s</a>',
		esc_url(menu_page_url('aep-settings', false)),
		esc_html__('Settings', AEP_SLUG)
	);
	return $actions;
}
function aep_display_settings_page() {
	if (is_admin() && current_user_can('manage_options')) {
		include AEP_PATH . 'template-parts/settings-page-admin.php';
	}
}


/**
 *	define / remove cron
 */
function aep_activate_cron() {
	if (!wp_next_scheduled('aep_cron_job')) {
		wp_schedule_event(time(), 'aep_cron_interval', 'aep_cron_job');
	}
}
function aep_deactivate_cron() {
	$timestamp = wp_next_scheduled('aep_cron_job');
	if ($timestamp) {
		wp_unschedule_event($timestamp, 'aep_cron_job');
	}
}
if (AEP_CRON) {
	add_filter('cron_schedules', 'aep_cron_schedules');
	function aep_cron_schedules($schedules) {
		$schedules['aep_cron_interval'] = array(
			'interval' => get_option('aep_cron_interval'),
			'display' => __('AEP Cron Interval', AEP_SLUG)
		);
		return $schedules;
	}

	add_action('aep_cron_job', 'aep_cron_job');
	function aep_cron_job() {
		if (get_transient('aep_cron_lock')) {
			error_log('AEP cron job already running');
			return;
		}
		set_transient('aep_cron_lock', true, get_option('aep_cron_interval')-1);

		if (AEP_DEBUG) error_log('AEP cron job running');
		set_transient('aep_data_transient', 'any_data', 'cache information');

		delete_transient('aep_cron_lock');
	}
}


/**
 *	define and display shortcode
 */
add_shortcode( 'alesagglo-empty-plugin', 'aep_shortcode' );
function aep_shortcode($atts, $content = null ) {
	extract(
		shortcode_atts(
			array( 'param' => null,
			),
			$atts
		)
	);
	ob_start();
	include('template-parts/shortcode.php');
	return ob_get_clean();
}


/**
 * ajax route
 */
add_action('wp_ajax_aep_ajax_action', 'aep_ajax_callback');
add_action('wp_ajax_nopriv_aep_ajax_action', 'aep_ajax_callback');
function aep_ajax_callback() {

	if (isset($_POST['query'])) {
		$query = sanitize_text_field($_POST['query']);

		if ($query == 'send value')
			$results = array('receive value');
		else
			$results = array($query);

		wp_reset_postdata();
		wp_send_json_success($results);
	} else {
		wp_send_json_error('Invalid request');
	}
}


/**
 * define cookie
 */
// add_action('init', 'aep_set_cookie');
function aep_set_cookie() {

	foreach (AEP_COOKIE as $name => $value) {
		if (!isset($_COOKIE[$name])) {
			setcookie($name, $value, time() + 24 * 60 * 60, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false);
			$_COOKIE[$name] = $value;
		}
	}
}

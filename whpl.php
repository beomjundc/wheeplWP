<?php

	/*
	Plugin Name: wheepl Widget
	Plugin URI: https://wheepl.com
	Author: wheepl
	Description: Widget for live conversations across blogs through hashtags.
	Version: 1.0.2
	Author URI: https://wheepl.com
	*/

	/*
	wheepl Widget
    Copyright (C) 2015  wheepl

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

// variable declarations
define('wheepl_version', '1.0.2');

if (get_option('whpl_admin') && get_option('whpl_siteRef')) {
	// add wheepl widget where the comments should be
	function whpl_widget () {
		return plugin_dir_path(__FILE__) . '/whpl-widget.php';
	}

	add_filter('comments_template', 'whpl_widget');

	// alter comments counter text
	function whpl_msgs_text ($comment_text) {
		global $post;

		return '<span>
			<a class="whpl-counter" data-whpl-post-url="'.get_permalink($post->ID).'" href="'.get_permalink($post->ID).'#whplContainer"></a>
			 messages on 
			<a class="whpl-phashtag" data-whpl-post-url="'.get_permalink($post->ID).'" href="'.get_permalink($post->ID).'#whplContainer"></a>
		</span>';
	}

	// replace comments text
	add_filter('comments_number', 'whpl_msgs_text');

	// add ajax js file to communicate with wheepl api server
	function whpl_retrieve_counter ($post) {
		wp_enqueue_script('whpl-conf-script', plugins_url('/js/whpl-conf.js', __FILE__));
		wp_enqueue_script('whpl-counter-script', plugins_url('/js/whpl-counter.js', __FILE__), array('jquery', 'whpl-conf-script')); // whpl-counter.js has dependency on whpl-conf.js

		// pass parameters to the script
		$params = array('ajaxUrl' => admin_url('admin-counter.php'), 'siteRef' => get_option('whpl_siteRef'));
		$params = array('l10n_print_after' => 'ajax_object = ' . json_encode($params) . ';');

		wp_localize_script('whpl-counter-script', 'ajax_object', $params);
	}

	add_action('wp_enqueue_scripts', 'whpl_retrieve_counter');
}

// add wheepl options page to admin management
if (true == is_admin() && get_option('whpl_admin') && get_option('whpl_siteRef')) {
	function whpl_admin_options () {
		include('whpl-options.php');
	}
	 
	function whpl_admin_actions () {
		$GLOBALS['whpl_admin_registration'] = add_options_page('wheepl', 'wheepl', 'manage_options', 'wheepl-admin-options', 'whpl_admin_options');
	}

	add_action('admin_menu', 'whpl_admin_actions');
}
else {
	function whpl_admin_form () {
		include('whpl-admin.php');
	}
	 
	function whpl_admin_actions () {
		$GLOBALS['whpl_admin_registration'] = add_options_page('wheepl', 'wheepl', 'manage_options', 'wheepl-admin-form', 'whpl_admin_form');
	}

	add_action('admin_menu', 'whpl_admin_actions');

	// add ajax js file to communicate with wheepl api server
	function whpl_admin_init ($hook) {
		if ( $GLOBALS['whpl_admin_registration'] != $hook ) {
			return;
		}
		
		wp_enqueue_script('whpl-conf-script', plugins_url('/js/whpl-conf.js', __FILE__));
		wp_enqueue_script('whpl-admin-script', plugins_url('/js/whpl-admin.js', __FILE__), array('jquery', 'whpl-conf-script')); // whpl-admin.js has dependency on whpl-conf.js

		// pass parameters to the script
		$params = array('ajaxUrl' => admin_url('admin-ajax.php'));
		$params = array('l10n_print_after' => 'ajax_object = ' . json_encode($params) . ';');

		wp_localize_script('whpl-admin-script', 'ajax_object', $params);
	}

	add_action('admin_enqueue_scripts', 'whpl_admin_init');

	function whpl_post_admin_callback () {
		update_option('whpl_ver', strtolower(wheepl_version));

		$username = sanitize_text_field($_POST['username']);

		if ( preg_match("/^[a-zA-Z0-9_]{1,15}$/", $username) ) // regex validation on username
			update_option('whpl_admin', $username);

		$siteRef = sanitize_text_field($_POST['siteRef']);

		if ( preg_match("/^[a-z0-9]{1,50}$/", $siteRef) ) // regex validation on siteRef
			update_option('whpl_siteRef', strtolower($siteRef));

		// wp_die();
		exit();
	}

	add_action('wp_ajax_whpl_post_admin', 'whpl_post_admin_callback'); // call when user logged in
	add_action('wp_ajax_nopriv_whpl_post_admin', 'whpl_post_admin_callback');
}

?>
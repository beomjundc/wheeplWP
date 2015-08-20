<?php

	/*
	Plugin Name: wheepl widget
	Plugin URI: https://whee.pl
	Author: wheepl
	Description: Widget for live conversations across blogs through hashtags.
	Version: 1.0.3
	Author URI: https://wheepl.com
	*/

	/*
	wheepl widget
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
define('wheepl_version', '1.0.3');

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

	update_option('whpl_ver', strtolower(wheepl_version));
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

// custom meta box for hashtags
$prefix = 'whpl_';

$meta_box = array(
	'id' => 'whpl-meta-box',
	'title' => 'wheepl Hashtags',
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => 'Primary Hashtag',
			'desc' => 'Enter the primary hashtag to tag the post with.',
			'id' => $prefix . 'pHashtag',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Secondary Hashtag',
			'desc' => 'Enter the secondary hashtag to tag the post with.',
			'id' => $prefix . 'sHashtag',
			'type' => 'text',
			'std' => ''
		)
	)
);

add_action('admin_menu', 'whpl_add_box');

// add custom meta box
function whpl_add_box () {
	global $meta_box;
	add_meta_box($meta_box['id'], $meta_box['title'], 'whpl_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

// callback function to show fields in meta box
function whpl_show_box () {
	global $meta_box, $post;

	// if post status is published, override the published flag
	get_post_status($post->ID) == 'publish' ? $published = true : $published = get_post_meta($post->ID, 'whpl_published', true);

	// use nonce for verification
	echo '<input type="hidden" name="whpl_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
	
	foreach ($meta_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		$invalid = get_post_meta($post->ID, $field['id'] . '_invalid', true);

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		
		switch ($field['type']) {
			case 'text':
				if ($published) {
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" disabled />', '<br />', $field['desc'];
				}
				else {
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
				
					if ($invalid) {
						echo '<p style="color: red">your hashtag input is invalid.</p>';
					}
				}
				break;
			case 'textarea':
				echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>';
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				break;
		}

		echo	'</td><td>',
			'</td></tr>';
	}

	echo '</table>';
}

add_action('save_post', 'whpl_save_hashtags');

// save data from meta box
function whpl_save_hashtags ($post_id) {
	global $meta_box;

	// verify nonce
	if (isset($_POST['whpl_meta_box_nonce']) && !wp_verify_nonce($_POST['whpl_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check permissions
	if (isset($_POST['post_type']) && $_POST['post_type'] == 'page') {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	}
	elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	
	foreach ($meta_box['fields'] as $field) {
		if (isset($_POST[$field['id']])) {
			$new = sanitize_text_field($_POST[$field['id']]);

			if (preg_match("/^(?=.{1,50}$)(#|\x{ff03}){1}([0-9_\p{L}]*[_\p{L}][0-9_\p{L}]*)$/u", $new) || $new == '') {
				// set post meta to flag valid hashtag
				update_post_meta($post_id, $field['id'] . '_invalid', false);

				update_post_meta($post_id, $field['id'], $new);

				if (!isset($prevent_publish)) {
					$prevent_publish = false;
				}
			}
			else {
				// set post meta to flag invalid hashtag
				update_post_meta($post_id, $field['id'] . '_invalid', true);

				update_post_meta($post_id, $field['id'], $new);

				$prevent_publish = true; // input is invalid so prevent publish
			}
		}
	}

	if (isset($prevent_publish)) {
		if ($prevent_publish) {
			// unhook this function to prevent indefinite loop
			remove_action('save_post', 'whpl_save_hashtags');

			// update the post to change post status
			wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));

			// re-hook this function again
			add_action('save_post', 'whpl_save_hashtags');
		}
		else {
			update_post_meta($post_id, 'whpl_published', true);
		}
	}
}

// display proper message on post redirect
add_filter('redirect_post_location', 'whpl_redirect_location', 10, 2);

function whpl_redirect_location($location, $post_id){
	// if post was published
	if (isset($_POST['publish'])) {
		// obtain current post status
		$status = get_post_status( $post_id );

		// the post was 'published', but if it is still a draft, display draft message (10).
		if ($status == 'draft')
			$location = add_query_arg('message', 10, $location);
	}

	return $location;
}

?>
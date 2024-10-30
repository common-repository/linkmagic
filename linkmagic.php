<?php
/*
Plugin Name: LinkMagic
Plugin URI:  http://linkmagic.co/documentation/wordpress-plugin
Description: Discover text in your content that can be monetized by linking it to Amazon products
Version:     1.0.4
Author:      LinkMagic
Author URI:  http://linkmagic.co
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

LinkMagic is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

LinkMagic is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with LinkMagic. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/* Add codes to footer */

add_action( 'wp_footer', 'linkmagic_footer_code', 1000 );

function linkmagic_footer_code() {
	global $post;

	// Load only in blog posts
	if ( is_single( $post->ID ) )
	{
		echo '<script>LinkMagic.init({api:\'' . get_option( 'linkmagic_code' ) . '\'});</script>';
	}
}

add_action( 'wp_enqueue_scripts', 'linkmagic_scripts' );

function linkmagic_scripts() {
	// Load only in blog posts
	if ( is_single( $post->ID ) )
	{
		wp_enqueue_script( 'linkmagic-script', '//linkmagic.co/lm.js', array(), '1.0.4', true );
	}
}




/* Admin page */

add_action( 'admin_menu', 'linkmagic_admin_menu' );

function linkmagic_admin_menu() {
	add_options_page( 'LinkMagic', 'LinkMagic', 'manage_options', 'linkmagic', 'linkmagic_page' );
}

function linkmagic_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '
		<div class="wrap">
			<h1>LinkMagic</h1>

			<h3 class="title">Enter your API key</h3>

			<p><label for="linkmagic_code">Enter your API key below, if you don\'t have one, <strong><a href="http://linkmagic.co" target="_blank">create an account at LinkMagic.co</a></strong> to generate one for free.</label></p>

			<form action="' . admin_url('admin.php') . '" method="post">
				<input type="hidden" name="action" value="linkmagic_save">

				<input type="text" id="linkmagic_code" name="linkmagic_code" class="large-text code" value="' . stripcslashes( get_option( 'linkmagic_code' ) ) . '" style="	width: 20em;">

				<p><small>To disable LinkMagic, leave the textbox empty and click <i>Save Changes</i>.</small></p>

				<p>If you have a Cache plugin active make sure to refresh the cache after changing/deleting the code.</p>

				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>

			</form>

			<h3 class="title">Documentation</h3>

			<ul>
				<li><a href="http://linkmagic.co/documentation" target="_blank">Documentation</a></li>
				<li><a href="http://linkmagic.co/faq" target="_blank">Frequently Asked Questions</a></li>
				<li><a href="http://linkmagic.co/dashboard/settings" target="_blank">Upgrade your plan</a></li>
			</ul>

		</div>
	';
}






/* Form submitted */

add_action( 'admin_action_linkmagic_save', 'linkmagic_save_admin_action' );

function linkmagic_save_admin_action() {

	/* If code updated, save it */
	if ( isset( $_POST[ 'linkmagic_code' ] ) )
	{
		if ( get_option( 'linkmagic_code' ) )
		{
			update_option( 'linkmagic_code', linkmagic_clean_code( $_POST[ 'linkmagic_code' ] ) );
		}
		else
		{
			add_option( 'linkmagic_code', $_POST[ 'linkmagic_code' ] );
		}
	}

	wp_redirect( admin_url( 'options-general.php?page=linkmagic' ) );
}

function linkmagic_clean_code( $code )
{
	return sanitize_text_field( preg_replace("/[^a-zA-Z0-9]+/", "", trim( $code ) ) );
}




/* Add settings link on plugin page */

function linkmagic_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=linkmagic.php">Settings</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'linkmagic_settings_link' );


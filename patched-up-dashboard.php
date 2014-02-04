<?php

/* Plugin Name: Patched Up Dashboard
 * Plugin URI: http://patchedupcreative.com/plugins/dashboard
 * Description: A plugin to easily customize your WordPress Dashboard
 * Version: 0.1.3
 * Date: 01-24-14
 * Author: Casey Patrick Driscoll
 * Author URI: http://caseypatrickdriscoll.com
 *
 *
 * Copyright:
 *   Copyright 2014 Casey Patrick Driscoll (email : caseypatrickdriscoll@me.com)
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License, version 2, as
 *   published by the Free Software Foundation.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( 'class-patched-up-dashboard-options.php' );

new Patched_Up_Dashboard_Options();

/* https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts */
function patched_up_dashboard_styles($hook) {
    if( 'index.php' != $hook )
        return;
	
		echo '<style>
						#wpwrap {
							background: url("' . patched_up_dashboard_option( 'dashboard_background_image' ) . '") center;
						}
						' . patched_up_dashboard_option( 'dashboard_custom_css' ) . '
					</style>';
}
add_action( 'admin_enqueue_scripts', 'patched_up_dashboard_styles' );

function patched_up_dashboard_option( $option ) {
	$options = get_option( 'patched_up_dashboard_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}

?>

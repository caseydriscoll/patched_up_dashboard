<?php

/* Plugin Name: Patched Up Dashboard
 * Plugin URI: http://patchedupcreative.com/plugins/dashboard
 * Description: A plugin to easily customize your WordPress Dashboard
 * Version: 0.0.2
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

/* http://codex.wordpress.org/Administration_Menus */
add_action( 'admin_menu', 'patched_up_dashboard_menu' );

function patched_up_dashboard_menu() {
	add_dashboard_page(	'Edit Dashboard', 
											'Edit Dashboard', 
											'manage_options', 
											'edit-dashboard', 
											'patched_up_dashboard_options' );
}

function patched_up_dashboard_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h2>Edit Dashboard</h2>';
	echo '</div>';
}

?>

<?php

	class Patched_Up_Dashboard_Options {
		
		private $sections;

		function __construct() {

			$this->checkboxes = array();
			$this->settings = array();
			$this->get_settings();
			
			$this->sections['general'] = __( 'General Settings' );

			add_action( 'admin_menu', array( &$this, 'add_pages' ) );
			add_action( 'admin_init', array( &$this, 'register_settings' ) );

			if ( ! get_option( 'patched_up_dashboard_options' ) )
				$this->initialize_settings();
		}

		/* http://codex.wordpress.org/Administration_Menus */
		public function add_pages() {
			add_dashboard_page(	'Edit Dashboard', 
													'Edit Dashboard', 
													'manage_options', 
													'patched-up-dashboard-options', 
													array( &$this, 'display_page' )
			);		
		}

		public function patched_up_dashboard_options_page() {

		}

		public function display_page() {
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}

			wp_enqueue_script( 'custom_wp_admin_script', plugin_dir_url(__FILE__) . '/js/script.js');
			wp_enqueue_media();

			echo '<div class="wrap">
							<h2>' . __( 'Edit Dashboard' ) . '</h2>
							<form action="options.php" method="post">';

			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
      	echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';

			settings_fields( 'patched_up_dashboard_options' );
			do_settings_sections( $_GET['page'] );

			submit_button();
	
			echo 		'</form>';
		}

		public function display_section() {

		}

		public function display_setting( $args = array() ) {
			extract( $args );

			$options = get_option( 'patched_up_dashboard_options' );

			if ( ! isset( $options[$id] ) && $type != 'checkbox' )
				$options[$id] = $std;
			elseif ( ! isset( $options[$id] ) )
				$options[$id] = 0;

			$field_class = '';
			if ( $class != '' )
				$field_class = ' class="' . $class . '"';

			switch ( $type ) {

				case 'image':
					echo '<div class="uploader"> 
									<input type="text"
												 name="patched_up_dashboard_options[' . $id . ']" 
												 id="' . $id . '" 
												 placeholder="' . $std . '" 
											 	 value="' . esc_attr( $options[$id] ) . '" />
									<input type="button" id="background_image_button" class="upload" value="Upload" /> 
								</div>';
					
					if ( $options[$id] )
						echo '<img src="' . esc_attr( $options[$id] ) . '" width="400px" />';

					break;
	
				case 'textarea':
					echo '<textarea class="' . $field_class . '" 
													id="' . $id . '" 
													name="patched_up_dashboard_options[' . $id . ']" 
													placeholder="' . $std . '" 
													rows="5" cols="30">' . 
									wp_htmledit_pre( $options[$id] ) . 
							 '</textarea>';

					if ( $desc != '' )
						echo '<br /><span class="description">' . $desc . '</span>';

					break;

				case 'text':
				default:
					echo '<input class="regular-text' . $field_class . '" 
											 type="text" 
											 id="' . $id . '" 
											 name="patched_up_dashboard_options[' . $id . ']" 
											 placeholder="' . $std . '" 
											 value="' . esc_attr( $options[$id] ) . '" />';

		 			if ( $desc != '' )
		 				echo '<br /><span class="description">' . $desc . '</span>';

		 			break;
			}
		}

		public function create_setting( $args = array() ) {
			
			$defaults = array(
				'id'			=> 'default_field',
				'title'		=> 'Default Field',
				'desc'		=> 'This is a default description',
				'std'			=> '',
				'type'		=> 'text',
				'section'	=> 'general',
				'choices'	=> array(),
				'class'		=> ''
			);

			extract( wp_parse_args( $args, $defaults ) ); 

			$field_args = array(
				'type'			=> $type,
				'id'				=> $id,
				'desc'			=> $desc,
				'std'				=> $std,
				'label_for'	=> $id,
				'class'			=> $class
			);

			if ( $type == 'checkbox' )
				$this->checkboxes[] = $id;

			add_settings_field( $id, 
													$title, 
													array( $this, 'display_setting' ), 
													'patched-up-dashboard-options', 
													$section, 
													$field_args 
			);
		}

		public function get_settings() {

			$this->settings['background_image'] = array(
				'section'	=> 'general',
				'title'   => __( 'Background Image' ),
				'desc'    => __( 'This is the background image.' ),
				'std'     => '',
				'type'    => 'image',
			);
				
			$this->settings['header_logo'] = array(
				'section'	=> 'general',
				'title'   => __( 'Header Logo' ),
				'desc'    => __( 'Enter the URL to your logo for the theme header.' ),
				'type'    => 'text',
				'std'     => ''
			);
				
			$this->settings['custom_css'] = array(
				'section'	=> 'general',
				'title'   => __( 'Custom Styles' ),
				'desc'    => __( 'Enter any custom CSS here to apply it to your theme.' ),
				'std'     => '',
				'type'    => 'textarea',
				'class'   => 'code'
			);

		}

		public function initialize_settings() {
			$default_settings = array();
			
			foreach ( $this->settings as $id => $setting ) {
				if ( $setting['type'] != 'heading' )
					$default_settings[$id] = $setting['std'];
			}

			update_option( 'patched_up_dashboard_options', $default_settings );
		}

		public function register_settings() {
		
			register_setting( 'patched_up_dashboard_options',
												'patched_up_dashboard_options'
			); 

			foreach ( $this->sections as $slug => $title )
				add_settings_section( $slug,
															$title,
															array( &$this, 'display_section' ),
															'patched-up-dashboard-options'
														);

			$this->get_settings();

			foreach ( $this->settings as $id => $setting ) {
				$setting['id'] = $id;
				$this->create_setting( $setting );
			}

		}

	}
?>

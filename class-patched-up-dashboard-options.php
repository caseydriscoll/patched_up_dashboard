<?php

	class Patched_Up_Dashboard_Options {
		
		private $sections;

		function __construct() {

			$this->checkboxes = array();
			$this->settings = array();
			$this->get_settings();
			
			$this->sections['dashboard'] 	= __( 'Dashboard Settings' );
			$this->sections['navigation'] = __( 'Navigation Settings' );
			$this->sections['login'] 			= __( 'Login Settings' );

			add_action( 'admin_menu', array( &$this, 'add_pages' ) );
			add_action( 'admin_init', array( &$this, 'register_settings' ) );

			if ( ! get_option( 'patched_up_dashboard_options' ) )
				$this->initialize_settings();
		}

		/* http://codex.wordpress.org/Administration_Menus */
		public function add_pages() {
			$admin_page = add_dashboard_page(	'Edit Dashboard', 
																				'Edit Dashboard', 
																				'manage_options', 
																				'patched-up-dashboard-options', 
																				array( &$this, 'patched_up_dashboard_options_page' )
			);		
			add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		}
		public function scripts() {
			wp_print_scripts( 'jquery-ui-tabs' );
		}

		public function patched_up_dashboard_options_page() {
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}

			wp_enqueue_script( 'custom_wp_admin_script', 
												 plugin_dir_url(__FILE__) . '/js/script.js', 
												 array( 'wp-color-picker' )
			);
			wp_enqueue_media();
		
			wp_register_style( 'custom_wp_admin_css', plugin_dir_url(__FILE__) . '/style.css', false, '1.0.0' );
    	wp_enqueue_style( 'custom_wp_admin_css' );

			wp_enqueue_style( 'wp-color-picker' );


			echo '<div class="wrap patched-up-form">
							<form action="options.php" method="post" id="edit-dashboard">
							<h2>' . __( 'Edit Dashboard' ) . '</h2>';
							
			submit_button('Save Changes', 'primary top', 'submit', false);

			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
      	echo 		'<div class="updated fade">
									<p>' . __( 'Dashboard updated. ' ) . '
										<a href="/wp-admin">' . __( 'See for yourself.' ) . '</a>
									</p>
								</div>';


			settings_fields( 'patched_up_dashboard_options' );

			echo			'<div class="ui-tabs">
										<ul class="ui-tabs-nav">';

			foreach ( $this->sections as $section )
				echo '<li><a href="#' . strtolower( str_replace( ' ', '_', $section ) ) . '">' . $section . '</a></li>';

			echo 					'</ul>';
			do_settings_sections( $_GET['page'] );

			echo 			'</div>'; // close ui-tabs

			submit_button();
	
			echo 		'</form>
							 <script type="text/javascript">
									jQuery(document).ready(function($) {
										var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
										wrapped.each(function() {
											$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));	
										});

										$(".ui-tabs-panel").each(function(index) {
											var str = $(this).children("h3").text().replace(/\s/g, "_");
											$(this).attr("id", str.toLowerCase());
											if (index > 0)
												$(this).addClass("ui-tabs-hide");
										});

										$(".ui-tabs").tabs({ fx: { opacity: "toggle", duration: "fast" } });

										$("input[type=text], textarea").each(function() {
											if ($(this).val() == $(this).attr("placeholder"))
												$(this).css("color", "#999");
										});

										$("input[type=text], textarea").focus(function() {
											if ($(this).val() == $(this).attr("placeholder")) {
												$(this).val("");
												$(this).css("color", "#000");
											}
										}).blur(function() {
											if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
												$(this).val($(this).attr("placeholder"));
												$(this).css("color", "#999");
											}
										});

										$(".wrap h3, .wrap table").show();

										if ($.browser.mozilla) 
											$("form").attr("autocomplete", "off");
									});
							 </script>';

			echo '</div>'; // wrap

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

			switch ( $type ) {

				case 'image':
					echo '<div class="uploader"> 
									<input type="text"
												 name="patched_up_dashboard_options[' . $id . ']" 
												 id="' . $id . '" 
												 placeholder="' . $std . '" 
											 	 value="' . esc_attr( $options[$id] ) . '" />
									<input type="hidden" 
												 name="patched_up_dashboard_options[' . $id . '_attachment_id]"
												 value="' . esc_attr( $options[$id . '_attachment_id']) . '"
												 id="' . $id . '_attachment_id" />
									<input type="button" id="' . $id . '_button" class="button upload" value="Upload" /> 
								</div>';
					if ( $desc != '' )
						echo '<p class="description">' . $desc . '</p>';
					
					if ( $options[$id] ) {
						$attachment_id = $id . "_attachment_id";
						$attachment_preview_attr = array(
							'id'		=> $id . "_preview",
							'class' => "preview",
						);
						echo wp_get_attachment_image( $options[$attachment_id], array(400, 225), 0, $attachment_preview_attr );
					} else {
						echo '<img id="' . $id . '_preview" class="preview" />';
					}
	

					break;
	
				case 'textarea':
					echo '<textarea class="' . $class . '" 
													id="' . $id . '" 
													name="patched_up_dashboard_options[' . $id . ']" 
													placeholder="' . $std . '">' . 
									wp_htmledit_pre( $options[$id] ) . 
							 '</textarea>';

					if ( $desc != '' )
						echo '<br /><span class="description">' . $desc . '</span>';

					break;

				case 'attachment':
					echo '';
					break;

				case 'color':
					echo '<input class="color-text ' . $class . '" 
											 type="text" 
											 id="' . $id . '" 
											 name="patched_up_dashboard_options[' . $id . ']" 
											 placeholder="' . $std . '" 
											 value="' . esc_attr( $options[$id] ) . '" />';

		 			if ( $desc != '' )
		 				echo '<br /><span class="description">' . $desc . '</span>';

		 			break;

				case 'select':
					echo '<select class="select' . $class . '" 
												name="patched_up_dashboard_options[' . $id . ']">';

					foreach ( $choices as $value => $label )
						echo '<option value="' . esc_attr( $value ) . '"' . 
													selected( $options[$id], $value, false ) . '>' . 
														$label . 
								 '</option>';

					echo '</select>';

					if ( $desc != '' )
						echo '<br /><span class="description">' . $desc . '</span>';

					break;

				case 'text':
				default:
					echo '<input class="regular-text ' . $class . '" 
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
				'section'	=> 'dashboard',
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
				'choices'	 	=> $choices,
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

			/* Dashboard Settings
			==============================*/

			$this->settings['dashboard_background_image'] = array(
				'section'	=> 'dashboard',
				'title'   => __( 'Background Image' ),
				'desc'    => __( 'This is the dashboard background image.' ),
				'std'     => '',
				'type'    => 'image',
			);

			$this->settings['dashboard_background_image_attachment_id'] = array(
				'section' => 'dashboard',
				'title'		=> '',
				'desc'		=> '',
				'std'			=> '',
				'type'		=> 'attachment',
			);

			$this->settings['dashboard_background_color'] = array(
				'section' => 'dashboard',
				'title'		=> __( 'Background Color' ),
				'desc'		=> __( 'This is the dashboard background color' ),
				'std'			=> '',
				'type'		=> 'color',
			);
	
			$this->settings['dashboard_background_repeat'] = array(
				'section' => 'dashboard',
				'title'   => __( 'Background Repeat' ),
				'desc'		=> __( 'This is the dashboard background repeat' ),
				'type'    => 'select',
				'std'     => '(Repeat)',
				'choices' => array(
					'' 					=> '(Repeat)',
					'no-repeat' => 'No Repeat',
					'repeat-y' 	=> 'Repeat Y',
					'repeat-x' 	=> 'Repeat X',
				)
			);
		
			$this->settings['dashboard_background_position'] = array(
				'section' => 'dashboard',
				'title'   => __( 'Background Position' ),
				'desc'		=> __( 'This is the dashboard background position' ),
				'type'    => 'select',
				'std'     => '(None)',
				'choices' => array(
					'' 								=> '(None)',
					'left top' 				=> 'Left Top',
					'left center' 		=> 'Left Center',
					'left bottom' 		=> 'Left Bottom',
					'center top' 			=> 'Center Top',
					'center center' 	=> 'Center Center',
					'center bottom' 	=> 'Center Bottom',
					'right top' 			=> 'Right Top',
					'right center' 		=> 'Right Center',
					'right bottom' 		=> 'Right Bottom',
				)
			);
						
			$this->settings['dashboard_custom_css'] = array(
				'section'	=> 'dashboard',
				'title'   => __( 'Custom Styles' ),
				'desc'    => __( 'Enter any custom CSS here to apply it to your dashboard.' ),
				'std'     => '',
				'type'    => 'textarea',
				'class'   => 'code'
			);


			/* Navigation Settings
			==============================*/

			$this->settings['header_logo'] = array(
				'section'	=> 'navigation',
				'title'   => __( 'Header Logo' ),
				'desc'    => __( 'Enter the URL to your logo for the navigation bar.' ),
				'type'    => 'image',
				'std'     => ''
			);

			/* Login Settings
			==============================*/

			$this->settings['login_logo'] = array(
				'section'	=> 'login',
				'title'   => __( 'Login Logo' ),
				'desc'    => __( 'Enter the URL to your logo for the login page.' ),
				'type'    => 'image',
				'std'     => ''
			);

			$this->settings['login_background_image'] = array(
				'section'	=> 'login',
				'title'   => __( 'Background Image' ),
				'desc'    => __( 'This is the login background image.' ),
				'type'    => 'image',
				'std'     => ''
			);				

			$this->settings['login_background_color'] = array(
				'section'	=> 'login',
				'title'   => __( 'Background Color' ),
				'desc'    => __( 'This is the login background color.' ),
				'type'    => 'color',
				'std'     => ''
			);				

			$this->settings['login_custom_css'] = array(
				'section'	=> 'login',
				'title'   => __( 'Custom Styles' ),
				'desc'    => __( 'Enter any custom CSS here to apply it to your login page.' ),
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

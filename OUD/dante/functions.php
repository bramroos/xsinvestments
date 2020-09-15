<?php
	
	/*
	*
	*	Dante Functions
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2019 - http://www.swiftideas.net
	*
	*/
	
	
	/* VARIABLE DEFINITIONS
	================================================== */ 
	define('DANTE_TEMPLATE_PATH', get_template_directory());
	define('DANTE_INCLUDES_PATH', DANTE_TEMPLATE_PATH . '/includes');
	define('DANTE_FRAMEWORK_PATH', DANTE_TEMPLATE_PATH . '/swift-framework');
	define('DANTE_WIDGETS_PATH', DANTE_INCLUDES_PATH . '/widgets');
	define('DANTE_LOCAL_PATH', get_template_directory_uri());
		

	/* DANTE FRAMEWORK CHECK
    ================================================== */
    if ( ! function_exists( 'dante_framework_check' ) ) {
        function dante_framework_check() {

        	if ( defined( 'DANTE_FRAMEWORK_VERSION' ) || !( current_user_can('editor') || current_user_can('administrator') ) ) {
        		return;
        	}

            $class = 'notice notice-error';
            $message = __( 'Please install/activate the Dante Framework plugin. If you have not installed the plugin, please go to Appearance > Install Plugins.', 'dante' );

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
        }
        add_action( 'admin_notices', 'dante_framework_check' );
    }

    /* META BOX CHECK
    ================================================== */
    if ( ! function_exists( 'dante_metabox_check' ) ) {
        function dante_metabox_check() {

        	if ( class_exists( 'RW_Meta_Box' ) || !( current_user_can('editor') || current_user_can('administrator') ) ) {
        		return;
        	}

            $class = 'notice notice-error';
            $message = __( 'Please install/activate the Meta Box plugin. If you have not installed the plugin, please go to Appearance > Install Plugins.', 'dante' );

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
        }
        add_action( 'admin_notices', 'dante_metabox_check' );
    }
	
	/* CHECK WPML IS ACTIVE
	================================================== */ 
	if ( ! function_exists( 'dante_wpml_activated' ) ) {
		function dante_wpml_activated() {
			if ( function_exists('icl_object_id') ) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/* PLUGIN INCLUDES
	================================================== */
	$options = get_option('sf_dante_options');
	$disable_loveit = false;
	if (isset($options['disable_loveit']) && $options['disable_loveit'] == 1) {
	$disable_loveit = true;
	}
	require_once(DANTE_INCLUDES_PATH . '/plugins/aq_resizer.php');
	include_once(DANTE_INCLUDES_PATH . '/plugin-includes.php');
	
	// Love it
	if (!$disable_loveit) {
	include_once(DANTE_INCLUDES_PATH . '/plugins/love-it-pro/love-it-pro.php');
	}
	
	// Revslider
	if ( function_exists('set_revslider_as_theme') ) {
		set_revslider_as_theme();
	}
	
	
	/* THEME SETUP
	================================================== */
	if (!function_exists('dante_theme_setup')) {
		function dante_theme_setup() { 	
		
			/* THEME SUPPORT
			================================================== */  			
			add_theme_support( 'structured-post-formats', array('audio', 'gallery', 'image', 'link', 'video') );
			add_theme_support( 'post-formats', array('aside', 'chat', 'quote', 'status') );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'title-tag' );
			add_theme_support( 'custom-logo' );
			add_theme_support( 'custom-background' );
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
			
			/* THUMBNAIL SIZES
			================================================== */  	
			set_post_thumbnail_size( 220, 150, true);
			add_image_size( 'widget-image', 94, 70, true);
			add_image_size( 'thumb-square', 250, 250, true);
			add_image_size( 'thumb-image', 600, 450, true);
			add_image_size( 'thumb-image-twocol', 900, 675, true);
			add_image_size( 'thumb-image-onecol', 1800, 1200, true);
			add_image_size( 'blog-image', 1280, 9999);
			add_image_size( 'full-width-image-gallery', 1280, 720, true);
			
			
			/* CONTENT WIDTH
			================================================== */
			if ( ! isset( $content_width ) ) $content_width = 1140;
			
			
			/* LOAD THEME LANGUAGE
			================================================== */
			load_theme_textdomain('dante', DANTE_TEMPLATE_PATH.'/language');


			/* MIGRATION
			================================================== */
			dante_migrate_old_theme_data();
			
		}
		add_action( 'after_setup_theme', 'dante_theme_setup' );
	}
	

	/* MIGRATION
	================================================== */
	function dante_migrate_old_theme_data() {
		$options = get_option('sf_dante_options');

		// LOGO
		if (isset($options['logo_upload']) && !has_custom_logo()) {
			
			// get logo data
			$logo = $options['logo_upload'];
			$id = attachment_url_to_postid($logo);
			if ( $id != '' ) {
				// save to customizer
				set_theme_mod( 'custom_logo', $id );
			}

		}
	}


	/* SWIFT FRAMEWORK
	================================================== */ 
	if (!function_exists('dante_include_framework')) {
		function dante_include_framework() {
			require_once(DANTE_INCLUDES_PATH . '/sf-theme-functions.php');
			require_once(DANTE_FRAMEWORK_PATH . '/swift-framework.php');
		}
		add_action('init', 'dante_include_framework', 0);
	}
	
	
	/* THEME OPTIONS FRAMEWORK
	================================================== */  
	if (!function_exists('dante_include_theme_options')) {
		function dante_include_theme_options() {
			require_once(DANTE_FRAMEWORK_PATH . '/sf-options.php');
		}
		add_action('after_setup_theme', 'dante_include_theme_options', 10);
	}
	
	
	/* LOAD STYLESHEETS
	================================================== */
	if (!function_exists('dante_enqueue_styles')) {
		function dante_enqueue_styles() {  
			
			$options = get_option('sf_dante_options');
			$enable_responsive = $options['enable_responsive'];

			$body_font_option = $options['body_font_option'];
			$headings_font_option = $options['headings_font_option'];
			$menu_font_option = $options['menu_font_option'];

			if (($body_font_option == "google") || ($headings_font_option == "google") || ($menu_font_option == "google")) {
				wp_enqueue_style( 'dante-google-fonts', dante_google_fonts_url(), array(), null );
			}
		
		    wp_enqueue_style('bootstrap', DANTE_LOCAL_PATH . '/css/bootstrap.min.css', array(), NULL, 'all');
	    	wp_enqueue_style('font-awesome-v5', DANTE_LOCAL_PATH .'/css/font-awesome.min.css', array(), '5.10.1', 'all');
	    	wp_enqueue_style('font-awesome-v4shims', DANTE_LOCAL_PATH .'/css/v4-shims.min.css', array(), NULL, 'all');
		    wp_enqueue_style('ssgizmo', DANTE_LOCAL_PATH .'/css/ss-gizmo.css', array(), NULL, 'all');
		    wp_enqueue_style('sf-main', get_stylesheet_directory_uri() . '/style.css', array(), NULL, 'all'); 
		    wp_register_style('sf-responsive', DANTE_LOCAL_PATH . '/css/responsive.css', array(), NULL, 'all');
		    
		    if ($enable_responsive) {
		    	wp_enqueue_style('sf-responsive');  
		    }
		
		}		
		add_action('wp_enqueue_scripts', 'dante_enqueue_styles', 99);  
	}
	
	
	/* LOAD FRONTEND SCRIPTS
	================================================== */
	if (!function_exists('dante_enqueue_scripts')) {
		function dante_enqueue_scripts() {
			
			$options = get_option('sf_dante_options');
			$enable_min_scripts = false;
			if (isset($options['enable_min_scripts'])) {	
			$enable_min_scripts = $options['enable_min_scripts'];
			}
			$lightbox_enabled 		  = true;
			if ( isset($options['lightbox_enabled']) ) {
			$lightbox_enabled     	  = $options['lightbox_enabled'];
			}
			$gmaps_api_key = "";
			if (isset($options['gmaps_api_key'])) {
				$gmaps_api_key = $options['gmaps_api_key'];
			}
			$post_type = get_query_var('post_type');
			

    		wp_enqueue_script('jquery');
    		wp_enqueue_script('modernizr', DANTE_LOCAL_PATH . '/js/lib/modernizr.js', array(), NULL, TRUE);
    		wp_enqueue_script('bootstrap', DANTE_LOCAL_PATH . '/js/lib/bootstrap.min.js', array('jquery'), NULL, TRUE);
    	    wp_enqueue_script('jquery-ui', DANTE_LOCAL_PATH . '/js/lib/jquery-ui-1.10.2.custom.min.js', array('jquery'), NULL, TRUE);
    	    wp_enqueue_script('flexslider', DANTE_LOCAL_PATH . '/js/lib/jquery.flexslider-min.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('easing', DANTE_LOCAL_PATH . '/js/lib/jquery.easing.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('owlcarousel', DANTE_LOCAL_PATH . '/js/lib/owl.carousel.min.js', array('jquery'), NULL, TRUE); 
    		wp_enqueue_script('dcmegamenu', DANTE_LOCAL_PATH . '/js/lib/dcmegamenu.js', array('jquery'), NULL, TRUE); 
    		wp_enqueue_script('masonry', DANTE_LOCAL_PATH . '/js/lib/masonry.js', array(), NULL, TRUE); 
    	    wp_enqueue_script('jquery-appear', DANTE_LOCAL_PATH . '/js/lib/jquery.appear.js', array('jquery'), NULL, TRUE); 
    	    wp_enqueue_script('jquery-animatenumber', DANTE_LOCAL_PATH . '/js/lib/jquery.animatenumber.js', array('jquery'), NULL, TRUE); 
    	    wp_enqueue_script('jquery-animOnScroll', DANTE_LOCAL_PATH . '/js/lib/jquery.animOnScroll.js', array('jquery'), NULL, TRUE); 
    	    wp_enqueue_script('jquery-classie', DANTE_LOCAL_PATH . '/js/lib/jquery.classie.js', array('jquery'), NULL, TRUE); 
    	    wp_enqueue_script('jquery-countdown', DANTE_LOCAL_PATH . '/js/lib/jquery.countdown.min.js', array('jquery'), NULL, TRUE); 
    		wp_enqueue_script('jquery-countTo', DANTE_LOCAL_PATH . '/js/lib/jquery.countTo.js', array('jquery'), NULL, TRUE);
			wp_enqueue_script('jquery-easypiechart', DANTE_LOCAL_PATH . '/js/lib/jquery.easypiechart.min.js', array('jquery'), NULL, TRUE);
			wp_enqueue_script('jquery-equalHeights', DANTE_LOCAL_PATH . '/js/lib/jquery.equalHeights.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('jquery-hoverIntent', DANTE_LOCAL_PATH . '/js/lib/jquery.hoverIntent.min.js', array('jquery'), NULL, TRUE);
		    wp_enqueue_script('jquery-infinite-scroll',  DANTE_LOCAL_PATH . '/js/lib/jquery.infinitescroll.min.js', array('jquery'), NULL, TRUE);
		    wp_enqueue_script('jquery-isotope', DANTE_LOCAL_PATH . '/js/lib/jquery.isotope.min.js', array('jquery'), NULL, TRUE);
		    wp_enqueue_script('jquery-imagesLoaded', DANTE_LOCAL_PATH . '/js/lib/imagesloaded.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('jquery-parallax', DANTE_LOCAL_PATH . '/js/lib/jquery.parallax.min.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('jquery-smartresize', DANTE_LOCAL_PATH . '/js/lib/jquery.smartresize.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('jquery-stickem', DANTE_LOCAL_PATH . '/js/lib/jquery.stickem.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('jquery-stickyplugin', DANTE_LOCAL_PATH . '/js/lib/jquery.stickyplugin.js', array('jquery'), NULL, TRUE);
    		wp_enqueue_script('jquery-viewport', DANTE_LOCAL_PATH . '/js/lib/jquery.viewport.js', array('jquery'), NULL, TRUE);
			wp_enqueue_script('owlcarousel', DANTE_LOCAL_PATH . '/js/lib/owl.carousel.min.js', array('jquery'), NULL, TRUE);

    	    if ( $lightbox_enabled ) {
    	    wp_enqueue_script('ilightbox', DANTE_LOCAL_PATH . '/js/lib/ilightbox.min.js', array('jquery'), NULL, TRUE);
    	    }
    	    
		    if ( !is_singular('tribe_events') && $post_type != 'tribe_events' && !is_post_type_archive('events') && !is_post_type_archive('tribe_events') && $gmaps_api_key != "") {
		    	wp_enqueue_script('google-maps', '//maps.google.com/maps/api/js?key=' . $gmaps_api_key, array('jquery'), NULL, TRUE);
		    }
	   	
    	    wp_enqueue_script('sf-functions', DANTE_LOCAL_PATH . '/js/functions.js', 'jquery', NULL, TRUE);
		    
		   	if (is_singular() && comments_open()) {
		    	wp_enqueue_script('comment-reply');
		    }
		}
		add_action('wp_enqueue_scripts', 'dante_enqueue_scripts');
	}
		
	/* LOAD BACKEND SCRIPTS
	================================================== */
	function dante_admin_scripts() {
	    wp_enqueue_script('admin-functions', get_template_directory_uri() . '/js/sf-admin.js', 'jquery', '1.0', TRUE);
	}
	add_action('admin_init', 'dante_admin_scripts');
	

	/* GOOGLE FONTS URL FUNCTION
    ================================================== */
	function dante_google_fonts_url() {
		$fonts_url = '';
		$font_families = array();
		$options = get_option('sf_dante_options');

		$custom_fonts = $google_font_one = $google_font_two = $google_font_three = $google_font_subset = $subset_output = "";
		
		$body_font_option = $options['body_font_option'];
		if (isset($options['google_standard_font'])) {
		$google_font_one = $options['google_standard_font'];
		}
		$headings_font_option = $options['headings_font_option'];
		if (isset($options['google_heading_font'])) {
		$google_font_two = $options['google_heading_font'];
		}
		$menu_font_option = $options['menu_font_option'];
		if (isset($options['google_menu_font'])) {
		$google_font_three = $options['google_menu_font'];
		}
		
		if (isset($options['google_font_subset'])) {
		$google_font_subset = $options['google_font_subset'];
			$s = 0;
			if (is_array($google_font_subset)) {
				foreach ($google_font_subset as $subset) {
					if ($subset == "none") {
						break;
					}
					if ($s > 0) {
					$subset_output .= ','.$subset;
					} else {
					$subset_output = $subset;
					}
					$s++;
				}
			}
		}

		if ($body_font_option == "google" && $google_font_one != "") {
			$font_families[] = str_replace(' ', '+', $google_font_one);
		}
		if ($headings_font_option == "google" && $google_font_two != "") {
			$font_families[] = str_replace(' ', '+', $google_font_two);
		}
		if ($menu_font_option == "google" && $google_font_three != "") {
			$font_families[] = str_replace(' ', '+', $google_font_three);
		}
		
		$query_args = array(
			'family' => implode( '|', $font_families ),
			'subset' => urlencode( $subset_output ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		
		return esc_url_raw( $fonts_url );
	}
	
	/* LAYERSLIDER OVERRIDES
	================================================== */
	function dante_layerslider_overrides() {
		// Disable auto-updates
		$GLOBALS['lsAutoUpdateBox'] = false;
	}
	add_action('layerslider_ready', 'dante_layerslider_overrides');
	
	
	/* PERFORMANCE FRIENDLY GET META FUNCTION
    ================================================== */
    if ( !function_exists( 'dante_get_post_meta' ) ) {
	    function dante_get_post_meta( $id, $key = "", $single = false ) {

	        $GLOBALS['dante_post_meta'] = isset( $GLOBALS['dante_post_meta'] ) ? $GLOBALS['dante_post_meta'] : array();
	        if ( ! isset( $id ) ) {
	            return;
	        }
	        if ( ! is_array( $id ) ) {
	            if ( ! isset( $GLOBALS['dante_post_meta'][ $id ] ) ) {
	                //$GLOBALS['dante_post_meta'][ $id ] = array();
	                $GLOBALS['dante_post_meta'][ $id ] = get_post_meta( $id );
	            }
	            if ( ! empty( $key ) && isset( $GLOBALS['dante_post_meta'][ $id ][ $key ] ) && ! empty( $GLOBALS['dante_post_meta'][ $id ][ $key ] ) ) {
	                if ( $single ) {
	                    return maybe_unserialize( $GLOBALS['dante_post_meta'][ $id ][ $key ][0] );
	                } else {
	                    return array_map( 'maybe_unserialize', $GLOBALS['dante_post_meta'][ $id ][ $key ] );
	                }
	            }

	            if ( $single ) {
	                return '';
	            } else {
	                return array();
	            }

	        }

	        return get_post_meta( $id, $key, $single );
	    }
    }
    
	
	/* META BOX MISSING FALLBACK
    ================================================== */
	if (!function_exists('rwmb_meta')) {
		function rwmb_meta($key, $args, $postID = null) {
			if (!$postID) {
				$postID = get_the_ID();
			}
			return get_post_meta($postID, $key, true);
		}
	}
	
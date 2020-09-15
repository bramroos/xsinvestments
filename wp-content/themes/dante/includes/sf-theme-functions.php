<?php
	
	/*
	*
	*	Swift Framework Theme Functions
	*	------------------------------------------------
	*	Swift Framework v2.0
	* 	Copyright Swift Ideas 2019 - http://www.swiftideas.net
	*
	*	dante_theme_activation()
	*	dante_bwm_filter()
	*	dante_bwm_filter_script()
	*	dante_filter_wp_title()
	*	dante_maintenance_mode()
	*	dante_custom_login_logo()
	*	dante_breadcrumbs()
	*	dante_language_flags()
	*	dante_hex2rgb()
	*	dante_get_comments_number()
	*	dante_get_menus_list()
	*	dante_get_category_list()
	*	dante_get_category_list_key_array()
	*	dante_get_woo_product_filters_array()
	*	dante_add_nofollow_cat()
	*	dante_remove_head_links()
	*	dante_current_page_url()
	*	dante_woocommerce_activated()
	*	dante_global_include_classes()
	*	dante_countdown_shortcode_locale()
	*	dante_admin_css()
	*
	*/
	
	/* THEME ACTIVATION
	================================================== */	
	if (!function_exists('dante_theme_activation')) {
		function dante_theme_activation() {
			global $pagenow;
			if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
				#set frontpage to display_posts
				update_option('show_on_front', 'posts');
				
				#provide hook so themes can execute theme specific functions on activation
				do_action('dante_theme_activation');
				
				#redirect to options page
				header( 'Location: '.admin_url().'admin.php?page=dante_theme_options&dante_welcome=true' ) ;
			}
		}
		add_action('admin_init', 'dante_theme_activation');
	}
	
	
	/* BETTER SEO PAGE TITLE
	================================================== */
	if (!function_exists('dante_filter_wp_title')) {
		function dante_filter_wp_title( $title ) {
			global $page, $paged;
		
			if ( is_feed() )
				return $title;
		
			$site_description = get_bloginfo( 'description' );
		
			$filtered_title = $title . get_bloginfo( 'name' );
			$filtered_title .= ( ! empty( $site_description ) && ( is_home() || is_front_page() ) ) ? ' | ' . $site_description: '';
			$filtered_title .= ( 2 <= $paged || 2 <= $page ) ? ' | ' . sprintf( __( 'Page %s', 'dante' ), max( $paged, $page ) ) : '';
		
			return $filtered_title;
		}
		add_filter( 'wp_title', 'dante_filter_wp_title' );
	}
	
	
	/* MAINTENANCE MODE
	================================================== */
	if (!function_exists('dante_maintenance_mode')) {
		function dante_maintenance_mode() {
			$options = get_option('sf_dante_options');
			$custom_logo = $custom_logo_output = $maintenance_mode = "";
			if (isset($options['custom_admin_login_logo'])) {
			$custom_logo = $options['custom_admin_login_logo'];
			}
			if ($custom_logo) {		
			$custom_logo_output = '<img src="'. $custom_logo .'" alt="maintenance" style="margin: 0 auto; display: block;" />';
			} else {
			$custom_logo_output = '<img src="'. get_template_directory_uri() .'/images/custom-login-logo.png" alt="maintenance" style="margin: 0 auto; display: block;" />';
			}
	
			if (isset($options['enable_maintenance'])) {
			$maintenance_mode = $options['enable_maintenance'];
			} else {
			$maintenance_mode = false;
			}
			
			if ($maintenance_mode == 2) {
				
				$holding_page = __($options['maintenance_mode_page'], 'dante');
			    $current_page_URL = dante_current_page_url();
			    $holding_page_URL = get_permalink($holding_page);
			    
			    if ($current_page_URL != $holding_page_URL) {
			    	if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
			    	wp_redirect( $holding_page_URL );
			    	exit;
			    	}
			    }
		    
		    } else if ($maintenance_mode == 1) {
		    	if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
		    	    wp_die($custom_logo_output . '<p style="text-align:center">'.__('We are currently in maintenance mode, please check back shortly.', 'dante').'</p>', get_bloginfo( 'name' ));
		    	}
		    }
		}
		add_action('get_header', 'dante_maintenance_mode');
	}
	
	
	/* CUSTOM LOGIN LOGO
	================================================== */
	if (!function_exists('dante_custom_login_logo')) {
		function dante_custom_login_logo() {
			$options = get_option('sf_dante_options');
			$custom_logo = "";
			if (isset($options['custom_admin_login_logo'])) {
			$custom_logo = $options['custom_admin_login_logo'];
			}
			if ($custom_logo) {		
			echo '<style type="text/css">
			    .login h1 a { background-image:url('. $custom_logo .') !important; height: 95px!important; width: 100%!important; background-size: auto !important; }
			</style>';
			} else {
			echo '<style type="text/css">
			    .login h1 a { background-image:url('. get_template_directory_uri() .'/images/custom-login-logo.png) !important; height: 95px!important; width: 100%!important; background-size: auto!important; }
			</style>';
			}
		}
		add_action('login_head', 'dante_custom_login_logo');
	}
	
	
	/* BREADCRUMBS
	================================================== */ 
	if (!function_exists('dante_breadcrumbs')) {
		function dante_breadcrumbs() {
			$breadcrumb_output = "";
			
			if ( function_exists('bcn_display') ) {
				$breadcrumb_output .= '<div id="breadcrumbs">'. "\n";
				$breadcrumb_output .= bcn_display(true);
				$breadcrumb_output .= '</div>'. "\n";
			} else if ( function_exists('yoast_breadcrumb') ) {
				$breadcrumb_output .= '<div id="breadcrumbs">'. "\n";
				$breadcrumb_output .= yoast_breadcrumb("","",false);
				$breadcrumb_output .= '</div>'. "\n";
			}
			
			return $breadcrumb_output;
		}
	}
	
	
	/* LANGUAGE FLAGS
	================================================== */
	if (!function_exists('dante_language_flags')){	
	    function dante_language_flags() {
	        $language_output = "";
	        if (function_exists('pll_the_languages')){
	            $languages = pll_the_languages(array('raw'=>1));
	            if(!empty($languages)){
	                foreach($languages as $l){
	                    $language_output .= '<li>';
	                    if($l['flag']){
	                        if(!$l['current_lang']) {
	                        	$language_output .= '<a href="'.$l['url'].'"><img src="'.$l['flag'].'" height="12" alt="'.$l['slug'].'" width="18" /><span class="language name">'.$l['name'].'</span></a>'."\n";
	                        } else {
	                        	$language_output .= '<div class="current-language"><img src="'.$l['flag'].'" height="12" alt="'.$l['slug'].'" width="18" /><span class="language name">'.$l['name'].'</span></div>'."\n";
	                        }
	                    }
	                    $language_output .= '</li>';
	                 }   
	            }
	        } elseif (function_exists('icl_get_languages')) {
	            $languages = icl_get_languages('skip_missing=0&orderby=code');
	            if(!empty($languages)){
	                foreach($languages as $l){
	                    $language_output .= '<li>';
	                    if($l['country_flag_url']){
	                        if(!$l['active']) {
	                        	$language_output .= '<a href="'.$l['url'].'"><img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" /><span class="language name">'.$l['translated_name'].'</span></a>'."\n";
	                        } else {
	                        	$language_output .= '<div class="current-language"><img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" /><span class="language name">'.$l['translated_name'].'</span></div>'."\n";
	                        }
	                    }
	                    $language_output .= '</li>';
	                }
	            }
	        } else {
	        	$flags_url = get_template_directory_uri() . '/images/flags';
	        	$language_output .= '<li><a href="#">DEMO - EXAMPLE PURPOSES</a></li><li><a href="#"><span class="language name">German</span></a></li><li><div class="current-language"><span class="language name">English</span></div></li><li><a href="#"><span class="language name">Spanish</span></a></li><li><a href="#"><span class="language name">French</span></a></li>'."\n";
	        }
	       return $language_output;
	    }
	}
	
	
	/* HEX TO RGB COLOR
	================================================== */
	function dante_hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}


	/* GET COMMENTS COUNT TEXT
	================================================== */
	function dante_get_comments_number($post_id) {
		$num_comments = get_comments_number($post_id); // get_comments_number returns only a numeric value
		$comments_text = "";
		
		if ( $num_comments == 0 ) {
			$comments_text = __('0 Comments', 'dante');
		} elseif ( $num_comments > 1 ) {
			$comments_text = $num_comments . __(' Comments', 'dante');
		} else {
			$comments_text = __('1 Comment', 'dante');
		}
		
		return $comments_text;
	}
	
	/* GET USER MENU LIST
	================================================== */
	function dante_get_menu_list() {
		$menu_list = array( '' => '' );
	    $user_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) ); 
	  
	    foreach( $user_menus as $menu ) {
			$menu_list[$menu->term_id] = $menu->name;
	    }
	    
	    return $menu_list;
	}
	
	/* GET CUSTOM POST TYPE TAXONOMY LIST
	================================================== */
	function dante_get_category_list( $category_name, $filter=0, $category_child = "" ){
		
		if (!$filter) { 
		
			$get_category = get_categories( array( 'taxonomy' => $category_name	));
			$category_list = array( '0' => 'All');
			
			foreach( $get_category as $category ){
				if (isset($category->slug)) {
				$category_list[] = $category->slug;
				}
			}
				
			return $category_list;
			
		} else if ($category_child != "" && $category_child != "All") {
			
			$childcategory = get_term_by('slug', $category_child, $category_name);
			$get_category = get_categories( array( 'taxonomy' => $category_name, 'child_of' => $childcategory->term_id));
			$category_list = array( '0' => 'All');
			
			foreach( $get_category as $category ){
				if (isset($category->cat_name)) {
				$category_list[] = $category->slug;
				}
			}
				
			return $category_list;	
		
		} else {
			
			$get_category = get_categories( array( 'taxonomy' => $category_name));
			$category_list = array( '0' => 'All');
			
			foreach( $get_category as $category ){
				if (isset($category->cat_name)) {
				$category_list[] = $category->cat_name;
				}
			}
				
			return $category_list;	
		
		}
	}
	
	function dante_get_category_list_key_array($category_name) {
			
		$get_category = get_categories( array( 'taxonomy' => $category_name	));
		$category_list = array( 'all' => 'All');
		
		foreach( $get_category as $category ){
			if (isset($category->slug)) {
			$category_list[$category->slug] = $category->cat_name;
			}
		}
			
		return $category_list;
	}
	
	function dante_get_woo_product_filters_array() {
		
		global $woocommerce;
		
		$attribute_array = array();
		
		$transient_name = 'wc_attribute_taxonomies';

		if (dante_woocommerce_activated()) {
		
			if ( false === ( $attribute_taxonomies = get_transient( $transient_name ) ) ) {
				global $wpdb;
				
					$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies" );
					set_transient( $transient_name, $attribute_taxonomies );
			}
	
			$attribute_taxonomies = apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );
			
			$attribute_array['product_cat'] = __('Product Category', 'dante');
			$attribute_array['price'] = __('Price', 'dante');
					
			if ( $attribute_taxonomies ) {
				foreach ( $attribute_taxonomies as $tax ) {
					$attribute_array[$tax->attribute_name] = $tax->attribute_name;
				}
			}
		
		}
		
		return $attribute_array;	
	}
	
	
	/* CATEGORY REL FIX
	================================================== */
	function dante_add_nofollow_cat( $text) {
	    $text = str_replace('rel="category tag"', "", $text);
	    return $text;
	}
	add_filter( 'the_category', 'dante_add_nofollow_cat' );
	
	
	/* REMOVE CERTAIN HEAD TAGS
	================================================== */
	if (!function_exists('dante_remove_head_links')) {
		function dante_remove_head_links() {
			remove_action('wp_head', 'index_rel_link');
			remove_action('wp_head', 'rsd_link');
			remove_action('wp_head', 'wlwmanifest_link');
		}
		add_action('init', 'dante_remove_head_links');
	}
	
	
	/* GET CURRENT PAGE URL
	================================================== */
	function dante_current_page_url() {
		global $wp;
        if ($wp) {
            $current_url = home_url( $wp->request );
            return trailingslashit( $current_url );
        }
	}
	
	
	/* CHECK WOOCOMMERCE IS ACTIVE
	================================================== */ 
	if ( ! function_exists( 'dante_woocommerce_activated' ) ) {
		function dante_woocommerce_activated() {
			if ( class_exists( 'woocommerce' ) ) {
				return true;
			} else {
				return false;
			}
		}
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
	
	
	/* DYNAMIC GLOBAL INCLUDE CLASSES
	================================================== */ 
	function dante_global_include_classes() {
	
		// INCLUDED FUNCTIONALITY SETUP
		global $post, $dante_has_portfolio, $dante_has_blog, $dante_has_products, $dante_include_maps, $dante_include_isotope, $dante_include_carousel, $dante_include_parallax, $dante_include_infscroll, $dante_has_progress_bar, $dante_has_chart, $dante_has_countdown, $dante_has_imagebanner, $dante_has_team, $dante_has_portfolio_showcase, $dante_has_gallery;
			
		$dante_inc_class = "";
		
		if ($dante_has_portfolio) {
			$dante_inc_class .= "has-portfolio ";
		}
		if ($dante_has_blog) {
			$dante_inc_class .= "has-blog ";
		}
		if ($dante_has_products) {
			$dante_inc_class .= "has-products ";
		}
		
		$content = $post->post_content;
		
		if (function_exists('has_shortcode')) {
			if (has_shortcode( $content, 'product_category' ) || has_shortcode( $content, 'featured_products' ) || has_shortcode( $content, 'products' )) {
				$dante_inc_class .= "has-products ";
				$dante_include_isotope = true;
			}
		}
		
		if ($dante_include_maps) {
			$dante_inc_class .= "has-map ";
		}
		if ($dante_include_carousel) {
			$dante_inc_class .= "has-carousel ";
		}
		if ($dante_include_parallax) {
			$dante_inc_class .= "has-parallax ";
		}
		if ($dante_has_progress_bar) {
			$dante_inc_class .= "has-progress-bar ";
		}
		if ($dante_has_chart) {
			$dante_inc_class .= "has-chart ";
		}
		if ($dante_has_countdown) {
			$dante_inc_class .= "has-countdown ";
		}
		if ($dante_has_imagebanner) {
			$dante_inc_class .= "has-imagebanner ";
		}
		if ($dante_has_team) {
			$dante_inc_class .= "has-team ";
		}
		if ($dante_has_portfolio_showcase) {
			$dante_inc_class .= "has-portfolio-showcase ";
		}
		if ($dante_has_gallery) {
			$dante_inc_class .= "has-gallery ";
		}
		
		if ($dante_include_infscroll) {
			$dante_inc_class .= "has-infscroll ";
		}
		
		$options = get_option('sf_dante_options');
		
		if (isset($options['enable_product_zoom'])) {	
			$enable_product_zoom = $options['enable_product_zoom'];	
			if ($enable_product_zoom) {
				$dante_inc_class .= "has-productzoom ";
			}
		}
		
		if (isset($options['enable_stickysidebars'])) {
			$enable_stickysidebars = $options['enable_stickysidebars'];
			if ($enable_stickysidebars) {
				$dante_inc_class .= "stickysidebars ";
			} 
		}
		
		if (isset($options['disable_megamenu'])) {
			$disable_megamenu = $options['disable_megamenu'];
			if ($disable_megamenu) {
				$dante_inc_class .= "disable-megamenu ";
			} 
		}
		
		if (isset($options['sticky_header_mobile']) && $options['sticky_header_mobile']) {
			$dante_inc_class .= 'sticky-header-mobile ';
		}
		
		return $dante_inc_class;
	}
	
	
	/* PLUGIN OPTION PARAMS
	================================================== */
	if (!function_exists('dante_option_parameters')) {
		function dante_option_parameters() {
			$options = get_option('sf_dante_options');
			
			$lightbox_nav = "default";
			$lightbox_thumbs = "true";
			$lightbox_skin = "light";
			$lightbox_sharing = "true";
			$slider_slideshowSpeed = "6000";
			$slider_animationSpeed = "500";
			$slider_autoplay = "0";
			
			
			$lightbox_enabled 		  = true;
			if ( isset($options['lightbox_enabled']) ) {
			$lightbox_enabled     	  = $options['lightbox_enabled'];
			}
			if (isset($options['lightbox_nav'])) {
			$lightbox_nav             = $options['lightbox_nav'];
			}
			if (isset($options['lightbox_thumbs'])) {
			$lightbox_thumbs          = $options['lightbox_thumbs'];
			}
			if (isset($options['lightbox_skin'])) {
			$lightbox_skin            = $options['lightbox_skin'];
			}
			if (isset($options['lightbox_sharing'])) {
			$lightbox_sharing         = $options['lightbox_sharing'];
			}
			if (isset($options['slider_slideshowSpeed']) && $options['slider_slideshowSpeed'] != "0") {
			$slider_slideshowSpeed = $options['slider_slideshowSpeed'];
			}
			if (isset($options['slider_animationSpeed'])  && $options['slider_animationSpeed'] != "0") {
			$slider_animationSpeed = $options['slider_animationSpeed'];
			}
			if (isset($options['slider_autoplay'])) {
			$slider_autoplay = $options['slider_autoplay'];
			}
		?>
			<div id="sf-option-params"
				data-lightbox-enabled="<?php echo esc_attr($lightbox_enabled); ?>"
				data-lightbox-nav="<?php echo esc_attr($lightbox_nav); ?>"
				data-lightbox-thumbs="<?php echo esc_attr($lightbox_thumbs); ?>"
				data-lightbox-skin="<?php echo esc_attr($lightbox_skin); ?>"
				data-lightbox-sharing="<?php echo esc_attr($lightbox_sharing); ?>"
				data-slider-slidespeed="<?php echo esc_attr($slider_slideshowSpeed);?>"
				data-slider-animspeed="<?php echo esc_attr($slider_animationSpeed);?>"
				data-slider-autoplay="<?php echo esc_attr($slider_autoplay);?>"></div>
		
		<?php 
		}
		add_action('wp_footer', 'dante_option_parameters');
	}
	
	
	/* COUNTDOWN SHORTCODE LOCALE
	================================================== */
	if (!function_exists('dante_countdown_shortcode_locale')) {
		function dante_countdown_shortcode_locale() {
			global $dante_has_countdown;
			if ($dante_has_countdown) { ?>
			<div id="countdown-locale" data-label_year="<?php _e('Year', 'dante'); ?>" data-label_years="<?php _e('Years', 'dante'); ?>" data-label_month="<?php _e('Month', 'dante'); ?>" data-label_months="<?php _e('Months', 'dante'); ?>" data-label_weeks="<?php _e('Weeks', 'dante'); ?>" data-label_week="<?php _e('Week', 'dante'); ?>" data-label_days="<?php _e('Days', 'dante'); ?>" data-label_day="<?php _e('Day', 'dante'); ?>" data-label_hours="<?php _e('Hours', 'dante'); ?>" data-label_hour="<?php _e('Hour', 'dante'); ?>" data-label_mins="<?php _e('Mins', 'dante'); ?>" data-label_min="<?php _e('Min', 'dante'); ?>" data-label_secs="<?php _e('Secs', 'dante'); ?>" data-label_sec="<?php _e('Sec', 'dante'); ?>"></div>
			<?php }
		}
		add_action('wp_footer', 'dante_countdown_shortcode_locale');
	}

	
	/* ICON LIST
    ================================================== */
    if ( ! function_exists( 'dante_get_icons_list' ) ) {
        function dante_get_icons_list( $type = "" ) {

            // VARIABLES
            $icon_list = $fa_list = $gizmo_list = "";

            // FONT AWESOME
            $fa_list = '<li><i class="fas fa-ad"></i><span class="icon-name">fas fa-ad</span></li><li><i class="fas fa-address-book"></i><span class="icon-name">fas fa-address-book</span></li><li><i class="fas fa-address-card"></i><span class="icon-name">fas fa-address-card</span></li><li><i class="fas fa-adjust"></i><span class="icon-name">fas fa-adjust</span></li><li><i class="fas fa-air-freshener"></i><span class="icon-name">fas fa-air-freshener</span></li><li><i class="fas fa-align-center"></i><span class="icon-name">fas fa-align-center</span></li><li><i class="fas fa-align-justify"></i><span class="icon-name">fas fa-align-justify</span></li><li><i class="fas fa-align-left"></i><span class="icon-name">fas fa-align-left</span></li><li><i class="fas fa-align-right"></i><span class="icon-name">fas fa-align-right</span></li><li><i class="fas fa-allergies"></i><span class="icon-name">fas fa-allergies</span></li><li><i class="fas fa-ambulance"></i><span class="icon-name">fas fa-ambulance</span></li><li><i class="fas fa-american-sign-language-interpreting"></i><span class="icon-name">fas fa-american-sign-language-interpreting</span></li><li><i class="fas fa-anchor"></i><span class="icon-name">fas fa-anchor</span></li><li><i class="fas fa-angle-double-down"></i><span class="icon-name">fas fa-angle-double-down</span></li><li><i class="fas fa-angle-double-left"></i><span class="icon-name">fas fa-angle-double-left</span></li><li><i class="fas fa-angle-double-right"></i><span class="icon-name">fas fa-angle-double-right</span></li><li><i class="fas fa-angle-double-up"></i><span class="icon-name">fas fa-angle-double-up</span></li><li><i class="fas fa-angle-down"></i><span class="icon-name">fas fa-angle-down</span></li><li><i class="fas fa-angle-left"></i><span class="icon-name">fas fa-angle-left</span></li><li><i class="fas fa-angle-right"></i><span class="icon-name">fas fa-angle-right</span></li><li><i class="fas fa-angle-up"></i><span class="icon-name">fas fa-angle-up</span></li><li><i class="fas fa-angry"></i><span class="icon-name">fas fa-angry</span></li><li><i class="fas fa-ankh"></i><span class="icon-name">fas fa-ankh</span></li><li><i class="fas fa-apple-alt"></i><span class="icon-name">fas fa-apple-alt</span></li><li><i class="fas fa-archive"></i><span class="icon-name">fas fa-archive</span></li><li><i class="fas fa-archway"></i><span class="icon-name">fas fa-archway</span></li><li><i class="fas fa-arrow-alt-circle-down"></i><span class="icon-name">fas fa-arrow-alt-circle-down</span></li><li><i class="fas fa-arrow-alt-circle-left"></i><span class="icon-name">fas fa-arrow-alt-circle-left</span></li><li><i class="fas fa-arrow-alt-circle-right"></i><span class="icon-name">fas fa-arrow-alt-circle-right</span></li><li><i class="fas fa-arrow-alt-circle-up"></i><span class="icon-name">fas fa-arrow-alt-circle-up</span></li><li><i class="fas fa-arrow-circle-down"></i><span class="icon-name">fas fa-arrow-circle-down</span></li><li><i class="fas fa-arrow-circle-left"></i><span class="icon-name">fas fa-arrow-circle-left</span></li><li><i class="fas fa-arrow-circle-right"></i><span class="icon-name">fas fa-arrow-circle-right</span></li><li><i class="fas fa-arrow-circle-up"></i><span class="icon-name">fas fa-arrow-circle-up</span></li><li><i class="fas fa-arrow-down"></i><span class="icon-name">fas fa-arrow-down</span></li><li><i class="fas fa-arrow-left"></i><span class="icon-name">fas fa-arrow-left</span></li><li><i class="fas fa-arrow-right"></i><span class="icon-name">fas fa-arrow-right</span></li><li><i class="fas fa-arrow-up"></i><span class="icon-name">fas fa-arrow-up</span></li><li><i class="fas fa-arrows-alt"></i><span class="icon-name">fas fa-arrows-alt</span></li><li><i class="fas fa-arrows-alt-h"></i><span class="icon-name">fas fa-arrows-alt-h</span></li><li><i class="fas fa-arrows-alt-v"></i><span class="icon-name">fas fa-arrows-alt-v</span></li><li><i class="fas fa-assistive-listening-systems"></i><span class="icon-name">fas fa-assistive-listening-systems</span></li><li><i class="fas fa-asterisk"></i><span class="icon-name">fas fa-asterisk</span></li><li><i class="fas fa-at"></i><span class="icon-name">fas fa-at</span></li><li><i class="fas fa-atlas"></i><span class="icon-name">fas fa-atlas</span></li><li><i class="fas fa-atom"></i><span class="icon-name">fas fa-atom</span></li><li><i class="fas fa-audio-description"></i><span class="icon-name">fas fa-audio-description</span></li><li><i class="fas fa-award"></i><span class="icon-name">fas fa-award</span></li><li><i class="fas fa-baby"></i><span class="icon-name">fas fa-baby</span></li><li><i class="fas fa-baby-carriage"></i><span class="icon-name">fas fa-baby-carriage</span></li><li><i class="fas fa-backspace"></i><span class="icon-name">fas fa-backspace</span></li><li><i class="fas fa-backward"></i><span class="icon-name">fas fa-backward</span></li><li><i class="fas fa-bacon"></i><span class="icon-name">fas fa-bacon</span></li><li><i class="fas fa-balance-scale"></i><span class="icon-name">fas fa-balance-scale</span></li><li><i class="fas fa-balance-scale-left"></i><span class="icon-name">fas fa-balance-scale-left</span></li><li><i class="fas fa-balance-scale-right"></i><span class="icon-name">fas fa-balance-scale-right</span></li><li><i class="fas fa-ban"></i><span class="icon-name">fas fa-ban</span></li><li><i class="fas fa-band-aid"></i><span class="icon-name">fas fa-band-aid</span></li><li><i class="fas fa-barcode"></i><span class="icon-name">fas fa-barcode</span></li><li><i class="fas fa-bars"></i><span class="icon-name">fas fa-bars</span></li><li><i class="fas fa-baseball-ball"></i><span class="icon-name">fas fa-baseball-ball</span></li><li><i class="fas fa-basketball-ball"></i><span class="icon-name">fas fa-basketball-ball</span></li><li><i class="fas fa-bath"></i><span class="icon-name">fas fa-bath</span></li><li><i class="fas fa-battery-empty"></i><span class="icon-name">fas fa-battery-empty</span></li><li><i class="fas fa-battery-full"></i><span class="icon-name">fas fa-battery-full</span></li><li><i class="fas fa-battery-half"></i><span class="icon-name">fas fa-battery-half</span></li><li><i class="fas fa-battery-quarter"></i><span class="icon-name">fas fa-battery-quarter</span></li><li><i class="fas fa-battery-three-quarters"></i><span class="icon-name">fas fa-battery-three-quarters</span></li><li><i class="fas fa-bed"></i><span class="icon-name">fas fa-bed</span></li><li><i class="fas fa-beer"></i><span class="icon-name">fas fa-beer</span></li><li><i class="fas fa-bell"></i><span class="icon-name">fas fa-bell</span></li><li><i class="fas fa-bell-slash"></i><span class="icon-name">fas fa-bell-slash</span></li><li><i class="fas fa-bezier-curve"></i><span class="icon-name">fas fa-bezier-curve</span></li><li><i class="fas fa-bible"></i><span class="icon-name">fas fa-bible</span></li><li><i class="fas fa-bicycle"></i><span class="icon-name">fas fa-bicycle</span></li><li><i class="fas fa-biking"></i><span class="icon-name">fas fa-biking</span></li><li><i class="fas fa-binoculars"></i><span class="icon-name">fas fa-binoculars</span></li><li><i class="fas fa-biohazard"></i><span class="icon-name">fas fa-biohazard</span></li><li><i class="fas fa-birthday-cake"></i><span class="icon-name">fas fa-birthday-cake</span></li><li><i class="fas fa-blender"></i><span class="icon-name">fas fa-blender</span></li><li><i class="fas fa-blender-phone"></i><span class="icon-name">fas fa-blender-phone</span></li><li><i class="fas fa-blind"></i><span class="icon-name">fas fa-blind</span></li><li><i class="fas fa-blog"></i><span class="icon-name">fas fa-blog</span></li><li><i class="fas fa-bold"></i><span class="icon-name">fas fa-bold</span></li><li><i class="fas fa-bolt"></i><span class="icon-name">fas fa-bolt</span></li><li><i class="fas fa-bomb"></i><span class="icon-name">fas fa-bomb</span></li><li><i class="fas fa-bone"></i><span class="icon-name">fas fa-bone</span></li><li><i class="fas fa-bong"></i><span class="icon-name">fas fa-bong</span></li><li><i class="fas fa-book"></i><span class="icon-name">fas fa-book</span></li><li><i class="fas fa-book-dead"></i><span class="icon-name">fas fa-book-dead</span></li><li><i class="fas fa-book-medical"></i><span class="icon-name">fas fa-book-medical</span></li><li><i class="fas fa-book-open"></i><span class="icon-name">fas fa-book-open</span></li><li><i class="fas fa-book-reader"></i><span class="icon-name">fas fa-book-reader</span></li><li><i class="fas fa-bookmark"></i><span class="icon-name">fas fa-bookmark</span></li><li><i class="fas fa-border-all"></i><span class="icon-name">fas fa-border-all</span></li><li><i class="fas fa-border-none"></i><span class="icon-name">fas fa-border-none</span></li><li><i class="fas fa-border-style"></i><span class="icon-name">fas fa-border-style</span></li><li><i class="fas fa-bowling-ball"></i><span class="icon-name">fas fa-bowling-ball</span></li><li><i class="fas fa-box"></i><span class="icon-name">fas fa-box</span></li><li><i class="fas fa-box-open"></i><span class="icon-name">fas fa-box-open</span></li><li><i class="fas fa-boxes"></i><span class="icon-name">fas fa-boxes</span></li><li><i class="fas fa-braille"></i><span class="icon-name">fas fa-braille</span></li><li><i class="fas fa-brain"></i><span class="icon-name">fas fa-brain</span></li><li><i class="fas fa-bread-slice"></i><span class="icon-name">fas fa-bread-slice</span></li><li><i class="fas fa-briefcase"></i><span class="icon-name">fas fa-briefcase</span></li><li><i class="fas fa-briefcase-medical"></i><span class="icon-name">fas fa-briefcase-medical</span></li><li><i class="fas fa-broadcast-tower"></i><span class="icon-name">fas fa-broadcast-tower</span></li><li><i class="fas fa-broom"></i><span class="icon-name">fas fa-broom</span></li><li><i class="fas fa-brush"></i><span class="icon-name">fas fa-brush</span></li><li><i class="fas fa-bug"></i><span class="icon-name">fas fa-bug</span></li><li><i class="fas fa-building"></i><span class="icon-name">fas fa-building</span></li><li><i class="fas fa-bullhorn"></i><span class="icon-name">fas fa-bullhorn</span></li><li><i class="fas fa-bullseye"></i><span class="icon-name">fas fa-bullseye</span></li><li><i class="fas fa-burn"></i><span class="icon-name">fas fa-burn</span></li><li><i class="fas fa-bus"></i><span class="icon-name">fas fa-bus</span></li><li><i class="fas fa-bus-alt"></i><span class="icon-name">fas fa-bus-alt</span></li><li><i class="fas fa-business-time"></i><span class="icon-name">fas fa-business-time</span></li><li><i class="fas fa-calculator"></i><span class="icon-name">fas fa-calculator</span></li><li><i class="fas fa-calendar"></i><span class="icon-name">fas fa-calendar</span></li><li><i class="fas fa-calendar-alt"></i><span class="icon-name">fas fa-calendar-alt</span></li><li><i class="fas fa-calendar-check"></i><span class="icon-name">fas fa-calendar-check</span></li><li><i class="fas fa-calendar-day"></i><span class="icon-name">fas fa-calendar-day</span></li><li><i class="fas fa-calendar-minus"></i><span class="icon-name">fas fa-calendar-minus</span></li><li><i class="fas fa-calendar-plus"></i><span class="icon-name">fas fa-calendar-plus</span></li><li><i class="fas fa-calendar-times"></i><span class="icon-name">fas fa-calendar-times</span></li><li><i class="fas fa-calendar-week"></i><span class="icon-name">fas fa-calendar-week</span></li><li><i class="fas fa-camera"></i><span class="icon-name">fas fa-camera</span></li><li><i class="fas fa-camera-retro"></i><span class="icon-name">fas fa-camera-retro</span></li><li><i class="fas fa-campground"></i><span class="icon-name">fas fa-campground</span></li><li><i class="fas fa-candy-cane"></i><span class="icon-name">fas fa-candy-cane</span></li><li><i class="fas fa-cannabis"></i><span class="icon-name">fas fa-cannabis</span></li><li><i class="fas fa-capsules"></i><span class="icon-name">fas fa-capsules</span></li><li><i class="fas fa-car"></i><span class="icon-name">fas fa-car</span></li><li><i class="fas fa-car-alt"></i><span class="icon-name">fas fa-car-alt</span></li><li><i class="fas fa-car-battery"></i><span class="icon-name">fas fa-car-battery</span></li><li><i class="fas fa-car-crash"></i><span class="icon-name">fas fa-car-crash</span></li><li><i class="fas fa-car-side"></i><span class="icon-name">fas fa-car-side</span></li><li><i class="fas fa-caret-down"></i><span class="icon-name">fas fa-caret-down</span></li><li><i class="fas fa-caret-left"></i><span class="icon-name">fas fa-caret-left</span></li><li><i class="fas fa-caret-right"></i><span class="icon-name">fas fa-caret-right</span></li><li><i class="fas fa-caret-square-down"></i><span class="icon-name">fas fa-caret-square-down</span></li><li><i class="fas fa-caret-square-left"></i><span class="icon-name">fas fa-caret-square-left</span></li><li><i class="fas fa-caret-square-right"></i><span class="icon-name">fas fa-caret-square-right</span></li><li><i class="fas fa-caret-square-up"></i><span class="icon-name">fas fa-caret-square-up</span></li><li><i class="fas fa-caret-up"></i><span class="icon-name">fas fa-caret-up</span></li><li><i class="fas fa-carrot"></i><span class="icon-name">fas fa-carrot</span></li><li><i class="fas fa-cart-arrow-down"></i><span class="icon-name">fas fa-cart-arrow-down</span></li><li><i class="fas fa-cart-plus"></i><span class="icon-name">fas fa-cart-plus</span></li><li><i class="fas fa-cash-register"></i><span class="icon-name">fas fa-cash-register</span></li><li><i class="fas fa-cat"></i><span class="icon-name">fas fa-cat</span></li><li><i class="fas fa-certificate"></i><span class="icon-name">fas fa-certificate</span></li><li><i class="fas fa-chair"></i><span class="icon-name">fas fa-chair</span></li><li><i class="fas fa-chalkboard"></i><span class="icon-name">fas fa-chalkboard</span></li><li><i class="fas fa-chalkboard-teacher"></i><span class="icon-name">fas fa-chalkboard-teacher</span></li><li><i class="fas fa-charging-station"></i><span class="icon-name">fas fa-charging-station</span></li><li><i class="fas fa-chart-area"></i><span class="icon-name">fas fa-chart-area</span></li><li><i class="fas fa-chart-bar"></i><span class="icon-name">fas fa-chart-bar</span></li><li><i class="fas fa-chart-line"></i><span class="icon-name">fas fa-chart-line</span></li><li><i class="fas fa-chart-pie"></i><span class="icon-name">fas fa-chart-pie</span></li><li><i class="fas fa-check"></i><span class="icon-name">fas fa-check</span></li><li><i class="fas fa-check-circle"></i><span class="icon-name">fas fa-check-circle</span></li><li><i class="fas fa-check-double"></i><span class="icon-name">fas fa-check-double</span></li><li><i class="fas fa-check-square"></i><span class="icon-name">fas fa-check-square</span></li><li><i class="fas fa-cheese"></i><span class="icon-name">fas fa-cheese</span></li><li><i class="fas fa-chess"></i><span class="icon-name">fas fa-chess</span></li><li><i class="fas fa-chess-bishop"></i><span class="icon-name">fas fa-chess-bishop</span></li><li><i class="fas fa-chess-board"></i><span class="icon-name">fas fa-chess-board</span></li><li><i class="fas fa-chess-king"></i><span class="icon-name">fas fa-chess-king</span></li><li><i class="fas fa-chess-knight"></i><span class="icon-name">fas fa-chess-knight</span></li><li><i class="fas fa-chess-pawn"></i><span class="icon-name">fas fa-chess-pawn</span></li><li><i class="fas fa-chess-queen"></i><span class="icon-name">fas fa-chess-queen</span></li><li><i class="fas fa-chess-rook"></i><span class="icon-name">fas fa-chess-rook</span></li><li><i class="fas fa-chevron-circle-down"></i><span class="icon-name">fas fa-chevron-circle-down</span></li><li><i class="fas fa-chevron-circle-left"></i><span class="icon-name">fas fa-chevron-circle-left</span></li><li><i class="fas fa-chevron-circle-right"></i><span class="icon-name">fas fa-chevron-circle-right</span></li><li><i class="fas fa-chevron-circle-up"></i><span class="icon-name">fas fa-chevron-circle-up</span></li><li><i class="fas fa-chevron-down"></i><span class="icon-name">fas fa-chevron-down</span></li><li><i class="fas fa-chevron-left"></i><span class="icon-name">fas fa-chevron-left</span></li><li><i class="fas fa-chevron-right"></i><span class="icon-name">fas fa-chevron-right</span></li><li><i class="fas fa-chevron-up"></i><span class="icon-name">fas fa-chevron-up</span></li><li><i class="fas fa-child"></i><span class="icon-name">fas fa-child</span></li><li><i class="fas fa-church"></i><span class="icon-name">fas fa-church</span></li><li><i class="fas fa-circle"></i><span class="icon-name">fas fa-circle</span></li><li><i class="fas fa-circle-notch"></i><span class="icon-name">fas fa-circle-notch</span></li><li><i class="fas fa-city"></i><span class="icon-name">fas fa-city</span></li><li><i class="fas fa-clinic-medical"></i><span class="icon-name">fas fa-clinic-medical</span></li><li><i class="fas fa-clipboard"></i><span class="icon-name">fas fa-clipboard</span></li><li><i class="fas fa-clipboard-check"></i><span class="icon-name">fas fa-clipboard-check</span></li><li><i class="fas fa-clipboard-list"></i><span class="icon-name">fas fa-clipboard-list</span></li><li><i class="fas fa-clock"></i><span class="icon-name">fas fa-clock</span></li><li><i class="fas fa-clone"></i><span class="icon-name">fas fa-clone</span></li><li><i class="fas fa-closed-captioning"></i><span class="icon-name">fas fa-closed-captioning</span></li><li><i class="fas fa-cloud"></i><span class="icon-name">fas fa-cloud</span></li><li><i class="fas fa-cloud-download-alt"></i><span class="icon-name">fas fa-cloud-download-alt</span></li><li><i class="fas fa-cloud-meatball"></i><span class="icon-name">fas fa-cloud-meatball</span></li><li><i class="fas fa-cloud-moon"></i><span class="icon-name">fas fa-cloud-moon</span></li><li><i class="fas fa-cloud-moon-rain"></i><span class="icon-name">fas fa-cloud-moon-rain</span></li><li><i class="fas fa-cloud-rain"></i><span class="icon-name">fas fa-cloud-rain</span></li><li><i class="fas fa-cloud-showers-heavy"></i><span class="icon-name">fas fa-cloud-showers-heavy</span></li><li><i class="fas fa-cloud-sun"></i><span class="icon-name">fas fa-cloud-sun</span></li><li><i class="fas fa-cloud-sun-rain"></i><span class="icon-name">fas fa-cloud-sun-rain</span></li><li><i class="fas fa-cloud-upload-alt"></i><span class="icon-name">fas fa-cloud-upload-alt</span></li><li><i class="fas fa-cocktail"></i><span class="icon-name">fas fa-cocktail</span></li><li><i class="fas fa-code"></i><span class="icon-name">fas fa-code</span></li><li><i class="fas fa-code-branch"></i><span class="icon-name">fas fa-code-branch</span></li><li><i class="fas fa-coffee"></i><span class="icon-name">fas fa-coffee</span></li><li><i class="fas fa-cog"></i><span class="icon-name">fas fa-cog</span></li><li><i class="fas fa-cogs"></i><span class="icon-name">fas fa-cogs</span></li><li><i class="fas fa-coins"></i><span class="icon-name">fas fa-coins</span></li><li><i class="fas fa-columns"></i><span class="icon-name">fas fa-columns</span></li><li><i class="fas fa-comment"></i><span class="icon-name">fas fa-comment</span></li><li><i class="fas fa-comment-alt"></i><span class="icon-name">fas fa-comment-alt</span></li><li><i class="fas fa-comment-dollar"></i><span class="icon-name">fas fa-comment-dollar</span></li><li><i class="fas fa-comment-dots"></i><span class="icon-name">fas fa-comment-dots</span></li><li><i class="fas fa-comment-medical"></i><span class="icon-name">fas fa-comment-medical</span></li><li><i class="fas fa-comment-slash"></i><span class="icon-name">fas fa-comment-slash</span></li><li><i class="fas fa-comments"></i><span class="icon-name">fas fa-comments</span></li><li><i class="fas fa-comments-dollar"></i><span class="icon-name">fas fa-comments-dollar</span></li><li><i class="fas fa-compact-disc"></i><span class="icon-name">fas fa-compact-disc</span></li><li><i class="fas fa-compass"></i><span class="icon-name">fas fa-compass</span></li><li><i class="fas fa-compress"></i><span class="icon-name">fas fa-compress</span></li><li><i class="fas fa-compress-arrows-alt"></i><span class="icon-name">fas fa-compress-arrows-alt</span></li><li><i class="fas fa-concierge-bell"></i><span class="icon-name">fas fa-concierge-bell</span></li><li><i class="fas fa-cookie"></i><span class="icon-name">fas fa-cookie</span></li><li><i class="fas fa-cookie-bite"></i><span class="icon-name">fas fa-cookie-bite</span></li><li><i class="fas fa-copy"></i><span class="icon-name">fas fa-copy</span></li><li><i class="fas fa-copyright"></i><span class="icon-name">fas fa-copyright</span></li><li><i class="fas fa-couch"></i><span class="icon-name">fas fa-couch</span></li><li><i class="fas fa-credit-card"></i><span class="icon-name">fas fa-credit-card</span></li><li><i class="fas fa-crop"></i><span class="icon-name">fas fa-crop</span></li><li><i class="fas fa-crop-alt"></i><span class="icon-name">fas fa-crop-alt</span></li><li><i class="fas fa-cross"></i><span class="icon-name">fas fa-cross</span></li><li><i class="fas fa-crosshairs"></i><span class="icon-name">fas fa-crosshairs</span></li><li><i class="fas fa-crow"></i><span class="icon-name">fas fa-crow</span></li><li><i class="fas fa-crown"></i><span class="icon-name">fas fa-crown</span></li><li><i class="fas fa-crutch"></i><span class="icon-name">fas fa-crutch</span></li><li><i class="fas fa-cube"></i><span class="icon-name">fas fa-cube</span></li><li><i class="fas fa-cubes"></i><span class="icon-name">fas fa-cubes</span></li><li><i class="fas fa-cut"></i><span class="icon-name">fas fa-cut</span></li><li><i class="fas fa-database"></i><span class="icon-name">fas fa-database</span></li><li><i class="fas fa-deaf"></i><span class="icon-name">fas fa-deaf</span></li><li><i class="fas fa-democrat"></i><span class="icon-name">fas fa-democrat</span></li><li><i class="fas fa-desktop"></i><span class="icon-name">fas fa-desktop</span></li><li><i class="fas fa-dharmachakra"></i><span class="icon-name">fas fa-dharmachakra</span></li><li><i class="fas fa-diagnoses"></i><span class="icon-name">fas fa-diagnoses</span></li><li><i class="fas fa-dice"></i><span class="icon-name">fas fa-dice</span></li><li><i class="fas fa-dice-d20"></i><span class="icon-name">fas fa-dice-d20</span></li><li><i class="fas fa-dice-d6"></i><span class="icon-name">fas fa-dice-d6</span></li><li><i class="fas fa-dice-five"></i><span class="icon-name">fas fa-dice-five</span></li><li><i class="fas fa-dice-four"></i><span class="icon-name">fas fa-dice-four</span></li><li><i class="fas fa-dice-one"></i><span class="icon-name">fas fa-dice-one</span></li><li><i class="fas fa-dice-six"></i><span class="icon-name">fas fa-dice-six</span></li><li><i class="fas fa-dice-three"></i><span class="icon-name">fas fa-dice-three</span></li><li><i class="fas fa-dice-two"></i><span class="icon-name">fas fa-dice-two</span></li><li><i class="fas fa-digital-tachograph"></i><span class="icon-name">fas fa-digital-tachograph</span></li><li><i class="fas fa-directions"></i><span class="icon-name">fas fa-directions</span></li><li><i class="fas fa-divide"></i><span class="icon-name">fas fa-divide</span></li><li><i class="fas fa-dizzy"></i><span class="icon-name">fas fa-dizzy</span></li><li><i class="fas fa-dna"></i><span class="icon-name">fas fa-dna</span></li><li><i class="fas fa-dog"></i><span class="icon-name">fas fa-dog</span></li><li><i class="fas fa-dollar-sign"></i><span class="icon-name">fas fa-dollar-sign</span></li><li><i class="fas fa-dolly"></i><span class="icon-name">fas fa-dolly</span></li><li><i class="fas fa-dolly-flatbed"></i><span class="icon-name">fas fa-dolly-flatbed</span></li><li><i class="fas fa-donate"></i><span class="icon-name">fas fa-donate</span></li><li><i class="fas fa-door-closed"></i><span class="icon-name">fas fa-door-closed</span></li><li><i class="fas fa-door-open"></i><span class="icon-name">fas fa-door-open</span></li><li><i class="fas fa-dot-circle"></i><span class="icon-name">fas fa-dot-circle</span></li><li><i class="fas fa-dove"></i><span class="icon-name">fas fa-dove</span></li><li><i class="fas fa-download"></i><span class="icon-name">fas fa-download</span></li><li><i class="fas fa-drafting-compass"></i><span class="icon-name">fas fa-drafting-compass</span></li><li><i class="fas fa-dragon"></i><span class="icon-name">fas fa-dragon</span></li><li><i class="fas fa-draw-polygon"></i><span class="icon-name">fas fa-draw-polygon</span></li><li><i class="fas fa-drum"></i><span class="icon-name">fas fa-drum</span></li><li><i class="fas fa-drum-steelpan"></i><span class="icon-name">fas fa-drum-steelpan</span></li><li><i class="fas fa-drumstick-bite"></i><span class="icon-name">fas fa-drumstick-bite</span></li><li><i class="fas fa-dumbbell"></i><span class="icon-name">fas fa-dumbbell</span></li><li><i class="fas fa-dumpster"></i><span class="icon-name">fas fa-dumpster</span></li><li><i class="fas fa-dumpster-fire"></i><span class="icon-name">fas fa-dumpster-fire</span></li><li><i class="fas fa-dungeon"></i><span class="icon-name">fas fa-dungeon</span></li><li><i class="fas fa-edit"></i><span class="icon-name">fas fa-edit</span></li><li><i class="fas fa-egg"></i><span class="icon-name">fas fa-egg</span></li><li><i class="fas fa-eject"></i><span class="icon-name">fas fa-eject</span></li><li><i class="fas fa-ellipsis-h"></i><span class="icon-name">fas fa-ellipsis-h</span></li><li><i class="fas fa-ellipsis-v"></i><span class="icon-name">fas fa-ellipsis-v</span></li><li><i class="fas fa-envelope"></i><span class="icon-name">fas fa-envelope</span></li><li><i class="fas fa-envelope-open"></i><span class="icon-name">fas fa-envelope-open</span></li><li><i class="fas fa-envelope-open-text"></i><span class="icon-name">fas fa-envelope-open-text</span></li><li><i class="fas fa-envelope-square"></i><span class="icon-name">fas fa-envelope-square</span></li><li><i class="fas fa-equals"></i><span class="icon-name">fas fa-equals</span></li><li><i class="fas fa-eraser"></i><span class="icon-name">fas fa-eraser</span></li><li><i class="fas fa-ethernet"></i><span class="icon-name">fas fa-ethernet</span></li><li><i class="fas fa-euro-sign"></i><span class="icon-name">fas fa-euro-sign</span></li><li><i class="fas fa-exchange-alt"></i><span class="icon-name">fas fa-exchange-alt</span></li><li><i class="fas fa-exclamation"></i><span class="icon-name">fas fa-exclamation</span></li><li><i class="fas fa-exclamation-circle"></i><span class="icon-name">fas fa-exclamation-circle</span></li><li><i class="fas fa-exclamation-triangle"></i><span class="icon-name">fas fa-exclamation-triangle</span></li><li><i class="fas fa-expand"></i><span class="icon-name">fas fa-expand</span></li><li><i class="fas fa-expand-arrows-alt"></i><span class="icon-name">fas fa-expand-arrows-alt</span></li><li><i class="fas fa-external-link-alt"></i><span class="icon-name">fas fa-external-link-alt</span></li><li><i class="fas fa-external-link-square-alt"></i><span class="icon-name">fas fa-external-link-square-alt</span></li><li><i class="fas fa-eye"></i><span class="icon-name">fas fa-eye</span></li><li><i class="fas fa-eye-dropper"></i><span class="icon-name">fas fa-eye-dropper</span></li><li><i class="fas fa-eye-slash"></i><span class="icon-name">fas fa-eye-slash</span></li><li><i class="fas fa-fan"></i><span class="icon-name">fas fa-fan</span></li><li><i class="fas fa-fast-backward"></i><span class="icon-name">fas fa-fast-backward</span></li><li><i class="fas fa-fast-forward"></i><span class="icon-name">fas fa-fast-forward</span></li><li><i class="fas fa-fax"></i><span class="icon-name">fas fa-fax</span></li><li><i class="fas fa-feather"></i><span class="icon-name">fas fa-feather</span></li><li><i class="fas fa-feather-alt"></i><span class="icon-name">fas fa-feather-alt</span></li><li><i class="fas fa-female"></i><span class="icon-name">fas fa-female</span></li><li><i class="fas fa-fighter-jet"></i><span class="icon-name">fas fa-fighter-jet</span></li><li><i class="fas fa-file"></i><span class="icon-name">fas fa-file</span></li><li><i class="fas fa-file-alt"></i><span class="icon-name">fas fa-file-alt</span></li><li><i class="fas fa-file-archive"></i><span class="icon-name">fas fa-file-archive</span></li><li><i class="fas fa-file-audio"></i><span class="icon-name">fas fa-file-audio</span></li><li><i class="fas fa-file-code"></i><span class="icon-name">fas fa-file-code</span></li><li><i class="fas fa-file-contract"></i><span class="icon-name">fas fa-file-contract</span></li><li><i class="fas fa-file-csv"></i><span class="icon-name">fas fa-file-csv</span></li><li><i class="fas fa-file-download"></i><span class="icon-name">fas fa-file-download</span></li><li><i class="fas fa-file-excel"></i><span class="icon-name">fas fa-file-excel</span></li><li><i class="fas fa-file-export"></i><span class="icon-name">fas fa-file-export</span></li><li><i class="fas fa-file-image"></i><span class="icon-name">fas fa-file-image</span></li><li><i class="fas fa-file-import"></i><span class="icon-name">fas fa-file-import</span></li><li><i class="fas fa-file-invoice"></i><span class="icon-name">fas fa-file-invoice</span></li><li><i class="fas fa-file-invoice-dollar"></i><span class="icon-name">fas fa-file-invoice-dollar</span></li><li><i class="fas fa-file-medical"></i><span class="icon-name">fas fa-file-medical</span></li><li><i class="fas fa-file-medical-alt"></i><span class="icon-name">fas fa-file-medical-alt</span></li><li><i class="fas fa-file-pdf"></i><span class="icon-name">fas fa-file-pdf</span></li><li><i class="fas fa-file-powerpoint"></i><span class="icon-name">fas fa-file-powerpoint</span></li><li><i class="fas fa-file-prescription"></i><span class="icon-name">fas fa-file-prescription</span></li><li><i class="fas fa-file-signature"></i><span class="icon-name">fas fa-file-signature</span></li><li><i class="fas fa-file-upload"></i><span class="icon-name">fas fa-file-upload</span></li><li><i class="fas fa-file-video"></i><span class="icon-name">fas fa-file-video</span></li><li><i class="fas fa-file-word"></i><span class="icon-name">fas fa-file-word</span></li><li><i class="fas fa-fill"></i><span class="icon-name">fas fa-fill</span></li><li><i class="fas fa-fill-drip"></i><span class="icon-name">fas fa-fill-drip</span></li><li><i class="fas fa-film"></i><span class="icon-name">fas fa-film</span></li><li><i class="fas fa-filter"></i><span class="icon-name">fas fa-filter</span></li><li><i class="fas fa-fingerprint"></i><span class="icon-name">fas fa-fingerprint</span></li><li><i class="fas fa-fire"></i><span class="icon-name">fas fa-fire</span></li><li><i class="fas fa-fire-alt"></i><span class="icon-name">fas fa-fire-alt</span></li><li><i class="fas fa-fire-extinguisher"></i><span class="icon-name">fas fa-fire-extinguisher</span></li><li><i class="fas fa-first-aid"></i><span class="icon-name">fas fa-first-aid</span></li><li><i class="fas fa-fish"></i><span class="icon-name">fas fa-fish</span></li><li><i class="fas fa-fist-raised"></i><span class="icon-name">fas fa-fist-raised</span></li><li><i class="fas fa-flag"></i><span class="icon-name">fas fa-flag</span></li><li><i class="fas fa-flag-checkered"></i><span class="icon-name">fas fa-flag-checkered</span></li><li><i class="fas fa-flag-usa"></i><span class="icon-name">fas fa-flag-usa</span></li><li><i class="fas fa-flask"></i><span class="icon-name">fas fa-flask</span></li><li><i class="fas fa-flushed"></i><span class="icon-name">fas fa-flushed</span></li><li><i class="fas fa-folder"></i><span class="icon-name">fas fa-folder</span></li><li><i class="fas fa-folder-minus"></i><span class="icon-name">fas fa-folder-minus</span></li><li><i class="fas fa-folder-open"></i><span class="icon-name">fas fa-folder-open</span></li><li><i class="fas fa-folder-plus"></i><span class="icon-name">fas fa-folder-plus</span></li><li><i class="fas fa-font"></i><span class="icon-name">fas fa-font</span></li><li><i class="fas fa-football-ball"></i><span class="icon-name">fas fa-football-ball</span></li><li><i class="fas fa-forward"></i><span class="icon-name">fas fa-forward</span></li><li><i class="fas fa-frog"></i><span class="icon-name">fas fa-frog</span></li><li><i class="fas fa-frown"></i><span class="icon-name">fas fa-frown</span></li><li><i class="fas fa-frown-open"></i><span class="icon-name">fas fa-frown-open</span></li><li><i class="fas fa-funnel-dollar"></i><span class="icon-name">fas fa-funnel-dollar</span></li><li><i class="fas fa-futbol"></i><span class="icon-name">fas fa-futbol</span></li><li><i class="fas fa-gamepad"></i><span class="icon-name">fas fa-gamepad</span></li><li><i class="fas fa-gas-pump"></i><span class="icon-name">fas fa-gas-pump</span></li><li><i class="fas fa-gavel"></i><span class="icon-name">fas fa-gavel</span></li><li><i class="fas fa-gem"></i><span class="icon-name">fas fa-gem</span></li><li><i class="fas fa-genderless"></i><span class="icon-name">fas fa-genderless</span></li><li><i class="fas fa-ghost"></i><span class="icon-name">fas fa-ghost</span></li><li><i class="fas fa-gift"></i><span class="icon-name">fas fa-gift</span></li><li><i class="fas fa-gifts"></i><span class="icon-name">fas fa-gifts</span></li><li><i class="fas fa-glass-cheers"></i><span class="icon-name">fas fa-glass-cheers</span></li><li><i class="fas fa-glass-martini"></i><span class="icon-name">fas fa-glass-martini</span></li><li><i class="fas fa-glass-martini-alt"></i><span class="icon-name">fas fa-glass-martini-alt</span></li><li><i class="fas fa-glass-whiskey"></i><span class="icon-name">fas fa-glass-whiskey</span></li><li><i class="fas fa-glasses"></i><span class="icon-name">fas fa-glasses</span></li><li><i class="fas fa-globe"></i><span class="icon-name">fas fa-globe</span></li><li><i class="fas fa-globe-africa"></i><span class="icon-name">fas fa-globe-africa</span></li><li><i class="fas fa-globe-americas"></i><span class="icon-name">fas fa-globe-americas</span></li><li><i class="fas fa-globe-asia"></i><span class="icon-name">fas fa-globe-asia</span></li><li><i class="fas fa-globe-europe"></i><span class="icon-name">fas fa-globe-europe</span></li><li><i class="fas fa-golf-ball"></i><span class="icon-name">fas fa-golf-ball</span></li><li><i class="fas fa-gopuram"></i><span class="icon-name">fas fa-gopuram</span></li><li><i class="fas fa-graduation-cap"></i><span class="icon-name">fas fa-graduation-cap</span></li><li><i class="fas fa-greater-than"></i><span class="icon-name">fas fa-greater-than</span></li><li><i class="fas fa-greater-than-equal"></i><span class="icon-name">fas fa-greater-than-equal</span></li><li><i class="fas fa-grimace"></i><span class="icon-name">fas fa-grimace</span></li><li><i class="fas fa-grin"></i><span class="icon-name">fas fa-grin</span></li><li><i class="fas fa-grin-alt"></i><span class="icon-name">fas fa-grin-alt</span></li><li><i class="fas fa-grin-beam"></i><span class="icon-name">fas fa-grin-beam</span></li><li><i class="fas fa-grin-beam-sweat"></i><span class="icon-name">fas fa-grin-beam-sweat</span></li><li><i class="fas fa-grin-hearts"></i><span class="icon-name">fas fa-grin-hearts</span></li><li><i class="fas fa-grin-squint"></i><span class="icon-name">fas fa-grin-squint</span></li><li><i class="fas fa-grin-squint-tears"></i><span class="icon-name">fas fa-grin-squint-tears</span></li><li><i class="fas fa-grin-stars"></i><span class="icon-name">fas fa-grin-stars</span></li><li><i class="fas fa-grin-tears"></i><span class="icon-name">fas fa-grin-tears</span></li><li><i class="fas fa-grin-tongue"></i><span class="icon-name">fas fa-grin-tongue</span></li><li><i class="fas fa-grin-tongue-squint"></i><span class="icon-name">fas fa-grin-tongue-squint</span></li><li><i class="fas fa-grin-tongue-wink"></i><span class="icon-name">fas fa-grin-tongue-wink</span></li><li><i class="fas fa-grin-wink"></i><span class="icon-name">fas fa-grin-wink</span></li><li><i class="fas fa-grip-horizontal"></i><span class="icon-name">fas fa-grip-horizontal</span></li><li><i class="fas fa-grip-lines"></i><span class="icon-name">fas fa-grip-lines</span></li><li><i class="fas fa-grip-lines-vertical"></i><span class="icon-name">fas fa-grip-lines-vertical</span></li><li><i class="fas fa-grip-vertical"></i><span class="icon-name">fas fa-grip-vertical</span></li><li><i class="fas fa-guitar"></i><span class="icon-name">fas fa-guitar</span></li><li><i class="fas fa-h-square"></i><span class="icon-name">fas fa-h-square</span></li><li><i class="fas fa-hamburger"></i><span class="icon-name">fas fa-hamburger</span></li><li><i class="fas fa-hammer"></i><span class="icon-name">fas fa-hammer</span></li><li><i class="fas fa-hamsa"></i><span class="icon-name">fas fa-hamsa</span></li><li><i class="fas fa-hand-holding"></i><span class="icon-name">fas fa-hand-holding</span></li><li><i class="fas fa-hand-holding-heart"></i><span class="icon-name">fas fa-hand-holding-heart</span></li><li><i class="fas fa-hand-holding-usd"></i><span class="icon-name">fas fa-hand-holding-usd</span></li><li><i class="fas fa-hand-lizard"></i><span class="icon-name">fas fa-hand-lizard</span></li><li><i class="fas fa-hand-middle-finger"></i><span class="icon-name">fas fa-hand-middle-finger</span></li><li><i class="fas fa-hand-paper"></i><span class="icon-name">fas fa-hand-paper</span></li><li><i class="fas fa-hand-peace"></i><span class="icon-name">fas fa-hand-peace</span></li><li><i class="fas fa-hand-point-down"></i><span class="icon-name">fas fa-hand-point-down</span></li><li><i class="fas fa-hand-point-left"></i><span class="icon-name">fas fa-hand-point-left</span></li><li><i class="fas fa-hand-point-right"></i><span class="icon-name">fas fa-hand-point-right</span></li><li><i class="fas fa-hand-point-up"></i><span class="icon-name">fas fa-hand-point-up</span></li><li><i class="fas fa-hand-pointer"></i><span class="icon-name">fas fa-hand-pointer</span></li><li><i class="fas fa-hand-rock"></i><span class="icon-name">fas fa-hand-rock</span></li><li><i class="fas fa-hand-scissors"></i><span class="icon-name">fas fa-hand-scissors</span></li><li><i class="fas fa-hand-spock"></i><span class="icon-name">fas fa-hand-spock</span></li><li><i class="fas fa-hands"></i><span class="icon-name">fas fa-hands</span></li><li><i class="fas fa-hands-helping"></i><span class="icon-name">fas fa-hands-helping</span></li><li><i class="fas fa-handshake"></i><span class="icon-name">fas fa-handshake</span></li><li><i class="fas fa-hanukiah"></i><span class="icon-name">fas fa-hanukiah</span></li><li><i class="fas fa-hard-hat"></i><span class="icon-name">fas fa-hard-hat</span></li><li><i class="fas fa-hashtag"></i><span class="icon-name">fas fa-hashtag</span></li><li><i class="fas fa-hat-wizard"></i><span class="icon-name">fas fa-hat-wizard</span></li><li><i class="fas fa-haykal"></i><span class="icon-name">fas fa-haykal</span></li><li><i class="fas fa-hdd"></i><span class="icon-name">fas fa-hdd</span></li><li><i class="fas fa-heading"></i><span class="icon-name">fas fa-heading</span></li><li><i class="fas fa-headphones"></i><span class="icon-name">fas fa-headphones</span></li><li><i class="fas fa-headphones-alt"></i><span class="icon-name">fas fa-headphones-alt</span></li><li><i class="fas fa-headset"></i><span class="icon-name">fas fa-headset</span></li><li><i class="fas fa-heart"></i><span class="icon-name">fas fa-heart</span></li><li><i class="fas fa-heart-broken"></i><span class="icon-name">fas fa-heart-broken</span></li><li><i class="fas fa-heartbeat"></i><span class="icon-name">fas fa-heartbeat</span></li><li><i class="fas fa-helicopter"></i><span class="icon-name">fas fa-helicopter</span></li><li><i class="fas fa-highlighter"></i><span class="icon-name">fas fa-highlighter</span></li><li><i class="fas fa-hiking"></i><span class="icon-name">fas fa-hiking</span></li><li><i class="fas fa-hippo"></i><span class="icon-name">fas fa-hippo</span></li><li><i class="fas fa-history"></i><span class="icon-name">fas fa-history</span></li><li><i class="fas fa-hockey-puck"></i><span class="icon-name">fas fa-hockey-puck</span></li><li><i class="fas fa-holly-berry"></i><span class="icon-name">fas fa-holly-berry</span></li><li><i class="fas fa-home"></i><span class="icon-name">fas fa-home</span></li><li><i class="fas fa-horse"></i><span class="icon-name">fas fa-horse</span></li><li><i class="fas fa-horse-head"></i><span class="icon-name">fas fa-horse-head</span></li><li><i class="fas fa-hospital"></i><span class="icon-name">fas fa-hospital</span></li><li><i class="fas fa-hospital-alt"></i><span class="icon-name">fas fa-hospital-alt</span></li><li><i class="fas fa-hospital-symbol"></i><span class="icon-name">fas fa-hospital-symbol</span></li><li><i class="fas fa-hot-tub"></i><span class="icon-name">fas fa-hot-tub</span></li><li><i class="fas fa-hotdog"></i><span class="icon-name">fas fa-hotdog</span></li><li><i class="fas fa-hotel"></i><span class="icon-name">fas fa-hotel</span></li><li><i class="fas fa-hourglass"></i><span class="icon-name">fas fa-hourglass</span></li><li><i class="fas fa-hourglass-end"></i><span class="icon-name">fas fa-hourglass-end</span></li><li><i class="fas fa-hourglass-half"></i><span class="icon-name">fas fa-hourglass-half</span></li><li><i class="fas fa-hourglass-start"></i><span class="icon-name">fas fa-hourglass-start</span></li><li><i class="fas fa-house-damage"></i><span class="icon-name">fas fa-house-damage</span></li><li><i class="fas fa-hryvnia"></i><span class="icon-name">fas fa-hryvnia</span></li><li><i class="fas fa-i-cursor"></i><span class="icon-name">fas fa-i-cursor</span></li><li><i class="fas fa-ice-cream"></i><span class="icon-name">fas fa-ice-cream</span></li><li><i class="fas fa-icicles"></i><span class="icon-name">fas fa-icicles</span></li><li><i class="fas fa-icons"></i><span class="icon-name">fas fa-icons</span></li><li><i class="fas fa-id-badge"></i><span class="icon-name">fas fa-id-badge</span></li><li><i class="fas fa-id-card"></i><span class="icon-name">fas fa-id-card</span></li><li><i class="fas fa-id-card-alt"></i><span class="icon-name">fas fa-id-card-alt</span></li><li><i class="fas fa-igloo"></i><span class="icon-name">fas fa-igloo</span></li><li><i class="fas fa-image"></i><span class="icon-name">fas fa-image</span></li><li><i class="fas fa-images"></i><span class="icon-name">fas fa-images</span></li><li><i class="fas fa-inbox"></i><span class="icon-name">fas fa-inbox</span></li><li><i class="fas fa-indent"></i><span class="icon-name">fas fa-indent</span></li><li><i class="fas fa-industry"></i><span class="icon-name">fas fa-industry</span></li><li><i class="fas fa-infinity"></i><span class="icon-name">fas fa-infinity</span></li><li><i class="fas fa-info"></i><span class="icon-name">fas fa-info</span></li><li><i class="fas fa-info-circle"></i><span class="icon-name">fas fa-info-circle</span></li><li><i class="fas fa-italic"></i><span class="icon-name">fas fa-italic</span></li><li><i class="fas fa-jedi"></i><span class="icon-name">fas fa-jedi</span></li><li><i class="fas fa-joint"></i><span class="icon-name">fas fa-joint</span></li><li><i class="fas fa-journal-whills"></i><span class="icon-name">fas fa-journal-whills</span></li><li><i class="fas fa-kaaba"></i><span class="icon-name">fas fa-kaaba</span></li><li><i class="fas fa-key"></i><span class="icon-name">fas fa-key</span></li><li><i class="fas fa-keyboard"></i><span class="icon-name">fas fa-keyboard</span></li><li><i class="fas fa-khanda"></i><span class="icon-name">fas fa-khanda</span></li><li><i class="fas fa-kiss"></i><span class="icon-name">fas fa-kiss</span></li><li><i class="fas fa-kiss-beam"></i><span class="icon-name">fas fa-kiss-beam</span></li><li><i class="fas fa-kiss-wink-heart"></i><span class="icon-name">fas fa-kiss-wink-heart</span></li><li><i class="fas fa-kiwi-bird"></i><span class="icon-name">fas fa-kiwi-bird</span></li><li><i class="fas fa-landmark"></i><span class="icon-name">fas fa-landmark</span></li><li><i class="fas fa-language"></i><span class="icon-name">fas fa-language</span></li><li><i class="fas fa-laptop"></i><span class="icon-name">fas fa-laptop</span></li><li><i class="fas fa-laptop-code"></i><span class="icon-name">fas fa-laptop-code</span></li><li><i class="fas fa-laptop-medical"></i><span class="icon-name">fas fa-laptop-medical</span></li><li><i class="fas fa-laugh"></i><span class="icon-name">fas fa-laugh</span></li><li><i class="fas fa-laugh-beam"></i><span class="icon-name">fas fa-laugh-beam</span></li><li><i class="fas fa-laugh-squint"></i><span class="icon-name">fas fa-laugh-squint</span></li><li><i class="fas fa-laugh-wink"></i><span class="icon-name">fas fa-laugh-wink</span></li><li><i class="fas fa-layer-group"></i><span class="icon-name">fas fa-layer-group</span></li><li><i class="fas fa-leaf"></i><span class="icon-name">fas fa-leaf</span></li><li><i class="fas fa-lemon"></i><span class="icon-name">fas fa-lemon</span></li><li><i class="fas fa-less-than"></i><span class="icon-name">fas fa-less-than</span></li><li><i class="fas fa-less-than-equal"></i><span class="icon-name">fas fa-less-than-equal</span></li><li><i class="fas fa-level-down-alt"></i><span class="icon-name">fas fa-level-down-alt</span></li><li><i class="fas fa-level-up-alt"></i><span class="icon-name">fas fa-level-up-alt</span></li><li><i class="fas fa-life-ring"></i><span class="icon-name">fas fa-life-ring</span></li><li><i class="fas fa-lightbulb"></i><span class="icon-name">fas fa-lightbulb</span></li><li><i class="fas fa-link"></i><span class="icon-name">fas fa-link</span></li><li><i class="fas fa-lira-sign"></i><span class="icon-name">fas fa-lira-sign</span></li><li><i class="fas fa-list"></i><span class="icon-name">fas fa-list</span></li><li><i class="fas fa-list-alt"></i><span class="icon-name">fas fa-list-alt</span></li><li><i class="fas fa-list-ol"></i><span class="icon-name">fas fa-list-ol</span></li><li><i class="fas fa-list-ul"></i><span class="icon-name">fas fa-list-ul</span></li><li><i class="fas fa-location-arrow"></i><span class="icon-name">fas fa-location-arrow</span></li><li><i class="fas fa-lock"></i><span class="icon-name">fas fa-lock</span></li><li><i class="fas fa-lock-open"></i><span class="icon-name">fas fa-lock-open</span></li><li><i class="fas fa-long-arrow-alt-down"></i><span class="icon-name">fas fa-long-arrow-alt-down</span></li><li><i class="fas fa-long-arrow-alt-left"></i><span class="icon-name">fas fa-long-arrow-alt-left</span></li><li><i class="fas fa-long-arrow-alt-right"></i><span class="icon-name">fas fa-long-arrow-alt-right</span></li><li><i class="fas fa-long-arrow-alt-up"></i><span class="icon-name">fas fa-long-arrow-alt-up</span></li><li><i class="fas fa-low-vision"></i><span class="icon-name">fas fa-low-vision</span></li><li><i class="fas fa-luggage-cart"></i><span class="icon-name">fas fa-luggage-cart</span></li><li><i class="fas fa-magic"></i><span class="icon-name">fas fa-magic</span></li><li><i class="fas fa-magnet"></i><span class="icon-name">fas fa-magnet</span></li><li><i class="fas fa-mail-bulk"></i><span class="icon-name">fas fa-mail-bulk</span></li><li><i class="fas fa-male"></i><span class="icon-name">fas fa-male</span></li><li><i class="fas fa-map"></i><span class="icon-name">fas fa-map</span></li><li><i class="fas fa-map-marked"></i><span class="icon-name">fas fa-map-marked</span></li><li><i class="fas fa-map-marked-alt"></i><span class="icon-name">fas fa-map-marked-alt</span></li><li><i class="fas fa-map-marker"></i><span class="icon-name">fas fa-map-marker</span></li><li><i class="fas fa-map-marker-alt"></i><span class="icon-name">fas fa-map-marker-alt</span></li><li><i class="fas fa-map-pin"></i><span class="icon-name">fas fa-map-pin</span></li><li><i class="fas fa-map-signs"></i><span class="icon-name">fas fa-map-signs</span></li><li><i class="fas fa-marker"></i><span class="icon-name">fas fa-marker</span></li><li><i class="fas fa-mars"></i><span class="icon-name">fas fa-mars</span></li><li><i class="fas fa-mars-double"></i><span class="icon-name">fas fa-mars-double</span></li><li><i class="fas fa-mars-stroke"></i><span class="icon-name">fas fa-mars-stroke</span></li><li><i class="fas fa-mars-stroke-h"></i><span class="icon-name">fas fa-mars-stroke-h</span></li><li><i class="fas fa-mars-stroke-v"></i><span class="icon-name">fas fa-mars-stroke-v</span></li><li><i class="fas fa-mask"></i><span class="icon-name">fas fa-mask</span></li><li><i class="fas fa-medal"></i><span class="icon-name">fas fa-medal</span></li><li><i class="fas fa-medkit"></i><span class="icon-name">fas fa-medkit</span></li><li><i class="fas fa-meh"></i><span class="icon-name">fas fa-meh</span></li><li><i class="fas fa-meh-blank"></i><span class="icon-name">fas fa-meh-blank</span></li><li><i class="fas fa-meh-rolling-eyes"></i><span class="icon-name">fas fa-meh-rolling-eyes</span></li><li><i class="fas fa-memory"></i><span class="icon-name">fas fa-memory</span></li><li><i class="fas fa-menorah"></i><span class="icon-name">fas fa-menorah</span></li><li><i class="fas fa-mercury"></i><span class="icon-name">fas fa-mercury</span></li><li><i class="fas fa-meteor"></i><span class="icon-name">fas fa-meteor</span></li><li><i class="fas fa-microchip"></i><span class="icon-name">fas fa-microchip</span></li><li><i class="fas fa-microphone"></i><span class="icon-name">fas fa-microphone</span></li><li><i class="fas fa-microphone-alt"></i><span class="icon-name">fas fa-microphone-alt</span></li><li><i class="fas fa-microphone-alt-slash"></i><span class="icon-name">fas fa-microphone-alt-slash</span></li><li><i class="fas fa-microphone-slash"></i><span class="icon-name">fas fa-microphone-slash</span></li><li><i class="fas fa-microscope"></i><span class="icon-name">fas fa-microscope</span></li><li><i class="fas fa-minus"></i><span class="icon-name">fas fa-minus</span></li><li><i class="fas fa-minus-circle"></i><span class="icon-name">fas fa-minus-circle</span></li><li><i class="fas fa-minus-square"></i><span class="icon-name">fas fa-minus-square</span></li><li><i class="fas fa-mitten"></i><span class="icon-name">fas fa-mitten</span></li><li><i class="fas fa-mobile"></i><span class="icon-name">fas fa-mobile</span></li><li><i class="fas fa-mobile-alt"></i><span class="icon-name">fas fa-mobile-alt</span></li><li><i class="fas fa-money-bill"></i><span class="icon-name">fas fa-money-bill</span></li><li><i class="fas fa-money-bill-alt"></i><span class="icon-name">fas fa-money-bill-alt</span></li><li><i class="fas fa-money-bill-wave"></i><span class="icon-name">fas fa-money-bill-wave</span></li><li><i class="fas fa-money-bill-wave-alt"></i><span class="icon-name">fas fa-money-bill-wave-alt</span></li><li><i class="fas fa-money-check"></i><span class="icon-name">fas fa-money-check</span></li><li><i class="fas fa-money-check-alt"></i><span class="icon-name">fas fa-money-check-alt</span></li><li><i class="fas fa-monument"></i><span class="icon-name">fas fa-monument</span></li><li><i class="fas fa-moon"></i><span class="icon-name">fas fa-moon</span></li><li><i class="fas fa-mortar-pestle"></i><span class="icon-name">fas fa-mortar-pestle</span></li><li><i class="fas fa-mosque"></i><span class="icon-name">fas fa-mosque</span></li><li><i class="fas fa-motorcycle"></i><span class="icon-name">fas fa-motorcycle</span></li><li><i class="fas fa-mountain"></i><span class="icon-name">fas fa-mountain</span></li><li><i class="fas fa-mouse-pointer"></i><span class="icon-name">fas fa-mouse-pointer</span></li><li><i class="fas fa-mug-hot"></i><span class="icon-name">fas fa-mug-hot</span></li><li><i class="fas fa-music"></i><span class="icon-name">fas fa-music</span></li><li><i class="fas fa-network-wired"></i><span class="icon-name">fas fa-network-wired</span></li><li><i class="fas fa-neuter"></i><span class="icon-name">fas fa-neuter</span></li><li><i class="fas fa-newspaper"></i><span class="icon-name">fas fa-newspaper</span></li><li><i class="fas fa-not-equal"></i><span class="icon-name">fas fa-not-equal</span></li><li><i class="fas fa-notes-medical"></i><span class="icon-name">fas fa-notes-medical</span></li><li><i class="fas fa-object-group"></i><span class="icon-name">fas fa-object-group</span></li><li><i class="fas fa-object-ungroup"></i><span class="icon-name">fas fa-object-ungroup</span></li><li><i class="fas fa-oil-can"></i><span class="icon-name">fas fa-oil-can</span></li><li><i class="fas fa-om"></i><span class="icon-name">fas fa-om</span></li><li><i class="fas fa-otter"></i><span class="icon-name">fas fa-otter</span></li><li><i class="fas fa-outdent"></i><span class="icon-name">fas fa-outdent</span></li><li><i class="fas fa-pager"></i><span class="icon-name">fas fa-pager</span></li><li><i class="fas fa-paint-brush"></i><span class="icon-name">fas fa-paint-brush</span></li><li><i class="fas fa-paint-roller"></i><span class="icon-name">fas fa-paint-roller</span></li><li><i class="fas fa-palette"></i><span class="icon-name">fas fa-palette</span></li><li><i class="fas fa-pallet"></i><span class="icon-name">fas fa-pallet</span></li><li><i class="fas fa-paper-plane"></i><span class="icon-name">fas fa-paper-plane</span></li><li><i class="fas fa-paperclip"></i><span class="icon-name">fas fa-paperclip</span></li><li><i class="fas fa-parachute-box"></i><span class="icon-name">fas fa-parachute-box</span></li><li><i class="fas fa-paragraph"></i><span class="icon-name">fas fa-paragraph</span></li><li><i class="fas fa-parking"></i><span class="icon-name">fas fa-parking</span></li><li><i class="fas fa-passport"></i><span class="icon-name">fas fa-passport</span></li><li><i class="fas fa-pastafarianism"></i><span class="icon-name">fas fa-pastafarianism</span></li><li><i class="fas fa-paste"></i><span class="icon-name">fas fa-paste</span></li><li><i class="fas fa-pause"></i><span class="icon-name">fas fa-pause</span></li><li><i class="fas fa-pause-circle"></i><span class="icon-name">fas fa-pause-circle</span></li><li><i class="fas fa-paw"></i><span class="icon-name">fas fa-paw</span></li><li><i class="fas fa-peace"></i><span class="icon-name">fas fa-peace</span></li><li><i class="fas fa-pen"></i><span class="icon-name">fas fa-pen</span></li><li><i class="fas fa-pen-alt"></i><span class="icon-name">fas fa-pen-alt</span></li><li><i class="fas fa-pen-fancy"></i><span class="icon-name">fas fa-pen-fancy</span></li><li><i class="fas fa-pen-nib"></i><span class="icon-name">fas fa-pen-nib</span></li><li><i class="fas fa-pen-square"></i><span class="icon-name">fas fa-pen-square</span></li><li><i class="fas fa-pencil-alt"></i><span class="icon-name">fas fa-pencil-alt</span></li><li><i class="fas fa-pencil-ruler"></i><span class="icon-name">fas fa-pencil-ruler</span></li><li><i class="fas fa-people-carry"></i><span class="icon-name">fas fa-people-carry</span></li><li><i class="fas fa-pepper-hot"></i><span class="icon-name">fas fa-pepper-hot</span></li><li><i class="fas fa-percent"></i><span class="icon-name">fas fa-percent</span></li><li><i class="fas fa-percentage"></i><span class="icon-name">fas fa-percentage</span></li><li><i class="fas fa-person-booth"></i><span class="icon-name">fas fa-person-booth</span></li><li><i class="fas fa-phone"></i><span class="icon-name">fas fa-phone</span></li><li><i class="fas fa-phone-alt"></i><span class="icon-name">fas fa-phone-alt</span></li><li><i class="fas fa-phone-slash"></i><span class="icon-name">fas fa-phone-slash</span></li><li><i class="fas fa-phone-square"></i><span class="icon-name">fas fa-phone-square</span></li><li><i class="fas fa-phone-square-alt"></i><span class="icon-name">fas fa-phone-square-alt</span></li><li><i class="fas fa-phone-volume"></i><span class="icon-name">fas fa-phone-volume</span></li><li><i class="fas fa-photo-video"></i><span class="icon-name">fas fa-photo-video</span></li><li><i class="fas fa-piggy-bank"></i><span class="icon-name">fas fa-piggy-bank</span></li><li><i class="fas fa-pills"></i><span class="icon-name">fas fa-pills</span></li><li><i class="fas fa-pizza-slice"></i><span class="icon-name">fas fa-pizza-slice</span></li><li><i class="fas fa-place-of-worship"></i><span class="icon-name">fas fa-place-of-worship</span></li><li><i class="fas fa-plane"></i><span class="icon-name">fas fa-plane</span></li><li><i class="fas fa-plane-arrival"></i><span class="icon-name">fas fa-plane-arrival</span></li><li><i class="fas fa-plane-departure"></i><span class="icon-name">fas fa-plane-departure</span></li><li><i class="fas fa-play"></i><span class="icon-name">fas fa-play</span></li><li><i class="fas fa-play-circle"></i><span class="icon-name">fas fa-play-circle</span></li><li><i class="fas fa-plug"></i><span class="icon-name">fas fa-plug</span></li><li><i class="fas fa-plus"></i><span class="icon-name">fas fa-plus</span></li><li><i class="fas fa-plus-circle"></i><span class="icon-name">fas fa-plus-circle</span></li><li><i class="fas fa-plus-square"></i><span class="icon-name">fas fa-plus-square</span></li><li><i class="fas fa-podcast"></i><span class="icon-name">fas fa-podcast</span></li><li><i class="fas fa-poll"></i><span class="icon-name">fas fa-poll</span></li><li><i class="fas fa-poll-h"></i><span class="icon-name">fas fa-poll-h</span></li><li><i class="fas fa-poo"></i><span class="icon-name">fas fa-poo</span></li><li><i class="fas fa-poo-storm"></i><span class="icon-name">fas fa-poo-storm</span></li><li><i class="fas fa-poop"></i><span class="icon-name">fas fa-poop</span></li><li><i class="fas fa-portrait"></i><span class="icon-name">fas fa-portrait</span></li><li><i class="fas fa-pound-sign"></i><span class="icon-name">fas fa-pound-sign</span></li><li><i class="fas fa-power-off"></i><span class="icon-name">fas fa-power-off</span></li><li><i class="fas fa-pray"></i><span class="icon-name">fas fa-pray</span></li><li><i class="fas fa-praying-hands"></i><span class="icon-name">fas fa-praying-hands</span></li><li><i class="fas fa-prescription"></i><span class="icon-name">fas fa-prescription</span></li><li><i class="fas fa-prescription-bottle"></i><span class="icon-name">fas fa-prescription-bottle</span></li><li><i class="fas fa-prescription-bottle-alt"></i><span class="icon-name">fas fa-prescription-bottle-alt</span></li><li><i class="fas fa-print"></i><span class="icon-name">fas fa-print</span></li><li><i class="fas fa-procedures"></i><span class="icon-name">fas fa-procedures</span></li><li><i class="fas fa-project-diagram"></i><span class="icon-name">fas fa-project-diagram</span></li><li><i class="fas fa-puzzle-piece"></i><span class="icon-name">fas fa-puzzle-piece</span></li><li><i class="fas fa-qrcode"></i><span class="icon-name">fas fa-qrcode</span></li><li><i class="fas fa-question"></i><span class="icon-name">fas fa-question</span></li><li><i class="fas fa-question-circle"></i><span class="icon-name">fas fa-question-circle</span></li><li><i class="fas fa-quidditch"></i><span class="icon-name">fas fa-quidditch</span></li><li><i class="fas fa-quote-left"></i><span class="icon-name">fas fa-quote-left</span></li><li><i class="fas fa-quote-right"></i><span class="icon-name">fas fa-quote-right</span></li><li><i class="fas fa-quran"></i><span class="icon-name">fas fa-quran</span></li><li><i class="fas fa-radiation"></i><span class="icon-name">fas fa-radiation</span></li><li><i class="fas fa-radiation-alt"></i><span class="icon-name">fas fa-radiation-alt</span></li><li><i class="fas fa-rainbow"></i><span class="icon-name">fas fa-rainbow</span></li><li><i class="fas fa-random"></i><span class="icon-name">fas fa-random</span></li><li><i class="fas fa-receipt"></i><span class="icon-name">fas fa-receipt</span></li><li><i class="fas fa-recycle"></i><span class="icon-name">fas fa-recycle</span></li><li><i class="fas fa-redo"></i><span class="icon-name">fas fa-redo</span></li><li><i class="fas fa-redo-alt"></i><span class="icon-name">fas fa-redo-alt</span></li><li><i class="fas fa-registered"></i><span class="icon-name">fas fa-registered</span></li><li><i class="fas fa-remove-format"></i><span class="icon-name">fas fa-remove-format</span></li><li><i class="fas fa-reply"></i><span class="icon-name">fas fa-reply</span></li><li><i class="fas fa-reply-all"></i><span class="icon-name">fas fa-reply-all</span></li><li><i class="fas fa-republican"></i><span class="icon-name">fas fa-republican</span></li><li><i class="fas fa-restroom"></i><span class="icon-name">fas fa-restroom</span></li><li><i class="fas fa-retweet"></i><span class="icon-name">fas fa-retweet</span></li><li><i class="fas fa-ribbon"></i><span class="icon-name">fas fa-ribbon</span></li><li><i class="fas fa-ring"></i><span class="icon-name">fas fa-ring</span></li><li><i class="fas fa-road"></i><span class="icon-name">fas fa-road</span></li><li><i class="fas fa-robot"></i><span class="icon-name">fas fa-robot</span></li><li><i class="fas fa-rocket"></i><span class="icon-name">fas fa-rocket</span></li><li><i class="fas fa-route"></i><span class="icon-name">fas fa-route</span></li><li><i class="fas fa-rss"></i><span class="icon-name">fas fa-rss</span></li><li><i class="fas fa-rss-square"></i><span class="icon-name">fas fa-rss-square</span></li><li><i class="fas fa-ruble-sign"></i><span class="icon-name">fas fa-ruble-sign</span></li><li><i class="fas fa-ruler"></i><span class="icon-name">fas fa-ruler</span></li><li><i class="fas fa-ruler-combined"></i><span class="icon-name">fas fa-ruler-combined</span></li><li><i class="fas fa-ruler-horizontal"></i><span class="icon-name">fas fa-ruler-horizontal</span></li><li><i class="fas fa-ruler-vertical"></i><span class="icon-name">fas fa-ruler-vertical</span></li><li><i class="fas fa-running"></i><span class="icon-name">fas fa-running</span></li><li><i class="fas fa-rupee-sign"></i><span class="icon-name">fas fa-rupee-sign</span></li><li><i class="fas fa-sad-cry"></i><span class="icon-name">fas fa-sad-cry</span></li><li><i class="fas fa-sad-tear"></i><span class="icon-name">fas fa-sad-tear</span></li><li><i class="fas fa-satellite"></i><span class="icon-name">fas fa-satellite</span></li><li><i class="fas fa-satellite-dish"></i><span class="icon-name">fas fa-satellite-dish</span></li><li><i class="fas fa-save"></i><span class="icon-name">fas fa-save</span></li><li><i class="fas fa-school"></i><span class="icon-name">fas fa-school</span></li><li><i class="fas fa-screwdriver"></i><span class="icon-name">fas fa-screwdriver</span></li><li><i class="fas fa-scroll"></i><span class="icon-name">fas fa-scroll</span></li><li><i class="fas fa-sd-card"></i><span class="icon-name">fas fa-sd-card</span></li><li><i class="fas fa-search"></i><span class="icon-name">fas fa-search</span></li><li><i class="fas fa-search-dollar"></i><span class="icon-name">fas fa-search-dollar</span></li><li><i class="fas fa-search-location"></i><span class="icon-name">fas fa-search-location</span></li><li><i class="fas fa-search-minus"></i><span class="icon-name">fas fa-search-minus</span></li><li><i class="fas fa-search-plus"></i><span class="icon-name">fas fa-search-plus</span></li><li><i class="fas fa-seedling"></i><span class="icon-name">fas fa-seedling</span></li><li><i class="fas fa-server"></i><span class="icon-name">fas fa-server</span></li><li><i class="fas fa-shapes"></i><span class="icon-name">fas fa-shapes</span></li><li><i class="fas fa-share"></i><span class="icon-name">fas fa-share</span></li><li><i class="fas fa-share-alt"></i><span class="icon-name">fas fa-share-alt</span></li><li><i class="fas fa-share-alt-square"></i><span class="icon-name">fas fa-share-alt-square</span></li><li><i class="fas fa-share-square"></i><span class="icon-name">fas fa-share-square</span></li><li><i class="fas fa-shekel-sign"></i><span class="icon-name">fas fa-shekel-sign</span></li><li><i class="fas fa-shield-alt"></i><span class="icon-name">fas fa-shield-alt</span></li><li><i class="fas fa-ship"></i><span class="icon-name">fas fa-ship</span></li><li><i class="fas fa-shipping-fast"></i><span class="icon-name">fas fa-shipping-fast</span></li><li><i class="fas fa-shoe-prints"></i><span class="icon-name">fas fa-shoe-prints</span></li><li><i class="fas fa-shopping-bag"></i><span class="icon-name">fas fa-shopping-bag</span></li><li><i class="fas fa-shopping-basket"></i><span class="icon-name">fas fa-shopping-basket</span></li><li><i class="fas fa-shopping-cart"></i><span class="icon-name">fas fa-shopping-cart</span></li><li><i class="fas fa-shower"></i><span class="icon-name">fas fa-shower</span></li><li><i class="fas fa-shuttle-van"></i><span class="icon-name">fas fa-shuttle-van</span></li><li><i class="fas fa-sign"></i><span class="icon-name">fas fa-sign</span></li><li><i class="fas fa-sign-in-alt"></i><span class="icon-name">fas fa-sign-in-alt</span></li><li><i class="fas fa-sign-language"></i><span class="icon-name">fas fa-sign-language</span></li><li><i class="fas fa-sign-out-alt"></i><span class="icon-name">fas fa-sign-out-alt</span></li><li><i class="fas fa-signal"></i><span class="icon-name">fas fa-signal</span></li><li><i class="fas fa-signature"></i><span class="icon-name">fas fa-signature</span></li><li><i class="fas fa-sim-card"></i><span class="icon-name">fas fa-sim-card</span></li><li><i class="fas fa-sitemap"></i><span class="icon-name">fas fa-sitemap</span></li><li><i class="fas fa-skating"></i><span class="icon-name">fas fa-skating</span></li><li><i class="fas fa-skiing"></i><span class="icon-name">fas fa-skiing</span></li><li><i class="fas fa-skiing-nordic"></i><span class="icon-name">fas fa-skiing-nordic</span></li><li><i class="fas fa-skull"></i><span class="icon-name">fas fa-skull</span></li><li><i class="fas fa-skull-crossbones"></i><span class="icon-name">fas fa-skull-crossbones</span></li><li><i class="fas fa-slash"></i><span class="icon-name">fas fa-slash</span></li><li><i class="fas fa-sleigh"></i><span class="icon-name">fas fa-sleigh</span></li><li><i class="fas fa-sliders-h"></i><span class="icon-name">fas fa-sliders-h</span></li><li><i class="fas fa-smile"></i><span class="icon-name">fas fa-smile</span></li><li><i class="fas fa-smile-beam"></i><span class="icon-name">fas fa-smile-beam</span></li><li><i class="fas fa-smile-wink"></i><span class="icon-name">fas fa-smile-wink</span></li><li><i class="fas fa-smog"></i><span class="icon-name">fas fa-smog</span></li><li><i class="fas fa-smoking"></i><span class="icon-name">fas fa-smoking</span></li><li><i class="fas fa-smoking-ban"></i><span class="icon-name">fas fa-smoking-ban</span></li><li><i class="fas fa-sms"></i><span class="icon-name">fas fa-sms</span></li><li><i class="fas fa-snowboarding"></i><span class="icon-name">fas fa-snowboarding</span></li><li><i class="fas fa-snowflake"></i><span class="icon-name">fas fa-snowflake</span></li><li><i class="fas fa-snowman"></i><span class="icon-name">fas fa-snowman</span></li><li><i class="fas fa-snowplow"></i><span class="icon-name">fas fa-snowplow</span></li><li><i class="fas fa-socks"></i><span class="icon-name">fas fa-socks</span></li><li><i class="fas fa-solar-panel"></i><span class="icon-name">fas fa-solar-panel</span></li><li><i class="fas fa-sort"></i><span class="icon-name">fas fa-sort</span></li><li><i class="fas fa-sort-alpha-down"></i><span class="icon-name">fas fa-sort-alpha-down</span></li><li><i class="fas fa-sort-alpha-down-alt"></i><span class="icon-name">fas fa-sort-alpha-down-alt</span></li><li><i class="fas fa-sort-alpha-up"></i><span class="icon-name">fas fa-sort-alpha-up</span></li><li><i class="fas fa-sort-alpha-up-alt"></i><span class="icon-name">fas fa-sort-alpha-up-alt</span></li><li><i class="fas fa-sort-amount-down"></i><span class="icon-name">fas fa-sort-amount-down</span></li><li><i class="fas fa-sort-amount-down-alt"></i><span class="icon-name">fas fa-sort-amount-down-alt</span></li><li><i class="fas fa-sort-amount-up"></i><span class="icon-name">fas fa-sort-amount-up</span></li><li><i class="fas fa-sort-amount-up-alt"></i><span class="icon-name">fas fa-sort-amount-up-alt</span></li><li><i class="fas fa-sort-down"></i><span class="icon-name">fas fa-sort-down</span></li><li><i class="fas fa-sort-numeric-down"></i><span class="icon-name">fas fa-sort-numeric-down</span></li><li><i class="fas fa-sort-numeric-down-alt"></i><span class="icon-name">fas fa-sort-numeric-down-alt</span></li><li><i class="fas fa-sort-numeric-up"></i><span class="icon-name">fas fa-sort-numeric-up</span></li><li><i class="fas fa-sort-numeric-up-alt"></i><span class="icon-name">fas fa-sort-numeric-up-alt</span></li><li><i class="fas fa-sort-up"></i><span class="icon-name">fas fa-sort-up</span></li><li><i class="fas fa-spa"></i><span class="icon-name">fas fa-spa</span></li><li><i class="fas fa-space-shuttle"></i><span class="icon-name">fas fa-space-shuttle</span></li><li><i class="fas fa-spell-check"></i><span class="icon-name">fas fa-spell-check</span></li><li><i class="fas fa-spider"></i><span class="icon-name">fas fa-spider</span></li><li><i class="fas fa-spinner"></i><span class="icon-name">fas fa-spinner</span></li><li><i class="fas fa-splotch"></i><span class="icon-name">fas fa-splotch</span></li><li><i class="fas fa-spray-can"></i><span class="icon-name">fas fa-spray-can</span></li><li><i class="fas fa-square"></i><span class="icon-name">fas fa-square</span></li><li><i class="fas fa-square-full"></i><span class="icon-name">fas fa-square-full</span></li><li><i class="fas fa-square-root-alt"></i><span class="icon-name">fas fa-square-root-alt</span></li><li><i class="fas fa-stamp"></i><span class="icon-name">fas fa-stamp</span></li><li><i class="fas fa-star"></i><span class="icon-name">fas fa-star</span></li><li><i class="fas fa-star-and-crescent"></i><span class="icon-name">fas fa-star-and-crescent</span></li><li><i class="fas fa-star-half"></i><span class="icon-name">fas fa-star-half</span></li><li><i class="fas fa-star-half-alt"></i><span class="icon-name">fas fa-star-half-alt</span></li><li><i class="fas fa-star-of-david"></i><span class="icon-name">fas fa-star-of-david</span></li><li><i class="fas fa-star-of-life"></i><span class="icon-name">fas fa-star-of-life</span></li><li><i class="fas fa-step-backward"></i><span class="icon-name">fas fa-step-backward</span></li><li><i class="fas fa-step-forward"></i><span class="icon-name">fas fa-step-forward</span></li><li><i class="fas fa-stethoscope"></i><span class="icon-name">fas fa-stethoscope</span></li><li><i class="fas fa-sticky-note"></i><span class="icon-name">fas fa-sticky-note</span></li><li><i class="fas fa-stop"></i><span class="icon-name">fas fa-stop</span></li><li><i class="fas fa-stop-circle"></i><span class="icon-name">fas fa-stop-circle</span></li><li><i class="fas fa-stopwatch"></i><span class="icon-name">fas fa-stopwatch</span></li><li><i class="fas fa-store"></i><span class="icon-name">fas fa-store</span></li><li><i class="fas fa-store-alt"></i><span class="icon-name">fas fa-store-alt</span></li><li><i class="fas fa-stream"></i><span class="icon-name">fas fa-stream</span></li><li><i class="fas fa-street-view"></i><span class="icon-name">fas fa-street-view</span></li><li><i class="fas fa-strikethrough"></i><span class="icon-name">fas fa-strikethrough</span></li><li><i class="fas fa-stroopwafel"></i><span class="icon-name">fas fa-stroopwafel</span></li><li><i class="fas fa-subscript"></i><span class="icon-name">fas fa-subscript</span></li><li><i class="fas fa-subway"></i><span class="icon-name">fas fa-subway</span></li><li><i class="fas fa-suitcase"></i><span class="icon-name">fas fa-suitcase</span></li><li><i class="fas fa-suitcase-rolling"></i><span class="icon-name">fas fa-suitcase-rolling</span></li><li><i class="fas fa-sun"></i><span class="icon-name">fas fa-sun</span></li><li><i class="fas fa-superscript"></i><span class="icon-name">fas fa-superscript</span></li><li><i class="fas fa-surprise"></i><span class="icon-name">fas fa-surprise</span></li><li><i class="fas fa-swatchbook"></i><span class="icon-name">fas fa-swatchbook</span></li><li><i class="fas fa-swimmer"></i><span class="icon-name">fas fa-swimmer</span></li><li><i class="fas fa-swimming-pool"></i><span class="icon-name">fas fa-swimming-pool</span></li><li><i class="fas fa-synagogue"></i><span class="icon-name">fas fa-synagogue</span></li><li><i class="fas fa-sync"></i><span class="icon-name">fas fa-sync</span></li><li><i class="fas fa-sync-alt"></i><span class="icon-name">fas fa-sync-alt</span></li><li><i class="fas fa-syringe"></i><span class="icon-name">fas fa-syringe</span></li><li><i class="fas fa-table"></i><span class="icon-name">fas fa-table</span></li><li><i class="fas fa-table-tennis"></i><span class="icon-name">fas fa-table-tennis</span></li><li><i class="fas fa-tablet"></i><span class="icon-name">fas fa-tablet</span></li><li><i class="fas fa-tablet-alt"></i><span class="icon-name">fas fa-tablet-alt</span></li><li><i class="fas fa-tablets"></i><span class="icon-name">fas fa-tablets</span></li><li><i class="fas fa-tachometer-alt"></i><span class="icon-name">fas fa-tachometer-alt</span></li><li><i class="fas fa-tag"></i><span class="icon-name">fas fa-tag</span></li><li><i class="fas fa-tags"></i><span class="icon-name">fas fa-tags</span></li><li><i class="fas fa-tape"></i><span class="icon-name">fas fa-tape</span></li><li><i class="fas fa-tasks"></i><span class="icon-name">fas fa-tasks</span></li><li><i class="fas fa-taxi"></i><span class="icon-name">fas fa-taxi</span></li><li><i class="fas fa-teeth"></i><span class="icon-name">fas fa-teeth</span></li><li><i class="fas fa-teeth-open"></i><span class="icon-name">fas fa-teeth-open</span></li><li><i class="fas fa-temperature-high"></i><span class="icon-name">fas fa-temperature-high</span></li><li><i class="fas fa-temperature-low"></i><span class="icon-name">fas fa-temperature-low</span></li><li><i class="fas fa-tenge"></i><span class="icon-name">fas fa-tenge</span></li><li><i class="fas fa-terminal"></i><span class="icon-name">fas fa-terminal</span></li><li><i class="fas fa-text-height"></i><span class="icon-name">fas fa-text-height</span></li><li><i class="fas fa-text-width"></i><span class="icon-name">fas fa-text-width</span></li><li><i class="fas fa-th"></i><span class="icon-name">fas fa-th</span></li><li><i class="fas fa-th-large"></i><span class="icon-name">fas fa-th-large</span></li><li><i class="fas fa-th-list"></i><span class="icon-name">fas fa-th-list</span></li><li><i class="fas fa-theater-masks"></i><span class="icon-name">fas fa-theater-masks</span></li><li><i class="fas fa-thermometer"></i><span class="icon-name">fas fa-thermometer</span></li><li><i class="fas fa-thermometer-empty"></i><span class="icon-name">fas fa-thermometer-empty</span></li><li><i class="fas fa-thermometer-full"></i><span class="icon-name">fas fa-thermometer-full</span></li><li><i class="fas fa-thermometer-half"></i><span class="icon-name">fas fa-thermometer-half</span></li><li><i class="fas fa-thermometer-quarter"></i><span class="icon-name">fas fa-thermometer-quarter</span></li><li><i class="fas fa-thermometer-three-quarters"></i><span class="icon-name">fas fa-thermometer-three-quarters</span></li><li><i class="fas fa-thumbs-down"></i><span class="icon-name">fas fa-thumbs-down</span></li><li><i class="fas fa-thumbs-up"></i><span class="icon-name">fas fa-thumbs-up</span></li><li><i class="fas fa-thumbtack"></i><span class="icon-name">fas fa-thumbtack</span></li><li><i class="fas fa-ticket-alt"></i><span class="icon-name">fas fa-ticket-alt</span></li><li><i class="fas fa-times"></i><span class="icon-name">fas fa-times</span></li><li><i class="fas fa-times-circle"></i><span class="icon-name">fas fa-times-circle</span></li><li><i class="fas fa-tint"></i><span class="icon-name">fas fa-tint</span></li><li><i class="fas fa-tint-slash"></i><span class="icon-name">fas fa-tint-slash</span></li><li><i class="fas fa-tired"></i><span class="icon-name">fas fa-tired</span></li><li><i class="fas fa-toggle-off"></i><span class="icon-name">fas fa-toggle-off</span></li><li><i class="fas fa-toggle-on"></i><span class="icon-name">fas fa-toggle-on</span></li><li><i class="fas fa-toilet"></i><span class="icon-name">fas fa-toilet</span></li><li><i class="fas fa-toilet-paper"></i><span class="icon-name">fas fa-toilet-paper</span></li><li><i class="fas fa-toolbox"></i><span class="icon-name">fas fa-toolbox</span></li><li><i class="fas fa-tools"></i><span class="icon-name">fas fa-tools</span></li><li><i class="fas fa-tooth"></i><span class="icon-name">fas fa-tooth</span></li><li><i class="fas fa-torah"></i><span class="icon-name">fas fa-torah</span></li><li><i class="fas fa-torii-gate"></i><span class="icon-name">fas fa-torii-gate</span></li><li><i class="fas fa-tractor"></i><span class="icon-name">fas fa-tractor</span></li><li><i class="fas fa-trademark"></i><span class="icon-name">fas fa-trademark</span></li><li><i class="fas fa-traffic-light"></i><span class="icon-name">fas fa-traffic-light</span></li><li><i class="fas fa-train"></i><span class="icon-name">fas fa-train</span></li><li><i class="fas fa-tram"></i><span class="icon-name">fas fa-tram</span></li><li><i class="fas fa-transgender"></i><span class="icon-name">fas fa-transgender</span></li><li><i class="fas fa-transgender-alt"></i><span class="icon-name">fas fa-transgender-alt</span></li><li><i class="fas fa-trash"></i><span class="icon-name">fas fa-trash</span></li><li><i class="fas fa-trash-alt"></i><span class="icon-name">fas fa-trash-alt</span></li><li><i class="fas fa-trash-restore"></i><span class="icon-name">fas fa-trash-restore</span></li><li><i class="fas fa-trash-restore-alt"></i><span class="icon-name">fas fa-trash-restore-alt</span></li><li><i class="fas fa-tree"></i><span class="icon-name">fas fa-tree</span></li><li><i class="fas fa-trophy"></i><span class="icon-name">fas fa-trophy</span></li><li><i class="fas fa-truck"></i><span class="icon-name">fas fa-truck</span></li><li><i class="fas fa-truck-loading"></i><span class="icon-name">fas fa-truck-loading</span></li><li><i class="fas fa-truck-monster"></i><span class="icon-name">fas fa-truck-monster</span></li><li><i class="fas fa-truck-moving"></i><span class="icon-name">fas fa-truck-moving</span></li><li><i class="fas fa-truck-pickup"></i><span class="icon-name">fas fa-truck-pickup</span></li><li><i class="fas fa-tshirt"></i><span class="icon-name">fas fa-tshirt</span></li><li><i class="fas fa-tty"></i><span class="icon-name">fas fa-tty</span></li><li><i class="fas fa-tv"></i><span class="icon-name">fas fa-tv</span></li><li><i class="fas fa-umbrella"></i><span class="icon-name">fas fa-umbrella</span></li><li><i class="fas fa-umbrella-beach"></i><span class="icon-name">fas fa-umbrella-beach</span></li><li><i class="fas fa-underline"></i><span class="icon-name">fas fa-underline</span></li><li><i class="fas fa-undo"></i><span class="icon-name">fas fa-undo</span></li><li><i class="fas fa-undo-alt"></i><span class="icon-name">fas fa-undo-alt</span></li><li><i class="fas fa-universal-access"></i><span class="icon-name">fas fa-universal-access</span></li><li><i class="fas fa-university"></i><span class="icon-name">fas fa-university</span></li><li><i class="fas fa-unlink"></i><span class="icon-name">fas fa-unlink</span></li><li><i class="fas fa-unlock"></i><span class="icon-name">fas fa-unlock</span></li><li><i class="fas fa-unlock-alt"></i><span class="icon-name">fas fa-unlock-alt</span></li><li><i class="fas fa-upload"></i><span class="icon-name">fas fa-upload</span></li><li><i class="fas fa-user"></i><span class="icon-name">fas fa-user</span></li><li><i class="fas fa-user-alt"></i><span class="icon-name">fas fa-user-alt</span></li><li><i class="fas fa-user-alt-slash"></i><span class="icon-name">fas fa-user-alt-slash</span></li><li><i class="fas fa-user-astronaut"></i><span class="icon-name">fas fa-user-astronaut</span></li><li><i class="fas fa-user-check"></i><span class="icon-name">fas fa-user-check</span></li><li><i class="fas fa-user-circle"></i><span class="icon-name">fas fa-user-circle</span></li><li><i class="fas fa-user-clock"></i><span class="icon-name">fas fa-user-clock</span></li><li><i class="fas fa-user-cog"></i><span class="icon-name">fas fa-user-cog</span></li><li><i class="fas fa-user-edit"></i><span class="icon-name">fas fa-user-edit</span></li><li><i class="fas fa-user-friends"></i><span class="icon-name">fas fa-user-friends</span></li><li><i class="fas fa-user-graduate"></i><span class="icon-name">fas fa-user-graduate</span></li><li><i class="fas fa-user-injured"></i><span class="icon-name">fas fa-user-injured</span></li><li><i class="fas fa-user-lock"></i><span class="icon-name">fas fa-user-lock</span></li><li><i class="fas fa-user-md"></i><span class="icon-name">fas fa-user-md</span></li><li><i class="fas fa-user-minus"></i><span class="icon-name">fas fa-user-minus</span></li><li><i class="fas fa-user-ninja"></i><span class="icon-name">fas fa-user-ninja</span></li><li><i class="fas fa-user-nurse"></i><span class="icon-name">fas fa-user-nurse</span></li><li><i class="fas fa-user-plus"></i><span class="icon-name">fas fa-user-plus</span></li><li><i class="fas fa-user-secret"></i><span class="icon-name">fas fa-user-secret</span></li><li><i class="fas fa-user-shield"></i><span class="icon-name">fas fa-user-shield</span></li><li><i class="fas fa-user-slash"></i><span class="icon-name">fas fa-user-slash</span></li><li><i class="fas fa-user-tag"></i><span class="icon-name">fas fa-user-tag</span></li><li><i class="fas fa-user-tie"></i><span class="icon-name">fas fa-user-tie</span></li><li><i class="fas fa-user-times"></i><span class="icon-name">fas fa-user-times</span></li><li><i class="fas fa-users"></i><span class="icon-name">fas fa-users</span></li><li><i class="fas fa-users-cog"></i><span class="icon-name">fas fa-users-cog</span></li><li><i class="fas fa-utensil-spoon"></i><span class="icon-name">fas fa-utensil-spoon</span></li><li><i class="fas fa-utensils"></i><span class="icon-name">fas fa-utensils</span></li><li><i class="fas fa-vector-square"></i><span class="icon-name">fas fa-vector-square</span></li><li><i class="fas fa-venus"></i><span class="icon-name">fas fa-venus</span></li><li><i class="fas fa-venus-double"></i><span class="icon-name">fas fa-venus-double</span></li><li><i class="fas fa-venus-mars"></i><span class="icon-name">fas fa-venus-mars</span></li><li><i class="fas fa-vial"></i><span class="icon-name">fas fa-vial</span></li><li><i class="fas fa-vials"></i><span class="icon-name">fas fa-vials</span></li><li><i class="fas fa-video"></i><span class="icon-name">fas fa-video</span></li><li><i class="fas fa-video-slash"></i><span class="icon-name">fas fa-video-slash</span></li><li><i class="fas fa-vihara"></i><span class="icon-name">fas fa-vihara</span></li><li><i class="fas fa-voicemail"></i><span class="icon-name">fas fa-voicemail</span></li><li><i class="fas fa-volleyball-ball"></i><span class="icon-name">fas fa-volleyball-ball</span></li><li><i class="fas fa-volume-down"></i><span class="icon-name">fas fa-volume-down</span></li><li><i class="fas fa-volume-mute"></i><span class="icon-name">fas fa-volume-mute</span></li><li><i class="fas fa-volume-off"></i><span class="icon-name">fas fa-volume-off</span></li><li><i class="fas fa-volume-up"></i><span class="icon-name">fas fa-volume-up</span></li><li><i class="fas fa-vote-yea"></i><span class="icon-name">fas fa-vote-yea</span></li><li><i class="fas fa-vr-cardboard"></i><span class="icon-name">fas fa-vr-cardboard</span></li><li><i class="fas fa-walking"></i><span class="icon-name">fas fa-walking</span></li><li><i class="fas fa-wallet"></i><span class="icon-name">fas fa-wallet</span></li><li><i class="fas fa-warehouse"></i><span class="icon-name">fas fa-warehouse</span></li><li><i class="fas fa-water"></i><span class="icon-name">fas fa-water</span></li><li><i class="fas fa-wave-square"></i><span class="icon-name">fas fa-wave-square</span></li><li><i class="fas fa-weight"></i><span class="icon-name">fas fa-weight</span></li><li><i class="fas fa-weight-hanging"></i><span class="icon-name">fas fa-weight-hanging</span></li><li><i class="fas fa-wheelchair"></i><span class="icon-name">fas fa-wheelchair</span></li><li><i class="fas fa-wifi"></i><span class="icon-name">fas fa-wifi</span></li><li><i class="fas fa-wind"></i><span class="icon-name">fas fa-wind</span></li><li><i class="fas fa-window-close"></i><span class="icon-name">fas fa-window-close</span></li><li><i class="fas fa-window-maximize"></i><span class="icon-name">fas fa-window-maximize</span></li><li><i class="fas fa-window-minimize"></i><span class="icon-name">fas fa-window-minimize</span></li><li><i class="fas fa-window-restore"></i><span class="icon-name">fas fa-window-restore</span></li><li><i class="fas fa-wine-bottle"></i><span class="icon-name">fas fa-wine-bottle</span></li><li><i class="fas fa-wine-glass"></i><span class="icon-name">fas fa-wine-glass</span></li><li><i class="fas fa-wine-glass-alt"></i><span class="icon-name">fas fa-wine-glass-alt</span></li><li><i class="fas fa-won-sign"></i><span class="icon-name">fas fa-won-sign</span></li><li><i class="fas fa-wrench"></i><span class="icon-name">fas fa-wrench</span></li><li><i class="fas fa-x-ray"></i><span class="icon-name">fas fa-x-ray</span></li><li><i class="fas fa-yen-sign"></i><span class="icon-name">fas fa-yen-sign</span></li><li><i class="fas fa-yin-yang"></i><span class="icon-name">fas fa-yin-yang</span></li>';

            // GIZMO
            $gizmo_list = '<li><i class="ss-cursor"></i><span class="icon-name">ss-cursor</span></li><li><i class="ss-crosshair"></i><span class="icon-name">ss-crosshair</span></li><li><i class="ss-search"></i><span class="icon-name">ss-search</span></li><li><i class="ss-zoomin"></i><span class="icon-name">ss-zoomin</span></li><li><i class="ss-zoomout"></i><span class="icon-name">ss-zoomout</span></li><li><i class="ss-view"></i><span class="icon-name">ss-view</span></li><li><i class="ss-attach"></i><span class="icon-name">ss-attach</span></li><li><i class="ss-link"></i><span class="icon-name">ss-link</span></li><li><i class="ss-unlink"></i><span class="icon-name">ss-unlink</span></li><li><i class="ss-move"></i><span class="icon-name">ss-move</span></li><li><i class="ss-write"></i><span class="icon-name">ss-write</span></li><li><i class="ss-writingdisabled"></i><span class="icon-name">ss-writingdisabled</span></li><li><i class="ss-erase"></i><span class="icon-name">ss-erase</span></li><li><i class="ss-compose"></i><span class="icon-name">ss-compose</span></li><li><i class="ss-lock"></i><span class="icon-name">ss-lock</span></li><li><i class="ss-unlock"></i><span class="icon-name">ss-unlock</span></li><li><i class="ss-key"></i><span class="icon-name">ss-key</span></li><li><i class="ss-backspace"></i><span class="icon-name">ss-backspace</span></li><li><i class="ss-ban"></i><span class="icon-name">ss-ban</span></li><li><i class="ss-smoking"></i><span class="icon-name">ss-smoking</span></li><li><i class="ss-nosmoking"></i><span class="icon-name">ss-nosmoking</span></li><li><i class="ss-trash"></i><span class="icon-name">ss-trash</span></li><li><i class="ss-target"></i><span class="icon-name">ss-target</span></li><li><i class="ss-tag"></i><span class="icon-name">ss-tag</span></li><li><i class="ss-bookmark"></i><span class="icon-name">ss-bookmark</span></li><li><i class="ss-flag"></i><span class="icon-name">ss-flag</span></li><li><i class="ss-like"></i><span class="icon-name">ss-like</span></li><li><i class="ss-dislike"></i><span class="icon-name">ss-dislike</span></li><li><i class="ss-heart"></i><span class="icon-name">ss-heart</span></li><li><i class="ss-star"></i><span class="icon-name">ss-star</span></li><li><i class="ss-sample"></i><span class="icon-name">ss-sample</span></li><li><i class="ss-crop"></i><span class="icon-name">ss-crop</span></li><li><i class="ss-layers"></i><span class="icon-name">ss-layers</span></li><li><i class="ss-layergroup"></i><span class="icon-name">ss-layergroup</span></li><li><i class="ss-pen"></i><span class="icon-name">ss-pen</span></li><li><i class="ss-bezier"></i><span class="icon-name">ss-bezier</span></li><li><i class="ss-pixels"></i><span class="icon-name">ss-pixels</span></li><li><i class="ss-phone"></i><span class="icon-name">ss-phone</span></li><li><i class="ss-phonedisabled"></i><span class="icon-name">ss-phonedisabled</span></li><li><i class="ss-touchtonephone"></i><span class="icon-name">ss-touchtonephone</span></li><li><i class="ss-mail"></i><span class="icon-name">ss-mail</span></li><li><i class="ss-inbox"></i><span class="icon-name">ss-inbox</span></li><li><i class="ss-outbox"></i><span class="icon-name">ss-outbox</span></li><li><i class="ss-chat"></i><span class="icon-name">ss-chat</span></li><li><i class="ss-user"></i><span class="icon-name">ss-user</span></li><li><i class="ss-users"></i><span class="icon-name">ss-users</span></li><li><i class="ss-usergroup"></i><span class="icon-name">ss-usergroup</span></li><li><i class="ss-businessuser"></i><span class="icon-name">ss-businessuser</span></li><li><i class="ss-man"></i><span class="icon-name">ss-man</span></li><li><i class="ss-male"></i><span class="icon-name">ss-male</span></li><li><i class="ss-woman"></i><span class="icon-name">ss-woman</span></li><li><i class="ss-female"></i><span class="icon-name">ss-female</span></li><li><i class="ss-raisedhand"></i><span class="icon-name">ss-raisedhand</span></li><li><i class="ss-hand"></i><span class="icon-name">ss-hand</span></li><li><i class="ss-pointup"></i><span class="icon-name">ss-pointup</span></li><li><i class="ss-pointupright"></i><span class="icon-name">ss-pointupright</span></li><li><i class="ss-pointright"></i><span class="icon-name">ss-pointright</span></li><li><i class="ss-pointdownright"></i><span class="icon-name">ss-pointdownright</span></li><li><i class="ss-pointdown"></i><span class="icon-name">ss-pointdown</span></li><li><i class="ss-pointdownleft"></i><span class="icon-name">ss-pointdownleft</span></li><li><i class="ss-pointleft"></i><span class="icon-name">ss-pointleft</span></li><li><i class="ss-pointupleft"></i><span class="icon-name">ss-pointupleft</span></li><li><i class="ss-cart"></i><span class="icon-name">ss-cart</span></li><li><i class="ss-creditcard"></i><span class="icon-name">ss-creditcard</span></li><li><i class="ss-calculator"></i><span class="icon-name">ss-calculator</span></li><li><i class="ss-barchart"></i><span class="icon-name">ss-barchart</span></li><li><i class="ss-piechart"></i><span class="icon-name">ss-piechart</span></li><li><i class="ss-box"></i><span class="icon-name">ss-box</span></li><li><i class="ss-home"></i><span class="icon-name">ss-home</span></li><li><i class="ss-globe"></i><span class="icon-name">ss-globe</span></li><li><i class="ss-navigate"></i><span class="icon-name">ss-navigate</span></li><li><i class="ss-compass"></i><span class="icon-name">ss-compass</span></li><li><i class="ss-signpost"></i><span class="icon-name">ss-signpost</span></li><li><i class="ss-location"></i><span class="icon-name">ss-location</span></li><li><i class="ss-floppydisk"></i><span class="icon-name">ss-floppydisk</span></li><li><i class="ss-database"></i><span class="icon-name">ss-database</span></li><li><i class="ss-hdd"></i><span class="icon-name">ss-hdd</span></li><li><i class="ss-microchip"></i><span class="icon-name">ss-microchip</span></li><li><i class="ss-music"></i><span class="icon-name">ss-music</span></li><li><i class="ss-headphones"></i><span class="icon-name">ss-headphones</span></li><li><i class="ss-discdrive"></i><span class="icon-name">ss-discdrive</span></li><li><i class="ss-volume"></i><span class="icon-name">ss-volume</span></li><li><i class="ss-lowvolume"></i><span class="icon-name">ss-lowvolume</span></li><li><i class="ss-mediumvolume"></i><span class="icon-name">ss-mediumvolume</span></li><li><i class="ss-highvolume"></i><span class="icon-name">ss-highvolume</span></li><li><i class="ss-airplay"></i><span class="icon-name">ss-airplay</span></li><li><i class="ss-camera"></i><span class="icon-name">ss-camera</span></li><li><i class="ss-picture"></i><span class="icon-name">ss-picture</span></li><li><i class="ss-video"></i><span class="icon-name">ss-video</span></li><li><i class="ss-webcam"></i><span class="icon-name">ss-webcam</span></li><li><i class="ss-film"></i><span class="icon-name">ss-film</span></li><li><i class="ss-playvideo"></i><span class="icon-name">ss-playvideo</span></li><li><i class="ss-videogame"></i><span class="icon-name">ss-videogame</span></li><li><i class="ss-play"></i><span class="icon-name">ss-play</span></li><li><i class="ss-pause"></i><span class="icon-name">ss-pause</span></li><li><i class="ss-stop"></i><span class="icon-name">ss-stop</span></li><li><i class="ss-record"></i><span class="icon-name">ss-record</span></li><li><i class="ss-rewind"></i><span class="icon-name">ss-rewind</span></li><li><i class="ss-fastforward"></i><span class="icon-name">ss-fastforward</span></li><li><i class="ss-skipback"></i><span class="icon-name">ss-skipback</span></li><li><i class="ss-skipforward"></i><span class="icon-name">ss-skipforward</span></li><li><i class="ss-eject"></i><span class="icon-name">ss-eject</span></li><li><i class="ss-repeat"></i><span class="icon-name">ss-repeat</span></li><li><i class="ss-replay"></i><span class="icon-name">ss-replay</span></li><li><i class="ss-shuffle"></i><span class="icon-name">ss-shuffle</span></li><li><i class="ss-index"></i><span class="icon-name">ss-index</span></li><li><i class="ss-storagebox"></i><span class="icon-name">ss-storagebox</span></li><li><i class="ss-book"></i><span class="icon-name">ss-book</span></li><li><i class="ss-notebook"></i><span class="icon-name">ss-notebook</span></li><li><i class="ss-newspaper"></i><span class="icon-name">ss-newspaper</span></li><li><i class="ss-gridlines"></i><span class="icon-name">ss-gridlines</span></li><li><i class="ss-rows"></i><span class="icon-name">ss-rows</span></li><li><i class="ss-columns"></i><span class="icon-name">ss-columns</span></li><li><i class="ss-thumbnails"></i><span class="icon-name">ss-thumbnails</span></li><li><i class="ss-mouse"></i><span class="icon-name">ss-mouse</span></li><li><i class="ss-usb"></i><span class="icon-name">ss-usb</span></li><li><i class="ss-desktop"></i><span class="icon-name">ss-desktop</span></li><li><i class="ss-laptop"></i><span class="icon-name">ss-laptop</span></li><li><i class="ss-tablet"></i><span class="icon-name">ss-tablet</span></li><li><i class="ss-smartphone"></i><span class="icon-name">ss-smartphone</span></li><li><i class="ss-cell"></i><span class="icon-name">ss-cell</span></li><li><i class="ss-battery"></i><span class="icon-name">ss-battery</span></li><li><i class="ss-highbattery"></i><span class="icon-name">ss-highbattery</span></li><li><i class="ss-mediumbattery"></i><span class="icon-name">ss-mediumbattery</span></li><li><i class="ss-lowbattery"></i><span class="icon-name">ss-lowbattery</span></li><li><i class="ss-chargingbattery"></i><span class="icon-name">ss-chargingbattery</span></li><li><i class="ss-lightbulb"></i><span class="icon-name">ss-lightbulb</span></li><li><i class="ss-washer"></i><span class="icon-name">ss-washer</span></li><li><i class="ss-downloadcloud"></i><span class="icon-name">ss-downloadcloud</span></li><li><i class="ss-download"></i><span class="icon-name">ss-download</span></li><li><i class="ss-downloadbox"></i><span class="icon-name">ss-downloadbox</span></li><li><i class="ss-uploadcloud"></i><span class="icon-name">ss-uploadcloud</span></li><li><i class="ss-upload"></i><span class="icon-name">ss-upload</span></li><li><i class="ss-uploadbox"></i><span class="icon-name">ss-uploadbox</span></li><li><i class="ss-fork"></i><span class="icon-name">ss-fork</span></li><li><i class="ss-merge"></i><span class="icon-name">ss-merge</span></li><li><i class="ss-refresh"></i><span class="icon-name">ss-refresh</span></li><li><i class="ss-sync"></i><span class="icon-name">ss-sync</span></li><li><i class="ss-loading"></i><span class="icon-name">ss-loading</span></li><li><i class="ss-file"></i><span class="icon-name">ss-file</span></li><li><i class="ss-files"></i><span class="icon-name">ss-files</span></li><li><i class="ss-addfile"></i><span class="icon-name">ss-addfile</span></li><li><i class="ss-removefile"></i><span class="icon-name">ss-removefile</span></li><li><i class="ss-checkfile"></i><span class="icon-name">ss-checkfile</span></li><li><i class="ss-deletefile"></i><span class="icon-name">ss-deletefile</span></li><li><i class="ss-exe"></i><span class="icon-name">ss-exe</span></li><li><i class="ss-zip"></i><span class="icon-name">ss-zip</span></li><li><i class="ss-doc"></i><span class="icon-name">ss-doc</span></li><li><i class="ss-pdf"></i><span class="icon-name">ss-pdf</span></li><li><i class="ss-jpg"></i><span class="icon-name">ss-jpg</span></li><li><i class="ss-png"></i><span class="icon-name">ss-png</span></li><li><i class="ss-mp3"></i><span class="icon-name">ss-mp3</span></li><li><i class="ss-rar"></i><span class="icon-name">ss-rar</span></li><li><i class="ss-gif"></i><span class="icon-name">ss-gif</span></li><li><i class="ss-folder"></i><span class="icon-name">ss-folder</span></li><li><i class="ss-openfolder"></i><span class="icon-name">ss-openfolder</span></li><li><i class="ss-downloadfolder"></i><span class="icon-name">ss-downloadfolder</span></li><li><i class="ss-uploadfolder"></i><span class="icon-name">ss-uploadfolder</span></li><li><i class="ss-quote"></i><span class="icon-name">ss-quote</span></li><li><i class="ss-unquote"></i><span class="icon-name">ss-unquote</span></li><li><i class="ss-print"></i><span class="icon-name">ss-print</span></li><li><i class="ss-copier"></i><span class="icon-name">ss-copier</span></li><li><i class="ss-fax"></i><span class="icon-name">ss-fax</span></li><li><i class="ss-scanner"></i><span class="icon-name">ss-scanner</span></li><li><i class="ss-printregistration"></i><span class="icon-name">ss-printregistration</span></li><li><i class="ss-shredder"></i><span class="icon-name">ss-shredder</span></li><li><i class="ss-expand"></i><span class="icon-name">ss-expand</span></li><li><i class="ss-contract"></i><span class="icon-name">ss-contract</span></li><li><i class="ss-help"></i><span class="icon-name">ss-help</span></li><li><i class="ss-info"></i><span class="icon-name">ss-info</span></li><li><i class="ss-alert"></i><span class="icon-name">ss-alert</span></li><li><i class="ss-caution"></i><span class="icon-name">ss-caution</span></li><li><i class="ss-logout"></i><span class="icon-name">ss-logout</span></li><li><i class="ss-login"></i><span class="icon-name">ss-login</span></li><li><i class="ss-scaleup"></i><span class="icon-name">ss-scaleup</span></li><li><i class="ss-scaledown"></i><span class="icon-name">ss-scaledown</span></li><li><i class="ss-plus"></i><span class="icon-name">ss-plus</span></li><li><i class="ss-hyphen"></i><span class="icon-name">ss-hyphen</span></li><li><i class="ss-check"></i><span class="icon-name">ss-check</span></li><li><i class="ss-delete"></i><span class="icon-name">ss-delete</span></li><li><i class="ss-notifications"></i><span class="icon-name">ss-notifications</span></li><li><i class="ss-notificationsdisabled"></i><span class="icon-name">ss-notificationsdisabled</span></li><li><i class="ss-clock"></i><span class="icon-name">ss-clock</span></li><li><i class="ss-stopwatch"></i><span class="icon-name">ss-stopwatch</span></li><li><i class="ss-alarmclock"></i><span class="icon-name">ss-alarmclock</span></li><li><i class="ss-egg"></i><span class="icon-name">ss-egg</span></li><li><i class="ss-eggs"></i><span class="icon-name">ss-eggs</span></li><li><i class="ss-cheese"></i><span class="icon-name">ss-cheese</span></li><li><i class="ss-chickenleg"></i><span class="icon-name">ss-chickenleg</span></li><li><i class="ss-pizzapie"></i><span class="icon-name">ss-pizzapie</span></li><li><i class="ss-pizza"></i><span class="icon-name">ss-pizza</span></li><li><i class="ss-cheesepizza"></i><span class="icon-name">ss-cheesepizza</span></li><li><i class="ss-frenchfries"></i><span class="icon-name">ss-frenchfries</span></li><li><i class="ss-apple"></i><span class="icon-name">ss-apple</span></li><li><i class="ss-carrot"></i><span class="icon-name">ss-carrot</span></li><li><i class="ss-broccoli"></i><span class="icon-name">ss-broccoli</span></li><li><i class="ss-cucumber"></i><span class="icon-name">ss-cucumber</span></li><li><i class="ss-orange"></i><span class="icon-name">ss-orange</span></li><li><i class="ss-lemon"></i><span class="icon-name">ss-lemon</span></li><li><i class="ss-onion"></i><span class="icon-name">ss-onion</span></li><li><i class="ss-bellpepper"></i><span class="icon-name">ss-bellpepper</span></li><li><i class="ss-peas"></i><span class="icon-name">ss-peas</span></li><li><i class="ss-grapes"></i><span class="icon-name">ss-grapes</span></li><li><i class="ss-strawberry"></i><span class="icon-name">ss-strawberry</span></li><li><i class="ss-bread"></i><span class="icon-name">ss-bread</span></li><li><i class="ss-mug"></i><span class="icon-name">ss-mug</span></li><li><i class="ss-mugs"></i><span class="icon-name">ss-mugs</span></li><li><i class="ss-espresso"></i><span class="icon-name">ss-espresso</span></li><li><i class="ss-macchiato"></i><span class="icon-name">ss-macchiato</span></li><li><i class="ss-cappucino"></i><span class="icon-name">ss-cappucino</span></li><li><i class="ss-latte"></i><span class="icon-name">ss-latte</span></li><li><i class="ss-icedcoffee"></i><span class="icon-name">ss-icedcoffee</span></li><li><i class="ss-coffeebean"></i><span class="icon-name">ss-coffeebean</span></li><li><i class="ss-coffeemilk"></i><span class="icon-name">ss-coffeemilk</span></li><li><i class="ss-coffeefoam"></i><span class="icon-name">ss-coffeefoam</span></li><li><i class="ss-coffeesugar"></i><span class="icon-name">ss-coffeesugar</span></li><li><i class="ss-sugarpackets"></i><span class="icon-name">ss-sugarpackets</span></li><li><i class="ss-capsule"></i><span class="icon-name">ss-capsule</span></li><li><i class="ss-capsulerecycling"></i><span class="icon-name">ss-capsulerecycling</span></li><li><i class="ss-insertcapsule"></i><span class="icon-name">ss-insertcapsule</span></li><li><i class="ss-tea"></i><span class="icon-name">ss-tea</span></li><li><i class="ss-teabag"></i><span class="icon-name">ss-teabag</span></li><li><i class="ss-jug"></i><span class="icon-name">ss-jug</span></li><li><i class="ss-pitcher"></i><span class="icon-name">ss-pitcher</span></li><li><i class="ss-kettle"></i><span class="icon-name">ss-kettle</span></li><li><i class="ss-wineglass"></i><span class="icon-name">ss-wineglass</span></li><li><i class="ss-sugar"></i><span class="icon-name">ss-sugar</span></li><li><i class="ss-oven"></i><span class="icon-name">ss-oven</span></li><li><i class="ss-stove"></i><span class="icon-name">ss-stove</span></li><li><i class="ss-vent"></i><span class="icon-name">ss-vent</span></li><li><i class="ss-exhaust"></i><span class="icon-name">ss-exhaust</span></li><li><i class="ss-steam"></i><span class="icon-name">ss-steam</span></li><li><i class="ss-dishwasher"></i><span class="icon-name">ss-dishwasher</span></li><li><i class="ss-toaster"></i><span class="icon-name">ss-toaster</span></li><li><i class="ss-microwave"></i><span class="icon-name">ss-microwave</span></li><li><i class="ss-electrickettle"></i><span class="icon-name">ss-electrickettle</span></li><li><i class="ss-refrigerator"></i><span class="icon-name">ss-refrigerator</span></li><li><i class="ss-freezer"></i><span class="icon-name">ss-freezer</span></li><li><i class="ss-utensils"></i><span class="icon-name">ss-utensils</span></li><li><i class="ss-cookingutensils"></i><span class="icon-name">ss-cookingutensils</span></li><li><i class="ss-whisk"></i><span class="icon-name">ss-whisk</span></li><li><i class="ss-pizzacutter"></i><span class="icon-name">ss-pizzacutter</span></li><li><i class="ss-measuringcup"></i><span class="icon-name">ss-measuringcup</span></li><li><i class="ss-colander"></i><span class="icon-name">ss-colander</span></li><li><i class="ss-eggtimer"></i><span class="icon-name">ss-eggtimer</span></li><li><i class="ss-platter"></i><span class="icon-name">ss-platter</span></li><li><i class="ss-plates"></i><span class="icon-name">ss-plates</span></li><li><i class="ss-steamplate"></i><span class="icon-name">ss-steamplate</span></li><li><i class="ss-cups"></i><span class="icon-name">ss-cups</span></li><li><i class="ss-steamglass"></i><span class="icon-name">ss-steamglass</span></li><li><i class="ss-pot"></i><span class="icon-name">ss-pot</span></li><li><i class="ss-steampot"></i><span class="icon-name">ss-steampot</span></li><li><i class="ss-chef"></i><span class="icon-name">ss-chef</span></li><li><i class="ss-weathervane"></i><span class="icon-name">ss-weathervane</span></li><li><i class="ss-thermometer"></i><span class="icon-name">ss-thermometer</span></li><li><i class="ss-thermometerup"></i><span class="icon-name">ss-thermometerup</span></li><li><i class="ss-thermometerdown"></i><span class="icon-name">ss-thermometerdown</span></li><li><i class="ss-droplet"></i><span class="icon-name">ss-droplet</span></li><li><i class="ss-sunrise"></i><span class="icon-name">ss-sunrise</span></li><li><i class="ss-sunset"></i><span class="icon-name">ss-sunset</span></li><li><i class="ss-sun"></i><span class="icon-name">ss-sun</span></li><li><i class="ss-cloud"></i><span class="icon-name">ss-cloud</span></li><li><i class="ss-clouds"></i><span class="icon-name">ss-clouds</span></li><li><i class="ss-partlycloudy"></i><span class="icon-name">ss-partlycloudy</span></li><li><i class="ss-rain"></i><span class="icon-name">ss-rain</span></li><li><i class="ss-rainheavy"></i><span class="icon-name">ss-rainheavy</span></li><li><i class="ss-lightning"></i><span class="icon-name">ss-lightning</span></li><li><i class="ss-thunderstorm"></i><span class="icon-name">ss-thunderstorm</span></li><li><i class="ss-umbrella"></i><span class="icon-name">ss-umbrella</span></li><li><i class="ss-rainumbrella"></i><span class="icon-name">ss-rainumbrella</span></li><li><i class="ss-rainbow"></i><span class="icon-name">ss-rainbow</span></li><li><i class="ss-rainbowclouds"></i><span class="icon-name">ss-rainbowclouds</span></li><li><i class="ss-fog"></i><span class="icon-name">ss-fog</span></li><li><i class="ss-wind"></i><span class="icon-name">ss-wind</span></li><li><i class="ss-tornado"></i><span class="icon-name">ss-tornado</span></li><li><i class="ss-snowflake"></i><span class="icon-name">ss-snowflake</span></li><li><i class="ss-snowcrystal"></i><span class="icon-name">ss-snowcrystal</span></li><li><i class="ss-lightsnow"></i><span class="icon-name">ss-lightsnow</span></li><li><i class="ss-snow"></i><span class="icon-name">ss-snow</span></li><li><i class="ss-heavysnow"></i><span class="icon-name">ss-heavysnow</span></li><li><i class="ss-hail"></i><span class="icon-name">ss-hail</span></li><li><i class="ss-crescentmoon"></i><span class="icon-name">ss-crescentmoon</span></li><li><i class="ss-waxingcrescentmoon"></i><span class="icon-name">ss-waxingcrescentmoon</span></li><li><i class="ss-firstquartermoon"></i><span class="icon-name">ss-firstquartermoon</span></li><li><i class="ss-waxinggibbousmoon"></i><span class="icon-name">ss-waxinggibbousmoon</span></li><li><i class="ss-waninggibbousmoon"></i><span class="icon-name">ss-waninggibbousmoon</span></li><li><i class="ss-lastquartermoon"></i><span class="icon-name">ss-lastquartermoon</span></li><li><i class="ss-waningcrescentmoon"></i><span class="icon-name">ss-waningcrescentmoon</span></li><li><i class="ss-fan"></i><span class="icon-name">ss-fan</span></li><li><i class="ss-bike"></i><span class="icon-name">ss-bike</span></li><li><i class="ss-wheelchair"></i><span class="icon-name">ss-wheelchair</span></li><li><i class="ss-briefcase"></i><span class="icon-name">ss-briefcase</span></li><li><i class="ss-hanger"></i><span class="icon-name">ss-hanger</span></li><li><i class="ss-comb"></i><span class="icon-name">ss-comb</span></li><li><i class="ss-medicalcross"></i><span class="icon-name">ss-medicalcross</span></li><li><i class="ss-up"></i><span class="icon-name">ss-up</span></li><li><i class="ss-upright"></i><span class="icon-name">ss-upright</span></li><li><i class="ss-right"></i><span class="icon-name">ss-right</span></li><li><i class="ss-downright"></i><span class="icon-name">ss-downright</span></li><li><i class="ss-down"></i><span class="icon-name">ss-down</span></li><li><i class="ss-downleft"></i><span class="icon-name">ss-downleft</span></li><li><i class="ss-left"></i><span class="icon-name">ss-left</span></li><li><i class="ss-upleft"></i><span class="icon-name">ss-upleft</span></li><li><i class="ss-navigateup"></i><span class="icon-name">ss-navigateup</span></li><li><i class="ss-navigateright"></i><span class="icon-name">ss-navigateright</span></li><li><i class="ss-navigatedown"></i><span class="icon-name">ss-navigatedown</span></li><li><i class="ss-navigateleft"></i><span class="icon-name">ss-navigateleft</span></li><li><i class="ss-retweet"></i><span class="icon-name">ss-retweet</span></li><li><i class="ss-share"></i><span class="icon-name">ss-share</span></li>';

            // OUTPUT
            $icon_list .= $fa_list;
            $icon_list .= $gizmo_list;
			
            // APPLY FILTERS
            $icon_list = apply_filters( 'dante_icons_list', $icon_list );

            return $icon_list;
        }
    }
	
	
	/* ADMIN CUSTOM POST TYPE ICONS
	================================================== */
	if (!function_exists('dante_admin_css')) {
		function dante_admin_css() {
			
			$options = get_option('sf_dante_options');
			$body_font_size = $options['body_font_size'];
			$body_font_line_height = $options['body_font_line_height'];
			if (isset($options['menu_font_size'])) {
			$menu_font_size = $options['menu_font_size'];
			}
			$h1_font_size = $options['h1_font_size'];
			$h1_font_line_height = $options['h1_font_line_height'];
			$h2_font_size = $options['h2_font_size'];
			$h2_font_line_height = $options['h2_font_line_height'];
			$h3_font_size = $options['h3_font_size'];
			$h3_font_line_height = $options['h3_font_line_height'];
			$h4_font_size = $options['h4_font_size'];
			$h4_font_line_height = $options['h4_font_line_height'];
			$h5_font_size = $options['h5_font_size'];
			$h5_font_line_height = $options['h5_font_line_height'];
			$h6_font_size = $options['h6_font_size'];
			$h6_font_line_height = $options['h6_font_line_height'];
			
		    ?>	    
		    <style type="text/css" media="screen">
		    	
		        #toplevel_page_dante_theme_options .wp-menu-image img {
		        	width: 11px;
		        	margin-top: -2px;
		        	margin-left: 3px;
		        }
		        .toplevel_page_dante_theme_options #adminmenu li#toplevel_page_dante_theme_options.wp-has-current-submenu a.wp-has-current-submenu, .toplevel_page_dante_theme_options #adminmenu #toplevel_page_dante_theme_options .wp-menu-arrow div, .toplevel_page_dante_theme_options #adminmenu #toplevel_page_dante_theme_options .wp-menu-arrow {
		        	background: #222;
		        	border-color: #222;
		        }
		        #wpbody-content {
		        	min-height: 815px;
		        }
		        .wp-list-table th#thumbnail, .wp-list-table td.thumbnail {
		        	width: 80px;
		        }
		        .wp-list-table td.thumbnail img {
		        	max-width: 100%;
		        	height: auto;
		        }
		        .sf-menu-options {
		        	clear: both;
		        	height: auto;
		        	overflow: hidden;
		        }
		        .sf-menu-options h4 {
		        	margin-top: 20px;
		        	margin-bottom: 5px;
		        	border-bottom: 1px solid #e3e3e3;
		        	margin-right: 15px;
		        	padding-bottom: 5px;
		        }
		        .sf-menu-options p label input[type=checkbox] {
		        	margin-left: 10px;
		        }
		        .sf-menu-options p label input[type=text] {
		        	margin-top: 5px;
		        }
		        .sf-menu-options p label textarea {
		        	margin-top: 5px;
		        	width: 100%;
		        }
		        
		        /* RWMB */
		        .rwmb-field {
		        	margin: 10px 0;
		        }
		        .rwmb-field > h3 {
		        	margin: 10px 0;
		        	border-bottom: 1px solid #e4e4e4;
		        	padding-bottom: 10px !important;
		        }
		        .rwmb-label label {
		        	padding-right: 10px;
		        	vertical-align: top;
		        }
		        .rwmb-checkbox-wrapper .description {
		        	display: block;
		        	margin: 6px 0 8px;
		        }
		        .rwmb-input .rwmb-slider {
		            background: #f7f7f7;
		            border: 1px solid #e3e3e3;
		        }
		        .meta-box-sortables select, .rwmb-input > input, .rwmb-media-view .rwmb-add-media {
		        	margin-bottom: 5px;
		        }
		        .meta-altbg-preview {
		        	max-width: 200px;
	        	    padding: 10px;
	        	    text-align: center;
	        	    margin-left: 25%;
		        }
		        
		        /* META BOX TABS */
		        #dante_meta_box > .inside {
                	margin: 0;
                	padding: 0;
                }
                .sf-meta-tabs-wrap {
                    height: auto;
                    overflow: hidden;
                }
    
                .rwmb-meta-box {
                    padding: 20px 10px;
                }
    
                .sf-meta-tabs-wrap.all-hidden {
                    display: none;
                }
    
                #sf-tabbed-meta-boxes {
                    position: relative;
                    z-index: 1;
                    float: right;
                    width: 80%;
                    border-left: 1px solid #e3e3e3;
                }
    
    
                #sf-tabbed-meta-boxes .inside {
                    display: block !important;
                }
    
                #sf-tabbed-meta-boxes > div {
                    border-left: 0;
                    border-right: 0;
                    border-bottom: 0;
                    margin-bottom: 0;
                    padding-bottom: 20px;
                    border-top: 0;
                }               

				#sf-tabbed-meta-boxes .handlediv,
				#sf-tabbed-meta-boxes .handlediv,
				#sf-tabbed-meta-boxes .hndle,
				#sf-tabbed-meta-boxes .postbox-header > .handle-actions {
					display: none!important;
				}
				#sf-tabbed-meta-boxes .postbox-header {
					border-bottom: none;
				}
    
                /*#sf-tabbed-meta-boxes > div.hide-if-js {
                       display: none!important;
                }*/
                #sf-meta-box-tabs {
                    margin: 0;
                    width: 20%;
                    position: relative;
                    z-index: 2;
                    float: left;
                    margin-right: -1px;
                }
    
                #sf-meta-box-tabs li {
                    margin-bottom: -1px;
                }
    
                #sf-meta-box-tabs li.user-hidden {
                    display: none !important;
                }
    			
    			#sf-meta-box-tabs li:first-child > a {
    				border-top: 0;
    			}
    			
                #sf-meta-box-tabs li > a {
                    display: block;
                    background: #f7f7f7;
                    padding: 12px;
                    line-height: 150%;
                    border: 1px solid #e5e5e5;
                    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                    box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                    text-decoration: none;
                }
    
                #sf-meta-box-tabs li > a:hover {
                    color: #222;
                    background: #fff;
                }
    
                #sf-meta-box-tabs li > a.active {
                    border-right-color: #fff;
                    background: #fff;
                    box-shadow: none;
                }
    
                .closed #sf-meta-box-tabs, .closed #sf-tabbed-meta-boxes {
                    display: none;
                }
                
                
                #sf-tabbed-meta-boxes .inside .rwmb-meta-box .rwmb-field:first-of-type > h3 {
                		margin-top: -10px;
				}
				
				.rwmb-field.rwmb-altbgpreview-wrapper {
					display: none!important;
				}
		       	
		       	<?php 
		       		echo '#typography-preview p {font-size: '.$body_font_size.'px;line-height: '.$body_font_line_height.'px;}';
		       		echo '#typography-preview h1 {font-size: '.$h1_font_size.'px;line-height: '.$h1_font_line_height.'px;}';
		       		echo '#typography-preview h2 {font-size: '.$h2_font_size.'px;line-height: '.$h2_font_line_height.'px;}';
		       		echo '#typography-preview h3 {font-size: '.$h3_font_size.'px;line-height: '.$h3_font_line_height.'px;}';
		       		echo '#typography-preview h4 {font-size: '.$h4_font_size.'px;line-height: '.$h4_font_line_height.'px;}';
		       		echo '#typography-preview h5 {font-size: '.$h5_font_size.'px;line-height: '.$h5_font_line_height.'px;}';
		       		echo '#typography-preview h6 {font-size: '.$h6_font_size.'px;line-height: '.$h6_font_line_height.'px;}'; 
		       	?>
		        
			</style>
		
		<?php }
		add_action( 'admin_head', 'dante_admin_css' );
	}
?>
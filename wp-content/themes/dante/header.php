<!DOCTYPE html>

<!--// OPEN HTML //-->
<html <?php language_attributes(); ?>>

	<!--// OPEN HEAD //-->
	<head>
		<?php
			
			global $post;
			$post_id = 0;
			
			if ( ( function_exists('is_shop') && is_shop() ) || ( function_exists('is_product_category') && is_product_category() ) ) {
				$post_id = wc_get_page_id( 'shop' );
			} else if ($post) {
				$post_id = $post->ID;
			}
			
			$options = get_option('sf_dante_options');
			$enable_responsive = $options['enable_responsive'];
			$is_responsive = "responsive-fluid";
			if (!$enable_responsive) {
				$is_responsive = "responsive-fixed";
			}
			$header_layout = $options['header_layout'];
			$page_layout = $options['page_layout'];
			$enable_logo_fade = $options['enable_logo_fade'];
			$enable_page_shadow = $options['enable_page_shadow'];
			$enable_top_bar = $options['enable_tb'];
			$enable_mini_header = $options['enable_mini_header'];
			$enable_header_shadow = $options['enable_header_shadow'];
			$header_search_type = "search-1";
			if (isset($options['header_search_type'])) {
				$header_search_type = $options['header_search_type'];
			}
			
			$page_class = $header_wrap_class = $logo_class = $ss_enable = "";
			
			if (isset($_GET['header'])) {
				$header_layout = $_GET['header'];
			}
			
			if ($header_layout == "header-3" || $header_layout == "header-4" || $header_layout == "header-5") {
				$header_wrap_class = " container";
				$page_class .= "header-overlay ";
			}

			if (isset($options['enable_fw_header']) && $options['enable_fw_header'] == true) {
				$header_wrap_class .= " fw-header";
			}
			
			global $dante_catalog_mode;
			if (isset($options['enable_catalog_mode'])) {
				$enable_catalog_mode = $options['enable_catalog_mode'];
				if ($enable_catalog_mode) {
					$dante_catalog_mode = true;
					$page_class .= "catalog-mode ";
				}
			}
			
			if ($enable_mini_header) { 
			$page_class .= "mini-header-enabled ";
			}
			
			if ($enable_page_shadow) { 
			$page_class .= "page-shadow ";
			}
			
			if ($enable_header_shadow) {
			$page_class .= "header-shadow ";
			}
			
			if ($enable_logo_fade) {
			$logo_class = "logo-fade";
			}

			if (isset($_GET['layout'])) {
				$page_layout = $_GET['layout'];
			}
			
			$page_class .= "layout-".$page_layout." ";
			
			if (isset($options['ss_enable'])) {
				$ss_enable = $options['ss_enable'];
			} else {
				$ss_enable = true;
			}
			
			global $remove_promo_bar, $enable_one_page_nav;
			$extra_page_class = $description = "";
			if ($post) {
				$extra_page_class = dante_get_post_meta($post->ID, 'sf_extra_page_class', true);
				$remove_promo_bar = dante_get_post_meta($post->ID, 'sf_remove_promo_bar', true);
				$enable_one_page_nav = dante_get_post_meta($post->ID, 'sf_enable_one_page_nav', true);
				$enable_naked_header = dante_get_post_meta($post->ID, 'sf_enable_naked_header', true);
				if ($enable_naked_header == "naked-light" || $enable_naked_header == "naked-dark") {
					if ($header_layout == "header-1" || $header_layout == "header-2") {
						$header_layout = "header-7";
					}
					$page_class .= "naked-header " . $enable_naked_header;
				}
			}
		?>
		
		<!--// SITE META //-->
		<meta charset="<?php bloginfo( 'charset' ); ?>" />	
		<?php if ($enable_responsive) { ?><meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php } ?>
		
		<!--// PINGBACK //-->
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<!--// WORDPRESS HEAD HOOK //-->
		<?php wp_head(); ?>
	
	<!--// CLOSE HEAD //-->
	</head>
	
	<!--// OPEN BODY //-->
	<body <?php body_class($page_class.' '.$is_responsive.' '.$extra_page_class.' '.$header_search_type); ?>>
		
		<div id="header-search">
			<div class="container clearfix">
				<i class="ss-search"></i>
				<form method="get" class="search-form" action="<?php echo home_url(); ?>/"><input type="text" placeholder="<?php _e("Search for something...", 'dante'); ?>" name="s" autocomplete="off" /></form>
				<a id="header-search-close" href="#"><i class="ss-delete"></i></a>
			</div>
		</div>
		
		<?php
			// SUPER SEARCH
			if (dante_woocommerce_activated() && $ss_enable) { 
				echo dante_super_search();
			}
		?>
		
		<?php  
			// MOBILE MENU
			echo dante_mobile_menu();
		?>
		
		<!--// OPEN #container //-->
		<?php if ($page_layout == "fullwidth") { ?>
		<div id="container">
		<?php } else { ?>
		<div id="container" class="boxed-layout">
		<?php } ?>
			
			<!--// HEADER //-->
			<div class="header-wrap<?php echo esc_attr($header_wrap_class); ?>">
				
				<?php if ($enable_top_bar) { ?>
					<!--// TOP BAR //-->
					<?php echo dante_top_bar(); ?>
				<?php } ?>	
					
				<div id="header-section" class="<?php echo esc_attr($header_layout); ?> <?php echo esc_attr($logo_class); ?>">
					<?php echo dante_header($header_layout); ?>
				</div>

			</div>
			
			<!--// OPEN #main-container //-->
			<div id="main-container" class="clearfix">
				
				<?php if (is_page()) {
					global $post;
					$show_posts_slider = dante_get_post_meta($post->ID, 'sf_posts_slider', true);
					$rev_slider_alias = dante_get_post_meta($post->ID, 'sf_rev_slider_alias', true);
					$layerSlider_ID = dante_get_post_meta($post->ID, 'sf_layerslider_id', true);
									
					if ($show_posts_slider) {
						dante_swift_slider();
					} else if ($rev_slider_alias != "") { ?>
						<div class="home-slider-wrap">
							<?php if (function_exists('putRevSlider')) {
								putRevSlider($rev_slider_alias);
							} ?>
						</div>
				<?php } else if ($layerSlider_ID != "") { ?>
					<div class="home-slider-wrap">
						<?php echo do_shortcode('[layerslider id="'.$layerSlider_ID.'"]'); ?>
					</div>
				<?php }
					}
				?>
								
				<?php 
					// Page Heading
					dante_page_heading();
				?>
				
				<?php
					// Page container handling
					$pb_fw_mode = false;
					$fw_override = false;
					
					if ( $post && is_singular() ) {
					
						global $dante_pb_active;
									
						$sidebar_config = dante_get_post_meta($post_id, 'sf_sidebar_config', true);
						if ( isset($_GET['sidebar']) ) {
							$sidebar_config = $_GET['sidebar'];
						}
						
						if ( is_singular('portfolio') ) {
							$sidebar_config = "no-sidebars";
						}
						
						$pb_active = dante_get_post_meta($post_id, '_spb_js_status', true);
						
						if ( $sidebar_config != "no-sidebars" || post_password_required() ) {
							$pb_fw_mode = false;
						} else if ( $pb_active == "true" ) {
							$pb_fw_mode = true;
						}
						
						if ( function_exists('is_product') && is_product() && $sidebar_config == "no-sidebars" ) {
							$pb_fw_mode = true;
						}
						
						if ( is_page_template( 'template-fw.php' ) || is_page_template( 'template-fw-alt.php' ) ) {
							$pb_fw_mode = true;
						}
						
						if ( is_singular( 'portfolio' ) ) {
							$fw_media_display = dante_get_post_meta($post->ID, 'sf_fw_media_display', true);
							if ( $fw_media_display == "fw-media" ) {
								$fw_override = true;
							}
						}
	
						$dante_pb_active = $pb_fw_mode;
					}
					
					
					
					// Check if page should be enabled in full width mode
					if ($pb_fw_mode || $fw_override) { ?>
					<!--// OPEN .pb-fw-wrap //-->
					<div class="pb-fw-wrap">
					<?php } else { ?>
					<!--// OPEN .container //-->
					<div class="container">
					<?php } ?>
				
					<!--// OPEN #page-wrap //-->
					<div id="page-wrap">
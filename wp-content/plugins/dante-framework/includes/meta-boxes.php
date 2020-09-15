<?php

	/*
	*
	*	Meta Box Functions
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2013 - http://www.swiftideas.net
	*
	*/

	/* GET CUSTOM POST TYPE TAXONOMY LIST
	================================================== */
    function dante_framework_get_category_list( $category_name, $filter = 0, $category_child = "" ) {

        if ( ! $filter ) {

            $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
            $category_list = array( '0' => 'All' );

            foreach ( $get_category as $category ) {
                if ( isset( $category->slug ) ) {
                    $category_list[] = $category->slug;
                }
            }

            return $category_list;

        } else if ( $category_child != "" && $category_child != "All" ) {

            $childcategory = get_term_by( 'slug', $category_child, $category_name );
            $get_category  = get_categories( array(
                    'taxonomy' => $category_name,
                    'child_of' => $childcategory->term_id
                ) );
            $category_list = array( '0' => 'All' );

            foreach ( $get_category as $category ) {
                if ( isset( $category->cat_name ) ) {
                    $category_list[] = $category->slug;
                }
            }

            return $category_list;

        } else {

            $get_category  = get_categories( array( 'taxonomy' => $category_name ) );
            $category_list = array( '0' => 'All' );

            foreach ( $get_category as $category ) {
                if ( isset( $category->cat_name ) ) {
                    $category_list[] = $category->cat_name;
                }
            }

            return $category_list;
        }
    }
	
	function dante_framework_get_category_list_key_array($category_name) {
			
		$get_category = get_categories( array( 'taxonomy' => $category_name	));
		$category_list = array( 'all' => 'All');
		
		foreach( $get_category as $category ){
			if (isset($category->slug)) {
			$category_list[$category->slug] = $category->cat_name;
			}
		}
			
		return $category_list;
	}
	
	function dante_framework_get_menu_list() {
		$menu_list = array( '' => '' );
	    $user_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) ); 
	  
	    foreach( $user_menus as $menu ) {
			$menu_list[$menu->term_id] = $menu->name;
	    }
	    
	    return $menu_list;
	}

	function dante_framework_register_meta_boxes() {
		$prefix = 'sf_';
		
		global $meta_boxes;
		
		$meta_boxes = array();
			
		$options = get_option('sf_dante_options');
		$default_page_heading_bg_alt = $options['default_page_heading_bg_alt'];
		$default_show_page_heading = $options['default_show_page_heading'];
		$default_sidebar_config = $options['default_sidebar_config'];
		$default_left_sidebar = $options['default_left_sidebar'];
		$default_right_sidebar = $options['default_right_sidebar'];
		
		if (!$default_page_heading_bg_alt || $default_page_heading_bg_alt == "") {
			$default_page_heading_bg_alt = "none";
		}
		if ($default_show_page_heading == "") {
			$default_show_page_heading = 1;
		}
		if ($default_sidebar_config == "") {
			$default_sidebar_config = "no-sidebars";
		}		
		if ($default_left_sidebar == "") {
			$default_left_sidebar = "Sidebar-1";
		}		
		if ($default_right_sidebar == "") {
			$default_right_sidebar = "Sidebar-1";
		}
		
		$default_product_sidebar_config = $default_product_left_sidebar = $default_product_right_sidebar = "";
		$default_include_author_info = true;
		
		if (isset($options['default_product_sidebar_config'])) {
		$default_product_sidebar_config = $options['default_product_sidebar_config'];
		}
		if (isset($options['default_product_left_sidebar'])) {
		$default_product_left_sidebar = $options['default_product_left_sidebar'];
		}
		if (isset($options['default_product_right_sidebar'])) {
		$default_product_right_sidebar = $options['default_product_right_sidebar'];
		}
		if (isset($options['default_include_author_info'])) {
		$default_include_author_info = $options['default_include_author_info'];
		}
		
		if ($default_product_sidebar_config == "") {
			$default_product_sidebar_config = "no-sidebars";
		}
		if ($default_product_left_sidebar == "") {
			$default_product_left_sidebar = "Sidebar-1";
		}		
		if ($default_product_right_sidebar == "") {
			$default_product_right_sidebar = "Sidebar-1";
		}
		
		$registered_menus = array('' => '');	
		$registered_menus = array_merge($registered_menus, get_registered_nav_menus());
		
		
		/* Thumbnail Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'thumbnail_meta_box',
			'title' => __('Thumbnail Options', 'dante-framework'),
			'pages' => array( 'post', 'portfolio' ),
			'context' => 'normal',
			'fields' => array(
		
				// THUMBNAIL TYPE
				array(
					'name' => __('Thumbnail type', 'dante-framework'),
					'id'   => "{$prefix}thumbnail_type",
					'type' => 'select',
					'options' => array(
						'none'		=> 'None',
						'image'		=> 'Image',
						'video'		=> 'Video',
						'slider'	=> 'Slider'
					),
					'multiple' => false,
					'std'  => 'image',
					'desc' => __('Choose what will be used for the item thumbnail.', 'dante-framework')
				),
				
				// THUMBNAIL IMAGE
				array(
					'name'  => __('Thumbnail image', 'dante-framework'),
					'desc'  => __('The image that will be used as the thumbnail image.', 'dante-framework'),
					'id'    => "{$prefix}thumbnail_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// THUMBNAIL VIDEO
				array(
					'name' => __('Thumbnail video URL', 'dante-framework'),
					'id' => $prefix . 'thumbnail_video_url',
					'desc' => __('Enter the video url for the thumbnail. Only links from Vimeo & YouTube are supported.', 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// THUMBNAIL GALLERY
				array(
					'name'             => __('Thumbnail gallery', 'dante-framework'),
					'desc'             => __('The images that will be used in the thumbnail gallery.', 'dante-framework'),
					'id'               => "{$prefix}thumbnail_gallery",
					'type'             => 'image_advanced',
					'max_file_uploads' => 50,
				),
				
				// THUMBNAIL LINK TYPE
				array(
					'name' => __('Thumbnail link type', 'dante-framework'),
					'id'   => "{$prefix}thumbnail_link_type",
					'type' => 'select',
					'options' => array(
						'link_to_post'		=> __('Link to item', 'dante-framework'),
						'link_to_url'		=> __('Link to URL', 'dante-framework'),
						'link_to_url_nw'	=> __('Link to URL (New Window)', 'dante-framework'),
						'lightbox_thumb'	=> __('Lightbox to the thumbnail image', 'dante-framework'),
						'lightbox_image'	=> __('Lightbox to image (select below)', 'dante-framework'),
						'lightbox_video'	=> __('Fullscreen Video Overlay (input below)', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'link-to-post',
					'desc' => __('Choose what link will be used for the image(s) and title of the item.', 'dante-framework')
				),
				
				// THUMBNAIL LINK URL
				array(
					'name' => __('Thumbnail link URL', 'dante-framework'),
					'id' => $prefix . 'thumbnail_link_url',
					'desc' => __('Enter the url for the thumbnail link.', 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// THUMBNAIL LINK LIGHTBOX IMAGE
				array(
					'name'  => __('Thumbnail link lightbox image', 'dante-framework'),
					'desc'  => __('The image that will be used as the lightbox image.', 'dante-framework'),
					'id'    => "{$prefix}thumbnail_link_image",
					'type'  => 'thickbox_image'
				),
				
				// THUMBNAIL LINK LIGHTBOX VIDEO
				array(
					'name' => __('Thumbnail link lightbox video URL', 'dante-framework'),
					'id' => $prefix . 'thumbnail_link_video_url',
					'desc' => __('Enter the video url for the thumbnail lightbox. Only links from Vimeo & YouTube are supported.', 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
			)
		);
		
		
		/* Detail Media Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'detail_media_meta_box',
			'title' => __('Detail Media Options', 'dante-framework'),
			'pages' => array( 'post', 'portfolio' ),
			'context' => 'normal',
			'fields' => array(
			
				// USE THUMBNAIL CONTENT FOR THE MAIN DETAIL DISPLAY
				array(
					'name' => __('Use the thumbnail content', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}thumbnail_content_main_detail",
					'type' => 'checkbox',
					'desc' => __('Uncheck this box if you wish to select different media for the main detail display.', 'dante-framework'),
					'std' => 0,
				),
				
				// DETAIL TYPE
				array(
					'name' => __('Post detail type', 'dante-framework'),
					'id'   => "{$prefix}detail_type",
					'type' => 'select',
					'options' => array(
						'none'		=> __('None', 'dante-framework'),
						'image'		=> __('Image', 'dante-framework'),
						'video'		=> __('Video', 'dante-framework'),
						'slider'	=> __('Standard Slider', 'dante-framework'),
						'layer-slider' => __('Revolution/Layer Slider', 'dante-framework'),
						'custom' => __('Custom', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'image',
					'desc' => __('Choose what will be used for the post item detail.', 'dante-framework')
				),
				
				// DETAIL IMAGE
				array(
					'name'  => __('Post detail image', 'dante-framework'),
					'desc'  => __('The image that will be used as the post detail image.', 'dante-framework'),
					'id'    => "{$prefix}detail_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// DETAIL VIDEO
				array(
					'name' => __('Post detail video URL', 'dante-framework'),
					'id' => $prefix . 'detail_video_url',
					'desc' => __('Enter the video url for the post thumbnail. Only links from Vimeo & YouTube are supported.', 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// DETAIL GALLERY
				array(
					'name'             => __('Post detail gallery', 'dante-framework'),
					'desc'             => __('The images that will be used in the post detail gallery.', 'dante-framework'),
					'id'               => "{$prefix}detail_gallery",
					'type'             => 'image_advanced',
					'max_file_uploads' => 50,
				),
				
				// DETAIL REV SLIDER
				array(
					'name' => __('Revolution slider alias', 'dante-framework'),
					'id' => $prefix . 'detail_rev_slider_alias',
					'desc' => __("Enter the revolution slider alias for the slider that you want to show.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// DETAIL LAYER SLIDER
				array(
					'name' => __('Layer Slider alias', 'dante-framework'),
					'id' => $prefix . 'detail_layer_slider_alias',
					'desc' => __("Enter the Layer Slider ID for the slider that you want to show.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// DETAIL CUSTOM
				array(
					'name' => __('Custom detail display', 'dante-framework'),
					'desc' => __("If you'd like to provide your own detail media, please add it here", 'dante-framework'),
					'id'   => "{$prefix}custom_media",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
			)
		);
		
		/* Page Heading Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'page_heading_meta_box',
			'title' => __('Page Heading Options', 'dante-framework'),
			'pages' => array( 'post', 'portfolio', 'page', 'product', 'team', 'galleries' ),
			'context' => 'normal',
			'fields' => array(
				// SHOW PAGE TITLE
				array(
					'name' => __('Show page title', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}page_title",
					'type' => 'checkbox',
					'desc' => __('Show the page title at the top of the page.', 'dante-framework'),
					'std' => $default_show_page_heading,
				),
				
				// PAGE TITLE STYLE
				array(
					'name' => __('Page Title Style', 'dante-framework'),
					'id'   => "{$prefix}page_title_style",
					'type' => 'select',
					'options' => array(
						'standard'		=> __('Standard', 'dante-framework'),
						'fancy'		=> __('Fancy', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'standard',
					'desc' => __('Choose the heading style.', 'dante-framework')
				),
				
				// PAGE TITLE LINE 1
				array(
					'name' => __('Page Title', 'dante-framework'),
					'id' => $prefix . 'page_title_one',
					'desc' => __("Enter a custom page title if you'd like.", 'dante-framework'),
					'type'  => 'text',
					'std' => '',
				),
				
				// PAGE TITLE LINE 2
				array(
					'name' => __('Page Subtitle', 'dante-framework'),
					'id' => $prefix . 'page_subtitle',
					'desc' => __("Enter a custom page title if you'd like (Fancy Page Title Style Only).", 'dante-framework'),
					'type'  => 'text',
					'std' => '',
				),
				
				// REMOVE BREADCRUMBS
				array(
					'name' => __('Remove breadcrumbs', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}no_breadcrumbs",
					'type' => 'checkbox',
					'desc' => __('Remove the breadcrumbs on the page (only shown on standard page titles).', 'dante-framework'),
					'std' => 0,
				),
				
				// PAGE TITLE BACKGROUND
				array(
					'name' => __('Page Title Background', 'dante-framework'),
					'id'   => "{$prefix}page_title_bg",
					'type' => 'select',
					'options' => array(
						'none'			=> __('None', 'dante-framework'),
						'alt-one'		=> __('Alt 1', 'dante-framework'),
						'alt-two'		=> __('Alt 2', 'dante-framework'),
						'alt-three'		=> __('Alt 3', 'dante-framework'),
						'alt-four'		=> __('Alt 4', 'dante-framework'),
						'alt-five'		=> __('Alt 5', 'dante-framework'),
						'alt-six'		=> __('Alt 6', 'dante-framework'),
						'alt-seven'		=> __('Alt 7', 'dante-framework'),
						'alt-eight'		=> __('Alt 8', 'dante-framework'),
						'alt-nine'		=> __('Alt 9', 'dante-framework'),
						'alt-ten'		=> __('Alt 10', 'dante-framework')
					),
					'multiple' => false,
					'std'  => $default_page_heading_bg_alt,
					'desc' => __('Choose the background for the page title (configured in the Dante Options panel).', 'dante-framework'),
				),
				
				// ALT BG PREVIEW
				array (
					'name' 	=> '',
				    'id' 	=> "{$prefix}altbg-preview",
				    'type' 	=> 'altbgpreview'
				),
				
				// FANCY HEADING IMAGE UPLOAD
				array(
					'name'  => __('Fancy Heading Background Image', 'dante-framework'),
					'desc'  => __('The image that will be used as the background for the fancy header. This will override the alt background selection.', 'dante-framework'),
					'id'    => "{$prefix}page_title_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// FANCY HEADING TEXT STYLE
				array(
					'name' => __('Fancy Heading Text Style', 'dante-framework'),
					'id'   => "{$prefix}page_title_text_style",
					'type' => 'select',
					'options' => array(
						'light'		=> __('Light', 'dante-framework'),
						'dark'		=> __('Dark', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'light',
					'desc' => __('If you uploaded an image in the option above, choose light/dark styling for the text heading text here.', 'dante-framework')
				),
				
			
			)
		);
		
		
		
		/* Portfolio Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'portfolio_meta_box',
			'title' => __('Portfolio Meta', 'dante-framework'),
			'pages' => array( 'portfolio' ),
			'context' => 'normal',
			'fields' => array(
				
				// Sub Text
				array(
					'name' => __('Subtitle', 'dante-framework'),
					'id' => $prefix . 'portfolio_subtitle',
					'desc' => __("Enter a subtitle for use within the portfolio item index (optional).", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// External Link
				array(
					'name' => __('External Link', 'dante-framework'),
					'id' => $prefix . 'portfolio_external_link',
					'desc' => __("Enter an external link for the item  (optional) (NOTE: INCLUDE HTTP://).", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// CUSTOM EXCERPT
				array(
					'name' => __('Custom excerpt', 'dante-framework'),
					'desc' => __("You can optionally write a custom excerpt here to display instead of the excerpt that is automatically generated. If you use the page builder, then you'll want to add content to this box.", 'dante-framework'),
					'id'   => "{$prefix}custom_excerpt",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// FULL WIDTH MEDIA DISPLAY
				array(
					'name' => __('Media Display', 'dante-framework'),
					'id'   => "{$prefix}fw_media_display",
					'type' => 'select',
					'options' => array(
						'fw-media'		=> __('Full Width Media', 'dante-framework'),
						'split'		=> __('Split Media / Description', 'dante-framework'),
						'standard'	=> __('Standard', 'dante-framework'),
					),
					'multiple' => false,
					'std'  => 'standard',
					'desc' => __('Choose how you would like to display your selected media - full width (edge to edge), split, or standard (media with content below).', 'dante-framework')
				),
				
				array(
					'name' => __('Item Sidebar Content', 'dante-framework'),
					'desc' => __("You can optionally add some content here to display in the details column, including shortcodes etc. Only visible on Standard and Full Width Media display types.", 'dante-framework'),
					'id'   => "{$prefix}item_sidebar_content",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
							
				// HIDE DETAILS BAR
				array(
					'name' => __('Hide item details bar', 'dante-framework'),
					'id'   => "{$prefix}hide_details",
					'type' => 'checkbox',
					'desc' => __('Check this box to hide the item details on the detail page.', 'dante-framework'),
					'std' => 0,
				),
				
				// INCLUDE SOCIAL SHARING
				array(
					'name' => __('Include social sharing', 'dante-framework'),
					'id'   => "{$prefix}social_sharing",
					'type' => 'checkbox',
					'desc' => __('Check this box to show social sharing icons on the detail page.', 'dante-framework'),
					'std' => 1,
				),
				
				// SWIFT SLIDER BACKGROUND IMAGE
				array(
					'name'  => __('Slide background image', 'dante-framework'),
					'desc'  => __('The image that will be used as the slide image in the Swift Slider.', 'dante-framework'),
					'id'    => "{$prefix}posts_slider_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// SWIFT SLIDER CAPTION POSITION
				array(
					'name' => __('Caption Position', 'dante-framework'),
					'id'   => "{$prefix}caption_position",
					'type' => 'select',
					'options' => array(
						'caption-left'		=> __('Left', 'dante-framework'),
						'caption-right'		=> __('Right', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'caption-right',
					'desc' => __('Choose which side you would like to display the caption over the slide.', 'dante-framework')
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'dante-framework'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// REMOVE PROMO BAR
				array(
					'name' => __('Remove promo bar', 'dante-framework'),   // File type: checkbox
					'id'   => "{$prefix}remove_promo_bar",
					'type' => 'checkbox',
					'desc' => __('Remove the promo bar at the bottom of the page.', 'dante-framework'),
					'std' => 0,
				)
			)
		);
		
		
		/* Page Background Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'page_background_meta_box',
			'title' => __('Page Background Options', 'dante-framework'),
			'pages' => array( 'post', 'portfolio', 'page' ),
			'context' => 'normal',
			'fields' => array(
	
				// BACKGROUND IMAGE
				array(
					'name'  => __('Background Image', 'dante-framework'),
					'desc'  => __('The image that will be used as the OUTER page background image.', 'dante-framework'),
					'id'    => "{$prefix}background_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// BACKGROUND SIZE
				array(
					'name' => __('Background Image Size', 'dante-framework'),
					'desc' => __('For fullscreen images, choose Cover. For repeating patterns, choose Auto.', 'dante-framework'),
					'id'   => "{$prefix}background_image_size",
					'type' => 'select',
					'options' => array(
						'cover'		=> 'Cover',
						'auto'	=> 'Auto'
					),
					'multiple' => false,
					'std'  => 'cover',
				),
				
				// INNER BACKGROUND IMAGE
				array(
					'name'  => __('Inner Background Image', 'dante-framework'),
					'desc'  => __('The image that will be used as the INNER page background image.', 'dante-framework'),
					'id'    => "{$prefix}inner_background_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				)
				
			)
		);
		
		
		/* Post Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'post_meta_box',
			'title' => __('Post Meta', 'dante-framework'),
			'pages' => array( 'post' ),
			'context' => 'normal',
			'fields' => array(
				
				// CUSTOM EXCERPT
				array(
					'name' => __('Custom excerpt', 'dante-framework'),
					'desc' => __("You can optionally write a custom excerpt here to display instead of the excerpt that is automatically generated. If you use the page builder, then you'll want to add content to this box.", 'dante-framework'),
					'id'   => "{$prefix}custom_excerpt",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
							
				// FULL WIDTH MEDIA
				array(
					'name' => __('Full Width Media Display', 'dante-framework'),
					'id'   => "{$prefix}full_width_display",
					'type' => 'checkbox',
					'desc' => __('Check this box to show the detail media above the page content / sidebar config, rather than inside the page content.', 'dante-framework'),
					'std' => 0,
				),
				
				// INCLUDE AUTHOR INFO
				array(
					'name' => __('Include author info', 'dante-framework'),
					'id'   => "{$prefix}author_info",
					'type' => 'checkbox',
					'desc' => __('Check this box to show the author info box on the detail page.', 'dante-framework'),
					'std' => $default_include_author_info,
				),
				
				// INCLUDE SOCIAL SHARING
				array(
					'name' => __('Include social sharing', 'dante-framework'),
					'id'   => "{$prefix}social_sharing",
					'type' => 'checkbox',
					'desc' => __('Check this box to show social sharing icons on the detail page.', 'dante-framework'),
					'std' => 1,
				),
				
				// INCLUDE RELATED ARTICLES
				array(
					'name' => __('Include related articles', 'dante-framework'),
					'id'   => "{$prefix}related_articles",
					'type' => 'checkbox',
					'desc' => __('Check this box to show related articles on the detail page.', 'dante-framework'),
					'std' => 1,
				),
			
				// SIDEBAR CONFIG
				array(
					'name' => __('Sidebar configuration', 'dante-framework'),
					'id'   => "{$prefix}sidebar_config",
					'type' => 'select',
					'options' => array(
						'no-sidebars'		=> __('No Sidebars', 'dante-framework'),
						'left-sidebar'		=> __('Left Sidebar', 'dante-framework'),
						'right-sidebar'		=> __('Right Sidebar', 'dante-framework'),
						'both-sidebars'		=> __('Both Sidebars', 'dante-framework')
					),
					'multiple' => false,
					'std'  => $default_sidebar_config,
					'desc' => __('Choose the sidebar configuration for the detail page of this post.', 'dante-framework'),
				),
				
				// LEFT SIDEBAR
				array (
					'name' 	=> __('Left Sidebar', 'dante-framework'),
				    'id' 	=> "{$prefix}left_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_left_sidebar
				),
				
				// RIGHT SIDEBAR
				array (
					'name' 	=> __('Right Sidebar', 'dante-framework'),
				    'id' 	=> "{$prefix}right_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_right_sidebar
				),
				
				// SWIFT SLIDER BACKGROUND IMAGE
				array(
					'name'  => __('Slide background image', 'dante-framework'),
					'desc'  => __('The image that will be used as the slide image in the Swift Slider.', 'dante-framework'),
					'id'    => "{$prefix}posts_slider_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// SWIFT SLIDER CAPTION POSITION
				array(
					'name' => __('Caption Position', 'dante-framework'),
					'id'   => "{$prefix}caption_position",
					'type' => 'select',
					'options' => array(
						'caption-left'		=> __('Left', 'dante-framework'),
						'caption-right'		=> __('Right', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'caption-right',
					'desc' => __('Choose which side you would like to display the caption over the slide.', 'dante-framework'),
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'dante-framework'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// REMOVE PROMO BAR
				array(
					'name' => __('Remove promo bar', 'dante-framework'),   // File type: checkbox
					'id'   => "{$prefix}remove_promo_bar",
					'type' => 'checkbox',
					'desc' => __('Remove the promo bar at the bottom of the page.', 'dante-framework'),
					'std' => 0,
				)
				
			)
		);
		
		
		/* Product Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'product_meta_box',
			'title' => __('Product Meta', 'dante-framework'),
			'pages' => array( 'product' ),
			'context' => 'normal',
			'fields' => array(

				// PRODUCT DESCRIPTION
				array(
					'name' => __('Product Short Description', 'dante-framework'),
					'desc' => __("You can optionally write a short description here, which shows above the variations/shopping bag options.", 'dante-framework'),
					'id'   => "{$prefix}product_short_description",
					'type' => 'wysiwyg',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// PRODUCT DESCRIPTION
				array(
					'name' => __('Product Description', 'dante-framework'),
					'desc' => __("You can optionally write a product description here, which shows under the description accordion heading if you have the page builder enabled for product pages.", 'dante-framework'),
					'id'   => "{$prefix}product_description",
					'type' => 'wysiwyg',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// SIDEBAR CONFIG
				array(
					'name' => __('Sidebar configuration', 'dante-framework'),
					'id'   => "{$prefix}sidebar_config",
					'type' => 'select',
					// Array of 'key' => 'value' pairs for select box
					'options' => array(
						'no-sidebars'		=> __('No Sidebars', 'dante-framework'),
						'left-sidebar'		=> __('Left Sidebar', 'dante-framework'),
						'right-sidebar'		=> __('Right Sidebar', 'dante-framework'),
						'both-sidebars'		=> __('Both Sidebars', 'dante-framework')
					),
					// Select multiple values, optional. Default is false.
					'multiple' => false,
					// Default value, can be string (single value) or array (for both single and multiple values)
					'std'  => $default_product_sidebar_config,
					'desc' => __('Choose the sidebar configuration for the detail page of this product.', 'dante-framework'),
				),
				
				// LEFT SIDEBAR
				array (
					'name' 	=> __('Left Sidebar', 'dante-framework'),
				    'id' 	=> "{$prefix}left_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_product_left_sidebar
				),
				
				// RIGHT SIDEBAR
				array (
					'name' 	=> __('Right Sidebar', 'dante-framework'),
				    'id' 	=> "{$prefix}right_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_product_right_sidebar
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'dante-framework'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// REMOVE PROMO BAR
				array(
					'name' => __('Remove promo bar', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}remove_promo_bar",
					'type' => 'checkbox',
					'desc' => __('Remove the promo bar at the bottom of the page.', 'dante-framework'),
					'std' => 0,
				)
				
			)
		);
		
		
		/* Team Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'team_meta_box',
			'title' => __('Team Member Meta', 'dante-framework'),
			'pages' => array( 'team' ),
			'fields' => array(
				
				// CUSTOM EXCERPT
				array(
					'name' => __('Custom excerpt', 'dante-framework'),
					'desc' => __("You can optionally write a custom excerpt here to display instead of the excerpt that is automatically generated (this is needed if you use the page builder above).", 'dante-framework'),
					'id'   => "{$prefix}custom_excerpt",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// TEAM MEMBER POSITION
				array(
					'name' => __('Position', 'dante-framework'),
					'id' => $prefix . 'team_member_position',
					'desc' => __("Enter the team member's position within the team.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER EMAIL
				array(
					'name' => __('Email Address', 'dante-framework'),
					'id' => $prefix . 'team_member_email',
					'desc' => __("Enter the team member's email address.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER PHONE NUMBER
				array(
					'name' => __('Phone Number', 'dante-framework'),
					'id' => $prefix . 'team_member_phone_number',
					'desc' => __("Enter the team member's phone number.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER TWITTER
				array(
					'name' => __('Twitter', 'dante-framework'),
					'id' => $prefix . 'team_member_twitter',
					'desc' => __("Enter the team member's Twitter username.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER FACEBOOK
				array(
					'name' => __('Facebook', 'dante-framework'),
					'id' => $prefix . 'team_member_facebook',
					'desc' => __("Enter the team member's Facebook URL.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER LINKEDIN
				array(
					'name' => __('LinkedIn', 'dante-framework'),
					'id' => $prefix . 'team_member_linkedin',
					'desc' => __("Enter the team member's LinkedIn URL.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER GOOGLE+
				array(
					'name' => __('Google+', 'dante-framework'),
					'id' => $prefix . 'team_member_google_plus',
					'desc' => __("Enter the team member's Google+ URL.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER SKYPE
				array(
					'name' => __('Skype', 'dante-framework'),
					'id' => $prefix . 'team_member_skype',
					'desc' => __("Enter the team member's Skype username.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER INSTAGRAM
				array(
					'name' => __('Instagram', 'dante-framework'),
					'id' => $prefix . 'team_member_instagram',
					'desc' => __("Enter the team member's Instragram URL (e.g. http://hashgr.am/).", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER DRIBBBLE
				array(
					'name' => __('Dribbble', 'dante-framework'),
					'id' => $prefix . 'team_member_dribbble',
					'desc' => __("Enter the team member's Dribbble username.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER XING
				array(
					'name' => __('Xing', 'dante-framework'),
					'id' => $prefix . 'team_member_xing',
					'desc' => __("Enter the team member's Xing URL.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
			)
		);
		
		
		/* Clients Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'client_meta_box',
			'title' => __('Client Meta', 'dante-framework'),
			'pages' => array( 'clients' ),
			'fields' => array(
				
				// CLIENT IMAGE LINK
				array(
					'name' => __('Client Link', 'dante-framework'),
					'id' => $prefix . 'client_link',
					'desc' => __("Enter the link for the client if you want the image to be clickable.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => ''
				),
				
				// CLIENT LINK TARGET
				array(
					'name' => __('Link to same window', 'dante-framework'),
					'id'   => "{$prefix}client_link_same_window",
					'type' => 'checkbox',
					'desc' => __('Check this box to set the client link to open in the same browser window/tab.', 'dante-framework'),
					'std' => 1,
				),
			)	
		);
		
		
		/* Testimonials Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'testimonials_meta_box',
			'title' => __('Testimonial Meta', 'dante-framework'),
			'pages' => array( 'testimonials' ),
			'fields' => array(
				
				// TESTIMONAIL CITE
				array(
					'name' => __('Testimonial Cite', 'dante-framework'),
					'id' => $prefix . 'testimonial_cite',
					'desc' => __("Enter the cite name for the testimonial.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => ''
				),
				
				// TESTIMONAIL CITE
				array(
					'name' => __('Testimonial Cite Subtext', 'dante-framework'),
					'id' => $prefix . 'testimonial_cite_subtext',
					'desc' => __("Enter the cite subtext for the testimonial (optional).", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => ''
				),
				
				// TESTIMONAIL IMAGE
				array(
					'name'  => __('Testimonial Cite Image', 'dante-framework'),
					'desc'  => __('Enter the cite image for the testimonial (optional).', 'dante-framework'),
					'id'    => "{$prefix}testimonial_cite_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
			)	
		);
		
		
		/* Slider Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'slider_meta_box',
			'title' => __('Page Slider Options', 'dante-framework'),
			'pages' => array( 'page' ),
			'fields' => array(
				
				// SHOW SWIFT SLIDER
				array(
					'name' => __('Show Swift Slider', 'dante-framework'),
					'id'   => "{$prefix}posts_slider",
					'type' => 'checkbox',
					'desc' => __('Show the Swift Slider at the top of the page.', 'dante-framework'),
					'std' => 0,
				),
				
				// SWIFT SLIDER TYPE
				array(
					'name' => __('Swift Slider Type', 'dante-framework'),
					'id'   => "{$prefix}posts_slider_type",
					'type' => 'select',
					'options' => array(
						'post'		=> __('Posts', 'dante-framework'),
						'portfolio'	=> __('Portfolio', 'dante-framework'),
						'hybrid'	=> __('Hybrid', 'dante-framework')
					),
					'multiple' => false,
					'std'  => 'post',
					'desc' => __('Choose the post type to display in the Swift Slider.', 'dante-framework'),
				),
				
				// SWIFT SLIDER CATEGORY
				array(
					'name' => __('Swift Slider category', 'dante-framework'),
					'id'   => "{$prefix}posts_slider_category",
					'type' => 'select',
					'desc' => __('Select the category for which the Swift Slider should show posts from.', 'dante-framework'),
					'options' => dante_framework_get_category_list_key_array('category'),
					'std' => '',
				),
				
				// SWIFT SLIDER PORTFOLIO CATEGORY
				array(
					'name' => __('Swift Slider portfolio category', 'dante-framework'),
					'id'   => "{$prefix}posts_slider_portfolio_category",
					'type' => 'select',
					'desc' => __('Select the category for which the Swift Slider should show portfolio items from.', 'dante-framework'),
					'options' => dante_framework_get_category_list_key_array('portfolio-category'),
					'std' => '',
				),
				
				// SWIFT SLIDER COUNT
				array(
					'name' => __('Swift Slider count', 'dante-framework'),
					'id' => $prefix . 'posts_slider_count',
					'desc' => __("The number of posts to show in the Swift Slider.", 'dante-framework'),
					'type'  => 'text',
					'std' => '5',
				),
				
				// SHOW FULL WIDTH REV SLIDER
				array(
					'name' => __('Revolution slider alias', 'dante-framework'),
					'id' => $prefix . 'rev_slider_alias',
					'desc' => __("Enter the revolution slider alias for the slider that you want to show. NOTE: If you have the Swift Slider enabled above, then this will be ignored.", 'dante-framework'),
					'type'  => 'text',
					'std' => '',
				),
				
				// SHOW FULL WIDTH REV SLIDER
				array(
					'name' => __('LayerSlider ID', 'dante-framework'),
					'id' => $prefix . 'layerslider_id',
					'desc' => __("Enter the LayerSlider ID for the slider that you want to show. NOTE: If you have the Swift Slider enabled above, then this will be ignored.", 'dante-framework'),
					'type'  => 'text',
					'std' => '',
				)
			)	
		);
		
			
		/* Page Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'page_meta_box',
			'title' => __('Page Meta', 'dante-framework'),
			'pages' => array( 'page' ),
			'fields' => array(
				
				// NAKED HEADER
				array(
					'name' => __('Enable Naked Header', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}enable_naked_header",
					'type' => 'select',
					'options' => array(
						''		=> __('Standard Header', 'dante-framework'),
						'naked-light'		=> __('Naked (Light)', 'dante-framework'),
						'naked-dark'		=> __('Naked (Dark)', 'dante-framework'),
					),
					'desc' => __('Enable naked header on this page. NOTE: It is important that you use a page slider for the naked header to be shown over.', 'dante-framework'),
					'std' => '',
				),
				
				// SIDEBAR CONFIG
				array(
					'name' => __('Sidebar configuration', 'dante-framework'),
					'id'   => "{$prefix}sidebar_config",
					'type' => 'select',
					'options' => array(
						'no-sidebars'		=> __('No Sidebars', 'dante-framework'),
						'left-sidebar'		=> __('Left Sidebar', 'dante-framework'),
						'right-sidebar'		=> __('Right Sidebar', 'dante-framework'),
						'both-sidebars'		=> __('Both Sidebars', 'dante-framework')
					),
					'multiple' => false,
					'std'  => $default_sidebar_config,
					'desc' => __('Choose the sidebar configuration for the detail page of this page.', 'dante-framework'),
				),
				
				// LEFT SIDEBAR
				array (
					'name' 	=> __('Left Sidebar', 'dante-framework'),
				    'id' 	=> "{$prefix}left_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_left_sidebar
				),
				
				// RIGHT SIDEBAR
				array (
					'name' 	=> __('Right Sidebar', 'dante-framework'),
				    'id' 	=> "{$prefix}right_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_right_sidebar
				),
				
				// REMOVE PROMO BAR
				array(
					'name' => __('Enable One Page Navigation', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}enable_one_page_nav",
					'type' => 'checkbox',
					'desc' => __('Enable the one page nav which appears on the right of the page.', 'dante-framework'),
					'std' => 0,
				),
				
				// PAGE MENU
				array(
					'name' => __('Page Menu', 'swift-framework-admin'),
					'id'   => "{$prefix}page_menu",
					'type' => 'select',
					'options' => dante_framework_get_menu_list(),
					'multiple' => false,
					'std'  => '',
					'desc' => __('Optionally you can choose to override the menu that is used on the page. This is ideal if you want to create a page with a anchor link scroll menu.', 'swift-framework-admin'),
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'dante-framework'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'dante-framework'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// REMOVE PROMO BAR
				array(
					'name' => __('Remove promo bar', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}remove_promo_bar",
					'type' => 'checkbox',
					'desc' => __('Remove the promo bar at the bottom of the page.', 'dante-framework'),
					'std' => 0,
				),
				
				// REMOVE TOP SPACING
				array(
					'name' => __('Remove top spacing', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}no_top_spacing",
					'type' => 'checkbox',
					'desc' => __('Remove the spacing at the top of the page.', 'dante-framework'),
					'std' => 0,
				),
				
				// REMOVE BOTTOM SPACING
				array(
					'name' => __('Remove bottom spacing', 'dante-framework'),    // File type: checkbox
					'id'   => "{$prefix}no_bottom_spacing",
					'type' => 'checkbox',
					'desc' => __('Remove the spacing at the bottom of the page.', 'dante-framework'),
					'std' => 0,
				)
			)
		);
	
	
		/* Gallery Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'gallery_meta_box',
			'title' => __('Gallery Options', 'dante-framework'),
			'pages' => array( 'galleries' ),
			'context' => 'normal',
			'fields' => array(
		
				// GALLERY IMAGES
				array(
					'name'             => __('Gallery Images', 'dante-framework'),
					'desc'             => __('The images that will be used in the gallery.', 'dante-framework'),
					'id'               => "{$prefix}gallery_images",
					'type'             => 'image_advanced',
					'max_file_uploads' => 200,
				)
			)
		);
	
		return $meta_boxes;
	}
	add_filter( 'rwmb_meta_boxes', 'dante_framework_register_meta_boxes' );

<?php
	
	/*
	*
	*	Dante Functions - Child Theme
	*	------------------------------------------------
	*	These functions will override the parent theme
	*	functions. We have provided some examples below.
	*
	*
	*/

	/* LOAD PARENT THEME STYLES
	================================================== */
	function dante_child_enqueue_styles() {
	    wp_enqueue_style( 'dante-parent-style', get_template_directory_uri() . '/style.css', array('bootstrap'), NULL );
	
	}
	add_action( 'wp_enqueue_scripts', 'dante_child_enqueue_styles' );
		

	/* REORDER PRODUCT TABS
	================================================== */
	function woo_reorder_tabs( $tabs ) {
		$tabs['description']['priority'] = 25;
		return $tabs;	
	}
	add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );


	// OVERWRITE PAGE BUILDER ASSETS
//	function spb_regsiter_assets() {
//		require_once( get_stylesheet_directory_uri() . '/default.php' );
//	}
//	if (is_admin()) {
//	add_action('admin_init', 'spb_regsiter_assets', 2);
//	}
//	if (!is_admin()) {
//	add_action('wp', 'spb_regsiter_assets', 2);
//	}
	
	
//	function sf_blog_aux($width) {
//		
//		$blog_aux_output = "";
//		$options = get_option('sf_dante_options');
//		$filter_wrap_bg = $options['filter_wrap_bg'];
//		$rss_feed_url = $options['rss_feed_url'];
//		
//		 		
//		$category_list = wp_list_categories('sort_column=name&title_li=&depth=1&number=10&echo=0&show_count=1');
//		$archive_list =  wp_get_archives('type=monthly&limit=12&echo=0');
//		$tags_list = wp_tag_cloud('smallest=12&largest=12&unit=px&format=list&number=50&orderby=name&echo=0');
//		$author_list = wp_list_authors('echo=0');
//		
//		$blog_aux_output .= '<div class="blog-aux-wrap row">'; // open .blog-aux-wrap
//		$blog_aux_output .= '<ul class="blog-aux-options bar-styling '.$width.'">'; // open .blog-aux-options
//		
//		// CATEGORIES
//		$blog_aux_output .= '<li><a href="#" class="blog-slideout-trigger" data-aux="categories"><i class="ss-index"></i>'.__("Categories", "swiftframework").'</a>';
//		
//		// AUTHORS
//		$blog_aux_output .= '<li><a href="#" class="blog-slideout-trigger" data-aux="authors"><i class="ss-user"></i>'.__("Authors", "swiftframework").'</a>';
//		
//		// SEARCH FORM
//		$blog_aux_output .= '<li class="search"><form method="get" class="search-form" action="'. home_url().'/">';
//		$blog_aux_output .= '<input type="text" placeholder="'. __("Search", "swiftframework") .'" name="s" />';
//		$blog_aux_output .= '</form></li>';
//		
//		// ARCHIVES
//		$blog_aux_output .= '<li><a href="#" class="blog-slideout-trigger" data-aux="archives"><i class="ss-storagebox"></i>'.__("Archives", "swiftframework").'</a>';
//		
//		// RSS LINK
//		if ($rss_feed_url != "") {
//		$blog_aux_output .= '<li><a href="'.$rss_feed_url.'" class="rss-link" target="_blank"><i class="fa-rss"></i>'.__("RSS", "swiftframework").'</a>';
//		}
//		
//		$blog_aux_output .= '</ul>'; // close .blog-aux-options
//		$blog_aux_output .= '</div>'; // close .blog-aux-wrap
//		
//		$blog_aux_output .= '<div class="filter-wrap slideout-filter blog-filter-wrap row clearfix">'; // open .blog-filter-wrap
//		$blog_aux_output .= '<div class="filter-slide-wrap col-sm-12 alt-bg '.$filter_wrap_bg.'">';
//		
//		if ($category_list != '') {  
//		    $blog_aux_output .= '<ul class="aux-list aux-categories row clearfix">'.$category_list.'</ul>';  
//		}
//		if ($author_list != '') {
//			$blog_aux_output .= '<ul class="aux-list aux-authors row clearfix">'.$author_list.'</ul>';
//		}
//		
//		if ($archive_list != '') {  
//		    $blog_aux_output .= '<ul class="aux-list aux-archives row clearfix">'.$archive_list.'</ul>';  
//		}
//		
//		$blog_aux_output .='</div></div>'; // close .blog-filter-wrap
//		
//		
//		/* AUX BUTTONS OUTPUT
//		================================================== */
//		return $blog_aux_output;	
//	
//	}
	
?>
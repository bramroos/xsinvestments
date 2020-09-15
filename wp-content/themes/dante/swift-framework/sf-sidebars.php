<?php
	
	/*
	*
	*	Swift Framework Sidebar Functions
	*	------------------------------------------------
	*	Swift Framework v2.0
	* 	Copyright Swift Ideas 2019 - http://www.swiftideas.net
	*
	*	dante_setup_sidebars()
	*	dante_sidebars_array()
	*	dante_set_sidebar_global()
	*
	*/
	
	/* REGISTER SIDEBARS
	================================================== */
	if (!function_exists('dante_register_sidebars')) {
		function dante_register_sidebars() {
			if (function_exists('register_sidebar')) {
			
				$options = get_option('sf_dante_options');
				if (isset($options['footer_layout'])) {
				$footer_config = $options['footer_layout'];
				} else {
				$footer_config = 'footer-1';
				}
			    register_sidebar(array(
			    	'id' => 'sidebar-1',
			        'name' => 'Sidebar One',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
			        'after_title' => '</span></h4></div>',
			    ));
			    register_sidebar(array(
			    	'id' => 'sidebar-2',
			        'name' => 'Sidebar Two',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
			        'after_title' => '</span></h4></div>',
			    ));
				register_sidebar(array(
					'id' => 'sidebar-3',
					'name' => 'Sidebar Three',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-4',
					'name' => 'Sidebar Four',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-5',
				    'name' => 'Sidebar Five',
				    'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
				    'after_widget' => '</section>',
				    'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
				    'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-6',
				    'name' => 'Sidebar Six',
				    'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
				    'after_widget' => '</section>',
				    'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
				    'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-7',
					'name' => 'Sidebar Seven',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-8',
					'name' => 'Sidebar Eight',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
					'after_title' => '</span></h4></div>',
				));
			    register_sidebar(array(
			    	'id' => 'footer-column-1',
			        'name' => 'Footer Column 1',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h6>',
			        'after_title' => '</h6></div>',
			    ));
			    if ($footer_config != "footer-9") {
			    register_sidebar(array(
			    	'id' => 'footer-column-2',
			        'name' => 'Footer Column 2',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h6>',
			        'after_title' => '</h6></div>',
			    ));
			    register_sidebar(array(
			    	'id' => 'footer-column-3',
			        'name' => 'Footer Column 3',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h6>',
			        'after_title' => '</h6></div>',
			    ));
			    }
			    if ($footer_config == "footer-1") {
			    register_sidebar(array(
			    	'id' => 'footer-column-4',
			        'name' => 'Footer Column 4',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h6>',
			        'after_title' => '</h6></div>',
			    ));
			    }
			    register_sidebar(array(
			        'id' => 'woocommerce-sidebar',
			        'name' => 'WooCommerce Sidebar',
			        'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
			        'after_widget' => '</section>',
			        'before_title' => '<div class="widget-heading clearfix"><h4 class="spb-heading"><span>',
			        'after_title' => '</span></h4></div>',
			    ));
			}
		}
		add_action( 'widgets_init', 'dante_register_sidebars');
	}
	
	
	/* GET SIDEBARS ARRAY
	================================================== */
	function dante_sidebars_array() {
	 	$sidebars = array();
	 	
	 	foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
	 		$sidebars[ucwords($sidebar['id'])] = $sidebar['name'];
	 	}
	 	return $sidebars;
	}
	
	
	/* SET SIDEBAR GLOBAL
	================================================== */
	function dante_set_sidebar_global($sidebar_config) {
		global $dante_sidebar_config;
		if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) {
		$dante_sidebar_config = 'one-sidebar';
		} else if ($sidebar_config == "both-sidebars") {
		$dante_sidebar_config = 'both-sidebars';
		} else {
		$dante_sidebar_config = 'no-sidebars';
		}
	}
	
?>
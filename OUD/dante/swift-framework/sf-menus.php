<?php
	
	/*
	*
	*	Swift Framework Menu Functions
	*	------------------------------------------------
	*	Swift Framework v2.0
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.net
	*
	*	dante_setup_menus()
	*
	*/
	
	
	/* CUSTOM MENU SETUP
	================================================== */
	if (!function_exists('dante_setup_menus')) {
		function dante_setup_menus() {
			// This theme uses wp_nav_menu() in four locations.
			register_nav_menus( array(
			'main_navigation' => __( 'Main Menu', 'dante' ),
			'mobile_menu' => __( 'Mobile Menu', 'dante' ),
			'top_bar_menu' => __( 'Top Bar Menu', 'dante' ),
			'footer_menu' => __( 'Footer Menu', 'dante' )
			) );
		}
		add_action( 'init', 'dante_setup_menus' );
	}
	
	
?>
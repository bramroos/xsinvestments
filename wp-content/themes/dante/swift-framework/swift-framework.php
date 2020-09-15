<?php

	/*
	*
	*	Swift Framework Main Class
	*	------------------------------------------------
	*	Swift Framework v2.0
	* 	Copyright Swift Ideas 2019 - http://www.swiftideas.net
	*
	*/

	/* SWIFT FUNCTIONS
	================================================== */
	include_once(DANTE_FRAMEWORK_PATH . '/sf-functions.php');


	/* CORE
	================================================== */ 
	require_once(DANTE_FRAMEWORK_PATH . '/sf-comments.php');
	require_once(DANTE_FRAMEWORK_PATH . '/sf-formatting.php');
	require_once(DANTE_FRAMEWORK_PATH . '/sf-media.php');
	require_once(DANTE_FRAMEWORK_PATH . '/sf-menus.php');
	require_once(DANTE_FRAMEWORK_PATH . '/sf-sidebars.php');


	/* CUSTOMISER OPTIONS
	================================================== */ 
	require_once (DANTE_FRAMEWORK_PATH . '/sf-customizer-options.php');


	/* CONTENT FUNCTIONS
	================================================== */  
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-blog.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-header.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-page-heading.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-pagination.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-portfolio-detail.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-portfolio.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-post-formats.php');
	include_once(DANTE_FRAMEWORK_PATH . '/sf-content-display/sf-products.php');


	/* WOOCOMMERCE FILTERS/HOOKS
	================================================== */
	if ( dante_woocommerce_activated() ) {
	    include_once( DANTE_FRAMEWORK_PATH . '/sf-supersearch.php' );
	}  
	include_once(DANTE_FRAMEWORK_PATH . '/sf-woocommerce.php');


	/* MEGA MENU
	================================================== */
	include_once(DANTE_FRAMEWORK_PATH . '/sf-megamenu/sf-megamenu.php');


	/* CUSTOM STYLES
	================================================== */  
	include(DANTE_FRAMEWORK_PATH . '/sf-custom-styles.php');
	
	
	/* STYLESWITCHER
	================================================== */  
	include(DANTE_FRAMEWORK_PATH . '/sf-styleswitcher/sf-styleswitcher.php');


	/* THEME UPDATER FRAMEWORK
	================================================== */  
	require_once(DANTE_FRAMEWORK_PATH . '/theme_update_check.php');
	$DanteUpdateChecker = new ThemeUpdateChecker(
	    'dante',
	    'https://kernl.us/api/v1/theme-updates/5667fe730a25612471e649f2/'
	);
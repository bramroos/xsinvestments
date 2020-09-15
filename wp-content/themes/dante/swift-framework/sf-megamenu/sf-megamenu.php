<?php

	/*
	*
	*	SF MEGA MENU FRAMEWORK
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2019 - http://www.swiftideas.net
	*
	*/

	class dante_mega_menu {

		/*--------------------------------------------*
		 * Constructor
		 *--------------------------------------------*/

		/**
		 * Initializes the plugin by setting localization, filters, and administration functions.
		 */
		function __construct() {

			// add custom menu fields to menu
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'dante_mega_menu_add_custom_nav_fields' ) );

			// save menu custom fields
			add_action( 'wp_update_nav_menu_item', array( $this, 'dante_mega_menu_update_custom_nav_fields'), 10, 3 );

			// edit menu walker
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'dante_mega_menu_edit_walker'), 10, 2 );

		} // end constructor

		/**
		 * Add custom fields to $item nav object
		 * in order to be used in custom Walker
		 *
		 * @access      public
		 * @since       1.0
		 * @return      void
		*/
		function dante_mega_menu_add_custom_nav_fields( $menu_item ) {

		    $menu_item->subtitle = dante_get_post_meta( $menu_item->ID, '_menu_item_subtitle', true );
		    $menu_item->htmlcontent = dante_get_post_meta( $menu_item->ID, '_menu_item_htmlcontent', true );
		    $menu_item->nocolumnspacing = dante_get_post_meta( $menu_item->ID, '_menu_nocolumnspacing', true );
		    $menu_item->ismegamenu = dante_get_post_meta( $menu_item->ID, '_menu_is_megamenu', true );
		    $menu_item->isnaturalwidth = dante_get_post_meta( $menu_item->ID, '_menu_is_naturalwidth', true );
		    $menu_item->altstyle = dante_get_post_meta( $menu_item->ID, '_menu_is_altstyle', true );
		    $menu_item->hideheadings = dante_get_post_meta( $menu_item->ID, '_menu_hideheadings', true );
		    $menu_item->megatitle = dante_get_post_meta( $menu_item->ID, '_menu_megatitle', true );
		    $menu_item->menuicon = dante_get_post_meta( $menu_item->ID, '_menu_item_icon', true );
		    $menu_item->menuwidth = dante_get_post_meta( $menu_item->ID, '_menu_item_width', true );
		    $menu_item->loggedinvis     = get_post_meta( $menu_item->ID, '_menu_loggedinvis', true );
		    $menu_item->loggedoutvis    = get_post_meta( $menu_item->ID, '_menu_loggedoutvis', true );
		    $menu_item->menuitembtn     = get_post_meta( $menu_item->ID, '_menu_menuitembtn', true );
		    return $menu_item;

		}

		/**
		 * Save menu custom fields
		 *
		 * @access      public
		 * @since       1.0
		 * @return      void
		*/
		function dante_mega_menu_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {

		    // Check if element is properly sent
		    if ( isset( $_REQUEST['menu-item-subtitle']) ) {
		        $subtitle_value = $_REQUEST['menu-item-subtitle'][$menu_item_db_id];
		        update_post_meta( $menu_item_db_id, '_menu_item_subtitle', $subtitle_value );
		    }

		    if ( isset( $_REQUEST['menu-item-icon']) ) {
		        $menu_icon_value = $_REQUEST['menu-item-icon'][$menu_item_db_id];
		        update_post_meta( $menu_item_db_id, '_menu_item_icon', $menu_icon_value );
		    }

		    if ( isset( $_REQUEST['menu-item-htmlcontent'][$menu_item_db_id]) ) {
		        $menu_htmlcontent_value = $_REQUEST['menu-item-htmlcontent'][$menu_item_db_id];
		        update_post_meta( $menu_item_db_id, '_menu_item_htmlcontent', $menu_htmlcontent_value );
		    }

		    if ( isset( $_REQUEST['menu-is-naturalwidth'][$menu_item_db_id]) ) {
		    	update_post_meta( $menu_item_db_id, '_menu_is_naturalwidth', 1 );
	    	} else {
	    		update_post_meta( $menu_item_db_id, '_menu_is_naturalwidth', 0 );
	    	}

		    if ( isset( $_REQUEST['menu-menuitembtn'][ $menu_item_db_id ] ) ) {
                update_post_meta( $menu_item_db_id, '_menu_menuitembtn', 1 );
            } else {
                update_post_meta( $menu_item_db_id, '_menu_menuitembtn', 0 );
            }

            if ( isset( $_REQUEST['menu-loggedinvis'][ $menu_item_db_id ] ) ) {
                update_post_meta( $menu_item_db_id, '_menu_loggedinvis', 1 );
            } else {
                update_post_meta( $menu_item_db_id, '_menu_loggedinvis', 0 );
            }

            if ( isset( $_REQUEST['menu-loggedoutvis'][ $menu_item_db_id ] ) ) {
                update_post_meta( $menu_item_db_id, '_menu_loggedoutvis', 1 );
            } else {
                update_post_meta( $menu_item_db_id, '_menu_loggedoutvis', 0 );
            }

		    if ( isset( $_REQUEST['menu-is-altstyle'][$menu_item_db_id]) ) {
		        update_post_meta( $menu_item_db_id, '_menu_is_altstyle', 1 );
		    } else {
		    	update_post_meta( $menu_item_db_id, '_menu_is_altstyle', 0 );
		    }

		    if ( isset( $_REQUEST['menu-hideheadings'][$menu_item_db_id]) ) {
		        update_post_meta( $menu_item_db_id, '_menu_hideheadings', 1 );
		    } else {
		    	update_post_meta( $menu_item_db_id, '_menu_hideheadings', 0 );
		    }

		    if ( isset( $_REQUEST['menu-megatitle'][$menu_item_db_id]) ) {
		        update_post_meta( $menu_item_db_id, '_menu_megatitle', 1 );
		    } else {
		    	update_post_meta( $menu_item_db_id, '_menu_megatitle', 0 );
		    }

		    if ( isset( $_REQUEST['menu-nocolumnspacing'][$menu_item_db_id]) ) {
		        update_post_meta( $menu_item_db_id, '_menu_nocolumnspacing', 1 );
		    } else {
		    	update_post_meta( $menu_item_db_id, '_menu_nocolumnspacing', 0 );
		    }

		    if ( isset( $_REQUEST['menu-item-width'][$menu_item_db_id]) ) {
		        $menu_width_value = $_REQUEST['menu-item-width'][$menu_item_db_id];
		        update_post_meta( $menu_item_db_id, '_menu_item_width', $menu_width_value );
		    }

		}

		/**
		 * Define new Walker edit
		 *
		 * @access      public
		 * @since       1.0
		 * @return      void
		*/
		function dante_mega_menu_edit_walker($walker,$menu_id) {

		    return 'Walker_Nav_Menu_Edit_Custom';

		}

	}

	// instantiate plugin's class
	$GLOBALS['dante_mega_menu'] = new dante_mega_menu();


	include_once( 'edit_custom_walker.php' );
	include_once( 'custom_walker.php' );
	include_once( 'mobile_menu_custom_walker.php' );

?>
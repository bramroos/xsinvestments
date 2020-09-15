<?php

    /* ==================================================

    Team Post Type Functions

    ================================================== */


    /* TEAM CATEGORY
    ================================================== */
    if ( !function_exists('sf_team_category_register') ) {
        function sf_team_category_register() {

            $team_permalinks = get_option( 'sf_team_permalinks' );

            $args = array(
                "label"             => __( 'Team Categories', 'dante-framework' ),
                "singular_label"    => __( 'Team Category', 'dante-framework' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $team_permalinks['category_base'] ) ? __( 'team-category', 'dante-framework' ) : __( $team_permalinks['category_base']  , 'dante-framework' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'team-category', 'team', $args );
        }
        add_action( 'init', 'sf_team_category_register' );
    }


    /* TEAM POST TYPE
    ================================================== */
    if ( !function_exists('sf_team_register') ) {
        function sf_team_register() {

            $team_permalinks = get_option( 'sf_team_permalinks' );
            $team_permalink  = empty( $team_permalinks['team_base'] ) ? __( 'team', 'dante-framework' ) : __( $team_permalinks['team_base']  , 'dante-framework' );

            $labels = array(
                'name'               => __( 'Team', 'dante-framework' ),
                'singular_name'      => __( 'Team Member', 'dante-framework' ),
                'add_new'            => __( 'Add New', 'dante-framework' ),
                'add_new_item'       => __( 'Add New Team Member', 'dante-framework' ),
                'edit_item'          => __( 'Edit Team Member', 'dante-framework' ),
                'new_item'           => __( 'New Team Member', 'dante-framework' ),
                'view_item'          => __( 'View Team Member', 'dante-framework' ),
                'search_items'       => __( 'Search Team Members', 'dante-framework' ),
                'not_found'          => __( 'No team members have been added yet', 'dante-framework' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'dante-framework' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-groups',
                'rewrite'           => $team_permalink != "team" ? array(
                    'slug'       => untrailingslashit( $team_permalink ),
                    'with_front' => false,
                    'feeds'      => true
                )
                    : false,
                'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
                'has_archive'       => true,
                'taxonomies'        => array( 'team-category', 'post_tag' )
            );

            register_post_type( 'team', $args );
        }
        add_action( 'init', 'sf_team_register' );
    }


    /* TEAM POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_team_edit_columns') ) {
        function sf_team_edit_columns( $columns ) {
            $columns = array(
                "cb"            => "<input type=\"checkbox\" />",
                "thumbnail"     => "",
                "title"         => __( "Team Member", 'dante-framework' ),
                "description"   => __( "Description", 'dante-framework' ),
                "team-category" => __( "Categories", 'dante-framework' )
            );

            return $columns;
        }
        add_filter( "manage_edit-team_columns", "sf_team_edit_columns" );
    }

?>
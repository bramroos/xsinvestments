<?php
/**
 * Asset functions
 */

	/* PERFORMANCE FRIENDLY GET META FUNCTION
    ================================================== */
    if ( !function_exists( 'dante_framework_get_post_meta' ) ) {
	    function dante_framework_get_post_meta( $id, $key = "", $single = false ) {

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
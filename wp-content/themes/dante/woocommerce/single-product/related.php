<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop, $sf_carouselID;

//$woocommerce_loop['columns'] 	= $columns;
$woocommerce_loop['columns'] = 4;

if ($sf_carouselID == "") {
$sf_carouselID = 1;
} else {
$sf_carouselID++;
}

if ( $related_products ) : ?>
	
	<div class="product-carousel spb_content_element related">

		<h4 class="lined-heading"><span><?php _e( 'Related Products', 'dante' ); ?></span></h4>
		
		<div class="carousel-wrap">
		
			<ul class="related products carousel-items" id="carousel-<?php echo esc_attr($sf_carouselID); ?>" data-columns="<?php echo esc_attr($woocommerce_loop['columns']); ?>>">
		
				<?php foreach ( $related_products as $related_product ) : ?>
	
					<?php
					 	$post_object = get_post( $related_product->get_id() );
	
					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
	
						wc_get_template_part( 'content', 'product' ); ?>
	
				<?php endforeach; ?>
		
			</ul>
	
			<a href="#" class="carousel-prev"><i class="fas fa-chevron-left"></i></a><a href="#" class="carousel-next"><i class="fas fa-chevron-right"></i></a>
			
		</div>

	</div>

<?php endif;

global $dante_include_carousel, $dante_include_isotope;
$dante_include_carousel = true;
$dante_include_isotope = true;

wp_reset_postdata();

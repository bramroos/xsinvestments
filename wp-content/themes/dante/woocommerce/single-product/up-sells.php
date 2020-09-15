<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
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
 * @version     3.0.0
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

if ( version_compare( WC_VERSION, '2.7', '>=' ) ) {
	
	if ( $upsells ) : ?>
		
		<div class="product-carousel spb_content_element">
		
			<h4 class="lined-heading"><span><?php _e( 'You may also like&hellip;', 'dante' ) ?></span></h4>
			
			<div class="carousel-wrap">
			
				<ul class="upsells products carousel-items" id="carousel-<?php echo esc_attr($sf_carouselID); ?>" data-columns="<?php echo esc_attr($woocommerce_loop['columns']); ?>>">
			
					<?php foreach ( $upsells as $upsell ) : ?>
		
						<?php
						 	$post_object = get_post( $upsell->get_id() );
		
							setup_postdata( $GLOBALS['post'] =& $post_object );
		
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

} else {

	global $product, $woocommerce;
	
	$upsells = $product->get_upsells();
	
	if ( sizeof( $upsells ) == 0 ) return;
	
	$meta_query = $woocommerce->query->get_meta_query();
	
	$args = array(
		'post_type'           => 'product',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
		'posts_per_page'      => $posts_per_page,
		'orderby'             => $orderby,
		'post__in'            => $upsells,
		'post__not_in'        => array( $product->id ),
		'meta_query'          => $meta_query
	);
	
	$products = new WP_Query( $args );
	
	if ( $products->have_posts() ) : ?>
		
		<div class="product-carousel spb_content_element">
		
			<h4 class="lined-heading"><span><?php _e( 'You may also like&hellip;', 'dante' ) ?></span></h4>
			
			<div class="carousel-wrap">
			
				<ul class="upsells products carousel-items" id="carousel-<?php echo esc_attr($sf_carouselID); ?>" data-columns="<?php echo esc_attr($woocommerce_loop['columns']); ?>>">
			
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
		
						<?php wc_get_template_part( 'content', 'product' ); ?>
		
					<?php endwhile; // end of the loop. ?>
			
				</ul>
		
				<a href="#" class="carousel-prev"><i class="fas fa-chevron-left"></i></a><a href="#" class="carousel-next"><i class="fas fa-chevron-right"></i></a>
				
			</div>
		
		</div>
		
	<?php endif;
	
	global $dante_include_carousel, $dante_include_isotope;
	$dante_include_carousel = true;
	$dante_include_isotope = true;
	
	wp_reset_postdata();

}
<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $woocommerce, $product, $woocommerce_loop, $dante_catalog_mode, $dante_sidebar_config;

$options = get_option('sf_dante_options');
	
$product_overlay_transition = $options['product_overlay_transition'];
$overlay_transition_type = "";

if (isset($options['overlay_transition_type'])) {
	$overlay_transition_type = $options['overlay_transition_type'];
}

if (is_singular('portfolio')) {
$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}
?>
<?php if ( version_compare( WC_VERSION, '3.6', '>=' ) ) { ?>
<li <?php wc_product_class( '', $product ); ?>>
<?php } else if ( version_compare( WC_VERSION, '3.4', '>=' ) ) { ?>
<li <?php wc_product_class(); ?>>
<?php } else { ?>
<li <?php post_class(); ?>>
<?php } ?>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		
	<?php if ($product_overlay_transition) {
		if ($overlay_transition_type == "slideleft") { ?>
		<figure class="product-transition-alt">	
	<?php } else if ($overlay_transition_type == "fade") { ?>
		<figure class="product-transition-fade">	
	<?php } else { ?>
		<figure class="product-transition">					
	<?php }
	?>
	<?php } else { ?>
	<figure class="no-transition">
	<?php } ?>
	
		<?php
			
			$image_html = "";
			
			$newdays = 7;
			
			if (isset($options['new_badge'])) {
				$newdays = $options['new_badge'];
			}
			
			$postdate 		= get_the_time( 'Y-m-d' );			// Post date
			$postdatestamp 	= strtotime( $postdate );			// Timestamped post date
			$newness 		= $newdays; 	// Newness in days
			
			if (dante_is_out_of_stock()) {
				
				echo '<span class="out-of-stock-badge">' . __( 'Out of Stock', 'dante' ) . '</span>';
		
			} else if ($product->is_on_sale()) {
				
				echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'dante' ).'</span>', $post, $product);				
			
			} else if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) {
				
				// If the product was published within the newness time frame display the new badge
				echo '<span class="wc-new-badge">' . __( 'New', 'dante' ) . '</span>';
			
			} else if ( $product->get_price() != "" && $product->get_price() == 0 ) {
				
				echo '<span class="free-badge">' . __( 'Free', 'dante' ) . '</span>';
				
			}
			
			if ( has_post_thumbnail() ) {
				$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );					
			}
		?>
				
		<a href="<?php the_permalink(); ?>">
			
			<?php
				
				if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
				
					$attachment_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids;					
					
					$img_count = 0;
					
					if ($attachment_ids) {
						
						echo '<div class="product-image">'.$image_html.'</div>';	
						
						foreach ( $attachment_ids as $attachment_id ) {
							
							if ( dante_get_post_meta( $attachment_id, '_woocommerce_exclude_image', true ) )
								continue;
							
							echo '<div class="product-image second-image">'.wp_get_attachment_image( $attachment_id, 'shop_catalog' ).'</div>';	
							
							$img_count++;
							
							if ($img_count == 1) break;
				
						}
									
					} else {
					
						echo '<div class="product-image">'.$image_html.'</div>';					
						echo '<div class="product-image second-image">'.$image_html.'</div>';
						
					}
				
				} else {
					
					$attachments = get_posts( array(
						'post_type' 	=> 'attachment',
						'numberposts' 	=> -1,
						'post_status' 	=> null,
						'post_parent' 	=> $post->ID,
						'post__not_in'	=> array( get_post_thumbnail_id() ),
						'post_mime_type'=> 'image',
						'orderby'		=> 'menu_order',
						'order'			=> 'ASC'
					) );
					
					$img_count = 0;
				
					if ($attachments) {
				
						$loop = 0;
						$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
				
						foreach ( $attachments as $key => $attachment ) {
				
							if ( dante_get_post_meta( $attachment->ID, '_woocommerce_exclude_image', true ) == 1 )
								continue;
				
							echo '<div class="product-image second-image">'.wp_get_attachment_image( $attachment->ID, 'shop_catalog' ).'</div>';	
							
							$img_count++;
							
							if ($img_count == 1) break;
				
						}
				
					} else {
					
						echo '<div class="product-image">'.$image_html.'</div>';					
						echo '<div class="product-image second-image">'.$image_html.'</div>';
						
					}
					
				}
			?>			
		</a> 
		<?php if (!$dante_catalog_mode) { ?>
		<figcaption>
			<div class="shop-actions clearfix">
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
			</div>
		</figcaption>
		<?php } ?>
	</figure>
	
	<div class="product-details">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php
			if ( function_exists('wc_get_product_category_list') ) {
				echo wc_get_product_category_list( ', ', '<span class="posted_in">', '</span>' );
			}
		?>
	</div>
	
	<?php
		/**
		 * woocommerce_after_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>
	
</li>
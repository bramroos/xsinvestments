<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
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

global $post, $product;

$options = get_option('sf_dante_options');
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>
	
	<p>
	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'dante' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'dante' ); ?></span> - </span>

	<?php endif; ?>
	<span class="need-help"><?php _e("Need Help?", 'dante'); ?> <a href="#email-form" class="inline" data-toggle="modal"><?php _e("Contact Us", 'dante'); ?></a></span>
	<span class="leave-feedback"><a href="#feedback-form" class="inline" data-toggle="modal"><?php _e("Leave Feedback", 'dante'); ?></a></span>
	</p>
	<p>
	<?php
		if ( function_exists('wc_get_product_category_list') ) {
			echo wc_get_product_category_list( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'dante' ) . ' ', '</span>' );
		}
	?>
	</p>
	<p>
	<?php
		if ( function_exists('wc_get_product_tag_list') ) {
			echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'dante' ) . ' ', '</span>' );
		}
	?>
	</p>
	<?php do_action( 'woocommerce_product_meta_end' ); ?>
	
	<div id="email-form" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="email-form-modal" aria-hidden="true">
		<div class="modal-dialog">
		    <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="ss-delete"></i></button>
					<h3 id="email-form-modal"><?php _e("Contact Us", 'dante'); ?></h3>
				</div>
				<div class="modal-body">
					<?php echo do_shortcode($options['email_modal']); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div id="feedback-form" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="feedback-form-modal" aria-hidden="true">
		<div class="modal-dialog">
		    <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="ss-delete"></i></button>
					<h3 id="feedback-form-modal"><?php _e("Leave Feedback", 'dante'); ?></h3>
				</div>
				<div class="modal-body">
					<?php echo do_shortcode($options['feedback_modal']); ?>
				</div>
			</div>
		</div>
	</div>

</div>
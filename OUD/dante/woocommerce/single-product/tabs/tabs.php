<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
function dante_add_desc_tab($tabs = array()) {
	global $post;
	$product_description = dante_get_post_meta($post->ID, 'sf_product_description', true);
	if ($product_description != "") {
		$tabs['description'] = array(
			'title'    => __( 'Description', 'dante' ),
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab'
		);
	}
	return $tabs;
}
add_filter('woocommerce_product_tabs', 'dante_add_desc_tab', 0);
 
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) : ?>

	<div class="woocommerce-tabs wc-tabs-wrapper">
		<ul class="tabs wc-tabs">
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
				
				<?php if (isset($product_tab['title'])) { ?>
				
				<li class="<?php echo esc_attr( $key ); ?>_tab">
					<a href="#tab-<?php echo esc_attr( $key ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
					</a>
				</li>
				
				<?php } ?>

			<?php endforeach; ?>
		</ul>
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
		
			<?php if (isset($product_tab['callback'])) { ?>

			<div class="panel entry-content" id="tab-<?php echo esc_attr($key); ?>">
				<?php call_user_func( $product_tab['callback'], $key, $product_tab ) ?>
			</div>
			
			<?php } ?>

		<?php endforeach; ?>
	</div>

<?php endif; ?>
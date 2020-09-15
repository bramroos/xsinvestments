<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
	
$options = get_option('sf_dante_options');
	
$sidebar_config = $options['woo_sidebar_config'];
$left_sidebar = strtolower($options['woo_left_sidebar']);
$right_sidebar = strtolower($options['woo_right_sidebar']);

if ($sidebar_config == "") {
	$sidebar_config = 'right-sidebar';
}
if ($left_sidebar == "") {
	$left_sidebar = 'woocommerce-sidebar';
}
if ($right_sidebar == "") {
	$right_sidebar = 'woocommerce-sidebar';
}

if (isset($_GET['sidebar'])) {
	$sidebar_config = $_GET['sidebar'];
}

dante_set_sidebar_global($sidebar_config);

global $dante_sidebar_config, $woocommerce_loop;

$columns = 4;

$page_wrap_class = $page_class = $content_class = '';
$page_wrap_class = "woocommerce-shop-page ";
if ($sidebar_config == "left-sidebar") {
$page_wrap_class .= 'has-left-sidebar has-one-sidebar row';
$page_class = "col-sm-9 col-sm-push-3 clearfix";
$content_class = "clearfix";
} else if ($sidebar_config == "right-sidebar") {
$page_wrap_class .= 'has-right-sidebar has-one-sidebar row';
$page_class = "col-sm-9 clearfix";
$content_class = "clearfix";
} else if ($sidebar_config == "both-sidebars") {
$page_wrap_class .= 'has-both-sidebars row';
$page_class = "col-sm-9 clearfix";
$content_class = "col-sm-8 clearfix";
} else {
$page_wrap_class .= 'has-no-sidebar';
$page_class = "row clearfix";
$content_class = "col-sm-12 clearfix";
}

global $dante_include_isotope, $dante_has_products;
$dante_include_isotope = true;
$dante_has_products = true;
	
get_header('shop');	?>

<?php
	/**
	 * woocommerce_before_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	do_action('woocommerce_before_main_content');
?>

<div class="inner-page-wrap <?php echo esc_attr($page_wrap_class); ?> clearfix">
	
	<!-- OPEN section -->
	<section class="<?php echo esc_attr($page_class); ?>">
	
		<div class="page-content <?php echo esc_attr($content_class); ?>">
		
		<?php
			/**
			 * woocommerce_before_shop_loop hook
			 *
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );
		?>
		
		<?php do_action( 'woocommerce_archive_description' ); ?>
		
		<?php
			if ($dante_sidebar_config == "no-sidebars") {
				$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
			} else if ($dante_sidebar_config == "both-sidebars") {
				$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );
				$columns = 2;
			} else {
				$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
				$columns = 3;
			}	
		?>
			
		<?php if ( function_exists('woocommerce_product_loop') ? woocommerce_product_loop() : have_posts() ) : ?>
								
			<?php woocommerce_product_loop_start(); ?>

				<?php if ( !version_compare( WC_VERSION, '3.3', '>=' ) ) {
					woocommerce_product_subcategories();
				}?>

				<?php if ( function_exists('wc_get_loop_prop') ) {
					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 *
							 * @hooked WC_Structured_Data::generate_product_data() - 10
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}
				} else {
					while ( have_posts() ) {
						the_post();

						wc_get_template_part( 'content', 'product' );
					}
				} ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>
		
		</div>

		<?php if ($sidebar_config == "both-sidebars") { ?>
		<aside class="sidebar left-sidebar col-sm-4">
			<?php dynamic_sidebar($left_sidebar); ?>
		</aside>
		<?php } ?>
	
	<!-- CLOSE section -->
	</section>

	<?php if ($sidebar_config == "left-sidebar") { ?>
			
	<aside class="sidebar left-sidebar col-sm-3 col-sm-pull-9">
		<?php dynamic_sidebar($left_sidebar); ?>
	</aside>

	<?php } else if ($sidebar_config == "right-sidebar") { ?>
		
	<aside class="sidebar right-sidebar col-sm-3">
		<?php dynamic_sidebar($right_sidebar); ?>
	</aside>
	
	<?php } else if ($sidebar_config == "both-sidebars") { ?>

	<aside class="sidebar right-sidebar col-sm-3">
		<?php dynamic_sidebar($right_sidebar); ?>
	</aside>
	
	<?php } ?>
		
</div>

<?php
	/**
	 * woocommerce_after_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
?>

<?php get_footer('shop'); ?>
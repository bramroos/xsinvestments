<?php get_header(); ?>

<?php	
	
	$options = get_option('sf_dante_options');
	$default_sidebar_config = $options['default_sidebar_config'];
	$default_left_sidebar = strtolower($options['default_left_sidebar']);
	$default_right_sidebar = strtolower($options['default_right_sidebar']);
	$sidebar_width = $options['sidebar_width'];
	
	$pb_active = dante_get_post_meta($post->ID, '_spb_js_status', true);
	
	$full_width_display = dante_get_post_meta($post->ID, 'sf_full_width_display', true);
	$show_author_info = dante_get_post_meta($post->ID, 'sf_author_info', true);
	$show_social = dante_get_post_meta($post->ID, 'sf_social_sharing', true);
	$show_related =  dante_get_post_meta($post->ID, 'sf_related_articles', true);
	$remove_breadcrumbs = dante_get_post_meta($post->ID, 'sf_no_breadcrumbs', true);
	
	$default_include_author_info = true;
	if (isset($options['default_include_author_info'])) {
	$default_include_author_info = $options['default_include_author_info'];
	}
	
	if ($show_author_info == "") {
		$show_author_info = $default_include_author_info;
	}
	if ($show_social == "") {
		$show_social = true;
	}
	
	$single_author = $options['single_author'];
	$remove_dates = false;
	if (isset($options['remove_dates']) && $options['remove_dates'] == 1) {
	$remove_dates = true;
	}
	
	$sidebar_config = dante_get_post_meta($post->ID, 'sf_sidebar_config', true);
	$left_sidebar = strtolower(dante_get_post_meta($post->ID, 'sf_left_sidebar', true));
	$right_sidebar = strtolower(dante_get_post_meta($post->ID, 'sf_right_sidebar', true));
	
	if ($sidebar_config == "") {
		$sidebar_config = $default_sidebar_config;
	}
	if ($left_sidebar == "") {
		$left_sidebar = $default_left_sidebar;
	}
	if ($right_sidebar == "") {
		$right_sidebar = $default_right_sidebar;
	}
	
	dante_set_sidebar_global($sidebar_config);
	
	$page_wrap_class = $post_class_extra = $sidebar_class = '';
	if ($sidebar_config == "left-sidebar") {
		$page_wrap_class = 'has-left-sidebar has-one-sidebar row';
		if ($sidebar_width == "reduced") {
			$post_class_extra = 'col-sm-9';
			$sidebar_class = 'col-sm-3';
		} else {
			$post_class_extra = 'col-sm-8';
			$sidebar_class = 'col-sm-4';
		}
	} else if ($sidebar_config == "right-sidebar") {
		$page_wrap_class = 'has-right-sidebar has-one-sidebar row';
		if ($sidebar_width == "reduced") {
			$post_class_extra = 'col-sm-9';
			$sidebar_class = 'col-sm-3';
		} else {
			$post_class_extra = 'col-sm-8';
			$sidebar_class = 'col-sm-4';
		}
	} else if ($sidebar_config == "both-sidebars") {
		$page_wrap_class = 'has-both-sidebars row';
		$post_class_extra = 'col-sm-9';
		$sidebar_class = 'col-sm-3';
	} else {
		$page_wrap_class = 'has-no-sidebar';
	}
	
	$same_category_navigation = false;
	if ( isset($options['same_category_navigation']) ) {
		$same_category_navigation = $options['same_category_navigation'];
	}
?>

<?php if (have_posts()) : the_post(); ?>
	
	<?php		
		$post_author = get_the_author_link();
		$post_date = get_the_date();
		$post_date_str = get_the_date('Y-m-d');
		$post_categories = get_the_category_list(', ');
		
		$media_type = $media_image = $media_video = $media_gallery = '';
				 
		$use_thumb_content = dante_get_post_meta($post->ID, 'sf_thumbnail_content_main_detail', true);
		$post_format = get_post_format($post->ID);
		if ( $post_format == "" ) {
			$post_format = 'standard';
		}
		if ($use_thumb_content) {
		$media_type = dante_get_post_meta($post->ID, 'sf_thumbnail_type', true);
		} else {
		$media_type = dante_get_post_meta($post->ID, 'sf_detail_type', true);
		}
		$media_slider = dante_get_post_meta($post->ID, 'sf_detail_rev_slider_alias', true);
		$media_layerslider = dante_get_post_meta($post->ID, 'sf_detail_layer_slider_alias', true);
		
		if ((($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar") || ($sidebar_config == "both-sidebars")) && !$full_width_display) {
		$media_width = 770;
		$media_height = NULL;
		$video_height = 433;
		} else {
		$media_width = 1170;
		$media_height = NULL;
		$video_height = 658;
		}
		
		$logo = $logo_width = $logo_height = "";
		if (isset($options['logo_upload'])) {
		$logo = __( $options['logo_upload'] , 'dante' );
		}
		if (isset($options['logo_width'])) {
		$logo_width = $options['logo_width'];
		}
		if (isset($options['logo_height'])) {
		$logo_height = $options['logo_height'];
		}
	?>
	
	<div class="inner-page-wrap <?php echo esc_attr($page_wrap_class); ?> clearfix">
		
		<?php if ($full_width_display && $media_type != "none") {

			echo '<figure class="media-wrap full-width-detail col-sm-12" itemscope>';
			
			if ($post_format == "standard") {
							
				if ($media_type == "video") {
							
					echo dante_video_post($post->ID, $media_width, $video_height, $use_thumb_content)."\n";
							
				} else if ($media_type == "slider") {
							
					echo dante_gallery_post($post->ID, $use_thumb_content)."\n";
						
				} else if ($media_type == "layer-slider") {
							
					echo '<div class="layerslider">'."\n";
					
					if ($media_slider != "") {
						echo do_shortcode('[rev_slider '.$media_slider.']')."\n";
					} else {
						echo do_shortcode('[layerslider id="'.$media_layerslider.'"]')."\n";
					}
							
					echo '</div>'."\n";
							
				} else if ($media_type == "custom") {							
					echo dante_get_post_meta($post->ID, 'sf_custom_media', true);					
				} else if ($media_type == "image") {			
					echo dante_image_post($post->ID, $media_width, $media_height, $use_thumb_content)."\n";
				}
				
			} else {
				echo dante_get_post_media($post->ID, $media_width, $media_height, $video_height, $use_thumb_content);			
			}
								
			echo '</figure>';

		} ?>
		
		<!-- OPEN article -->
		<?php if ($sidebar_config == "left-sidebar") { ?>
		<article <?php post_class('clearfix ' . $post_class_extra); ?> id="<?php the_ID(); ?>" itemscope itemtype="http://schema.org/BlogPosting">
		<?php } elseif ($sidebar_config == "right-sidebar") { ?>
		<article <?php post_class('clearfix ' . $post_class_extra); ?> id="<?php the_ID(); ?>" itemscope itemtype="http://schema.org/BlogPosting">
		<?php } elseif ($sidebar_config == "both-sidebars")  { ?>
		<article <?php post_class('clearfix col-sm-9'); ?> id="<?php the_ID(); ?>" itemscope itemtype="http://schema.org/BlogPosting">
		<?php } else { ?>
		<article <?php post_class('clearfix row'); ?> id="<?php the_ID(); ?>" itemscope itemtype="http://schema.org/BlogPosting">
		<?php } ?>
		
		<div class="article-meta">
			<div itemprop="headline"><?php the_title(); ?></div>
		</div>
		
		<?php if ($sidebar_config == "both-sidebars") { ?>
		<div class="row">
			<div class="page-content col-sm-8 clearfix">
		<?php } else if ($sidebar_config == "no-sidebars") { ?>
			<div class="page-content col-sm-12 clearfix">
		<?php } else { ?>
			<div class="page-content clearfix">
		<?php } ?>
				
				<?php if ($sidebar_config == "no-sidebars" && $pb_active == "true") { ?>
				<div class="container">
				<?php } ?>
				
				<meta itemprop="datePublished" content="<?php echo get_the_date( 'Y-m-d' ); ?>"/>
				<meta itemprop="dateModified" content="<?php echo get_the_modified_date( 'Y-m-d' ); ?>"/>
				<div itemprop="publisher" itemscope="" itemtype="https://schema.org/Organization">
					<div itemprop="logo" itemscope="" itemtype="https://schema.org/ImageObject" style="display:none;">
						<img src="<?php echo esc_url($logo); ?>" />
						<meta content="<?php echo esc_url($logo); ?>" itemprop="url" />
					  	<meta content="<?php echo esc_attr($logo_width); ?>" itemprop="width" />
					  	<meta content="<?php echo esc_attr($logo_height); ?>" itemprop="height" />
				   	</div>
				  	<meta itemprop="name" content="<?php bloginfo('name'); ?>" />
				</div>
			
				<div class="entry-title"><?php the_title(); ?></div>
				
				<ul class="post-pagination-wrap curved-bar-styling clearfix">
					<li class="prev"><?php next_post_link('%link', __('<i class="ss-navigateleft"></i> <span class="nav-text">%title</span>', 'dante'), $same_category_navigation); ?></li>
					<li class="next"><?php previous_post_link('%link', __('<span class="nav-text">%title</span><i class="ss-navigateright"></i>', 'dante'), $same_category_navigation); ?></li>
				</ul>
				
				<div class="post-info clearfix">
					<?php if ($single_author && !$remove_dates) { ?>
						<span><?php echo sprintf(__('Posted on <span class="date updated">%1$s</span> in %2$s', 'dante'), $post_date, $post_categories); ?></span>
					<?php } else if ($single_author && $remove_dates) { ?>
						<span><?php echo sprintf(__('Posted in %1$s', 'dante'), $post_categories); ?></span>
					<?php } else if ($remove_dates) { ?>
						<span class="vcard author"><?php echo sprintf(__('Posted by <span itemprop="author" class="fn">%1$s</span> in %2$s', 'dante'), $post_author, $post_categories); ?></span>
					<?php } else { ?>
						<span class="vcard author"><?php echo sprintf(__('Posted by <span itemprop="author" class="fn">%1$s</span> on <span class="date updated">%2$s</span> in %3$s', 'dante'), $post_author, $post_date, $post_categories); ?></span>
					<?php } ?>
					<?php if ( comments_open() ) { ?>
					<div class="comments-likes">
						<div class="comments-wrapper"><a href="#comments"><i class="ss-chat"></i><span><?php comments_number(__('0 Comments', 'dante'), __('1 Comment', 'dante'), __('% Comments', 'dante')); ?></span></a></div>
					</div>
					<?php } ?>
				</div>
				
				<?php if (!$full_width_display && $media_type != "none") {
					echo '<figure class="media-wrap" itemscope>';
		
					if ($post_format == "standard") {
									
						if ($media_type == "video") {
									
							echo dante_video_post($post->ID, $media_width, $video_height, $use_thumb_content)."\n";
									
						} else if ($media_type == "slider") {
									
							echo dante_gallery_post($post->ID, $use_thumb_content)."\n";
								
						} else if ($media_type == "layer-slider") {
									
							echo '<div class="layerslider">'."\n";
							
							if ($media_slider != "") {
								echo do_shortcode('[rev_slider '.$media_slider.']')."\n";
							} else {
								echo do_shortcode('[layerslider id="'.$media_layerslider.'"]')."\n";
							}
									
							echo '</div>'."\n";
									
						} else if ($media_type == "custom") {
															
							echo dante_get_post_meta($post->ID, 'sf_custom_media', true);				
									
						} else if ($media_type == "image") {
										
							echo dante_image_post($post->ID, $media_width, $media_height, $use_thumb_content)."\n";
									
						}
						
					} else {
						
						echo dante_get_post_media($post->ID, $media_width, $media_height, $video_height, $use_thumb_content);
												
					}
										
					echo '</figure>';
				} ?>
				
				<?php if ($sidebar_config == "no-sidebars" && $pb_active == "true") { ?>
				</div>
				<?php } ?>
															
				<section class="article-body-wrap">
					<div class="body-text clearfix" itemprop="articleBody">
						<?php the_content(); ?>
					</div>
					
					<?php if ($sidebar_config == "no-sidebars" && $pb_active == "true") { ?>
					<div class="container">
					<?php } ?>
	
					<div class="link-pages"><?php wp_link_pages(); ?></div>
											
					<div class="tags-link-wrap clearfix">
						<?php if (has_tag()) { ?>
						<div class="tags-wrap"><?php _e("Tags:", 'dante'); ?><span class="tags"><?php the_tags(''); ?></span></div>
						<?php } ?>
					</div>
					
					<?php if ($show_social) { ?>
					<div class="share-links curved-bar-styling clearfix">
						<div class="share-text"><?php _e("Share this article:", 'dante'); ?></div>
						<ul class="social-icons">
							<?php if (function_exists( 'lip_love_it_link' )) { ?>
							<li class="sf-love">
								<div class="comments-likes">
									<?php echo lip_love_it_link(get_the_ID(), '<i class="ss-heart"></i>', '<i class="ss-heart"></i>', false); ?>
								</div>
							</li>
							<?php } ?>
						    <li class="facebook"><a href="https://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>" class="post_share_facebook" onclick="javascript:window.open(this.href,
						      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=220,width=600');return false;"><i class="fab fa-facebook"></i><i class="fab fa-facebook"></i></a></li>
						    <li class="twitter"><a href="https://twitter.com/share?url=<?php the_permalink(); ?>&text=<?php echo urlencode(get_the_title()); ?>" onclick="javascript:window.open(this.href,
						      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=260,width=600');return false;" class="product_share_twitter"><i class="fab fa-twitter"></i><i class="fab fa-twitter"></i></a></li>   
						    <li class="googleplus"><a href="https://plus.google.com/share?url=<?php the_permalink(); ?>" onclick="javascript:window.open(this.href,
						      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fab fa-google-plus"></i><i class="fab fa-google-plus"></i></a></li>
						    <li class="pinterest"><a href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php if(function_exists('the_post_thumbnail')) echo wp_get_attachment_url(get_post_thumbnail_id()); ?>&description=<?php echo get_the_title(); ?>" onclick="javascript:window.open(this.href,
						      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=320,width=600');return false;"><i class="fab fa-pinterest"></i><i class="fab fa-pinterest"></i></a></li>
							<li class="linkedin"><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=<?php echo get_the_title(); ?>&summary=<?php echo strip_tags(dante_excerpt(20)); ?>" onclick="javascript:window.open(this.href,
							  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=320,width=600');return false;"><i class="fab fa-linkedin"></i><i class="fab fa-linkedin"></i></a></li>
							<li class="mail"><a href="mailto:?subject=<?php the_title(); ?>&body=<?php echo strip_tags(dante_excerpt(20)); ?> <?php htmlentities(the_permalink()); ?>" class="product_share_email"><i class="ss-mail"></i><i class="ss-mail"></i></a></li>
						</ul>						
					</div>					
					<?php } ?>


					<?php if ($show_author_info) { ?>
					
					<div class="author-info-wrap clearfix">
						<div class="author-avatar"><?php if(function_exists('get_avatar')) { echo get_avatar(get_the_author_meta('ID'), '140'); } ?></div>
						<div class="author-bio">
							<div class="author-name" itemprop="author" itemscope itemtype="http://schema.org/Person"><h3><?php _e("About", 'dante'); ?> <span itemprop="name"><?php the_author_meta('display_name'); ?></span></h3></div>
							<div class="author-bio-text">
								<?php the_author_meta('description'); ?>
							</div>
						</div>
					</div>
					
					<?php } ?>
					
					<?php if ($sidebar_config == "no-sidebars" && $pb_active == "true") { ?>
					</div>
					<?php } ?>
										
				</section>
				
				<?php if ($sidebar_config == "no-sidebars" && $pb_active == "true") { ?>
				<div class="container">
				<?php } ?>
				
				<?php if ($show_related) { ?>
				
				<div class="related-wrap">
				<?php
				
					$args = array();	
				    $tags = wp_get_post_tags($post->ID);  
				    $categories = get_the_category($post->ID);
				    
				    if ($tags) {  
					    $tag_ids = array();  
					    foreach ($tags as $individual_tag) {
					    	$tag_ids[] = $individual_tag->term_id;  
					    }
					    $args = array(  
						    'tag__in' => $tag_ids,  
						    'post__not_in' => array($post->ID),  
						    'posts_per_page'=> 4, // Number of related posts to display.  
						    'ignore_sticky_posts'=> 1  
					    );
				    } else if ($categories) {
						$category_ids = array();
						foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
	
						$args=array(
							'category__in' => $category_ids,
							'post__not_in' => array($post->ID),
							'showposts'=> 4, // Number of related posts that will be shown.
							'orderby' => 'rand'
						);
					}
					
					$related_posts_query = new wp_query($args);
					if( $related_posts_query->have_posts() ) {	
						echo '<h3 class="spb-heading"><span>';
						_e("Related Articles", 'dante');
						echo '</span></h3>';
						echo '<ul class="related-items row clearfix">';
						while ($related_posts_query->have_posts()) {
							$related_posts_query->the_post();
							$thumb_image = "";
							$thumb_image = dante_get_post_meta($post->ID, 'sf_thumbnail_image', true);
							if (!$thumb_image) {
								$thumb_image = get_post_thumbnail_id();
							}
							$thumb_img_url = wp_get_attachment_url( $thumb_image, 'full' );
							$thumb_width = 300;
							$thumb_height = 225;
							if ( isset($options['related_article_thumb_width']) ) {
								$thumb_width = $options['related_article_thumb_width'];
							}
							if ( isset($options['related_article_thumb_height']) ) {
								$thumb_height = $options['related_article_thumb_height'];
							}
							if ($thumb_width == "") {
								$thumb_width = 300;
							}
							if ($thumb_height == "") {
								$thumb_height = 225;
							}
							$image = dante_aq_resize( $thumb_img_url, $thumb_width, $thumb_height, true, false);
							?>
							<li class="related-item col-sm-3 clearfix">
								<figure class="animated-overlay overlay-alt">
									<?php if ($image) { ?>
									<img src="<?php echo esc_url($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" alt="<?php the_title(); ?>" />
									<?php } else { ?>
									<div class="img-holder"><i class="ss-pen"></i></div>
									<?php } ?>
									<a href="<?php the_permalink(); ?>"></a>
									<figcaption>
										<div class="thumb-info thumb-info-alt">						
											<i class="ss-navigateright"></i>
										</div>
									</figcaption>
								</figure>
								<h5><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h5>
							</li>
						<?php }
						echo '</ul>';
					}
												
					wp_reset_query();
				?>
				</div>
				
				<?php } ?>
				
				<?php if ( comments_open() ) { ?>
				<div id="comment-area">
					<?php comments_template('', true); ?>
				</div>
				<?php } ?>
				
				<?php if ($sidebar_config == "no-sidebars" && $pb_active == "true") { ?>
				</div>
				<?php } ?>
			
			</div>
			
			<?php if ($sidebar_config == "both-sidebars") { ?>
			<aside class="sidebar left-sidebar col-sm-4">
				<div class="sidebar-widget-wrap sticky-widget">
				    <?php dynamic_sidebar( $left_sidebar ); ?>
				</div>
			</aside>
			</div>
			<?php } ?>
		
		<!-- CLOSE article -->
		</article>
	
		<?php if ($sidebar_config == "left-sidebar") { ?>
				
			<aside class="sidebar left-sidebar <?php echo esc_attr($sidebar_class); ?>">
				<div class="sidebar-widget-wrap sticky-widget">
				    <?php dynamic_sidebar( $left_sidebar ); ?>
				</div>
			</aside>
	
		<?php } else if ($sidebar_config == "right-sidebar") { ?>
			
			<aside class="sidebar right-sidebar <?php echo esc_attr($sidebar_class); ?>">
				<div class="sidebar-widget-wrap sticky-widget">
				    <?php dynamic_sidebar( $right_sidebar ); ?>
				</div>
			</aside>
			
		<?php } else if ($sidebar_config == "both-sidebars") { ?>
	
			<aside class="sidebar right-sidebar col-sm-3">
				<div class="sidebar-widget-wrap sticky-widget">
				    <?php dynamic_sidebar( $right_sidebar ); ?>
				</div>
			</aside>
		
		<?php } ?>
				
	</div>

<?php endif; ?>

<!--// WordPress Hook //-->
<?php get_footer(); ?>
<?php get_header(); ?>

<?php	
	$member_position = dante_get_post_meta($post->ID, 'sf_team_member_position', true);
	$member_email = dante_get_post_meta($post->ID, 'sf_team_member_email', true);
	$member_phone = dante_get_post_meta($post->ID, 'sf_team_member_phone_number', true);
	$member_twitter = dante_get_post_meta($post->ID, 'sf_team_member_twitter', true);
	$member_facebook = dante_get_post_meta($post->ID, 'sf_team_member_facebook', true);
	$member_linkedin = dante_get_post_meta($post->ID, 'sf_team_member_linkedin', true);
	$member_skype = dante_get_post_meta($post->ID, 'sf_team_member_skype', true);
	$member_google_plus = dante_get_post_meta($post->ID, 'sf_team_member_google_plus', true);
	$member_instagram = dante_get_post_meta($post->ID, 'sf_team_member_instagram', true);
	$member_dribbble = dante_get_post_meta($post->ID, 'sf_team_member_dribbble', true);
	$member_xing = dante_get_post_meta($post->ID, 'sf_team_member_xing', true);
	$member_image_url = wp_get_attachment_url( get_post_thumbnail_id(), 'full' );
	$image_alt = esc_attr( dante_get_post_meta( get_post_thumbnail_id() , '_wp_attachment_image_alt', true) );
	
	$same_category_navigation = false;
	if ( isset($options['same_category_navigation']) ) {
		$same_category_navigation = $options['same_category_navigation'];
	}
?>

<div class="container">

	<div class="inner-page-wrap clearfix">
	
		<?php if (have_posts()) : the_post(); ?>
	
			<!-- OPEN article -->
			<article <?php post_class('clearfix '); ?> id="<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Person">
				
				<div class="entry-title" itemprop="name"><?php the_title(); ?></div>
				
				<figure class="profile-image-wrap">
					<?php $detail_image = dante_aq_resize( $member_image_url, 600, NULL, true, false); ?>
					
					<?php if ($detail_image) { ?>
						
					<img itemprop="image" src="<?php echo esc_url($detail_image[0]); ?>" width="<?php echo esc_attr($detail_image[1]); ?>" height="<?php echo esc_attr($detail_image[2]); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
						
					<?php } ?>
				</figure>			
				
				<section class="article-body-wrap">
					<div class="body-text">
						<h4 class="member-position" itemscope="jobTitle"><?php echo wp_kses_post($member_position); ?></h4>
						<?php the_content(); ?>
						<ul class="member-contact">
							<?php if ($member_email) {?><li><i class="ss-mail"></i><span itemscope="email"><a href="mailto:<?php echo esc_url($member_email); ?>"><?php echo wp_kses_post($member_email); ?></a></span></li><?php } ?>
							<?php if ($member_phone) {?><li><i class="ss-phone"></i><span itemscope="telephone"><?php echo wp_kses_post($member_phone); ?></span></li><?php } ?>
						</ul>
						<ul class="social-icons">
							<?php if ($member_twitter) {?><li class="twitter"><a href="http://www.twitter.com/<?php echo esc_url($member_twitter); ?>" target="_blank"><i class="fab fa-twitter"></i><i class="fab fa-twitter"></i></a></li><?php } ?>
							<?php if ($member_facebook) {?><li class="facebook"><a href="<?php echo esc_url($member_facebook); ?>" target="_blank"><i class="fab fa-facebook"></i><i class="fab fa-facebook"></i></a></li><?php } ?>
							<?php if ($member_linkedin) {?><li class="linkedin"><a href="<?php echo esc_url($member_linkedin); ?>" target="_blank"><i class="fab fa-linkedin"></i><i class="fab fa-linkedin"></i></a></li><?php } ?>
							<?php if ($member_google_plus) {?><li class="googleplus"><a href="<?php echo esc_url($member_google_plus); ?>" target="_blank"><i class="fab fa-google-plus"></i><i class="fab fa-google-plus"></i></a></li><?php } ?>
							<?php if ($member_skype) {?><li class="skype"><a href="skype:<?php echo esc_url($member_skype); ?>" target="_blank"><i class="fab fa-skype"></i><i class="fab fa-skype"></i></a></li><?php } ?>
							<?php if ($member_instagram) {?><li class="instagram"><a href="<?php echo esc_url($member_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i><i class="fab fa-instagram"></i></a></li><?php } ?>
							<?php if ($member_dribbble) {?><li class="dribbble"><a href="http://www.dribbble.com/<?php echo esc_url($member_dribbble); ?>" target="_blank"><i class="fab fa-dribbble"></i><i class="fab fa-dribbble"></i></a></li><?php } ?>
							<?php if ($member_xing) {?><li class="xing"><a href="<?php echo esc_url($member_xing); ?>" target="_blank"><i class="fab fa-xing"></i><i class="fab fa-xing"></i></a></li><?php } ?>
						</ul>
					</div>
				</section>
			
			<!-- CLOSE article -->
			</article>
			
			<ul class="post-pagination-wrap curved-bar-styling clearfix">
				<li class="prev"><?php next_post_link('%link', __('<i class="ss-navigateleft"></i> <span class="nav-text">%title</span>', 'dante'), $same_category_navigation, '', 'team-category'); ?></li>
				<li class="next"><?php previous_post_link('%link', __('<span class="nav-text">%title</span><i class="ss-navigateright"></i>', 'dante'), $same_category_navigation, '', 'team-category'); ?></li>
			</ul>
	
		<?php endif; ?>
		
	</div>

</div>

<!--// WordPress Hook //-->
<?php get_footer(); ?>
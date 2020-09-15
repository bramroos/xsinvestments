<?php

	/*
	*
	*	Swift Page Builder - Products Function Class
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2019 - http://www.swiftideas.net
	*	
	*	dante_mini_product_items()
	*	dante_product_items()
	*
	*/
	
	/* MINI PRODUCTS
	================================================== */
	if (!function_exists('dante_mini_product_items')) { 
		function dante_mini_product_items($asset_type, $category, $item_count, $sidebar_config, $width) {
			
			global $woocommerce, $dante_catalog_mode;
					
			$product_list_output = $image = "";
			$args = array();
			
			// ARRAY ARGUMENTS
			if ($asset_type == "latest-products") {
				$args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'product_cat'         => $category,
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $item_count,
					'meta_query'          => WC()->query->get_meta_query(),
					'tax_query'           => WC()->query->get_tax_query(),
				);
			} else if ($asset_type == "featured-products") {			
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();
				$args = array(
				    'post_type'           => 'product',
				    'post_status'         => 'publish',
				    'product_cat'         => $category,
				    'ignore_sticky_posts' => 1,
				    'posts_per_page'      => $item_count
				);
				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				);
			} else if ($asset_type == "top-rated") {
				$args = array(
					'posts_per_page' => $item_count,
					'product_cat' => $category,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'meta_key'       => '_wc_average_rating',
					'orderby'        => 'meta_value_num',
					'order'          => 'DESC',
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
				);
			} else if ($asset_type == "recently-viewed") {			
	
				// Get recently viewed product cookies data
				$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
				$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );
			
				// If no data, quit
				if ( empty( $viewed_products ) )
					return '<p class="no-products">'.__( "You haven't viewed any products yet.", 'dante').'</p>';
			
				// Create query arguments array
			    $args = array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'product_cat' => $category,
						'ignore_sticky_posts'   => 1,
	    				'posts_per_page' => $item_count, 
	    				'no_found_rows'  => 1, 
	    				'post__in'       => $viewed_products, 
	    				'orderby'        => 'rand'
	    			);
			
				// Add meta_query to query args
				//$args['meta_query'] = array();
			
			    // Check products stock status
			    //$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
	
			} else if ($asset_type == "sale-products") {
				$args = array(
					'posts_per_page' => $item_count,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'product_cat'    => $category,
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
					'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
				);
			} else {
				$args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $item_count,
					'meta_key'            => 'total_sales',
					'orderby'             => 'meta_value_num',
					'meta_query'          => WC()->query->get_meta_query(),
					'tax_query'           => WC()->query->get_tax_query(),
					'product_cat' 		  => $category,
				);			
			}
			
			// OUTPUT PRODUCTS    
		    $products = new WP_Query( $args );
		    
		    if ( $products->have_posts() ) {
		        
		       $product_list_output .= '<ul class="mini-list mini-'.$asset_type.'">';
		       
		       while ( $products->have_posts() ) : $products->the_post();
		    
		            $product_output = $rating_output = "";
		            
		            global $product, $post, $wpdb, $woocommerce_loop; 
		    
		            if ( has_post_thumbnail() ) {
		    			$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
		    			$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
		    			
		    			if ($image_link == "") {
		    				$image_link = "default";
		    			}
		    			
		    			$image = dante_aq_resize( $image_link, 70, 70, true, false);
		    			
		    			if ($image) {
		    				$image_html = '<img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" alt="'.$image_title.'" />';   			
		    			}            			
		           	}
		           	
		           	if ( comments_open() ) {
		           	
		           		$count = $wpdb->get_var("
		           		    SELECT COUNT(meta_value) FROM $wpdb->commentmeta
		           		    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		           		    WHERE meta_key = 'rating'
		           		    AND comment_post_ID = $post->ID
		           		    AND comment_approved = '1'
		           		    AND meta_value > 0
		           		");
		           	
		           		$rating = $wpdb->get_var("
		           	        SELECT SUM(meta_value) FROM $wpdb->commentmeta
		           	        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		           	        WHERE meta_key = 'rating'
		           	        AND comment_post_ID = $post->ID
		           	        AND comment_approved = '1'
		           	    ");
		           	
		           	    if ( $count > 0 ) {
		           	
		           	        $average = number_format($rating / $count, 2);	           			
		           	        $rating_output = '<div class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'dante'), $average).'"><span style="width:'.($average*16).'px"><span class="rating">'.$average.'</span> '.__('out of 5', 'dante').'</span></div>';
		           	
		           	    }
		           	}
		            
		            $product_output .= '<li class="clearfix">';
		            
		           	if ($image) {
			            $product_output .= '<figure>';
			            $product_output .= '<a href="'.get_permalink($post->ID).'">';
			            $product_output .= $image_html;	
			            $product_output .= '</a>';
			            $product_output .= '</figure>';
		            }
		            $product_output .= '<div class="product-details">';
		            $product_output .= '<h5><a href="'.get_permalink($post->ID).'">'.get_the_title().'</a></h5>';
		       		
		       		if ($asset_type == "top-rated") {
		       		
		       			$product_output .= $rating_output;
		       		
		       		} else {
		            
	            		if ( function_exists('wc_get_product_category_list') ) {
	            			$product_output .= wc_get_product_category_list( ', ', '<span class="product-cats">', '</span>' );
	            		} else {
	            			$product_output .= $product->get_categories( ', ', '<span class="product-cats">', '</span>' );
	            		}

	            	}
	            	if (!$dante_catalog_mode) {
		            $product_output .= '<span class="price">'.$product->get_price_html().'</span>';
		            }
		            $product_output .= '</div>';
		            $product_output .= '</li>';
		            
		            $product_list_output .= $product_output;
		
		       endwhile;
		       
		       wp_reset_query();
		       wp_reset_postdata();
		       
		       $product_list_output .= '</ul>';
		       
		       return $product_list_output;
		    }	    
			
		}
	}
	
	
	/* STANDARD PRODUCTS
	================================================== */
	if (!function_exists('dante_product_items')) { 		
		function dante_product_items($asset_type, $category, $products, $carousel, $product_size, $item_count, $width) {
			
			global $woocommerce, $woocommerce_loop;
			
			$args = array();
			
			// ARRAY ARGUMENTS
			if ($asset_type == "latest-products") {
				$args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'product_cat'         => $category,
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $item_count,
					'meta_query'          => WC()->query->get_meta_query(),
					'tax_query'           => WC()->query->get_tax_query(),
				);
			} else if ($asset_type == "featured-products") {			
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();
				$args = array(
				    'post_type'           => 'product',
				    'post_status'         => 'publish',
				    'product_cat'         => $category,
				    'ignore_sticky_posts' => 1,
				    'posts_per_page'      => $item_count
				);
				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				);
			} else if ($asset_type == "top-rated") {
				$args = array(
					'posts_per_page' => $item_count,
					'product_cat' => $category,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'meta_key'       => '_wc_average_rating',
					'orderby'        => 'meta_value_num',
					'order'          => 'DESC',
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
				);
			} else if ($asset_type == "recently-viewed") {			
	
				// Get recently viewed product cookies data
				$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
				$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );
			
				// If no data, quit
				if ( empty( $viewed_products ) )
					return '<p class="no-products">'.__( "You haven't viewed any products yet.", 'dante').'</p>';
			
				// Create query arguments array
			    $args = array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'product_cat' => $category,
						'ignore_sticky_posts'   => 1,
	    				'posts_per_page' => $item_count, 
	    				'no_found_rows'  => 1, 
	    				'post__in'       => $viewed_products, 
	    				'orderby'        => 'rand'
	    			);
			
				// Add meta_query to query args
				//$args['meta_query'] = array();
			
			    // Check products stock status
			    //$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
	
			} else if ($asset_type == "sale-products") {
				
				$args = array(
					'posts_per_page' => $item_count,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'product_cat'    => $category,
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
					'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
				);
				
			} else {				
				$args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $item_count,
					'meta_key'            => 'total_sales',
					'orderby'             => 'meta_value_num',
					'meta_query'          => WC()->query->get_meta_query(),
					'tax_query'           => WC()->query->get_tax_query(),
					'product_cat' 		  => $category,
				);
			}
			
			ob_start();
					
			// OUTPUT PRODUCTS    
		    $products = new WP_Query( $args );
		    
		    global $dante_sidebar_config, $sf_carouselID;
		    
		    if ($sf_carouselID == "") {
		    $sf_carouselID = 1;
		    } else {
		    $sf_carouselID++;
		    }
		    
		    if (is_singular('portfolio')) {
		    	$dante_sidebar_config = "no-sidebars";
		    }
		    
		    $columns = 4;
		    if ($dante_sidebar_config == "no-sidebars") {
		   	    if ($width == "3/4") {
			   	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
			   	    $columns = 3;	   	    
		   	    } else if ($width == "1/2") {
			   	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );
			   	    $columns = 2;	
			   	} else if ($width == "1/4") {
			   	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 1 );
			   	    $columns = 1;   	    
		   	    } else {
		   	    	if ($product_size == "mini") {
		   	    		$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 6 );
		   	    		$columns = 6;
		   	    	} else {
		   	    		$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
		   	    	}
		   	    }
		    } else if ($dante_sidebar_config == "one-sidebar") {
		    	if ($width == "3/4") {
		    	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
		    	    $columns = 3;	   	    
		    	} else if ($width == "1/2") {
		    	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );
		    	    $columns = 2;	
		    	} else if ($width == "1/4") {
		    	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 1 );
		    	    $columns = 1;   	    
		    	} else {
		    		if ($product_size == "mini") {
						$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
					} else {
						$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
						$columns = 3;
					}
		    	}
		    } else {
		    	if ($width == "3/4") {
		    	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );
		    	    $columns = 2;	   	    
		    	} else if ($width == "1/2") {
		    	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 1 );
		    	    $columns = 1;	
		    	} else if ($width == "1/4") {
		    	    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 1 );
		    	    $columns = 1;   	    
		    	} else {
		    		if ($product_size == "mini") {
		    			$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
		    			$columns = 3;
		    		} else {
						$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );
						$columns = 2;
		    		}
		    	}
		    }
		    	    
			if ( $products->have_posts() ) { ?>
			   
				<?php if ($carousel == "yes") { ?>
					
					<div class="product-carousel carousel-wrap">
										
						<ul class="products list-<?php echo esc_attr($asset_type); ?> carousel-items"
						     id="carousel-<?php echo esc_attr($sf_carouselID); ?>" data-columns="<?php echo esc_attr($columns); ?>">
						
							<?php while ( $products->have_posts() ) : $products->the_post(); ?>
						
								<?php wc_get_template_part( 'content', 'product' ); ?>
						
							<?php endwhile; // end of the loop. ?>
						 
						</ul>
						
						<a href="#" class="carousel-prev"><i class="ss-navigateleft"></i></a><a href="#" class="carousel-next"><i class="ss-navigateright"></i></a>
						
					</div>
					
				<?php } else {  ?> 
				
				<ul class="products list-<?php echo esc_attr($asset_type); ?>">
				
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				
						<?php wc_get_template_part( 'content', 'product' ); ?>
				
					<?php endwhile; // end of the loop. ?>
				 
				</ul>
				
				<?php } ?>
			   
			<?php }
		       
	       $product_list_output = ob_get_contents();
	       ob_end_clean();
	       
	       wp_reset_query();
	       wp_reset_postdata();
	       if (function_exists('woocommerce_reset_loop')) {
	       woocommerce_reset_loop();	
	       }
	       
	       return $product_list_output;
		
		}	    	
	}	
?>
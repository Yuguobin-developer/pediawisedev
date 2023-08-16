<?php
/**
 * The template for displaying all single posts
 *
 * @package pediawise
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );

?>

<div class="single-post-page">

    <!-- Post Wrap -->
	<div class="post-wrp">
	
		<?php
		while ( have_posts() ): the_post();

			$header_banner = get_field('header_banner');

			if (!empty($header_banner)) :?>

                <!-- Header Banner Section -->
				<div class="header-banner" style="background-image: url(<?php echo $header_banner['background_image']; ?>)">

	                <div class="container text-center">

	                    <div class="title" style="background-image: url(<?php echo $header_banner['title_background_ribbon']; ?>)">

                            <?php
                                $tax_args = array('public' => true, '_builtin' => false);
                                $taxonomies = get_taxonomies($tax_args);
                                $terms = wp_get_post_terms(get_the_ID(), $taxonomies);

                                $term_name = "";
                                if (count($terms) > 0) {
                                    $term_name = $terms[0]->name;
                                }
                                

                                $args = array(
                                    'public'   => true,
                                    '_builtin' => true
                                );
                             
                                $output = 'objects';
                                $operator = 'or';
                             
                                // get all taxonomies
                                $taxonomies = get_taxonomies( $args, $output, $operator );

                                $tax_label = "";                             
                                foreach ($taxonomies as $taxonomy) {
                                    // if the provided slug matches, save and return the Title/Label
                                    if ($taxonomy->name == $terms[0]->taxonomy) {
                                        $tax_label = $taxonomy->label;
                                    }   
                                }

                                if ($term_name == "" || $tax_label == "") {
                                    $header_title = $header_banner['top_title'];
                                } else {
                                    $header_title = $term_name . " / " . $tax_label;
                                }

                            ?>

                            <h1 style="color: <?php echo $header_banner['title_color']; ?>">
                                <?php echo $header_title; ?>
                            </h1>

		                </div><!-- .title -->

					</div><!-- .container -->

				</div><!-- header-banner -->

				<?php
			endif; ?>


            <div class="container">

                <!-- Post Title -->
                <div class="post-title">

                    <h1>
                       <?php echo get_the_title(); ?> 
                    </h1>

                </div><!--  .post-title -->

                <!-- Feature Image -->
                <div class="feature-image text-center">

                    <?php echo get_the_post_thumbnail( get_the_ID() ); ?>

                </div><!-- .feature-image -->

                <!-- Post Content -->
    			<div class="post-content">

    				<?php echo get_the_content(); ?>
    				
    			</div><!-- .post-content-->

            </div><!-- .container -->


            <!-- Sources Section -->
            <?php
            $sources = get_field('sources');

            if ( !empty( $sources ) ) :?>

                <div class="sources-section">

                    <div class="section-title-wrp">

                        <h3 class="section-title text-center">View Source</h3>

                    </div><!-- .section-title -->

                    <div class="container source-content">

                        <?php echo $sources; ?>

                    </div>

                </div>

            <?php
            endif; ?>
            <!-- End Sources Section -->

            <!-- Related Posts Section -->
            <?php
            $related_posts_section = get_field('related_posts_section');

            if (!empty($related_posts_section)) :?>

                <div class="related-posts-section">

                    <div class="container text-center">

                        <div class="related-posts-wrp">

                            <?php
                            foreach ($related_posts_section as $each) : $each_post = $each['post']; ?>

                                <div class="each">

                                    <div class="each-wrp">

                                        <a href="<?php echo get_permalink($each_post->ID) ?>">

                                            <div class="image-wrp">

                                                <?php echo get_the_post_thumbnail( $each_post->ID ); ?>

                                            </div><!-- .image-wrp -->

                                            <div class="sub-title">

                                                <p><?php echo $each_post->post_title; ?></p>

                                            </div><!-- .sub-title -->

                                        </a>

                                    </div><!-- .each-wrp -->

                                </div><!-- .each -->

                                <?php
                            endforeach;?>

                        </div><!-- .related-posts-wrp -->

                    </div><!-- .container -->

                </div><!-- .related-posts-section -->

                <?php
            endif;?>


            <!-- Categories Section -->
            <?php
            $category_section = get_field('category_section');

            if (!empty($category_section)) :?>

                <div class="category-section">

                    <div class="container text-center">

                        <div class="title">

                            <h2><?php echo $category_section['title']; ?></h2>

                        </div><!-- .title -->

                        <?php if ( $category_section['categories'] ) : ?>

                            <div class="category-wrp">

                                <?php
                                foreach ($category_section['categories'] as $each) :?>

                                    <div class="each">

                                        <a href="<?php echo $each['page_link']; ?>">

                                            <div class="image-wrp">

                                                <img src="<?php echo $each['feature_image']; ?>">

                                            </div><!-- .image-wrp -->

                                            <div class="sub-title">

                                                <h3><?php echo $each['title']; ?></h3>

                                            </div><!-- .sub-title -->

                                        </a>

                                    </div><!-- .each -->

                                    <?php
                                endforeach; ?>

                            </div><!-- .category-wrp -->

                        <?php endif; ?>

                    </div><!-- .container -->

                </div><!-- .category-section -->

                <?php
            endif; 

		endwhile; // End of the loop.
		?>

	</div><!-- .post-wrp -->
	
</div><!-- .single-post-page -->

<?php get_footer(); ?>

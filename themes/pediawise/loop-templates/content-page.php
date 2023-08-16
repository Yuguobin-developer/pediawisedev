<?php
/**
 * Partial template for content in page.php
 *
 * @package pediawise
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<div class="post-thumbnail">

		<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

	</div>

	<div class="entry-content">

		<?php the_content(); ?>

	</div><!-- .entry-content -->

</article><!-- #post-## -->

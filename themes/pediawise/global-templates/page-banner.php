<?php
/**
 * Page Banner
 *
 * @package pediawise
 */

$banner_theme = get_field( 'banner_theme' );
$breadcrumbs_theme = get_field( 'breadcrumbs_theme' );
$breadcrumbs_text_theme = get_field( 'breadcrumbs_text_theme' );
$breadcrumbs_separator_theme = get_field( 'breadcrumbs_separator_theme' );
$banner_title1 = get_field( 'banner_title1' );
$banner_title2 = get_field( 'banner_title2' );
?>

<?php if ( $banner_title1 || $banner_title2 ) : ?>

    <div class="page-banner page-banner-<?php echo $banner_theme; ?>">

        <div class="breadcrumbs-wrapper breadcrumbs-bg-<?php echo $breadcrumbs_theme; ?>">

            <ul class="breadcrumbs breadcrumbs-text-<?php echo $breadcrumbs_text_theme; ?> breadcrumbs-separator-<?php echo $breadcrumbs_separator_theme; ?>">

                <?php if ( $banner_title1 ) : ?>
                    <li><?php echo $banner_title1; ?></li>
                <?php endif; ?>

                <?php if ( $banner_title2 ) : ?>
                    <li><?php echo $banner_title2; ?></li>
                <?php endif; ?>

            </ul>

        </div><!-- .breadcrumbs -->

    </div><!-- .page-banner -->

<?php endif; ?>
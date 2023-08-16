<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package pediawise
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );

/* Hero */
$hero_title = get_field( 'hero_title' );
$hero_content = get_field( 'hero_content' );
$hero_buttons = get_field( 'hero_buttons' );

/* About Us */
$about_title = get_field( 'about_title' );
$about_sub_text = get_field( 'about_sub_text' );
$about_feeds = get_field( 'about_feeds' );
$about_button_link = get_field( 'about_button_link' );

/* Features */
$features = get_field( 'features' );

/* Hot Topics */
$hot_topics_title = get_field( 'hot_topics_title' );
$hot_topics_sub_text = get_field( 'hot_topics_sub_text' );
$hot_topics_navs = get_field( 'hot_topics_navs' );

/* Questions */
$question1 = get_field( 'question1' );
$question2 = get_field( 'question2' );

/* Services */
$services_title = get_field( 'services_title' );
$services_sub_text = get_field( 'services_sub_text' );
$services = get_field( 'services' );

?>

<!-- Hero -->
<div id="hero" class="section" style="background-image: url('<?php echo get_stylesheet_directory_uri() ?>/images/main_bg_2.png')">

    <div class="wrapper">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 col-xl-7 col-lg-8">

                    <div class="hero-content">

                        <?php if ( $hero_title ) : ?>
                            <h1 class="primary-color"><?php echo $hero_title; ?></h1>
                        <?php endif; ?>

                        <?php if ( $hero_content ) : ?>
                            <?php echo $hero_content; ?>
                        <?php endif; ?>

                        <div class="search-form-wrapper">

                            <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
                                <div class="input-group">
                                    <input class="field form-control" id="s" name="s" type="text" placeholder="<?php esc_attr_e( 'Search hundreds of topics&hellip;', 'pediawise' ); ?>" value="<?php the_search_query(); ?>" autocomplete="off">
                                    <span class="input-group-append">
                                        <input class="submit btn btn-primary background-btn" id="searchsubmit" name="submit" type="submit"
                                        value="<?php esc_attr_e( 'Search', 'understrap' ); ?>">
                                    </span>
                                </div>
                            </form>
                        
                        </div><!-- .search-form-wrapper -->

                        <?php if ( $hero_buttons ) : ?>

                            <div class="btns-wrapper">
                                <?php foreach ( $hero_buttons as $btn ) : ?>
                                    <a href="<?php echo esc_url( $btn['link'] ); ?>" class="btn background-btn <?php echo $btn['type']; ?>"><?php echo $btn['title']; ?></a>
                                <?php endforeach; ?>

                            </div><!-- .btns-wrapper -->

                        <?php endif; ?>

                    </div><!-- .hero-content -->

                </div><!-- col -->

            </div><!-- .row -->

        </div><!-- .container-fluid -->

    </div><!-- .wrapper -->

</div><!-- #hero -->


<!-- About Us -->
<div id="about-us" class="section">

    <div class="wrapper">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 col-md-6">
                    
                    <?php if ( $about_title ) : ?>
                        <h2 class="section-title light-color font-family-secondary"><?php echo $about_title; ?></h2>
                    <?php endif; ?>

                    <?php if ( $about_sub_text ) : ?>
                        <p class="white-color"><?php echo $about_sub_text; ?></p>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $about_button_link ); ?>" class="btn btn-type3 btn-bg-color-light background-btn">View All</a>

                </div><!-- col -->

                <div class="col-12 col-md-6">
                    <div class="feed-wrp">

                        <?php 
                        if ( $about_feeds ) :
                            foreach ($about_feeds as $each) :?>
                                <div class="each text-center">
                                    <div class="each-wrp">
                                        <div class="img-wrp">
                                            <img src="<?php echo $each['feature_image'];?>">
                                        </div>
                                        <div class="title">
                                            <p><?php echo $each['title']; ?></p>
                                        </div>
                                        <div class="date">
                                            <p><?php echo $each['date']; ?></p>
                                        </div>
                                        <div class="description">
                                            <p><?php echo $each['description']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endforeach;?>

                        <?php endif; ?>

                    </div>

                </div><!-- col -->

            </div><!-- .row -->

        </div><!-- .container-fluid -->

    </div><!-- .wrapper -->

</div><!-- #about-us -->


<!-- Features -->
<?php if ( $features ) : ?>
    <div id="features" class="section">

        <div class="wrapper">

            <div class="container-fluid">

                <div class="row">

                    <?php foreach ( $features as $feature ) : ?>

                        <div class="col-12 col-md-4">

                            <div class="feature text-center">
                                <img class="featured-img" src="<?php echo esc_url( $feature['image'] ); ?>" />
                                <h2 class="secondary-color font-family-secondary"><?php echo $feature['title']; ?></h2>
                                <p><?php echo $feature['sub_text']; ?></p>
                                <a href="<?php echo esc_url( $feature['link'] ); ?>" class="btn btn-type4 btn-bg-color-secondary background-btn">View All</a>
                            </div>

                        </div><!-- col -->

                    <?php endforeach; ?>

                </div><!-- .row -->

            </div><!-- .container-fluid -->

        </div><!-- .wrapper -->

    </div><!-- #features -->
<?php endif; ?>


<!-- Hot Topics -->
<div id="hot-topics" class="section">

    <div class="wrapper">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 col-md-6 offset-md-6 text-center">
                    
                    <?php if ( $hot_topics_title ) : ?>
                        <h2 class="section-title primary-color font-family-secondary"><?php echo $hot_topics_title; ?></h2>
                    <?php endif; ?>

                    <?php if ( $hot_topics_sub_text ) : ?>
                        <p><?php echo $hot_topics_sub_text; ?> </p>
                    <?php endif; ?>
                    
                    <?php if ( $hot_topics_navs ) : ?>
                        <div class="row">
                            <?php foreach ( $hot_topics_navs as $nav ) : ?>
                                <div class="col-md-3 each">
                                    <a href="<?php echo esc_url( $nav['link'] ); ?>" class="topic-nav-item">
                                        <div class="img-wrp">
                                            <img src="<?php echo esc_url( $nav['image'] ); ?>" />
                                        </div>                                        
                                        <?php echo $nav['title']; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div><!-- col -->

            </div><!-- .row -->

        </div><!-- .container-fluid -->

    </div><!-- .wrapper -->

</div><!-- #hot-topics -->


<!-- Questions -->
<div id="questions" class="section">

    <div class="wrapper">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 col-md-6 text-center left-column">
                    
                    <?php if ( $question1 ) { ?>
                        <?php if ( $question1['image'] ) : ?>
                            <img class="featured-img" src="<?php echo esc_url( $question1['image'] ); ?>" />
                        <?php endif; ?>

                        <?php if ( $question1['title'] ) : ?>
                            <h2 class="white-color font-family-secondary"><?php echo $question1['title']; ?></h2>
                        <?php endif; ?>

                        <?php if ( $question1['sub_text'] ) : ?>
                            <p><?php echo $question1['sub_text']; ?></p>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url( $question1['button_link'] ); ?>" class="btn btn-type3-1 btn-bg-color-white background-btn">Preview</a>
                    <?php } ?>

                </div><!-- col -->

                <div class="col-12 col-md-6 text-center right-column">
                    
                    <?php if ( $question2 ) { ?>
                        <?php if ( $question2['image'] ) : ?>
                            <img class="featured-img" src="<?php echo esc_url( $question2['image'] ); ?>" />
                        <?php endif; ?>

                        <?php if ( $question2['title'] ) : ?>
                            <h2 class="white-color font-family-secondary"><?php echo $question2['title']; ?></h2>
                        <?php endif; ?>

                        <?php if ( $question2['sub_text'] ) : ?>
                            <p><?php echo $question2['sub_text']; ?></p>
                        <?php endif; ?>

                        <div class="question-form-wrapper">
                            <form id="questionForm" action="<?php echo esc_url( $question2['form_action'] ); ?>">
                                <input class="field form-control" type="text" placeholder="Type your question here..." value="" />
                                <a href="#" class="btn background-btn">Submit</a>
                            </form>
                        </div>
                    <?php } ?>

                </div><!-- col -->

            </div><!-- .row -->

        </div><!-- .container-fluid -->

    </div><!-- .wrapper -->

</div><!-- #questions -->


<!-- Services -->
<div id="services" class="section">

    <div class="wrapper">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 text-center">
                    
                    <?php if ( $services_title ) : ?>
                        <h2 class="primary-color font-family-secondary section-title"><?php echo $services_title; ?></h2>
                    <?php endif; ?>

                    <?php if ( $services_sub_text ) : ?>
                        <h3 class="secondary-color"><?php echo $services_sub_text; ?></h3>
                    <?php endif; ?>

                </div>

            </div>

            <?php if ( $services ) : ?>

                <div class="row">

                    <?php foreach ( $services as $service ) : ?>

                        <div class="col-12 col-md-4">

                            <div class="service text-center">
                                <img class="featured-img" src="<?php echo esc_url( $service['image'] ); ?>" />
                                <h3 class="primary-color"><?php echo $service['title']; ?></h3>
                                <p><?php echo $service['content']; ?></p>
                            </div>

                        </div><!-- col -->

                    <?php endforeach; ?>

                </div><!-- .row -->

            <?php endif; ?>

        </div><!-- .container-fluid -->

    </div><!-- .wrapper -->

</div><!-- #features -->

<?php get_footer(); ?>

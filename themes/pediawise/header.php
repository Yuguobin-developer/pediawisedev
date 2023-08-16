<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package pediawise
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="site" id="page">

	<header id="header-wrapper" class="wrapper">

		<div class="header-main">
			
			<div class="header-main-wrapper">

				<div class="header-left">

					<!-- Header Logo -->
					<a href="<?php echo home_url(); ?>" class="navbar-brand custom-logo-link">
						<img class="desktop-view" src="<?php echo get_stylesheet_directory_uri(); ?>/images/common/header_logo.png">
						<img class="tablet-view" src="<?php echo get_stylesheet_directory_uri(); ?>/images/common/footer_logo.png">
					</a>

				</div><!-- .header-left -->

				<div class="header-right d-flex justify-content-end">

					<!-- Header dropdown menu search form wrap -->
					<div class="top-menu-search-form-wrapper">

						<!-- Search form -->
		        <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">

		          <div class="input-group">

		            <input class="field form-control" id="s" name="s" type="text" value="<?php the_search_query(); ?>" autocomplete="off" placeholder="Search PediaWise">

		            <span class="input-group-append">
		              <input class="submit btn btn-primary" id="searchsubmit" name="submit" type="submit" value="<?php esc_attr_e( 'Search', 'understrap' ); ?>">
		            </span>

		          </div>

		        </form><!-- end form -->
		      
		      </div><!-- .search-form-wrapper -->
					
					<!-- Top Navigation -->
					<ul class="top-nav">
						<li>
							<a href="<?php echo home_url() ?>/signup/" class="top-nav-item">
								<span class="top-nav-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/Subscribe.png" /></span>
								<?php echo __( 'Subscribe', 'pediawise' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo home_url() ?>/gift/" class="top-nav-item">
								<span class="top-nav-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/Gift.png" /></span>
								<?php echo __( 'Gift', 'pediawise' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo home_url() ?>/sign-in/" class="top-nav-item">
								<span class="top-nav-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/SignIn.png" /></span>
								<?php echo __( 'Sign In', 'pediawise' ); ?>
							</a>
						</li>
					</ul><!-- .top-nav -->

					<button href="javascript:void(0);" class="menu-toggler" data-toggle="collapse" data-target="#nav-menu" aria-controls="nav-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'pediawise' ); ?>">
						<span class="top-nav-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/Menu-Icons6.png" /></span>
						<?php echo __( 'Menu', 'pediawise' ); ?>
					</button>

				</div><!-- .header-right -->

			</div><!-- .container -->

		</div><!-- .header-main -->

		<nav class="navbar collapse navbar-collapse" id="nav-menu">

			<!-- The WordPress Menu goes here -->
			<?php wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					// 'container_class' => 'collapse navbar-collapse',
					'container_id'    => 'navbarNavDropdown',
					'menu_class'      => 'navbar-nav ml-auto',
					'fallback_cb'     => '',
					'menu_id'         => 'main-menu',
					'depth'           => 2,
					'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
				)
			); ?>

			<!-- Header dropdown menu search form wrap -->
			<div class="menu-search-form-wrapper">

				<!-- Search form -->
        <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">

          <div class="input-group">

            <input class="field form-control" id="s" name="s" type="text" value="<?php the_search_query(); ?>" autocomplete="off" placeholder="Search Pediawise">

            <span class="input-group-append">
              <input class="submit btn btn-primary" id="searchsubmit" name="submit" type="submit" value="<?php esc_attr_e( 'Search', 'understrap' ); ?>">
            </span>

          </div>

        </form><!-- end form -->
      
      </div><!-- .search-form-wrapper -->


      <div class="social-links-wrp">
      	<?php

      	$social_links = get_field("social_links", "options");

      	?>
      	<a href="<?php echo $social_links['facebook_link']; ?>">
      		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/facebook_icon.png">
      	</a>
      	<a href="<?php echo $social_links['twitter_link']; ?>">
      		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/twitter_icon.png">
      	</a>
      	<a href="<?php echo $social_links['instagram_link']; ?>">
      		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/instagram_icon.png">
      	</a>
      </div>


		</nav><!-- .site-navigation -->

	</header><!-- #header-wrapper -->

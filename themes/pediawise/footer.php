<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package pediawise
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<footer class="footer-wrp">
  <img class="footer-top-image" src="<?php echo get_stylesheet_directory_uri(); ?>/images/footer-bg.png">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-3 col-md-5 logo-section">
        <div class="footer-logo">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/images/common/footer_logo.png">
        </div>
        <div class="contact-info">
          <a href="mailto:info@pediawise.com">info@pediawise.com</a>
        </div>
        <div class="social-links">
          <a href="#"><i class="fa fa-twitter"></i></a>
          <a href="#"><i class="fa fa-facebook-square"></i></a>
          <a href="#"><i class="fa fa-instagram"></i></a>
        </div>
      </div>
      <div class="col-lg-4 col-md-7 quick-link-section">
        <p class="footer-sub-title">Quick Links</p>
        <ul class="quick-links">
          <li><a href="<?php echo home_url();?>/signup-page">Subscribe</a></li>
          <li><a href="<?php echo home_url();?>/gift-page">Gift</a></li>
          <li><a href="<?php echo home_url();?>/signup-page">Sign-in</a></li>
          <li><a href="<?php echo home_url();?>/">Home</a></li>
          <li><a href="<?php echo home_url();?>/about">About</a></li>
          <li><a href="<?php echo home_url();?>/">FAQs</a></li>
          <li><a href="<?php echo home_url();?>/">Meet the Doctor</a></li>
          <li><a href="<?php echo home_url();?>/">Newsletters</a></li>
          <li><a href="<?php echo home_url();?>/freebies-page">Freebies</a></li>
          <li><a href="<?php echo home_url();?>/top-10s-page">Top 10s</a></li>
          <li><a href="<?php echo home_url();?>/hot-topics-page">Hot Topics</a></li>
          <li><a href="<?php echo home_url();?>/question-of-the-week-page">Question of the Week</a></li>
          <li><a href="<?php echo home_url();?>/">Contact Us</a></li>
          <li><a href="<?php echo home_url();?>/">Feedback/Suggestions</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-6 feed-section">
        <p class="footer-sub-title">Follow Us</p>
        <div class="follow-us-wrp">
          <div class="each"></div>
          <div class="each"></div>
          <div class="each"></div>
          <div class="each"></div>
          <div class="each"></div>
          <div class="each"></div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 search-section">
        <p class="footer-sub-title">Search Pediawise</p>
        <!-- <div class="search-wrp text-center">
          <div class="inner-wrp"> -->
            <!-- <input type="text" name="question-s" />
            <a href="#"><div class="submit-btn">
            </div></a> -->

            <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
              <div class="input-group">
                <input class="field form-control" id="s" name="s" type="text" value="<?php the_search_query(); ?>" autocomplete="off">
                <span class="input-group-append">
                  <input class="submit btn btn-primary" id="searchsubmit" name="submit" type="submit" value="<?php esc_attr_e( 'Search', 'understrap' ); ?>">
                </span>
              </div>
            </form>

          <!-- </div>
        </div> -->
      </div>
    </div>
  </div>
</footer>

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>


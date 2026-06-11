<?php
/**
 * Template Name: Products Page
 *
 * Full-bleed products page. Renders the products banner from ACF options,
 * then the_content() for whatever blocks are in the page builder (product-
 * category-grid, join-now-form, etc.).
 *
 * No site-wrapper wrapper here — blocks handle their own width/padding.
 * The entry-title is intentionally suppressed; the banner serves that role.
 *
 * Banner fields read from Theme Settings → Products:
 *   products_banner_image         — background image (attachment URL)
 *   products_banner_title         — e.g. "FLAVOR WITH AN EDGE"
 *   products_banner_subtitle      — small label above the title (optional)
 *   products_banner_content       — body copy below the title (optional)
 *   products_banner_overlay_image — foreground product image (e.g. hot dog PNG)
 *
 * @package ScottPete
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$banner_image   = ipc_option( 'products_banner_image' );
$banner_title   = ipc_option( 'products_banner_title' );
$banner_subtitle = ipc_option( 'products_banner_subtitle' );
$banner_content = ipc_option( 'products_banner_content' );
$banner_overlay = ipc_option( 'products_banner_overlay_image' );
?>

<?php if ( $banner_image || $banner_title ) : ?>
<section class="products-banner"
         style="<?php if ( $banner_image ) echo 'background-image:url(' . esc_url( $banner_image ) . ');'; ?>">
    <div class="products-banner-overlay"></div>
    <div class="products-banner-content">
        <?php if ( $banner_subtitle ) : ?>
            <span class="products-banner-subtitle"><?php echo esc_html( $banner_subtitle ); ?></span>
        <?php endif; ?>
        <?php if ( $banner_title ) : ?>
            <h1 class="products-banner-title"><?php echo esc_html( $banner_title ); ?></h1>
        <?php endif; ?>
        <?php if ( $banner_content ) : ?>
            <p class="products-banner-body"><?php echo wp_kses_post( $banner_content ); ?></p>
        <?php endif; ?>
    </div>
    <?php if ( $banner_overlay ) : ?>
        <div class="products-banner-product">
            <img src="<?php echo esc_url( $banner_overlay ); ?>" alt="" role="presentation">
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php while ( have_posts() ) : the_post(); ?>
    <div class="products-page-content">
        <?php the_content(); ?>
    </div>
<?php endwhile; ?>

<?php get_footer();

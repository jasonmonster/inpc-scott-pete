<?php
/**
 * Scott Pete - Where to Buy Page Template (child override of ipc-base)
 *
 * Template Name: Where to Buy
 *
 * Renders the FULL Destini store locator (the "Product First" page widget),
 * not the per-product button. Two things differ from the ipc-base parent:
 *
 *   1. Script: productFirstSnippet.js (full map) instead of
 *      productWidgetSnippet.js (the button/pop-up used on product pages).
 *   2. Values: the store-locator pair (destini_store_locator_id /
 *      destini_store_alpha_code = 4624 / 1210) instead of the shared
 *      product-widget pair, so this page and the product button can point
 *      at different Destini locators.
 *
 * Values currently come from the temp Destini block in functions.php until
 * real ACF options are wired.
 *
 * @package ScottPete
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$locator_id = ipc_option( 'destini_store_locator_id' );
$alpha_code = ipc_option( 'destini_store_alpha_code' );
?>

<section class="wtb-page">
    <div class="wtb-banner">
        <div class="site-wrapper">
            <?php while ( have_posts() ) : the_post(); ?>
                <h1 class="wtb-title"><?php the_title(); ?></h1>
                <?php if ( get_the_content() ) : ?>
                    <div class="wtb-intro"><?php the_content(); ?></div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="wtb-locator">
        <div class="site-wrapper">
            <?php if ( $locator_id ) : ?>
                <div id="destini-locator-<?php echo esc_attr( $locator_id ); ?>"
                     class="destini-locator-class"
                     data-locator-id="<?php echo esc_attr( $locator_id ); ?>"
                     data-alpha-code="<?php echo esc_attr( $alpha_code ); ?>">
                </div>
                <script src="https://lets.shop/productFirstSnippet.js" charset="utf-8"></script>
            <?php else : ?>
                <p class="wtb-no-config">
                    <?php esc_html_e( 'Store locator not configured. Add your Destini store locator ID in Theme Settings.', 'scott-pete' ); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer();

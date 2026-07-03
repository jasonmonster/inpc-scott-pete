<?php
/**
 * Scott Pete — Product Category Archive Override
 *
 * Overrides ipc-base/taxonomy-product_category.php.
 *
 * Changes from parent:
 *   1. Adds the same options-driven hero banner used on page-products.php
 *      (products_banner_* fields) — parent renders no banner at all here.
 *   2. Section-heading order swapped: h2 title first, eyebrow/subtitle
 *      after. Parent renders eyebrow before h2.
 *
 * Everything else (redirect-on-single-product logic, category flip grid,
 * product list) is unchanged from parent.
 *
 * @package ScottPete
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Get current category term ──────────────────────────────
$current_term = get_queried_object();

// ── Single-product redirect ────────────────────────────────
// If this category only has one product, skip the list entirely
// and send the user directly to that product's detail page.
if ( $current_term instanceof WP_Term && $current_term->count === 1 ) {
    $single = get_posts( array(
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'tax_query'      => array( array(
            'taxonomy' => 'product_category',
            'field'    => 'term_id',
            'terms'    => $current_term->term_id,
        ) ),
    ) );

    if ( ! empty( $single ) ) {
        $redirect_url = get_permalink( $single[0] ) . '#product_detail';
        wp_redirect( $redirect_url, 301 );
        exit;
    }
}

get_header();

// ── Hero banner (same ACF fields as page-products.php) ─────
$banner_image    = ipc_option( 'products_banner_image' );
$banner_title    = ipc_option( 'products_banner_title' );
$banner_subtitle = ipc_option( 'products_banner_subtitle' );
$banner_content  = ipc_option( 'products_banner_content' );
$banner_overlay  = ipc_option( 'products_banner_overlay_image' );
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

<?php
// ── All product categories for the flip-card grid ──────────
$all_categories = get_terms( array(
    'taxonomy'   => 'product_category',
    'hide_empty' => true,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
) );

// ── ACF options for section headings ───────────────────────
$grid_title    = ipc_option( 'products_grid_title',    '' );
$grid_subtitle = ipc_option( 'products_grid_subtitle', '' );
$grid_cta_text = ipc_option( 'products_grid_cta_text', __( 'All Products', 'ipc-base' ) );
$grid_cta_link = ipc_option( 'products_grid_cta_link', get_post_type_archive_link( 'product' ) );
?>

<?php /* ── Category Flip-Card Grid ──────────────────────── */ ?>
<section class="product-category-section" id="product_list">
    <div class="site-wrapper">

        <?php if ( $grid_title || $grid_subtitle ) : ?>
            <header class="section-heading">
                <?php if ( $grid_title ) : ?>
                    <h2><?php echo esc_html( $grid_title ); ?></h2>
                <?php endif; ?>
                <?php if ( $grid_subtitle ) : ?>
                    <span class="eyebrow"><?php echo esc_html( $grid_subtitle ); ?></span>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <div class="product-grid-outer">
            <div class="product-grid-track" id="productGridTrack" role="list">
                <?php foreach ( $all_categories as $term ) :
                    $is_active  = ( $term->term_id === $current_term->term_id );
                    $card_url   = ipc_get_category_card_url( $term );
                    $image_url  = ipc_get_category_image_url( $term, 'category-card' );

                    if ( ! $card_url ) continue;
                ?>
                    <div class="product-flip-card <?php echo $is_active ? 'product-flip-card--active' : ''; ?>"
                         role="listitem">
                        <a href="<?php echo esc_url( $card_url ); ?>">
                            <div class="product-flip-inner">
                                <div class="product-flip-front">
                                    <?php if ( $image_url ) : ?>
                                        <img src="<?php echo esc_url( $image_url ); ?>"
                                             alt="<?php echo esc_attr( $term->name ); ?>">
                                    <?php endif; ?>
                                    <span class="product-flip-front-label">
                                        <?php echo esc_html( $term->name ); ?>
                                    </span>
                                </div>
                                <div class="product-flip-back">
                                    <h3><?php echo esc_html( $term->name ); ?></h3>
                                    <?php if ( $term->description ) : ?>
                                        <p><?php echo esc_html( $term->description ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div><!-- .product-grid-track -->

            <div class="product-scroll-arrows">
                <button class="product-scroll-arrow product-scroll-arrow--prev"
                        aria-label="<?php esc_attr_e( 'Scroll left', 'ipc-base' ); ?>"
                        data-track="productGridTrack">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="product-scroll-arrow product-scroll-arrow--next"
                        aria-label="<?php esc_attr_e( 'Scroll right', 'ipc-base' ); ?>"
                        data-track="productGridTrack">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>

        </div><!-- .product-grid-outer -->

        <?php if ( $grid_cta_text && $grid_cta_link ) : ?>
            <div class="product-grid-cta">
                <a href="<?php echo esc_url( $grid_cta_link ); ?>" class="btn btn--outline-white">
                    <?php echo esc_html( $grid_cta_text ); ?>
                </a>
            </div>
        <?php endif; ?>

    </div><!-- .site-wrapper -->
</section>

<?php /* ── Product List ──────────────────────────────────── */ ?>
<?php if ( have_posts() ) : ?>
<section class="product-list-section" id="product_list_grid">
    <div class="site-wrapper">

        <ul class="product-list-grid">
            <?php while ( have_posts() ) : the_post();
                $thumbnail_id = get_post_meta( get_the_ID(), '_product_thumbnail', true );
                $hover_id     = get_post_meta( get_the_ID(), '_product_hover_thumbnail', true );
                $thumbnail_url = $thumbnail_id
                    ? wp_get_attachment_image_url( $thumbnail_id, 'product-thumbnail' )
                    : get_the_post_thumbnail_url( get_the_ID(), 'product-thumbnail' );
                $hover_url = $hover_id
                    ? wp_get_attachment_image_url( $hover_id, 'product-thumbnail' )
                    : '';
                $product_url = get_permalink() . '?cat_id=' . $current_term->term_id . '#product_detail';
            ?>
                <li>
                    <a href="<?php echo esc_url( $product_url ); ?>"
                       class="product-list-item">
                        <div class="product-list-item-images">
                            <?php if ( $thumbnail_url ) : ?>
                                <img class="img-default"
                                     src="<?php echo esc_url( $thumbnail_url ); ?>"
                                     alt="<?php echo esc_attr( get_the_title() ); ?>">
                            <?php endif; ?>
                            <?php if ( $hover_url ) : ?>
                                <img class="img-hover"
                                     src="<?php echo esc_url( $hover_url ); ?>"
                                     alt="<?php echo esc_attr( get_the_title() ); ?>">
                            <?php endif; ?>
                        </div>
                        <h3><?php the_title(); ?></h3>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>

    </div>
</section>
<?php endif; ?>

<?php get_footer();

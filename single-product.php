<?php
/**
 * Scott Pete — Single Product Override
 *
 * Overrides ipc-base/single-product.php.
 *
 * Changes from parent:
 *   1. Adds the same options-driven hero banner used on page-products.php
 *      and the taxonomy-product_category.php override. Parent renders no
 *      banner here at all.
 *   2. Section-heading order swapped: h2 title first, eyebrow/subtitle
 *      after — matches the taxonomy override.
 *   3. Adds a product-list-section (the red circular product list) for
 *      the active category, between the category flip row and the
 *      product detail. Parent doesn't render this here — on the
 *      reference site the list only exists on the category archive and
 *      disappears once you land on a product. Per Jason: render it here
 *      too, scoped to the same category, so it reads as staying in
 *      place instead of vanishing. Its back-to-grid link points at
 *      #product_list on this page (the category row directly above)
 *      rather than crossing back to the archive, since it's now local.
 *      Skipped entirely for single-product categories (nothing to list).
 *
 * Everything else (variation nav/panels, nutrition, certifications,
 * ingredients, Destini widget) is unchanged from parent.
 *
 * @package ScottPete
 */

if ( ! defined( 'ABSPATH' ) ) exit;

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
// ── Context: which category are we coming from? ────────────
$cat_id      = isset( $_GET['cat_id'] ) ? absint( $_GET['cat_id'] ) : 0;
$active_term = $cat_id ? get_term( $cat_id, 'product_category' ) : null;
if ( is_wp_error( $active_term ) ) $active_term = null;

// If no cat_id, try to get the first category of this product
if ( ! $active_term ) {
    $product_cats = wp_get_post_terms( get_the_ID(), 'product_category' );
    if ( ! empty( $product_cats ) && ! is_wp_error( $product_cats ) ) {
        $active_term = $product_cats[0];
    }
}

// ── Product data ───────────────────────────────────────────
$variations   = ipc_get_product_variations();
$has_multi    = ipc_product_has_multiple_variations();
$var_count    = count( $variations );
$destini_skus = ipc_get_product_destini_skus();
$destini_id   = ipc_option( 'destini_locator_id' );
$destini_apo  = ipc_option( 'destini_alpha_code' );

// ── All categories for the flip-card grid ─────────────────
$all_categories = get_terms( array(
    'taxonomy'   => 'product_category',
    'hide_empty' => true,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
) );

// ── ACF options ────────────────────────────────────────────
$grid_title    = ipc_option( 'products_grid_title',    '' );
$grid_subtitle = ipc_option( 'products_grid_subtitle', '' );
$grid_cta_text = ipc_option( 'products_grid_cta_text', __( 'All Products', 'ipc-base' ) );
$grid_cta_link = ipc_option( 'products_grid_cta_link', '' );

while ( have_posts() ) : the_post();
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
                    $is_active = ( $active_term && $term->term_id === $active_term->term_id );
                    $card_url  = ipc_get_category_card_url( $term );
                    $image_url = ipc_get_category_image_url( $term, 'category-card' );
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

    </div>
</section>


<?php /* ── Product List — same category, stays in place on the way into detail ── */ ?>
<?php
if ( $active_term ) :
    $siblings = get_posts( array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array( array(
            'taxonomy' => 'product_category',
            'field'    => 'term_id',
            'terms'    => $active_term->term_id,
        ) ),
    ) );
    if ( count( $siblings ) > 1 ) :
?>
<section class="product-list-section" id="product_list_grid">
    <div class="site-wrapper">

        <div class="back-to-grid">
            <a href="#product_list">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
                <?php esc_html_e( 'All Categories', 'ipc-base' ); ?>
            </a>
        </div>

        <h2 class="text-center" style="margin-bottom: var(--space-xl);">
            <?php echo esc_html( $active_term->name ); ?>
        </h2>

        <ul class="product-list-grid">
            <?php foreach ( $siblings as $sibling ) :
                $sibling_id    = $sibling->ID;
                $thumbnail_id  = get_post_meta( $sibling_id, '_product_thumbnail', true );
                $hover_id      = get_post_meta( $sibling_id, '_product_hover_thumbnail', true );
                $thumbnail_url = $thumbnail_id
                    ? wp_get_attachment_image_url( $thumbnail_id, 'product-thumbnail' )
                    : get_the_post_thumbnail_url( $sibling_id, 'product-thumbnail' );
                $hover_url = $hover_id
                    ? wp_get_attachment_image_url( $hover_id, 'product-thumbnail' )
                    : '';
                $is_current  = ( $sibling_id === get_the_ID() );
                $sibling_url = get_permalink( $sibling_id ) . '?cat_id=' . $active_term->term_id . '#product_detail';
            ?>
                <li>
                    <a href="<?php echo esc_url( $sibling_url ); ?>"
                       class="product-list-item<?php echo $is_current ? ' product-list-item--active' : ''; ?>">
                        <div class="product-list-item-images">
                            <?php if ( $thumbnail_url ) : ?>
                                <img class="img-default"
                                     src="<?php echo esc_url( $thumbnail_url ); ?>"
                                     alt="<?php echo esc_attr( get_the_title( $sibling_id ) ); ?>">
                            <?php endif; ?>
                            <?php if ( $hover_url ) : ?>
                                <img class="img-hover"
                                     src="<?php echo esc_url( $hover_url ); ?>"
                                     alt="<?php echo esc_attr( get_the_title( $sibling_id ) ); ?>">
                            <?php endif; ?>
                        </div>
                        <h3><?php echo esc_html( get_the_title( $sibling_id ) ); ?></h3>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
</section>
<?php
    endif;
endif;
?>


<?php /* ── Product Detail ───────────────────────────────── */ ?>
<section class="product-detail-section" id="product_detail">
    <div class="site-wrapper">

        <?php /* Back-to-grid anchor */ ?>
        <div class="back-to-grid">
            <a href="#product_list">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
                <?php if ( $active_term ) : ?>
                    <?php echo esc_html( $active_term->name ); ?>
                <?php else : ?>
                    <?php esc_html_e( 'All Products', 'ipc-base' ); ?>
                <?php endif; ?>
            </a>
        </div>

        <div class="product-detail-layout">

            <?php /* ── Left: Content ── */ ?>
            <div class="product-detail-content">

                <h1 class="product-detail-title"><?php the_title(); ?></h1>

                <?php if ( get_the_content() ) : ?>
                    <div class="product-detail-description">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

                <?php /* CTAs: Meal Ideas + Where to Buy */ ?>
                <div class="product-detail-ctas">

                    <?php /* Meal Ideas link — from recipe tag */ ?>
                    <?php ipc_the_product_meal_ideas_link(); ?>

                    <?php /* Destini Where to Buy widget */ ?>
                    <?php if ( $destini_skus && $destini_id ) : ?>
                        <div id="destini-locator-<?php echo esc_attr( $destini_id ); ?>"
                             class="destini-locator-class"
                             data-locator-id="<?php echo esc_attr( $destini_id ); ?>"
                             data-alpha-code="<?php echo esc_attr( $destini_apo ); ?>"
                             data-apo="<?php echo esc_attr( $destini_skus ); ?>">
                        </div>
                        <script src="https://lets.shop/productWidgetSnippet.js" charset="utf-8" defer></script>
                    <?php endif; ?>

                </div>

                <?php /* Variation navigation — only when 2+ variations */ ?>
                <?php if ( $has_multi ) : ?>
                    <nav class="product-variation-nav" aria-label="<?php esc_attr_e( 'Product variations', 'ipc-base' ); ?>">
                        <p class="product-variation-nav-label">
                            <?php esc_html_e( 'Select a variety:', 'ipc-base' ); ?>
                        </p>
                        <ul class="product-variation-nav-list">
                            <?php foreach ( $variations as $index => $variation ) : ?>
                                <li>
                                    <button type="button"
                                            class="<?php echo $index === 0 ? 'is-active' : ''; ?>"
                                            data-variation="<?php echo esc_attr( $index ); ?>"
                                            aria-controls="variation-panel-<?php echo esc_attr( $index ); ?>">
                                        <?php echo esc_html( $variation['name'] ); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div><!-- .product-detail-content -->


            <?php /* ── Right: Visual (hero image + variation panels) ── */ ?>
            <div class="product-detail-visual">

                <?php /* Hero lifestyle image (featured image) */ ?>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="product-detail-hero-image">
                        <?php the_post_thumbnail( 'product-hero', array(
                            'alt' => get_the_title(),
                        ) ); ?>
                    </div>
                <?php endif; ?>

                <?php /* Variation panels */ ?>
                <?php if ( ! empty( $variations ) ) : ?>
                    <div class="product-variation-panels" id="variationPanels">
                        <?php foreach ( $variations as $index => $variation ) :
                            $image_url  = $variation['image'] ? wp_get_attachment_image_url( $variation['image'], 'product-hero' ) : '';
                            $is_single  = ! $has_multi;
                            $is_active  = ( $index === 0 );
                            $panel_class = 'product-variation-panel';
                            if ( $is_single )  $panel_class .= ' product-variation-panel--single';
                            if ( $is_active )  $panel_class .= ' is-active';
                        ?>
                            <div class="<?php echo esc_attr( $panel_class ); ?>"
                                 id="variation-panel-<?php echo esc_attr( $index ); ?>"
                                 role="tabpanel">

                                <?php /* Package image */ ?>
                                <?php if ( $image_url ) : ?>
                                    <div class="product-variation-image">
                                        <img src="<?php echo esc_url( $image_url ); ?>"
                                             alt="<?php echo esc_attr( $variation['name'] ?: get_the_title() ); ?>">
                                    </div>
                                <?php endif; ?>

                                <?php /* Variation name — hidden for single-variation products */ ?>
                                <?php if ( $variation['name'] ) : ?>
                                    <h3 class="product-variation-name">
                                        <?php echo esc_html( $variation['name'] ); ?>
                                    </h3>
                                <?php endif; ?>

                                <?php /* Certifications — shown once per product (not per variation) */ ?>
                                <?php if ( $index === 0 ) : ?>
                                    <?php ipc_the_product_certifications(); ?>
                                <?php endif; ?>

                                <?php /* Nutrition facts */ ?>
                                <?php
                                $has_nutrition = $variation['calories'] || $variation['total_fat']
                                             || $variation['sugars']   || $variation['protein'];
                                ?>
                                <?php if ( $has_nutrition ) : ?>
                                    <div class="product-nutrition">
                                        <div class="product-nutrition-grid">
                                            <?php if ( $variation['calories'] ) : ?>
                                                <div class="product-nutrition-item">
                                                    <span class="product-nutrition-value">
                                                        <?php echo esc_html( $variation['calories'] ); ?>
                                                    </span>
                                                    <span class="product-nutrition-label">
                                                        <?php esc_html_e( 'Calories', 'ipc-base' ); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ( $variation['total_fat'] ) : ?>
                                                <div class="product-nutrition-item">
                                                    <span class="product-nutrition-value">
                                                        <?php echo esc_html( $variation['total_fat'] ); ?>
                                                    </span>
                                                    <span class="product-nutrition-label">
                                                        <?php esc_html_e( 'Total Fat', 'ipc-base' ); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ( $variation['sugars'] ) : ?>
                                                <div class="product-nutrition-item">
                                                    <span class="product-nutrition-value">
                                                        <?php echo esc_html( $variation['sugars'] ); ?>
                                                    </span>
                                                    <span class="product-nutrition-label">
                                                        <?php esc_html_e( 'Sugars', 'ipc-base' ); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ( $variation['protein'] ) : ?>
                                                <div class="product-nutrition-item">
                                                    <span class="product-nutrition-value">
                                                        <?php echo esc_html( $variation['protein'] ); ?>
                                                    </span>
                                                    <span class="product-nutrition-label">
                                                        <?php esc_html_e( 'Protein', 'ipc-base' ); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php /* Ingredients */ ?>
                                <?php if ( $variation['ingredients'] ) : ?>
                                    <div class="product-ingredients">
                                        <p class="product-ingredients-label">
                                            <?php esc_html_e( 'Ingredients', 'ipc-base' ); ?>
                                        </p>
                                        <p><?php echo wp_kses_post( $variation['ingredients'] ); ?></p>
                                    </div>
                                <?php endif; ?>

                            </div><!-- .product-variation-panel -->
                        <?php endforeach; ?>
                    </div><!-- .product-variation-panels -->
                <?php endif; ?>

            </div><!-- .product-detail-visual -->

        </div><!-- .product-detail-layout -->

    </div><!-- .site-wrapper -->
</section>

<?php endwhile; ?>

<?php get_footer();

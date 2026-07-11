<?php
/**
 * Scott Pete — Product Category Grid Block Override
 *
 * Overrides ipc-base/blocks/product-category-grid/product-category-grid.php
 *
 * The parent block links flip cards directly to get_term_link(), with no
 * anchor. This override calls ipc_get_category_card_url() — the helper in
 * product-template-tags.php — which already implements the correct routing:
 *
 *   count = 1  → {product_permalink}#product_detail   (skip the list)
 *   count > 1  → {term_archive}#product_list          (circles at top)
 *   count = 0  → card is hidden entirely
 *
 * ACF Fields (same as parent, field-group.json lives in ipc-base):
 *   cta_text  (text) — button label below the grid
 *   cta_link  (url)  — button URL
 *
 * Category image is stored as attachment ID in term meta _category_image.
 * ipc_get_category_image_url() reads it — no ACF get_field() needed.
 *
 * @package ScottPete
 */

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'product-category-grid-' . $block['id'];
$cta_text = get_field( 'cta_text' ) ?: '';
$cta_link = get_field( 'cta_link' ) ?: '';

// Same options fields taxonomy-product_category.php and single-product.php
// read for their category-row header — one field, three templates.
//
// On the homepage this block is nested inside a red-container that already
// carries its own "More Products To Explore" heading, so the options header
// ("PREMIUM PRODUCTS" + subtitle) would render a second, duplicate title on
// a navy box. Suppress it on the front page; keep it on the product
// archive / single templates where the block stands alone.
$show_heading  = ! is_front_page();
$grid_title    = $show_heading ? ipc_option( 'products_grid_title',    '' ) : '';
$grid_subtitle = $show_heading ? ipc_option( 'products_grid_subtitle', '' ) : '';

$terms = get_terms( array(
    'taxonomy'   => 'product_category',
    'hide_empty' => false,
    'parent'     => 0,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
) );

if ( is_wp_error( $terms ) || empty( $terms ) ) return;
?>
<div class="product-category-grid" id="<?php echo esc_attr( $block_id ); ?>">

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

    <div class="product-category-grid__cards">
        <?php foreach ( $terms as $term ) :
            $card_url  = ipc_get_category_card_url( $term );
            $image_url = ipc_get_category_image_url( $term, 'category-card' );

            // Hide empty categories
            if ( ! $card_url ) continue;

            $desc = $term->description;
        ?>
            <div class="product-category-card">
                <a class="product-category-card__flip"
                   href="<?php echo esc_url( $card_url ); ?>">

                    <div class="product-category-card__front">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>"
                                 alt="<?php echo esc_attr( $term->name ); ?>">
                        <?php else : ?>
                            <div class="product-category-card__placeholder"></div>
                        <?php endif; ?>
                        <h3 class="product-category-card__label">
                            <?php echo esc_html( $term->name ); ?>
                        </h3>
                    </div>

                    <div class="product-category-card__back">
                        <h3><?php echo esc_html( $term->name ); ?></h3>
                        <?php if ( $desc ) : ?>
                            <p><?php echo wp_kses_post( $desc ); ?></p>
                        <?php endif; ?>
                    </div>

                </a>
            </div>

        <?php endforeach; ?>
    </div>

    <?php if ( $cta_text && $cta_link ) : ?>
        <div class="product-category-grid__cta">
            <a href="<?php echo esc_url( $cta_link ); ?>" class="btn">
                <?php echo esc_html( $cta_text ); ?>
            </a>
        </div>
    <?php endif; ?>

</div>

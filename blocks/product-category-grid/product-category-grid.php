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
                        <?php if ( $desc ) : ?>
                            <p><?php echo wp_kses_post( $desc ); ?></p>
                        <?php else : ?>
                            <p><?php echo esc_html( $term->name ); ?></p>
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

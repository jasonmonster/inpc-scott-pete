<?php
/**
 * Scott Pete — Pete's Perks Block (join-now-form override)
 *
 * Two-zone layout:
 *   1. Hero zone  — skyline background image, product assortment PNG
 *                   overlapping down into the form zone
 *   2. Form zone  — navy BG, heading/copy, raw Mailchimp embed
 *
 * ACF Fields:
 *   skyline_image  (image, URL) — Chicago skyline (gold-toned, output by designer)
 *   product_image  (image, URL) — product assortment + Scott Pete logo PNG
 *   title          (text)       — e.g. "Pete's Perks"
 *   subtitle       (text)       — e.g. "Reserved for the biggest Scott Pete fans..."
 *   content        (textarea)   — body copy paragraph
 *   embed_code     (textarea)   — raw Mailchimp HTML embed (not a shortcode)
 *
 * Note: The Mailchimp embed code contains a Facebook Like widget hardcoded
 * to another brand. It is hidden via CSS (.fb-like { display: none }).
 * Update the form action URL in the embed code to the Scott Pete list URL
 * when available from Mailchimp.
 *
 * @package ScottPete
 */

$skyline     = get_field( 'skyline_image' );
$product_img = get_field( 'product_image' );
$title       = get_field( 'title' )   ?: "Pete's Perks";
$subtitle    = get_field( 'subtitle' );
$content     = get_field( 'content' );
$embed_code  = get_field( 'embed_code' );

$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'join-form-' . $block['id'];
$bg_style    = $skyline ? ' style="background-image:url(' . esc_url( $skyline ) . ');"' : '';
?>
<section class="join-form-section perks-section" id="<?php echo esc_attr( $block_id ); ?>">

    <!-- Zone 1: Skyline hero -->
    <div class="perks-hero"<?php echo $bg_style; ?>>
        <?php if ( $product_img ) : ?>
            <div class="perks-product-wrap">
                <img class="perks-product-img"
                     src="<?php echo esc_url( $product_img ); ?>"
                     alt="Scott Pete Products">
            </div>
        <?php endif; ?>
    </div>

    <!-- Zone 2: Navy form area -->
    <div class="perks-form-zone">
        <div class="site-wrapper">

            <div class="perks-content">
                <?php if ( $title ) : ?>
                    <h2 class="perks-title"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <?php if ( $subtitle ) : ?>
                    <p class="perks-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
                <?php if ( $content ) : ?>
                    <p class="perks-body"><?php echo wp_kses_post( $content ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( $embed_code ) : ?>
                <div class="perks-embed">
                    <?php
                    /*
                     * Output raw Mailchimp embed HTML.
                     * No escaping — Mailchimp embed requires <script> tags and
                     * inline styles that wp_kses_post() would strip.
                     * The embed_code field should only ever be populated by an
                     * admin with the raw snippet from Mailchimp's embed builder.
                     */
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $embed_code;
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</section>

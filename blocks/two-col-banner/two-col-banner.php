<?php
/**
 * Scott Pete — Two Column Banner Block Override
 *
 * Overrides ipc-base/blocks/two-col-banner/two-col-banner.php
 *
 * Change from parent: CTA button moved into .two-col-banner-image
 * so it renders below the heading image (left column) per the
 * Scott Pete design, rather than below the text (right column).
 *
 * ACF Fields (unchanged from parent):
 *   image, image_side, sub_title, title, content, cta_text, cta_link, bg_color
 *
 * @package ScottPete
 */

$image    = get_field( 'image' );
$side     = get_field( 'image_side' ) ?: 'left';
$subtitle = get_field( 'sub_title' );
$title    = get_field( 'title' );
$content  = get_field( 'content' );
$cta_text = get_field( 'cta_text' );
$cta_link = get_field( 'cta_link' );
$bg_color = get_field( 'bg_color' );

$block_id  = ! empty( $block['anchor'] ) ? $block['anchor'] : 'two-col-' . $block['id'];
$bg_style  = $bg_color ? ' style="background-color:' . esc_attr( $bg_color ) . ';"' : '';
$mod_class = $side === 'right' ? ' two-col-banner--image-right' : '';
?>
<section class="two-col-banner<?php echo $mod_class; ?>" id="<?php echo esc_attr( $block_id ); ?>"<?php echo $bg_style; ?>>
    <div class="site-wrapper">
        <div class="two-col-banner-inner">

            <?php if ( $image ) : ?>
                <div class="two-col-banner-image">
                    <img src="<?php echo esc_url( $image ); ?>"
                         alt="<?php echo esc_attr( $title ?: '' ); ?>">
                    <?php if ( $cta_text && $cta_link ) : ?>
                        <a href="<?php echo esc_url( $cta_link ); ?>" class="btn">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="two-col-banner-content">
                <?php if ( $subtitle ) : ?>
                    <span class="eyebrow"><?php echo esc_html( $subtitle ); ?></span>
                <?php endif; ?>
                <?php if ( $title ) : ?>
                    <h2><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <?php if ( $content ) : ?>
                    <div class="two-col-banner-text">
                        <?php echo wp_kses_post( $content ); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

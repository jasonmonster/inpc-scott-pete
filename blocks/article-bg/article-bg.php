<?php
/**
 * Scott Pete — Flavor Fan Block (article-bg override)
 *
 * ACF fields used per slide:
 *   header_text      — section title (first slide only)
 *   sub_title        — section subtitle (first slide only)
 *   background_color — section bg color (first slide only)
 *   background_image — logo scatter pattern (first slide only)
 *   hero_image       — large sausage photo (swaps in main area on hover)
 *   article_image    — product package thumbnail
 *   cta_text         — product label below thumbnail
 *   cta_link         — product page link (optional)
 *
 * @package ScottPete
 */

$slides = get_field( 'promo_slides' );
if ( empty( $slides ) ) return;

$block_id  = ! empty( $block['anchor'] ) ? $block['anchor'] : 'article-bg-' . $block['id'];
$first     = $slides[0];

$title     = $first['header_text']      ?? '';
$subtitle  = $first['sub_title']        ?? '';
$bg_color  = $first['background_color'] ?? '#152e86';
$bg_image  = $first['background_image'] ?? '';
$main_img  = $first['hero_image']       ?? ''; // default hero = first slide's hero

$bg_style  = 'background-color:' . esc_attr( $bg_color ) . ';';
if ( $bg_image ) {
    $bg_style .= 'background-image:url(' . esc_url( $bg_image ) . ');';
}
?>
<section class="flavor-fan" id="<?php echo esc_attr( $block_id ); ?>"
         style="<?php echo $bg_style; ?>">

    <div class="site-wrapper">

        <?php if ( $title ) : ?>
            <h2 class="flavor-fan-title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $subtitle ) : ?>
            <p class="flavor-fan-subtitle"><?php echo esc_html( $subtitle ); ?></p>
        <?php endif; ?>

        <?php if ( $main_img ) : ?>
            <div class="flavor-fan-main">
                <img class="flavor-fan-main-image"
                     src="<?php echo esc_url( $main_img ); ?>"
                     alt="">
            </div>
        <?php endif; ?>

        <div class="flavor-fan-thumbs">
            <?php foreach ( $slides as $index => $slide ) :
                $thumb     = $slide['article_image'] ?? '';
                $hero      = $slide['hero_image']    ?? '';
                $label     = $slide['cta_text']      ?? '';
                $link      = $slide['cta_link']      ?? '';
                if ( ! $thumb ) continue;
            ?>
                <div class="flavor-fan-thumb <?php echo $index === 0 ? 'is-active' : ''; ?>"
                     data-hero="<?php echo esc_url( $hero ); ?>">
                    <?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>"><?php endif; ?>
                        <img src="<?php echo esc_url( $thumb ); ?>"
                             alt="<?php echo esc_attr( $label ); ?>">
                        <?php if ( $label ) : ?>
                            <span class="flavor-fan-label"><?php echo esc_html( $label ); ?></span>
                        <?php endif; ?>
                    <?php if ( $link ) : ?></a><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</section>

<script>
( function () {
    var section = document.getElementById( '<?php echo esc_js( $block_id ); ?>' );
    if ( ! section ) return;

    var mainImg = section.querySelector( '.flavor-fan-main-image' );
    var thumbs  = section.querySelectorAll( '.flavor-fan-thumb' );

    if ( ! mainImg || ! thumbs.length ) return;

    thumbs.forEach( function ( thumb ) {
        thumb.addEventListener( 'mouseenter', function () {
            var hero = this.dataset.hero;
            if ( hero ) mainImg.src = hero;

            thumbs.forEach( function ( t ) { t.classList.remove( 'is-active' ); } );
            this.classList.add( 'is-active' );
        } );
    } );
} )();
</script>

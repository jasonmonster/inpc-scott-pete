<?php
/**
 * Scott Pete — Recipe Decision Tree Block (reciepes-cta override)
 *
 * Renders the "Recipe Decision Tree" section:
 *   - Large heading + subtitle
 *   - Inline SVG bracket (red lines) behind the panels
 *   - Two pre-made polaroid images (shadow/tilt/label baked into the PNG)
 *   - Center logo at the bracket junction
 *   - "All Recipes" CTA button below the logo
 *
 * ACF Fields:
 *   title        (text)       — e.g. "Recipe Decision Tree"
 *   subtitle     (text)       — e.g. "Click below to browse..."
 *   left_image   (image, URL) — full polaroid PNG (tilt + shadow + label baked in)
 *   left_link    (url)        — recipe category archive URL
 *   right_image  (image, URL) — full polaroid PNG (tilt + shadow + label baked in)
 *   right_link   (url)        — recipe category archive URL
 *   center_logo  (image, URL) — Scott Pete 100 badge
 *   cta_text     (text)       — e.g. "All Recipes"
 *   cta_link     (url)        — all recipes archive URL
 *
 * @package ScottPete
 */

$title      = get_field( 'title' )      ?: 'Recipe Decision Tree';
$subtitle   = get_field( 'subtitle' );
$left_img   = get_field( 'left_image' );
$left_link  = get_field( 'left_link' );
$right_img  = get_field( 'right_image' );
$right_link = get_field( 'right_link' );
$logo       = get_field( 'center_logo' );
$cta_text   = get_field( 'cta_text' )   ?: 'All Recipes';
$cta_link   = get_field( 'cta_link' );

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'recipes-cta-' . $block['id'];
?>
<section class="recipes-cta recipe-decision-tree" id="<?php echo esc_attr( $block_id ); ?>">
    <div class="site-wrapper">

        <!-- Heading -->
        <div class="rdt-heading">
            <?php if ( $title ) : ?>
                <h2 class="rdt-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="rdt-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Bracket + panels -->
        <div class="rdt-bracket-wrap">

            <!--
                SVG bracket — decision tree connector lines.
                viewBox coords:
                  500 = center (stem + horizontal midpoint)
                  190 = left arm x (roughly center of left column in a 1fr/auto/1fr grid)
                  810 = right arm x
                  100 = y of horizontal bar
                  200 = bottom of SVG (meets top of polaroid images)
                preserveAspectRatio="none" stretches it to any container width.
            -->
            <svg class="rdt-bracket"
                 viewBox="0 0 1000 200"
                 preserveAspectRatio="none"
                 aria-hidden="true"
                 focusable="false"
                 xmlns="http://www.w3.org/2000/svg">
                <line x1="500" y1="0"   x2="500" y2="100" />
                <line x1="190" y1="100" x2="810" y2="100" />
                <line x1="190" y1="100" x2="190" y2="200" />
                <line x1="810" y1="100" x2="810" y2="200" />
            </svg>

            <!-- Three-column panels row -->
            <div class="rdt-panels">

                <!-- Left polaroid -->
                <div class="rdt-panel rdt-panel--left">
                    <?php if ( $left_img ) : ?>
                        <?php if ( $left_link ) : ?>
                            <a href="<?php echo esc_url( $left_link ); ?>" class="rdt-polaroid-link">
                        <?php endif; ?>
                            <img class="rdt-polaroid-img"
                                 src="<?php echo esc_url( $left_img ); ?>"
                                 alt="Bun-Wrecking Recipes">
                        <?php if ( $left_link ) : ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Center: logo + CTA -->
                <div class="rdt-center">
                    <?php if ( $logo ) : ?>
                        <img class="rdt-logo"
                             src="<?php echo esc_url( $logo ); ?>"
                             alt="Scott Pete">
                    <?php endif; ?>
                    <?php if ( $cta_text && $cta_link ) : ?>
                        <a href="<?php echo esc_url( $cta_link ); ?>" class="btn rdt-cta">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Right polaroid -->
                <div class="rdt-panel rdt-panel--right">
                    <?php if ( $right_img ) : ?>
                        <?php if ( $right_link ) : ?>
                            <a href="<?php echo esc_url( $right_link ); ?>" class="rdt-polaroid-link">
                        <?php endif; ?>
                            <img class="rdt-polaroid-img"
                                 src="<?php echo esc_url( $right_img ); ?>"
                                 alt="Give the Bun a Break">
                        <?php if ( $right_link ) : ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div><!-- .rdt-panels -->
        </div><!-- .rdt-bracket-wrap -->

    </div><!-- .site-wrapper -->
</section>

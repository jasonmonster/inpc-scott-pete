<?php
/**
 * Scott Pete — Recipes Archive Hub
 *
 * Overrides ipc-base/archive-recipe.php.
 *
 * Changes from parent:
 *   1. Adds a "Bun or No Bun?" section between the banner and the
 *      category slider — two large link-cards pointing at the
 *      'bun-wrecking-recipes' and 'bun-free-recipes' recipe_category
 *      terms. Content comes from the recipe_bun_card_* /
 *      recipe_nobun_card_* fields (Theme Settings → Recipes). If
 *      those terms don't exist yet, the section just doesn't
 *      render — nothing to break.
 *
 * Everything else (banner, category slider, tag grid) is unchanged
 * from parent, pulled from the same shared template parts. The
 * category slider excludes those two terms automatically via the
 * ipc_recipe_category_exclude_slugs filter in functions.php, since
 * they get their own dedicated cards here instead.
 *
 * @package ScottPete
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

get_template_part( 'template-parts/recipe-banner' );

// ── Bun or No Bun ────────────────────────────────────────
$bun_term   = get_term_by( 'slug', 'bun-wrecking-recipes', 'recipe_category' );
$nobun_term = get_term_by( 'slug', 'bun-free-recipes',      'recipe_category' );

if ( $bun_term && $nobun_term ) :

    $bun_image    = ipc_option( 'recipe_bun_card_image' );
    $bun_label    = ipc_option( 'recipe_bun_card_label', __( 'Bun-Wrecking Recipes', 'scott-pete' ) );
    $nobun_image  = ipc_option( 'recipe_nobun_card_image' );
    $nobun_label  = ipc_option( 'recipe_nobun_card_label', __( 'Give the Bun a Break', 'scott-pete' ) );
    $bun_title    = ipc_option( 'recipe_bun_section_title',    __( 'Bun or No Bun?', 'scott-pete' ) );
    $bun_subtitle = ipc_option( 'recipe_bun_section_subtitle', __( 'Choose your path or choose your recipe category below', 'scott-pete' ) );
    ?>

    <section class="recipe-bun-section" id="bun_section">
        <div class="site-wrapper">

            <header class="section-heading">
                <h2><?php echo esc_html( $bun_title ); ?></h2>
                <?php if ( $bun_subtitle ) : ?>
                    <p class="section-subtitle"><?php echo esc_html( $bun_subtitle ); ?></p>
                <?php endif; ?>
            </header>

            <div class="recipe-bun-cards">

                <a class="recipe-bun-card" href="<?php echo esc_url( get_term_link( $bun_term ) . '#recipe_list' ); ?>">
                    <?php if ( $bun_image ) : ?>
                        <img src="<?php echo esc_url( $bun_image ); ?>" alt="<?php echo esc_attr( $bun_label ); ?>">
                    <?php endif; ?>
                    <span class="recipe-bun-card-label"><?php echo esc_html( $bun_label ); ?></span>
                </a>

                <a class="recipe-bun-card" href="<?php echo esc_url( get_term_link( $nobun_term ) . '#recipe_list' ); ?>">
                    <?php if ( $nobun_image ) : ?>
                        <img src="<?php echo esc_url( $nobun_image ); ?>" alt="<?php echo esc_attr( $nobun_label ); ?>">
                    <?php endif; ?>
                    <span class="recipe-bun-card-label"><?php echo esc_html( $nobun_label ); ?></span>
                </a>

            </div>

        </div>
    </section>

<?php endif; ?>

<?php
get_template_part( 'template-parts/recipe-category-slider' );
get_template_part( 'template-parts/recipe-tag-grid' );

get_footer();

<?php
/**
 * Scott Pete — Child Theme Functions
 *
 * Responsibilities:
 *  - Enqueue parent (ipc-base) stylesheet
 *  - Enqueue child stylesheet
 *  - Load Adobe Fonts for this brand
 *  - Register any Scott Pete–specific overrides
 *
 * All CPTs, meta boxes, ACF options pages, and block registration
 * are handled by the ipc-base parent theme.
 */

defined( 'ABSPATH' ) || exit;


/* =========================================================
   1. ENQUEUE STYLES
   ========================================================= */

add_action( 'wp_enqueue_scripts', 'scott_pete_enqueue_styles' );

function scott_pete_enqueue_styles() {

	// 1a. Parent theme stylesheet
	wp_enqueue_style(
		'ipc-base-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'ipc-base' )->get( 'Version' )
	);

	// 1b. Child theme stylesheet (variable overrides + brand components)
	wp_enqueue_style(
		'scott-pete-style',
		get_stylesheet_uri(),
		array( 'ipc-base-style' ),
		wp_get_theme()->get( 'Version' )
	);

}


/* =========================================================
   2. FONTS — Adobe Fonts (kit: ipc5oaz)
   ========================================================= */

add_action( 'wp_head', 'scott_pete_adobe_fonts', 5 );

function scott_pete_adobe_fonts() {
    echo '<link rel="stylesheet" href="https://use.typekit.net/ipc5oaz.css">' . "\n";
}

/**
 * Adobe Fonts kit reference (ipc5oaz):
 *   "futura-pt"            400/700  — body and UI text
 *   "futura-pt-bold"       700      — bold display variant
 *   "futura-pt-condensed"  400/500/700/800 — headings and CTAs
 *     500 = Cond Medium  → --font-cta (buttons, labels)
 *     800 = Cond Extra Bold → --font-heading (section headings)
 */


/* =========================================================
   3. DERIVED BRAND CSS VARIABLES
   Runs at priority 100 — after ipc_output_brand_css (priority 99).
   Outputs additional variables derived from the ACF brand color
   fields that brand.php doesn't cover natively:

     --color-bg-dark          ← from brand_color_primary_dark
                                 used for dark section backgrounds
                                 (product category grid, etc.)

     --product-flip-back-bg   ← from brand_color_secondary
                                 flip card back face color

   This avoids hardcoding hex values in child CSS while keeping
   everything driven by the ACF Brand options page.
   ========================================================= */

add_action( 'wp_head', 'scott_pete_derived_brand_css', 100 );

function scott_pete_derived_brand_css() {
    $primary_dark = ipc_option( 'brand_color_primary_dark' );
    $secondary    = ipc_option( 'brand_color_secondary' );

    if ( ! $primary_dark && ! $secondary ) return;

    echo '<style id="scott-pete-derived-css">' . "\n";
    echo ':root {' . "\n";

    if ( $primary_dark ) {
        $safe = ipc_sanitize_css_color( $primary_dark );
        if ( $safe ) {
            echo '    --color-bg-dark: ' . $safe . ';' . "\n";
        }
    }

    if ( $secondary ) {
        $safe = ipc_sanitize_css_color( $secondary );
        if ( $safe ) {
            echo '    --product-flip-back-bg: ' . $safe . ';' . "\n";
        }
    }

    echo '}' . "\n";
    echo '</style>' . "\n";
}


/* =========================================================
   4. HEADER SCROLL BEHAVIOR
   Homepage: transparent → gold sticky (adds .is-sticky)
   Internal pages: gold always visible, logo shrinks on scroll
   (adds .logo-scrolled at 40px threshold)
   ========================================================= */

add_action( 'wp_footer', 'scott_pete_sticky_header_script' );

function scott_pete_sticky_header_script() {
	?>
	<script>
	( function () {
		var header    = document.querySelector( '.site-header' );
		var body      = document.body;
		var isHome    = body.classList.contains( 'home' );
		var threshold = 80;
		var logoThreshold = 40;

		if ( ! header ) return;

		if ( isHome ) {
			// Homepage: transparent → solid sticky
			function onScroll() {
				if ( window.scrollY > threshold ) {
					header.classList.add( 'is-sticky' );
					body.classList.add( 'header-is-sticky' );
				} else {
					header.classList.remove( 'is-sticky' );
					body.classList.remove( 'header-is-sticky' );
				}
			}
			window.addEventListener( 'scroll', onScroll, { passive: true } );
			onScroll();
		} else {
			// Internal pages: logo shrinks on scroll
			function onScrollInternal() {
				if ( window.scrollY > logoThreshold ) {
					header.classList.add( 'logo-scrolled' );
				} else {
					header.classList.remove( 'logo-scrolled' );
				}
			}
			window.addEventListener( 'scroll', onScrollInternal, { passive: true } );
			onScrollInternal();
		}
	} )();
	</script>
	<?php
}


/* =========================================================
   5. THEME SETUP
   ========================================================= */

add_action( 'after_setup_theme', 'scott_pete_setup' );

function scott_pete_setup() {

	// Child theme text domain
	load_child_theme_textdomain( 'scott-pete', get_stylesheet_directory() . '/languages' );

}


/* =========================================================
   6. BRAND IDENTITY FILTER
   Lets the parent theme know which brand is active so it can
   apply brand-specific logic (e.g. Destini embed, social links).
   ========================================================= */

add_filter( 'ipc_active_brand', function() {
	return 'scott-pete';
} );


/* =========================================================
   7. CUSTOM BODY CLASSES
   ========================================================= */

add_filter( 'body_class', 'scott_pete_body_classes' );

function scott_pete_body_classes( $classes ) {
	$classes[] = 'brand-scott-pete';
	return $classes;
}


/* =========================================================
   8. RECIPE CATEGORY SLIDER — EXCLUDE BUN / NO BUN
   Bun and No Bun are real recipe_category terms so their archive
   pages work like any other category, but they get their own large
   cards on the recipes hub (archive-recipe.php) instead of sitting
   in the auto-generated flip-card slider with Grill, Appetizers,
   etc. This keeps them out of every slider instance across the
   recipe template family — hub, category archive, tag archive,
   and single recipe — without touching ipc-base at all.
   ========================================================= */

add_filter( 'ipc_recipe_category_exclude_slugs', function( $slugs ) {
	$slugs[] = 'bun';
	$slugs[] = 'no-bun';
	return $slugs;
} );


/* =========================================================
   9. DESTINI — TEMPORARY HARDCODED LOCATOR VALUES
   ---------------------------------------------------------
   !!! TODO: REPLACE WITH SCOTT PETE'S OWN VALUES !!!

   The parent single-product.php reads these via
   ipc_option('destini_locator_id') / ipc_option('destini_alpha_code'),
   which call get_field($name, 'option'). Those ACF options are not yet
   registered/populated for Scott Pete, so the "Where to Buy" button
   won't render until they return a value.

   As a stopgap we borrow Kentucky Legend's live values (locator 4812,
   alpha 12CC) so the Destini widget renders and can be styled/tested.
   These point at KYL's store data — they are NOT correct for Scott Pete.

   pre_load_value short-circuits ACF before it looks up the field, so this
   works even though the fields aren't registered in an options group yet.

   WHEN REAL VALUES ARRIVE: delete this entire block and set the values
   in the proper ACF Destini options (or register the fields there).
   ========================================================= */

add_filter( 'acf/pre_load_value', 'scott_pete_destini_temp_values', 10, 3 );

function scott_pete_destini_temp_values( $value, $post_id, $field ) {
	// Only act on the options page, not individual posts.
	if ( 'option' !== $post_id && 'options' !== $post_id ) {
		return $value;
	}

	$name = is_array( $field ) && isset( $field['name'] ) ? $field['name'] : '';

	// TEMP: Kentucky Legend values — replace with Scott Pete's.
	if ( 'destini_locator_id' === $name ) {
		return '4812';
	}
	if ( 'destini_alpha_code' === $name ) {
		return '12CC';
	}

	return $value;
}

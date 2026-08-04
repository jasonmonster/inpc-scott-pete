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
	$slugs[] = 'bun-wrecking-recipes';
	$slugs[] = 'bun-free-recipes';
	return $slugs;
} );


/* =========================================================
   9. DESTINI — TEMPORARY HARDCODED LOCATOR VALUES
   ---------------------------------------------------------
   !!! TODO: REPLACE WITH REAL ACF DESTINI OPTIONS !!!

   Scott Pete runs TWO separate Destini locators, each with its own
   loader script. Keep them apart or the wrong one renders (a full map
   vs. a single button):

     * Store locator — Where to Buy page (the full map).
         destini_store_locator_id = 4624
         destini_store_alpha_code = 1210
         script: productFirstSnippet.js   (page-where-to-buy.php)

     * Product widget — single product pages (the per-product button).
         destini_locator_id       = 4630
         destini_alpha_code       = 1216
         script: productWidgetSnippet.js  (single-product.php)
         data-apo is the product's SKUs, from _product_destini_skus meta.

   pre_load_value short-circuits ACF before it looks up the field, so these
   resolve even though the fields aren't registered in an options group yet.

   WHEN REAL VALUES ARE WIRED: delete this whole block and set the values
   in the proper ACF Destini options, keeping the two locators separate.
   ========================================================= */

add_filter( 'acf/pre_load_value', 'scott_pete_destini_temp_values', 10, 3 );

function scott_pete_destini_temp_values( $value, $post_id, $field ) {
	// Only act on the options store, not individual posts.
	if ( 'option' !== $post_id && 'options' !== $post_id ) {
		return $value;
	}

	$name = is_array( $field ) && isset( $field['name'] ) ? $field['name'] : '';

	// Store locator — Where to Buy page (full map, productFirstSnippet.js).
	if ( 'destini_store_locator_id' === $name ) {
		return '4624';
	}
	if ( 'destini_store_alpha_code' === $name ) {
		return '1210';
	}

	// Product widget — single product pages (button, productWidgetSnippet.js).
	if ( 'destini_locator_id' === $name ) {
		return '4630';
	}
	if ( 'destini_alpha_code' === $name ) {
		return '1216';
	}

	return $value;
}


/* =========================================================
   10. ACF — ALLOW RELATIVE URLS IN URL FIELDS
   ---------------------------------------------------------
   ACF's URL field only accepts values that contain "://" or
   start with "//". Internal paths like "/recipes/" therefore
   fail validation and block the whole post from saving.

   This filter runs after ACF's own url check (priority 20 vs
   ACF's 10) and rescues root-relative ("/recipes/"),
   protocol-relative ("//cdn.example.com"), and on-page anchor
   ("#section") values. Everything else keeps ACF's result, so
   genuine junk is still rejected.

   Applies to every ACF URL field site-wide — including the
   Recipe Decision Tree block's left_link / right_link / cta_link.
   ========================================================= */

add_filter( 'acf/validate_value/type=url', 'scott_pete_allow_relative_urls', 20, 4 );

function scott_pete_allow_relative_urls( $valid, $value, $field, $input ) {

	// Leave ACF's result alone if it already passed or the value is empty.
	if ( true === $valid || '' === $value || null === $value ) {
		return $valid;
	}

	// Accept root-relative ("/recipes/"), protocol-relative ("//host"),
	// and on-page anchor ("#section") values.
	if ( is_string( $value ) ) {
		$first = substr( $value, 0, 1 );
		if ( '/' === $first || '#' === $first ) {
			return true;
		}
	}

	return $valid;
}

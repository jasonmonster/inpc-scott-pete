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
   3. STICKY HEADER SCROLL BEHAVIOR
   Adds/removes .is-sticky on the header as the user scrolls.
   Inline script kept small — no external file needed.
   ========================================================= */

add_action( 'wp_footer', 'scott_pete_sticky_header_script' );

function scott_pete_sticky_header_script() {
	?>
	<script>
	( function () {
		var header    = document.querySelector( '.site-header' );
		var body      = document.body;
		var threshold = 80;

		if ( ! header ) return;

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
	} )();
	</script>
	<?php
}


/* =========================================================
   4. THEME SETUP
   ========================================================= */

add_action( 'after_setup_theme', 'scott_pete_setup' );

function scott_pete_setup() {

	// Child theme text domain
	load_child_theme_textdomain( 'scott-pete', get_stylesheet_directory() . '/languages' );

}


/* =========================================================
   4. BRAND IDENTITY FILTER
   Lets the parent theme know which brand is active so it can
   apply brand-specific logic (e.g. Destini embed, social links).
   ========================================================= */

add_filter( 'ipc_active_brand', function() {
	return 'scott-pete';
} );


/* =========================================================
   5. CUSTOM BODY CLASSES
   ========================================================= */

add_filter( 'body_class', 'scott_pete_body_classes' );

function scott_pete_body_classes( $classes ) {
	$classes[] = 'brand-scott-pete';
	return $classes;
}

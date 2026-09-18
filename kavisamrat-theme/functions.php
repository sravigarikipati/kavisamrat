<?php
/**
 * Kavisamrat — Digital Archive of Viswanadha Sathyanarayana
 * Theme functions.
 *
 * Requires: Pods Framework (https://pods.io), SiteOrigin Page Builder
 * (+ optionally SiteOrigin Widgets Bundle).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'KAVISAMRAT_VERSION', '1.0.8' );
define( 'KAVISAMRAT_DIR', get_template_directory() );
define( 'KAVISAMRAT_URI', get_template_directory_uri() );

/* -----------------------------------------------------------
 * 1. THEME SUPPORT
 * --------------------------------------------------------- */
function kavisamrat_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// SiteOrigin Page Builder needs this declared on any CPT that should be
	// buildable with it. Also declared per-pod in inc/register-book-pod.php.
	add_theme_support( 'siteorigin-panels', array(
		'contain' => true,
	) );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'kavisamrat' ),
		'footer'  => __( 'Footer Navigation', 'kavisamrat' ),
	) );

	set_post_thumbnail_size( 800, 1200, true ); // portrait book covers
}
add_action( 'after_setup_theme', 'kavisamrat_setup' );

/* -----------------------------------------------------------
 * 2. ASSETS — fonts, design system CSS, GSAP, animation JS
 * --------------------------------------------------------- */
function kavisamrat_enqueue_assets() {

	// Google Fonts — Tiro Telugu (headings) + Noto Sans Telugu (body)
	wp_enqueue_style(
		'kavisamrat-fonts',
		'https://fonts.googleapis.com/css2?family=Tiro+Telugu:ital@0;1&family=Noto+Sans+Telugu:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);

	// Design system
	wp_enqueue_style(
		'kavisamrat-design-system',
		KAVISAMRAT_URI . '/assets/css/kavisamrat-design-system.css',
		array( 'kavisamrat-fonts' ),
		KAVISAMRAT_VERSION
	);

	// GSAP core + ScrollTrigger (CDN, deferred)
	wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );

	wp_enqueue_script(
		'kavisamrat-animations',
		KAVISAMRAT_URI . '/assets/js/kavisamrat-animations.js',
		array( 'gsap', 'gsap-scrolltrigger' ),
		KAVISAMRAT_VERSION,
		true
	);

	if ( is_singular( array( 'book', 'article', 'authors' ) ) || is_post_type_archive( array( 'book', 'article', 'authors' ) ) || is_front_page() ) {
		wp_enqueue_style( 'kavisamrat-book', KAVISAMRAT_URI . '/assets/css/book.css', array( 'kavisamrat-design-system' ), KAVISAMRAT_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'kavisamrat_enqueue_assets' );

function kavisamrat_favicon() {
	$icon_url = KAVISAMRAT_URI . '/assets/images/highlight-logo.png?ver=' . KAVISAMRAT_VERSION;
	echo '<link rel="icon" type="image/png" href="' . esc_url( $icon_url ) . '" />' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $icon_url ) . '" />' . "\n";
}
add_action( 'wp_head', 'kavisamrat_favicon', 1 );

// Ensure the body gets our theming hook class
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'kavisamrat';
	return $classes;
} );

/* -----------------------------------------------------------
 * 3. REQUIRE PLUGIN CHECK — friendly admin notice
 * --------------------------------------------------------- */
function kavisamrat_plugin_check_notice() {
	$missing = array();
	if ( ! function_exists( 'pods' ) )                     $missing[] = 'Pods Framework';
	if ( ! class_exists( 'SiteOrigin_Panels' ) )           $missing[] = 'SiteOrigin Page Builder';

	if ( $missing ) {
		printf(
			'<div class="notice notice-warning"><p><strong>Kavisamrat theme:</strong> please install/activate: %s</p></div>',
			esc_html( implode( ', ', $missing ) )
		);
	}
}
add_action( 'admin_notices', 'kavisamrat_plugin_check_notice' );

/* -----------------------------------------------------------
 * 4. LOAD PODS 'book' REGISTRATION (code-based, idempotent)
 *    See inc/register-book-pod.php — this is the authoritative
 *    definition of the Pod; pods-export/book-pod-export.json is
 *    provided as an alternative importable copy for the Pods UI.
 * --------------------------------------------------------- */
require_once KAVISAMRAT_DIR . '/inc/register-book-pod.php';
require_once KAVISAMRAT_DIR . '/inc/register-editorial-pods.php';
require_once KAVISAMRAT_DIR . '/inc/helpers.php';

/* -----------------------------------------------------------
 * 5. SITEORIGIN WIDGETS — custom widgets that render PODS data
 *    inside SiteOrigin rows/columns.
 * --------------------------------------------------------- */
function kavisamrat_register_so_widgets() {
	if ( ! class_exists( 'SiteOrigin_Widget' ) ) return; // needs SiteOrigin Widgets Bundle

	require_once KAVISAMRAT_DIR . '/inc/widgets/class-so-book-grid-widget.php';
	require_once KAVISAMRAT_DIR . '/inc/widgets/class-so-book-highlights-widget.php';

	register_widget( 'Kavisamrat_SO_Book_Grid_Widget' );
	register_widget( 'Kavisamrat_SO_Book_Highlights_Widget' );
}
add_action( 'widgets_init', 'kavisamrat_register_so_widgets' );

/* -----------------------------------------------------------
 * 6. Tell SiteOrigin the 'book' CPT can be built with Page Builder
 * --------------------------------------------------------- */
add_filter( 'siteorigin_panels_post_type_support', function ( $support ) {
	$support[] = 'book';
	return $support;
} );

/* -----------------------------------------------------------
 * 7. Register the 'Where to Buy' vendor icons / misc theme options
 * --------------------------------------------------------- */
function kavisamrat_vendor_icon( $vendor_name ) {
	$vendor_name = strtolower( $vendor_name );
	$icons = array(
		'amazon'    => '🛒',
		'pustakam'  => '📚',
		'flipkart'  => '🛍️',
	);
	foreach ( $icons as $key => $icon ) {
		if ( strpos( $vendor_name, $key ) !== false ) return $icon;
	}
	return '🔗';
}

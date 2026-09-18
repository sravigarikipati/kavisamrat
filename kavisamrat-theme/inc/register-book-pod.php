<?php
/**
 * Registers the "book" Pod (Custom Post Type + fields) via the Pods API.
 *
 * This runs once (guarded by an option flag) so editors are free to tweak
 * field labels/order from wp-admin > Pods Admin afterwards without this
 * script overwriting their changes on every page load.
 *
 * To re-run registration after changing this file, delete the
 * `kavisamrat_book_pod_registered` option (or use WP-CLI: wp option delete
 * kavisamrat_book_pod_registered) and reload wp-admin once.
 *
 * Field type slugs are current as of Pods 3.x. If your installed Pods
 * version differs, open Pods Admin > Books > Edit Fields and confirm each
 * field type matches the intent noted in the comments below.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function kavisamrat_register_book_pod() {

	// Only run in wp-admin, only once, and only if Pods is active.
	if ( ! is_admin() || ! function_exists( 'pods_api' ) ) return;
	$registered_version = get_option( 'kavisamrat_book_pod_registered' );
	if ( $registered_version === KAVISAMRAT_VERSION ) return;

	$api = pods_api();
	if ( $registered_version ) {
		$save_character_field = function ( $field ) use ( $api ) {
			$existing = $api->load_field( array(
				'name' => $field['name'],
				'pod'  => 'book',
			) );
			if ( $existing && method_exists( $existing, 'get_args' ) ) {
				$existing_args = $existing->get_args();
				if ( ! empty( $existing_args['id'] ) ) {
					$field['id'] = $existing_args['id'];
				}
			}
			return $api->save_field( $field );
		};
		$save_character_field( array(
			'pod'     => 'book',
			'name'    => 'book_characters',
			'label'   => 'Book Characters',
			'type'    => 'group',
			'options' => array(
				'required'          => 0,
				'repeatable'        => 1,
				'repeatable_format' => 'group',
				'label_group_field' => 'character_name',
			),
		) );
		$save_character_field( array(
			'pod'    => 'book',
			'parent' => 'book/book_characters',
			'name'   => 'character_name',
			'label'  => 'Character Name',
			'type'   => 'text',
		) );
		$save_character_field( array(
			'pod'    => 'book',
			'parent' => 'book/book_characters',
			'name'   => 'character_description',
			'label'  => 'Character Description',
			'type'   => 'paragraph',
		) );
		$save_character_field( array(
			'pod'     => 'book',
			'parent'  => 'book/book_characters',
			'name'    => 'character_image',
			'label'   => 'Character Image',
			'type'    => 'file',
			'options' => array(
				'file_type'        => 'image',
				'file_format_type' => 'single',
				'file_uploader'    => 'media',
			),
		) );
		update_option( 'kavisamrat_book_pod_registered', KAVISAMRAT_VERSION );
		return;
	}

	/* ---------------------------------------------------------
	 * A. THE POD ITSELF — Custom Post Type "book"
	 * --------------------------------------------------------- */
	$pod_id = $api->save_pod( array(
		'name'    => 'book',
		'label'   => 'Books',
		'type'    => 'post_type',
		'storage' => 'meta',
		'options' => array(
			'label_singular'        => 'Book',
			'label_plural'          => 'Books',
			'public'                 => 1,
			'show_ui'                => 1,
			'show_in_menu'           => 1,
			'menu_icon'              => 'dashicons-book-alt',
			'has_archive'            => 1,
			'rewrite'                => 1,
			'rewrite_custom_slug'    => 'books',
			'supports_title'         => 1,
			'supports_editor'        => 1,      // "Description"
			'supports_thumbnail'     => 1,       // Featured Image
			'supports_revisions'     => 1,
			'supports_siteorigin-panels' => 1,   // SiteOrigin Page Builder support
			'publicly_queryable'     => 1,
		),
	) );

	if ( ! $pod_id || is_wp_error( $pod_id ) ) return;

	/* ---------------------------------------------------------
	 * B. CUSTOM FIELDS
	 * --------------------------------------------------------- */

	// 1. Book Title (plain text) — used as card title + single H1
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'book_title',
		'label'   => 'Book Title',
		'type'    => 'text',
		'options' => array( 'required' => 1 ),
	) );

	// 2. Book Thumbnail — single image (vertical cover)
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'book_thumbnail',
		'label'   => 'Book Thumbnail',
		'type'    => 'file',
		'options' => array(
			'file_type'         => 'image',
			'file_format_type'  => 'single',
			'file_uploader'     => 'media',
			'required'          => 1,
		),
	) );

	// 3. Book Images — gallery (multiple)
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'book_images',
		'label'   => 'Book Images',
		'type'    => 'file',
		'options' => array(
			'file_type'        => 'image',
			'file_format_type' => 'multi',
			'file_uploader'    => 'media',
		),
	) );

	// 4. Book Highlights — repeatable group (quote + optional attribution)
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'book_highlights',
		'label'   => 'Book Highlights',
		'type'    => 'group',
		'options' => array(
			'repeatable'       => 1,
			'repeatable_format' => 'group',
			'label_group_field' => 'highlight_text',
		),
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/book_highlights',
		'name'    => 'highlight_text',
		'label'   => 'Highlight / Quote',
		'type'    => 'paragraph',
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/book_highlights',
		'name'    => 'highlight_attribution',
		'label'   => 'Attribution (optional)',
		'type'    => 'text',
	) );

	// 5. Book Description — WYSIWYG
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'book_description',
		'label'   => 'Book Description',
		'type'    => 'wysiwyg',
	) );

	// 6. Where to Buy — repeatable group of vendor_name + vendor_url
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'buy_links',
		'label'   => 'Where to Buy',
		'type'    => 'group',
		'options' => array(
			'repeatable'        => 1,
			'repeatable_format' => 'group',
			'label_group_field' => 'vendor_name',
		),
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/buy_links',
		'name'    => 'vendor_name',
		'label'   => 'Vendor Name',
		'type'    => 'text',
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/buy_links',
		'name'    => 'vendor_url',
		'label'   => 'Vendor URL',
		'type'    => 'website',
	) );

	// 7. Media Links — repeatable plain text / code field (URLs or <iframe> embeds)
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'media_links',
		'label'   => 'Media Links',
		'type'    => 'code',
		'options' => array(
			'repeatable' => 1,
			'code_editor_theme' => 'default',
		),
	) );

	// 8. Book Characters (optional) — repeatable group
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'book_characters',
		'label'   => 'Book Characters',
		'type'    => 'group',
		'options' => array(
			'required'          => 0,
			'repeatable'        => 1,
			'repeatable_format' => 'group',
			'label_group_field' => 'character_name',
		),
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/book_characters',
		'name'    => 'character_name',
		'label'   => 'Character Name',
		'type'    => 'text',
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/book_characters',
		'name'    => 'character_description',
		'label'   => 'Character Description',
		'type'    => 'paragraph',
	) );
	$api->save_field( array(
		'pod'     => 'book',
		'parent'  => 'book/book_characters',
		'name'    => 'character_image',
		'label'   => 'Character Image',
		'type'    => 'file',
		'options' => array(
			'file_type'        => 'image',
			'file_format_type' => 'single',
			'file_uploader'    => 'media',
		),
	) );

	// 9. Publication Year — used for chronological archive ordering/filtering
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'publication_year',
		'label'   => 'Publication Year',
		'type'    => 'number',
		'options' => array( 'number_format_type' => 'integer' ),
	) );

	// 10. Genre — used for archive filters
	$api->save_field( array(
		'pod'     => 'book',
		'name'    => 'genre',
		'label'   => 'Genre',
		'type'    => 'pick',
		'options' => array(
			'pick_object'  => 'custom-simple',
			'pick_custom'  => "Poetry\nDrama\nProse\nTranslation\nCriticism",
		),
	) );

	update_option( 'kavisamrat_book_pod_registered', KAVISAMRAT_VERSION );
}
add_action( 'admin_init', 'kavisamrat_register_book_pod' );

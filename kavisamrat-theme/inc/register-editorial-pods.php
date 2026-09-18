<?php
/**
 * Registers the editorial Authors and Articles Pods plus article taxonomies.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function kavisamrat_register_editorial_taxonomies() {
	register_taxonomy( 'article_category', 'article', array(
		'labels' => array(
			'name'          => 'Article Categories',
			'singular_name' => 'Article Category',
		),
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'hierarchical'      => true,
		'rewrite'           => array( 'slug' => 'article-category' ),
	) );

	register_taxonomy( 'article_tag', 'article', array(
		'labels' => array(
			'name'          => 'Article Tags',
			'singular_name' => 'Article Tag',
		),
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'hierarchical'      => false,
		'rewrite'           => array( 'slug' => 'article-tag' ),
	) );
}
add_action( 'init', 'kavisamrat_register_editorial_taxonomies' );

function kavisamrat_register_editorial_pods() {
	if ( ! is_admin() || ! function_exists( 'pods_api' ) ) return;
	$registered_version = get_option( 'kavisamrat_editorial_pods_registered' );
	if ( $registered_version === '1.0.1' ) return;

	$api = pods_api();
	if ( $registered_version === '1.0.0' ) {
		$profile_field = array(
			'pod'     => 'authors',
			'name'    => 'profile_photo',
			'label'   => 'Profile Photo / Avatar',
			'type'    => 'file',
			'options' => array(
				'file_type'        => 'image',
				'file_format_type' => 'single',
				'file_uploader'    => 'media',
			),
		);
		$existing_profile_field = $api->load_field( array( 'name' => 'profile_photo', 'pod' => 'authors' ) );
		if ( $existing_profile_field && method_exists( $existing_profile_field, 'get_args' ) ) {
			$existing_args = $existing_profile_field->get_args();
			if ( ! empty( $existing_args['id'] ) ) $profile_field['id'] = $existing_args['id'];
		}
		$api->save_field( $profile_field );
		update_option( 'kavisamrat_editorial_pods_registered', '1.0.1' );
		return;
	}
	$author_id = $api->save_pod( array(
		'name'    => 'authors',
		'label'   => 'Authors',
		'type'    => 'post_type',
		'storage' => 'meta',
		'options' => array(
			'label_singular'    => 'Author',
			'label_plural'      => 'Authors',
			'public'            => 1,
			'show_ui'           => 1,
			'show_in_menu'      => 1,
			'menu_icon'         => 'dashicons-admin-users',
			'has_archive'       => 1,
			'rewrite'           => 1,
			'rewrite_custom_slug' => 'authors',
			'supports_title'    => 1,
			'supports_editor'   => 1,
			'supports_thumbnail' => 1,
			'publicly_queryable' => 1,
		),
	) );
	if ( ! $author_id || is_wp_error( $author_id ) ) return;

	$api->save_field( array(
		'pod'     => 'authors',
		'name'    => 'author_bio',
		'label'   => 'Author Bio / Description',
		'type'    => 'wysiwyg',
	) );
	$api->save_field( array(
		'pod'     => 'authors',
		'name'    => 'profile_photo',
		'label'   => 'Profile Photo / Avatar',
		'type'    => 'file',
		'options' => array(
			'file_type'        => 'image',
			'file_format_type' => 'single',
			'file_uploader'    => 'media',
		),
	) );
	$api->save_field( array(
		'pod'     => 'authors',
		'name'    => 'author_designation',
		'label'   => 'Designation / Title',
		'type'    => 'text',
	) );
	$api->save_field( array(
		'pod'     => 'authors',
		'name'    => 'social_links',
		'label'   => 'Social Links / External Web Links',
		'type'    => 'group',
		'options' => array(
			'repeatable'        => 1,
			'repeatable_format' => 'group',
			'label_group_field' => 'social_label',
		),
	) );
	$api->save_field( array(
		'pod'    => 'authors',
		'parent' => 'authors/social_links',
		'name'   => 'social_label',
		'label'  => 'Link Label',
		'type'   => 'text',
	) );
	$api->save_field( array(
		'pod'    => 'authors',
		'parent' => 'authors/social_links',
		'name'   => 'social_url',
		'label'  => 'URL',
		'type'   => 'website',
	) );

	$article_id = $api->save_pod( array(
		'name'    => 'article',
		'label'   => 'Articles',
		'type'    => 'post_type',
		'storage' => 'meta',
		'options' => array(
			'label_singular'     => 'Article',
			'label_plural'       => 'Articles',
			'public'             => 1,
			'show_ui'            => 1,
			'show_in_menu'       => 1,
			'menu_icon'          => 'dashicons-media-document',
			'has_archive'        => 1,
			'rewrite'            => 1,
			'rewrite_custom_slug' => 'articles',
			'supports_title'     => 1,
			'supports_editor'    => 1,
			'supports_thumbnail' => 1,
			'supports_revisions' => 1,
			'publicly_queryable' => 1,
		),
	) );
	if ( ! $article_id || is_wp_error( $article_id ) ) return;

	$api->save_field( array(
		'pod'   => 'article',
		'name'  => 'article_subtitle',
		'label' => 'Subtitle / Dek',
		'type'  => 'text',
	) );
	$api->save_field( array(
		'pod'     => 'article',
		'name'    => 'featured_image_caption',
		'label'   => 'Featured Image Caption / Credit',
		'type'    => 'text',
	) );
	$api->save_field( array(
		'pod'     => 'article',
		'name'    => 'article_author',
		'label'   => 'Author',
		'type'    => 'pick',
		'options' => array(
			'pick_object'     => 'post_type',
			'pick_val'        => 'authors',
			'pick_format_type' => 'single',
			'pick_format'     => 'dropdown',
		),
	) );
	$api->save_field( array(
		'pod'     => 'article',
		'name'    => 'publication_date',
		'label'   => 'Publication / Historical Date',
		'type'    => 'date',
		'options' => array( 'date_format' => 'yy-mm-dd' ),
	) );
	$api->save_field( array(
		'pod'   => 'article',
		'name'  => 'reading_time',
		'label' => 'Estimated Reading Time',
		'type'  => 'text',
	) );
	$api->save_field( array(
		'pod'   => 'article',
		'name'  => 'article_excerpt',
		'label' => 'Excerpt / Summary',
		'type'  => 'paragraph',
	) );
	$api->save_field( array(
		'pod'     => 'article',
		'name'    => 'primary_sources',
		'label'   => 'Primary Sources / Footnotes',
		'type'    => 'group',
		'options' => array(
			'repeatable'        => 1,
			'repeatable_format' => 'group',
			'label_group_field' => 'source_title',
		),
	) );
	$api->save_field( array(
		'pod'    => 'article',
		'parent' => 'article/primary_sources',
		'name'   => 'source_title',
		'label'  => 'Source / Citation Title',
		'type'   => 'text',
	) );
	$api->save_field( array(
		'pod'    => 'article',
		'parent' => 'article/primary_sources',
		'name'   => 'source_text',
		'label'  => 'Citation / Excerpt',
		'type'   => 'wysiwyg',
	) );
	$api->save_field( array(
		'pod'   => 'article',
		'name'  => 'media_embed',
		'label' => 'Audio / Visual Media Embed',
		'type'  => 'website',
	) );

	update_option( 'kavisamrat_editorial_pods_registered', '1.0.1' );
	flush_rewrite_rules( false );
}
add_action( 'admin_init', 'kavisamrat_register_editorial_pods' );

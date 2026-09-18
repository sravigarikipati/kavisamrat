<?php
/**
 * SiteOrigin Widget: Book Grid
 * Drop into any SiteOrigin row/column to pull `book` Pod entries
 * (e.g. the Home Page "Featured Books" section).
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'SiteOrigin_Widget' ) ) return;

class Kavisamrat_SO_Book_Grid_Widget extends SiteOrigin_Widget {

	public function __construct() {
		parent::__construct(
			'kavisamrat-book-grid',
			__( 'Kavisamrat: Book Grid', 'kavisamrat' ),
			array(
				'description' => __( 'Displays Books (Pods) in a responsive, animated grid.', 'kavisamrat' ),
			),
			array(),
			array(
				'title' => array(
					'type'  => 'text',
					'label' => __( 'Section Title', 'kavisamrat' ),
					'default' => 'Major Works',
				),
				'mode' => array(
					'type'    => 'select',
					'label'   => __( 'Which books?', 'kavisamrat' ),
					'default' => 'featured',
					'options' => array(
						'featured' => __( 'Hand-picked (choose below)', 'kavisamrat' ),
						'latest'   => __( 'Latest by publish date', 'kavisamrat' ),
						'chrono'   => __( 'Chronological by publication year', 'kavisamrat' ),
					),
				),
				'book_ids' => array(
					'type'  => 'text',
					'label' => __( 'Book Post IDs (comma-separated, used when mode = Hand-picked)', 'kavisamrat' ),
					'default' => '',
				),
				'count' => array(
					'type'    => 'number',
					'label'   => __( 'Number of books to show', 'kavisamrat' ),
					'default' => 6,
				),
				'columns' => array(
					'type'    => 'select',
					'label'   => __( 'Columns (desktop)', 'kavisamrat' ),
					'default' => '3',
					'options' => array( '2' => '2', '3' => '3', '4' => '4' ),
				),
			)
		);
	}

	public function get_template_name( $instance ) {
		return 'book-grid';
	}

	public function get_template_dir( $instance ) {
		return KAVISAMRAT_DIR . '/template-parts/widgets';
	}

	public function get_less_variables( $instance ) {
		return array();
	}

	protected function get_widget_output( $instance ) {
		$count   = ! empty( $instance['count'] ) ? intval( $instance['count'] ) : 6;
		$mode    = $instance['mode'] ?? 'featured';
		$columns = $instance['columns'] ?? '3';

		$args = array(
			'post_type'      => 'book',
			'posts_per_page' => $count,
		);

		if ( 'featured' === $mode && ! empty( $instance['book_ids'] ) ) {
			$ids = array_map( 'intval', array_filter( array_map( 'trim', explode( ',', $instance['book_ids'] ) ) ) );
			if ( $ids ) {
				$args['post__in'] = $ids;
				$args['orderby']  = 'post__in';
			}
		} elseif ( 'chrono' === $mode ) {
			$args['meta_key'] = 'publication_year';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'ASC';
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}

		$query = new WP_Query( $args );

		return array(
			'title'   => $instance['title'] ?? '',
			'columns' => $columns,
			'query'   => $query,
		);
	}
}

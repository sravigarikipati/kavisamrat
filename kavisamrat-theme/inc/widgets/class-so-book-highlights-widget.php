<?php
/**
 * SiteOrigin Widget: Book Highlights Carousel
 * Renders the `book_highlights` repeater of the current (or specified) book
 * as a swipeable quote carousel — used on the Single Book layout.
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'SiteOrigin_Widget' ) ) return;

class Kavisamrat_SO_Book_Highlights_Widget extends SiteOrigin_Widget {

	public function __construct() {
		parent::__construct(
			'kavisamrat-book-highlights',
			__( 'Kavisamrat: Book Highlights Carousel', 'kavisamrat' ),
			array(
				'description' => __( 'Quote carousel driven by the book_highlights Pods field.', 'kavisamrat' ),
			),
			array(),
			array(
				'title' => array(
					'type'    => 'text',
					'label'   => __( 'Section Title', 'kavisamrat' ),
					'default' => 'Highlights',
				),
				'book_id' => array(
					'type'    => 'text',
					'label'   => __( 'Book Post ID (leave blank to use current post)', 'kavisamrat' ),
					'default' => '',
				),
			)
		);
	}

	protected function get_widget_output( $instance ) {
		$book_id = ! empty( $instance['book_id'] ) ? intval( $instance['book_id'] ) : get_the_ID();
		$pod = kavisamrat_get_book( $book_id );

		ob_start();
		if ( $pod ) {
			if ( ! empty( $instance['title'] ) ) {
				echo '<h3 class="reveal-up">' . esc_html( $instance['title'] ) . '</h3>';
			}
			kavisamrat_render_highlights_carousel( $pod );
		}
		return ob_get_clean();
	}
}

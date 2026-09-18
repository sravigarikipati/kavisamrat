<?php
/**
 * @var array $instance  { title, columns, query (WP_Query) }
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$query   = $instance['query'];
$columns = $instance['columns'] ?? '3';
?>
<div class="kv-section kv-book-grid-widget">
	<div class="kv-container">
		<?php if ( ! empty( $instance['title'] ) ) : ?>
			<h2 class="reveal-up"><?php echo esc_html( $instance['title'] ); ?></h2>
			<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>
		<?php endif; ?>

		<?php if ( $query->have_posts() ) : ?>
			<div class="kv-grid kv-grid--<?php echo esc_attr( $columns ); ?>">
				<?php while ( $query->have_posts() ) : $query->the_post();
					$pod = kavisamrat_get_book( get_the_ID() );
					get_template_part( 'template-parts/book-card', null, array( 'pod' => $pod ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'No books published yet.', 'kavisamrat' ); ?></p>
		<?php endif; ?>
	</div>
</div>

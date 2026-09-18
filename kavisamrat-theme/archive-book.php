<?php
/**
 * Books Archive — Layout reference: victoriaaveyard.com/books
 * Chronological grid, vertical covers, responsive genre filter.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// Query all books in chronological order of publication (falls back to post date).
$paged = max( 1, get_query_var( 'paged' ) );
$args = array(
	'post_type'      => 'book',
	'posts_per_page' => 24,
	'paged'          => $paged,
	'meta_key'       => 'publication_year',
	'orderby'        => 'meta_value_num',
	'order'          => 'ASC',
);
$books = new WP_Query( $args );

// Collect distinct genres present for the filter bar.
$genres = array();
if ( function_exists( 'pods' ) ) {
	foreach ( $books->posts as $p ) {
		$pod = kavisamrat_get_book( $p->ID );
		$g = kavisamrat_field( $pod, 'genre', '' );
		if ( $g && ! in_array( $g, $genres, true ) ) $genres[] = $g;
	}
}
?>

<main id="main">

	<section class="kv-section--inverse kv-section" style="padding-block: var(--space-lg);">
		<div class="kv-container">
			<div class="eyebrow" style="color:var(--color-brass);">The Complete Archive</div>
			<h1 class="reveal-up">Published Works</h1>
			<p class="font-heading-italic" style="color: rgba(249,246,240,.75); max-width: 640px;">
				Forty-two volumes of poetry, drama, and prose, presented in the order they entered the world.
			</p>
		</div>
	</section>

	<section class="kv-section">
		<div class="kv-container">

			<?php if ( ! empty( $genres ) ) : ?>
				<div class="book-filters" role="tablist" aria-label="<?php esc_attr_e( 'Filter by genre', 'kavisamrat' ); ?>">
					<button type="button" class="is-active" data-filter="all">All</button>
					<?php foreach ( $genres as $g ) : ?>
						<button type="button" data-filter="<?php echo esc_attr( sanitize_title( $g ) ); ?>"><?php echo esc_html( $g ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $books->have_posts() ) : ?>
				<div class="kv-grid kv-grid--3" id="book-archive-grid">
					<?php while ( $books->have_posts() ) : $books->the_post();
						$pod = kavisamrat_get_book( get_the_ID() );
						$genre_slug = sanitize_title( kavisamrat_field( $pod, 'genre', '' ) );
						?>
						<div class="book-archive-item" data-genre="<?php echo esc_attr( $genre_slug ); ?>">
							<?php get_template_part( 'template-parts/book-card', null, array( 'pod' => $pod ) ); ?>
						</div>
					<?php endwhile; ?>
				</div>

				<div class="kv-container" style="margin-top: var(--space-lg); text-align:center;">
					<?php
					echo paginate_links( array(
						'total'   => $books->max_num_pages,
						'current' => $paged,
						'prev_text' => '&larr; Previous',
						'next_text' => 'Next &rarr;',
					) );
					?>
				</div>

				<script>
				document.addEventListener('DOMContentLoaded', function () {
					var buttons = document.querySelectorAll('.book-filters button');
					var items = document.querySelectorAll('.book-archive-item');
					buttons.forEach(function (btn) {
						btn.addEventListener('click', function () {
							buttons.forEach(function (b) { b.classList.remove('is-active'); });
							btn.classList.add('is-active');
							var filter = btn.getAttribute('data-filter');
							items.forEach(function (item) {
								var match = filter === 'all' || item.getAttribute('data-genre') === filter;
								item.style.display = match ? '' : 'none';
							});
						});
					});
				});
				</script>

			<?php else : ?>
				<p><?php esc_html_e( 'No books have been published yet. Add entries via Pods Admin \u2192 Books.', 'kavisamrat' ); ?></p>
			<?php endif; ?>

			<?php wp_reset_postdata(); ?>
		</div>
	</section>

</main>

<?php get_footer();

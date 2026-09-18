<?php
/**
 * The main template file — WordPress' required fallback.
 *
 * Used whenever a more specific template (front-page.php, archive-book.php,
 * single-book.php, etc.) doesn't match: blog index, search results, 404s,
 * regular pages/posts, and any other post type without its own template.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main id="main">
	<div class="kv-section">
		<div class="kv-container">

			<?php if ( is_search() ) : ?>
				<div class="eyebrow">Search Results</div>
				<h1 class="reveal-up">
					<?php printf( esc_html__( 'Results for: %s', 'kavisamrat' ), '<span>' . get_search_query() . '</span>' ); ?>
				</h1>
				<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>
			<?php elseif ( is_404() ) : ?>
				<div class="eyebrow">404</div>
				<h1 class="reveal-up"><?php esc_html_e( 'Page Not Found', 'kavisamrat' ); ?></h1>
				<p><?php esc_html_e( "The page you're looking for doesn't exist. It may have been moved, or the address may be mistyped.", 'kavisamrat' ); ?></p>
				<p>
					<a class="kv-btn kv-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return Home', 'kavisamrat' ); ?></a>
					<a class="kv-btn kv-btn--outline-brass" href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>"><?php esc_html_e( 'Browse the Books', 'kavisamrat' ); ?></a>
				</p>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>

				<div class="kv-grid kv-grid--2" style="margin-top: var(--space-md);">
					<?php while ( have_posts() ) : the_post(); ?>
						<article <?php post_class( 'kv-card reveal-up' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'large' ); ?>
								</a>
							<?php endif; ?>

							<h2 class="font-heading" style="font-size: var(--fs-h4); margin-top: var(--space-sm);">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<div class="eyebrow eyebrow--muted"><?php echo esc_html( get_the_date() ); ?></div>

							<div><?php the_excerpt(); ?></div>

							<a class="kv-card__link" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Read More', 'kavisamrat' ); ?> &rarr;
							</a>
						</article>
					<?php endwhile; ?>
				</div>

				<div style="margin-top: var(--space-lg); text-align:center;">
					<?php
					the_posts_pagination( array(
						'prev_text' => '&larr; ' . esc_html__( 'Previous', 'kavisamrat' ),
						'next_text' => esc_html__( 'Next', 'kavisamrat' ) . ' &rarr;',
					) );
					?>
				</div>

			<?php elseif ( ! is_404() ) : ?>
				<p><?php esc_html_e( 'Nothing found.', 'kavisamrat' ); ?></p>
			<?php endif; ?>

		</div>
	</div>
</main>

<?php get_footer();

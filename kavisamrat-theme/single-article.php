<?php
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();
	$article_pod = function_exists( 'pods' ) ? pods( 'article', get_the_ID() ) : null;
	$author_id = kavisamrat_get_article_author_id( $article_pod );
	$subtitle = kavisamrat_field( $article_pod, 'article_subtitle', '' );
	$caption = kavisamrat_field( $article_pod, 'featured_image_caption', '' );
	$reading_time = kavisamrat_field( $article_pod, 'reading_time', '' );
	$publication_date = kavisamrat_field( $article_pod, 'publication_date', '' );
	$media_embed = kavisamrat_field( $article_pod, 'media_embed', '' );
	$display_date = $publication_date ? wp_date( 'd F Y', strtotime( $publication_date ) ) : get_the_date( 'd F Y' );
	$categories = get_the_terms( get_the_ID(), 'article_category' );
	$share_url = rawurlencode( get_permalink() );
	$share_title = rawurlencode( get_the_title() );
	$previous = get_previous_post();
	$next = get_next_post();
	$related_args = array(
		'post_type'      => 'article',
		'posts_per_page' => 3,
		'post__not_in'   => array( get_the_ID() ),
		'no_found_rows'  => true,
	);
	if ( $categories && ! is_wp_error( $categories ) ) {
		$related_args['tax_query'] = array( array(
			'taxonomy' => 'article_category',
			'field'    => 'term_id',
			'terms'    => wp_list_pluck( $categories, 'term_id' ),
		) );
	}
	$related = new WP_Query( $related_args );
	?>
	<main id="main" class="article-single">
		<header class="article-single__header">
			<div class="kv-container article-single__container">
				<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
					<div class="article-single__categories">
						<?php foreach ( $categories as $category ) : ?><a class="article-category-badge" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a><?php endforeach; ?>
					</div>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<?php if ( $subtitle ) : ?><p class="article-single__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
				<div class="article-single__meta">
					<span><?php echo esc_html( $display_date ); ?></span>
					<?php if ( $reading_time ) : ?><span><?php echo esc_html( $reading_time ); ?></span><?php endif; ?>
				</div>
				<div class="article-share" aria-label="Share this article">
					<span class="article-share__label">Share</span>
					<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $share_url ); ?>&text=<?php echo esc_attr( $share_title ); ?>" target="_blank" rel="noopener" aria-label="Share on X">X</a>
					<a href="https://api.whatsapp.com/send?text=<?php echo esc_attr( $share_title . '%20' . $share_url ); ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp">WhatsApp</a>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $share_url ); ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">Facebook</a>
					<button type="button" data-copy-article-link aria-label="Copy article link">Copy link</button>
				</div>
				<?php kavisamrat_render_author_byline( $author_id ); ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="article-single__featured">
				<img src="<?php the_post_thumbnail_url( 'large' ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
				<?php if ( $caption ) : ?><figcaption><?php echo esc_html( $caption ); ?></figcaption><?php endif; ?>
			</figure>
		<?php endif; ?>

		<article class="kv-container article-single__content">
			<?php the_content(); ?>
			<?php if ( $media_embed ) : ?><div class="article-media"><p class="eyebrow">Listen &amp; Watch</p><?php echo wp_oembed_get( esc_url( $media_embed ) ); ?></div><?php endif; ?>
			<?php kavisamrat_render_article_sources( $article_pod ); ?>
		</article>

		<div class="kv-container article-single__footer">
			<nav class="article-pagination" aria-label="Article navigation">
				<?php if ( $previous ) : ?><a href="<?php echo esc_url( get_permalink( $previous ) ); ?>"><span>Previous article</span><strong><?php echo esc_html( get_the_title( $previous ) ); ?></strong></a><?php endif; ?>
				<?php if ( $next ) : ?><a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><span>Next article</span><strong><?php echo esc_html( get_the_title( $next ) ); ?></strong></a><?php endif; ?>
			</nav>
			<?php if ( $related->have_posts() ) : ?>
				<section class="article-related" aria-labelledby="article-related-title">
					<h2 id="article-related-title">Related Reading</h2>
					<div class="kv-grid kv-grid--3">
						<?php while ( $related->have_posts() ) : $related->the_post(); $related_pod = pods( 'article', get_the_ID() ); ?>
							<article class="article-card">
								<a href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?><img src="<?php the_post_thumbnail_url( 'medium_large' ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" /><?php endif; ?>
									<?php $related_categories = get_the_terms( get_the_ID(), 'article_category' ); if ( $related_categories && ! is_wp_error( $related_categories ) ) : ?><span class="article-category-badge"><?php echo esc_html( $related_categories[0]->name ); ?></span><?php endif; ?>
									<h3><?php the_title(); ?></h3>
									<?php $related_time = kavisamrat_field( $related_pod, 'reading_time', '' ); if ( $related_time ) : ?><span class="article-card__reading-time"><?php echo esc_html( $related_time ); ?></span><?php endif; ?>
								</a>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>
		</div>
	</main>
	<script>
		document.addEventListener('click', function (event) {
			if (!event.target.matches('[data-copy-article-link]')) return;
			navigator.clipboard.writeText(window.location.href).then(function () {
				event.target.textContent = 'Copied';
				setTimeout(function () { event.target.textContent = 'Copy link'; }, 1600);
			});
		});
	</script>
<?php endwhile; get_footer();

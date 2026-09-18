<?php
/***
 * This page is intended to be built and re-arranged with SiteOrigin Page
 * Builder using the "Kavisamrat: Book Grid" widget etc. If the front page
 * (Settings > Reading > a static page) already has SiteOrigin Panels data
 * saved, that content is rendered as-is via the_content(). Otherwise this
 * file renders a fully-built default layout matching the brief, so the
 * site looks complete immediately after theme activation.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$front_id = get_option( 'page_on_front' );
$has_panels_layout = $front_id && get_post_meta( $front_id, 'panels_data', true );
?>

<main id="main" class="home-page">

<?php if ( $has_panels_layout && have_posts() ) :
	while ( have_posts() ) : the_post();
		the_content(); // SiteOrigin Page Builder filters this automatically
	endwhile;
else : ?>

	<!-- ============ HERO ============ -->
	<section class="kv-hero kv-hero--editorial">
		<div class="kv-hero__inner">
			<div class="kv-hero__copy">
				<div class="kv-hero__telugu telugu-text">కవిసమ్రాట్ విశ్వనాథ సాహిత్యానుబంధం</div>
				<div class="kv-hero__eyebrow">The Literary Legacy of Kavi Samrat Viswanadha</div>
				<h1 class="kv-hero__title">Viswanadha<br />Sathyanarayana</h1>
				<p class="kv-hero__telugu-title telugu-text">విశ్వనాథ సత్యనారాయణ</p>
				<p class="kv-hero__quote">&ldquo;His language was the river Godavari itself&mdash;ancient, unhurried, carrying within it the sediment of a thousand years of song.&rdquo;</p>
				<div class="kv-hero__actions">
					<a class="kv-btn kv-btn--primary" href="<?php echo esc_url( home_url( '/about' ) ); ?>">View Biography <span aria-hidden="true">&#8599;</span></a>
					<a class="kv-hero__secondary-cta" href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>">Explore Books <span aria-hidden="true">&#8594;</span></a>
				</div>
			</div>
			<div class="kv-hero__artwork-wrap">
				<div class="kv-hero__artwork-glow"></div>
				<img class="kv-hero__artwork" src="<?php echo esc_url( KAVISAMRAT_URI . '/assets/images/vs_hero.png' ); ?>" alt="Viswanadha Sathyanarayana" />
			</div>
		</div>
	</section>

	<!-- ============ FEATURED BOOKS ============ -->
	<section class="kv-section">
		<div class="kv-container">
			<div class="eyebrow">Selected Works</div>
			<div class="front-section-heading">
				<h2 class="reveal-up">Major Literary Works</h2>
				<a class="kv-btn kv-btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>">Explore Books <span aria-hidden="true">&#8599;</span></a>
			</div>
			<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>
		</div>
		<?php
		$featured = new WP_Query( array(
			'post_type'      => 'book',
			'posts_per_page' => 6,
			'meta_key'       => 'publication_year',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
		) );
		if ( $featured->have_posts() ) : ?>
			<div class="kv-container">
				<div class="kv-grid kv-grid--3">
					<?php while ( $featured->have_posts() ) : $featured->the_post();
						$pod = kavisamrat_get_book( get_the_ID() );
						get_template_part( 'template-parts/book-card', null, array( 'pod' => $pod ) );
					endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		<?php else : ?>
			<div class="kv-container"><p><?php esc_html_e( 'Add books via Pods Admin \u2192 Books to populate this grid.', 'kavisamrat' ); ?></p></div>
		<?php endif; ?>
	</section>

	<!-- ============ BIOGRAPHY / LEGACY ============ -->
	<section class="kv-section kv-section--alt">
		<div class="kv-container kv-grid kv-grid--2" style="align-items:center;">
			<div class="reveal-up">
				<div class="eyebrow">Literary Legacy</div>
				<h2>Eight Decades of Unceasing Creation</h2>
				<p>Viswanadha Sathyanarayana&rsquo;s poetic legacy spans over forty works of verse, prose, and drama&mdash;each rooted in classical Sanskrit tradition yet breathing with the vernacular spirit of Telugu. He was the first Telugu writer honoured with the Jnanpith Award, India&rsquo;s highest literary distinction, for his magnum opus <em>Ramayana Kalpavrikshamu</em>.</p>
				<a class="kv-btn kv-btn--secondary" href="<?php echo esc_url( home_url( '/about' ) ); ?>">Read Full Biography</a>
			</div>
			<div class="kv-grid" style="grid-template-columns:1fr 1fr;gap:1rem;">
				<div class="kv-card kv-card--stat is-ivory reveal-up">
					<div class="kv-card__icon">&#128214;</div>
					<h3 style="font-size:1.4rem;">Published Works</h3>
					<p>42 volumes spanning poetry, drama and prose narrative.</p>
				</div>
				<div class="kv-card kv-card--stat is-emerald reveal-up">
					<div class="kv-card__icon">&#128337;</div>
					<h3 style="font-size:1.4rem;color:var(--color-ivory);">1895&ndash;1976</h3>
					<p>Eight decades of unceasing literary creation.</p>
				</div>
				<div class="kv-card kv-card--stat is-ivory reveal-up" style="grid-column: span 2;">
					<div class="kv-card__icon">&#9878;&#65039;</div>
					<h3 style="font-size:1.4rem;">Jnanpith Award</h3>
					<p>India&rsquo;s highest literary honour, awarded 1970 for <em>Ramayana Kalpavrikshamu</em>.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ LATEST POSTS ============ -->
	<section class="kv-section kv-latest-posts">
		<div class="kv-container">
			<div class="eyebrow">@veyipadagalu</div>
			<h2 class="reveal-up">Latest Posts</h2>
			<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>
			<?php $latest_x_posts = kavisamrat_get_latest_x_posts(); ?>
			<?php if ( $latest_x_posts ) : ?>
				<div class="latest-posts-grid">
					<?php foreach ( $latest_x_posts as $post ) : $avatar = $post['avatar'] ?: KAVISAMRAT_URI . '/assets/images/highlight-logo.png'; ?>
						<article class="latest-post-card reveal-up">
							<header class="latest-post-card__header"><img src="<?php echo esc_url( $avatar ); ?>" alt="" /><span><strong>Veyi Padagalu</strong><small>@veyipadagalu</small></span><b aria-hidden="true">X</b></header>
							<div class="latest-post-card__body"><?php echo kavisamrat_linkify_x_text( $post['text'] ); ?></div>
							<?php if ( $post['image'] ) : ?><a class="latest-post-card__media" href="<?php echo esc_url( 'https://x.com/veyipadagalu/status/' . $post['id'] ); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url( $post['image'] ); ?>" alt="" loading="lazy" /></a><?php endif; ?>
							<a class="latest-post-card__time" href="<?php echo esc_url( 'https://x.com/veyipadagalu/status/' . $post['id'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $post['created_at'] ? human_time_diff( strtotime( $post['created_at'] ), current_time( 'timestamp' ) ) . ' ago' : 'View on X' ); ?></a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?><p class="latest-posts-empty">Latest posts are temporarily unavailable.</p><?php endif; ?>
			<a class="kv-btn kv-btn--primary latest-posts-cta" href="https://x.com/veyipadagalu" target="_blank" rel="noopener">Follow @veyipadagalu on X <span aria-hidden="true">&#8599;</span></a>
		</div>
	</section>

	<!-- ============ LATEST VIDEOS ============ -->
	<section class="kv-section">
		<div class="kv-container">
			<div class="eyebrow">Koneti Metlu - విశ్వనాథ సాహిత్య అకాడమి</div>
			<h2 class="reveal-up">Latest Videos</h2>
			<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>
			<?php $latest_videos = kavisamrat_get_latest_videos(); ?>
			<?php if ( $latest_videos ) : ?>
				<div class="latest-videos-grid">
					<?php foreach ( $latest_videos as $video ) : ?>
						<a class="latest-video-card reveal-up" href="<?php echo esc_url( $video['url'] ); ?>" target="_blank" rel="noopener">
							<span class="latest-video-card__thumbnail"><img src="<?php echo esc_url( $video['thumbnail'] ); ?>" alt="<?php echo esc_attr( $video['title'] ); ?>" loading="lazy" /><span class="latest-video-card__play" aria-hidden="true">&#9654;</span></span>
							<span class="latest-video-card__title"><?php echo esc_html( $video['title'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="latest-videos-empty">Latest videos are temporarily unavailable.</p>
			<?php endif; ?>
			<a class="kv-btn kv-btn--primary latest-videos-cta" href="https://youtube.com/channel/UC1xoYyxDA4UD9l7nLCPJL9A" target="_blank" rel="noopener">Watch Full Channel <span aria-hidden="true">&#8599;</span></a>
		</div>
	</section>

<?php endif; ?>

</main>

<?php get_footer();

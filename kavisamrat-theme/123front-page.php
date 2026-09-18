<?php
/**
 * Template Name: Home - Alternate Version
 *
 * A second, standalone homepage design — does NOT replace front-page.php
 * or the primary home-page-layout.json. To use it:
 *   1. Pages → Add New → title it e.g. "Home (Alternate)".
 *   2. In the Page Attributes panel, set Template → "Home - Alternate Version".
 *   3. Publish, then open its URL to review it side-by-side with the real
 *      home page. It only becomes the live homepage if you deliberately
 *      choose it under Settings → Reading.
 *
 * Like the primary templates, this checks for a saved SiteOrigin Page
 * Builder layout first (so it's fully rebuildable visually — see
 * siteorigin-layouts/home-alternate-layout.json for a matching starter
 * import) and falls back to the coded vintage layout below otherwise.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header( 'alternate' );

$has_panels_layout = get_post_meta( get_the_ID(), 'panels_data', true );
$img = KAVISAMRAT_URI . '/assets/images/';
?>

<main id="main">

<?php if ( $has_panels_layout && have_posts() ) :
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
else : ?>

	<!-- ============ HERO ============ -->
	<section class="ha-section ha-hero">
		<div class="kv-container" style="display:contents;">
			<div class="reveal-up">
				<div class="ha-hero__eyebrow">Est. 1895 &middot; Nandamuru, Andhra</div>
				<h1 class="ha-hero__title">Kavisamrat</h1>
				<p class="ha-hero__lede">Step into the world of Viswanadha Sathyanarayana&mdash;where classical Telugu verse still breathes on the page.</p>
				<div class="ha-hero__actions">
					<a class="kv-btn kv-btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>">Enter the Archive</a>
					<a class="kv-btn kv-btn--outline-brass" href="#legacy-alt">His Literary Legacy</a>
				</div>
			</div>
			<figure class="ha-photo-card reveal-up">
				<img src="<?php echo esc_url( $img . 'author-portrait-archival.jpg' ); ?>" alt="Viswanadha Sathyanarayana, seated portrait with signature" loading="lazy" />
				<img class="ha-seal-stamp" src="<?php echo esc_url( $img . 'highlight-logo.png' ); ?>" alt="Kavisamrat seal" loading="lazy" />
				<figcaption>Viswanadha Sathyanarayana, with his signature</figcaption>
			</figure>
		</div>
	</section>

	<div class="ha-torn-edge ha-torn-edge--espresso"></div>

	<!-- ============ STAT RIBBON ============ -->
	<section class="ha-section ha-section--espresso" style="padding-block: var(--space-lg);">
		<div class="ha-stat-ribbon">
			<div class="ha-stat-ribbon__item reveal-up">
				<div class="ha-stat-ribbon__number">1st</div>
				<div class="ha-stat-ribbon__label">Telugu Jnanpith Award Winner, 1970</div>
			</div>
			<div class="ha-stat-ribbon__item reveal-up">
				<div class="ha-stat-ribbon__number">60+</div>
				<div class="ha-stat-ribbon__label">Epic Novels &amp; Masterpieces</div>
			</div>
			<div class="ha-stat-ribbon__item reveal-up">
				<div class="ha-stat-ribbon__number" style="font-size: clamp(1.1rem, 2.5vw, 1.6rem);">Padma Bhushan</div>
				<div class="ha-stat-ribbon__label">Govt. of India, 1971</div>
			</div>
			<div class="ha-stat-ribbon__item reveal-up">
				<div class="ha-stat-ribbon__number">100+</div>
				<div class="ha-stat-ribbon__label">Works Across Poetry, Drama &amp; Essays</div>
			</div>
		</div>
	</section>

	<div class="ha-torn-edge"></div>

	<!-- ============ MAGNUM OPUSES ============ -->
	<section id="legacy-alt" class="ha-section ha-section--parchment">
		<div class="kv-container">
			<div class="eyebrow">Manuscripts &amp; Masterworks</div>
			<h2 class="reveal-up">Magnum Opuses &amp; Legendary Works</h2>
			<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>

			<div class="ha-manuscript-grid">
				<div class="ha-manuscript-card reveal-up">
					<div class="ha-manuscript-card__meta">Epic Poetry &middot; 1970</div>
					<h3>Ramayana Kalpavrikshamu</h3>
					<p>Epic poetry rendering of Valmiki&rsquo;s Ramayana imbued with deep Telugu nativity and spiritual depth, awarded the Jnanpith Award.</p>
				</div>
				<div class="ha-manuscript-card reveal-up">
					<div class="ha-manuscript-card__meta">Novel</div>
					<h3>Veyi Padagalu <em style="font-size:.7em;color:var(--color-text-muted);">(The Thousand Hoods)</em></h3>
					<p>A monumental epic novel analyzing societal evolution, culture, and human consciousness&mdash;translated into Hindi (<em>Sahasraphan</em>) by former PM P. V. Narasimha Rao.</p>
				</div>
				<div class="ha-manuscript-card reveal-up">
					<div class="ha-manuscript-card__meta">Poetry</div>
					<h3>Kinnerasani Patalu</h3>
					<p>Legendary poetic mermaid songs showcasing sublime classical lyrical beauty.</p>
				</div>
			</div>
		</div>
	</section>

	<div class="ha-torn-edge ha-torn-edge--espresso"></div>

	<!-- ============ BIOGRAPHY ============ -->
	<section class="ha-section ha-section--espresso">
		<div class="kv-container ha-bio">
			<figure class="ha-photo-card reveal-up" style="transform: rotate(2deg);">
				<img src="<?php echo esc_url( $img . 'author-portrait-sketch.png' ); ?>" alt="Illustrated portrait of Viswanadha Sathyanarayana" loading="lazy" />
				<figcaption>Commemorative portrait study</figcaption>
			</figure>
			<div class="reveal-up">
				<div class="eyebrow" style="color:var(--color-brass);">Cultural Heritage</div>
				<h2>Preserving the Cultural Soul of Telugu Land</h2>
				<div class="ha-bio__body">
					<p>Born in 1895 in Nandamuru, Andhra Pradesh, Viswanadha Satyanarayana mastered classical Sanskrit and Telugu, crafting works that seamlessly blended modern narrative techniques with classical meters.</p>
					<blockquote>&ldquo;His language was the river Godavari itself&mdash;ancient, unhurried, carrying within it the sediment of a thousand years of song.&rdquo;</blockquote>
				</div>
			</div>
		</div>
	</section>

	<div class="ha-torn-edge"></div>

	<!-- ============ MEDIA & ARCHIVAL EXCERPTS ============ -->
	<section class="ha-section ha-section--parchment">
		<div class="kv-container">
			<div class="eyebrow">From the Archive</div>
			<h2 class="reveal-up">Media &amp; Archival Excerpts</h2>
			<div class="kv-divider"><span class="kv-divider__mark">&#10022;</span></div>
			<div class="ha-media-reel">
				<div class="ha-media-reel__item reveal-up">
					<p class="eyebrow">Audio Recitation</p>
					<p>Placeholder &mdash; add a rare audio recitation via the Book Pod&rsquo;s Media Links field, or paste an &lt;iframe&gt; embed code here.</p>
				</div>
				<div class="ha-media-reel__item reveal-up">
					<p class="eyebrow">Documentary / Interview</p>
					<p>Placeholder &mdash; paste a YouTube URL or &lt;iframe&gt; embed code here once footage is available.</p>
				</div>
			</div>
		</div>
	</section>

<?php endif; ?>

</main>

<?php get_footer( 'alternate' );

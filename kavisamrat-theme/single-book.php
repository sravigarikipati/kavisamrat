<?php
/**
 * Single Book — Layout references:
 *   jamesclear.com/atomic-habits (hero, buy buttons, quotes, media)
 *   sethring.com/book-series/battle-mage-farmer-series (clean metadata)
 *
 * If this post has a saved SiteOrigin Panels layout, that is rendered via
 * the_content() instead (editors can fully rebuild this page visually).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

	$pod = kavisamrat_get_book( get_the_ID() );
	$has_panels_layout = get_post_meta( get_the_ID(), 'panels_data', true );

	if ( $has_panels_layout ) :
		the_content();
		continue;
	endif;

	$title       = kavisamrat_field( $pod, 'book_title', get_the_title() );
	$year        = kavisamrat_field( $pod, 'publication_year', '' );
	$genre       = kavisamrat_field( $pod, 'genre', '' );
	$description = kavisamrat_field( $pod, 'book_description', '' );
	$thumb       = $pod ? $pod->field( 'book_thumbnail' ) : null;
	$thumb_url   = is_array( $thumb ) ? ( $thumb['guid'] ?? '' ) : ( has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '' );
	?>

	<main id="main">

		<!-- ============ TOP HERO AREA ============ -->
		<section class="kv-section">
			<div class="kv-container book-hero">
				<div class="book-hero__cover reveal-up">
					<?php if ( $thumb_url ) : ?>
						<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
					<?php endif; ?>
				</div>
				<div class="reveal-up">
					<?php if ( $genre || $year ) : ?><div class="eyebrow"><?php echo $genre ? esc_html( $genre ) : ''; ?><?php echo ( $genre && $year ) ? ' &middot; ' : ''; ?><?php if ( $year ) : ?><span class="book-hero__publication-year">Publication Year: <?php echo esc_html( $year ); ?></span><?php endif; ?></div><?php endif; ?>
					<h1><?php echo esc_html( $title ); ?></h1>
					<?php
					$body = get_the_content();
					if ( $body ) : ?>
						<div class="book-hero__body font-heading-italic" style="font-size: var(--fs-h4);"><?php echo apply_filters( 'the_content', $body ); ?></div>
					<?php endif; ?>
					<?php kavisamrat_render_buy_links( $pod ); ?>
				</div>
			</div>
		</section>

		<!-- ============ BOOK HIGHLIGHTS ============ -->
		<section class="kv-section kv-section--inverse">
			<div class="kv-container">
				<div class="eyebrow book-section-label" style="text-align:center;">In His Own Words - <span class="telugu-text">అమృత వాక్యాలు</span></div>
				<h2 class="reveal-up" style="text-align:center;color:var(--color-ivory);">Highlights</h2>
				<?php kavisamrat_render_highlights_carousel( $pod ); ?>
			</div>
		</section>

		<!-- ============ MAIN DESCRIPTION ============ -->
		<?php if ( $description ) : ?>
		<section class="kv-section">
			<div class="kv-container" style="max-width: 780px;">
				<h2 class="reveal-up">About This Work</h2>
				<div class="reveal-up book-description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- ============ CHARACTERS ============ -->
		<?php if ( $pod && ( $pod->field( 'book_characters' ) || $pod->field( 'character_name' ) ) ) : ?>
		<section class="kv-section kv-section--alt">
			<div class="kv-container">
				<div class="eyebrow book-section-label"><span class="telugu-text">Character Introductions - పాత్ర పరిచయం</span></div>
				<h2 class="reveal-up">Key Characters</h2>
				<?php kavisamrat_render_characters( $pod ); ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- ============ MEDIA & INTERVIEWS ============ -->
		<?php if ( $pod && $pod->field( 'media_links' ) ) : ?>
		<section class="kv-section">
			<div class="kv-container">
				<div class="eyebrow book-section-label">Watch &amp; Listen - <span class="telugu-text">వీడియో మరియు ఆడియో</span></div>
				<h2 class="reveal-up">Media &amp; Interviews</h2>
				<?php kavisamrat_render_media_links( $pod ); ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- ============ GALLERY ============ -->
		<?php if ( $pod && $pod->field( 'book_images' ) ) : ?>
		<section class="kv-section kv-section--alt">
			<div class="kv-container">
				<div class="eyebrow book-section-label">Gallery</div>
				<h2 class="reveal-up">Additional Images</h2>
				<?php kavisamrat_render_gallery( $pod ); ?>
			</div>
		</section>
		<?php endif; ?>

	</main>

<?php endwhile;

get_footer();

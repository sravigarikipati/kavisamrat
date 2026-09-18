<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<main id="main"><section class="kv-section"><div class="kv-container">
	<div class="eyebrow book-section-label">Contributors</div><h1>Authors</h1>
	<div class="kv-grid kv-grid--3">
	<?php while ( have_posts() ) : the_post(); $pod = pods( 'authors', get_the_ID() ); ?>
		<article class="author-card"><a href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) : ?><img src="<?php the_post_thumbnail_url( 'medium' ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" /><?php endif; ?><h2><?php the_title(); ?></h2><p><?php echo esc_html( kavisamrat_field( $pod, 'author_designation', '' ) ); ?></p></a></article>
	<?php endwhile; ?>
	</div>
</div></section></main>
<?php get_footer();
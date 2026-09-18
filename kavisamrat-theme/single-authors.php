<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	$pod = pods( 'authors', get_the_ID() );
	$bio = kavisamrat_field( $pod, 'author_bio', '' );
	$designation = kavisamrat_field( $pod, 'author_designation', '' );
	$articles = new WP_Query( array( 'post_type' => 'article', 'posts_per_page' => 12, 'meta_key' => 'article_author', 'meta_value' => get_the_ID() ) );
	?>
	<main id="main"><section class="kv-section author-profile"><div class="kv-container author-profile__container">
		<?php if ( has_post_thumbnail() ) : ?><img class="author-profile__image" src="<?php the_post_thumbnail_url( 'medium' ); ?>" alt="<?php the_title_attribute(); ?>" /><?php endif; ?>
		<div class="eyebrow book-section-label">Author Profile</div><h1><?php the_title(); ?></h1>
		<?php if ( $designation ) : ?><p class="author-profile__designation"><?php echo esc_html( $designation ); ?></p><?php endif; ?>
		<?php if ( $bio ) : ?><div class="author-profile__bio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></div><?php endif; ?>
		<h2>Articles by <?php the_title(); ?></h2>
		<div class="kv-grid kv-grid--3"><?php while ( $articles->have_posts() ) : $articles->the_post(); ?><article class="article-card"><div class="eyebrow">Article</div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p></article><?php endwhile; wp_reset_postdata(); ?></div>
	</div></section></main>
<?php endwhile; get_footer();
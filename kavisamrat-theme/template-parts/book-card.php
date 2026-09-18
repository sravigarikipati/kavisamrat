<?php
/**
 * Book card — used on archive-book.php and the SiteOrigin Book Grid widget.
 * @var array $args { pod: Pods|null }
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$pod = $args['pod'] ?? kavisamrat_get_book();
if ( ! $pod ) return;

$title   = kavisamrat_field( $pod, 'book_title', get_the_title() );
$year    = kavisamrat_field( $pod, 'publication_year', '' );
$excerpt = wp_trim_words( get_the_excerpt() ?: kavisamrat_field( $pod, 'book_description', '' ), 18 );
$thumb   = $pod->field( 'book_thumbnail' );
$thumb_url = is_array( $thumb ) ? ( $thumb['guid'] ?? ( $thumb['sizes']['medium_large'] ?? '' ) ) : ( has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '' );
?>
<a class="book-card reveal-up" href="<?php the_permalink(); ?>">
	<div class="book-card__cover-wrap">
		<?php if ( $thumb_url ) : ?>
			<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
		<?php endif; ?>
	</div>
	<div class="book-card__meta">
		<?php if ( $year ) : ?><div class="book-card__year"><?php echo esc_html( $year ); ?></div><?php endif; ?>
		<h3 class="book-card__title"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $excerpt ) : ?><p class="book-card__excerpt"><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
	</div>
</a>

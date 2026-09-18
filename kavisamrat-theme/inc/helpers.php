<?php
/**
 * Small template helpers so front-end files stay readable.
 * Wrap every Pods read defensively — editors may leave fields empty.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get a Pods object for the current (or given) book post.
 *
 * @param int|null $post_id
 * @return Pods|null
 */
function kavisamrat_get_book( $post_id = null ) {
	if ( ! function_exists( 'pods' ) ) return null;
	$post_id = $post_id ?: get_the_ID();
	$pod = pods( 'book', $post_id );
	return ( $pod && $pod->exists() ) ? $pod : null;
}

/**
 * Safe field getter with a fallback.
 */
function kavisamrat_field( $pod, $name, $default = '' ) {
	if ( ! $pod ) return $default;
	$value = $pod->field( $name );
	if ( is_array( $value ) ) {
		$value = array_values( array_filter( $value, function ( $item ) {
			return is_scalar( $item ) && $item !== '';
		} ) );
		$value = $value ? reset( $value ) : $default;
	}
	return ( $value === null || $value === false || $value === '' ) ? $default : $value;
}

function kavisamrat_get_article_author_id( $pod ) {
	if ( ! $pod ) return 0;
	$value = $pod->field( 'article_author' );
	if ( is_object( $value ) && isset( $value->ID ) ) {
		$value = $value->ID;
	}
	if ( is_array( $value ) ) {
		if ( isset( $value['ID'] ) ) {
			$value = $value['ID'];
		} elseif ( isset( $value[0] ) && is_object( $value[0] ) && isset( $value[0]->ID ) ) {
			$value = $value[0]->ID;
		} else {
		$value = $value['ID'] ?? ( $value[0] ?? 0 );
		}
	}
	return absint( $value );
}

function kavisamrat_get_latest_videos() {
	$cached = get_transient( 'kavisamrat_latest_videos' );
	if ( false !== $cached ) return is_array( $cached ) ? $cached : array();
	$feed = wp_remote_get( 'https://www.youtube.com/feeds/videos.xml?channel_id=UC1xoYyxDA4UD9l7nLCPJL9A', array(
		'timeout' => 8,
		'headers' => array( 'Accept' => 'application/atom+xml' ),
	) );
	$videos = array();
	if ( ! is_wp_error( $feed ) && 200 === wp_remote_retrieve_response_code( $feed ) ) {
		$xml = simplexml_load_string( wp_remote_retrieve_body( $feed ) );
		if ( $xml ) {
			$namespaces = $xml->getNamespaces( true );
			foreach ( $xml->entry as $entry ) {
				$entry_yt = $entry->children( $namespaces['yt'] );
				$video_id = (string) ( $entry_yt->videoId ?? '' );
				$title = trim( (string) $entry->title );
				if ( $video_id && $title ) {
					$videos[] = array(
						'id'        => $video_id,
						'title'     => $title,
						'url'       => 'https://www.youtube.com/watch?v=' . rawurlencode( $video_id ),
						'thumbnail' => 'https://i.ytimg.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg',
					);
				}
				if ( count( $videos ) === 3 ) break;
			}
		}
	}
	set_transient( 'kavisamrat_latest_videos', $videos, 12 * HOUR_IN_SECONDS );
	return $videos;
}

function kavisamrat_linkify_x_text( $text ) {
	$text = esc_html( $text );
	$text = preg_replace( '~(https?://[^\s<]+)~', '<a href="$1" target="_blank" rel="noopener">$1</a>', $text );
	$text = preg_replace( '/(^|\s)(#[\p{L}\d_]+)/u', '$1<a href="https://x.com/hashtag/$2" target="_blank" rel="noopener">$2</a>', $text );
	return nl2br( $text );
}

function kavisamrat_get_latest_x_posts() {
	$token = defined( 'KAVISAMRAT_X_BEARER_TOKEN' ) ? KAVISAMRAT_X_BEARER_TOKEN : getenv( 'KAVISAMRAT_X_BEARER_TOKEN' );
	$cache_key = 'kavisamrat_latest_x_posts_' . ( $token ? 'api' : 'rss' );
	$cached = get_transient( $cache_key );
	if ( false !== $cached ) return is_array( $cached ) ? $cached : array();
	$posts = array();
	if ( $token ) {
		$user_response = wp_remote_get( 'https://api.twitter.com/2/users/by/username/veyipadagalu?user.fields=name,username,profile_image_url', array(
			'timeout' => 8,
			'headers' => array( 'Authorization' => 'Bearer ' . trim( $token ) ),
		) );
		$user_data = ! is_wp_error( $user_response ) ? json_decode( wp_remote_retrieve_body( $user_response ), true ) : array();
		$user_id = $user_data['data']['id'] ?? '';
		if ( $user_id ) {
			$url = 'https://api.twitter.com/2/users/' . rawurlencode( $user_id ) . '/tweets?max_results=5&tweet.fields=created_at,attachments,entities&expansions=attachments.media_keys&media.fields=preview_image_url,type,url';
			$response = wp_remote_get( $url, array( 'timeout' => 8, 'headers' => array( 'Authorization' => 'Bearer ' . trim( $token ) ) ) );
			$data = ! is_wp_error( $response ) ? json_decode( wp_remote_retrieve_body( $response ), true ) : array();
			$media = array();
			foreach ( $data['includes']['media'] ?? array() as $item ) $media[ $item['media_key'] ] = $item['url'] ?? ( $item['preview_image_url'] ?? '' );
			foreach ( array_slice( $data['data'] ?? array(), 0, 3 ) as $tweet ) {
				$image = '';
				if ( ! empty( $tweet['attachments']['media_keys'][0] ) ) $image = $media[ $tweet['attachments']['media_keys'][0] ] ?? '';
				$posts[] = array( 'id' => $tweet['id'], 'text' => $tweet['text'], 'created_at' => $tweet['created_at'] ?? '', 'image' => $image, 'avatar' => $user_data['data']['profile_image_url'] ?? '' );
			}
		}
	}
	if ( ! $posts ) {
		foreach ( array( 'https://nitter.poast.org/veyipadagalu/rss', 'https://nitter.privacydev.net/veyipadagalu/rss' ) as $feed_url ) {
			$feed = wp_remote_get( $feed_url, array( 'timeout' => 6, 'headers' => array( 'Accept' => 'application/rss+xml' ) ) );
			if ( is_wp_error( $feed ) || 200 !== wp_remote_retrieve_response_code( $feed ) ) continue;
			$xml = simplexml_load_string( wp_remote_retrieve_body( $feed ) );
			if ( ! $xml ) continue;
			foreach ( array_slice( (array) $xml->channel->item, 0, 3 ) as $item ) {
				$item = (array) $item;
				$link = (string) ( $item['link'] ?? '' );
				$id = preg_match( '~status/(\d+)~', $link, $match ) ? $match[1] : '';
				if ( $id && ! empty( $item['title'] ) ) $posts[] = array( 'id' => $id, 'text' => wp_strip_all_tags( html_entity_decode( $item['description'] ?? $item['title'] ) ), 'created_at' => $item['pubDate'] ?? '', 'image' => '', 'avatar' => '' );
			}
			if ( $posts ) break;
		}
	}
	if ( ! $posts ) {
		$profile = wp_remote_get( 'https://x.com/veyipadagalu', array(
			'timeout' => 8,
			'headers' => array( 'User-Agent' => 'Mozilla/5.0' ),
		) );
		$markup = ! is_wp_error( $profile ) && 200 === wp_remote_retrieve_response_code( $profile ) ? wp_remote_retrieve_body( $profile ) : '';
		if ( $markup && preg_match_all( '/tweet-(\d+)/', $markup, $matches ) ) {
			$ids = array_values( array_unique( $matches[1] ) );
			preg_match_all( '/full_text:"((?:\\\\.|[^"])*)"/s', $markup, $text_matches );
			preg_match_all( '/created_at_ms:(\d+)/', $markup, $date_matches );
			preg_match_all( '/media_url_https:"([^"]+)"/', $markup, $image_matches );
			foreach ( array_slice( $ids, 0, 3 ) as $index => $id ) {
				if ( empty( $text_matches[1][ $index ] ) ) continue;
				$posts[] = array(
					'id'         => $id,
					'text'       => stripcslashes( $text_matches[1][ $index ] ),
					'created_at' => ! empty( $date_matches[1][ $index ] ) ? gmdate( 'c', (int) ( $date_matches[1][ $index ] / 1000 ) ) : '',
					'image'      => $image_matches[1][ $index ] ?? '',
					'avatar'     => '',
				);
			}
		}
	}
	usort( $posts, function ( $first, $second ) {
		$first_time = ! empty( $first['created_at'] ) ? strtotime( $first['created_at'] ) : 0;
		$second_time = ! empty( $second['created_at'] ) ? strtotime( $second['created_at'] ) : 0;
		if ( $first_time === $second_time ) return strcmp( (string) ( $second['id'] ?? '' ), (string) ( $first['id'] ?? '' ) );
		return $second_time <=> $first_time;
	} );
	set_transient( $cache_key, array_slice( $posts, 0, 3 ), 9 * HOUR_IN_SECONDS );
	return array_slice( $posts, 0, 3 );
}

function kavisamrat_render_article_sources( $pod ) {
		if ( ! $pod ) return;
		$sources = $pod->field( 'primary_sources' );
		if ( empty( $sources ) || ! is_array( $sources ) ) return;
		if ( isset( $sources['source_title'] ) ) $sources = array( $sources );
		$items = array();
		foreach ( $sources as $source ) {
			if ( ! is_array( $source ) ) continue;
			$title = $source['source_title'] ?? '';
			$text  = $source['source_text'] ?? '';
			if ( ! $title && ! $text ) continue;
			$items[] = array( 'title' => $title, 'text' => $text );
		}
		if ( ! $items ) return;
		?>
		<section class="article-sources" aria-labelledby="article-sources-title">
			<h2 id="article-sources-title">Primary Sources &amp; Notes</h2>
			<?php foreach ( $items as $item ) : ?>
				<div class="article-source">
					<?php if ( $item['title'] ) : ?><h3><?php echo esc_html( $item['title'] ); ?></h3><?php endif; ?>
					<?php if ( $item['text'] ) : ?><div><?php echo wp_kses_post( wpautop( $item['text'] ) ); ?></div><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</section>
		<?php
	}

function kavisamrat_render_author_byline( $author_id ) {
	$author_id = absint( $author_id );
	if ( ! $author_id ) return;
	$author_pod = function_exists( 'pods' ) ? pods( 'authors', $author_id ) : null;
	$author = get_post( $author_id );
	if ( ! $author || 'authors' !== $author->post_type ) return;
	$name = get_the_title( $author_id );
	$designation = kavisamrat_field( $author_pod, 'author_designation', '' );
	$bio = kavisamrat_field( $author_pod, 'author_bio', '' );
	$image = $author_pod ? $author_pod->field( 'profile_photo' ) : '';
	$image = is_array( $image ) ? ( $image['guid'] ?? ( $image['url'] ?? '' ) ) : $image;
	$image = $image ?: get_the_post_thumbnail_url( $author_id, 'medium' );
	?>
	<aside class="article-author-card">
		<div>
			<div class="eyebrow">By Author</div>
			<div class="article-author-card__identity">
				<?php if ( $image ) : ?><img class="article-author-card__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" /><?php endif; ?>
				<h3><a href="<?php echo esc_url( get_permalink( $author_id ) ); ?>"><?php echo esc_html( $name ); ?></a></h3>
			</div>
			<?php if ( $designation ) : ?><p class="article-author-card__designation"><?php echo esc_html( $designation ); ?></p><?php endif; ?>
			<?php if ( $bio ) : ?><div class="article-author-card__bio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></div><?php endif; ?>
		</div>
	</aside>
	<?php
}

/**
 * Render the "Where to Buy" buttons for a book.
 */
function kavisamrat_render_buy_links( $pod ) {
	if ( ! $pod ) return;
	$links = $pod->field( 'buy_links' );
	$has_grouped_links = is_array( $links ) && isset( $links[0] ) && is_array( $links[0] );
	if ( ! $has_grouped_links ) {
		$names = $pod->field( 'vendor_name' );
		$urls  = $pod->field( 'vendor_url' );
		$names = is_array( $names ) ? $names : ( $names ? array( $names ) : array() );
		$urls  = is_array( $urls ) ? $urls : ( $urls ? array( $urls ) : array() );
		$links = array();
		foreach ( $names as $index => $name ) {
			$links[] = array(
				'vendor_name' => $name,
				'vendor_url'  => $urls[ $index ] ?? '',
			);
		}
	}
	if ( empty( $links ) ) return;

	echo '<div class="book-hero__buy">';
	foreach ( $links as $link ) {
		$raw_name = $link['vendor_name'] ?? '';
		$url      = esc_url( $link['vendor_url'] ?? '' );
		if ( ! $raw_name || ! $url ) continue;
		printf(
			'<a class="kv-btn kv-btn--primary" href="%1$s" target="_blank" rel="noopener">%2$s</a>',
			$url,
			esc_html( $raw_name )
		);
	}
	echo '</div>';
}

/**
 * Render the Book Highlights quote carousel.
 */
function kavisamrat_render_highlights_carousel( $pod ) {
	if ( ! $pod ) return;
	$highlights = $pod->field( 'book_highlights' );
	if ( is_object( $highlights ) && method_exists( $highlights, 'to_array' ) ) {
		$highlights = $highlights->to_array();
	}
	if ( is_string( $highlights ) ) {
		$decoded = json_decode( $highlights, true );
		$highlights = is_array( $decoded ) ? $decoded : array( $highlights );
	}
	if ( empty( $highlights ) || ! is_array( $highlights ) ) return;
	if ( isset( $highlights['highlight_text'] ) ) {
		$highlights = array( $highlights );
	}
	$slides = array();
	foreach ( $highlights as $highlight ) {
		if ( is_string( $highlight ) && $highlight !== '' ) {
			$slides[] = array( 'highlight_text' => $highlight );
		} elseif ( is_array( $highlight ) && ! empty( $highlight['highlight_text'] ) ) {
			$slides[] = $highlight;
		}
	}
	if ( empty( $slides ) ) return;
	$logo_url = get_site_icon_url( 80 );
	foreach ( array( 'highlight-logo.png', 'highlight-logo.jpg', 'highlight-logo.webp' ) as $logo_file ) {
		if ( file_exists( KAVISAMRAT_DIR . '/assets/images/' . $logo_file ) ) {
			$logo_url = KAVISAMRAT_URI . '/assets/images/' . $logo_file;
			break;
		}
	}
	?>
	<div class="kv-carousel" data-kv-carousel data-kv-carousel-arrows>
		<div class="kv-carousel__track">
			<?php foreach ( $slides as $h ) :
				$text = $h['highlight_text'];
				$attribution = $h['highlight_attribution'] ?? '';
			?>
				<div class="kv-carousel__slide">
					<?php if ( $logo_url ) : ?>
						<img class="kv-carousel__logo" src="<?php echo esc_url( $logo_url ); ?>" width="40" height="40" alt="" aria-hidden="true" />
					<?php endif; ?>
					<p class="kv-carousel__quote">&ldquo;<?php echo esc_html( $text ); ?>&rdquo;</p>
					<?php if ( $attribution ) : ?>
						<p class="kv-carousel__attribution"><?php echo esc_html( $attribution ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<button class="kv-carousel__arrow kv-carousel__arrow--prev" type="button" aria-label="Previous highlight">&larr;</button>
		<button class="kv-carousel__arrow kv-carousel__arrow--next" type="button" aria-label="Next highlight">&rarr;</button>
		<div class="kv-carousel__nav"></div>
	</div>
	<?php
}

/**
 * Render Media Links (raw <iframe> embeds or bare URLs → oEmbed).
 */
function kavisamrat_render_media_links( $pod ) {
	if ( ! $pod ) return;
	$media = $pod->field( 'media_links' );
	if ( empty( $media ) ) return;
	if ( ! is_array( $media ) ) $media = array( $media );

	echo '<div class="kv-grid kv-grid--2">';
	foreach ( $media as $entry ) {
		if ( empty( $entry ) ) continue;
		echo '<div class="book-media-embed reveal-up">';
		if ( strpos( $entry, '<iframe' ) !== false ) {
			// Trusted content entered by an editor in wp-admin — allow the iframe through.
			echo wp_kses( $entry, array(
				'iframe' => array(
					'src' => true, 'width' => true, 'height' => true, 'frameborder' => true,
					'allow' => true, 'allowfullscreen' => true, 'title' => true,
				),
			) );
		} else {
			echo wp_oembed_get( esc_url( $entry ) );
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Render Book Gallery (book_images) as a carousel.
 */
function kavisamrat_render_gallery( $pod ) {
	if ( ! $pod ) return;
	$images = $pod->field( 'book_images' );
	if ( empty( $images ) ) return;
	if ( isset( $images['ID'] ) ) $images = array( $images ); // single-image edge case
	$slides = array();
	foreach ( $images as $img ) {
		if ( ! is_array( $img ) ) continue;
		$full = $img['guid'] ?? ( $img['file'] ?? '' );
		$thumb = $img['sizes']['medium'] ?? $full;
		if ( ! $full ) continue;
		$slides[] = array(
			'full'  => $full,
			'thumb' => $thumb,
			'alt'   => $img['post_title'] ?? '',
		);
	}
	if ( empty( $slides ) ) return;

	echo '<div class="book-gallery kv-carousel" data-kv-carousel data-kv-carousel-label="gallery image">';
	echo '<div class="kv-carousel__track">';
	foreach ( $slides as $slide ) {
		printf(
			'<div class="kv-carousel__slide book-gallery__slide"><a href="%1$s" data-lightbox="book-gallery"><img src="%2$s" alt="%3$s" loading="lazy" /></a></div>',
			esc_url( $slide['full'] ),
			esc_url( $slide['thumb'] ),
			esc_attr( $slide['alt'] )
		);
	}
	echo '</div><div class="kv-carousel__nav"></div></div>';
}

/**
 * Render Book Characters grid.
 */
function kavisamrat_render_characters( $pod ) {
	if ( ! $pod ) return;
	$characters = $pod->field( 'book_characters' );
	if ( empty( $characters ) || ! is_array( $characters ) ) {
		$names = $pod->field( 'character_name' );
		$descriptions = $pod->field( 'character_description' );
		$images = $pod->field( 'character_image' );
		$names = is_array( $names ) ? $names : ( $names ? array( $names ) : array() );
		$descriptions = is_array( $descriptions ) ? $descriptions : ( $descriptions ? array( $descriptions ) : array() );
		$images = is_array( $images ) ? $images : ( $images ? array( $images ) : array() );
		$characters = array();
		foreach ( $names as $index => $name ) {
			$characters[] = array(
				'character_name'        => $name,
				'character_description' => $descriptions[ $index ] ?? '',
				'character_image'       => $images[ $index ] ?? '',
			);
		}
	}
	if ( empty( $characters ) ) return;
	?>
	<div class="book-characters">
		<?php foreach ( $characters as $c ) :
			$name = $c['character_name'] ?? '';
			$desc = $c['character_description'] ?? '';
				$image = $c['character_image'] ?? '';
			if ( ! $name ) continue;
				$image_url = is_array( $image ) ? ( $image['guid'] ?? ( $image['url'] ?? '' ) ) : $image;
		?>
			<div class="book-character reveal-up">
					<?php if ( $image_url ) : ?>
						<img class="book-character__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" />
					<?php endif; ?>
				<h4 class="book-character__name"><?php echo esc_html( $name ); ?></h4>
				<?php if ( $desc ) : ?>
					<div class="book-character__description"><?php echo wp_kses_post( wpautop( $desc ) ); ?></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="kv-site-header">
	<div class="kv-container kv-site-header__inner">
		<a class="kv-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="kv-logo__title"><?php bloginfo( 'name' ); ?></span>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<span class="kv-logo__tagline"><?php bloginfo( 'description' ); ?></span>
			<?php endif; ?>
		</a>
		<nav class="kv-nav" aria-label="<?php esc_attr_e( 'Primary', 'kavisamrat' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => function () {
					echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
					echo '<li><a href="' . esc_url( get_post_type_archive_link( 'book' ) ) . '">Books</a></li>';
					 echo '<li><a href="' . esc_url( get_post_type_archive_link( 'article' ) ) . '">Articles</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/about' ) ) . '">Biography</a></li></ul>';
				},
			) );
			?>
		</nav>
	</div>
</header>

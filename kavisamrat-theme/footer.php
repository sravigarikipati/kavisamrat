<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<footer class="kv-site-footer">
	<div class="kv-container">
		<div class="kv-grid kv-grid--3">
			<div>
				<div class="kv-logo" style="margin-bottom: .5rem;"><?php bloginfo( 'name' ); ?></div>
				<p class="font-heading-italic" style="color: rgba(249,246,240,.7);">
					<?php esc_html_e( '“Kavi Samrat” — Emperor of Poets. Jnanpith Award, 1970.', 'kavisamrat' ); ?>
				</p>
			</div>
			<div>
				<div class="eyebrow eyebrow--muted" style="color:var(--color-brass);">Explore</div>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => function () {
						echo '<ul style="list-style:none;padding:0;margin:.5rem 0 0;">';
						echo '<li style="margin-bottom:.4rem;"><a href="' . esc_url( get_post_type_archive_link( 'book' ) ) . '">Books</a></li>';
						echo '<li style="margin-bottom:.4rem;"><a href="' . esc_url( get_post_type_archive_link( 'article' ) ) . '">Articles</a></li>';
						echo '<li style="margin-bottom:.4rem;"><a href="' . esc_url( home_url( '/about' ) ) . '">Biography</a></li>';
						echo '</ul>';
					},
				) );
				?>
			</div>
			<div>
				<div class="eyebrow eyebrow--muted" style="color:var(--color-brass);">&copy; <?php echo esc_html( date( 'Y' ) ); ?></div>
				<p style="color: rgba(249,246,240,.7);"><?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All literary works remain the property of their respective rights holders.', 'kavisamrat' ); ?></p>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

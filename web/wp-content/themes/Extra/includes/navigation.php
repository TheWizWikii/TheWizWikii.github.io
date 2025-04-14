<nav class="post-nav">
	<div class="nav-links clearfix">
		<div class="nav-link nav-link-prev">
			<?php next_posts_link( et_get_safe_localization( __( '<span class="button">Older Entries</span>', 'extra' ) ) ); ?>
		</div>
		<div class="nav-link nav-link-next">
			<?php previous_posts_link( et_get_safe_localization( __( '<span class="button">Next Entries</span>', 'extra' ) ) ); ?>
		</div>
	</div>
</nav>
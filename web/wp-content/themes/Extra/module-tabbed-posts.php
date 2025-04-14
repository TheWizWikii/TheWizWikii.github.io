<?php $id_attr = '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : ''; ?>
<div <?php echo $id_attr ?> class="module tabbed-post-module et_pb_extra_module <?php echo esc_attr( $module_class ); ?>" style="border-top-color:<?php echo esc_attr( $border_top_color ); ?>">
	<div class="tabs clearfix">
		<ul>
			<?php
			foreach ( $terms as $tab_id => $term ) {
				$no_term_color_tab_nav_class = is_customize_preview() && $term['color'] === extra_global_accent_color() ? 'no-term-color-tab' : '';
			?>
			<li id="category-tab-<?php echo esc_attr( $tab_id ); ?>" class="et-accent-color-parent-term <?php echo esc_attr( $no_term_color_tab_nav_class ); ?>" data-tab-id="<?php echo esc_attr( $tab_id ); ?>" data-term-color="<?php echo esc_attr( $term['color'] ); ?>" ripple="" ripple-inverse="">
				<span>
					<?php echo esc_html( $term['name'] ); ?>
				</span>
			</li>
			<?php } ?>
		</ul>
		<div class="tab-nav">
			<span class="prev arrow" title="<?php esc_attr_e( 'Previous Tab', 'extra' ); ?>"></span>
			<span class="next arrow" title="<?php esc_attr_e( 'Next Tab', 'extra' ); ?>"></span>
		</div>
	</div>

	<div class="tab-contents">
		<?php echo $content; ?>
	</div>
</div>

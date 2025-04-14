<?php $id_attr = '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : ''; ?>
<div <?php echo $id_attr ?> class="module-etads module <?php echo esc_attr( $border_class ); ?> <?php echo esc_attr( $module_class ); ?> et_pb_extra_module" style="<?php echo esc_attr( $border_style ); ?>">
	<?php if ( !empty( $header_text ) ) { ?>
	<div class="module-head">
		<h1 style="color:<?php echo esc_attr( $header_text_color ); ?>;"><?php echo esc_html( $header_text ); ?></h1>
	</div>
	<?php } ?>
	<div class="module-body">
		<?php foreach ( $ads as $ad ) { ?>
		<?php $new_line = !empty( $ad['new_line'] ) ? ' new_line' : ''; ?>
		<div class="etad<?php echo esc_attr( $new_line ); ?> <?php echo esc_attr( $ad['module_class'] ); ?>">
			<?php if ( !empty( $ad['img_url'] ) && !empty( $ad['link_url'] ) ) { ?>
				<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank">
					<img src="<?php echo esc_url( $ad['img_url'] ); ?>" alt="<?php echo esc_attr( $ad['img_alt_text'] ); ?>" />
				</a>
			<?php } else if ( !empty( $ad['ad_html'] ) ) { ?>
				<?php echo $ad['ad_html']; ?>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>

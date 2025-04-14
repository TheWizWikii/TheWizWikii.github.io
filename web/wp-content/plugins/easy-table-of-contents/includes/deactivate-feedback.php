<?php

/**
 * Deactivate Feedback Template
 * @since 2.0.27
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
$email = '';
if( $current_user instanceof WP_User ) {
	$email = trim( $current_user->user_email );	
}

$reasons = array(
    		1 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="temporary"/>' . esc_html__('It is only temporary', 'easy-table-of-contents') . '</label></li>',
		2 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="stopped showing toc"/>' . esc_html__('I stopped showing TOC on my site', 'easy-table-of-contents') . '</label></li>',
		3 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="missing feature"/>' . esc_html__('I miss a feature', 'easy-table-of-contents') . '</label></li>
		<li><input type="text" name="eztoc_disable_text[]" value="" placeholder="'. esc_attr__( 'Please describe the feature', 'easy-table-of-contents' ) .'"/></li>',
		4 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="technical issue"/>' . esc_html__('Technical Issue', 'easy-table-of-contents') . '</label></li>
		<li><textarea name="eztoc_disable_text[]" placeholder="' . esc_html__('Can we help? Please describe your problem', 'easy-table-of-contents') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="other plugin"/>' . esc_html__('I switched to another plugin', 'easy-table-of-contents') .  '</label></li>
		<li><input type="text" name="eztoc_disable_text[]" value="" placeholder="'. esc_attr__( 'Name of the plugin', 'easy-table-of-contents' ). '"/></li>',
		6 => '<li><label><input type="radio" name="eztoc_disable_reason" required value="other"/>' . esc_html__('Other reason', 'easy-table-of-contents') . '</label></li>
		<li><textarea name="eztoc_disable_text[]" placeholder="' . esc_attr__('Please specify, if possible', 'easy-table-of-contents') . '"></textarea></li>',
    );
shuffle($reasons);
?>


<div id="eztoc-reloaded-feedback-overlay" style="display: none;">
    <div id="eztoc-reloaded-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php esc_html_e('If you have a moment, please let us know why you are deactivating:', 'easy-table-of-contents'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason_escaped){
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : Already escaped
                    echo $reason_escaped;
                }
                ?>
	    </ul>
	    <?php if( null !== $email && !empty( $email ) ) : ?>
    	    <input type="hidden" name="eztoc_disable_from" value="<?php echo esc_attr($email); ?>" />
	    <?php endif; ?>
	    <input id="eztoc-reloaded-feedback-submit" class="button button-primary" type="submit" name="eztoc_disable_submit" value="<?php esc_html_e('Submit & Deactivate', 'easy-table-of-contents'); ?>"/>
	    <a class="button eztoc-feedback-only-deactivate"><?php esc_html_e('Only Deactivate', 'easy-table-of-contents'); ?></a>
	    <a class="eztoc-feedback-not-deactivate" href="#"><?php esc_html_e('Don\'t deactivate', 'easy-table-of-contents'); ?></a>
	</form>
    </div>
</div>
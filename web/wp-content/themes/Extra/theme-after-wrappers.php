<?php
/**
 * Template partial used to add content to the page in Theme Builder.
 * Duplicates partial content from footer.php in order to maintain
 * backwards compatibility with child themes.
 */

?>
<?php if ( ! et_builder_is_product_tour_enabled() ) : ?>
    </div> <!-- #page-container -->
<?php endif; ?>

<?php if ( 'on' == et_get_option( 'extra_back_to_top' ) ) { ?>
    <span title="<?php esc_attr_e( 'Back To Top', 'extra' ); ?>" id="back_to_top"></span>
<?php } ?>

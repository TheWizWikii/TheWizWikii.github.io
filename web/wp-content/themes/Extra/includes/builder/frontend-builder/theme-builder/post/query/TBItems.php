<?php
/**
 * Implement ET_Builder_Post_Query_TBItems to query `et_tb_item`.
 *
 * @since 4.18.0
 *
 * @package Builder
 */

/**
 * Class to handle `et_tb_item` query.
 *
 * Think of this class as WP_Query for ET_TB_ITEM_POST_TYPE.
 */
class ET_Builder_Post_Query_TBItems extends ET_Core_Post_Query {
	/**
	 * {@inheritDoc}
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @param array  $terms    Taxonomy terms.
	 * @param bool   $negate   Whether to negate this tax query.
	 */
	protected function _add_tax_query( $taxonomy, $terms, $negate = null ) {
		$args   = self::$_->array_flatten( $terms );
		$negate = $this->_reset_negate();

		if ( ! $args ) {
			return $this;
		}

		parent::_add_tax_query( $taxonomy, $args, $negate );

		return $this;
	}
}

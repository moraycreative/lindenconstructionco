<?php
/**
 * Sortable portfolio filter header
 *
 * @package wpv
 * @subpackage construction
 */
?>
<nav class="sort_by_cat grid-1-1" data-for="#<?php echo esc_attr( $main_id ) ?>">
	<span class="inner-wrapper">
		<span class="cat"><a data-value="all" href="#" class="active"><?php _e( 'All', 'construction' )?></a></span>
		<?php
			// show the categories present in this listing
			$terms = array();
			if (!empty($cat) && $cat != 'null') {
				foreach($cat as $term_slug) {
					$term = get_term_by( 'slug', $term_slug, 'portfolio_category' );
					if($term)
						$terms[] = $term;
				}
			} else {
				$terms = get_terms( 'portfolio_category', 'hide_empty=1' );
			}
		?>
		<?php foreach($terms as $term): ?>
			<span class="cat"><a data-value="<?php echo esc_attr( preg_replace( '/[\pZ\pC]+/u', '-', $term->slug ) ) ?>" href="#"><span data-text="<?php echo esc_attr( $term->name ) ?>"><?php echo $term->name // xss ok?></span></a></span>
		<?php endforeach ?>
	</span>
</nav>
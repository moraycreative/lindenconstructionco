<?php

function wpv_post_meta_default( $meta, $global, $post_id = null ) {
	if ( is_null( $post_id ) ) {
		$post_id = wpv_get_the_ID();
	}

	$global = wpv_sanitize_bool( wpv_get_option( $global ) );
	$local = wpv_sanitize_bool( wpv_post_meta( $post_id, $meta, true ) );
	$local_raw = wpv_post_meta( $post_id, $meta );

	if ( $local === 'default' || empty( $local_raw ) ) {
		return $global;
	}

	return $local;
}

function wpv_get_the_ID() {
	global $post;

	return (wpv_has_woocommerce() && is_woocommerce() && !is_singular( array('page', 'product') )) ? wc_get_page_id( 'shop' ) : (isset($post) ? $post->ID : null);
}

/**
 * Wrapper around get_post_meta which takes special pages into account
 *
 * @uses get_post_meta()
 *
 * @param  int    $post_id Post ID.
 * @param  string $key     Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param  bool   $single  Whether to return a single value.
 * @return mixed           Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function wpv_post_meta($post_id, $meta='', $single=false) {
	$real_id = wpv_get_the_ID();

	if ($real_id && $post_id != $real_id)
		$post_id = $real_id;

	return get_post_meta( $post_id, $meta, $single );
}

/**
 * helper function - returns second argument when the first is empty, otherwise returns the first
 *
 */
function wpv_default($value, $default) {
	if (empty($value))
		return $default;
	return $value;
}

/*
 * gets the width in px of the central column depending on current post settings
 */

if ( !function_exists( 'wpv_get_central_column_width' ) ) :
function wpv_get_central_column_width() {
	global $post, $content_width;

	if ( defined( 'WPV_LAYOUT' ) ) {
		$layout_type = WPV_LAYOUT;
	} else if ( is_single() ){
		$layout_type = get_post_meta( $post->ID, 'layout-type', 'left-only' );
	} else {
		$layout_type = 'full';
	}

	$central_width = $content_width;
	$left_sidebar = (float)wpv_get_option( 'left-sidebar-width' );
	$right_sidebar = (float)wpv_get_option( 'right-sidebar-width' );
	switch ( $layout_type ) {
		case 'left-only':
		case 'left-sidebar':
			$central_width = floor( (100-$left_sidebar)/100*$central_width );
		break;

		case 'right-only':
		case 'right-sidebar':
			$central_width = floor( (100-$right_sidebar)/100*$central_width );
		break;

		case 'left-right':
		case 'two-sidebars':
			$central_width = floor( (100-$left_sidebar-$right_sidebar)/100*$central_width );
		break;
	}

	$column = array(1,1);

	if ( isset($GLOBALS['wpv_column_stack']) && is_array( $GLOBALS['wpv_column_stack'] ) ) {
		foreach ( $GLOBALS['wpv_column_stack'] as $c ) {
			$c = explode( '/', $c );
			$column[0] *= $c[0];
			$column[1] *= $c[1];
		}
	}

	$column = $column[0]/$column[1];

	return round( $central_width * $column );
}
endif;

// turns a string as four_fifths to a value in pixels, works only for the central column
if ( !function_exists( 'wpv_str_to_width' ) ) :
function wpv_str_to_width($frac = 'full') {
	$width = wpv_get_central_column_width();
	if ( $frac != 'full' ) {
		$frac = explode( '_', $frac );
		$map = array(
			'one' => 1,
			'two' => 2,
			'half' => 2,
			'three' => 3,
			'third' => 3,
			'thirds' => 3,
			'four' => 4,
			'fourth' => 4,
			'fourths' => 4,
			'five' => 5,
			'fifth' => 5,
			'fifths' => 5,
			'six' => 6,
			'sixth' => 6,
			'sixths' => 6,
		);

		$frac[0] = $map[$frac[0]];
		$frac[1] = $map[$frac[1]];

		$width = ($width - ($frac[1]-1)*20)/$frac[1]*$frac[0] + ($frac[0]-1)*20;
	}

	return $width;
}
endif;

// lazy load images
if ( !function_exists( 'wpv_lazy_load' ) ) :
function wpv_lazy_load($url, $alt='', $atts = array()) {
	echo wpv_get_lazy_load( $url, $alt, $atts ); // xss ok
}

function wpv_get_lazy_load($url, $alt='', $atts = array()) {
	$disabled = true; //wpv_get_option('disable-lazy-load');
	$atts['class'] = isset($atts['class']) ? explode( ' ', $atts['class'] ) : array();

	if ( !$disabled ) {
		$atts['class'][] = 'lazy';

		if (isset($atts['height']) && (int)$atts['height'] < 40 &&
		   isset($atts['width']) && (int)$atts['width'] < 40)
			$atts['class'][] = 'no-animation';
	}

	if ( isset($atts['height']) && empty($atts['height']) ) {
		unset($atts['height']);
	}

	if ( isset($atts['width']) && empty($atts['width']) ) {
		unset($atts['width']);
	}

	$atts['class'] = implode( ' ', $atts['class'] );

	$extended_atts = array();
	foreach ( $atts as $att=>$val ) {
		$extended_atts[] = $att . '="' . esc_attr( $val ) .'"';
	}
	$atts = implode( ' ', $extended_atts );

	ob_start();
?>
	<img src="<?php echo esc_url( $url ) ?>" alt="<?php echo esc_attr( $alt ) ?>" <?php echo $atts // xss ok?> />
<?php
	$clean = ob_get_clean();

	ob_start();
?>
	<img src="<?php echo esc_url( trailingslashit( WPV_IMAGES ) ) ?>blank.gif" alt="<?php echo esc_attr( $alt ) ?>" data-href="<?php echo esc_url( $url ) ?>" <?php echo $atts // xss ok?> />
	<noscript><?php echo $clean // xss ok ?></noscript>
<?php
	$lazy = ob_get_clean();
	return $disabled ? $clean : $lazy;
}
endif;

function wpv_get_portfolio_options() {
	global $post;

	$res = array();

	$res['image'] = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'full', true );
	$res['type'] = wpv_default( get_post_meta( get_the_id(), 'portfolio_type', true ), 'image' );

	$res['width'] = '';
	$res['height'] = '';
	$res['iframe'] = '';
	$res['link_target'] = '_self';

	// calculate some options depending on the portfolio item's type
	switch ( $res['type'] ) {
		case 'image':
		case 'html':
			$res['href'] =  $res['image'][0];
		break;

		case 'video':
			$res['href'] = get_post_meta( get_the_id(), 'wpv-portfolio-format-video', true );

			if ( empty( $res['href'] ) ) {
				$res['href'] = $res['image'][0];
			}
		break;

		case 'link':
			$res['href'] = get_post_meta( get_the_ID(), 'wpv-portfolio-format-link', true );

			$res['link_target'] = get_post_meta( get_the_ID(), '_link_target', true );
			$res['link_target'] = $res['link_target'] ? $res['link_target'] : '_self';
		break;

		case 'gallery':
			list($res['gallery'], ) = WpvPostFormats::get_first_gallery( get_the_content(), null, WpvPostFormats::get_thumb_name( array('p' => $post) ) );
		break;

		case 'document':
			$res['href'] = is_single() ? $res['image'][0] : get_permalink();
		break;
	}

	return $res;
}

function wpv_custom_js() {
	$custom_js = wpv_get_option( 'custom_js' );

	if ( ! empty( $custom_js ) ) :
?>
	<script><?php echo $custom_js; // xss ok?></script>
<?php
	endif;
}
add_action( 'wp_footer', 'wpv_custom_js', 10000 );

function wpv_sub_shortcode($name, $content, &$params, &$sub_contents) {
	if ( !preg_match_all( "/\[$name\b(?P<params>.*?)(?:\/)?\](?:(?P<contents>.*?)\[\/$name\])?/s", $content, $matches ) ) {
		return false;
	}

	$params = array();
	$sub_contents = $matches['contents'];

	// this is from wp-includes/formatting.php
	/* translators: opening curly double quote */
	$opening_quote = _x( '&#8220;', 'opening curly double quote', 'default' );
	/* translators: closing curly double quote */
	$closing_quote = _x( '&#8221;', 'closing curly double quote', 'default' );
	/* translators: double prime, for example in 9" (nine inches) */
	$double_prime = _x( '&#8243;', 'double prime', 'default' );

	foreach ( $matches['params'] as $param_str ) {
		$param_str = str_replace( array( $opening_quote, $closing_quote, $double_prime, '&#8220;', '&#8221;' ), '"', $param_str );
		$params[]  = shortcode_parse_atts( $param_str );
	}

	return true;
}

/**
 * @see http://wordpress.stackexchange.com/a/7094/8344
 */
function wpv_get_attachment_id( $url ) {
	$dir = wp_upload_dir();
	$dir = trailingslashit( $dir['baseurl'] );

	if ( false === strpos( $url, $dir ) )
		return false;

	$file = basename( $url );

	$query = array(
		'post_type' => 'attachment',
		'fields' => 'ids',
		'meta_query' => array(
			array(
				'value' => $file,
				'compare' => 'LIKE',
			)
		)
	);

	$query['meta_query'][0]['key'] = '_wp_attached_file';
	$ids = get_posts( $query );

	foreach ( $ids as $id ) {
		$attachment = wp_get_attachment_image_src( $id, 'full' );
		if ( $url == array_shift( $attachment ) )
			return $id;
	}

	$query['meta_query'][0]['key'] = '_wp_attachment_metadata';
	$ids = get_posts( $query );

	foreach ( $ids as $id ) {

		$meta = wp_get_attachment_metadata( $id );

		if ( isset($meta['sizes']) && is_array( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size => $values ) {
				if ( $values['file'] == $file && $url == array_shift( wp_get_attachment_image_src( $id, $size ) ) ) {
					return $id;
				}
			}
		}
	}

	return false;
}

function wpv_get_attachment_file( $src ) {
	$attachment_id = wpv_get_attachment_id($src);
	$upload_dir    = wp_upload_dir();

	if ( $attachment_id !== false && wp_attachment_is_image( $attachment_id ) ) {
		$file = get_attached_file( $attachment_id );

		$file = preg_replace( '/^('. preg_quote( $upload_dir['basedir'] . '/', '/' ) .')?/', $upload_dir['basedir'].'/', $file );

		return $file;
	}

	return str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $src );
}

function wpv_url_to_image( $src, $size = 'full', $attr = '' ) {
	$attachment_id = wpv_get_attachment_id($src);

	if ( $attachment_id !== false && wp_attachment_is_image( $attachment_id ) ) {
		echo wp_get_attachment_image( $attachment_id, $size, $attr ); // xss ok
	} else {
		// fallback, typically used on fresly imported demo content

		echo '<img src="' . esc_url( $src ) . '" alt="" />';
	}
}

function wpv_prepare_url($url) {
	while ( preg_match( '#/[-\w]+/\.\./#', $url ) ) {
		$url = preg_replace( '#/[-\w]+/\.\./#', '/', $url );
	}

	return $url;
}

function wpv_sanitize_portfolio_item_type($type) {
	if ($type == 'gallery' || $type == 'video' || $type == 'image')
		return $type;

	return 'image';
}
add_filter( 'wpv_fancy_portfolio_item_type', 'wpv_sanitize_portfolio_item_type' );

function wpv_fix_shortcodes($content) {
	// array of custom shortcodes requiring the fix
	$block = join( "|", apply_filters( 'wpv_escaped_shortcodes', include(WPV_THEME_METABOXES . 'shortcode.php') ) );

	// opening tag
	$rep = preg_replace( "/(<p>\s*)?\[($block)(\s[^\]]+)?\](\s*<\/p>|<br \/>)?/","[$2$3]", $content );

	// closing tag
	$rep = preg_replace( "/(?:<\/p>\n*)?(?:<p>\s*)?\[\/($block)](?:\s*<\/p>|<br \/>)?/","[/$1]", $rep );

	return $rep;
}
add_filter( 'the_content', 'wpv_fix_shortcodes' );

function wpv_get_portfolio_terms() {
	$terms = get_the_terms( get_the_id(), 'portfolio_category' );
	$terms_slug = $terms_name = array();
	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			$terms_slug[] = preg_replace( '/[\pZ\pC]+/u', '-', $term->slug );
			$terms_name[] = $term->name;
		}
	}

	return array($terms_slug, $terms_name);
}

function vamtam_recursive_preg_replace($regex, $replace, $subject) {
	if(is_array($subject) || is_object($subject)) {
		foreach($subject as &$sub) {
			$sub = vamtam_recursive_preg_replace($regex, $replace, $sub);
		}
		unset($sub);
	}
	if(is_string($subject)) {
		$subject = preg_replace($regex, $replace, $subject);
	}
	return $subject;
}
<?php

/**
 * Blog shortcode options
 *
 * @package wpv
 * @subpackage editor
 */


return array(
	'name' => 'Blog',
	'desc' => __( 'Please note that this element shows already created blog posts. To create one go to the Posts tab in the WordPress main navigation menu on the left - add new. You do not have to go to Settings - Reading to set the blog listing page.' , 'construction' ),
	'icon' => array(
		'char' => WPV_Editor::get_icon( 'blog' ),
		'size' => '30px',
		'lheight' => '45px',
		'family' => 'vamtam-editor-icomoon',
	),
	'value' => 'blog',
	'controls' => 'size name clone edit delete',
	'options' => array(

		array(
			'name' => __( 'Layout', 'construction' ),
			'desc' => __('Big images - this is the standard layout in one column. <br/>
				Small images, Small Images - Scrollable, Small images - Masonry - the posts in these layouts come in boxes with image on top and text below. They come in 2,3,4 columns.', 'construction') ,
			'id' => 'layout',
			'type' => 'select',
			'default' => 'normal',
			'options' => array(
				'normal' => __( 'Big Images', 'construction' ),
				'small' => __( 'Small Images - Normal', 'construction' ),
				'scroll-x' => __( 'Small Images - Scrollable', 'construction' ),
				'masonry' => __( 'Small Images - Masonry', 'construction' ),
			),
			'field_filter' => 'fbs',
		),
		array(
			'name' => __( 'Columns', 'construction' ) ,
			'desc' => __( 'Number of posts to show per row.', 'construction' ) ,
			'id' => 'column',
			'default' => 2,
			'min' => 1,
			'max' => 4,
			'type' => 'range',
			'class' => 'fbs fbs-small fbs-scroll-x fbs-masonry',
		) ,
		array(
			'name' => __( 'Limit', 'construction' ) ,
			'desc' => __( 'Number of posts to show per page.', 'construction' ) ,
			'id' => 'count',
			'default' => 3,
			'min' => 1,
			'max' => 50,
			'type' => 'range',
		) ,

		array(
			'name' => __( 'Display Post Content', 'construction' ) ,
			'id' => 'show_content',
			'desc' => __('Big Images Layout: If the option is on, it will display the content of the post, otherwise it will display the excerpt.<br>
				Small Images - Normal, Scrollable, Masonry: If the option is on, the post excerpt will be shown, otherwise no content will be shown.', 'construction') ,
			'default' => false,
			'type' => 'toggle',
		) ,
		array(
			'name' => __( 'Nopaging', 'construction' ) ,
			'id' => 'nopaging',
			'desc' => __( 'If the option is on, it will disable pagination. You can set the type of pagination in General Settings - Posts - Pagination Type. ', 'construction' ) ,
			'default' => true,
			'type' => 'toggle',
			'class' => 'fbs fbs-normal fbs-small fbs-masonry',
		) ,
		array(
			'name' => __( 'Category (optional)', 'construction' ) ,
			'desc' => __( 'All categories will be shown if none are selected. Please note that if you do not see categories, there are none created most probably. You can use ctr + click to select multiple categories', 'construction' ) ,
			'id' => 'cat',
			'default' => array() ,
			'target' => 'cat',
			'type' => 'multiselect',
			'layout' => 'checkbox',
		) ,
		array(
			'name' => __( 'Posts (optional)', 'construction' ) ,
			'desc' => __( 'All posts will be shown if none are selected. If you select any posts here, this option will override the category option above. You can use ctr + click to select multiple posts.', 'construction' ) ,
			'id' => 'posts',
			'default' => array() ,
			'target' => 'post',
			'type' => 'multiselect',
		) ,


		array(
			'name' => __( 'Title (optional)', 'construction' ) ,
			'desc' => __( 'The title is placed just above the element.', 'construction' ),
			'id' => 'column_title',
			'default' => '',
			'type' => 'text'
		) ,
		array(
			'name' => __( 'Title Type (optional)', 'construction' ) ,
			'id' => 'column_title_type',
			'default' => 'single',
			'type' => 'select',
			'options' => array(
				'single' => __( 'Title with divider next to it', 'construction' ),
				'double' => __( 'Title with divider below', 'construction' ),
				'no-divider' => __( 'No Divider', 'construction' ),
			),
		) ,
		array(
			'name'    => __( 'Element Animation (optional)', 'construction' ) ,
			'id'      => 'column_animation',
			'default' => 'none',
			'type'    => 'select',
			'options' => array(
				'none'        => __( 'No animation', 'construction' ),
				'from-left'   => __( 'Appear from left', 'construction' ),
				'from-right'  => __( 'Appear from right', 'construction' ),
				'from-top'    => __( 'Appear from top', 'construction' ),
				'from-bottom' => __( 'Appear from bottom', 'construction' ),
				'fade-in'     => __( 'Fade in', 'construction' ),
				'zoom-in'     => __( 'Zoom in', 'construction' ),
			),
		) ,
	) ,
);




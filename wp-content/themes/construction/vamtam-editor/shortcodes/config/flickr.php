<?php
return array(
	'name' => __( 'Flickr', 'construction' ) ,
	'desc' => __( 'This element is usefull if you have a Flickr account. Use <a href="http://idgettr.com/" target="_blank">idGettr</a> if you don\'t know your ID.<br/><br/>.' , 'construction' ),
	'icon' => array(
		'char' => WPV_Editor::get_icon( 'flickr' ),
		'size' => '30px',
		'lheight' => '45px',
		'family' => 'vamtam-editor-icomoon',
	),
	'value' => 'flickr',
	'controls' => 'size name clone edit delete',
	'options' => array(

		array(
			'name' => __( 'Flickr ID', 'construction' ),
			'desc' => __( 'Use <a href="http://idgettr.com/" target="_blank">idGettr</a> if you don\'t know your ID.<br/><br/>', 'construction' ),
			'id' => 'id',
			'default' => '',
			'type' => 'text'
		),
		
		array(
			'name' => __( 'Type', 'construction' ),
			'id' => 'type',
			'default' => 'page',
			'options' => array(
				'user' => __( 'User', 'construction' ),
				'group' => __( 'Group', 'construction' ),
			),
			'type' => 'select',
		),
		
		array(
			'name' => __( 'Count', 'construction' ),
			'desc' => '',
			'id' => 'count',
			'default' => 4,
			'min' => 0,
			'max' => 20,
			'type' => 'range'
		),
		array(
			'name' => __( 'Display', 'construction' ),
			'id' => 'display',
			'default' => 'latest',
			'options' => array(
				'latest' => __( 'Latest', 'construction' ),
				'random' => __( 'Random', 'construction' ),
			),
			'type' => 'select',
		),
		
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
	

	) ,
);

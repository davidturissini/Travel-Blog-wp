<?php
  add_action( 'init', 'create_post_type' );
  function create_post_type() {
	register_post_type( 'journal_entry',
		array(
			'labels' => array(
				'name' => __( 'Journal Entries' ),
				'singular_name' => __( 'Journal Entry' )
			),
		'public' => true,
		'has_archive' => true,
		)
	);

    register_post_type( 'location',
                array(
                        'labels' => array(
                                'name' => __( 'Locations' ),
                                'singular_name' => __( 'Location' )
                        ),
                'public' => true,
                'has_archive' => false,
                'hierarchical' => true,
	        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
                )
        );
  }
?>

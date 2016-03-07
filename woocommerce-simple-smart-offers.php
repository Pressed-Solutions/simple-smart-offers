<?php
/*
 * Plugin Name: Simple Smart Offers
 * Version: 1.0.0
 * Description: Simple Smart Offers upsells every product except what is currently in the cart
 * Author: Andrew Minion/Pressed Solutions
 * Author URI: http://pressedsolutions.com
 * Plugin URI: https://github.com/Pressed-Solutions/simple-smart-offers
 * Text Domain: simple-smart-offers
 * Domain Path: /languages
 * License: GPL2
 * GitHub Plugin URI: https://github.com/Pressed-Solutions/simple-smart-offers
 */

/* prevent this file from being accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register Custom Post Type
function custom_smart_offers() {

	$labels = array(
		'name'                  => 'Simple Smart Offers',
		'singular_name'         => 'Simple Smart Offer',
		'menu_name'             => 'Simple Smart Offer',
		'name_admin_bar'        => 'Simple Smart Offer',
		'archives'              => 'Item Archives',
		'parent_item_colon'     => 'Parent Offer:',
		'all_items'             => 'All Offers',
		'add_new_item'          => 'Add New Offer',
		'add_new'               => 'Add New',
		'new_item'              => 'New Offer',
		'edit_item'             => 'Edit Offer',
		'update_item'           => 'Update Offer',
		'view_item'             => 'View Offer',
		'search_items'          => 'Search Offer',
		'not_found'             => 'Not found',
		'not_found_in_trash'    => 'Not found in Trash',
		'featured_image'        => 'Featured Image',
		'set_featured_image'    => 'Set featured image',
		'remove_featured_image' => 'Remove featured image',
		'use_featured_image'    => 'Use as featured image',
		'insert_into_item'      => 'Insert into offer',
		'uploaded_to_this_item' => 'Uploaded to this offer',
		'items_list'            => 'Offers list',
		'items_list_navigation' => 'Offers list navigation',
		'filter_items_list'     => 'Filter offers list',
	);
	$args = array(
		'label'                 => 'Simple Smart Offer',
		'description'           => 'Simple Smart Offer',
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields', ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 60,
		'menu_icon'             => 'dashicons-feedback',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'simple_smart_offer', $args );

}
add_action( 'init', 'custom_smart_offers', 0 );

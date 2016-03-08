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
        'menu_name'             => 'Simple Smart Offers',
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
        'menu_position'         => 58,
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

// get upsell meta
function upsell_product_get_meta( $value ) {
    global $post;

    $field = get_post_meta( $post->ID, $value, true );
    if ( ! empty( $field ) ) {
        return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
    } else {
        return false;
    }
}

// add upsell meta box
function upsell_product_add_meta_box() {
    add_meta_box(
        'upsell_product-upsell-product',
        __( 'Upsell Product', 'upsell_product' ),
        'upsell_product_html',
        'simple_smart_offer',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'upsell_product_add_meta_box' );

// add upsell meta box content
function upsell_product_html( $post ) {
    wp_nonce_field( '_upsell_product_nonce', 'upsell_product_nonce' ); ?>

    <p>Choose the product and discount amount you’d like to offer.</p>

    <p>
        <label for="upsell_product_choose_the_product"><?php _e( 'Choose the product', 'upsell_product' ); ?></label><br>
        <select name="upsell_product_choose_the_product" id="upsell_product_choose_the_product">
            <option <?php echo (upsell_product_get_meta( 'upsell_product_choose_the_product' ) === 'product 1' ) ? 'selected' : '' ?>>product 1</option>
            <option <?php echo (upsell_product_get_meta( 'upsell_product_choose_the_product' ) === 'product 2' ) ? 'selected' : '' ?>>product 2</option>
            <option <?php echo (upsell_product_get_meta( 'upsell_product_choose_the_product' ) === 'product 3' ) ? 'selected' : '' ?>>product 3</option>
        </select>
    </p>	<p>
        <label for="upsell_product_discount_amount"><?php _e( 'Discount amount', 'upsell_product' ); ?></label><br>
        <input type="text" name="upsell_product_discount_amount" id="upsell_product_discount_amount" value="<?php echo upsell_product_get_meta( 'upsell_product_discount_amount' ); ?>">
    </p><?php
}

// save upsell product data
function upsell_product_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['upsell_product_nonce'] ) || ! wp_verify_nonce( $_POST['upsell_product_nonce'], '_upsell_product_nonce' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['upsell_product_choose_the_product'] ) )
        update_post_meta( $post_id, 'upsell_product_choose_the_product', esc_attr( $_POST['upsell_product_choose_the_product'] ) );
    if ( isset( $_POST['upsell_product_discount_amount'] ) )
        update_post_meta( $post_id, 'upsell_product_discount_amount', esc_attr( $_POST['upsell_product_discount_amount'] ) );
}
add_action( 'save_post', 'upsell_product_save' );

/*
    Usage: upsell_product_get_meta( 'upsell_product_choose_the_product' )
    Usage: upsell_product_get_meta( 'upsell_product_discount_amount' )
*/

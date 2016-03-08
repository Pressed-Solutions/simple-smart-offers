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
function upsell_product_get_meta( $ID, $value ) {
    $field = get_post_meta( $ID, $value, true );
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
    wp_nonce_field( '_upsell_product_nonce', 'upsell_product_nonce' );
    $product_names = get_product_names(); ?>

    <p>Choose the product and discount amount you’d like to offer.</p>

    <p>
        <label for="upsell_product_choose_the_product"><?php _e( 'Product', 'upsell_product' ); ?></label>
        <select name="upsell_product_choose_the_product" id="upsell_product_choose_the_product">
        <?php foreach ( $product_names as $this_product_ID => $this_product_name ) { ?>
            <option value="<?php echo $this_product_ID; ?>"<?php echo (upsell_product_get_meta( $post->ID, 'upsell_product_choose_the_product' ) == $this_product_ID ) ? 'selected' : '' ?>><?php echo $this_product_name; ?></option>
        <?php } ?>
        </select>
    </p>	<p>
        <label for="upsell_product_discount_amount"><?php _e( 'Discount percentage amount', 'upsell_product' ); ?></label>
        <input type="number" min="1" max="100" name="upsell_product_discount_amount" id="upsell_product_discount_amount" value="<?php echo upsell_product_get_meta( $post->ID, 'upsell_product_discount_amount' ); ?>"> %
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

// get all available WooCommerce products
function get_product_names() {
    $product_names = array();
    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'product' ),
        'post_status'            => array( 'publish' ),
        'cache_results'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );

    // The Query
    $products_query = new WP_Query( $args );

    // The Loop
    if ( $products_query->have_posts() ) {
        while ( $products_query->have_posts() ) {
            $products_query->the_post();
            $product_names[get_the_ID()] = get_the_title();
        }
    } else {
        // no posts found
    }

    // Restore original Post Data
    wp_reset_postdata();

    // return the data
    return $product_names;
}

<?php
/*
Plugin Name: ACF Helpers
Plugin URI: https://stevehitchman.uk/
Description: Some general ACF helpers
Version: 0.1.0
Author: Steve Hitchman
Author URI: https://stevehitchman.uk/
Copyright: Steve Hitchman
Text Domain: bbpxl
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Get a field
 *
 * @param $key
 * @param bool $single
 * @param bool $post_id
 *
 * @return mixed
 */
function wp_get_field($key, $single = true, $post_id = false)
{
    $post_id = $post_id ? $post_id : get_the_ID();

    if ($post_id == 'options') {
        return get_option('options_'. $key);
    }

    return get_post_meta($post_id, $key, $single);
}

/**
 * Echo a field
 *
 * @param $key
 * @param bool $single
 * @param bool $post_id
 */
function wp_the_field($key, $single = true, $post_id = false)
{
    echo wp_get_field($key, $single, $post_id);
}

/**
 * Load an image from metadata
 *
 * @param $key
 * @param bool $post_id
 *
 * @return array|mixed|object
 */
function wp_get_image($key, $post_id = false)
{
    $post_id = $post_id ? $post_id : get_the_ID();

    if ($post_id == 'options') {
        $image_id = get_option('options_'. $key);

        return json_decode(get_option('options_image_'. $image_id, true));
    }

    $image_id = get_post_meta($post_id, $key, true);

    return json_decode(get_post_meta($post_id, 'image_'. $image_id, true));
}

/**
 * Remove additional attachment meta when deleted
 *
 * @param $post_id
 */
function remove_generated_attachment_meta_on_delete($post_id)
{
    $key = 'image_'. $post_id;

    global $wpdb;
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key = %s", $key));
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", 'options_'. $key));
}
add_action('delete_attachment', 'remove_generated_attachment_meta_on_delete');

/**
 * Generate image meta data on save
 *
 * @param $value
 * @param $post_id
 * @param $field
 *
 * @return mixed
 */
function generate_meta_for_acf_image($value, $post_id, $field)
{
    $image_data = json_encode(acf_get_attachment($value));

    if ($post_id == 'options') {
        update_option('options_image_'. $value, maybe_serialize($image_data), true);
    } else {
        update_post_meta($post_id, 'image_' . $value, maybe_serialize($image_data));
    }

    return $value;
}
add_filter('acf/update_value/type=image', 'generate_meta_for_acf_image', 10, 3);
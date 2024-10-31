<?php
/**
 * Plugin Name:  nK Wishes
 * Description:  Plugin to add/show/manage wishes.
 * Version:      1.0.0
 * Author:       nK
 * Author URI:   http://nkdev.info
 * License:      GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  nk-wishes
 */

/*
nK Wishes is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
nK Wishes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with nK Wishes. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'NK_WISHES_VERSION', '1.0.0' );
define( 'NK_WISHES__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NK_WISHES__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

load_plugin_textdomain('nk-wishes', false, NK_WISHES__PLUGIN_DIR . '/languages');

/* Add wishes post type */
add_action( 'init', 'nk_wishes_init' );
if ( !function_exists( 'nk_wishes_init' ) ) :
function nk_wishes_init() {
    register_post_type( 'nk_wish',
        array(
            'labels' => array(
                'name'                 => esc_html__('Wishes', 'nk_wishes'),
                'singular_name'        => esc_html__('Wish', 'nk_wishes'),
                'add_new_item'         => esc_html__('Add New Wish', 'nk_wishes'),
                'edit_item'            => esc_html__('Edit Wish', 'nk_wishes'),
                'new_item'             => esc_html__('New Wish', 'nk_wishes'),
                'view_item'            => esc_html__('View Wish', 'nk_wishes'),
                'search_items'         => esc_html__('Search Wishes', 'nk_wishes'),
                'not_found'            => esc_html__('No wishes found', 'nk_wishes'),
                'not_found_in_trash'   => esc_html__('No wishes found in Trash', 'nk_wishes'),
                'all_items'            => esc_html__('All Wishes', 'nk_wishes'),
                'archives'             => esc_html__('Wish Archives', 'nk_wishes'),
                'insert_into_item'     => esc_html__('Insert into wish', 'nk_wishes'),
                'uploaded_to_this_item'=> esc_html__('Uploaded to this wish', 'nk_wishes'),
            ),
            'public'                => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-format-status',
            'supports'              => array(
                'title', 'editor'
            ),
            'register_meta_box_cb'  => 'add_nk_wishes_metaboxes',
            'show_in_rest'          => true
        )
    );

    // Add the Events Meta Boxes
    // The Event Location Metabox
    function nk_wish_metaboxes() {
        global $post;

        // Noncename needed to verify where the data originated
        echo '<input type="hidden" name="nk_wishes_meta_noncename" id="nk_wishes_meta_noncename" value="' . 
        wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
        
        $name = get_post_meta($post->ID, 'nk_wishes_name', true);
        $email = get_post_meta($post->ID, 'nk_wishes_email', true);
        
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th style="width:30%">
                        <label for="nk_wishes_name">Name</label>
                    </th>
                    <td>
                        <input type="text" name="nk_wishes_name" id="nk_wishes_name" value="<?php echo $name; ?>" size="30" style="width:97%">
                        <div style="padding-top:5px;">
                            <small></small>
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th style="width:30%">
                        <label for="nk_wishes_email">Email</label>
                    </th>
                    <td>
                        <input type="text" name="nk_wishes_email" id="nk_wishes_email" value="<?php echo $email; ?>" size="30" style="width:97%">
                        <div style="padding-top:5px;">
                            <small></small>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php
    }
    function add_nk_wishes_metaboxes() {
        add_meta_box('nk_wish_metaboxes', 'Wish Author Info', 'nk_wish_metaboxes', 'nk_wish', 'normal', 'default');
    }

    // Save the Metabox Data
    function nk_wishes_save_meta($post_id, $post) {
        if(!isset($_POST['nk_wishes_meta_noncename'])) {
            return;
        }

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !wp_verify_nonce( $_POST['nk_wishes_meta_noncename'], plugin_basename(__FILE__) )) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ))
            return $post->ID;

        // OK, we're authenticated: we need to find and save the data
        // We'll put it into an array to make it easier to loop though.
        $wishes_meta['nk_wishes_name'] = isset($_POST['nk_wishes_name']) ? $_POST['nk_wishes_name'] : '';
        $wishes_meta['nk_wishes_email'] = isset($_POST['nk_wishes_email']) ? $_POST['nk_wishes_email'] : '';
        
        // Add values of $wishes_meta as custom fields
        foreach ($wishes_meta as $key => $value) { // Cycle through the $wishes_meta array!
            if( $post->post_type == 'revision' ) return; // Don't store custom data twice
            $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
            if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
        }

    }
    add_action('save_post', 'nk_wishes_save_meta', 1, 2); // save the custom fields
}
endif;


/**
 * Enqueue scripts and styles.
 */
function nk_wishes_assets() {
    wp_enqueue_style('nk-wishes', NK_WISHES__PLUGIN_URL . '/css/nk-wishes.css');
    wp_enqueue_script('nk-wishes', NK_WISHES__PLUGIN_URL . '/js/nk-wishes.js', array('jquery'));

    $plugin_js_data = array(
        'WP'    => array(
            'siteurl' => get_option('siteurl')
        )
    );
    wp_localize_script('nk-wishes', 'NK_WISHES', $plugin_js_data );
}
add_action( 'wp_enqueue_scripts', 'nk_wishes_assets' );


/**
 * Get template located in:
 * 1. 'plugins/nk-wishes' directory in theme
 * 2. 'templates' directory in plugin
 */
function nk_wishes_get_template_e($template_name) {
    // get template from theme
    $located = locate_template(
        array(
            'nk-wishes/' . $template_name,
            $template_name
        )
    );

    if(!$located) {
        $located = NK_WISHES__PLUGIN_DIR . '/templates/' . $template_name;
    }

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf('<code>%s</code> does not exist.', $located ), '2.1');
        return;
    }

    // Allow 3rd party plugin filter template file from their plugin.
    $located = apply_filters('nk_wishes_get_template', $located, $template_name);

    include($located);
}
    function nk_wishes_get_template($template_name) {
        ob_start();
        nk_wishes_get_template_e($template_name);
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }


/**
 * Wishes Form Functions
 */
function nk_wishes_form_attrs($attrs = array()) {
    $class = isset($attrs['class']) ? $attrs['class'] : '';
    return ' method="post" class="nk-wishes-form ' . esc_attr($class) . '" ';
}
    function nk_wishes_form_attrs_e($attrs = array()) {
        echo nk_wishes_form_attrs($attrs);
    }
function nk_wishes_form_input_name_attrs() {
    return ' name="nk_wishes_name" ';
}
    function nk_wishes_form_input_name_attrs_e() {
        echo nk_wishes_form_input_name_attrs();
    }
function nk_wishes_form_input_email_attrs() {
    return ' name="nk_wishes_email" ';
}
    function nk_wishes_form_input_email_attrs_e() {
        echo nk_wishes_form_input_email_attrs();
    }
function nk_wishes_form_input_content_attrs() {
    return ' name="nk_wishes_content" ';
}
    function nk_wishes_form_input_content_attrs_e() {
        echo nk_wishes_form_input_content_attrs();
    }
function nk_wishes_form_result() {
    return '<div class="nk-wishes-form-result"></div>';
}
    function nk_wishes_form_result_e() {
        echo nk_wishes_form_result();
    }
function nk_wishes_form_load_spinner_e() {
    ?>

    <span class="nk-wishes-form-spinner">
        <?php nk_wishes_get_template_e('form-load-spinner.php'); ?>
    </span>

    <?php
}
    function nk_wishes_form_load_spinner() {
        ob_start();
        nk_wishes_form_load_spinner_e();
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
function nk_wishes_form_end_e() {
    ?>

    <input type="hidden" name="post_type" id="post_type" value="nk_wish">

    <input type="hidden" name="action" value="add_nk_wish">

    <?php wp_nonce_field( 'nk_wishes_nonce_action', 'nk_wishes_nonce_field' ); ?>

    <?php
}
    function nk_wishes_form_end() {
        ob_start();
        nk_wishes_form_end_e();
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }



/**
 * Wishes Each Functions
 */
function nk_wish_content() {
    return get_the_content();
}
    function nk_wish_content_e() {
        the_content();
    }
function nk_wish_name() {
    return get_post_meta(get_the_ID(), 'nk_wishes_name', true);
}
    function nk_wish_name_e() {
        echo nk_wish_name();
    }
function nk_wish_email() {
    return get_post_meta(get_the_ID(), 'nk_wishes_email', true);
}
    function nk_wish_email_e() {
        echo nk_wish_email();
    }



// Shortcodes
require_once( NK_WISHES__PLUGIN_DIR . 'shortcodes/form.php' );
require_once( NK_WISHES__PLUGIN_DIR . 'shortcodes/wishes.php' );

<?php
/**
 * nK Wishes Form
 *
 * Example:
 * [nk_wishes_form]
 */
add_shortcode("nk_wishes_form", "nk_wishes_form");
function nk_wishes_form($atts, $content="null"){
    return nk_wishes_get_template('form.php');
}


/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_nk_wishes_form" );
function vc_nk_wishes_form() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
            "name"     => esc_html__("nK Wishes Form", "nk_wishes"),
            "base"     => "nk_wishes_form",
            "controls" => "full",
            "category" => "nK",
            "params"   => array()
        ) );
    }
}



function add_nk_wish() {
    if (isset( $_POST['nk_wishes_nonce_field'] ) && wp_verify_nonce( $_POST['nk_wishes_nonce_field'], 'nk_wishes_nonce_action' ) ) {

        $name = isset($_POST['nk_wishes_name']) ? $_POST['nk_wishes_name'] : null;
        $email = isset($_POST['nk_wishes_email']) ? $_POST['nk_wishes_email'] : null;
        $content = isset($_POST['nk_wishes_content']) ? $_POST['nk_wishes_content'] : null;

        if(!$name) {
            echo '<div data-status="error">';
                nk_wishes_get_template_e('form-warning-name.php');
            echo '</div>';
        } else if(!$email || !is_email($email)) {
            echo '<div data-status="error">';
                nk_wishes_get_template_e('form-warning-email.php');
            echo '</div>';
        } else if(!$content) {
            echo '<div data-status="error">';
                nk_wishes_get_template_e('form-warning-content.php');
            echo '</div>';
        } else {
            // create post object with the form values
            $nk_wish_args = array(
                'post_title'    => 'Wish from ' . $name,
                'post_content'  => $content,
                'post_status'   => 'pending',
                'post_type'     => $_POST['post_type']
            );

            // insert the post into the database
            $nk_wish_id = wp_insert_post( $nk_wish_args );

            // save name and email
            $wishes_meta['nk_wishes_name'] = $name;
            $wishes_meta['nk_wishes_email'] = $email;
            
            // Add values of $wishes_meta as custom fields
            foreach ($wishes_meta as $key => $value) { // Cycle through the $wishes_meta array!twice
                $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
                if(get_post_meta($nk_wish_id, $key, false)) { // If the custom field already has a value
                    update_post_meta($nk_wish_id, $key, $value);
                } else { // If the custom field doesn't have a value
                    add_post_meta($nk_wish_id, $key, $value);
                }
                if(!$value) delete_post_meta($nk_wish_id, $key); // Delete if blank
            }

            $name = $email = $content = '';

            echo '<div data-status="success">';
                nk_wishes_get_template_e('form-success.php');
            echo '</div>';
        }
        
        die();
    }
}
add_action('wp_ajax_add_nk_wish', 'add_nk_wish');
add_action('wp_ajax_nopriv_add_nk_wish', 'add_nk_wish'); // not really needed
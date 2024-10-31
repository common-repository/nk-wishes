<?php
/**
 * nK Wishes
 *
 * Example:
 * [nk_wishes count="5" pagination="false"]
 */
add_shortcode("nk_wishes", "nk_wishes");
if ( ! function_exists( 'nk_wishes' ) ) :
function nk_wishes($atts, $content = null) {
    extract(shortcode_atts(array(
        "count"            => 5,
        "pagination"       => false,
        "class"            => ""
    ), $atts));

    $result = nk_wishes_get_template('wishes.php');

    $each_wish = '';
    
    ?> <div class="<?php echo esc_attr($class); ?>"> <?php
        $paged = 0;
        if($pagination) {
            $paged = max( 1, get_query_var( 'page' ), get_query_var( 'paged' ) );
        }
        $query_opts = array(
            'showposts'     => intval($count),
            'posts_per_page'=> intval($count),
            'paged'         => $paged,
            'post_type'     => 'nk_wish'
        );

        $nk_query = new WP_Query($query_opts);
        $counter = 0;
        while ($nk_query->have_posts()) : $nk_query->the_post();
            $each_wish .= nk_wishes_get_template('wishes-each.php');
        endwhile;
    ?>
    </div>

    <?php

    if($pagination) {
        $pagination = nk_wishes_pagination($nk_query);
    } else {
        $pagination = '';
    }

    wp_reset_postdata();
    
    $result = str_replace('{%wishes_class%}', esc_attr($class), $result);
    $result = str_replace('{%wishes_each%}', $each_wish, $result);
    $result = str_replace('{%wishes_pagination%}', $pagination, $result);

    return $result;
}
endif;

if ( ! function_exists( 'nk_wishes_pagination' ) ) :
function nk_wishes_pagination_e($query = null) {
    if($query == null) {
        $query = $GLOBALS['wp_query'];
    }

    // Don't print empty markup if there's only one page.
    if ($query->max_num_pages > 1) {
        ?>
        <nav>
            <ul class="nk-wishes-pager">
                <li class="previous"><?php next_posts_link( esc_html__('&laquo; Older Wishes', 'nk-wishes'), $query->max_num_pages ); ?></li>
                <li class="next"><?php previous_posts_link( esc_html__('Newer Wishes &raquo;', 'nk-wishes') ); ?></li>
            </ul>
        </nav>
        <?php
    }
}
function nk_wishes_pagination($query = null) {
    ob_start();
    nk_wishes_pagination_e($query);
    $result = ob_get_contents();
    ob_end_clean();

    return $result;
}
endif;


/* Add VC Shortcode */
add_action( "init", "vc_nk_wishes" );
if ( ! function_exists( 'vc_nk_wishes' ) ) :
function vc_nk_wishes() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "name" => __("nK Wishes", "nk-wishes"),
           "base" => "nk_wishes",
           "controls" => "full",
           "category" => "nK",
           "params" => array(
              array(
                 "type"        => "textfield",
                 "heading"     => __("Wishes Count", "nk-wishes"),
                 "param_name"  => "count",
                 "value"       => 5,
                 "description" => "",
              ),
              array(
                 "type"        => "checkbox",
                 "heading"     => __("Pagination", "nk-wishes"),
                 "param_name"  => "pagination",
                 "value"       => array( __( "", "nk-wishes" ) => true ),
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => __("Custom Classes", "nk-wishes"),
                 "param_name"  => "class",
                 "value"       => __("", "nk-wishes"),
                 "description" => "",
              ),
           )
        ) );
    }
}
endif;
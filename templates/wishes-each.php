<?php
/**
 * The template for displaying each wishes
 *
 * Override this template by copying it to yourtheme/nk-wishes/wishes-each.php
 *
 * @author  nK
 * @package nK Wishes/Templates
 * @version 1.0.0
 */
?>
<div class="nk-wish nk-wish-styled">
    <?php echo get_avatar( nk_wish_email(), '200', '', false, array('class' => 'nk-wish-author-photo') ); ?> 

    <div class="nk-wish-author-name"><?php nk_wish_name_e(); ?></div>
    <blockquote>
        <?php nk_wish_content_e(); ?>
    </blockquote>
</div>

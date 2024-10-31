<?php
/**
 * The template for displaying wishes form
 *
 * Override this template by copying it to yourtheme/nk-wishes/form.php
 *
 * @author  nK
 * @package nK Wishes/Templates
 * @version 1.0.0
 */
?>

<form <?php nk_wishes_form_attrs_e(array('class' => 'nk-wishes-form-styled')); ?>>
    <label for="nk_wishes_name"><?php esc_html_e('Name', 'nk_wishes') ?> *</label>
    <input type="text" id="nk_wishes_name" class="form-control" <?php nk_wishes_form_input_name_attrs_e(); ?>>

    <label for="nk_wishes_email"><?php esc_html_e('Email', 'nk_wishes') ?> *</label>
    <input type="email" class="form-control" id="nk_wishes_email" <?php nk_wishes_form_input_email_attrs_e(); ?>>

    <label for="nk_wishes_content"><?php esc_html_e('Wish', 'nk_wishes') ?> *</label>
    <textarea class="form-control" id="nk_wishes_content" rows="5" <?php nk_wishes_form_input_content_attrs_e(); ?>></textarea>

    <?php nk_wishes_form_result_e(); ?>

    <button type="submit"><?php esc_html_e('Submit', 'nk_wishes') ?></button> <?php nk_wishes_form_load_spinner_e(); ?>

    <?php nk_wishes_form_end_e(); ?>
</form>

<fieldset>
    <legend> <?php _e("Wp Form", "telsender"); ?> </legend>
    <p style="color: red"><?php _e("In the pro version, it is possible to specify different Telegram channels for the forms", "telsender"); ?></p>
    <ul class="list-forms">

        <?php foreach ($wpfList as $pst):?>
            <li>
                <input name="tscfwc_setting_acseswpforms[]" type="checkbox" <?php echo esc_attr( (in_array($pst->ID, (array) $this->tscfwc_setting_acseswpforms) ? 'checked' : '')) ?>  value="<?php echo esc_attr( $pst->ID ) ?>">
                <input class="input-pro" type="text" placeholder="Chat id" disabled><sup>Pro</sup>
                <?php echo esc_attr($pst->post_title) ?>
            </li>
        <?php endforeach;?>
    </ul>

</fieldset>

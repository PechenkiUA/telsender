<fieldset>
    <legend> <?php _e("Wp Form", "telsender"); ?> </legend>
    <p style="color: red"><?php _e("In the pro version, it is possible to specify different Telegram channels for the forms", "telsender"); ?></p>
    <?php

    echo '<select multiple id="wpforms_list" name="tscfwc_setting_acseswpforms[]" >';
    foreach ($wpfList as $pst) {
        echo '<option ' . ((in_array($pst->ID, (array)$this->tscfwc_setting_acseswpforms)) ? 'selected' : '') . '  value="' . $pst->ID . '">' . $pst->post_title . '</option>';
    }
    echo '</select>';
    ?>
</fieldset>

<fieldset>
    <legend><?php _e("CF7", "telsender"); ?></legend>

    <input type="checkbox" name="" value="1" disabled="disabled"/>
    <?php _e("Send Files cf7", "telsender"); ?> <sup>Pro</sup></br>

    <p style="color: red"><?php _e("In the pro version, it is possible to specify different Telegram channels for the forms", "telsender"); ?></p>

    <hr/>
    <?php
    echo '<select multiple id="selinfo" name="tscfwc_setting_acsesform[]" >';
    foreach ($cf7List as $pst):?>
       <option <?php echo esc_attr( (in_array($pst->ID, (array) $this->tscfwc_setting_acsesform) ? 'selected' : '')) ?>  value="<?php echo esc_attr( $pst->ID ) ?>"><?php echo esc_attr($pst->post_title) ?></option>
    <?php
    endforeach;
    echo '</select>';
    ?>
</fieldset>
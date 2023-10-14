<fieldset>
    <legend><?php _e("CF7", "telsender"); ?></legend>

    <input type="checkbox" name="" value="1" disabled="disabled"/>
    <?php _e("Send Files cf7", "telsender"); ?> <sup>Pro</sup></br>

    <p style="color: red"><?php _e("In the pro version, it is possible to specify different Telegram channels for the forms", "telsender"); ?></p>

    <hr/>
    <ul class="list-forms">
    <?php
    foreach ($cf7List as $pst):?>
       <li>
           <input name="tscfwc_setting_acsesform[]" type="checkbox" <?php echo esc_attr( (in_array($pst->ID, (array) $this->tscfwc_setting_acsesform) ? 'checked' : '')) ?>  value="<?php echo esc_attr( $pst->ID ) ?>">
           <input class="input-pro" type="text" placeholder="Chat id" disabled><sup>Pro</sup>
           <?php echo esc_attr($pst->post_title) ?>
       </li>
    <?php endforeach; ?>

    </ul>
</fieldset>
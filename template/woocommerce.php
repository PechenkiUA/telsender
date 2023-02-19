<fieldset>
    <legend><?php _e("Send out woocommerce", "telsender"); ?></legend>

    <input type="checkbox" name="tscfwc_setting_setcheck[wooc_check]"
           value="1" <?php checked($is_check_wc); ?> />
    <?php _e("Send out woocommerce", "telsender"); ?></br></p>

    <?php /* @var string $list_statuse_wc - list statuses */
    if ($list_statuse_wc) : ?>
        <p><?php _e("Send with status", "telsender"); ?></p>
        <select multiple="multiple" id="tscfwc_status" name="tscfwc_setting_status_wc[]">
            <?php foreach ($list_statuse_wc as $list_key => $list_value) : ?>
                <option <?= ((in_array($list_key, (array)$this->tscfwc_setting_status_wc)) ? 'selected' : ''); ?>
                        value="<?= esc_attr($list_key) ?>"><?= $list_value ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <div class="template_wc_telsender">
        <p><?php _e("woocommerce template", "telsender"); ?>

            <smal><a href="https://gist.github.com/PechenkiUA/7b4e6ba706506cd7e5c489b8ba6b65f4" target="_blank">All
                    tags</a>
            </smal>
        </p>

        <textarea id="tscfwc_setting_wooc_template_editor" name="tscfwc_setting_wooc_template"
                  rows="8"
                  cols="80"><?= stripcslashes(html_entity_decode($this->tscfwc_setting_wooc_template)); ?></textarea>

    </div>
</fieldset>
<?php
if (!defined('ABSPATH')) {
    exit;
}

use pechenki\Telsender\clasess\log;

$caunt = 0;
//  log::clearLog();

?>

<div class="telsenderSetting">
    <h2>Настройки плагина Telsender </h2>

    <table>

        <tr>
            <td>

                <form method="post" action="options.php" id="formsetinvendor">
                    <div class="tabs">
                        <input type="radio" name="tabs" id="tabone" checked="checked">
                        <label for="tabone" class="labelclass"><?php _e("Setting", "telsender"); ?><span
                                    class="dashicons dashicons-admin-generic"></span></label>
                        <div class="tab">
                            <h1><?php _e("Settings", "telsender"); ?></h1>
                            <hr/>
                            <p>
                                <input id="sendKey" type="checkbox" name="tscfwc_setting_setcheck[tscfwc_key]"
                                       value="1" <?php checked($is_check_pechenki); ?> /><?php _e("Send to key TSkey", "telsender"); ?></p>
                            <div class="radioinputdefaul" style="display: none;">
                                <div class="con-def" style="display: none;">


                                    <p><?php _e("To get a token and register your bot, you need to add a bot", "telsender"); ?>
                                        <a href="https://telegram.im/BotFather" target="_blank"> @BotFather</a>.

                                        <?php _e("Further, according to the instructions of the bot, you will register your own and at the end you will be given a token", "telsender"); ?>
                                    </p>

                                    <p><?php _e("To find out the chat id", "telsender"); ?> <strong>https://api.telegram.org/botxxxxxxxxxxxxxxxx/getUpdates</strong>
                                    </p>

                                    <?php _e(" xxxxxxxxx your token that BotFather gave you.", "telsender"); ?>
                                </div>
                                <hr/>
                                <label><?php _e("Token", "telsender"); ?></label></br>
                                <span class="dashicons dashicons-post-status"></span><input style="width:90%"
                                                                                            type="text"
                                                                                            name="tscfwc_setting_token"
                                                                                            value="<?php echo $this->tscfwc->Option('tscfwc_setting_token'); ?>"/></br>

                                <label><?php _e('ChatID', 'telsender'); ?></label></br>
                                <span class="dashicons dashicons-post-status"></span>
                                <input type="text" name="tscfwc_setting_chatid"
                                       value="<?php echo $this->tscfwc->Option('tscfwc_setting_chatid'); ?>"/></br>

                            </div>
                            <div class="radioinputkey" style="display: none;">
                                <div class="con-key" style="display: none;">
                                    <p> <?php _e("In this mode, all messages will be sent to your chat bot.", "telsender"); ?>
                                        <a href="https://telegram.im/@telsender_bot" target="_blank">Telsender_bot</a>
                                    </p>
                                </div>
                                <label><?php _e("Key TSkey", "telsender"); ?></label></br>
                                <span class="dashicons dashicons-admin-network"></span>
                                <input style="width:90%" type="text" name="tscfwc_setting_newtoken"
                                       value="<?php echo $this->tscfwc->Option('tscfwc_setting_newtoken'); ?>"/></br>
                            </div>


                            <hr/>
                            <input type="checkbox" name="" value="1" disabled="disabled"/>
                            <?php _e("Send Files cf7", "telsender"); ?> <sup>Pro</sup></br>


                            <hr/>
                            <p><?php _e("CF7", "telsender"); ?></p>
                            <?php
                            $args = array(
                                'post_type' => 'wpcf7_contact_form',
                                'posts_per_page' => -1,
                            );
                            $query = new WP_Query;
                            $my_posts = $query->query($args);
                            echo '<select multiple id="selinfo" name="tscfwc_setting_acsesform[]" >';
                            foreach ($my_posts as $pst) {
                                echo '<option ' . ((in_array($pst->ID, (array)$this->tscfwc->Option("tscfwc_setting_acsesform"))) ? 'selected' : '') . '  value="' . $pst->ID . '">' . $pst->post_title . '</option>';
                            }
                            echo '</select>';
                            ?>
                            <hr/>
                            <p><?php _e("Wp Form", "telsender"); ?></p>
                            <?php
                            $args = array(
                                'post_type' => 'wpforms',
                                'posts_per_page' => -1,
                            );
                            $query = new WP_Query;
                            $my_posts = $query->query($args);
                            echo '<select multiple id="wpforms_list" name="tscfwc_setting_acseswpforms[]" >';
                            foreach ($my_posts as $pst) {
                                echo '<option ' . ((in_array($pst->ID, (array)$this->tscfwc->Option("tscfwc_setting_acseswpforms"))) ? 'selected' : '') . '  value="' . $pst->ID . '">' . $pst->post_title . '</option>';
                            }
                            echo '</select>';
                            ?>
                        </div>
                        <!-- woocommerce -->
                        <input type="radio" name="tabs" id="tabtwoc">
                        <label for="tabtwoc" class="labelclass">woocommerce <span
                                    class="dashicons dashicons-shield-alt"></span></label>
                        <div class="tab">
                            <input type="checkbox" name="tscfwc_setting_setcheck[wooc_check]"
                                   value="1" <?php checked($is_check_wc); ?> />
                            <?php _e("Send out woocommerce", "telsender"); ?></br></p>

                            <?php if ($list_statuse_wc) : ?>
                                <select multiple="multiple" id="tscfwc_status" name="tscfwc_setting_status_wc[]">
                                    <?php foreach ($list_statuse_wc as $list_key => $list_value) : ?>
                                        <option <?= ((in_array($list_key, (array)$this->tscfwc->Option("tscfwc_setting_status_wc"))) ? 'selected' : ''); ?>
                                                value="<?= $list_key ?>"><?= $list_value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                            <?php ?>
                            <div class="template_wc_telsender">
                                <p><?php _e("woocommerce template", "telsender"); ?>

                                    <smal><a href="https://pechenki.top/telsender.html#listcode" target="_blank">All
                                            tags</a>
                                    </smal>
                                </p>

                                <textarea id="tscfwc_setting_wooc_template_editor" name="tscfwc_setting_wooc_template"
                                          rows="8"
                                          cols="80"><?= stripcslashes(html_entity_decode($this->tscfwc->Option('tscfwc_setting_wooc_template'))); ?></textarea>

                            </div>
                        </div>
                        <!-- woocommerce -->

                        <input type="radio" name="tabs" id="tabthree">
                        <label for="tabthree">Log</label>
                        <div class="tab">
                            <!-- <h1>Tab Three Content</h1> -->
                            <div class="log0wrap">


                                <table>
                                    <tbody>
                                    <tr>
                                        <td>Date</td>
                                        <td>Content</td>
                                    </tr>
                                    <?php

                                    if (log::getLog()) {
                                        foreach (json_decode(log::getLog(), true) as $keyl => $valuel) {
                                            $code = json_decode($valuel['data']);

                                            echo '<tr>';
                                            echo '<td class="log-date">' . date("Y-m-d H:i:s", $valuel["date"]) . '</td>';
                                            echo '<td  class="log-data">' . $valuel['data'] . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>


                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    </br>


                </form>

                <button class="button-primary" id="telsetingven"> Save</button>

            </td>

            <td style="border-left:1px dashed #ccc;padding-left:20px;max-width: 400px;">
                <?php include 'help.php'; ?>
                <?php include  'list-shortcode-html.php';?>

                <a href="https://pechenki.top/telsender.html"><?php _e("More information", "telsender"); ?> </a>
                <hr/>
                <form method="post" action="">
                    <input type="submit" name="curssent" class="button-primary" value="Отправить тестовое слово "/>
                </form>
                <hr/>
                Плагин бесплатный, но если хотите отблагодарить автора.

                <p><a href="https://pechenki.top/donate.html" target="_blank" class="button-info" style="

    background: #17a2b8;

    padding: 9px;

    white-space:  nowrap;

    font-size: 15px;

    color: lemonchiffon;

    text-decoration:  none;

    border-radius:  5px;

"> € На Печеньки</a></p>

                <strong>Автор проекта : Александр

                    <a href="//Pechenki.top" target="_blank">сайт плагина</a>

                </strong>

                <a href="https://pechenki.top/plugin-and-modules/telsenderPro.html" target="_blank">
                    <img src="https://pechenki.top/assets/cache_image/tlsPro/Telsender_800x350_ee8.jpg"
                         style="max-width: 100%;">
                    <p>TelsenderPro</p>
                </a>
            </td>

        </tr>

    </table>

</div>


<script>

</script>
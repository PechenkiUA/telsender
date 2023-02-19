<?php
/** @var TYPE_NAME $token */
/** @var TYPE_NAME $default_token */

?>
<div id="telsenderevent">
    <form action="options.php" method="post" >
        <p style="background: #3A75C4;
                        background: -webkit-linear-gradient(to bottom, #3A75C4 54%, #F9DD16 50%, #F9DD16 100%);
                        background: -moz-linear-gradient(to bottom, #3A75C4 54%, #F9DD16 50%, #F9DD16 100%);
                        background: linear-gradient(to bottom, #3A75C4 60%, #F9DD16 50%, #F9DD16 100%);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        font-size: 1.8em;
                        font-weight: 900;
                        display: initial;
                        margin: 0 auto;
                        width: 100%;">
            Made in Ukraine
        </p>
    <h2>Telsender Event page</h2>
    <?php echo wp_nonce_field('update-options');?>

    <div>
        <label>
            <input type="text" value="<?php echo esc_attr($token);?>" name="ts_event_token"
                   placeholder="<?php echo esc_attr($default_token);?>" style="width:75%;"/>
            <span>Telegram token bot</span></label>
    </div>

    <fieldset>
        <legend>Login Failed</legend>
        <label><input value="1" <?php  checked( $login_failed );?> name="ts_event_login_failed" type="checkbox"/><span>Enabled</span></label>
        <div>
            <label><input type="text" value="<?php echo esc_attr($login_failed_chat_id);?>" name="ts_event_login_failed_chat_id" placeholder="<?php echo esc_attr($default_chat_id);?>"/><span> Send Chat id</span></label>
        </div>
    </fieldset>

    <fieldset>
        <legend>Login success</legend>
        <label><input value="1" <?php  checked( $login_success );?> name="ts_event_login_success" type="checkbox"/><span>Enabled</span></label>
        <div>
            <label><input type="text" value="" name="ts_event_login_success_chat_id" placeholder="<?php echo esc_attr($login_success_chat_id);?>"/><span> Send Chat id</span></label>
        </div>
    </fieldset>

    <?php if (is_array($interception_list_val)): ?>
        <fieldset>
            <legend>Post interception</legend>
            <label><input value="1" <?php  checked( $interception_post );?> name="ts_event_interception_post" type="checkbox"/><span>Enabled</span></label>
            <div>
                <label>
                    <input type="text" value="<?php echo esc_attr($interception_post_chat_id);?>" name="ts_event_interception_post_chat_id"
                           placeholder="<?php echo esc_attr($default_chat_id);?>"/><span> Send Chat id</span>
                </label>
            </div>

            <div class="utm_list_wrap">
                <?php foreach ($interception_list_val as $key => $inter): ?>
                    <div class="utm_list">
                        <input type="text" value="<?php echo esc_attr($inter['param']);?>" name="ts_event_interception_list_param[]"
                               placeholder="interception params"/>
                        <input type="text" value="<?php echo esc_attr($inter['value']);?>" name="ts_event_interception_list_value[]"
                               placeholder="interception params value"/>
                        <input type="text" value="<?php echo esc_attr($inter['title']);?>" name="ts_event_interception_list_title[]" placeholder="Title message"/>
                        <?php if ($key == 0): ?>
                            <button type="button" class="add">+</button>
                        <?php endif; ?>

                        <button type="button" <?php echo ($key == 0)?"disabled":"" ;?> class="remove">&times</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </fieldset>
    <?php endif; ?>


    <fieldset disabled>
        <div class="disabled">
        <legend> Utm </legend>

        <label><input value="1" <?php  checked( $utm );?> name="ts_event_utm" type="checkbox"/><span>Enabled</span></label>
        <div>
            <label><input type="text" value="<?php echo esc_attr($utm_chat_id);?>" name="ts_event_utm_chat_id"
                          placeholder="<?php echo esc_attr($default_chat_id);?>"/><span> Send Chat id</span></label>
        </div>
        <div class="utm_list_wrap">
            <?php if (is_array($utm_list_val)): ?>
                <?php  foreach ($utm_list_val as $key => $utm) : ?>
                    <div class="utm_list">
                        <input type="text" value="<?php echo  esc_attr($utm['source'])?>" name="ts_event_utm_list_param[]" placeholder="utm params"/><input
                            type="text" value="<?php echo  esc_attr($utm['value']);?>" name="ts_event_utm_list_value[]" placeholder="utm params value"/>
                        <button type="button" class="add">+</button>
                        <button type="button" class="remove">&times</button>
                    </div>
                <?php endforeach; ?>
            <?php endif;?>
        </div>
        </div>
    </fieldset>

        <?php echo checked( $wc_add_to_cart );?>
    <fieldset>
        <legend>Woocommerce Add To Cart</legend>
        <label><input value="1" <?php  checked( $wc_add_to_cart );?>  name="ts_event_wc_add_to_cart" type="checkbox"/><span>Enabled</span></label>
        <div>
            <label><input type="text" value="<?php echo  esc_attr($wc_add_to_cart_chat_id);?>" name="ts_event_wc_add_to_cart_chat_id" placeholder="<?php echo esc_attr($default_chat_id)?>"/><span> Send Chat id</span></label>
        </div>
    </fieldset>


    <fieldset>
        <legend>Search bots</legend>
        <label><input value="1" <?php echo checked(  $bots );?> name="ts_event_bots" type="checkbox"/><span>Enabled</span></label>
        <div class="bot_list_wrap">
            <div class="bots_list"><select multiple="multiple" name="ts_event_bot_list_value[]"  placeholder="Bot params">
                    <?php foreach ($searchBots as $botValue =>$botName): ?>
                        <option <?php if (in_array($botValue,$bots_list_val)) echo 'selected';  ?> value="<?php echo  esc_attr($botValue);?>"> <?php echo $botName;?> </option>
                    <?php endforeach; ?>
                    <option selected value="">Other</option>
                </select>
            </div>
            <div class="other_bots">
                <input type="text" placeholder="Other bots" name="otherbots" value="<?php echo esc_attr($otherbots); ?>"/>
            </div>
        </div>
    </fieldset>

    <input type="hidden" name="action" value="update"/><input type="hidden" name="page_options" value="
        ts_event_token,
        ts_event_login_failed,
        ts_event_wc_add_to_cart,
        ts_event_wc_add_to_cart_chat_id,
        ts_event_login_success,
        ts_event_utm,
        ts_event_utm_chat_id,
        ts_event_login_failed_chat_id,
        ts_event_utm_list_param,
        ts_event_utm_list_value,
        ts_event_interception_post,
        ts_event_interception_post_chat_id,
        ts_event_interception_list_value,
        ts_event_interception_list_title,
        ts_event_interception_list_param,
        ts_event_bot_list_value,
        ts_event_bots,
        otherbots,
        ts_event_login_success_chat_id"/>

    <button class="button-primary" type="submit">Save</button>
    </form>
</div>


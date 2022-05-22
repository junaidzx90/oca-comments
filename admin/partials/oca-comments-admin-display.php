<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="settings_wrapper">
    <h3 class="header3">Settings</h3>
    <hr>

    <?php
    $current_tab = 'general-settings';

    if(isset($_GET['page']) && $_GET['page'] === 'oca-settings' && isset($_GET['tab'])){
        $tab = sanitize_text_field( $_GET['tab'] );

        switch ($tab) {
            case 'general-settings':
                $current_tab = 'general-settings';
                break;
            case 'star-setting':
                $current_tab = 'star-setting';
                break;
            case 'activecampaign':
                $current_tab = 'activecampaign';
                break;
            case 'animated-popups':
                $current_tab = 'animated-popups';
                break;
            case 'replacing-comments-text':
                $current_tab = 'replacing-comments-text';
                break;
            case 'highlight-comments':
                $current_tab = 'highlight-comments';
                break;
            case 'replacing-text-to-link':
                $current_tab = 'replacing-text-to-link';
                break;
            case 'top10settings':
                $current_tab = 'top10settings';
                break;
            case 'profile':
                $current_tab = 'profile';
                break;
            case 'comments-to-other':
                $current_tab = 'comments-to-other';
                break;
            
            default:
                $current_tab = 'general-settings';
                break;
        }
    }
    ?>

    <div class="settings__tab">
        <a href="?page=oca-settings&tab=general-settings" class="tablinks <?php echo (($current_tab === 'general-settings') ? 'active': '') ?>">General Setting</a>

        <a href="?page=oca-settings&tab=star-setting" class="tablinks <?php echo (($current_tab === 'star-setting') ? 'active': '') ?>">Star Setting</a>

        <a href="?page=oca-settings&tab=activecampaign" class="tablinks <?php echo (($current_tab === 'activecampaign') ? 'active': '') ?>">ActiveCampaign</a>

        <a href="?page=oca-settings&tab=animated-popups" class="tablinks <?php echo (($current_tab === 'animated-popups') ? 'active': '') ?>">Animated PopUps</a>

        <a href="?page=oca-settings&tab=replacing-comments-text" class="tablinks <?php echo (($current_tab === 'replacing-comments-text') ? 'active': '') ?>">Replacing Texts</a>

        <a href="?page=oca-settings&tab=replacing-text-to-link" class="tablinks <?php echo (($current_tab === 'replacing-text-to-link') ? 'active': '') ?>">Replacing text into Link</a>

        <a href="?page=oca-settings&tab=highlight-comments" class="tablinks <?php echo (($current_tab === 'highlight-comments') ? 'active': '') ?>">Highlight Comments</a>

        <a href="?page=oca-settings&tab=top10settings" class="tablinks <?php echo (($current_tab === 'top10settings') ? 'active': '') ?>">Commenter Ranks</a>

        <a href="?page=oca-settings&tab=profile" class="tablinks <?php echo (($current_tab === 'profile') ? 'active': '') ?>">Profile</a>

        <a href="?page=oca-settings&tab=comments-to-other" class="tablinks <?php echo (($current_tab === 'comments-to-other') ? 'active': '') ?>">Comments to other text</a>
    </div>

    <div id="general-settings" class="tabcontent <?php echo (($current_tab === 'general-settings') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post" action="options.php">
                <table class="widefat">
                    <?php
                    settings_fields( 'general_opt_section' );
                    do_settings_fields('general_opt_page', 'general_opt_section' );
                    ?>
                </table>
                <?php submit_button(  ); ?>
            </form>		
		</div>
    </div>

    <div id="star-setting" class="tabcontent <?php echo (($current_tab === 'star-setting') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post" action="options.php">
                <table class="widefat">
                    <?php
                    settings_fields( 'oca_email_opt_section' );
                    do_settings_fields('oca_email_opt_page', 'oca_email_opt_section' );
                    ?>
                </table>
                <?php submit_button(  ); ?>
            </form>		
		</div>
    </div>

    <div id="activecampaign" class="tabcontent <?php echo (($current_tab === 'activecampaign') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post" action="options.php">
                <table class="widefat">
                    <?php
                    settings_fields( 'oca_activecampaign_opt_section' );
                    do_settings_fields('oca_activecampaign_opt_page', 'oca_activecampaign_opt_section' );
                    ?>
                </table>
                <?php submit_button(  ); ?>
            </form>		
		</div>
    </div>

    <div id="animated-popups" class="tabcontent <?php echo (($current_tab === 'animated-popups') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post">
                <div id="animated-popups">
                    <h3 class="aititle">Animated Images</h3>
                    <div class="animation-images">
                        <div id="imagesWrap" class="imagesWrap">
                            <?php
                            $images = get_option( 'oca_animated_images' );
                            if(is_array($images)){
                                foreach($images as $image){
                                    echo '<div class="anim-image">
                                        <span class="remove_anim">+</span>
                                        <img src="'.$image.'">
                                        <input type="hidden" name="anim_images[]" value="'.$image.'">
                                    </div>';
                                }
                            }
                            ?>
                            <!-- Images will appear here -->
                        </div>

                        <button id="add_animated_image" class="add_animated_image">Add new</button>
                    </div>

                    <h3>PopUp Messages</h3>
                    <div class="popup-messages">
                        <?php
                        $msg0 = get_option("_generic_avatar_popup");
                        $msg0Arr = [];
                        if($msg0 && is_array($msg0)){
                            $msg0Arr = $msg0;
                        }

                        $msg1 = get_option("when_got_oca_star_popup");
                        $msg1Arr = [];
                        if($msg1 && is_array($msg1)){
                            $msg1Arr = $msg1;
                        }

                        $msg2 = get_option("when_in_top_10_commenter_popup");
                        $msg2Arr = [];
                        if($msg2 && is_array($msg2)){
                            $msg2Arr = $msg2;
                        }

                        $msg3 = get_option("when_so_close_oca_star_popup");
                        $msg3Arr = [];
                        if($msg3 && is_array($msg3)){
                            $msg3Arr = $msg3;
                        }

                        $other1 = get_option("other_fallback_1");
                        $other1Arr = [];
                        if($other1 && is_array($other1)){
                            $other1Arr = $other1;
                        }

                        $other2 = get_option("other_fallback_2");
                        $other2Arr = [];
                        if($other2 && is_array($other2)){
                            $other2Arr = $other2;
                        }

                        $other3 = get_option("other_fallback_3");
                        $other3Arr = [];
                        if($other3 && is_array($other3)){
                            $other3Arr = $other3;
                        }

                        $other4 = get_option("other_fallback_4");
                        $other4Arr = [];
                        if($other4 && is_array($other4)){
                            $other4Arr = $other4;
                        }

                        $other5 = get_option("other_fallback_5");
                        $other5Arr = [];
                        if($other5 && is_array($other5)){
                            $other5Arr = $other5;
                        }
                        ?>

                        <div class="ppmsg">
                            <?php 
                            $msg0Switch = ((array_key_exists("switch", $msg0Arr)) ? $msg0Arr['switch']: 'gif');
                            ?>
                            
                            <h4>Generic avatar message</h4>
                            <div class="switch_attachement">
                                <div class="switch_container">
                                    <div class="switch_buttons">
                                        <div class="_switch ">
                                            <input type="radio" name="generic_avatar[switch]" id="generic_avatar_gif_switch" class="target_switch__input" value="gif" <?php echo (($msg0Switch === 'gif') ? 'checked' : '') ?> />
                                            <label for="generic_avatar_gif_switch" class="target1_switch__label">Gif</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="generic_avatar[switch]" id="generic_avatar_image_switch" class="target_switch__input" value="image" <?php echo (($msg0Switch === 'image') ? 'checked' : '') ?> />
                                            <label for="generic_avatar_image_switch" class="target1_switch__label">Image</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="generic_avatar[switch]" id="generic_avatar_video_switch" class="target_switch__input" value="video" <?php echo (($msg0Switch === 'video') ? 'checked' : '') ?> />
                                            <label for="generic_avatar_video_switch" class="target1_switch__label">Video</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php 
                                $msg0Url = ((array_key_exists("url", $msg0Arr)) ? $msg0Arr['url']: '');
                                ?>

                                <input type="url" name="generic_avatar[url]" class="attachment__url <?php echo (($msg0Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $msg0Url ?>" placeholder="Attachment URL">
                            </div>

                            <?php
                            $msg0Text = ((array_key_exists("text", $msg0Arr)) ? wpautop( stripslashes($msg0Arr['text']) ): '');

                            wp_editor( $msg0Text, 'generic_avatar_msg', [
                                'media_buttons' => false,
                                'editor_height' => 100,
                                'textarea_name' => 'generic_avatar[text]'
                            ] );
                            ?>
                            <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                        </div>

                        <div class="ppmsg">
                            <?php 
                            $msg1Switch = ((array_key_exists("switch", $msg1Arr)) ? $msg1Arr['switch']: 'gif');
                            ?>
                            <h4>When got star/stars</h4>
                            <div class="switch_attachement">
                                <div class="switch_container">
                                    <div class="switch_buttons">
                                        <div class="_switch ">
                                            <input type="radio" name="when_got_oca_star[switch]" id="when_got_oca_star_gif_switch" class="target_switch__input" value="gif" <?php echo (($msg1Switch === 'gif') ? 'checked' : '') ?> />
                                            <label for="when_got_oca_star_gif_switch" class="target1_switch__label">Gif</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="when_got_oca_star[switch]" id="when_got_oca_star_image_switch" class="target_switch__input" value="image" <?php echo (($msg1Switch === 'image') ? 'checked' : '') ?> />
                                            <label for="when_got_oca_star_image_switch" class="target1_switch__label">Image</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="when_got_oca_star[switch]" id="when_got_oca_star_video_switch" class="target_switch__input" value="video" <?php echo (($msg1Switch === 'video') ? 'checked' : '') ?> />
                                            <label for="when_got_oca_star_video_switch" class="target1_switch__label">Video</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php 
                                $msg1Url = ((array_key_exists("url", $msg1Arr)) ? $msg1Arr['url']: '');
                                ?>

                                <input type="url" name="when_got_oca_star[url]" class="attachment__url <?php echo (($msg1Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $msg1Url ?>" placeholder="Attachment URL">
                            </div>

                            <?php
                            $msg1Text = ((array_key_exists("text", $msg1Arr)) ?wpautop( stripslashes($msg1Arr['text']) ): '');

                            wp_editor( $msg1Text, 'when_got_oca_star', [
                                'media_buttons' => false,
                                'editor_height' => 100,
                                'textarea_name' => 'when_got_oca_star[text]'
                            ] );
                            ?>
                            <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                        </div>

                        <div class="ppmsg">
                            <?php 
                            $msg2Switch = ((array_key_exists("switch", $msg2Arr)) ? $msg2Arr['switch']: 'gif');
                            ?>
                            <h4>Commenter In top 10</h4>
                            <div class="switch_attachement">
                                <div class="switch_container">
                                    <div class="switch_buttons">
                                        <div class="_switch ">
                                            <input type="radio" name="when_in_top_10_commenter[switch]" id="when_in_top_10_gif_switch" class="target_switch__input" value="gif" <?php echo (($msg2Switch === 'gif') ? 'checked' : '') ?> />
                                            <label for="when_in_top_10_gif_switch" class="target1_switch__label">Gif</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="when_in_top_10_commenter[switch]" id="when_in_top_10_image_switch" class="target_switch__input" value="image" <?php echo (($msg2Switch === 'image') ? 'checked' : '') ?> />
                                            <label for="when_in_top_10_image_switch" class="target1_switch__label">Image</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="when_in_top_10_commenter[switch]" id="when_in_top_10_video_switch" class="target_switch__input" value="video" <?php echo (($msg2Switch === 'video') ? 'checked' : '') ?> />
                                            <label for="when_in_top_10_video_switch" class="target1_switch__label">Video</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php 
                                $msg2Url = ((array_key_exists("url", $msg2Arr)) ? $msg2Arr['url']: '');
                                ?>

                                <input type="url" name="when_in_top_10_commenter[url]" class="attachment__url <?php echo (($msg2Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $msg2Url ?>" placeholder="Attachment URL">
                            </div>

                            <?php
                            $msg2Text = ((array_key_exists("text", $msg2Arr)) ? wpautop( stripslashes($msg2Arr['text']) ): '');

                            wp_editor( $msg2Text, 'when_in_top_10_commenter', [
                                'media_buttons' => false,
                                'editor_height' => 100,
                                'textarea_name' => 'when_in_top_10_commenter[text]'
                            ] );
                            ?>
                            <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                        </div>

                        <div class="ppmsg">
                            <?php 
                            $msg3Switch = ((array_key_exists("switch", $msg3Arr)) ? $msg3Arr['switch']: 'gif');
                            ?>
                            <h4>So close to the star/stars</h4>
                            <div class="switch_attachement">
                                <div class="switch_container">
                                    <div class="switch_buttons">
                                        <div class="_switch ">
                                            <input type="radio" name="when_so_close_oca_star[switch]" id="when_so_close_gif_switch" class="target_switch__input" value="gif" <?php echo (($msg3Switch === 'gif') ? 'checked' : '') ?> />
                                            <label for="when_so_close_gif_switch" class="target1_switch__label">Gif</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="when_so_close_oca_star[switch]" id="when_so_close_image_switch" class="target_switch__input" value="image" <?php echo (($msg3Switch === 'image') ? 'checked' : '') ?> />
                                            <label for="when_so_close_image_switch" class="target1_switch__label">Image</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="when_so_close_oca_star[switch]" id="when_so_close_video_switch" class="target_switch__input" value="video" <?php echo (($msg3Switch === 'video') ? 'checked' : '') ?> />
                                            <label for="when_so_close_video_switch" class="target1_switch__label">Video</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php 
                                $msg3Url = ((array_key_exists("url", $msg3Arr)) ? $msg3Arr['url']: '');
                                ?>

                                <input type="url" name="when_so_close_oca_star[url]" class="attachment__url <?php echo (($msg3Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $msg3Url ?>" placeholder="Attachment URL">
                            </div>

                            <?php
                            $msg3Text = ((array_key_exists("text", $msg3Arr)) ? wpautop( stripslashes($msg3Arr['text']) ): '');

                            wp_editor( $msg3Text, 'when_so_close_oca_star', [
                                'media_buttons' => false,
                                'editor_height' => 100,
                                'textarea_name' => 'when_so_close_oca_star[text]'
                            ] );
                            ?>
                            <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                        </div>

                        <!-- -------- -->
                        <div class="othercases_area">
                            <h3>Other cases</h3>
                            <hr>

                            <div class="ppmsg">
                                <?php 
                                $other1Switch = ((array_key_exists("switch", $other1Arr)) ? $other1Arr['switch']: 'gif');
                                ?>
                                <h4>Message 1</h4>
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_1[switch]" id="other_fallback_1_gif_switch" class="target_switch__input" value="gif" <?php echo (($other1Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="other_fallback_1_gif_switch" class="target1_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_1[switch]" id="other_fallback_1_image_switch" class="target_switch__input" value="image" <?php echo (($other1Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="other_fallback_1_image_switch" class="target1_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_1[switch]" id="other_fallback_1_video_switch" class="target_switch__input" value="video" <?php echo (($other1Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="other_fallback_1_video_switch" class="target1_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php 
                                    $other1msg = ((array_key_exists("url", $other1Arr)) ? $other1Arr['url']: '');
                                    ?>

                                    <input type="url" name="other_fallback_1[url]" class="attachment__url <?php echo (($other1Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $other1msg ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $other1Text = ((array_key_exists("text", $other1Arr)) ? wpautop( stripslashes($other1Arr['text']) ): '');

                                wp_editor( $other1Text, 'other_fallback_1', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'other_fallback_1[text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <?php 
                                $other2Switch = ((array_key_exists("switch", $other2Arr)) ? $other2Arr['switch']: 'gif');
                                ?>
                                <h4>Message 2</h4>
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch ">
                                                <input type="radio" name="other_fallback_2[switch]" id="other_fallback_2_gif_switch" class="target_switch__input" value="gif" <?php echo (($other2Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="other_fallback_2_gif_switch" class="target1_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_2[switch]" id="other_fallback_2_image_switch" class="target_switch__input" value="image" <?php echo (($other2Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="other_fallback_2_image_switch" class="target1_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_2[switch]" id="other_fallback_2_video_switch" class="target_switch__input" value="video" <?php echo (($other2Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="other_fallback_2_video_switch" class="target1_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php 
                                    $other2msg = ((array_key_exists("url", $other2Arr)) ? $other2Arr['url']: '');
                                    ?>

                                    <input type="url" name="other_fallback_2[url]" class="attachment__url <?php echo (($other2Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $other2msg ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $other2Text = ((array_key_exists("text", $other2Arr)) ? wpautop( stripslashes($other2Arr['text']) ): '');

                                wp_editor( $other2Text, 'other_fallback_2', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'other_fallback_2[text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <?php 
                                $other3Switch = ((array_key_exists("switch", $other3Arr)) ? $other3Arr['switch']: 'gif');
                                ?>
                                <h4>Message 3</h4>
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch ">
                                                <input type="radio" name="other_fallback_3[switch]" id="other_fallback_3_gif_switch" class="target_switch__input" value="gif" <?php echo (($other3Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="other_fallback_3_gif_switch" class="target1_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_3[switch]" id="other_fallback_3_image_switch" class="target_switch__input" value="image" <?php echo (($other3Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="other_fallback_3_image_switch" class="target1_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_3[switch]" id="other_fallback_3_video_switch" class="target_switch__input" value="video" <?php echo (($other3Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="other_fallback_3_video_switch" class="target1_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php 
                                    $other3msg = ((array_key_exists("url", $other3Arr)) ? $other3Arr['url']: '');
                                    ?>

                                    <input type="url" name="other_fallback_3[url]" class="attachment__url <?php echo (($other3Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $other3msg ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $other3Text = ((array_key_exists("text", $other3Arr)) ? wpautop( stripslashes($other3Arr['text']) ): '');

                                wp_editor( $other3Text, 'other_fallback_3', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'other_fallback_3[text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <?php 
                                $other4Switch = ((array_key_exists("switch", $other4Arr)) ? $other4Arr['switch']: 'gif');
                                ?>
                                <h4>Message 4</h4>
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch ">
                                                <input type="radio" name="other_fallback_4[switch]" id="other_fallback_4_gif_switch" class="target_switch__input" value="gif" <?php echo (($other4Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="other_fallback_4_gif_switch" class="target1_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_4[switch]" id="other_fallback_4_image_switch" class="target_switch__input" value="image" <?php echo (($other4Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="other_fallback_4_image_switch" class="target1_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_4[switch]" id="other_fallback_4_video_switch" class="target_switch__input" value="video" <?php echo (($other4Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="other_fallback_4_video_switch" class="target1_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php 
                                    $other4msg = ((array_key_exists("url", $other4Arr)) ? $other4Arr['url']: '');
                                    ?>

                                    <input type="url" name="other_fallback_4[url]" class="attachment__url <?php echo (($other4Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $other4msg ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $other4Text = ((array_key_exists("text", $other4Arr)) ? wpautop( stripslashes($other4Arr['text']) ): '');

                                wp_editor( $other4Text, 'other_fallback_4', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'other_fallback_4[text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <?php 
                                $other5Switch = ((array_key_exists("switch", $other5Arr)) ? $other5Arr['switch']: 'gif');
                                ?>
                                <h4>Message 5</h4>
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch ">
                                                <input type="radio" name="other_fallback_5[switch]" id="other_fallback_5_gif_switch" class="target_switch__input" value="gif" <?php echo (($other5Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="other_fallback_5_gif_switch" class="target1_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_5[switch]" id="other_fallback_5_image_switch" class="target_switch__input" value="image" <?php echo (($other5Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="other_fallback_5_image_switch" class="target1_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="other_fallback_5[switch]" id="other_fallback_5_video_switch" class="target_switch__input" value="video" <?php echo (($other5Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="other_fallback_5_video_switch" class="target1_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php 
                                    $other5msg = ((array_key_exists("url", $other5Arr)) ? $other5Arr['url']: '');
                                    ?>

                                    <input type="url" name="other_fallback_5[url]" class="attachment__url <?php echo (($other5Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $other5msg ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $other5Text = ((array_key_exists("text", $other5Arr)) ? wpautop( stripslashes($other5Arr['text']) ): '');

                                wp_editor( $other5Text, 'other_fallback_5', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'other_fallback_5[text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                        </div>

                        <!-- -------- -->
                        <div class="manual_popups">
                            <h3>Top priorities popups</h3>
                            <hr>

                            <?php
                            $priorities_popups = get_option("manual_priorities_popups");
                            $popupsArr = [];
                            if($priorities_popups && is_array($priorities_popups)){
                                $popupsArr = array_values($priorities_popups);
                            }
                            ?>

                            <div class="ppmsg">
                                <h4>Target one</h4>

                                <input type="number" name="manual_priorities[1][comments]" placeholder="Comments level" value="<?php echo ((array_key_exists("0", $popupsArr) && array_key_exists("comments", $popupsArr[0])) ? $popupsArr[0]['comments']: '') ?>">

                                <?php 
                                $target1Switch = ((array_key_exists("0", $popupsArr) && array_key_exists("switch", $popupsArr[0])) ? $popupsArr[0]['switch']: 'gif');
                                ?>
                                
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch ">
                                                <input type="radio" name="manual_priorities[1][switch]" id="gif__switch1" class="target_switch__input" value="gif" <?php echo (($target1Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="gif__switch1" class="target1_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[1][switch]" id="image__switch1" class="target_switch__input" value="image" <?php echo (($target1Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="image__switch1" class="target1_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[1][switch]" id="video__switch1" class="target_switch__input" value="video" <?php echo (($target1Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="video__switch1" class="target1_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $target1_url = ((array_key_exists("0", $popupsArr) && array_key_exists("url", $popupsArr[0])) ? $popupsArr[0]['url']: '');
                                    ?>
                                    
                                    <input type="url" name="manual_priorities[1][url]" class="attachment__url <?php echo (($target1Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $target1_url ?>" placeholder="Attachment URL">
                                </div>


                                <?php
                                $target1_editor = ((array_key_exists("0", $popupsArr) && array_key_exists("text", $popupsArr[0])) ? wpautop( stripslashes($popupsArr[0]['text']) ): '');

                                 wp_editor( $target1_editor, 'target1_editor', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'manual_priorities[1][text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <h4>Target two</h4>
                                <input type="number" name="manual_priorities[2][comments]" placeholder="Comments level" value="<?php echo ((array_key_exists("1", $popupsArr) && array_key_exists("comments", $popupsArr[1])) ? $popupsArr[1]['comments']: '') ?>">

                                <?php 
                                $target2Switch = ((array_key_exists("1", $popupsArr) && array_key_exists("switch", $popupsArr[1])) ? $popupsArr[1]['switch']: 'gif');
                                ?>

                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[2][switch]" id="gif__switch2" class="target_switch__input" value="gif" <?php echo (($target2Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="gif__switch2" class="target2_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[2][switch]" id="image__switch2" class="target_switch__input" value="image" value="image" <?php echo (($target2Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="image__switch2" class="target2_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[2][switch]" id="video__switch2" class="target_switch__input" value="video" <?php echo (($target2Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="video__switch2" class="target2_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $target2_url = ((array_key_exists("1", $popupsArr) && array_key_exists("url", $popupsArr[1])) ? $popupsArr[1]['url']: '');
                                    ?>
                                    <input type="url" name="manual_priorities[2][url]" class="attachment__url <?php echo (($target2Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $target2_url ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $target2_editor = ((array_key_exists("1", $popupsArr) && array_key_exists("text", $popupsArr[1])) ? wpautop( stripslashes($popupsArr[1]['text']) ): '');

                                wp_editor( $target2_editor, 'target2_editor', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'manual_priorities[2][text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <h4>Target three</h4>
                                <input type="number" name="manual_priorities[3][comments]" placeholder="Comments level" value="<?php echo ((array_key_exists("2", $popupsArr) && array_key_exists("comments", $popupsArr[2])) ? $popupsArr[2]['comments']: '') ?>">

                                <?php 
                                $target3Switch = ((array_key_exists("2", $popupsArr) && array_key_exists("switch", $popupsArr[2])) ? $popupsArr[2]['switch']: 'gif');
                                ?>
                                
                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[3][switch]" id="gif__switch3" class="target_switch__input"  value="gif" <?php echo (($target3Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="gif__switch3" class="target3_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[3][switch]" id="image__switch3" class="target_switch__input" value="image" <?php echo (($target3Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="image__switch3" class="target3_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[3][switch]" id="video__switch3" class="target_switch__input" value="video" <?php echo (($target3Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="video__switch3" class="target3_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $target3_url = ((array_key_exists("2", $popupsArr) && array_key_exists("url", $popupsArr[2])) ? $popupsArr[2]['url']: '');
                                    ?>
                                    <input type="url" name="manual_priorities[3][url]" class="attachment__url <?php echo (($target3Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $target3_url ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $target3_editor = ((array_key_exists("2", $popupsArr) && array_key_exists("text", $popupsArr[2])) ? wpautop(stripslashes($popupsArr[2]['text'])): '');

                                wp_editor( $target3_editor, 'target3_editor', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'manual_priorities[3][text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <h4>Target four</h4>
                                <input type="number" name="manual_priorities[4][comments]" placeholder="Comments level" value="<?php echo ((array_key_exists("3", $popupsArr) && array_key_exists("comments", $popupsArr[3])) ? $popupsArr[3]['comments']: '') ?>">

                                <?php 
                                $target4Switch = ((array_key_exists("3", $popupsArr) && array_key_exists("switch", $popupsArr[3])) ? $popupsArr[3]['switch']: 'gif');
                                ?>

                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[4][switch]" id="gif__switch4" class="target_switch__input" value="gif" <?php echo (($target4Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="gif__switch4" class="target4_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[4][switch]" id="image__switch4" class="target_switch__input" value="image" <?php echo (($target4Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="image__switch4" class="target4_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[4][switch]" id="video__switch4" class="target_switch__input" value="video" <?php echo (($target4Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="video__switch4" class="target4_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $target4_url = ((array_key_exists("3", $popupsArr) && array_key_exists("url", $popupsArr[3])) ? $popupsArr[3]['url']: '');
                                    ?>
                                    <input type="url" name="manual_priorities[4][url]" class="attachment__url <?php echo (($target4Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $target4_url ?>" placeholder="Attachment URL">
                                </div>

                                <?php
                                $target4_editor = ((array_key_exists("3", $popupsArr) && array_key_exists("text", $popupsArr[3])) ? wpautop(stripslashes($popupsArr[3]['text'])): '');
                                 wp_editor( $target4_editor, 'target4_editor', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'manual_priorities[4][text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>

                            <div class="ppmsg">
                                <h4>Target five</h4>
                                <input type="number" name="manual_priorities[5][comments]" placeholder="Comments level" value="<?php echo ((array_key_exists("4", $popupsArr) && array_key_exists("comments", $popupsArr[4])) ? $popupsArr[4]['comments']: '') ?>">

                                <?php 
                                $target5Switch = ((array_key_exists("4", $popupsArr) && array_key_exists("switch", $popupsArr[4])) ? $popupsArr[4]['switch']: 'gif');
                                ?>

                                <div class="switch_attachement">
                                    <div class="switch_container">
                                        <div class="switch_buttons">
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[5][switch]" id="gif__switch5" class="target_switch__input" value="gif" <?php echo (($target5Switch === 'gif') ? 'checked' : '') ?> />
                                                <label for="gif__switch5" class="target5_switch__label">Gif</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[5][switch]" id="image__switch5" class="target_switch__input" value="image" <?php echo (($target5Switch === 'image') ? 'checked' : '') ?> />
                                                <label for="image__switch5" class="target5_switch__label">Image</label>
                                            </div>
                                            <div class="_switch">
                                                <input type="radio" name="manual_priorities[5][switch]" id="video__switch5" class="target_switch__input" value="video" <?php echo (($target5Switch === 'video') ? 'checked' : '') ?> />
                                                <label for="video__switch5" class="target5_switch__label">Video</label>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $target5_url = ((array_key_exists("4", $popupsArr) && array_key_exists("url", $popupsArr[4])) ? $popupsArr[4]['url']: '');
                                    ?>
                                    <input type="url" name="manual_priorities[5][url]" class="attachment__url <?php echo (($target5Switch === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $target5_url ?>" placeholder="Attachment URL">
                                </div>
                                <?php

                                $target5_editor = ((array_key_exists("4", $popupsArr) && array_key_exists("text", $popupsArr[4])) ? wpautop( stripslashes($popupsArr[4]['text']) ): '');
                                wp_editor( $target5_editor, 'target5_editor', [
                                    'media_buttons' => false,
                                    'editor_height' => 100,
                                    'textarea_name' => 'manual_priorities[5][text]'
                                ] );
                                ?>
                                <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                            </div>
                        </div>

                        <!-- -------- -->
                        <h3>Next celebration popup</h3>
                        <hr>

                        <div class="ppmsg">
                            <?php 

                            $celebratsData = get_option("nextcelebration_popup");
                            $nextcelebrationArr = [];
                            if($celebratsData && is_array($celebratsData)){
                                $nextcelebrationArr = $celebratsData;
                            }

                            $nextcelebration = ((array_key_exists("switch", $nextcelebrationArr)) ? $nextcelebrationArr['switch']: 'gif');
                            ?>
                            <h4>Message</h4>

                            <input type="number" name="nextcelebration[target]" placeholder="Comments level" value="<?php echo ((array_key_exists("target", $nextcelebrationArr)) ? $nextcelebrationArr['target']: '') ?>">

                            <div class="switch_attachement">
                                <div class="switch_container">
                                    <div class="switch_buttons">
                                        <div class="_switch">
                                            <input type="radio" name="nextcelebration[switch]" id="nextcelebration_gif_switch" class="target_switch__input" value="gif" <?php echo (($nextcelebration === 'gif') ? 'checked' : '') ?> />
                                            <label for="nextcelebration_gif_switch" class="target1_switch__label">Gif</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="nextcelebration[switch]" id="nextcelebration_image_switch" class="target_switch__input" value="image" <?php echo (($nextcelebration === 'image') ? 'checked' : '') ?> />
                                            <label for="nextcelebration_image_switch" class="target1_switch__label">Image</label>
                                        </div>
                                        <div class="_switch">
                                            <input type="radio" name="nextcelebration[switch]" id="nextcelebration_video_switch" class="target_switch__input" value="video" <?php echo (($nextcelebration === 'video') ? 'checked' : '') ?> />
                                            <label for="nextcelebration_video_switch" class="target1_switch__label">Video</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php 
                                $nextcelebrationAttch = ((array_key_exists("url", $nextcelebrationArr)) ? $nextcelebrationArr['url']: '');
                                ?>

                                <input type="url" name="nextcelebration[url]" class="attachment__url <?php echo (($nextcelebration === 'gif') ? 'hiddingclass' : '') ?> widefat" value="<?php echo $nextcelebrationAttch ?>" placeholder="Attachment URL">
                            </div>

                            <?php
                            $nextcelebrationText = ((array_key_exists("text", $nextcelebrationArr)) ? wpautop( stripslashes($nextcelebrationArr['text']) ): '');

                            wp_editor( $nextcelebrationText, 'nextcelebration', [
                                'media_buttons' => false,
                                'editor_height' => 100,
                                'textarea_name' => 'nextcelebration[text]'
                            ] );
                            ?>
                            <p>Use these placeholders to show their values: <code>%name%</code>, <code>%email%</code>, <code>%star_count%</code>, <code>%missing%</code>, <code>%comments%</code></p>
                        </div>
                    </div>
                </div>
                <?php echo get_submit_button( 'Save changes', 'button-primary', 'messages_informations' ) ?>
            </form>		
		</div>
    </div>
    
    <div id="replacing-comments-text" class="tabcontent <?php echo (($current_tab === 'replacing-comments-text') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post">
                <div id="replacing_area">
                    <div class="text_input_rows">
                        <?php 
                        $rows = get_option('replaceing_comment_texts');
                        if(!is_array($rows)){
                            $rows = array();
                        }

                        if(sizeof($rows) > 0){
                            foreach($rows as $key => $row){
                                ?>
                                <div class="inputbox">
                                    <input type="text" placeholder="Search" class="from_text" name="replace_texts[<?php echo $key ?>][search]" value="<?php echo stripcslashes($row['search']) ?>">
                                    <input type="text" required placeholder="Replace with" class="replace_text" name="replace_texts[<?php echo $key ?>][replace]" value="<?php echo stripcslashes($row['replace']) ?>">
                                    <span class="remove_text_inp">+</span>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <span class="addmore_text_input button-secondary">Add Row</span>
                </div>
                <input type="submit" value="Save Changes" name="replacing_text" class="button-primary">
            </form>		
		</div>
    </div>

    <div id="replacing-text-to-link" class="tabcontent <?php echo (($current_tab === 'replacing-text-to-link') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post">
                <div id="replacing_area">
                    <div class="input_urls_rows">
                        <?php 
                        $rows = get_option('replaceing_comment_text_to_urls');
                        if(!is_array($rows)){
                            $rows = array();
                        }

                        if(sizeof($rows) > 0){
                            foreach($rows as $key => $row){
                                ?>
                                <div class="inputbox">
                                    <input type="text" placeholder="Search" class="from_text" name="replace_urls[<?php echo $key ?>][search]" value="<?php echo stripcslashes($row['search']) ?>">
                                    <input type="url" required placeholder="Replace with" class="replace_text" name="replace_urls[<?php echo $key ?>][replace]" value="<?php echo stripcslashes($row['replace']) ?>">
                                    <span class="remove_url_inp">+</span>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <span class="addmore_url_input button-secondary">Add Row</span>
                </div>
                <input type="submit" value="Save Changes" name="replacing_urls" class="button-primary">
            </form>		
		</div>
    </div>

    <div id="highlight-comments" class="tabcontent <?php echo (($current_tab === 'highlight-comments') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post">
                <div class="user_emails">
                    <table style="width: 60%;" class="widefat">
                        <tbody>
                            <tr>
                                <th>Highlight Color</th>
                                <td><input type="text" data-default-color="#fff4e6" name="highlight_color" id="highlight_color" value="<?php echo ((get_option( 'highlight_color' )) ? get_option( 'highlight_color' ) : '#fff4e6') ?>"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h3>Commenter Emails</h3>
                    <ul>
                        <?php
                        $emails = get_option( 'highlight_emails' );
                        if(is_array($emails)){
                            foreach($emails as $email){
                                echo '<li>
                                    <input class="widefat" placeholder="Commenter Email" type="email" name="highlight_emails[]" id="highlight-email" value="'.$email.'">
                                    <span class="remove_eml_inp">+</span>
                                </li>';
                            }
                        }
                        ?>
                    </ul>
                    <span class="addmore_eml_input button-secondary">Add Row</span>
                </div>
                <input type="submit" value="Save Changes" name="update_highlights_emls" class="button-primary">
            </form>      
        </div>
    </div>

    <div id="top10settings" class="tabcontent <?php echo (($current_tab === 'top10settings') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post" action="options.php">
                <table class="widefat">
                    <?php
                    settings_fields( 'oca_top10_opt_section' );
                    do_settings_fields('oca_top10_opt_page', 'oca_top10_opt_section' );
                    ?>
                    <tr>
                        <td>
                            <h3>Exclude emails from rank table</h3>
                            <ul id="exclude_emls">
                                <?php
                                $emailsExc = get_option( 'exclude_rank_emails' );
                                if(is_array($emailsExc)){
                                    foreach($emailsExc as $email){
                                        echo '<li class="exclude_eml_item">
                                            <input class="widefat" placeholder="Commenter Email" type="email" name="exclude_rank_emails[]" id="exclude_rank_emails" value="'.$email.'">
                                            <span class="remove_exc_eml_inp">+</span>
                                        </li>';
                                    }
                                }
                                ?>
                            </ul>
                            <span class="addmore_exc_eml_input button-secondary">Add Row</span>
                        </td>
                    </tr>
                </table>
                <?php submit_button( 'Save changes', 'primary', 'commenter_ranks') ?>
            </form>		
		</div>
    </div>

    <div id="profile" class="tabcontent <?php echo (($current_tab === 'profile') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post">
                <div id="profile_area">
                    <div class="profile_rows">
                        <?php 
                        $rows = get_option('custom_profiles');
                        if(!is_array($rows)){
                            $rows = array();
                        }

                        if(sizeof($rows) > 0){
                            foreach($rows as $key => $row){
                                ?>
                                <div class="inputbox">
                                    <input type="email" placeholder="Email address" class="profile_email" name="profiles[<?php echo $key ?>][email]" value="<?php echo stripcslashes($row['email']) ?>">
                                    <input type="url" required placeholder="Profile URL" class="profile_url" name="profiles[<?php echo $key ?>][url]" value="<?php echo stripcslashes($row['url']) ?>">
                                    <span class="remove_profile_inp">+</span>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <span class="addmore_profile_input button-secondary">Add Profile</span>
                </div>
                <?php submit_button( 'Save changes', 'primary', 'custom_profiles') ?>
            </form>		
		</div>
    </div>

    <div id="comments-to-other" class="tabcontent <?php echo (($current_tab === 'comments-to-other') ? 'active': '') ?>">
        <div class="oca-comments-settings">
            <form method="post">
                <div id="comments-to-other_area">
                    <div class="comments-to-other_rows" id="comments-to-other_rows">
                        <?php 
                        $results = get_option( 'oca_comments_texts' );
                        if(is_array($results)){
                            foreach($results as $key => $result){
                                ?>
                                <div class="inputbox">
                                    <?php
                                    $categories = get_categories( array('hide_empty' => false) );
                                    if($categories){
                                        echo '<select required name="comments_texts['.$key.'][category]" id="comments_texts" class="widefat">';
                                        echo '<option value="">Select</option>';
                                        foreach($categories as $category){
                                            echo '<option '.((intval($result['category']) === intval($category->term_id)) ? 'selected' : '').' value="'.$category->term_id.'">'.$category->name.'</option>';
                                        }
                                        echo '</select>';
                                    }
                                    ?>
                                    <input type="text" required class="singular" placeholder="Singular" name="comments_texts[<?php echo $key ?>][singular]" value="<?php echo $result['singular'] ?>">
                                    <input type="text" required class="plural" placeholder="plural" name="comments_texts[<?php echo $key ?>][plural]" value="<?php echo $result['plural'] ?>">

                                    <span class="remove_rool_inp">+</span>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <span class="addmore_rool_input button-secondary">Add Rool</span>
                </div>
                <?php submit_button( 'Save changes', 'primary', 'comments-to-other') ?>
            </form>		
		</div>
    </div>
</div>
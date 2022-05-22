<?php $commenters_list = $this->get_commenters_list($min, $max, $stars); ?>
<div class="commenter_list">
    <div class="clistTable">
        <?php
        if($commenters_list && is_array($commenters_list)){
            foreach($commenters_list as $commenter){
                $user_email =  $commenter['user_email'];
                $comment_stars = oca_get_stars($user_email, true);
                ?>
                <div class="listRow">
                    <?php
                    $commenterurl = get_commenter_url($user_email);
                    ?>
                    <div class="profileImg">
                        <a class="_authorimg" href="<?php echo $commenterurl ?>" data-content="<?php echo get_commenter_tooltip_info($user_email) ?>" target="_b"><?php echo get_avatar( $user_email ) ?></a>
                    </div>
                    
                    <div class="comment_status">
                        <a class="commentername _authorname" href="<?php echo $commenterurl ?>" data-content="<?php echo get_commenter_tooltip_info($user_email) ?>" target="_b"><b><?php echo __(ucfirst($commenter['user_name']), 'oca-comments') ?></b></a>
                        <?php _e($comment_stars, 'oca-comments') ?>    
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

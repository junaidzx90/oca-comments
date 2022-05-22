<div class="oca_to_ten">
    <?php
    try {
        global $wpdb;
        $emailsExc = get_option( 'exclude_rank_emails' );
        if(!is_array($emailsExc)){
            $emailsExc = array();
        }
        $emails = '""';
        if($emailsExc){
            $sep = '';
            $emails = '';
            foreach($emailsExc as $exc){
                $emails .= $sep.'"'.$exc.'"';
                $sep = ',';
            }
        }

        $commentsObj = $wpdb->get_results("SELECT *, COUNT(comment_author_email) AS counts FROM 
            {$wpdb->prefix}comments 
            WHERE DATE(comment_date) >= DATE(NOW()) - INTERVAL 30 DAY AND `comment_approved` = 1 
            AND comment_author_email NOT IN(".$emails.") AND comment_author_email != '' GROUP BY comment_author_email ORDER BY counts DESC LIMIT $count");

        $comments = array();

        if($commentsObj){
            foreach($commentsObj as $comment){
                $arr['stars'] = oca_get_top_10_stars( $comment->comment_author_email, $comment->counts );
                $arr['email'] = $comment->comment_author_email;

                if($comment->comment_author_email){
                    $comments[] = $arr;
                }
            }
        }

        if(sizeof($comments) > 0){
            $position = 1;
            echo '<ul id="top10users">';
            foreach($comments as $comment){ 
                $commenterurl = get_commenter_url($comment['email']);
                ?>

                <li data-id="<?php echo $position ?>" class="top10item">
                    <a class="_authorimg" data-content="<?php echo get_commenter_tooltip_info($comment['email']) ?>" href="<?php echo $commenterurl ?>" target="_blank"><?php echo get_avatar( $comment['email'], 50 ) ?></a>
                    <div class="info">
                        <a class="_authorname" data-content="<?php echo get_commenter_tooltip_info($comment['email']) ?>" href="<?php echo $commenterurl ?>" target="_blank"><?php echo ucfirst(get_fullname_if_user_exist($comment['email'])) ?></a>

                        <?php echo $comment['stars']; ?>
                    </div>
                </li>
                <?php
                $position++;
            }
            echo '</ul>';
        }
    } catch (\Throwable $th) {
        //throw $th;
    }
    ?>
</div>
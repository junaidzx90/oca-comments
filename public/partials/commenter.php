<?php 
$commenter = '';
$user_email = '';
if(isset($_GET['user'])){
    $commenter = $_GET['user'];
    $user_email = base64_decode($commenter); 
}else{
    if(!empty($attr)){
        if(is_array($attr) && array_key_exists('email', $attr)){
            $user_email = $attr['email'];
        }
    }
}

if($commenter || $user_email){
    global $wpdb;
    $comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_author_email = '$user_email' AND comment_approved = 1 ORDER BY comment_ID DESC");
    $user_name = $wpdb->get_var("SELECT comment_author FROM {$wpdb->prefix}comments WHERE comment_author_email = '$user_email' AND comment_approved = 1");
    $comment_stars = oca_get_stars($user_email, true);
    ?>
    <div id="commenter_info">
        <div class="commenter__header">
            <h3 class="comment_author">
                <?php echo get_avatar( $user_email ) ?>
                <?php echo __(ucfirst($user_name), 'oca-comments') ?>
            </h3>
            <div class="total__comments">
                <?php _e($comment_stars, 'oca-comments') ?>    
            </div>
        </div>
        <div class="author__comments">
            <?php
            if($comments){
                $devider = '';
                foreach($comments as $comment){

                    $categories = get_the_category( $comment->comment_post_ID );
                    $category_ids = [];
                    if($categories){
                        foreach($categories as $category){
                            $category_ids[] = $category->term_id;
                        }
                    }

                    $exclude = get_option( 'cat_of_exclude' );
                    if(!is_array($exclude)){
                        $exclude = array();
                    }

                    if(!array_intersect($category_ids, $exclude)){
                        echo $devider;

                        $date = $comment->comment_date;
                        $post_id = $comment->comment_post_ID;
                        $comment_text = $comment->comment_content;
                        $parent_id = $comment->comment_parent;

                        ?>
                        <div class="comment__box">
                            <div class="comment__head">
                                <span class="date"><?php echo date("F j, Y", strtotime($date)) ?></span>
                                <span class="cooment_of_post"><a target="_blank" href="<?php echo (($post_id) ? get_the_permalink( $post_id ) : '#') ?>"><?php echo (($post_id) ? get_the_title( $post_id ) : '') ?></a></span>
                            </div>
                            <!-- Child comments -->
                            <div class="comments">
                                <?php 
                                if($parent_id){
                                    $parent_author = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}comments WHERE comment_ID = $parent_id");
                                    if($parent_author){
			                            $commenterurl = get_commenter_url($parent_author->comment_author_email);
                                        ?>
                                        <div class="in_replay_to">
                                            <p>In replay to <a target="_blank" href="<?php echo $commenterurl ?>"><?php echo ucfirst($parent_author->comment_author) ?></a></p>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                                <div class="comment_text">
                                    <?php 
                                    if($parent_id){
                                        echo '<span class="replayIcon">➥</span>';
                                    } ?>
                                    <p>
                                        <?php 
                                        $definedText = get_option('replaceing_comment_texts');
                                        if(!is_array($definedText)){
                                            $definedText = array();
                                        }
                                
                                        $replaced_comment = '';
                                
                                        foreach($definedText as $text){
                                            $search = stripcslashes(sanitize_text_field( $text['search'] ));
                                            $replace = stripcslashes(sanitize_text_field( $text['replace'] ));
                                
                                            $replaced_comment = preg_replace("/\b(?i)$search\b/"," $replace ", ((!empty($replaced_comment)) ? $replaced_comment : $comment_text));
                                        }
                                
                                
                                        $definedUrls = get_option('replaceing_comment_text_to_urls');
                                        if(!is_array($definedUrls)){
                                            $definedUrls = array();
                                        }
                                
                                        foreach($definedUrls as $url){
                                            $searchtxt = stripcslashes(sanitize_text_field( $url['search'] ));
                                            $replaceUrl = sanitize_text_field( $url['replace'] );
                                
                                            $replaced_comment = preg_replace("/\b(?i)$searchtxt\b/","<a target='_blank' href='".$replaceUrl."'>$searchtxt</a>", ((!empty($replaced_comment)) ? $replaced_comment : $comment_text));
                                        }

                                        echo __(substr((( $replaced_comment ) ?  $replaced_comment : $comment_text), 0, 375), 'oca-comments');
                                        if(strlen((( $replaced_comment ) ?  $replaced_comment : $comment_text)) > 375){
                                            echo '...&nbsp;<a target="_blank" href="'.(($post_id) ? get_the_permalink( $post_id ).'#comment-'.$comment->comment_ID : '#').'" class="readmore__comment">Read More</a>';
                                        }
                                        ?> 
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
                        $devider = '<div class="comment_devider"></div>';
                    }
                }
            }
            ?>
        </div>
    </div>
<?php } ?>
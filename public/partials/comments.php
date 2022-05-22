<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

function astra_modified_theme_comment( $comment, $args, $depth ) {
	switch ( $comment->comment_type ) {
		case 'pingback':
		case 'trackback':
			// Display trackbacks differently than normal comments.
			?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php esc_html_e( 'Pingback:', 'astra' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'astra' ), '<span class="edit-link">', '</span>' ); ?></p>
			</li>
			<?php
			break;
		default:
			// Proceed with normal comments.
			global $post;
			$commenterurl = get_commenter_url($comment->comment_author_email);

            $highlight_emails = get_option( 'highlight_emails' );
            if(!is_array($highlight_emails)){
                $highlight_emails = array();
            }
            $highlight_color = ((get_option( 'highlight_color' )) ? get_option( 'highlight_color' ) : '#fff4e6');

			$authorname = $comment->comment_author;

            $highlight = false;
            if(in_array($comment->comment_author_email, $highlight_emails)){
                $highlight = true;
            }
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article style="<?php echo (($highlight) ? 'padding: 10px; background-color: '.$highlight_color.';margin-bottom: 5px': '') ?>" id="comment-<?php comment_ID(); ?>" class="ast-comment">
				<div class= 'ast-comment-info'>
					<div class='ast-comment-avatar-wrap'><a class="_authorimg" data-content="<?php echo get_commenter_tooltip_info($comment->comment_author_email) ?>" href="<?php echo $commenterurl ?>" target="_blank"><?php echo get_avatar( $comment, 50 ); ?></a></div><!-- Remove 1px Space
					-->
							<?php
							astra_markup_open( 'ast-comment-data-wrap' );
							astra_markup_open( 'ast-comment-meta-wrap' );
							echo '<header ';
							echo astra_attr(
								'commen-meta-author',
								array(
									'class' => 'ast-comment-meta ast-row ast-comment-author vcard capitalize',
								)
							);
							echo '>';
								printf(
									astra_markup_open(
										'ast-comment-cite-wrap',
										array(
											'open'  => '<div %s>',
											'class' => 'ast-comment-cite-wrap',
										)
									) . '<cite><b class="fn"><a class="_authorname" data-content="'.get_commenter_tooltip_info($comment->comment_author_email).'" href="'.$commenterurl.'" target="_blank">%1$s</a></b> %2$s</cite></div>',
									get_comment_author(),
									// If current post author is also comment author, make it known visually.
									( $comment->user_id === $post->post_author ) ? '<span class="ast-highlight-text ast-cmt-post-author"></span>' : ''
								);
								
								echo '<div class="oca-comment-stars">';
									// Stars
									echo oca_get_stars($comment->comment_author_email);
								echo '</div>';
								
							?>
							<?php astra_markup_close( 'ast-comment-meta-wrap' ); ?>
							</header> <!-- .ast-comment-meta -->
						</div>
						<section class="ast-comment-content comment">
							<?php comment_text(); ?>
							<div class="ast-comment-edit-reply-wrap">
                                <div class="comment-votes">
                                    <?php edit_comment_link( astra_default_strings( 'string-comment-edit-link', false ), '<span class="ast-edit-link">', '</span>' ); ?>
                                </div>
                                <?php
                                comment_reply_link(
                                    array_merge(
                                        $args,
                                        array(
                                            'reply_text' => astra_default_strings( 'string-comment-reply-link', false ),
                                            'add_below' => 'comment',
                                            'depth'  => $depth,
                                            'max_depth' => $args['max_depth'],
                                            'before' => '<span class="ast-reply-link">',
                                            'after'  => '</span>',
                                        )
                                    )
                                );
                                ?>
							</div>
							<?php if ( '0' == $comment->comment_approved ) : ?>
								<p class="ast-highlight-text comment-awaiting-moderation"><?php echo esc_html( astra_default_strings( 'string-comment-awaiting-moderation', false ) ); ?></p>
							<?php endif; ?>
						</section> <!-- .ast-comment-content -->
						<?php astra_markup_close( 'ast-comment-data-wrap' ); ?>
				</article><!-- #comment-## -->

			<?php
			break;
	}
}

?>

<div id="comments" class="comments-area">

	<?php astra_comments_before(); ?>

	<?php
	if ( have_comments() ) :
		astra_markup_open( 'comment-count-wrapper' );
		
		if(get_search_included_cat(get_post()->ID)){
			require_once 'filter-comments.php';
		}

		astra_markup_close( 'comment-count-wrapper' );
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			?>
		<nav id="comment-nav-above" class="navigation comment-navigation" aria-label="<?php esc_attr_e( 'Comments Navigation', 'astra' ); ?>">
			<h3 class="screen-reader-text"><?php echo esc_html( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></h3>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( astra_default_strings( 'string-comment-navigation-previous', false ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; ?>

		<ol id="fc-list" class="ast-comment-list">
			<?php
			wp_list_comments(
				array(
					'callback' => 'astra_modified_theme_comment',
					'style'    => 'ol',
				)
			);
			?>
		</ol><!-- .ast-comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" aria-label="<?php esc_attr_e( 'Comments Navigation', 'astra' ); ?>">
			<h3 class="screen-reader-text"><?php echo esc_html( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></h3>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( astra_default_strings( 'string-comment-navigation-previous', false ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; ?>

	<?php endif; ?>

	<?php
	$changed = change_comments_string(get_post()->ID);
	$singularString = '';
	if($changed){
		$singularString = $changed['singular'];
	}
	?>

	<?php
	if(empty($changed)){
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
			<p class="no-comments"><?php echo esc_html( astra_default_strings( 'string-comment-closed', false ) ); ?></p>
			<?php 
		endif; 
	}
	
	?>

	<?php
	add_filter('comment_form_defaults', 'set_oca_comments_title', 20);
	function set_oca_comments_title( $defaults ){
		$changed = change_comments_string(get_post()->ID);
		$singularString = '';
		if($changed){
			$singularString = $changed['singular'];
		}
		$defaults['title_reply'] = __(((!empty($singularString)) ? 'Leave a '.$singularString: 'Leave a comment'), 'oca-comments');
		return $defaults;
	}
	?>

	<?php comment_form([
		'label_submit' => ((!empty($singularString)) ? 'Post a '.$singularString: 'Post a comment')
	]); ?>

	<?php astra_comments_after(); ?>

</div><!-- #comments -->
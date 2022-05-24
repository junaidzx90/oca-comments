<?php
global $post;
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/public/partials
 */
?>


<?php
$changed = change_comments_string($post->ID);
$string = 'Comments';
if($changed){
    $string = $changed['plural'];
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="commentheader">
    <p><?php echo number_format_i18n( get_comments_number($post->ID) ); ?> <?php echo $string ?></p>
</div>

<div id="filter-comments">
    <div class="searchbox">
        <span class="searchIcon">
            <svg class="" xmlns="http://www.w3.org/2000/svg" width="25" height="25" xmlns:v="https://vecta.io/nano"><path d="M24.406 22.473l-6.207-6.203c2.98-3.926 2.684-9.562-.895-13.141-1.895-1.895-4.41-2.937-7.09-2.937a9.94 9.94 0 0 0-7.086 2.938C1.234 5.02.191 7.539.191 10.215a9.97 9.97 0 0 0 2.938 7.09 9.99 9.99 0 0 0 7.086 2.93c2.137 0 4.27-.68 6.055-2.035l6.203 6.207c.27.266.617.402.969.402a1.37 1.37 0 0 0 .965-.402c.258-.254.402-.605.402-.969a1.36 1.36 0 0 0-.402-.965zM5.063 15.367a7.23 7.23 0 0 1-2.137-5.152 7.22 7.22 0 0 1 2.137-5.152c1.379-1.375 3.203-2.137 5.152-2.137a7.23 7.23 0 0 1 5.152 2.137 7.29 7.29 0 0 1 0 10.305 7.3 7.3 0 0 1-10.305 0zm0 0"/></svg>
            <div class="loading-wrapper fcnone">
                <div class="bar one"></div>
                <div class="bar two"></div>
                <div class="bar three"></div>
            </div>
        </span>
        <?php global $post ?>
        <input type="hidden" id="postid" value="<?php echo $post->ID ?>">
        <input type="search" placeholder="Search Author Name" id="authorname">
    </div>
</div>
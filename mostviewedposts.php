<?php 

function wpb_set_post_views($postID) {
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
Now that you have this function in place, we need to call this function on the single post pages. This way the function knows exactly which post gets the credit for the views. To do this, you would need to paste the following code inside your single post loop:
wpb_set_post_views(get_the_ID());
If you are using a child theme or you just want to make things easy for yourself, then you should simply add the tracker in your header by using wp_head hook. So paste the following code in your theme’s functions.php file or the site-specific plugin:
function wpb_track_post_views ($post_id) {
    if ( !is_single() ) return;
    if ( empty ( $post_id) ) {
        global $post;
        $post_id = $post->ID;    
    }
    wpb_set_post_views($post_id);
}
add_action( 'wp_head', 'wpb_track_post_views');
Once you have placed this, every time a user visits the post, the custom field will be updated.
Note: If you are using a caching plugin, this technique will NOT work by default. We are using W3 Total Cache, and it has the feature called Fragmented Caching. You can use that to make this work just fine. Here is what needs to be changed:
<!-- mfunc wpb_set_post_views($post_id); --><!-- /mfunc -->
Now, you can do all sort of cool stuff such as display post view count, or sort posts by view count. Lets take a look at how to do some of these cool things.
If you want to display the post view count on your single post pages (often next to the comment count or something). Then the first thing you need to do is add the following in your theme’s functions.php file or the site-specific plugin.
function wpb_get_post_views($postID){
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}
Then inside your post loop add the following code:
wpb_get_post_views(get_the_ID());
If you want to sort the posts by view count, then you can do so easily by using the the wp_query post_meta parameter. The most basic example loop query would look like this:
<?php 
$popularpost = new WP_Query( array( 'posts_per_page' => 4, 'meta_key' => 'wpb_post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );
while ( $popularpost->have_posts() ) : $popularpost->the_post();

the_title();

endwhile;
?>


Get all post categories ordered by count.

String syntax:

$categories = get_terms( 'category', 'orderby=count&hide_empty=0' );
Array syntax:

 $categories = get_terms( 'category', array(
 	'orderby'    => 'count',
 	'hide_empty' => 0
 ) );
Get all the links categories:

$mylinks_categories = get_terms('link_category', 'orderby=count&hide_empty=0');
List all the terms in a custom taxonomy, without a link:

 $terms = get_terms("my_taxonomy");
 $count = count($terms);
 if ( $count > 0 ){
     echo "<ul>";
     foreach ( $terms as $term ) {
       echo "<li>" . $term->name . "</li>";
        
     }
     echo "</ul>";
 }
List all the terms, with link to term archive, separated by an interpunct (·). (language specific, WPML method):

$args = array( 'taxonomy' => 'my_term' );

$terms = get_terms('my_term', $args);

$count = count($terms); $i=0;
if ($count > 0) {
    $cape_list = '<p class="my_term-archive">';
    foreach ($terms as $term) {
        $i++;
    	$term_list .= '<a href="' . get_term_link( $term ) . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
    	if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
    }
    echo $term_list;
}

http://wordpress.org/plugins/all-related-posts/

http://wordpress.org/plugins/wp-user-frontend/
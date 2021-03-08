<?php
/**
 * Template Name: Search Management
 */
get_header();
?>

<!-- https://wordpress.stackexchange.com/questions/229003/filter-by-title-content-and-meta-key-at-the-same-time -->


<!-- Start Search Form -->
<form role="search" method="get" class="search-form" action="http://localhost/home-practice/test-search/">
  <label>
    <span class="screen-reader-text">Search for:</span>
    <input type="search" class="search-field" placeholder="Search …" value="" name="search">
  </label>
  <input type="submit" class="search-submit" value="Search">
</form>
<!-- End Search Form -->



<?php
if (isset($_GET['search'])) {

    $search_keyword = $_GET['search'];
    
// start this part search in  post titles, descriptions
    $q1 = get_posts(array(
        'post_type' => 'product', //custom post type name
        'post_status' => 'publish',
        'posts_per_page' => '-1',
        's' => $search_keyword,
    ));
// end this part search in post titles, descriptions


// start this part search in meta field
    $q2 = get_posts(array(
        'post_type' => 'product', //custom post type name
        'post_status' => 'publish',
        'posts_per_page' => '-1',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'product_email', //meta field key 
                'value' => $search_keyword,
                'compare' => 'LIKE',
            ),
            array(
                'key' => 'product_username', //meta field key 
                'value' => $search_keyword,
                'compare' => 'LIKE',
            ),
        ),
    ));
// end this part search in meta field



    $merged = array_merge($q1, $q2);
    $post_ids = array();
    foreach ($merged as $item) {
        $post_ids[] = $item->ID;
    }
    $unique = array_unique($post_ids);
    if (!$unique) {
        $unique = array('0');
    }
    $args = array(
        'post_type' => 'product', //custom post type name
        'posts_per_page' => '1',
        'post__in' => $unique,
        'paged' => get_query_var('paged'),
    );
    $wp_query = new WP_Query($args);

    $loop = new WP_Query($args);
    if ($loop->have_posts()):
        while ($loop->have_posts()): $loop->the_post();
            echo the_title();
            echo "</br>";
        endwhile;

        echo "<nav class=\"sw-pagination\">";
        $big = 999999999; // need an unlikely integer
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $loop->max_num_pages,
        ));
        echo "</nav>";
    endif;
}
wp_reset_query();

get_footer();
?>

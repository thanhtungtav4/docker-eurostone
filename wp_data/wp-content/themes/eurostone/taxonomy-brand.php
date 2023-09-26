<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package recruit
 */
get_header();
$term = get_queried_object();
if(!empty($term) && ($term->parent == 0)){
  include_once(get_stylesheet_directory() .  '/module/brand/is-parent.php');
}
else{
  include_once(get_stylesheet_directory() .  '/module/brand/is-child.php');
}
get_footer();

?>

<script>
jQuery(function ($) {
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();

        var $this = $(this);
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var href = $this.attr('href');
        var match = href.match(/\/page\/(\d+)\//);
        var page = match ? match[1] : 1; // Use the extracted page number or default to 1
        var terms = '<?php echo $term->term_id; ?>';

        // Make the AJAX request
        $.ajax({
            type: 'GET',
            url: ajaxurl,
            data: {
                action: 'custom_pagination', // Custom AJAX action
                page: page,
                terms: terms, // Send the term ID in the AJAX request
            },
            success: function (response) {
                $('#ajax-content').html(response);

                // Remove "aria-current" attribute from the old current page
                $('.page-numbers[aria-current="page"]').removeAttr('aria-current');
                // Remove the "inactive" class from all page numbers
                $('.page-numbers').removeClass('inactive');

                // Add "aria-current" attribute to the new current page
                $this.addClass('current').attr('aria-current', 'page');

                // Add the "inactive" class to the clicked page link to make it inactive
                $this.addClass('inactive');
            },
        });
    });
});

</script>

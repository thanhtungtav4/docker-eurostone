<?php
/**
 * Modify the permalink structure for custom post type "content"
 *
 * This function modifies the permalink structure for the "content" post type
 * to use the URL "/contents/id", where "id" is the ID of the post.
 *
 * @param string $permalink The permalink for the post.
 * @param WP_Post $post The post in question.
 * @param bool $leavename Whether to keep the post name.
 * @return string The modified permalink.
 */
function custom_post_type_permalink($permalink, $post, $leavename) {
  if ($post->post_type === 'content') {
      $permalink = home_url('/contents/' . $post->ID);
  }
  return $permalink;
}
add_filter('post_type_link', 'custom_post_type_permalink', 10, 3);


function custom_post_type_rewrite_rule() {
    add_rewrite_rule(
      '^contents/(\d+)/?$',
      'index.php?post_type=content&p=$matches[1]',
      'top'
    );
}
add_action('init', 'custom_post_type_rewrite_rule');

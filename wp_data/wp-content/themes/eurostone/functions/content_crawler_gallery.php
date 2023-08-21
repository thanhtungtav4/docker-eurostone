<?php

// Function to get attachment ID from URL
function get_attachment_id_from_url($url) {
  $dir = wp_upload_dir();

  // Check if the URL is within the uploads directory
  if (false === strpos($url, $dir['baseurl'] . '/')) {
    return false;
  }

  // Get the file name from the URL
  $file = basename($url);

  // Prepare the query to get attachment IDs for files with the same name
  $query = array(
    'post_type'  => 'attachment',
    'fields'     => 'ids',
    'meta_query' => array(
      array(
        'key'     => '_wp_attached_file',
        'value'   => $file,
        'compare' => 'LIKE'
      )
    )
  );

  // Get attachment IDs that match the query
  $ids = get_posts($query);

  if (!empty($ids)) {
    // Check if the URL matches the attachment URL for each ID
    foreach ($ids as $id) {
      if (wp_get_attachment_url($id) === $url) {
        return $id;
      }
    }
  }

  return false;
}

// Function to extract URLs from a string
function extract_urls($urlString) {
  // Split the string by "http://" or "https://"
  $urls = preg_split('/(https?:\/\/)/', $urlString, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

  // Combine "http://" or "https://" with the URLs
  for ($i = 1; $i < count($urls); $i += 2) {
    $urls[$i] = $urls[$i - 1] . $urls[$i];
  }

  // Remove the unnecessary elements (the original URLs without protocols)
  for ($i = count($urls) - 2; $i >= 0; $i -= 2) {
    unset($urls[$i]);
  }

  // Reindex the array keys
  $urls = array_values($urls);

  // Get the attachment (image) IDs from the URLs using WordPress function
  $attachmentIds = array();
  foreach ($urls as $url) {
    $attachmentId = get_attachment_id_from_url($url);
    if ($attachmentId) {
      $attachmentIds[] = $attachmentId;
    }
  }
  return $attachmentIds;
}

// Function to update the 'product_image_gallery' meta_value by post ID
function update_product_image_gallery_by_id($post_id) {
  $post = get_post($post_id);

  // Regular expression pattern to validate URLs
  $pattern = '/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';

  if (!$post) {
    return false;
  }

  $meta_key = 'product_image_gallery';
  $meta_value = get_post_meta($post_id, $meta_key, true);

  // Explicitly convert $meta_value to a string
  $meta_value = (string) $meta_value;

  // Check if the meta_value is empty or does not contain valid URLs
  if (empty($meta_value) || (preg_match($pattern, $meta_value) != 1)) {
    return false;
  }

  // Extract and update the attachment IDs in 'product_image_gallery' meta field
  $urlExtract = extract_urls($meta_value);
  update_post_meta($post_id, $meta_key, $urlExtract);

  return true;
}

// Function to change data when a post is saved
function changeData($post_ID) {
  update_product_image_gallery_by_id($post_ID);
  return true;
}
// Hook to execute 'changeData' function when a post is saved
add_action('save_post', 'changeData');


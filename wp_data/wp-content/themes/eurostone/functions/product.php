<?php
 function get_background_taxonomy($taxonomy, $term_id){
  $bg_id =  get_field('image_tax_background', $taxonomy . '_' . $term_id);
  if($bg_id){
    return "background-image: url(".  wp_get_attachment_image_url($bg_id, 'full').")";
  }
  return '';
 }
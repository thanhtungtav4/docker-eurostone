<?php
/**
 * Returns the URL of the default post image.
 *
 * @return string|null
 */
function get_default_post_image() {
  return get_the_post_thumbnail_url() ?: null;
}

/**
 * Returns the URL of the SEO post image if available, or null otherwise.
 *
 * @return string|null
 */
function get_seo_post_image() {
  if (!class_exists('Smart_Custom_Fields') || !in_array(get_post_type(), POST_TYPES)) {
    return null;
  }

  $meta_image = SCF::get('meta_image');
  return !empty($meta_image) ? wp_get_attachment_image_url($meta_image) : null;
}

/**
 * Returns the URL of the meta image, using the SEO post image if available,
 * or the default post image otherwise. If neither is available, returns a
 * placeholder image URL.
 *
 * @return string
 */
function get_meta_image() {
  $image_seo = get_seo_post_image();
  if ($image_seo) {
    return $image_seo;
  }

  $image_default = get_default_post_image();
  if ($image_default) {
    return $image_default;
  }

  return PLACEHOLDER_IMAGE_OGP;
}

/**
 * Returns the meta title, using the SEO post title if available, or a
 * combination of the site name and the post title otherwise.
 *
 * @return string
 */
function get_meta_title() {
  $bloginfo = get_bloginfo('name') ?: 'ホームセンターのCAINZ 公式企業サイト';
  $title_default = [get_the_title(), $bloginfo];

  if (class_exists('Smart_Custom_Fields') && in_array(get_post_type(), POST_TYPES)) {
    $meta_title = SCF::get('meta_title');
    if (!empty($meta_title)) {
      $title_seo = $meta_title;
    }
  }

  return isset($title_seo) ? $title_seo : implode(' | ', $title_default);
}

/**
 * Returns the meta description, using the SEO post description if available,
 * or default descriptions for the 'post' and 'content' post types, or an empty
 * string otherwise.
 *
 * @return string
 */
function get_meta_description() {
  if (class_exists('Smart_Custom_Fields') && in_array(get_post_type(), POST_TYPES)) {
    $meta_description = SCF::get('meta_description');
    if (!empty($meta_description)) {
      return $meta_description;
    }
  }

  if (is_singular('post')) {
    return 'カインズのニュースリリース・プレスリリースをご紹介します。ホームセンターのCAINZでおなじみ、株式会社カインズの公式企業サイトです。';
  }

  if (is_singular('content')) {
    return 'カインズの取り組みを、深堀りしてご紹介しています。ホームセンターのCAINZでおなじみ、株式会社カインズの公式企業サイトです。';
  }

  return '';
}


function get_meta_keywords(){
  if(get_post_type() === 'post'){
    $keyword = !empty(SCF::get( 'meta_keyword')) ? SCF::get( 'meta_keyword') : PLACEHOLDER_KEYWORDS_META;
  }
  if(get_post_type() === 'content'){
    $keyword = !empty(SCF::get( 'meta_keyword')) ? SCF::get( 'meta_keyword') : PLACEHOLDER_KEYWORDS_META_CONTENT;
  }
  else{
    $keyword = !empty(SCF::get( 'meta_keyword')) ? SCF::get( 'meta_keyword') : PLACEHOLDER_KEYWORDS_META_POST;
  }
  return $keyword;
}

function gen_meta_title() {
  $ttlSeo = get_meta_title();
  echo '<title>'.$ttlSeo.'</title>';
  echo '<meta name="title" content="'.$ttlSeo.'">';
  echo '<meta property="og:title" content="'.$ttlSeo.'">';
  echo '<meta property="twitter:title" content="'.$ttlSeo.'">';
}
add_action('wp_head', 'gen_meta_title', 3);

function gen_meta_image() {
  $imageMeta = get_meta_image();
  if($imageMeta){
    echo '<meta property="og:image" content="'.$imageMeta.'">';
    echo '<meta property="twitter:image" content="'.$imageMeta.'">';
  }
}
add_action('wp_head', 'gen_meta_image', 3);

function gen_meta_description(){
  $descriptionSeo = get_meta_description();
  if(!empty($descriptionSeo)){
    echo '<meta name="description" content="'.$descriptionSeo.'">';
    echo '<meta property="og:description" content="'.$descriptionSeo.'">';
    echo '<meta property="twitter:description" content="'.$descriptionSeo.'">';
  }
}
add_action('wp_head', 'gen_meta_description', 3);

function gen_meta_keywords(){
    $keyword = get_meta_keywords();
    if($keyword){
      echo '<meta name="keywords" content="'.$keyword.'">';
    }
  }
add_action('wp_head', 'gen_meta_keywords', 3);
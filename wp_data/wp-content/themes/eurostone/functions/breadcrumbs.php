<?php
/**
 * Generate HTML for a breadcrumb navigation
 *
 * @return void Outputs HTML directly to the page
 */
function get_breadcrumbs() {
  $breadcrumbs = [];
  $breadcrumbs[] = '<a href="'.home_url().'">ホーム</a>';
  if (is_page()) {
    global $post;
    $ancestors = $post->ancestors;
    $parent = $post->post_parent;
    if ($ancestors) {
      $parents = array_reverse($ancestors);
      foreach ($parents as $key => $parent) {
        $breadcrumbs[] = '<a href="'.get_permalink($parent).'">'.get_the_title($parent).'</a>';
      }
    }
    $breadcrumbs[] = '<span>'. get_the_title() .'</span>';
    }
    elseif(is_singular('post')){
     $breadcrumbs[] = '<a href="'. home_url('news'). '">ニュースリリース</a>';
     $breadcrumbs[] = '<span>'. get_the_title() .'</span>';
    }
    elseif(is_singular('content')){
      $breadcrumbs[] = '<a href="'. home_url('contents'). '">コンテンツ</a>';
      $breadcrumbs[] = '<span>'. get_the_title() .'</span>';
    }
    else {
      $breadcrumbs[] = '<span>'. get_the_title() .'</span>';
    }

  $breadcrumb_list = '<li class="m-breadcrumb__item">' . implode('</li><li class="m-breadcrumb__item">', $breadcrumbs) . '</li>';

  echo '<div class="m-breadcrumb"><div class="g-inner"><ul class="m-breadcrumb__list">' . $breadcrumb_list . '</ul> </div></div>';
}
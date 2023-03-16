<?php
function generateSchemaFormat() {
  $breadcrumbs = array();
  $breadcrumbs[] = array(
    "@id" => home_url(),
    "name" => "ホーム"
  );
  if (is_page() && !is_front_page()) {
    global $post;
    $ancestors = $post->ancestors;
    $parent = $post->post_parent;
    if ($ancestors) {
      $parents = array_reverse($ancestors);
      foreach ($parents as $key => $parent) {
        $breadcrumbs[] = array(
          "@id" => get_permalink($parent),
          "name" => get_the_title($parent)
        );
      }
    }
    $breadcrumbs[] = array(
      "@id" => get_permalink(),
      "name" => get_the_title()
    );
  } elseif (is_singular('post')) {
    $breadcrumbs[] = array(
      "@id" => home_url('news'),
      "name" => "ニュースリリース"
    );
    $breadcrumbs[] = array(
      "@id" => get_permalink(),
      "name" => get_the_title()
    );
  } elseif (is_singular('content')) {
    $breadcrumbs[] = array(
      "@id" => home_url('contents'),
      "name" => "コンテンツ"
    );
    $breadcrumbs[] = array(
      "@id" => get_permalink(),
      "name" => get_the_title()
    );
  }
  elseif (is_front_page()) {
  }
  else {
    $breadcrumbs[] = array(
      "@id" => get_permalink(),
      "name" => get_the_title()
    );
  }

  // Generate the schema format
  $schema = '<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [';
  foreach ($breadcrumbs as $key => $breadcrumb) {
    $schema .= '{
      "@type": "ListItem",
      "position": ' . ($key + 1) . ',
      "item": {
        "@id": "' . $breadcrumb['@id'] . '",
        "name": "' . $breadcrumb['name'] . '"
      }
    },';
  }
  $schema = rtrim($schema, ',');
  $schema .= '
    ]
  }
  </script>';

  return $schema;
}

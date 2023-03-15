<?php
//   function genSchema() {
//     // Store common calls
//     // Site's main url
//     $site_url = site_url();

//     // Check if we are on the frontpage as wp_title() will return blank if we are
//     $is_front_page = is_front_page();

//     // Site name, set in Settings - General
//     $site_name = get_bloginfo('name');

//     // Page Title. Use $site_name for frontpage
//     $site_title = $is_front_page ? $site_name : wp_title('&raquo;', false);

//     // Site description, set in Settings - General
//     $site_description = get_bloginfo('description');

//     // Breadcrumbs
//     $ancestors = null;
//     $parent = null;
//     $breadcrumbs = [];
//     $schemadata = [];

//     // Check if we are on a page or post
//     if (is_singular()) {
//       global $post;
//       $ancestors = $post->ancestors;
//       $parent = $post->parent;
//       $breadcrumbs[] = '<a href="' . home_url() . '">ホーム</a>';
//       $schemadata[] = [
//         '@type' => 'ListItem',
//         'position' => 1,
//         'name' => 'ホーム',
//         'item' => home_url()
//       ];
//       if ($ancestors) {
//         $parents = array_reverse($ancestors);
//         foreach ($parents as $key => $parent) {
//           $breadcrumbs[] = '<a href="' . get_page_link($parent) . '">' . get_the_title($parent) . '</a>';
//           $schemadata[] = [
//             '@type' => 'ListItem',
//             'position' => $key + 2,
//             'name' => get_the_title($parent),
//             'item' => get_page_link($parent)
//           ];
//         }
//       } else if ($parent) {
//         $breadcrumbs[] = '<a href="' . get_page_link($parent) . '">' . get_the_title($parent) . '</a>';
//         $schemadata[] = [
//           '@type' => 'ListItem',
//           'position' => 2,
//           'name' => get_the_title($parent),
//           'item' => get_page_link($parent)
//         ];
//       }
//       $breadcrumbs[] = '<span>' . get_the_title() . '</span>';
//       // $schemadata[] = [
//       //   '@type' => 'ListItem',
//       //   'position' => count($ancestors) + 2,
//       //   'name' => get_the_title(),
//       // ];
//     }

//     // Layout of schema as PHP Array
//     $global_schema = [
//       '@context' => 'https://schema.org',
//       '@graph'   => [
//         [
//           '@type' => 'WebPage',
//           '@id'   => $site_url,
//           'url'   => $site_url,
//           'name'  => $site_title,
//           'breadcrumb' => [
//             '@type' => 'BreadcrumbList',
//             'itemListElement' => $schemadata
//           ],
//         ],
//         [
//           '@type' => 'WebSite',
//           '@id' => $site_url . '#website',
//           'url' => $site_url,
//           'name' => $site_name,
//           'description' => $site_description,
//           'inLanguage' => 'ja-JP',
//         ],
//       ]
//     ];
//     echo '<script type="application/ld+json, charset=utf-8">' . json_encode($global_schema, JSON_UNESCAPED_UNICODE) . '</script>';
//   }
// add_action('wp_head', 'genSchema');


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

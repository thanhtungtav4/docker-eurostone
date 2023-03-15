<?php
    /**
     * Generate an HTML markup for a secondary headline for a web page.
     *
     * @param string $ttlEN The headline text in English.
     * @param string $ttlJP The headline text in Japanese.
     * @return string The generated HTML markup.
     */
    function pageHeadLine($ttlEN, $ttlJP){
        $en = $ttlEN ? $ttlEN : '';
        $jp = $ttlJP ? $ttlJP : '';
        $genHeadHtml = '';
        $genHeadHtml .= '<div class="m-headlineSecondary" data-bg-text="'.  $en .'">';
        $genHeadHtml .= '<div class="g-inner">';
        $genHeadHtml .= '<h1 class="m-headlineSecondary__title m-headlineSecondary__title--center">'.  $en .'<span class="m-headlineSecondary__title-jp m-headlineSecondary__title-jp--center">'. $jp .'</span></h1>';
        $genHeadHtml .= '</div></div>';
        return $genHeadHtml;
    }

    /**
     * Generates HTML for child pages of the current page.
     *
     * @param int $current_page_id The ID of the current page.
     * @return string The generated HTML.
     */
    function generate_child_html($current_page_id = 0) {
        $parent_page_id = wp_get_post_parent_id($current_page_id);
        $child_html = '';

        $child_args = [
            'post_parent' => $parent_page_id,
            'post_type' => 'page',
            'post_status' => 'publish',
            'post__not_in' => [$current_page_id],
            'posts_per_page' => -1,
            'order'   => 'ASC',
            'orderby'        => 'menu_order',
        ];

        $queryChild = new WP_Query($child_args);

        if ($queryChild->have_posts()) {
            $child_html .= '<section class="m-groupLink"><div class="g-inner"><ul class="m-groupLink__list">';
            while ($queryChild->have_posts()) {
                $queryChild->the_post();
                $child_html .= sprintf(
                    '<li class="m-groupLink__item"><div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="%s">%s</a></div></li>',
                    get_the_permalink(),
                    get_the_title()
                );
            }
            $child_html .= '</ul></div></section>';
        }

        wp_reset_postdata();
        return $child_html;
    }

    /**
     * Generates HTML for category of the current post.
     *
     * @param int $id The ID of the current post.
     * @return string The generated HTML.
     */

    function get_html_category($id){
        $categoryHtml = '';
        $category = get_the_category($id);
        if(!empty($category)){
            $categoryHtml     .= '<div class="m-newsList__item--category">';
            foreach($category as $item){
                $categoryHtml .= sprintf(
                    '<span>%s</span>',
                    $item->name
                );
            }
            $categoryHtml     .= '</div>';
        }
        return $categoryHtml;
    }

    /**
     * Generates HTML for category of the current post detail.
     *
     * @param int $id The ID of the current post.
     * @return string The generated HTML.
     */

     function get_html_category_detail($id){
        $categoryHtml = '';
        $category = get_the_category($id);
        if(!empty($category)){
            $categoryHtml .= '<div class="newsDetail__category">';
            foreach($category as $item){
                $categoryHtml .= sprintf(
                    '<span>%s</span>',
                    $item->name
                );
            }
            $categoryHtml .= '</div>';

        }
        return $categoryHtml;
    }

    /**
     * Generates HTML for category of the current post content.
     *
     * @param int $id The ID of the current post.
     * @return string The generated HTML.
     */

     function get_contents_category($id){
        $categoryHtml = '';
        $term_list = get_the_terms($id, 'category-content');
        if(!empty($term_list)){
            $categoryHtml .= '<div class="m-contentsList__item--category">';
            foreach($term_list as $item){
                $categoryHtml .= sprintf(
                    '<span>%s</span>',
                    $item->name
                );
            }
            $categoryHtml .= '</div>';
        }
        return $categoryHtml;
    }


    /**
     * Generates HTML for category of the current Related content.
     *
     * @param int $id The ID of the current post.
     * @return string The generated HTML.
     */

     function get_contents_related_category($id){
        $categoryHtml = '';
        $term_list = get_the_terms($id, 'category-content');
        if(!empty($term_list)){
            $categoryHtml .='<div class="m-contentsRelatedList__item--category">';
            foreach($term_list as $item){
                $categoryHtml .= sprintf(
                    '<span>%s</span>',
                    $item->name
                );
            }
            $categoryHtml .='</div>';
        }
        return $categoryHtml;
    }


    /**
     * Generates HTML for category of the current detail content.
     *
     * @param int $id The ID of the current post.
     * @return string The generated HTML.
     */

     function get_detail_contents_category($id){
        $categoryHtml = '';
        $term_list = get_the_terms($id, 'category-content');
        if(!empty($term_list)){
            foreach($term_list as $item){
                $categoryHtml .= sprintf(
                    '<dd><a href="%s">%s</a></dd>',
                    home_url('contents/?category='. $item->name),
                    $item->name
                );
            }
        }
        return $categoryHtml;
    }

    /**
     * Generates HTML for category of the current detail content not tag a.
     *
     * @param int $id The ID of the current post.
     * @return string The generated HTML.
     */

     function get_detail_contents_category_name($id){
        $categoryHtml = '';
        $term_list = get_the_terms($id, 'category-content');
        if(!empty($term_list)){
            foreach($term_list as $key => $item){
                $categoryHtml .= sprintf(
                    '<dd>%s</dd>',
                    $item->name
                );
            }
        }
        return $categoryHtml;
    }

    /**
     * Convert Category name to Taxonomy slug
     *
     * @param string $name The name of category.
     * @param string $taxonomy_name The name of taxonomy.
     * @return string The generated Slug.
     */
    function convert_tax_name_to_slug_or_id($name, $taxonomy_name){
        $term = get_term_by('name', $name, $taxonomy_name);  
        if($taxonomy_name == 'category')      {
            return $term->term_id;
        }else{
            return $term->slug;
        }        
    }

    /**
     * Generates HTML for category of the current Related content.
     *
     * @param int $post_id The ID of the current post.
     * @return string The generated HTML.
     */
     
     function get_contents_related_from_category($post_id, $post_type, $taxonomy_name){        
        $cat_slug = array();
        $cat_id = array();
        $argsContent = array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'order'          => 'DESC',
        );
        if (class_exists('Smart_Custom_Fields') && ($related_content = SCF::get('related_content', $post_id)) && !empty($related_content)) {
            $argsContent['post__in'] = $related_content;
        } else {
            if($post_type == 'post'){
                $term_obj_list = get_the_terms( $post_id, 'category-content' );                
                if(!empty($term_obj_list)){            
                    foreach($term_obj_list as $item){
                        $convert_id = convert_tax_name_to_slug_or_id($item->name, $taxonomy_name);                
                        if(!empty($convert_id)){
                            $cat_id[] = $convert_id;
                        }                    
                    }
                } 
                $argsContent['category__in'] = $cat_id;  
            }else{
                $category = get_the_category($post_id);                          
                if(!empty($category)){            
                    foreach($category as $item){
                        $convert_slug = convert_tax_name_to_slug_or_id($item->name, $taxonomy_name);                
                        if(!empty($convert_slug)){
                            $cat_slug[] = $convert_slug;
                        }                    
                    }
                }              
                $argsContent['tax_query'] =  array(
                    array(
                        'taxonomy' => $taxonomy_name,
                        'field'    => 'slug',
                        'terms'    => $cat_slug,
                    ),
                );
            }              
        }     
        $dataContent = new WP_Query($argsContent);
        if($dataContent->have_posts()){
            echo '<section class="m-related contentsRelated">';
            echo '<h2 class="m-related__title">関連コンテンツ</h2>';
            echo '<div class="g-inner">';
            echo '<ul class="m-contentsRelatedList">';
                while ( $dataContent->have_posts() ) : $dataContent->the_post();
                    require( get_stylesheet_directory() . '/module/item/relatedContentItem.php' );
                endwhile;
                    wp_reset_postdata();
            echo '</ul>';
            echo '<div class="m-btnSecondary"><a class="m-btnSecondary__link" href="/contents/">コンテンツ一覧</a></div>';
            echo '</div>';
            echo '</section>';
        }
    }

    /**
     * Generates HTML for category of the current Related content.
     *
     * @param int $post_id The ID of the current post.
     * @return string The generated HTML.
     */
     
     function get_contents_related_same_taxonomy($post_id, $post_type, $taxonomy_name){    
        $cat_slug = array();
        $cat_id = array();         
        $argsPost = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'order'          => 'DESC',
		);
        if (class_exists('Smart_Custom_Fields') && ($related_posts = SCF::get('related_post')) && !empty($related_posts)) {
			$argsPost['post__in'] = $related_posts;
		} else {
            if($post_type == 'post'){
                $category = get_the_category($post_id); 
                if(!empty($category)){            
                    foreach($category as $item){
                        $cat_id[] = $item->term_id;
                    }
                } 
                $argsPost['category__in'] = $cat_id;                 
            }else{
                $term_obj_list = get_the_terms( $post_id, 'category-content' ); 
                if(!empty($term_obj_list)){            
                    foreach($term_obj_list as $item){
                        $cat_slug[] = $item->slug;                        
                    }
                }                 
                $argsPost['tax_query'] =  array(
                    array(
                        'taxonomy' => $taxonomy_name,
                        'field'    => 'slug',
                        'terms'    => $cat_slug,
                    ),
                ); 
            } 
			$argsPost['post__not_in'] = array($post_id);			
		}        
        $dataPost = new WP_Query($argsPost);
        if($dataPost->have_posts()){
            echo '<section class="m-related newsRelated">';
            echo '<h2 class="m-related__title">関連ニュースリリース</h2>';
            echo '<div class="g-inner">';
            echo '<ul class="m-newsList">';
                while ( $dataPost->have_posts() ) : $dataPost->the_post();                    
                    require( get_stylesheet_directory() . '/module/item/newsItem.php' );
                endwhile;
                    wp_reset_postdata();
            echo '</ul>';
            echo '<div class="m-btnSecondary"><a class="m-btnSecondary__link" href="/news/">ニュースリリース一覧</a></div>';
            echo '</div>';
            echo '</section>';
        }
    }

    
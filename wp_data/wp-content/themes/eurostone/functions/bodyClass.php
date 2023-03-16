<?php
  function bodyClass(){
    $type = get_post_type();
    $class = '';
    if($type === 'page' && is_front_page()){
      $class = 'homepage';
    }
    return body_class($class);
  }
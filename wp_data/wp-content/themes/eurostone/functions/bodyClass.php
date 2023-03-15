<?php
  function bodyClass(){
    $type = get_post_type();
    $class = '';
    if($type === 'page' && is_front_page()){
      $class = 'homepage';
    }
    if($type === 'page' && is_page('corporate')){
      $class = 'company';
    }
    if($type ==='page' && is_page('philosophy') ){
      $class = 'companyPhilosophy';
    }
    if($type ==='page' && is_page('history') ){
      $class = 'companyHistory';
    }
    if($type ==='page' && is_page('top_message') ){
      $class = 'companyMessage';
    }
    if($type ==='page' && is_page('aboutus') ){
      $class = 'about';
    }
    if($type ==='page' && is_page('diyhr') ){
      $class = 'aboutDiy';
    }
    if($type ==='page' && is_page('logistics') ){
      $class = 'aboutLogistics';
    }
    if($type ==='page' && is_page('partners') ){
      $class = 'partners';
    }
    if($type ==='page' && is_page('store') ){
      $class = 'aboutStore';
    }
    if($type ==='page' && is_page('quality') ){
      $class = 'aboutQuality';
    }
    if($type ==='page' && is_page('digital') ){
      $class = 'aboutDigital';
    }
    if($type ==='page' && is_page('group') ){
      $class = 'companyAboutbeisia';
    }
    if($type ==='page' && is_page('construction') ){
      $class = 'partnersConstruction';
    }
    if($type ==='page' && is_page('overview') ){
      $class = 'companyOutline';
    }
    if($type ==='page' && is_page('policy') ){
      $class = 'companyPrinciples';
    }
    if($type ==='page' && is_page('news') || $type ==='post' && is_singular('post')){
      $class = 'news';
    }
    if($type ==='page' && is_page('contents') || $type ==='content' && is_singular('content') ){
      $class = 'contentPage';
    }
    if($type ==='page' && is_page('kumimachi') ){
      $class = 'kumimachi';
    }
    if($type ==='page' && is_page('site') ){
      $class = 'site';
    }
    if($type ==='page' && is_page('tenant') ){
      $class = 'partnersTenant';
    }
    if($type ==='page' && is_page('franchise') ){
      $class = 'partnersFranchise';
    }
    if($type ==='page' && is_page('access') ){
      $class = 'companyAccess';
    }
    if($type ==='page' && is_page('products_partner') ){
      $class = 'partnersProductsPartner';
    }
    if($type ==='page' && is_page('management') ){
      $class = 'corporateManagement';
    }
    if($type ==='page' && is_page('event') ){
      $class = 'partnersEvent';
    }
    return body_class($class);
  }
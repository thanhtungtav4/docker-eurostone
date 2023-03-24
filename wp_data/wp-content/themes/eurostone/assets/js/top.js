$(document).ready(function () {
  "use strict";
  if ($(window).width() < 1025) {
    $(".hamburger").click(function (e) {
      e.preventDefault();
      $(this).toggleClass("active");
      $(".m-header__top").toggleClass("active");
      $(".m-header__bottom").toggleClass("opened");
      $(".m-header__bottom--menu .sub").removeClass("active");
      $(".m-header__submenu").removeClass("active");
    });

    $(".m-header__bottom--menu .sub").click(function (e) {
      e.preventDefault();
      $(this).toggleClass("active");
      $(this).find(".m-header__submenu").toggleClass("active");
    });

    $(".m-header__bottom--menu a").click(function (e) {
      $(".m-header__bottom").removeClass("opened");
    });
  }
  $(".translate li").click(function (e) {
    $(".translate li").removeClass("current-lang");
    $(this).addClass("current-lang");
  });
  $(".menu-slider").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    infinite: true,
    arrows: true,
    touchMove: true,
    pauseOnHover: false,
    cssEase: "linear",
    responsive: [
      {
        breakpoint: 1025,
        settings: "unslick",
      },
    ],
  });
  $(".mainv-slider").slick({
    slidesToShow: 1,
    infinite: true,
    dots: true,
    arrows: false,
    touchMove: true,
    pauseOnHover: false,
    autoplay: true,
    autoplaySpeed: 3000,
    fade: true,
    cssEase: "linear",
  });

  $(".person-slider").slick({
    slidesToShow: 1,
    infinite: true,
    dots: true,
    arrows: false,
    touchMove: true,
    pauseOnHover: false,
    autoplay: true,
    autoplaySpeed: 3000,
    fade: true,
    cssEase: "linear",
  });

  $(".slider01").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    infinite: true,
    dots: true,
    arrows: true,
    touchMove: true,
    pauseOnHover: false,
    cssEase: "linear",
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 2,
        },
      },
    ],
  });
  $(".slider02").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    infinite: true,
    dots: true,
    arrows: true,
    touchMove: true,
    pauseOnHover: false,
    cssEase: "linear",
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 1,
        },
      },
    ],
  });

  if ($(".m-header__submenu").length && $(window).width() > 767) {
    $(".m-header__submenu figcaption").matchHeight();
  }

  if ($(".m-blockfqa").length) {
    $(".m-blockfqa dl").each(function (index) {
      $(this)
        .find("dt")
        .click(function () {
          $(this).toggleClass("active");
          $(".m-blockfqa dl dd").eq(index).slideToggle();
        });
    });
  }

  if ($(".benefit__block").length) {
    $(".benefit__block dl").each(function (index) {
      $(this)
        .find("dt")
        .click(function () {
          $(this).toggleClass("active");
          $(".benefit__block dl dd").eq(index).slideToggle();
        });
    });
  }

  if ($(".list-2cols").length) {
    $(".list-2cols h4").matchHeight({
      byRow: true,
    });
  }
  if ($(".list-3cols").length) {
    $(".list-3cols h4").matchHeight({
      byRow: true,
    });
  }
  if ($(".c-slider01").length) {
    $(".c-slider01 .sec").matchHeight();
    $(".c-slider01 h4").matchHeight();
  }
  if($('.m-blockdetail').length) {
    $(".slider-for").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      asNavFor: ".slider-nav",
    });
    $(".slider-nav").slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      asNavFor: ".slider-for",
      dots: true,
      focusOnSelect: true,
    });
  
    if ($(".m-blockdetail__tab ").length) {
      $(".tab-ttl li").each(function (index) {
        $(this).click(function () {
          $(".tab-ttl li").removeClass("active");
          $(this).toggleClass("active");
          $(".tab-cont .tab-frame").removeClass("active");
          $(".tab-cont .tab-frame").eq(index).toggleClass("active");
        });
      });
    }
  }

});

$(document).ready(function () {
  "use strict";

  $(".hamburger").click(function (e) {
    e.preventDefault();
    $(this).toggleClass("active");
    $(".menu").toggleClass("opened");
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
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 2,
        },
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

  if ($(".m-header__submenu").length) {
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
});

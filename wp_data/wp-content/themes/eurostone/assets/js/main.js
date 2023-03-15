document.addEventListener('DOMContentLoaded', () => {

  let body = document.querySelector('body');
  var menuToggleElement = document.querySelector('.js-menu-content');
  var hamburgerButon = document.querySelector('.js-humburger-btn');

  /**
   * click event for hamburder button
   * common
   */

  hamburgerButon.addEventListener('click', () => {
    if (hamburgerButon.checked == true) {
      body.style.overflow = 'hidden';
      menuToggleElement.style.right = '0';
    } else {
      body.style.overflow = '';
      menuToggleElement.style.right = '-100%';
    }
  });

  /**
   * max length
   * common
   */

  var p = document.querySelectorAll(".js-text-length");
  var maxLength = 64;

  p.forEach(element => {
    if (element.textContent.length > maxLength) {
      element.textContent = element.textContent.substring(0, maxLength) + "...";
    }
  });

  /**
   * scrollToTop
   * scrolling browser
   * common
   */

  // Lấy tham chiếu đến nút cuộn lên đầu trang
  var btnBackTop = document.querySelector(".js-backtop-btn");
  var btnCard = document.querySelector('.js-cart-btn');

  $(".js-backtop-btn").click(function () {
    $('html, body').animate({ scrollTop: 0 }, 500);
  });

  var timer;
  var lastScrollTop = 0;
  window.onscroll = function () {
    if (window.innerWidth <= 1079) {
      // Get the current scroll position
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
      const btnTopHeight = btnBackTop.offsetHeight;
      const footerWrapEle = document.querySelector('.m-footer__wrap');
      const footerWrapEleOffsetTop = footerWrapEle.offsetTop - window.innerHeight + btnTopHeight;

      if (scrollTop >= footerWrapEleOffsetTop) {
        btnBackTop.classList.remove('fixed');
        btnCard && btnCard.classList.remove('fixed');
      } else {
        btnBackTop.classList.add('fixed');
        btnCard && btnCard.classList.add('fixed');
      }

      // khi user scroll down mới hidden button
      if (scrollTop > lastScrollTop && scrollTop < footerWrapEleOffsetTop) {
        if (timer) {
          btnBackTop.classList.add('hidden');
          btnCard && btnCard.classList.add('hidden');
          clearTimeout(timer);
        }

        timer = setTimeout(() => {
          btnBackTop.classList.remove('hidden');
          btnCard && btnCard.classList.remove('hidden');
        }, 300);
      }

      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;

    }
  };

  /**
   * hide current item in group link
   * common
   */

  const groupLinks = document.querySelectorAll('.js-link-btn');

  if (groupLinks) {
    groupLinks.forEach(element => {
      const href = element.href.split('/');
      const current = href[href.length - 2];
      if (window.location.href.match(current)) {
        element.parentNode.parentNode.style.display = 'none';
      }
    })
  }

  /**
   * slider top page
   * top page
   */
  const homepage = $('.homepage');
  if (homepage.length) {
    new Swiper('.top_slider', {
      loop: true,
      autoplay: true,
      pagination: {
        el: '.slider-main .pagination',
        clickable: true,
        renderBullet: (index, className) => {
          return '<span class="' + className + '">' + (index + 1) + "</span>";
        }
      },
      speed: 500,
      centeredSlides: true,
      slidesPerView: 1,
      breakpoints: {
        1441: {
          slidesPerView: 1.32,
        }
      }
    });

  }

  var contentsSwiper = null;
  var timer, timerInit, timerStart;
  function initContentsSwiper() {
    contentsSwiper = new Swiper('.contents_slider', {
      loop: true,
      loopedSlides: 2,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      pagination: {
        el: '.contents__main .pagination',
        clickable: true,
        renderBullet: (index, className) => {
          return '<span class="' + className + '">' + (index + 1) + "</span>";
        },
      },
      speed: 500,
      slidesPerView: 1,
      spaceBetween: 20,
      on: {
        slideChange: function () {
          if (window.innerWidth < 1080) return;
          let realLength = this.slides.length - 4;
          if (this.activeIndex > realLength) {
            this.autoplay.stop();
            timerInit = setTimeout(function () {
              contentsSwiper.slideTo(2);
            }, 2000);
            timerStart = setTimeout(function () {
              contentsSwiper.autoplay.start();
            }, 2500);
          }
        }
      },
      breakpoints: {
        1080: {
          slidesPerView: 'auto',
        }
      }
    });
  }

  initContentsSwiper();

  window.addEventListener('resize', debounce(function () {
    if (homepage.length && contentsSwiper) {
      contentsSwiper.destroy(true, true);
    }

    if (timer) {
      clearTimeout(timer);
    }

    if (timerInit) {
      clearTimeout(timerInit);
    }

    if (timerStart) {
      clearTimeout(timerStart);
    }

    timer = setTimeout(initContentsSwiper, 500);
  }, 500));

  function debounce(func, delay) {
    let timerId;
    return function (...args) {
      if (timerId) {
        clearTimeout(timerId);
      }
      timerId = setTimeout(() => {
        func(...args);
        timerId = null;
      }, delay);
    };
  }

  /**
   * search form
   * news, contents page
   */

  $('.js-select').each(function () {
    var _this = $(this);
    var liLength = _this.find('.m-box-select__list li').length;
    if (liLength >= 11) {
      _this.find('.m-box-select').css({
        'height': '250px',
        'paddingBottom': '20px',
      });
    }
    _this.find('.m-input-select').click(function () {
      $(this).toggleClass('is-active');
      if ($(this).hasClass('is-active')) {
        $(this).next('.m-box-select').show();
      } else {
        $(this).next('.m-box-select').hide();
      }
    })
    _this.find('.m-box-select__list li').click(function () {
      var data = $(this).data('box');
      _this.find('.m-input-select').val(data);
      _this.find('.m-input-select').removeClass('is-active');
      _this.find('.m-box-select').hide();
    })
  });

  $('body').on('click', function (event) {
    if (!$(event.target).closest('.block-select').length) {
      $('.m-input-select').removeClass('is-active');
      $('.m-box-select').hide();
    }
  });

  var selectSwiper = new Swiper('.js-swiper-box', {
    direction: 'vertical',
    slidesPerView: "auto",
    mousewheel: true,
    navigation: {
      nextEl: '.button-next',
    },
  });

  // chọn và bỏ toàn bộ
  $("#selectAll").click(function () {
    $(".checkbox").prop("checked", $(this).prop("checked"));
  });

  // kiểm tra nếu có 1 thằng không checked
  $(".checkbox").click(function () {
    if ($(".checkbox:checked").length == $(".checkbox").length) {
      $("#selectAll").prop("checked", true);
    } else {
      $("#selectAll").prop("checked", false);
    }
  });

  // show Advanced Filter Search on Mobile
  $(".js-btn-showSearch").click(function (e) {
    e.preventDefault();
    $('.m-searchForm__wrap').addClass('show');
    $(".m-searchForm__btnShow").remove();
  });

  // TABS
  if ($('.js-tabs').length) {
    $('.js-tabs').each(function () {
      var _tab = $(this);
      _tab.find('.m-tabs__nav a').click(function () {
        $('.m-tabs__nav a').removeClass('is-active');
        $(this).addClass('is-active');
        var dataTab = $(this).data('tab');
        $('.m-tabs__content .tab-block').removeClass('is-active');
        $('.m-tabs__content .tab-block#' + dataTab).addClass('is-active');
      })
    })
  }

  // SCROLL TO DIV
  $(window).on("load resize", function () {
    $('a.js-scroll[href*=\\#]:not([href=\\#])').click(function () {
      var headerH = $('.m-header').outerHeight();
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.substr(1) + ']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top - headerH
        }, 1000);
        return false;
      }
    });
  });

});
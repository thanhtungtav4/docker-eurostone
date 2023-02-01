function addClass(A){document.documentElement.classList.add(A)}var avif=new Image;function check_webp_feature(a){var e=new Image;e.onload=function(){var A=0<e.width&&0<e.height;a(A)},e.onerror=function(){a(!1)},e.src="data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA=="}avif.src="data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgANogQEAwgMg8f8D///8WfhwB8+ErK42A=",avif.onload=function(){addClass("avif")},avif.onerror=function(){check_webp_feature(function(A){return addClass(A?"webp":"fallback")})};
// check support AVIF support detection script
!function(e){"function"==typeof define&&define.amd?define(e):e()}(function(){var e,t=["scroll","wheel","touchstart","touchmove","touchenter","touchend","touchleave","mouseout","mouseleave","mouseup","mousedown","mousemove","mouseenter","mousewheel","mouseover"];if(function(){var e=!1;try{var t=Object.defineProperty({},"passive",{get:function(){e=!0}});window.addEventListener("test",null,t),window.removeEventListener("test",null,t)}catch(e){}return e}()){var n=EventTarget.prototype.addEventListener;e=n,EventTarget.prototype.addEventListener=function(n,o,r){var i,s="object"==typeof r&&null!==r,u=s?r.capture:r;(r=s?function(e){var t=Object.getOwnPropertyDescriptor(e,"passive");return t&&!0!==t.writable&&void 0===t.set?Object.assign({},e):e}(r):{}).passive=void 0!==(i=r.passive)?i:-1!==t.indexOf(n)&&!0,r.capture=void 0!==u&&u,e.call(this,n,o,r)},EventTarget.prototype.addEventListener._original=e}});
//# Makes {passive: true} by default when EventListenerOptions are supported

// var navicon = document.getElementById('navicon');
// var navEl = document.getElementById('siteNav');
//  function toggleMenu(){
//    navEl.classList.toggle('is_active');
//    navicon.classList.toggle('is_active');
// };
// navicon.addEventListener("click", toggleMenu, false);
$( document ).ready(function() {
  $('.m-carousel').not('.slick-initialized').slick({
    dots: true,
    speed: 300,
    arrows: false
  });
  $('.m-carousel').on('init', function(){
    $(this).removeClass('slick-not-init')
  })
  /* Simulate defer loading slick */
  setTimeout(function() {
    $('.m-carousel').slick({});
  }, 2500)
});
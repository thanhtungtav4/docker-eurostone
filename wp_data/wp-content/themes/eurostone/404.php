<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package recruit
 */

get_header();

?>
  <style>
    .error-container,.link-container,.zoom-area,h1{text-align:center}.m-notfound h1{font-size:3rem}.error-container{font-size:106px;font-weight:800;margin:70px 15px}.error-container>span{display:inline-block;position:relative}.error-container>span.four{width:136px;height:43px;border-radius:999px;background:linear-gradient(140deg,rgba(0,0,0,.1) 0,rgba(0,0,0,.07) 43%,transparent 44%,transparent 100%),linear-gradient(105deg,transparent 0,transparent 40%,rgba(0,0,0,.06) 41%,rgba(0,0,0,.07) 76%,transparent 77%,transparent 100%),linear-gradient(to right,#d89ca4,#e27b7e)}.error-container>span.four:after,.error-container>span.four:before{content:'';display:block;position:absolute;border-radius:999px}.error-container>span.four:before{width:43px;height:156px;left:60px;bottom:-43px;background:linear-gradient(128deg,rgba(0,0,0,.1) 0,rgba(0,0,0,.07) 40%,transparent 41%,transparent 100%),linear-gradient(116deg,rgba(0,0,0,.1) 0,rgba(0,0,0,.07) 50%,transparent 51%,transparent 100%),linear-gradient(to top,#99749d,#b895ab,#cc9aa6,#d7969e,#e0787f)}.error-container>span.four:after{width:137px;height:43px;transform:rotate(-49.5deg);left:-18px;bottom:36px;background:linear-gradient(to right,#99749d,#b895ab,#cc9aa6,#d7969e,#e0787f)}.error-container>span.zero{vertical-align:text-top;width:156px;height:156px;border-radius:999px;background:linear-gradient(-45deg,transparent 0,rgba(0,0,0,.06) 50%,transparent 51%,transparent 100%),linear-gradient(to top right,#99749d,#99749d,#b895ab,#cc9aa6,#d7969e,#ed8687,#ed8687);overflow:hidden;animation:5s infinite bgshadow}.error-container>span.zero:before{content:'';display:block;position:absolute;transform:rotate(45deg);width:90px;height:90px;background-color:transparent;left:0;bottom:0;background:linear-gradient(95deg,transparent 0,transparent 8%,rgba(0,0,0,.07) 9%,transparent 50%,transparent 100%),linear-gradient(85deg,transparent 0,transparent 19%,rgba(0,0,0,.05) 20%,rgba(0,0,0,.07) 91%,transparent 92%,transparent 100%)}.error-container>span.zero:after{content:'';display:block;position:absolute;border-radius:999px;width:70px;height:70px;left:43px;bottom:43px;background:#fdfaf5;box-shadow:-2px 2px 2px 0 rgba(0,0,0,.1)}.screen-reader-text{position:absolute;top:-9999em;left:-9999em}@keyframes bgshadow{0%{box-shadow:inset -160px 160px 0 5px rgba(0,0,0,.4)}45%,55%{box-shadow:inset 0 0 0 0 rgba(0,0,0,.1)}100%{box-shadow:inset 160px -160px 0 5px rgba(0,0,0,.4)}}*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}body{background-color:#fdfaf5;margin-bottom:50px}button,html,input,select,textarea{font-family:Montserrat,Helvetica,sans-serif;color:#bbb}h1{margin:30px 15px}.zoom-area{max-width:490px;margin:30px auto;font-size:19px}a.more-link{text-transform:uppercase;font-size:1.5rem;background-color:#000936;padding:10px 15px;border-radius:0;color:#fff;display:inline-block;margin-right:5px;margin-bottom:5px;line-height:1.5;text-decoration:none;margin-top:50px;letter-spacing:1px;font-weight:700}@media (max-width:768px){.error-container{font-size:74.2px;font-weight:800;margin:21px 4.5px}.error-container>span.four{width:95.2px;height:30.9px;border-radius:699.3px}.error-container>span.four:before{width:30.1px;height:109.2px;left:42px;bottom:-30.9px}.error-container>span.four:after{width:95.2px;height:30.9px;transform:rotate(-34.65deg);left:-12.6px;bottom:25.2px}.error-container>span.zero{width:109.2px;height:109.2px;border-radius:699.3px}.error-container>span.zero:before{transform:rotate(31.5deg);width:63px;height:63px}.error-container>span.zero:after{width:49px;height:49px;left:30.1px;bottom:30.1px}}.form{display:flex;justify-content:center}.m-notfound .m-header__search input{border:1px solid #0779f1;border-radius:5px}form{margin:0 auto}@media screen and (max-width:1024px){.m-notfound .m-header__search{display:block;width:90%}}
  </style>
  <section class="m-notfound">
    <h1>Không tìm thấy trang</h1>
    <section class="error-container">
      <span class="four"><span class="screen-reader-text">4</span></span>
      <span class="zero"><span class="screen-reader-text">0</span></span>
      <span class="four"><span class="screen-reader-text">4</span></span>
    </section>
    <form action="/" method="get" class="m-header__search">
      <input type="text" name="Tìm kiếm" id="search" value="Tìm kiếm" />
      <button class="btn_search"></button>
    </form>
    <div class="link-container">
      <a target="_blank" href="<?php echo get_home_url() ?>" class="more-link">Trang chủ</a>
    </div>
  </section>
<?php
get_footer();

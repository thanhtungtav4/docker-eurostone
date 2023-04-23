<section id="idx_contact">
    <div class="inner">
      <div class="box-left">
        <?php if( have_rows('main_contact') ): ?>
          <ul class="person-slider">
            <?php while( have_rows('main_contact') ): the_row();
              ?>
              <li class="items">
                <?php
                  handle_thumbnail_id(get_sub_field('image_contact'), 'TAX-STONE-THUMB', get_sub_field('name_contact'), true);
                ?>
                <div class="block">
                  <h4><?php the_sub_field('name_contact') ?><br><?php the_sub_field('sub_name_contact') ?></h4>
                  <div class="sec">
                    <?php the_sub_field('excerpt_contact') ?>
                  </div>
                </div>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php endif; ?>
      </div>
      <div class="box-right">
        <div class="form-contact">
          <h4 class="form-ttl">ĐẶT LỊCH HẸN VÀ TƯ VẤN TRỰC TIẾP</h4>
          <p class="form-txt">Quý khách vui lòng để lại thông tin để được tư vấn về sản phẩm và dịch vụ của Eurostone</p>
          <ul class="form-contact__cont">
            <li class="cus_input">
              <span class="required"></span>
              <input id="fullname" type="text" name="fullname" value="" placeholder="Họ và tên">
            </li>
            <li class="cus_input">
              <span class="required"></span>
              <input id="number" type="text" name="number" value="" placeholder="Điện thoại">
            </li>
            <li class="cus_input">
              <span class="required"></span>
              <input id="email" type="text" name="email" value="" placeholder="email">
            </li>
            <li class="cus_textarea">
              <textarea id="contentarea" name="" cols="30" rows="10" placeholder="nội dung"></textarea>
            </li>
            <li class="btn_gr">
              <div class="btn_submit">
                <input type="submit" value="<?php _e('SUBMIT EMAIL', 'eurostone'); ?>">
              </div>
              <span class="required"><?php _e('Required Information', 'eurostone'); ?></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
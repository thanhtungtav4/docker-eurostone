<?php 
    $faqs = get_field('brand-faq', $taxonomy_term);
    if($faqs) :
?>
<section id="dekton_box06"> 
    <div class="inner"> 
        <h3 class="c-title03"><?php single_term_title(); ?> - FAQs</h3>
        <div class="m-blockfqa">
            <?php 
                foreach($faqs as $key => $item) :
            ?>
            <dl> 
                <dt class="<?php  $key == 0 ? print 'active' : '' ?>"><?php ($item && $item['name_faq']) ? print $item['name_faq'] : '' ?></dt>
                <dd> 
                    <p><?php ($item && $item['description']) ? print $item['description'] : '' ?></p>
                </dd>
            </dl>
            <?php 
                endforeach;
            ?>
        </div>
    </div>
</section>
<?php 
    endif;
?>
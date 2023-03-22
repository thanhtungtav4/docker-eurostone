<li class="items">
    <a href="<?php the_permalink() ?>">
        <?php handle_thumbnail('STONE-THUMB', true) ?>
        <div class="sec">
            <h4><?php the_title(); ?></h4>
            <ul class="list-tag">
                <?php
                $terms= get_the_terms(get_the_ID(), 'made-in' );
                $posttags = get_the_tags();
                $terms ? print '<li>'. $terms[0]->name .'</li>' : '';
                if($posttags){
                    foreach($posttags as $tag) {
                        print '<li>'. $tag->name .'</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </a>
</li>
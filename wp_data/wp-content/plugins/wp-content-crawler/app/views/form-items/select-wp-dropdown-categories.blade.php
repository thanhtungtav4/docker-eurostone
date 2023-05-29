<?php
    /** @var string $name */
    $args['name'] = $name;
    $args['id'] = $name;
    $args['selected'] = isset($settings[$name]) ? (isset($isOption) && $isOption ? $settings[$name] : $settings[$name][0]) : false;
?>

<div class="input-group">
    <div class="input-container">
        <?php wp_dropdown_categories( $args ) ?>
    </div>
</div>
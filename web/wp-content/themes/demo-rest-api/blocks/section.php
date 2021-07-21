<?php
$bloc_class = 'section';
$bloc_style = '';
if (get_field('background_color') != 'custom') :
    $bloc_class .= ' bg-'.get_field('background_color');
else:
    $bloc_style .= 'background-color: '.get_field('custom_background_color').';';
endif;
$bloc_class .= ' text-'.get_field('text_color');
?>
<div class="<?php echo $bloc_class; ?>" style="<?php echo $bloc_style; ?>">
    <div class="container">
        <InnerBlocks />
    </div>
</div>

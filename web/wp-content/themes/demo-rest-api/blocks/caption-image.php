<?php
$image      = get_field('image');
$caption    = get_field('caption');
?>
<figure class="caption-image">
    <?php echo wp_get_attachment_image($image, 'full') ?>
    <figcaption><?php echo $caption ?></figcaption>
</figure>

<ul class="social">
    <?php if (get_field('facebook', 'option')) { ?>
    <li><a href="<?php echo get_field('facebook', 'option'); ?>" class="fb" target="_blank"><span class="sr-only"><?php _e('Suivez-nous sur Facebook', 'theme_name'); ?></span></a></li>
    <?php } ?>
    <?php if (get_field('twitter', 'option')) { ?>
    <li><a href="<?php echo get_field('twitter', 'option'); ?>" class="tw" target="_blank"><span class="sr-only"><?php _e('Suivez-nous sur Twitter', 'theme_name'); ?></span></a></li>
    <?php } ?>
    <?php if (get_field('linkedin', 'option')) { ?>
    <li><a href="<?php echo get_field('linkedin', 'option'); ?>" class="lk" target="_blank"><span class="sr-only"><?php _e('Suivez-nous sur Linkedin', 'theme_name'); ?></span></a></li>
    <?php } ?>
    <?php if (get_field('youtube', 'option')) { ?>
    <li><a href="<?php echo get_field('youtube', 'option'); ?>" class="yt" target="_blank"><span class="sr-only"><?php _e('Suivez-nous sur YouTube', 'theme_name'); ?></span></a></li>
    <?php } ?>
    <?php if (get_field('instagram', 'option')) { ?>
    <li><a href="<?php echo get_field('instagram', 'option'); ?>" class="ig" target="_blank"><span class="sr-only"><?php _e('Suivez-nous sur Instagram', 'theme_name'); ?></span></a></li>
    <?php } ?>
</ul>

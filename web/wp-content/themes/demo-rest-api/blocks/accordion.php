<div class="accordion-block accordion">
    <?php if (have_rows('sections')) : ?>
    <?php $i=0; ?>
    <?php while(have_rows('sections')): the_row() ?>
    <div class="card">
        <div class="card-header bg-primary text-white" id="accordeonHeading<?php echo $i; ?>" data-toggle="collapse" data-target="#collapse<?php echo $i; ?>" aria-expanded="<?php echo ($i==0) ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $i; ?>">
            <h5 class="mb-0">
                <?php the_sub_field('title'); ?>
                <span class="d-block"><?php the_sub_field('sub_title'); ?></span>
            </h5>
        </div>
        <div id="collapse<?php echo $i; ?>" class="collapse <?php if ($i==0) echo 'show'; ?>" aria-labelledby="accordeonHeading<?php echo $i; ?>">
            <div class="card-body text-dark">
                <?php the_sub_field('content'); ?>
            </div>
        </div>
    </div>
    <?php $i++; ?>
    <?php endwhile; ?>
    <?php endif; ?>
</div>

<style type="text/css" media="screen">
    <?php foreach ($forums as $forum): ?>
        .category_list li.forum_<?php echo $forum['clean_name'] ?> a.current { background:<?php echo $forum['active_color'] ?>; }
    <?php endforeach ?>
</style>
<?php if (isset($unfocus)): ?>
    <div style="opacity:0.5">
<?php else: ?>
    <div style="opacity:1">
<?php endif ?>
    <h4 style="border-bottom:1px solid #ddd; padding:5px 3px; color:#444">&nbsp;Town's Categories</h4>
    <ul class="category_list">
        <?php foreach ($forums as $forum): ?>
            <li class="forum_<?php echo $forum['clean_name'] ?>">
                <a href="<?php echo (isset($unfocus) ? '/community' : '') ?>#<?php echo $forum['id'] ?>" id="f<?php echo $forum['id'] ?>">
                    <?php echo image('community/'.$forum['icon'], 'width="16" height="16"') ?> <?php echo $forum['name'] ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>

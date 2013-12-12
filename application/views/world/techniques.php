 <div class="sub_world_title">
    <h3 class="left">The Arcane Library</h3>
    <a href="#" id="back_to_world" class="right">&lsaquo; Back to World</a>
</div>
<style type="text/css">
#listed_skills {
    overflow-y:auto;
    list-style:none;
    height:320px;
}

#listed_skills li {
    background:#191919;
    overflow:hidden;
    padding:10px;
    border:1px solid #333;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow:inset 0px 1px 6px 0px #000000;
    -moz-box-shadow:inset 0px 1px 6px 0px #000000;
    box-shadow:inset 0px 1px 6px 0px #000000;
    margin:5px 10px;
}
#listed_skills li img.main_thumbnail {
    float:left;
    margin-right:10px;
    margin-left:5px;
    width:40px;
    height:40px;
    border:1px solid #444;
    padding:3px;
    background:black;
}
.learn_skill {
    margin:2px 4px 0 0;
    display:block;
    padding:2px 11px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    background: #d9fbff; /* Old browsers */
    background: -moz-linear-gradient(top, #d9fbff 0%, #a5edfb 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#d9fbff), color-stop(100%,#a5edfb)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, #d9fbff 0%,#a5edfb 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, #d9fbff 0%,#a5edfb 100%); /* Opera11.10+ */
    background: -ms-linear-gradient(top, #d9fbff 0%,#a5edfb 100%); /* IE10+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d9fbff', endColorstr='#a5edfb',GradientType=0 ); /* IE6-9 */
    background: linear-gradient(top, #d9fbff 0%,#a5edfb 100%); /* W3C */    
    color:#0A5272;
    font-weight:bold;
    text-shadow:1px 1px 0 #fff;
    border:1px solid #ECF5FB;
    margin-bottom:3px;
    
    -webkit-transition: all 300ms ease;
    -moz-transition: all 300ms ease;
    -o-transition: all 300ms ease;
    transition: all 300ms ease;
}
.learn_skill:hover {
    background: #e3fbff; /* Old browsers */
    background: -moz-linear-gradient(top, #e3fbff 0%, #baeffc 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e3fbff), color-stop(100%,#baeffc)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, #e3fbff 0%,#baeffc 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, #e3fbff 0%,#baeffc 100%); /* Opera11.10+ */
    background: -ms-linear-gradient(top, #e3fbff 0%,#baeffc 100%); /* IE10+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e3fbff', endColorstr='#baeffc',GradientType=0 ); /* IE6-9 */
    background: linear-gradient(top, #e3fbff 0%,#baeffc 100%); /* W3C */
    color:#1375A8;
    text-decoration:none;
    -webkit-box-shadow: 0px 0px 7px 0px #AEF7FE;
    -moz-box-shadow: 0px 0px 7px 0px #AEF7FE;
    box-shadow: 0px 0px 7px 0px #AEF7FE;
}
.learn_skill:active {
    background: #75d2e9; /* Old browsers */
    background: -moz-linear-gradient(top, #75d2e9 0%, #9ee7f4 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#75d2e9), color-stop(100%,#9ee7f4)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, #75d2e9 0%,#9ee7f4 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, #75d2e9 0%,#9ee7f4 100%); /* Opera11.10+ */
    background: -ms-linear-gradient(top, #75d2e9 0%,#9ee7f4 100%); /* IE10+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#75d2e9', endColorstr='#9ee7f4',GradientType=0 ); /* IE6-9 */
    background: linear-gradient(top, #75d2e9 0%,#9ee7f4 100%); /* W3C */
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
}
.coins {
    width:12px;
    height:12px;
}
.locked {
    opacity:0.5;
}
</style>
<script type="text/javascript">
    $(document).ready(function(){
        var new_template;
        $(".learn_skill").die('click');
        $(".learn_skill").live('click', function(){
            $(".learn_skill").die('click');
            var skill_obj = $(this);
            $("#my_gold").decrease(skill_obj.parent().find('#price').text(), true);
            $.ajax({
                type: "POST",
                url: "/world/learn_technique",
                data: { id: skill_obj.attr('data_skill_id') },
                dataType: "json",
                success: function(json){
                    new_template = setTimeout(function(){
                        redirect('world');
                    }, 2000);
                    $("#world_contents").load(baseurl+"world/techniques", function(response, status, xhr) {
                        if (status == "error") {
                            redirect('world');
                        } else {
                            clearTimeout(new_template);
                        }
                    });
                }
            });
            return false;
        })
    });
</script>
<ul id="listed_skills">
    <?php foreach ($techniques as $technique): ?>
        <?php if (isset($character_skills[$technique['id']])): ?>
            <li id="<?php echo $technique['id'] ?>">
                <?=image('skill_icons/'.$technique['icon'], 'class="main_thumbnail"')?>
                <div class="left">
                    <h4><?php echo $technique['name'] ?> (lvl.<?php echo 1+$character_skills[$technique['id']]['skill_level'] ?>)</h4>
                    <p>Base <?php echo ($technique['target'] == 'monster' || $technique['target'] == 'all_monsters' ? 'damage' : 'heal points') ?>: <?php echo $technique['base_points'] ?></p>
                </div>
                <div class="right" style="text-align:center">
                    <span style="font-size:11px"><?php echo substr(human_time(date('c', $technique['time']+time())), 0, -9) ?> + <strong id="price"><?php echo $technique['price'] ?></strong> <?=image('coins.png', 'class="coins"')?></span>
                    <?php if ($this->system->userdata['gold'] >= $technique['price']): ?>
                        <a href="#" class="learn_skill" data_skill_id="<?php echo $technique['id'] ?>">+ Upgrade</a> 
                    <?php else: ?>
                        <br><span style="background-color: #9c9c9c;
                        background-image: -webkit-gradient(linear, left top, left bottom, to(rgba(156, 156, 156, 1.00)), from(rgba(182, 182, 182, 1.00)));
                        background-image: -webkit-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                        background-image: -moz-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                        background-image: -o-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                        background-image: -ms-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                        background-image: linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#9c9c9c', EndColorStr='#b6b6b6');
                        border:1px solid #bbb; color:#444; font-size:12px; padding:4px 9px; display:inline-block; margin-top:2px; -webkit-border-radius: 5px;
                        -moz-border-radius: 5px;
                        border-radius: 5px;">Not enough gold</span>
                    <?php endif ?>
                </div>
            </li>
        <?php else: ?>
            <?php if ($this->system->userdata['level'] >= $technique['min_level_required']): ?>
                <li id="<?php echo $technique['id'] ?>">
                    <?=image('skill_icons/'.$technique['icon'], 'class="main_thumbnail"')?>
                    <div class="left">
                        <h4><?php echo $technique['name'] ?> (lvl.1)</h4>
                        <p>Base <?php echo ($technique['target'] == 'monster' || $technique['target'] == 'all_monsters' ? 'damage' : 'healing') ?>: <?php echo $technique['base_points'] ?></p>
                    </div>
                    <div class="right" style="text-align:center">
                        <?php $time_algorithm = (pow(5, ($technique['scale']))+10)*60 ?>
                        <span style="font-size:11px"><?php echo substr(human_time(date('c', $technique['time']+time())), 0, -9) ?> + <strong id="price"><?php echo $technique['price'] ?></strong> <?=image('coins.png', 'class="coins"')?></span>
                        <?php if ($this->system->userdata['gold'] >= $technique['price']): ?>
                            <a href="#" class="learn_skill" data_skill_id="<?php echo $technique['id'] ?>">Learn skill</a> 
                        <?php else: ?>
                            <br><span style="background-color: #9c9c9c;
                            background-image: -webkit-gradient(linear, left top, left bottom, to(rgba(156, 156, 156, 1.00)), from(rgba(182, 182, 182, 1.00)));
                            background-image: -webkit-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                            background-image: -moz-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                            background-image: -o-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                            background-image: -ms-linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                            background-image: linear-gradient(top, rgba(156, 156, 156, 1.00), rgba(182, 182, 182, 1.00));
                            filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#9c9c9c', EndColorStr='#b6b6b6');
                            border:1px solid #bbb; color:#444; font-size:12px; padding:4px 9px; display:inline-block; margin-top:2px; -webkit-border-radius: 5px;
                            -moz-border-radius: 5px;
                            border-radius: 5px;">Not enough gold</span>
                        <?php endif ?>
                    </div>
                </li>
            <?php else: ?>
                <li class="locked">
                    <?=image('skill_icons/locked.png', 'class="main_thumbnail"')?>
                    <div class="left">
                        <h4>Locked Skill</h4>
                        <p>You must be in level <strong><?php echo $technique['min_level_required'] ?></strong> or higher to unlocked this skill</p>
                    </div>
                </li>
            <?php endif ?>
        <?php endif ?>
    <?php endforeach ?>
</ul>
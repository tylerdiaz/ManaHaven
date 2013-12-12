<script type="text/javascript">

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

    var battle_data = {
        wave: <?php echo $this->battle_engine->battle['wave_level'] ?>,
        battle_id: <?php echo $this->battle_engine->battle['id'] ?>,
        my_character_id: {my_character_id},
        monsters: <?php echo json_encode($js_monsters, JSON_NUMERIC_CHECK) ?>,
        total_monsters: <?php echo count($this->battle_engine->battle['monsters']) ?>,
        characters: <?php echo json_encode(array_keys($battle['characters']), JSON_NUMERIC_CHECK) ?>,
        multiplayer: <?php echo ($battle['battle_type'] === 'multiplayer' ? 'true' : 'false') ?>,
        multiplayer_host: <?php echo ($battle['creator_id'] === $user['id'] ? 'true' : 'false') ?>
    };
    
    var sit_down_idle;
    
    $(document).ready(function(){
        // Let's start the battles!
        if(battle_data.multiplayer === true){
            var timeout_i = 0;
            for (var i = battle_data.characters.length - 1; i >= 0; i--){
                
                setTimeout(function(){
                    animate.character.idle('#player_'+battle_data.characters[timeout_i]+'_sprite');
                    timeout_i++;
                }, (150*i));
            };
        } else {
            animate.character.idle('#player_'+battle_data.my_character_id+'_sprite');            
        }

        battle.show_action_menu();
        battle.create_monsters(battle_data['monsters']);
        battle.start_battle();
        rebind_actions();
    });
</script>

<?php echo script('mustache.js') ?>
<?php echo script('battle/battles.js?'.rand(1, 100000)) ?>
<?php echo script('battle/animations.js?'.rand(1, 100000)) ?>
<?php echo script('battle/gameplay.js?'.rand(1, 100000)) ?>

<?php if ($battle['battle_type'] === 'multiplayer'): ?>
    <?php echo script('battle/multiplayer.js?'.rand(1, 100000)) ?>
<?php endif ?>

<?php echo stylesheet('battles.css?v1.1') ?>

<div style="overflow:hidden">
    <div class="grid_5" style="position:relative;">
        <div id="battle_overview">
            <div id="techniques_overview">
                <h4>My techniques</h4>
                <ul class="quick_list" id="technique_list">

                    {my_skills}
                    <li>
                        <a href="#" data_skill_id="{id}" data_target="{target_type}">
                            <img src="/images/skill_icons/{icon}" alt="" width="32" height="32" />
                            <h5>{name}</h5>
                            <span>{description}</span>
                        </a>
                    </li>
                    {/my_skills}

                </ul>
            </div>
            <div id="items_overview">
                <h4>My backpack</h4>
                <ul class="quick_list" id="backpack_list">

                    {my_items}
                        <li id="backpack_item_{id}">
                            <a href="#" data_item_id="{id}">
                                <img src="/images/items/{thumb}" alt="" width="32" height="32" />
                                <h5>{name}</h5>
                                <span>x<span class="item_count" data_amount="{amount}">{amount}</span> left</span>
                            </a>
                        </li>
                    {/my_items}

                </ul>
            </div>
            <a href="#" id="toggle_overview">View my items &rsaquo;</a>
            <div id="cover_battle_overview_ui"></div>
            <canvas id="energy_timer" width="100" height="100"></canvas>
        </div>
        
        
        <div id="battlefield" style="background-image:url(/images/backgrounds/battle_bg.gif); overflow:hidden;">
            
            <canvas class="flame_canvas" id="" width="495" height="260"></canvas>
            
            <h3 id="wave_completed">
                <span>W</span><span>a</span><span>v</span><span>e</span> <span>#</span><span id="wave_completed_number">3</span>
                <span>C</span><span>o</span><span>m</span><span>p</span><span>l</span><span>e</span><span>t</span><span>e</span><span>d</span><span>!</span>
            </h3>
            
            <div id="battle_queue_dialog">
                This person used something!
            </div>
            
            <?php $character_offset = 40+(count($battle['characters'])*20); ?>
            <?php foreach ($battle['characters'] as $character_id => $character): ?>
                <div id="player_<?php echo $character_id ?>" class="idle player_container" style="top:90px; left:<?php echo $character_offset ?>px">
                    <div class="health_bar" data_total_hp="<?php echo $character['character_data']['max_hp'] ?>" data_current_hp="<?php echo $character['character_data']['hp'] ?>"><div style="width:<?php echo percent($character['character_data']['hp'], $character['character_data']['max_hp']) ?>%"></div></div>
                    <div class="player_avatar" id="player_<?php echo $character_id ?>_sprite" style="background-image:url(/images/avatars/sprites/<?php echo $character['character_user_id'] ?>.gif?<?php echo $user['last_saved_avatar'] ?>);"></div>
                    <span class="avatar_label"><?php echo $character['character_username'] ?></span>
                </div>
                <?php $character_offset += $character_offset/1.75; ?>
            <?php endforeach ?>
            
            <div id="monster_hive">
                
            </div>
            
            <div class="attack_choice">
            <? foreach($battle['monsters'] as $monster): ?>
                <a href="#" id="<?php echo $monster['id'] ?>"><?php echo $monster['monster_data']['name'] ?></a>
            <? endforeach; ?>
            </div>
            
            <a href="#" id="continue_waves">Advance to the next wave &raquo;</a>
            <a href="#" id="retreat_waves">&laquo; Retreat with Jackpot</a>
            
            <div id="jackpot_bonuses">
                <div class="jp_arrow"></div>
                <h4 style="text-align:center; margin-top:3px;">Victory bonuses</h4>
                <span id="jackpot_bonuses_rewards">
                    <span>Quick battle (+12 <?php echo image('coins.png', 'width="13" height="13"') ?>)</span><br>
                    <span>Bravery (+12 <?php echo image('coins.png', 'width="13" height="13"') ?>)</span><br>
                </span>
            </div>

        </div>
    </div>
    <div class="grid_1">
        <div id="jackpot" style="background-color: #fff3cb;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(255, 243, 203, 1.00)), to(rgba(255, 255, 255, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(255, 243, 203, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -moz-linear-gradient(top, rgba(255, 243, 203, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -o-linear-gradient(top, rgba(255, 243, 203, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -ms-linear-gradient(top, rgba(255, 243, 203, 1.00), rgba(255, 255, 255, 1.00));
        background-image: linear-gradient(top, rgba(255, 243, 203, 1.00), rgba(255, 255, 255, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#fff3cb', EndColorStr='#ffffff');
         padding:10px 5px; border:1px solid #E7DCB8; -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;">
            <img src="/images/world/icons/gold.png" alt="" class="left">
            <span>Jackpot</span>
            <h3 data_amount="<?php echo $battle['jackpot'] ?>"><?php echo $battle['jackpot'] ?></h3>
        </div>
        <br>
        <!-- <h4>Objectives (1/4)</h4>
        <ul class="objective_list">
            <li class="completed"><?php echo image('task_completed.png') ?> <p>Defeat 4 monsters</p></li>
            <li class="uncompleted"><?php echo image('task_uncompleted.png') ?> <p>Reach wave 4</p></li>
            <li class="uncompleted"><?php echo image('task_uncompleted.png') ?> <p>Jackpot over 100g</p></li>
            <li class="uncompleted"><?php echo image('task_uncompleted.png') ?> <p>Use an item</p></li>
        </ul> -->
        <br>
        <!-- <a href="#" id="sound" class="mini button" style="margin-left:6px"><span id="sound_icon" class="font_icon"><</span>&nbsp;&nbsp;Mute sounds</a> -->
		<!-- <div style="background:#FEEAC2; padding:5px 0; text-align:center; -webkit-border-radius: 6px;
		-moz-border-radius: 6px;
		border-radius: 6px;">
			Bug report key:
			<input type="text" value="Jumping Chicken" style="font-size:12px; border:none; background:white; color:teal; margin:0 5px 10px 5px; width:110px; padding:3px">
	        <a href="#" id="bug_report" class="mini button" style="margin-left:6px">Capture errors</a>
		</div> -->
<br><br>        
    </div>
    
    <div id="gold_hive">
        <!-- This is where all the flying coins get spawned in -->
    </div>
    <div id="misc_hive">
        <!-- This is where other chunks of animations get spawned into -->
    </div>
    <div id="animation_hive">
        <!-- This is where other chunks of skill animations get spawned into -->
    </div>
    
</div>

<div id="battle_progress">
    <?php foreach ($battle['characters'] as $character_id => $character): ?>
        <?php if ($battle['battle_type'] === 'multiplayer' && $character_id != $this->system->userdata['character_id']) continue; ?>
        <div class="clearfix battler_status left" id="<?php echo 'player_'.$character_id.'_sprite_status'; ?>">
            <span class="left"><?php echo $character['character_username'] ?></span>
            <span class="right"><?php echo $character['character_data']['hp'] ?>/<?php echo $character['character_data']['max_hp'] ?></span>
            <div class="health_condition"><div style="width:<?php echo percent($character['character_data']['hp'], $character['character_data']['max_hp']) ?>%"></div></div>
            <div class="energy_condition"><div></div></div>
        </div>
    <?php endforeach ?>
    
    <div id="monster_status_hive">
        
    </div>
</div>
<style type="text/css" media="screen">
    #wave_keys {
        float:left;
        margin:0 11px;
    }
    #wave_keys li {
        list-style:none;
        float:left;
        border:1px solid #666;
        color:#eee;
        width:16px;
        height:28px;
        margin:3px 2px;
        text-align:center;
        line-height:28px;
        font-size:12px;
        opacity:0.3;
        text-indent:-9999px;
        background-image:url(/images/locked_icon.png);
        background-repeat:no-repeat;
        background-position:center center;
    }
    
    #wave_keys li.boss {
/*        -webkit-box-shadow: 0px 0px 1px 1px #92B9D1;
        -moz-box-shadow: 0px 0px 1px 1px #92B9D1;
        box-shadow: 0px 0px 1px 1px #92B9D1;
*/        border:1px solid #888;
        margin-right:10px;
    }
    
    #wave1 { background:#072998; }
    #wave2 { background:#0C5790; }
    #wave3 { background:#127D88; }
    #wave4 { background:#16834B; }
    #wave5 { background:#0D6902; }
                       
    #wave6 { background:#5B7203; }
    #wave7 { background:#808305; }
    #wave8 { background:#7A6105; }
    #wave9 { background:#702F04; }
    #wave10 { background:#661404; }

    #wave11 { background:#3F0111; }
    #wave12 { background:#5C002E; }
    #wave13 { background:#710067; }
    #wave14 { background:#4A0068; }
    #wave15 { background:#1E005F; }
                        
    #wave16 { background:#1A033E; }
    #wave17 { background:#14012C; }
    #wave18 { background:#0D0019; }
    #wave19 { background:#09040E; }
    #wave20 { background:#000000; }
    
    #wave_keys li.completed_wave {
        opacity:1;
        -webkit-box-shadow: 0px 0px 0 2px #A5D08A;
        -moz-box-shadow: 0px 0px 0 2px #A5D08A;
        box-shadow: 0px 0px 0 2px #A5D08A;
        border:1px solid #000;
        color:#aaa;
        background-color: #2f2f2f;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(47, 47, 47, 1.00)), to(rgba(83, 83, 83, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(47, 47, 47, 1.00), rgba(83, 83, 83, 1.00));
        background-image: -moz-linear-gradient(top, rgba(47, 47, 47, 1.00), rgba(83, 83, 83, 1.00));
        background-image: -o-linear-gradient(top, rgba(47, 47, 47, 1.00), rgba(83, 83, 83, 1.00));
        background-image: -ms-linear-gradient(top, rgba(47, 47, 47, 1.00), rgba(83, 83, 83, 1.00));
        background-image: linear-gradient(top, rgba(47, 47, 47, 1.00), rgba(83, 83, 83, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#2f2f2f', EndColorStr='#535353');
        text-indent:0;
        background-image:none;
    }
    #wave_keys li.current_wave {
        opacity:1;
        -webkit-box-shadow: 0px 0px 2px 1px #333;
        -moz-box-shadow: 0px 0px 2px 1px #333;
        box-shadow: 0px 0px 2px 1px #333;
        color:white;
        border:1px solid white;
        font-weight:bold;
        text-indent:0;
        background-image:none;
    }
</style>
<div class="grid_5" style="margin:5px;">
    <h4 class="wave_badge" style="width:140px; float:left" id="wave<?php echo $this->battle_engine->battle['wave_level'] ?>">Wave #<?php echo $this->battle_engine->battle['wave_level'] ?></h4>
    <?php
        $total_waves = 20;
        $wave_i = 1;
        $current_wave = $this->battle_engine->battle['wave_level'];
    ?>
    <ul id="wave_keys">
        <?
        while($wave_i <= $total_waves):
            echo '<li id="wave'.$wave_i.'" class="'.($current_wave > $wave_i ? 'completed_wave' : '').' '.($current_wave == $wave_i ? 'current_wave' : '').' '.(($wave_i % 5 == 0) ? 'boss' : '').'">'.$wave_i.'</li>';
            $wave_i++;
        endwhile;
        ?>    
    </ul>
</div>

<script id="monster_status_tpl" type="x-tmpl-mustache">
    <div class="clearfix battler_status right monster_status" style="width:90px;" id="monster_{{id}}_sprite_status">
        <span class="left">{{name}}</span>
        <span class="right">{{hp}}/{{max_hp}}</span>
        <div class="health_condition"><div style="width:{{hp_percent}}%"></div></div>
        <div class="energy_condition"><div style="100%"></div></div>
    </div>
</script>

<script id="monster_sprite_tpl" type="x-tmpl-mustache">
    <div id="monster_{{id}}" class="idle enemy_container" style="top:100px; left:{{offset}}px; display:none">
        <div class="health_bar" data_total_hp="{{max_hp}}" data_current_hp="{{hp}}"><div style="width:{{hp_percent}}%"></div></div>
        <div class="monster_avatar" id="monster_{{id}}_sprite" style="width:65px; height:80px; background-image:url(/images/monsters/{{image}});"></div>
        <span class="avatar_label">{{name}}</span>
    </div>
</script>

<div style="position:absolute; background:transparent url(/images/level_up_shine.png)no-repeat; height:150px; width:340px; top:17px; margin:0 auto 0 508px; display:none;" id="level_up_shine"></div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
    });
</script>
<div style="position:absolute; background:transparent url(/images/level_up_shine.png)no-repeat; height:150px; width:340px; top:17px; margin:0 auto 0 508px; display:none;" id="level_up_shine"></div>
<script type="text/javascript">
    var battle_data = {
        wave: <?php echo $this->battle_engine->battle['wave_level'] ?>,
        my_character_id: {my_character_id},
        monsters: [ 
        {js_monsters}
        { 
            monster_id: {monster_id}, 
            battle_monster_id: {id},  
            recoil: {recoil},
            name: "{name}",
            hp: {hp},
            max_hp: {max_hp},
            image: "{image}"
        },
        {/js_monsters}
        ],
        total_monsters: <?php echo count($this->battle_engine->battle['monsters']) ?>
    };    
</script>
<?php echo script('battle/battles.js?'.rand(1, 100000)) ?>
<?php echo script('battle/animations.js?'.rand(1, 100000)) ?>
<?php echo script('battle/gameplay.js?'.rand(1, 100000)) ?>
<?php echo script('battle_components/rat.js?'.rand(1, 100000)) ?>
<style type="text/css">
    @font-face {
    	font-family: 'Pictos';
    	src: url('/fonts/pictos-web.eot');
    	src: local('☺'), url('/fonts/pictos-web.woff') format('woff'), url('/fonts/pictos-web.ttf') format('truetype'), url('/fonts/pictos-web.svg#webfontphKv1xv9') format('svg');
    	font-weight: normal;
    	font-style: normal;
    }

    .font_icon {
        display:inline-block;
        font-family: "Pictos", "Arial";
        font-weight:normal;
    }
    
    .flip_div {
        -moz-transform: scaleX(-1);
        -o-transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
        transform: scaleX(-1);
        filter: FlipH;
        -ms-filter: "FlipH";
    }

    .damage {
        color:#E65A2A; 
        font-size:18px; 
        text-shadow:1px 1px 0px rgba(255, 255, 255, 0.8),  
                    -1px -1px 0px rgba(255, 255, 255, 0.8), 
                    -1px 1px 0px rgba(255, 255, 255, 0.8), 
                    1px -1px 0px rgba(255, 255, 255, 0.8), 
                    2px 2px 2px #000;
        position:absolute;
        top:95px;
        right:155px;
        filter: dropshadow(color=#000000, offx=2, offy=2);
    }

    .exp_points {
        color:#728C10; 
        font-size:16px; 
        text-shadow:1px 1px 0px rgba(255, 255, 255, 0.8),  
                    -1px -1px 0px rgba(255, 255, 255, 0.8), 
                    -1px 1px 0px rgba(255, 255, 255, 0.8), 
                    1px -1px 0px rgba(255, 255, 255, 0.8), 
                    2px 2px 2px #000;
        position:absolute;
        top:95px;
        right:155px;
        filter: dropshadow(color=#000000, offx=2, offy=2);
    }
    
    #ready_headline {
        background: url('/global/styles/images/battle_headers.png') no-repeat;
        width:290px;
        height:48px;
        position:absolute;
        left:120px;
        top:90px;
        opacity:0;
    }
    #start_headline {
        background: url('/global/styles/images/battle_headers.png') no-repeat right bottom;
        width:290px;
        height:63px;
        position:absolute;
        left:60px;
        top:85px;
        opacity:0;
    }
    #cover_screen {
        background:rgba(0, 0, 0, 0.8);
        height:300px;
        width:500px;
        position:absolute;
    }
    #battle_queue_dialog {
        position:absolute;
        background:rgba(0, 0, 0, 0.7);
        color:#eee;
        margin:5px 10px;
        width:460px;
        padding:5px 9px 4px;
        text-align:center;
        font-size:15px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        border:1px solid black;
        display:none;
    }
</style>
<div style="overflow:hidden">
    <div class="grid_5" style="position:relative;">
        <div id="battle_overview">
            <div id="techniques_overview">
                <h4>My techniques</h4>
                <ul class="quick_list" id="technique_list">
                    {my_skills}
                    <li>
                        <a href="#" data_skill_id="{id}">
                            <img src="/images/skill_icons/{icon}" alt="" width="32" height="32" />
                            <h5>{name}</h5>
                            <span>{min_damage}-{max_damage} damage</span>
                        </a>
                    </li>
                    {/my_skills}
                </ul>
            </div>
            <div id="items_overview">
                <h4>My backpack</h4>
                <ul class="quick_list" id="technique_list">
                    {my_items}
                        <li>
                            <a href="#" data_item_id="{id}">
                                <img src="/images/items/{thumb}" alt="" width="32" height="32" />
                                <h5>{name}</h5>
                                <span>x{amount} left</span>
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
            
            <div id="battle_queue_dialog">
                This person used something!
            </div>
            
            <div id="player_<?php echo $battle['character']['id'] ?>" class="idle player_container" style="top:90px; left:80px">
                <div class="health_bar" data_total_hp="<?php echo $battle['character']['character_data']['max_hp'] ?>" data_current_hp="<?php echo $battle['character']['character_data']['hp'] ?>"><div style="width:<?php echo percent($battle['character']['character_data']['hp'], $battle['character']['character_data']['max_hp']) ?>%"></div></div>
                <div class="player_avatar" id="player_sprite" style="background-image:url(/images/avatars/sprites/<?php echo $user['id'] ?>.gif);"></div>
                <span class="avatar_label"><?php echo $user['username'] ?></span>
            </div>
            
            <div id="monster_hive">
                <!-- In here will go the monsters! -->
                <? $offset = 300; ?>
                <? foreach($battle['monsters'] as $monster): ?>
                <? $offset += 50; ?>
                    <div id="monster_<?php echo $monster['id'] ?>" class="idle enemy_container" style="top:100px; left:<?php echo $offset ?>px; display:none;">
                        <div class="health_bar" data_total_hp="<?php echo $monster['monster_data']['max_hp'] ?>" data_current_hp="<?php echo $monster['monster_data']['hp'] ?>"><div style="width:<?php echo percent($monster['monster_data']['hp'], $monster['monster_data']['max_hp']) ?>%"></div></div>
                        <div class="monster_avatar" id="monster_<?php echo $monster['id'] ?>_sprite" style="width:65px; height:80px; background-image:url(images/monsters/<?php echo $monster['monster_data']['image'] ?>);"></div>
                        <span class="avatar_label"><?php echo $monster['monster_data']['name'] ?></span>
                    </div>
                <? endforeach; ?>
            </div>

            <style type="text/css">
                .attack_choice {
                    position:absolute; 
                    color:#fff; 
                    margin-top:10px;
                    background:#222;
                    min-width:120px;
                    border:1px solid #555;
                    border-left:2px solid #111;
                    -moz-border-radius-topleft: 0px;
                    -moz-border-radius-topright: 5px;
                    -moz-border-radius-bottomright: 5px;
                    -moz-border-radius-bottomleft: 0px;
                    -webkit-border-radius: 0px 5px 5px 0px;
                    border-radius: 0px 5px 5px 0px;
                    -webkit-box-shadow: 1px 1px 2px 0px #000000;
                    -moz-box-shadow: 1px 1px 2px 0px #000000;
                    box-shadow: 1px 1px 2px 0px #000000;
                    overflow:hidden;
                    display:none;
                }
                .attack_choice a {
                    display:block;
                    padding:7px 12px;
                    border-bottom:1px dotted #555;
                }
                .attack_choice a:hover {
                    background:#151515;
                }
                .attack_choice a:last-child {
                    border-bottom:none;
                }
                
                #jackpot {
                    line-height:1.1;
                }
                #jackpot span {
                    font-size:11px;
                    text-transform:uppercase;
                    font-weight:bold;
                    color:#8D6505;
                }
                #jackpot span {
                    font-size:11px;
                    text-transform:uppercase;
                    font-weight:bold;
                    color:#8D6505;
                }
                #jackpot h3 {
                    font-size:24px;
                    color:#785403;
                }
                .flying_coin {
                    position:absolute;
                    top:50px;
                    left:630px;
                    z-index:999;
                }
                
                #retreat_waves {
                    background:rgba(0, 0, 0, 0.4); color:white; display:block; float:left; font-size:12px; padding:7px 6px 7px 11px; margin-top:95px; max-width:80px; margin-left:-300px;
                }

                #continue_waves {
                    background:rgba(0, 0, 0, 0.8); color:white; display:block; float:right; font-size:16px; padding:7px 12px 7px 18px; margin-top:100px; -moz-border-radius-topleft: 4px;
                    -moz-border-radius-topright: 0px;
                    -moz-border-radius-bottomright: 0px;
                    -moz-border-radius-bottomleft: 4px;
                    -webkit-border-radius: 4px 0px 0px 4px;
                    border-radius: 4px 0px 0px 4px; margin-right:-300px;
                }
            </style>
            
            <div class="attack_choice">
            <? foreach($battle['monsters'] as $monster): ?>
                <a href="#" id="<?php echo $monster['id'] ?>"><?php echo $monster['monster_data']['name'] ?></a>
            <? endforeach; ?>
            </div>
            
            <a href="#" id="retreat_waves">
                &laquo; Retreat with Jackpot
            </a>
            <a href="#" id="continue_waves">
                Advance to the next wave &raquo;
            </a>
            
        </div>
    </div>
    <div class="grid_1">
        <h4 class="wave_badge">Wave #<?php echo $this->battle_engine->battle['wave_level'] ?></h4>
        <div id="jackpot">
            <img src="/images/world/icons/gold.png" alt="" class="left">
            <span>Jackpot</span>
            <h3 data_amount="<?php echo $battle['jackpot'] ?>"><?php echo $battle['jackpot'] ?></h3>
        </div>
        <br><br>
        <a href="#" id="sound" class="mini button" style="margin-left:6px"><span id="sound_icon" class="font_icon"><</span>&nbsp;&nbsp;Mute sounds</a>
        <br><br>
    </div>
    <div id="gold_hive">
        <!-- This is where all the flying coins get spawned in -->
    </div>
    <div id="misc_hive">
        <!-- This is where other chunks of animations get spawned into -->
    </div>

</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        animate.character.idle('#player_sprite');            
        
        battle.show_action_menu();
        
        // There could be multiple enemy containers, and fading them normally would cause some incosistencies.
        var ajax_action_lock = 0;
        
        if(ajax_action_lock == 0){
            battle.create_monsters(battle_data['monsters']);
            ajax_action_lock = 1; // Lock it next time
        }

        $(".enemy_container").fadeIn(500, function(){
            // Create the monsters!
        });
        
        battle.start_battle();
    });
</script>
<style type="text/css" media="screen">
    #battle_progress {
        height:35px;
        padding:5px 10px;
        margin:-45px 100px 0 160px;
        width:440px;
        position:absolute;
        border-top:2px solid #000;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 0, 0, 0.90)), to(rgba(0, 0, 0, 0.00)));
        background-image: -webkit-linear-gradient(top, rgba(0, 0, 0, 0.90), rgba(0, 0, 0, 0.00));
        background-image: -moz-linear-gradient(top, rgba(0, 0, 0, 0.90), rgba(0, 0, 0, 0.00));
        background-image: -o-linear-gradient(top, rgba(0, 0, 0, 0.90), rgba(0, 0, 0, 0.00));
        background-image: -ms-linear-gradient(top, rgba(0, 0, 0, 0.90), rgba(0, 0, 0, 0.00));
        background-image: linear-gradient(top, rgba(0, 0, 0, 0.90), rgba(0, 0, 0, 0.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#000000', EndColorStr='#000000');
    }
    .battler_status {
        color:#ddd;
        font-size:11px;
        width:120px;
        line-height:1.2;
        margin:0 4px 0 0;
    }
    
    .battler_status .health_condition,  .battler_status .energy_condition{
        background:#310001; border:1px solid #5B3A3D; height:8px; clear:both; -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px; margin-bottom:1px
    }
    .battler_status .health_condition div {
        background-color: #ec2726;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(236, 39, 38, 1.00)), to(rgba(189, 18, 5, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(236, 39, 38, 1.00), rgba(189, 18, 5, 1.00));
        background-image: -moz-linear-gradient(top, rgba(236, 39, 38, 1.00), rgba(189, 18, 5, 1.00));
        background-image: -o-linear-gradient(top, rgba(236, 39, 38, 1.00), rgba(189, 18, 5, 1.00));
        background-image: -ms-linear-gradient(top, rgba(236, 39, 38, 1.00), rgba(189, 18, 5, 1.00));
        background-image: linear-gradient(top, rgba(236, 39, 38, 1.00), rgba(189, 18, 5, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ec2726', EndColorStr='#bd1205');
         width:80%; height:100%
    }
    
    .battler_status .energy_condition { background:#04232A; border:1px solid #334C52; height:4px; }
    .battler_status .energy_condition div { background-color: #ec2726;
    background-color: #007ee9;
    background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 126, 233, 1.00)), to(rgba(0, 41, 187, 1.00)));
    background-image: -webkit-linear-gradient(top, rgba(0, 126, 233, 1.00), rgba(0, 41, 187, 1.00));
    background-image: -moz-linear-gradient(top, rgba(0, 126, 233, 1.00), rgba(0, 41, 187, 1.00));
    background-image: -o-linear-gradient(top, rgba(0, 126, 233, 1.00), rgba(0, 41, 187, 1.00));
    background-image: -ms-linear-gradient(top, rgba(0, 126, 233, 1.00), rgba(0, 41, 187, 1.00));
    background-image: linear-gradient(top, rgba(0, 126, 233, 1.00), rgba(0, 41, 187, 1.00));
    filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#007ee9', EndColorStr='#0029bb');
    
     width:100%; height:100% }
</style>
<div id="battle_progress">
    <div class="clearfix battler_status left" id="player_sprite_status" id="<?php ($battle['character']['id'] == $this->session->userdata('id') ? 'player_sprite_status' : 'player_sprite_'.$battle['character']['id'].'_status') ?>">
        <span class="left"><?php echo $user['username'] ?></span>
        <span class="right"><?php echo $battle['character']['character_data']['hp'] ?>/<?php echo $battle['character']['character_data']['max_hp'] ?></span>
        <div class="health_condition"><div style="width:<?php echo percent($battle['character']['character_data']['hp'], $battle['character']['character_data']['max_hp']) ?>%"></div></div>
        <div class="energy_condition"><div style=""></div></div>
    </div>
    <?php $battle['monsters'] = array_reverse($battle['monsters']) ?>
    <? foreach($battle['monsters'] as $monster): ?>
    <div class="clearfix battler_status right monster_status" style="width:90px;" id="monster_<?php echo $monster['id'] ?>_sprite_status">
        <span class="left"><?php echo $monster['monster_data']['name'] ?></span>
        <span class="right"><?php echo $monster['monster_data']['hp'] ?>/<?php echo $monster['monster_data']['max_hp'] ?></span>
        <div class="health_condition"><div style="width:<?php echo percent($monster['monster_data']['hp'], $monster['monster_data']['max_hp']) ?>%"></div></div>
        <div class="energy_condition"><div style="100%"></div></div>
    </div>
    <? endforeach; ?>
</div>
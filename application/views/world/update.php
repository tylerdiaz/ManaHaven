<style type="text/css" media="screen">
@font-face {
  font-family: 'Lora';
  font-style: normal;
  font-weight: bold;
  src: local('Lora Bold'), local('Lora-Bold'), url('/fonts/Lora-Bold.woff') format('woff');
}
#world_navigation {
    background:url(<?php echo site_url('images/navigation_parchment.jpg'); ?>)no-repeat left top;
    overflow:hidden;
    width:250px;
    height:386px;
    padding:2px;
}
#world_navigation li {
    list-style:none;
    overflow:hidden;
    position:relative;
}
#world_navigation li a {
    font-family:Lora;
    font-size:26px;
    display:block;
    padding:0 10px;
    height:63px;
    line-height:63px;
    color:white;
    padding-left:68px;
    text-shadow:1px 1px 4px rgba(0, 0, 0, 0.5);
}
#world_navigation li a:hover {
    background-color:rgba(255, 255, 255, 0.1);
    text-decoration:none;
}
#world_navigation li a:active {
    background-color:rgba(0, 0, 0, 0.1);
    text-shadow:none;
    -webkit-box-shadow: inset 0px 0px 12px 0px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: inset 0px 0px 12px 0px rgba(0, 0, 0, 0.2);
    box-shadow: inset 0px 0px 12px 0px rgba(0, 0, 0, 0.2);
}
#world_navigation li a:first-child {
    height:62px;
    line-height:62px;
}
.active_parchment {
    background:url(/images/active_parechment_navigation.png); ?>)no-repeat left top;
    background-color:rgba(255, 255, 255, 0.1);
}
.active_parchment:hover {
    background-color:transparent;
}
#world_navigation li.active_parchment a {
    color:#D8FF7C;
}
#world_navigation li.coming_soon a { text-shadow:-1px -1px 1px rgba(0, 0, 0, 0.4);  }
#world_navigation li.coming_soon a:hover { background-color:transparent; cursor:default; }
#world_navigation li.coming_soon { opacity:0.5; }
#world_navigation li.coming_soon span { position:absolute; top:10px; right:15px; color:#FDD995; font-size:12px; display:block; font-weight:bold; text-shadow:1px 1px 3px #000; background:rgba(0, 0, 0, 0.6); padding:1px 4px; -webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px; }

#techniques a { background:url(/images/world/icons/technique.png)no-repeat 17px 10px; }
#shops a { background:url(/images/world/icons/shop.png)no-repeat 17px 10px; }
#multiplayer a { background:url(/images/world/icons/multi.png)no-repeat 17px 10px; }
#forge a { background:url(/images/world/icons/anvil.png)no-repeat 17px 13px; }
#story a { background:url(/images/world/icons/story.png)no-repeat 17px 10px; }
#scavenger a { background:url(/images/world/icons/scavenge.png)no-repeat 17px 12px; }
#inventory a { background:url(/images/world/icons/bag.png)no-repeat 17px 12px; }
/*
 * Filled with hacks, this needs to be re-written
*/
#back_arrow {
    background:transparent url(/images/back_arrow.png)no-repeat; width:32px; height:32px; display:block; float:left; margin-right:10px;
}
#back_arrow:hover {
    background:transparent url(/images/back_arrow.png)no-repeat left bottom;
}

#play_multiplayer {
    -webkit-animation-name: call_to_action;
	-webkit-animation-duration: 4s;
	-webkit-animation-iteration-count: infinite;	
}

@-webkit-keyframes call_to_action {
    0% { -webkit-box-shadow: none }
    50% { -webkit-box-shadow: 0px 0px 12px 0px rgba(255, 255, 0, 0.5) }
    100% { -webkit-box-shadow: none }
}

.sub_world_title { overflow:hidden; line-height:36px; padding:1px 15px; background:rgba(0, 0, 0, 0.5); -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; }
</style>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        
        var tip_multiplayer = "";

        function reload_main_world(){
            $("#avatar_box img.left").animate({ opacity: 1 })
            $("#world_navigation li").removeClass('active_parchment');
            $("#world_contents").load(baseurl+"world #world_contents", function(){
                $("#parent_content").css('background-image', 'url(/images/backgrounds/world_gauntlet_bg.jpg)');
            });
        }
        
        $("#back_to_world").live('click', function(){
            reload_main_world();
            return false;
        })
        
        $("#world_navigation li").live('click', function(){
            var nav = $(this);
            if(nav.hasClass('coming_soon')){
                alert('This part of the game is under development, it\'ll be coming soon!');
                return false;
            } else {
                if(nav.hasClass('active_parchment')){
                    reload_main_world();
                    return false;
                } else {
                    $("#world_navigation li").removeClass('active_parchment')
                    nav.addClass('active_parchment');
                    $("#parent_content").css('background-image', 'url(/images/world_bg.jpg)');
                    $("#world_contents").load(baseurl+nav.attr('ajax'));
                    return false;
                }
            }
        });
        
        $("#full_heal").die('click');
        $("#full_heal").live('click', function(){
            var button_obj = $("#full_heal");
            if($(".active_energy_token").length > 0){
                $.ajax({
                    type: "POST",
                    url: "/aether/full_heal",
                    dataType: "json",
                    success: function(json){
                        button_obj.hide();
                        button_obj.parent().find('div').animate({ width: "99%", backgroundColor: "#2E9003"});
                        $("#energy_stash").append('<img src="/images/energy_token_inactive.png" width="16" height="16" class="inactive_energy_token" title="Inactive energy token" /> ');
                        $(".active_energy_token").last().remove();
                        $("#current_hp").text(json.hp);
                    },
                    error: function(xhr, status, error){
                        alert("AJAX error "+error+". Status: "+status);
                    }
                });
            }
            return false;
        });
        
        $("#disabled_multiplayer").live('click', function(){
            return false;
        });

		/*
		 * Shops!
		*/
		
		$(".toggle_raw_preview").live('click', function(){
	        var toggle_obj = $(this);
	        var toggle_obj_parent = toggle_obj.parent();
	        if( ! toggle_obj.hasClass('toggled_raw_preview')){
	            toggle_obj.addClass('toggled_raw_preview');
	            toggle_obj_parent.find('canvas').hide();
	            if(toggle_obj_parent.find('img').length < 1){
	                toggle_obj_parent.append('<img src="/avatar/preview_item/'+toggle_obj.attr('data_item_id')+'" alt="">')
	            } else {
	                toggle_obj_parent.find('img').show();
	            }
	        } else {
	            toggle_obj.removeClass('toggled_raw_preview');
	            toggle_obj_parent.find('img').hide();
	            toggle_obj_parent.find('canvas').show();
	        }
	        return false;
	    });
	
		$(".purchase_shop_item").live('click', function(){
	        var item_name = $(this).attr('data_item_name');
	        $.ajax({
	            type: "POST",
	            url: '/world/purchase_item/',
	            data: { item_id: $(this).attr('data_id') },
	            cache: false,
	            async: true,
	            dataType: "json",
	            success: function(json){
	                if(json.success){
	                    $("#my_gold").decrease(json.reduction, true);
	                    if(json.type == 'usable'){
	                        popup.create({
	                            title: "You successfully purchased a "+item_name,
	                            content: "Awesome! If you'd like, we can take you to your inventory, or you can continue to shop around for other things you like!",
	                            cancel_button: { label: 'Stay in shop' },
	                            confirm_button: { label: 'Go to inventory &rsaquo;', callback: function(){
	                                redirect('/avatar');
	                            }}
	                        }); // end popup
	                    } else {
	                        popup.create({
	                            title: "You now own a "+item_name,
	                            content: "Your item has been successfully purchased! Would you want to go and wear your new item or continue shopping around?",
	                            cancel_button: { label: 'Stay in shop' },
	                            confirm_button: { label: 'Equip it now &rsaquo;', callback: function(){
	                                redirect('/avatar');
	                            }}
	                        }); // end popup
	                    }
	                } else {
	                    alert('An error occurred purchasing this item, please report this to a developer!')
	                }
	            },
	            error: function(xhr, status, error){
	                alert("AJAX error "+error+". Status: "+status);
	            }
	        });
	        return false;
	    });

    });
</script>
<style type="text/css">
    #play_multiplayer {
        background:url(/images/multiplayer_button.png) left top;
        width:201px; 
        height:55px; 
        display:block;
        text-indent:-9999px;
        margin:50px 0 0 5px;
    }
    #play_multiplayer:hover {
        background-position:left center;
    }
    #play_multiplayer:active {
        background-position:left bottom;
    }
    #disabled_multiplayer {
        background:url(/images/disabled_multiplayer_button.png) left top;
        width:201px; 
        height:55px; 
        display:block;
        text-indent:-9999px;
        margin:50px 0 0 5px;
        cursor:default;
    }
    #play_singleplayer {
        display:block;
        margin-top:35px;
        font-weight:bold;
        color:#CAB35C;
        text-decoration:none;
    }
    #play_singleplayer:hover {
        display:block;
        margin-top:35px;
        font-weight:bold;
        text-decoration:underline;
        color:#E6E0C0;
    }
    .compressed_friends {
        opacity:0.6;
    }
    .compressed_friends li {
        list-style:none;
        float:left;
        margin:0 -15px 0 -5px;
    }
    #parent_content {
        background:#222 url(/images/backgrounds/world_gauntlet_bg.jpg) left top; 
        padding:5px 0; 
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px; 
        color:#ddd; 
        position:relative;
    }
    .widget_sheet {
        float:left; 
        width:234px; 
        height:250px; 
        margin:100px 0 0 30px;
        color:#bbb;
    }
    .gold_text {
        color:#B8A106;
    }
    #world_navigation .notification_bubble {
        position:absolute; 
        top:15px; 
        right:45px; 
        color:#fff; 
        font-size:12px; 
        display:block; 
        font-weight:bold; 
        text-shadow:1px 1px 3px #000; 
        background:red; 
        padding:0px 5px; 
        -webkit-border-radius: 12px;
        -moz-border-radius: 12px;
        border-radius: 12px;
        text-align:center;
    }
    #full_heal {
        position:absolute; top:4px; left:4px; text-align:center; width:120px; font-size:12px;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(221, 172, 111, 1.00)), to(rgba(195, 137, 66, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(221, 172, 111, 1.00), rgba(195, 137, 66, 1.00));
        background-image: -moz-linear-gradient(top, rgba(221, 172, 111, 1.00), rgba(195, 137, 66, 1.00));
        background-image: -o-linear-gradient(top, rgba(221, 172, 111, 1.00), rgba(195, 137, 66, 1.00));
        background-image: -ms-linear-gradient(top, rgba(221, 172, 111, 1.00), rgba(195, 137, 66, 1.00));
        background-image: linear-gradient(top, rgba(221, 172, 111, 1.00), rgba(195, 137, 66, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ddac6f', EndColorStr='#c38942');
         color:white; text-shadow:1px 1px 0 rgba(0, 0, 0, 0.4); -webkit-border-radius: 1px; -moz-border-radius: 1px; border-radius: 1px;
    }
</style>
<div class="clearfix" id="parent_content">
    <div class="grid_4" style="height:390px;">
        <div id="world_contents">
            <div style="height:390px;">
                <div class="widget_sheet">
                    <div style="height:75px; padding:5px 10px; font-size:13px;" class="clearfix">
                        <strong>Your battle information:</strong>
                        <div style="float:left; width:130px; margin:5px 10px 0 0;">
                            <strong class="clearfix" style="display:block;">
                                <span class="left">HP:</span>
                                <span class="right"><span id="current_hp"><?php echo $user['hp'] ?></span>/<?php echo $user['max_hp'] ?></span>
                            </strong>
                            <?php
                                $hp_percent = percent($user['hp'], $user['max_hp']);

                                if($hp_percent > 75):
                                    $hp_color = "#2E9003";
                                elseif($hp_percent <= 75 && $hp_percent >= 50):
                                  $hp_color = "#7CA706";
                                elseif($hp_percent <= 50 && $hp_percent >= 25):
                                    $hp_color = "#B69508";
                                elseif($hp_percent < 25):
                                    $hp_color = "#9E000A";
                                endif;
                            
                            ?>
                            <div style="background:black; height:24px; border:1px solid #444; position:relative; padding:1px">
                                <div style="height:24px; width:<?php echo ($hp_percent-1) ?>%; background:<?php echo $hp_color ?>; position:absolute; left:1px; top:1px">
                                    
                                </div>
                                <?php if ($user['hp'] <= 0 && $user['energy'] > 0): ?>
                                    <a href="#" id="full_heal">Use energy to heal</a>
                                <?php endif ?>
                            </div>
                        </div>
                        <div style="float:left; width:71px; margin-top:8px; line-height:21px; margin-left:3px;">
                            <strong id="wins">Wins: <span style="color:#57B704"><?php echo $user['battles_won'] ?></span></strong>
                            <strong id="wins">Losses: <span style="color:#F74E63"><?php echo $user['battles_lost'] ?></span></strong>
                        </div>
                    </div>
                    <div style="height:82px; font-size:13px;">
                        <?php echo image('avatars/'.$champion['user_id'].'.gif', 'width="90" height="90" style="margin:-12px -15px 0 5px; float:left;"') ?>
                        <div style="font-size:12px; color:#ccc; margin:15px 0 0 5px; float:left; font-weight:bold;">Today's Champion is...</div>
                        <h3 style="text-align:center; float:left; width:140px; margin-top:3px; font-size:17px;">
                            <?php echo anchor('/profile/'.urlencode($champion['username']), $champion['username'], 'class="gold_text"') ?>
                        </h3>
                    </div>
                    <div style="min-height:70px; font-size:13px; padding:10px 10px 5px 10px; text-align:center;" id="energy_stash">
                        <strong>Energy tokens Left:</strong><br>
                        <?php
                            $energy = $user['energy'];
                        ?>
                        <?php $i = 0; while($i < $user['energy']): ?>
                            <?php echo image('energy_token_active.png', 'width="16" height="16" class="active_energy_token" title="Active energy token"') ?>
                        <?php $i++; endwhile; ?>
                        <?php while($energy < $user['max_energy']): ?>
                            <?php echo image('energy_token_inactive.png', 'width="16" height="16" class="inactive_energy_token" title="Inactive energy token"') ?>
                        <?php $energy++; endwhile; ?>
                    </div>
                </div>
                <div style="float:left; width:210px; height:250px; margin:100px 0 0 20px; text-align:center;">
                    <?php echo anchor('world#', 'Play Multiplayer', 'id="disabled_multiplayer"'); ?>
                    <?php echo anchor('world/singleplayer', 'Venture on your own', 'id="play_singleplayer"'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="grid_2" style="height:386px;">
        <ul id="world_navigation">
            <li id="shops" ajax="world/shop"><a href="#">Shops</a></li>
            <li id="techniques" ajax="world/techniques"><a href="#">Techniques</a></li>
            <li class="coming_soon" id="forge"><a href="#">Forge</a> <span>Coming soon!</span></li>
            <li class="coming_soon" id="multiplayer"><a href="#">Quests</a> <span>Coming soon!</span></li>
            <li id="story" ajax="world/character"><a href="#">Character</a> <?php echo ($this->system->userdata['skill_points'] > 0 ? '<span class="notification_bubble">'.$this->system->userdata['skill_points'].'</span>': '' ) ?></li>
            <li class="coming_soon" id="scavenger"><a href="#">Adventure</a> <span>Coming soon!</span></li>
        </ul>
    </div>
</div>
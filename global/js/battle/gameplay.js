// Preload some of the most common sounds. :D
var buzz_preferences = {
    formats: ["mp3", "ogg"],
    preload: true,
    autoload: false,
    loop: false
}

var errors = {};
var action_lock = false;

function rebind_actions(){
    if(actions_binded == false){
        actions_binded = true;
        
        $(".attack_choice a").live('click', function(){
            var post_data = { skill_id: list_skill_id, target_id: $(this).attr('id') };
            $(".attack_choice").hide(0);
            $.ajax({
                type: "POST",
                url: "/battle/use_skill/",
                data: post_data,
                cache: false,
                async: true,
                dataType: "json",
                success: function(json){
                    if(typeof json.error == 'undefined'){
                        battle.process_json(json);
                	    action_lock = false;
                    } else {
                        if(json.error == 39){
                            $("#quick_success").css({backgroundColor: "#710000"}).html('An error has occured, refreshing in 5 seconds!').fadeIn(300);
                            setTimeout(function(){
                                redirect('battle');
                            }, 5000);
                        } else {
                            console.log('Not ready yet!');
                        }
                	    action_lock = false;
                    }
                }
            });
            
            return false;
    	});

        $("#backpack_list li a").bind('click', function(e){
            this_obj = $(this);
            data_item_id = this_obj.attr('data_item_id');
            this_obj.find('.item_count').decrease(1);
            
            // Is this our last one?
            if(parseInt(this_obj.find('.item_count').attr('data_amount')) < 1){
                this_obj.parent().fadeOut(200);
            }
            
            battle.sounds.play_sound('select');
            $("#toggle_overview").click();
            
            $.ajax({
                type: "POST",
                url: "/battle/use_item/",
                data: { item_id: data_item_id },
                dataType: "json",
                success: function(json){
                    if(typeof json.error == 'undefined'){
                        battle.process_json(json);
                	    action_lock = false;
                    } else {
                        action_lock = false;
                        console.log('Not ready yet!');
                    }
                }
            });
            
            e.preventDefault();
        });
    	
        
    	$("#technique_list li a").bind('click', function(e){
    	    if(action_lock) return false;

    	    action_lock = true;
            this_obj = $(this);
            list_skill_id = this_obj.attr('data_skill_id');
            skill_target = this_obj.attr('data_target');
            
            battle.sounds.play_sound('select');
            
            switch(skill_target){
                case 'monster':
                    // Only one monster left!
                    if(battle_data['total_monsters'] === 1 || battle.auto_select_monster === true){
                        if(battle.auto_select_monster === true){
                            $(".attack_choice").fadeOut(200); 
                        }

                        // Let's get the active monster id, and remember it for next time.
                        if(battle.active_monster_key === false){
                            $.each(battle_data['monsters'], function(key, value){
                                if(value !== false){
                                    battle.active_monster_key = key;
                                }
                            });
                        }

                        // Only one monster is possible to damage, so let's skip the logistics
                        var post_data = { 
                            skill_id: list_skill_id, 
                            target_id: battle_data['monsters'][battle.active_monster_key]['battle_monster_id'] 
                        };

                        $.ajax({
                            type: "POST",
                            url: "/battle/use_skill/",
                            data: post_data,
                            cache: false,
                            async: true,
                            dataType: "json",
                            success: function(json){
                                if(typeof json.error == 'undefined'){
                                    battle.process_json(json);
                            	    action_lock = false;
                                } else {
                                    if(json.error == 39){
                                        $("#quick_success").css({backgroundColor: "#710000"}).html('An error has occured, refreshing in 5 seconds!').fadeIn(300);
                                        setTimeout(function(){
                                            redirect('battle');
                                        }, 5000);
                                    } else {
                                        console.log('Not ready yet!');
                                    }
                                    action_lock = false;
                                }
                            },
                            error: function(){
                                action_lock = false;
                            }
                        });
                    } else {
                        // Prompt the user which monster he/she wants to hit, then tie the event to another ajax event
                        $(".attack_choice").fadeToggle(200);
                	    action_lock = false;
                    }
                break;
                case 'character':
                    // Only one monster left!
                    //if(battle_data['total_monsters'] == 1){
                       // Let's get the active monster id, and remember it for next time.

                       // Only one monster is possible to damage, so let's skip the logistics
                       var post_data = { 
                           skill_id: list_skill_id, 
                           target_id: battle_data['my_character_id']
                       };

                        $.ajax({
                            type: "POST",
                            url: "/battle/use_skill/",
                            data: post_data,
                            cache: false,
                            async: true,
                            dataType: "json",
                            success: function(json){
                               battle.process_json(json);
                       	       action_lock = false;
                            }
                        });
                    //} else {
                    //    // Prompt the user which monster he/she wants to hit, then tie the event to another ajax event
                    //    $(".attack_choice").fadeToggle(200);
                    //}
                break;
            }

            e.preventDefault();
        });
    }    
}



$(document).ready(function(){
    
    function turn_off_music(){          
        for(var i in buzz.sounds) {
            buzz.sounds[i].mute();
        }
        local_db.set('remember_sound_toggle', true);
        battle.battle_theme.pause();
        battle.sounds_enabled = false;  
        $("#sound").html('<span id="sound_icon" class="font_icon">></span> Play sounds');
    }                                   
                                        
    function turn_on_music(){
        for(var i in buzz.sounds) {
            buzz.sounds[i].unmute();
        }
        local_db.set('remember_sound_toggle', false);
        battle.battle_theme.setVolume(0).play().fadeTo(30, 5000).loop();
        battle.sounds_enabled = true;
        $("#sound").html('<span id="sound_icon" class="font_icon"><</span>&nbsp;&nbsp;Mute sounds');
    }
    
    // if(Modernizr.localstorage){
        if(local_db.get('remember_sound_toggle') === true){
            turn_off_music();
        }
    // }

    $("#toggle_overview").toggle(function(){
        $("#techniques_overview").hide();
        $("#items_overview").show();
        $("#toggle_overview").html('&lsaquo; Back to skills');
        battle.sounds.play_sound('button_click');
    }, function(){
        $("#techniques_overview").show();
        $("#items_overview").hide();
        $("#toggle_overview").html('View my items &rsaquo;');
        battle.sounds.play_sound('button_click');
    })    

    $("#sound").bind('click', function(e){
       if(battle.sounds_enabled === true){
           turn_off_music();
       } else {
           turn_on_music();
       }
       e.preventDefault();
    })
    
    
    $(".attack_choice a").live("mouseover mouseout", function(event) {
        if ( event.type == "mouseover" ) {
    		$(".enemy_container, .monster_status").css({ opacity: "0.5"});
    		$("#monster_"+$(this).attr('id')+" .avatar_label, #monster_"+$(this).attr('id')+" .health_bar, #monster_"+$(this).attr('id')+"_sprite_status").stop().animate({ opacity: "1"}, 200);
    		$("#monster_"+$(this).attr('id')).css({ opacity: "1"});
        } else {
    		$(".enemy_container, .monster_status").css({ opacity: "1"});
    		$(".enemy_container .avatar_label, .enemy_container .health_bar").stop().css({ opacity: "0"}, 100);
        }
    });


    $("#start_battle_music").live('click', function(){
        battle.sounds.battle_bg();
        return false;
    });

    $(".player_container").hover(function(){
        $(this).children(".health_bar, .avatar_label").stop().animate({opacity: 1}, 200);
    }, function(){
        $(this).children(".health_bar, .avatar_label").stop().animate({opacity: 0}, 200);
    })

    $(".enemy_container").live("mouseover mouseout", function(event) {
        if (event.type == "mouseover"){
            $(this).children(".health_bar, .avatar_label").stop().animate({opacity: 1}, 200);
        } else {
            $(this).children(".health_bar, .avatar_label").stop().animate({opacity: 0}, 200);
        }
    });
    
    var claimed = false;
    var advance_lock = false;
    
    $("#continue_waves").live('click', function(){
        if(advance_lock) return false;
        if(claimed){
            redirect('home');
            return false;
        }
        
        advance_lock = true;
        
        $(".flying_coin").remove(); // Just incase we have any spare coins laying around
        
        clearTimeout(sit_down_idle);
        clearTimeout(battle_data.fadeout_letters);
        for (var i = battle_data['characters'].length - 1; i >= 0; i--){
            animate.character.idle('#player_'+battle_data['characters'][i]+'_sprite');
        };
        
        if(battle_data.wave == 1){
            $("#continue_waves").animate({marginRight: "-=300"}, 400);
            $("#retreat_waves").animate({marginLeft: "-=300"}, 600);
        } else {
            $("#continue_waves").animate({marginRight: "-=300"}, 200);
            $("#retreat_waves").animate({marginLeft: "-=300"}, 400);
        }
        
        $.getJSON("/battle/move_forward/", function(json){
            battle.structure.clear();
            battle.create_monsters(json['monsters']);
            battle_data.total_monsters = json.total_monsters;
            battle_data.wave = json.wave;
            
            $("#wave_keys li.current_wave").removeClass('current_wave').addClass('completed_wave')
            $(".wave_badge").html('Wave #'+json.wave).attr('id', 'wave'+json.wave);
            $("#wave_keys li#wave"+json.wave).addClass('current_wave');
            
            // Reset the monsters structure.
            $.each(battle_data['monsters'], function(key, value){
                if(typeof value == 'object') battle.active_monster_key = key;
            });
            
            setTimeout(function(){
                battle.show_action_menu();
                advance_lock = false;
            }, 500);
            
            $("#wave_completed span").animate({opacity: 0}, 500);
        });
        
        return false;
    });
    
    $("#retreat_waves").live('click', function(){
        if(claimed){
            return false;
        } else {
            claimed = true;
        }
        for (var i = battle_data['characters'].length - 1; i >= 0; i--){
            animate.character.idle('#player_'+battle_data['characters'][i]+'_sprite');
        };
        
        $("#continue_waves").die('click').animate({marginRight: "-=300"}, 400);
        $("#retreat_waves").die('click').animate({marginLeft: "-=300"}, 600);
        $.getJSON("/battle/leave_battles/", function(json){
            var rewarded_gold = 0;
            
            var my_gold_obj = $("#my_gold"), jackpot_obj = $("#jackpot h3");
            var depletion_speed = 50;
            
            if(json.jackpot_gold > 50 && json.jackpot_gold < 100){
                depletion_speed = 40;
            } else {
                depletion_speed = 20;
            }
            
            while(rewarded_gold < json.jackpot_gold){
                setTimeout(function(){
                    my_gold_obj.increase(1, true);
                    jackpot_obj.decrease(1, true);
                }, rewarded_gold*depletion_speed)
                rewarded_gold++;
            }
            
            setTimeout(function(){
                redirect('world');
            }, (json.jackpot_gold*depletion_speed)+100);
        });
        
        return false;
    });
    
    $("#bug_report").live('click', function(){
        
    });
    
    /*
     * Preload some key sounds.
    */
    battle['sounds']['cached']['punch'] = new buzz.sound(baseurl+"sounds/punch", buzz_preferences);
    battle['sounds']['cached']['button_click'] = new buzz.sound(baseurl+"sounds/button_click", buzz_preferences);
    battle['sounds']['cached']['level_passed'] = new buzz.sound(baseurl+"sounds/level_passed", buzz_preferences);
    battle['sounds']['cached']['rat_attack'] = new buzz.sound(baseurl+"sounds/rat_attack", buzz_preferences);
    battle['sounds']['cached']['rat_hit'] = new buzz.sound(baseurl+"sounds/rat_hit", buzz_preferences);
    battle['sounds']['cached']['select'] = new buzz.sound(baseurl+"sounds/select", buzz_preferences).setVolume(20);
    
});

$(window).keydown(function(e){
    switch(e.keyCode){
        case 16:
            battle.auto_select_monster = true;
            return false;
        break;
    }
});

$(window).keyup(function(e){
    switch(e.keyCode){
        case 16:
            battle.auto_select_monster = false;
            return false;
        break;
    }
});

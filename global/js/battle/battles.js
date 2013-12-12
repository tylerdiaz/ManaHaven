var log_events = false;
var list_skill_id = 0;	
var actions_binded = false;
var show_action_menu;

buzz.defaults.preload = 'auto';
buzz.defaults.duration = 5000;

/*
    TODO: Test
*/

// This is the script mainly taking charge for the battles
var battle = {
    monster_structures: {},
    sounds_enabled: false,
    auto_select_monster: false, // Hold shift to enable
    active_monster_key: false,
    battle_theme: new buzz.sound(baseurl+"sounds/battle_bridge", { formats: ["mp3", "ogg"] }),
    process_json: function(json){
        
        if( ! (battle_data.multiplayer === true && json.caster_id != battle_data.my_character_id)){
            battle.hide_action_menu();
            $("#player_"+json.caster_id+"_sprite_status .energy_condition div").width(0).css('opacity', 0.7);
            $("#player_"+json.caster_id+"_sprite_status .energy_condition div").animate({ width: "100%" }, parseInt(json.waiting_time), "linear", function(){
                $(this).animate({'opacity': 1}, 200)
            });

            show_action_menu = setTimeout(function(){
                battle.show_action_menu();
            }, (parseInt(json['waiting_time'])+200));
        }
		
		if(json.battle_won === true){
		    battle_data.battle_won = true;
		    setTimeout(function(){
		        if(battle_data.battle_won === true){    
                    // Some lag must have occured, let's do some error-recovery
                    battle.process_json(json);
		        }
		    }, 3500);
		}
		
        switch(json.response_type){
            case "animate":
                animate['skills'][json['animate_data']['animation_key']](json);
            break;
            case "loss":
                // Your team lost!
            break;
            case "win":
                // Your team won!
            break;
            case "item":
                // using an item
                animate['items'][json['animate_data']['animation_key']](json);
            break;
            case "escape":
                // let's bounce! ;)
            break;
        }   
    },
    sounds: {
        cached: {},
        battle_bg: function(){
            if(battle.sounds_enabled){
                battle.battle_theme.setVolume(0).play().fadeTo(30, 5000).loop();
            }
        },
        play_sound: function(file_name){
            if(battle.sounds_enabled){
                // Only need to load the sound files once!
                if(battle['sounds']['cached'][file_name] === undefined){
                    battle['sounds']['cached'][file_name] = new buzz.sound(baseurl+"sounds/"+file_name, { formats: ["mp3", "ogg"] });
                }
                battle['sounds']['cached'][file_name].play();
            }
        },
    },
    start_battle: function(){
        battle.sounds.battle_bg();
    },
    hide_action_menu: function(){
        actions_binded = false;
        $(".attack_choice a").die('click');
        $("#technique_list li a, #backpack_list li a").unbind('click');
        $("#battle_overview").fadeTo(200, 1);
        $("#techniques_overview, #items_overview, #toggle_overview").animate({ opacity: 0.4 });
        $("#cover_battle_overview_ui").show();
    },
    show_action_menu: function(){
        rebind_actions();
        $("#battle_overview").fadeTo(200, 1)
        $("#techniques_overview, #items_overview, #toggle_overview").animate({ opacity: 1 })
        $("#cover_battle_overview_ui").hide();
    },
	deplete_hp: function(target, amount, real_hp){
        var target_distance = find_distance(target);
        var damage_id = "d"+rand(1, 1000000);
        var status_obj = $(target+"_status");
        
        $("#battlefield").append('<strong class="damage" id="'+damage_id+'" style="opacity:0;">'+amount+'</strong>')
        $("#"+damage_id).css({ left: target_distance['x']+25, top: target_distance['y']+10 })
        		        .animate({ opacity:1, fontSize: "22px" }, 100, "linear")
        		        .animate({ opacity: 0, top: target_distance['y']-5, fontSize: "14px"}, 700, "linear", function(){
                             $(this).remove();
                         });

        $(target).parent().children(".avatar_label").animate({opacity: 1}, 200);
        $(target).parent().children(".health_bar").animate({opacity: 1}, 200, function(){
            var target_hp = $(target).parent().children('.health_bar');
            var new_hp = parseInt(target_hp.attr('data_current_hp'))-amount;
            if(new_hp < 0) new_hp = 0; 

            if(typeof real_hp !== 'undefined') new_hp = real_hp;

            var new_hp_percent = percent(new_hp, target_hp.attr('data_total_hp'));
            target_hp.attr('data_current_hp', new_hp);
            target_hp.children('div').animate({ width: new_hp_percent+'%' }, 500);
            
            // Status bar update!
            status_obj.children('.health_condition').children('div').animate({ width: new_hp_percent+'%' }, 500);
            
            if(new_hp_percent < 15){
                status_obj.children('.right').html('<b style="color:red">'+new_hp+'</b>/'+target_hp.attr('data_total_hp'))
            } else {
                status_obj.children('.right').text(new_hp+'/'+target_hp.attr('data_total_hp'))                
            }

        });
        
        setTimeout(function(){
            $(target).parent().children(".health_bar, .avatar_label").animate({opacity: 0}, 600);
        }, 1800);
	},
	increase_hp: function(target, amount){
        var target_distance = find_distance(target);
        var damage_id = "d"+rand(1, 1000000);
        var status_obj = $(target+"_status");
        
        $("#battlefield").append('<strong class="healing_points" id="'+damage_id+'" style="opacity:0;">+'+amount+'</strong>')
        $("#"+damage_id).css({ left: target_distance['x']+25, top: target_distance['y']+10 })
        		        .animate({ opacity:1, fontSize: "22px" }, 100, "linear")
        		        .animate({ opacity: 0, top: target_distance['y']-5, fontSize: "14px"}, 700, "linear", function(){
                             $(this).remove();
                         });

        $(target).parent().children(".avatar_label").animate({opacity: 1}, 200);
        $(target).parent().children(".health_bar").animate({opacity: 1}, 200, function(){
            var target_hp = $(target).parent().children('.health_bar');
            var new_hp = parseInt(target_hp.attr('data_current_hp'))+amount;
            
            // Set a max cap...
            if(new_hp > target_hp.attr('data_total_hp')) new_hp = target_hp.attr('data_total_hp');
            
            // ... an a min cap (thought this being an increase wouldn't be very logical. Unless the heal is accidentally negative)
            if(new_hp < 0) new_hp = 0; 
            
            var new_hp_percent = percent(new_hp, target_hp.attr('data_total_hp'));
            target_hp.attr('data_current_hp', new_hp);
            target_hp.children('div').animate({ width: new_hp_percent+'%' }, 500);
            
            // Status bar update!
            status_obj.children('.health_condition').children('div').animate({ width: new_hp_percent+'%' }, 500);
            
            if(new_hp_percent < 15){
                status_obj.children('.right').html('<b style="color:red">'+new_hp+'</b>/'+target_hp.attr('data_total_hp'))
            } else {
                status_obj.children('.right').text(new_hp+'/'+target_hp.attr('data_total_hp'))                
            }

        });
        
        setTimeout(function(){
            $(target).parent().children(".health_bar, .avatar_label").animate({opacity: 0}, 600);
        }, 1800);
	},
	add_exp: function(amount, exp_percent){
        var exp_txt_id = "exp"+rand(1, 1000000);
        
        $("#misc_hive").after('<strong class="exp_points" id="'+exp_txt_id+'" style="opacity:0;">+'+amount+'</strong>')
        $(".progress_text #current_exp").increase(amount);
        
        $(".experience_bar div").animate({ width: exp_percent+'%' }, 1000);
        var exp_start_location = $(".experience_bar").offset();
        
        $("#"+exp_txt_id).css({ left: exp_start_location.left+100, top: exp_start_location.top-20,  fontSize: "24px" });
        $("#"+exp_txt_id).animate({ opacity:1, fontSize: "20px", top: "+=15" }, 300, "linear", function(){
            var exp_tar = $("#"+exp_txt_id);
            setTimeout(function(){
                exp_tar.animate({ opacity: 0}, 1000, "linear", function(){
                    exp_tar.remove();
                });
            }, 3000);
        });
	},
	missed_target: function(target){
        var target_distance = find_distance(target);
        var label_id = "d"+rand(1, 1000000);
        
        $("#battlefield").append('<strong class="missed_label" id="'+label_id+'" style="opacity:0; letter-spacing:1px;">Miss</strong>')
        $("#"+label_id).css({ left: target_distance['x']+10, top: target_distance['y']+20 })
        		        .animate({ opacity:1, letterSpacing: 0 }, 350, "linear")
        		        .animate({ opacity: 0, top: target_distance['y']-15 }, 1200, "linear", function(){
                             $(this).remove();
                         });
	},
    critical_target: function(target){
            var target_distance = find_distance(target);
            var label_id = "d"+rand(1, 1000000);
            
            $("#battlefield").append('<strong class="critical_label" id="'+label_id+'" style="opacity:0;">Critical!</strong>')
            $("#"+label_id).css({ left: target_distance['x']-10, top: target_distance['y']+35 })
                         .animate({ opacity:1 }, 200, "linear")
                         .animate({ opacity: 0 }, 1000, "linear", function(){
                             $(this).remove();
                         });
    },
    jackpot_bonuses: function(data){
        $("#jackpot_bonuses_rewards").html('');
        var rewards_html = "", total_gold_bonus = 0;
        
        $.each(data, function(title, amount){
            rewards_html += '<span>'+title+' (+'+amount+' <img src="/images/coins.png" width="13" height="13" />)</span><br>';
            total_gold_bonus += amount;
        });

        $("#jackpot_bonuses_rewards").append(rewards_html);
        
        $("#jackpot_bonuses").show().animate({ opacity: 1, right: "-=10" }, 1000, function(){
            var gold_obj = $("#jackpot h3");
            var current_gold = parseInt(gold_obj.attr('data_amount'));
            var increased_gold = 0, interval_gold = 0;
            
            setTimeout(function(){
                $("#jackpot_bonuses").animate({ opacity:0, right: "+=10"}, 1000).hide();
            }, 1200);
            
            if((current_gold+total_gold_bonus) > 1000){
                while(interval_gold <= total_gold_bonus){
                    setTimeout(function(){
                        gold_obj.text(number_format(increased_gold+current_gold));
                        increased_gold++;
                    }, interval_gold*50);
                    interval_gold++;
                }                    
                gold_obj.attr('data_amount', interval_gold+current_gold);
            } else {
                while(interval_gold <= total_gold_bonus){
                    setTimeout(function(){
                        gold_obj.text(parseInt(increased_gold)+parseInt(current_gold));
                        increased_gold++;
                    }, interval_gold*50);
                    gold_obj.attr('data_amount', (parseInt(interval_gold)+parseInt(current_gold)));
                    interval_gold++;
                }                    
            }
        });
    },
    structure: {
        clear: function(){            
            // Clear all residual data
            $.each(battle_data['monsters'], function(key, monster_data){
                clearInterval(battle_data['monsters'][key]['attack_interval']);
                $(".enemy_container, .monster_status").fadeOut(200, function(){
                    $(this).remove();
                });
            });
            
            battle_data['monsters'] = {};
            battle_data['total_monsters'] = 0;
        },
        initiate: function(){
            // Start all the intervals, fadeins and other stuff involved.
        }
    },
    create_monsters: function(monsters){
        var monster_stack_delay = 500;
        var choices_html = "";
        var loaded_scripts = {};        
        var total_monsters = Object.size(monsters);
        var loaded_monsters = 0;
        
        $.each(monsters, function(monster_key, monster_data){
            // First we create the DOM to the monsters.
            $('#monster_status_tpl').mustache(monster_data).prependTo('#monster_status_hive');
            $('#monster_sprite_tpl').mustache(monster_data).appendTo('#monster_hive');
            
            $("#monster_"+monster_data.id).fadeIn(200);
            
            if(monster_data['animation_frame'] == "slime"){
                $('#monster_'+monster_data.battle_monster_id+'_sprite').parent().height(63).width(70).css({ top: function(){
			        return ($('#monster_'+monster_data.battle_monster_id+'_sprite').position()['top']+125)+'px';
			    }});
				animate.process({
					animation: 0,
    	    		img_width: 560,
    	    		tile_x:70,
    	    		tile_y:63,
					selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
					time: 1600,
					loop: true
				});
            } else {
                animate.process({
                    img_width: 520,
                    animation: 0, // Idle
                    selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
                    time: 1600,
                    loop: true
                });
            }
            
            if(typeof battle_data['monsters'][monster_key] === 'undefined') battle_data['monsters'][monster_key] = {};
            $.getScript('/global/js/battle_components/'+monster_data['animation_frame']+'.js', function(data, textStatus){
                loaded_monsters++;
                
                if(battle_data.multiplayer === true){
                    if(loaded_monsters == total_monsters){
                        multiplayer.polling.start();
                    } 
                    
                    if(battle_data.multiplayer_host !== true) return false;
                }

                battle_data['monsters'][monster_key]['attack'] = (function(){
                    $.ajax({
                        type: "POST",
                        url: "/battle/monster_turn/",
                        data: { monster: monster_data.battle_monster_id },
                        dataType: "json",
                        success: function (json){
                            // is the monster still alive?
                            if(typeof json.error === 'undefined'){
                                battle.monster_structures[monster_data['animation_frame']][json.animate_data.animation_key](json, monster_key, monster_data)
                                // Relaod the energy bar!
                                var energy_bar = $("#monster_"+monster_data.battle_monster_id+"_sprite_status .energy_condition div");

                                energy_bar.stop().width(0).css('opacity', 0.7);
                                energy_bar.animate({ width: "100%" }, (json.waiting_time-300), "linear", function(){
                                    energy_bar.animate({'opacity': 1}, 200);
                                });

                                // Setout to attack again shortly!
                                battle_data['monsters'][monster_key]['attack_interval'] = setTimeout(function(){
                                    battle_data['monsters'][monster_key]['attack']();
                                }, parseInt(json.waiting_time)+parseInt(rand(500, 2000)));
                            } else {
                                // Recover from the "not ready yet error" in 3 seconds...
                                if(json.error === 4){ // Battle is over!
                                    battle_data['monsters'][monster_key]['attack_interval'] = setTimeout(function(){
                                        battle_data['monsters'][monster_key]['attack']();
                                    }, 3000);
                                } else if(json.error === 1){
                                    $("#quick_success").css({backgroundColor: "#710000"}).html('You\'ve been logged out, refreshing in 5 seconds!').fadeIn(300);
                                    setTimeout(function(){
                                        redirect('home');
                                    }, 5000);
                                }
                            }
                        },
                        error: function(){
                            console.log('Something went wrong! ):');
                            $("#quick_success").css({backgroundColor: "#710000"}).html('An error has occured, refreshing in 5 seconds!').fadeIn(300);
                            setTimeout(function(){
                                redirect('battle');
                            }, 5000);
                        }
                    });
                });

                // Let's kick things off!
                battle_data['monsters'][monster_key]['attack_interval'] = setTimeout(function(){
                    battle_data['monsters'][monster_key]['attack']();
                }, (1000+(loaded_monsters*2000)));
            });
            
            battle_data['monsters'][monster_key] = monster_data;
            battle_data['monsters'][monster_key]['distance'] = find_distance('#monster_'+monster_data.battle_monster_id+'_sprite');
            
            choices_html += '<a href="#" id="'+monster_data.battle_monster_id+'">'+monster_data.name+'</a>';
            monster_stack_delay += 1500;
        });
        
        $(".attack_choice").html(choices_html);
    }
}
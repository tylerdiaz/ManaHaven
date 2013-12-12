function find_distance(selector){
	var x_location = $(selector).parent().css('left'), y_location = $(selector).parent().css('top');
	
	return {
		x: parseInt(x_location.substring(0, x_location.length-2)),
		y: parseInt(y_location.substring(0, y_location.length-2))
	};
}

var log_events = false;
var list_skill_id = 0;	
var active_monster_key = false;
var actions_binded = false;
var show_action_menu;

buzz.defaults.preload = 'auto';
buzz.defaults.duration = 5000;

// This is the little loading icon script
var loading = {
    centerX: 50,
    centerY: 50,
    radius: 20,
    endingAngle_decimal: 1.5,
    auto_increment_decimal: 0.02,
    startingAngle: 1.5 * Math.PI,
    endingAngle: (this.endingAngle_decimal * Math.PI),
    canvas: true,
    timer: true,
    explode_radius: 20,
    explode_opacity: 1,
    explode_width: 6,
    start: function(total_time){
        $('#energy_timer').fadeIn(300);
        loading.canvas = $('#energy_timer')[0].getContext("2d");
        var frame_speed = parseInt(total_time/Math.ceil(2/loading.auto_increment_decimal));
        loading.timer = setInterval(loading.draw, frame_speed);
        return loading.timer;
    },
    explode: function(){
        loading.canvas.clearRect(0,0,100,100);
        loading.canvas.beginPath();
        loading.canvas.lineWidth = 6; 
        loading.canvas.strokeStyle = "#4C88C5"; // line color
        loading.canvas.arc(loading.centerX, loading.centerY, loading.radius, loading.startingAngle, (3.5 * Math.PI), false);
        loading.canvas.stroke();
        loading.canvas.closePath();

        loading.explode_radius++;
        loading.explode_opacity -= 0.08;
        loading.explode_width -= 0.5;
        
        loading.canvas.globalAlpha = loading.explode_opacity;
        loading.canvas.beginPath();
        loading.canvas.lineWidth = loading.explode_width;
        loading.canvas.strokeStyle = "#4C88C5"; // line color
        loading.canvas.arc(loading.centerX, loading.centerY, loading.explode_radius, loading.startingAngle, (3.5 * Math.PI), false);
        loading.canvas.stroke();
        loading.canvas.closePath();
    },
    draw: function(){
        if(loading.endingAngle_decimal > 3.5){
            clearInterval(loading.timer);
            loading.endingAngle = (loading.endingAngle_decimal * Math.PI);
            loading.canvas.beginPath();
            loading.canvas.lineWidth = 4;
            loading.canvas.strokeStyle = "#4C88C5"; // line color
            loading.canvas.arc(loading.centerX, loading.centerY, loading.radius, loading.startingAngle, loading.endingAngle, false);
            loading.canvas.stroke();
            loading.canvas.closePath();
            loading.explode_fx = setInterval(loading.explode, 10);
            loading.endingAngle_decimal = 1.5;
            $('#energy_timer').fadeOut(400, function(){
                clearInterval(loading.explode_fx);
                loading.explode_radius = loading.radius;
                loading.explode_opacity = 1;
                loading.canvas.globalAlpha = 1;
                loading.explode_width = 6;
            });
        } else {
            loading.endingAngle_decimal += loading.auto_increment_decimal;
            loading.endingAngle = (loading.endingAngle_decimal * Math.PI);
            loading.canvas.clearRect(0,0,100,100);
            loading.canvas.beginPath();
            loading.canvas.lineWidth = 6;

            loading.canvas.strokeStyle = "#0A5292"; // line color
            //ending_angle_decimal = ending_angle_decimal+0.15;
            loading.canvas.arc(loading.centerX, loading.centerY, loading.radius, loading.startingAngle, loading.endingAngle, false);
            loading.canvas.stroke();
            loading.canvas.closePath();
        }
    }
};

// This is the script mainly taking charge for the battles
var battle = {
    sounds_enabled: true,
    process_json: function(json){
        battle.hide_action_menu();
        loading.start(json.waiting_time);

        show_action_menu = setTimeout(function(){
            battle.show_action_menu();
        }, (parseInt(json['waiting_time'])+200));
		
		if(typeof json.monster_defeated != 'undefined'){
			$("#monster_"+json.monster_defeated).fadeOut(300);

			$.each(battle_data['monsters'], function(key){
				if(battle_data['monsters'][key]['battle_monster_id'] == json.monster_defeated){
					clearInterval(battle_data['monsters'][key]['attack']);
					// Don't do: "battle_data['monsters'].splice(key, 1)" It'll ruin the magic
					battle_data['monsters'][key] = false;
					battle_data['total_monsters'] -= 1;
				}
			});
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
                battle.battle_theme = new buzz.sound(baseurl+"sounds/battle_bridge", { formats: ["mp3", "ogg"] });
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
        button_click: function(){
            battle.sounds.play_sound('button_click');
        },
        sword_slash: function(){
            battle.sounds.play_sound('slash');
        },
    },
    start_battle: function(){
        battle.sounds.battle_bg();
    },
    hide_action_menu: function(){
        actions_binded = false;
        $(".attack_choice a").unbind('click');
        $("#technique_list li a").unbind('click');
        $("#battle_overview").fadeTo(200, 1)
        $("#techniques_overview, #items_overview, #toggle_overview").animate({ opacity: 0.4 })
        $("#cover_battle_overview_ui").show();
    },
    show_action_menu: function(){
        rebind_actions();
        $("#battle_overview").fadeTo(200, 1)
        $("#techniques_overview, #items_overview, #toggle_overview").animate({ opacity: 1 })
        $("#cover_battle_overview_ui").hide();
    },
	deplete_hp: function(target, amount){
       var target_distance = find_distance(target);
       var damage_id = "d"+rand(1, 1000000);
       $("#battlefield").append('<strong class="damage" id="'+damage_id+'" style="opacity:0;">'+amount+'</strong>')
       
       $("#"+damage_id).css({ left: target_distance['x']+25, top: target_distance['y']+10 })
       				.animate({ opacity:1, fontSize: "22px" }, 100, "linear")
       				.animate({ opacity: 0, top: target_distance['y']-5, fontSize: "18px"}, 700, "linear");
       
       $(target).parent().children(".avatar_label").animate({opacity: 1}, 200);
       $(target).parent().children(".health_bar").animate({opacity: 1}, 200, function(){
       	var target_hp = $(target).parent().children('.health_bar');
       	var new_hp = parseInt(target_hp.attr('data_current_hp'))-amount;
       	target_hp.attr('data_current_hp', new_hp);
       	target_hp.children('div').animate({ width: percent(new_hp, target_hp.attr('data_total_hp'))+'%' }, 500);
       });
       
       setTimeout(function(){
           $(target).parent().children(".health_bar, .avatar_label").animate({opacity: 0}, 600);
       }, 1800);
	}
}

// Preload some of the most common sounds. :D
var buzz_preferences = {
    formats: ["mp3", "ogg"],
    preload: true,
    autoload: false,
    loop: false
}

var animate = {
	animation_busy: false,
	queue: [],
    frame: {},
    animation_interval: {},
    animation_data_cache: {},
	run_queue: function(){
		if(animate['queue'].length > 0){
			console.log("Running "+animate['queue'][0]['selector']+" from the queue.");
			animate.process(animate.queue[0]);
			animate.queue.splice(0, 1);
		} else {
			console.log("Queue is empty.");
			return true;
		}
	},
    process: function(animation_obj){
        // We can't have any overlapping animations
        if(typeof animate.animation_interval[animation_obj.selector] != 'undefined'){
            // console.log("Clearing old "+animation_obj.selector+" animation")
            clearInterval(animate.animation_interval[animation_obj.selector]);
        }

        if(typeof animate.animation_data_cache[animation_obj.selector] != 'undefined'){
            animation_obj.tile_x = (typeof animation_obj.tile_x == 'undefined') ? animate.animation_data_cache[animation_obj.selector]['tile_x'] : animation_obj.tile_x;
            animation_obj.tile_y = (typeof animation_obj.tile_y == 'undefined') ? animate.animation_data_cache[animation_obj.selector]['tile_y'] : animation_obj.tile_y;

            animation_obj.img_width = (typeof animation_obj.img_width == 'undefined') ? animate.animation_data_cache[animation_obj.selector]['img_width'] : animation_obj.img_width;
            animation_obj.animation = (typeof animation_obj.animation == 'undefined') ? animate.animation_data_cache[animation_obj.selector]['animation'] : animation_obj.animation;

            // Let's give it a kick start, to avoid the frame-skip lag
            animate.frame[animation_obj.selector] = 1;
        } else {
            // Let's set some default data...
            animation_obj.tile_x = (typeof animation_obj.tile_x == 'undefined') ? parseInt($(animation_obj.selector).css('width')) : animation_obj.tile_x;
            animation_obj.tile_y = (typeof animation_obj.tile_y == 'undefined') ? parseInt($(animation_obj.selector).css('height')) : animation_obj.tile_y;

            animation_obj.animation = (typeof animation_obj.animation == 'undefined') ? 1 : animation_obj.animation;
            animate.animation_data_cache[animation_obj.selector] = animation_obj;
			
			if(typeof animation_obj.img_width == 'undefined') console.log('No img_width set for '+animation_obj.selector+', inconsistancies may be noticed.')
			
            // All set, let's begin defining object variables and what not...
            animate.frame[animation_obj.selector] = 0;
        }

        animation_obj.time = (typeof animation_obj.time == 'undefined') ? animate.animation_data_cache[animation_obj.selector]['time'] : animation_obj.time;
        animation_obj.loop = (typeof animation_obj.loop == 'undefined') ? false : animation_obj.loop;

		// If we're currently animating, let's push the animation to the queue
		if(animate.animation_busy == true && typeof animation_obj.queue != 'undefined'){
			// Pass this through the queue
			if(animation_obj.queue == true){
				console.log("Pushing "+animation_obj.selector+" to the queue");
				animate.queue.push(animation_obj);
				return true;
			}
		}

        // Is there any pre-animation functions?
        if(typeof animation_obj.onload != 'undefined') animation_obj.onload();
        
        // getElementById() is a lot faster than jQuery's css selector: http://jsperf.com/jquery-css
        var cached_sprite_selector = document.getElementById(animation_obj.selector.substring(1)).style;
        
        // Start the sprite/frame loop
        animate.animation_interval[animation_obj.selector] = setInterval(function(){
            // Should we loop back to the first frame?
            if(animate.frame[animation_obj.selector] > Math.round(animation_obj.img_width/animation_obj.tile_x)) animate.frame[animation_obj.selector] = 1;
            
            // Set the background position to the current frame.
            cached_sprite_selector.backgroundPosition='-'+(animate.frame[animation_obj.selector]*animation_obj.tile_x)+'px -'+(animation_obj.animation*animation_obj.tile_y)+'px';
            
            animate.frame[animation_obj.selector]++;
        }, (Math.round(animation_obj.time/Math.round(animation_obj.img_width/animation_obj.tile_x))));
        
		if(typeof animation_obj.move_x != 'undefined' && typeof animation_obj.move_y == 'undefined'){
			$(animation_obj.selector).parent().animate({ left: animation_obj.move_x+"px" }, animation_obj.time, "linear")
		} else if(typeof animation_obj.move_x != 'undefined' && typeof animation_obj.move_y == 'undefined'){
			$(animation_obj.selector).parent().animate({ top: animation_obj.move_y+"px" }, animation_obj.time, "linear")
		} else if (typeof animation_obj.move_x != 'undefined' && typeof animation_obj.move_y != 'undefined'){
			
			var current_x_pos = $(animation_obj.selector).parent().css('left');
			var current_y_pos = $(animation_obj.selector).parent().css('top');
			
			current_x_pos = parseInt(current_x_pos.substring(0, current_x_pos.length-2));
			current_y_pos = parseInt(current_y_pos.substring(0, current_y_pos.length-2));
			
			if(current_x_pos > animation_obj.move_x){
    			var jump_back_animation_data = {
    			    start: { x: current_x_pos, y: current_y_pos, angle: 13, length: 0.5 },  
    			    end: { x:animation_obj.move_x, y:animation_obj.move_y }
    			}
			} else {
    			var jump_back_animation_data = {
    			    start: { x: current_x_pos, y: current_y_pos, angle: -13, length: 0.5 },  
    			    end: { x:animation_obj.move_x, y:animation_obj.move_y }
    			}
			}
			
			$(animation_obj.selector).parent().animate({ top: current_y_pos-2+'px'}, 150, "linear").animate({path: new $.path.bezier(jump_back_animation_data) }, (animation_obj.time/1.5))
		}

        // Is it a one-time animation?
        if(animation_obj.loop === false){
			animate.animation_busy = true;
            // Stop the animation once it's finished
            setTimeout(function(){
				animate.animation_busy = false;
                clearInterval(animate.animation_interval[animation_obj.selector]);
                // Run the callback!
                if(typeof animation_obj.callback != 'undefined'){
                    animation_obj.callback();
                }
            }, animation_obj.time)
        }
    },
	character: {
        idle: function(selector){
			animate.process({
                img_width: 720,
                animation: 2,
                selector: selector,
                time: 2400,
                loop: true
            });
        },
        cheer: function(selector){
			animate.process({
                img_width: 720,
                animation: 4,
                selector: selector,
                time: 1600
            });
        },
        faint: function(selector){
			animate.process({
                img_width: 720,
                animation: 5,
                selector: selector,
                time: 2000,
				callback: function(){
					$(selector).animate({ opacity: 0.4 }, 500);
				}
            });
        },			
        block: function(selector){
			animate.process({
                img_width: 720,
                animation: 1,
                selector: '#player_sprite',
                time: 1000,
				queue: false,
                callback: function(){
					animate.character.idle(selector);
                }
            });		 						    
        },			
	},
	events: {
        battle_checks: function(data){
            if(typeof data.battle_won != 'undefined'){
                animate.events.battle_won();
            } else if(typeof data.battle_lost != 'undefined'){
                animate.character.faint('#player_sprite');
            } else {
                animate.run_queue();
                animate.character.idle('#player_sprite');
            }
        },
	    battle_won: function(){
	        $(".enemy_container").fadeOut(400);
            
	        battle.hide_action_menu();
	        clearTimeout(show_action_menu);
            
			setTimeout(function(){
				animate.character.cheer('#player_sprite');
				battle.sounds.play_sound('level_passed');
				if(battle.sounds_enabled == true) battle.battle_theme.fadeTo(10, 200);
			}, 500)
			setTimeout(function(){
			    $.getJSON(baseurl+"battle/reward_popup", function(json){
        	        $("#cover_screen").fadeIn(300).html('<a href="#" id="flee_button" style="">Flee with jackpot</a> \
                        <div id="dialog"> \
                            <h3>Wave 3 completed!</h3> \
                            <img src="/images/world/feed/gold.png" class="left" /> <h2 style="margin-top:14px; text-align:left; color:#A8A77A"><span id="gold_amount" style="font-size:26px; color:#CDC828">125</span> <span>Gold</span></h2> \
                            <br clear="all" /> \
                            <a href="#" id="move_up_button">Battle another wave &rsaquo;</a> \
                        </div>');
			    });
            //  popup.create({
            //      title: "Congratulations, you made it past wave #"+battle_data.wave+"!",
            //      content: { ajax: "battle/reward_popup" },
            //      cancel_button: { 
            //          label: 'Take the jackpot and flee', 
            //          callback: function(){ 
            //              redirect('battle/retrieve_jackpot');
            //          } 
            //      },
            //      confirm_button: { 
            //          callback: function(){
            //              $.getJSON(baseurl+"battle/move_forward", function(json){
            //                  redirect('battle');
            //              });
            //          },
            //          label: 'Continue to the next wave &rsaquo;'
            //      }
            //  });
            }, 2800);
	    },
	    battle_loss: function(){
	        popup.create({
			    title: "You lost!",
			    content: "The battle has been lost. Try stocking up on a ton of HP potions next time, the really help!",
			    cancel_button: { 
					label: 'Back to dashboard', 
					position: "right",
					callback: function(){ 
						redirect('world') 
					} 
				}
			});
	    }
	},
    skills: {
        slash: function(data){
            var my_location = find_distance('#player_sprite');

            animate.process({
                img_width: 720,
                animation: 0,
                selector: '#player_sprite',
                time:400,
				queue: true,
				move_x: (my_location.x+55),
                callback: function(){
                    battle.sounds.play_sound('rat_hit');
                    battle.deplete_hp("#monster_"+data.animate_data.target_id+"_sprite", data['animate_data']['amount']);
                    animate.process({
                        img_width: 550, // -2 frames
    					animation: 3,
    					selector: '#player_sprite',
    					time: 800,
    					callback: function(){
    					    $("#player_sprite").addClass('flip_div').parent().stop().css({ left: function(){
    					        return (my_location.x+40)+'px';
    					    }});

                            animate.process({
            					animation: 0,
            					selector: '#player_sprite',
            					time: 250,
            					move_x: my_location['x']-15,
            					// Go idle
            					callback: function(){
            					    $("#player_sprite").removeClass('flip_div').parent().stop().css({ 
            					        left: function(){ return my_location['x']+'px'; }
            					    });
            					    
            					    animate.events.battle_checks(data);
            					}
            				});
                        }
    				});
                }
            });
        },
        arrow_shot: function(data){
            var my_location = find_distance('#player_sprite');

            animate.process({
                img_width: 720,
                animation: 0,
                selector: '#player_sprite',
                time:400,
				queue: true,
				move_x: (my_location.x-15),
                callback: function(){
                    setTimeout(function(){
                        battle.sounds.play_sound('rat_hit');
                        battle.deplete_hp("#monster_"+data.animate_data.target_id+"_sprite", data['animate_data']['amount']);
                        animate.process({
                            img_width: 550, // -2 frames
        					animation: 3,
        					selector: '#player_sprite',
        					time: 800,
        					callback: function(){
                                animate.process({
                					animation: 0,
                					selector: '#player_sprite',
                					time: 250,
                					move_x: my_location['x'],
                					// Go idle
                					callback: function(){
                					    animate.events.battle_checks(data);
                					}
                				});
                            }
        				});
                    }, 1000)
                }
            });
        },
    },
};


function rebind_actions(){
    if(actions_binded == false){
        actions_binded = true;
        
        $(".attack_choice a").bind('click', function(){
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
                    battle.process_json(json);
                }
            });
    	});

    	$("#technique_list li a").bind('click', function(e){
            list_skill_id = $(this).attr('data_skill_id');
            battle.sounds.play_sound('select');
            // Only one monster left!
            if(battle_data['total_monsters'] == 1){
               // Let's get the active monster id, and remember it for next time.
               if(active_monster_key === false){
                   console.log('LOOP: Search for active monster ID.');
                   $.each(battle_data['monsters'], function(key, value){
                       if(typeof value == 'object') active_monster_key = key;
                   });
               }

               // Only one monster is possible to damage, so let's skip the logistics
               var post_data = { 
                   skill_id: list_skill_id, 
                   target_id: battle_data['monsters'][active_monster_key]['battle_monster_id'] 
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
                    }
                });
            } else {
                // Prompt the user which monster he/she wants to hit, then tie the event to another ajax event
                $(".attack_choice").fadeToggle(200);
            }

            e.preventDefault();
        })
    }    
}



$(document).ready(function(){

	// ----------------------------------------------------
	
	/*
	 * From here till the next check point is pure testing
	*/

    // animate.character.idle('#player_sprite');
    // 
    // // This will help avoid 2 monsters attacking at once, which might 
    // // seem a bit strange/overwhelming from a users point over view
    // 
    // var monster_stack_delay = 2000;
    // 
    // $.each(battle_data['monsters'], function(key, monster_data){
    //  animate.process({
    //      img_width: 520,
    //      animation: 0, // Idle
    //      selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
    //      time: 1600,
    //      loop: true
    //  });
    //  
    //  battle_data['monsters'][key]['distance'] = find_distance('#monster_'+monster_data.battle_monster_id+'_sprite');
    //      battle_data['monsters'][key]['attack'] = setInterval(function(){
    //          $.ajax({
    //              type: "POST",
    //              url: "/battle/monster_turn",
    //              data: { monster: monster_data.battle_monster_id },
    //              dataType: "json",
    //              success: function (json) {
    //                     rat.attack(json, key, monster_data)
    //              },                      
    //              error: function(){      
    //                  console.log('Battle is over!');
    //              }                       
    //          });                         
    //      }, monster_data.recoil+monster_stack_delay)
    //                                      
    //      monster_stack_delay += 1500;    
    //  }); 
    //                                         
    // battle.start_battle();              
	                                    
	// battle.start_battle();                            
	
	// ----------------------------------------------------
	                                    
	/*                                  
	 * Anything below is simply binding events
	*/                                  
	                       
    function turn_off_music(){          
        local_db.set('remember_sound_toggle', true);
        battle.battle_theme.togglePlay();
        battle.sounds_enabled = false;  
        $("#sound").text('Turn on sounds');
    }                                   
                                        
    function turn_on_music(){
        local_db.set('remember_sound_toggle', false);
        battle.battle_theme.togglePlay();
        battle.sounds_enabled = true;
        $("#sound").text('Turn off sounds');
    }
    
    if(Modernizr.localstorage){
        if(local_db.get('remember_sound_toggle') === true){
            turn_off_music();
        }
    }

    $("#toggle_overview").toggle(function(){
        $("#techniques_overview").hide();
        $("#items_overview").show();
        $("#toggle_overview").html('&lsaquo; Back to techniques');
        battle.sounds.button_click();
    }, function(){
        $("#techniques_overview").show();
        $("#items_overview").hide();
        $("#toggle_overview").html('View my items &rsaquo;');
        battle.sounds.button_click();
    })    

    $("#sound").live('click', function(){
       console.log(battle.sounds_enabled);
       if(battle.sounds_enabled === true){
          turn_off_music();
       } else {
          turn_on_music();
       }
       return false;
    })

	$(".attack_choice a").hover(function(){
		$(".enemy_container").css({ opacity: "0.5"});
		$("#monster_"+$(this).attr('id')+" .avatar_label, #monster_"+$(this).attr('id')+" .health_bar").stop().animate({ opacity: "1"});
		$("#monster_"+$(this).attr('id')).css({ opacity: "1"});
	}, function(){
		$(".enemy_container").css({ opacity: "1"});
		$(".enemy_container .avatar_label, .enemy_container .health_bar").stop().css({ opacity: "0"});
	})
            
    $("#start_battle_music").live('click', function(){
        battle.sounds.battle_bg();
        return false;
    });

    $(".player_container, .enemy_container").hover(function(){
        $(this).children(".health_bar, .avatar_label").animate({opacity: 1}, 200);
    }, function(){
        $(this).children(".health_bar, .avatar_label").animate({opacity: 0}, 200);
    })
    
    /*
     * Preload some key sounds.
    */
    battle['sounds']['cached']['punch'] = new buzz.sound(baseurl+"sounds/punch", buzz_preferences);
    battle['sounds']['cached']['button_click'] = new buzz.sound(baseurl+"sounds/button_click", buzz_preferences);
    battle['sounds']['cached']['level_passed'] = new buzz.sound(baseurl+"sounds/level_passed", buzz_preferences);
    battle['sounds']['cached']['rat_attack'] = new buzz.sound(baseurl+"sounds/rat_attack", buzz_preferences);
    battle['sounds']['cached']['rat_hit'] = new buzz.sound(baseurl+"sounds/rat_hit", buzz_preferences);
    battle['sounds']['cached']['coins'] = new buzz.sound(baseurl+"sounds/coins", buzz_preferences);
    battle['sounds']['cached']['select'] = new buzz.sound(baseurl+"sounds/select", buzz_preferences).setVolume(20);
});
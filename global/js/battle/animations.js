function find_distance(selector){
	var x_location = $(selector).parent().css('left'), y_location = $(selector).parent().css('top');
	
	return {
		x: parseInt(x_location.substring(0, x_location.length-2)),
		y: parseInt(y_location.substring(0, y_location.length-2))
	};
}

var animate = {
	animation_busy: false,
	queue: [],
    animation_interval: {},
    animation_data_cache: {},
	run_queue: function(){
		if(animate['queue'].length > 0){
            // console.log("Running "+animate['queue'][0]['selector']+" from the queue.");
			animate.process(animate.queue[0]);
			animate.queue.splice(0, 1);
		} else {
            // console.log("Queue is empty.");
			return true;
		}
	},
    process: function(animation_obj){
        var frame = 0;
        var selector_obj = $(animation_obj.selector);
        var selector_obj_parent = $(animation_obj.selector).parent();
        
        // console.log(animation_obj.selector+' :: '+animation_obj.animation);
        // We can't have any overlapping animations
        if(typeof animate.animation_interval[animation_obj.selector] != 'undefined'){
            clearInterval(animate.animation_interval[animation_obj.selector]);
        }
                
        if(typeof animate.animation_data_cache[animation_obj.selector] != 'undefined'){
            animation_obj = $.extend({
                tile_x: animate.animation_data_cache[animation_obj.selector]['tile_x'],
                tile_y: animate.animation_data_cache[animation_obj.selector]['tile_y'],
                img_width: animate.animation_data_cache[animation_obj.selector]['img_width'],
                animation: animate.animation_data_cache[animation_obj.selector]['animation'],
                queue: false,
                time: animate.animation_data_cache[animation_obj.selector]['time'],
                loop: false,
            }, animation_obj);

            // Let's give it a kick start, to avoid the frame-skip lag
            // frame = 1;
        } else {
            // Let's set some default data. This call might be a tad expensive because of the selector call, but it gets cached
            animation_obj = $.extend({
                tile_x: parseInt(selector_obj.width()),
                tile_y: parseInt(selector_obj.height()),
                animation: 1,
                queue: false,
                time: 1000,
                loop: false,
            }, animation_obj);
            
            animate.animation_data_cache[animation_obj.selector] = animation_obj;
        }

		// If we're currently animating, let's push the animation to the queue
		if(animate.animation_busy == true && animation_obj.queue == true){
            // console.log("Pushing "+animation_obj.selector+" to the queue");
			animate.queue.push(animation_obj);
			return false;
		}
        
        // Is there any pre-animation functions?
        if(typeof animation_obj.onload != 'undefined') animation_obj.onload();
        
        // Is there a dialog to this animation
        if(typeof animation_obj.message != 'undefined') this.events.dialog(animation_obj.message);
        
        // getElementById() is a lot faster than jQuery's css selector: http://jsperf.com/jquery-css
        var cached_sprite_selector = document.getElementById(animation_obj.selector.substring(1));

        // Start the sprite/frame loop
        animate.animation_interval[animation_obj.selector] = setInterval(function(){
            // Should we loop back to the first frame?
            if(frame > Math.round(animation_obj.img_width/animation_obj.tile_x)) frame = 1;
            
            // Set the background position to the current frame.
            cached_sprite_selector.style.backgroundPosition = '-'+(frame*animation_obj.tile_x)+'px -'+(animation_obj.animation*animation_obj.tile_y)+'px';
            frame++;
        }, (Math.round(animation_obj.time/Math.round(animation_obj.img_width/animation_obj.tile_x))));

		if(typeof animation_obj.move_x != 'undefined' && typeof animation_obj.move_y == 'undefined'){
			selector_obj_parent.animate({ left: animation_obj.move_x+"px" }, animation_obj.time, "linear")
		} else if(typeof animation_obj.move_x != 'undefined' && typeof animation_obj.move_y == 'undefined'){
			selector_obj_parent.parent().animate({ top: animation_obj.move_y+"px" }, animation_obj.time, "linear")
		} else if (typeof animation_obj.move_x != 'undefined' && typeof animation_obj.move_y != 'undefined'){
			// This can be done a lot better is we use offset
			var current_position = selector_obj_parent.position();
			
			if(current_position.left > animation_obj.move_x){
    			var jump_back_animation_data = {
    			    start: { x: current_position.left, y: current_position.top, angle: 13, length: 0.5 },  
    			    end: { x:animation_obj.move_x, y:animation_obj.move_y }
    			}
			} else {
    			var jump_back_animation_data = {
    			    start: { x: current_position.left, y: current_position.top, angle: -13, length: 0.5 },  
    			    end: { x:animation_obj.move_x, y:animation_obj.move_y }
    			}
			}
			
			selector_obj_parent.animate({ top: current_position.top-2+'px'}, 150, "linear").animate({path: new $.path.bezier(jump_back_animation_data) }, (animation_obj.time/1.5))
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
                selector: selector,
                time: 1000,
				queue: false,
                callback: function(){
					animate.character.idle(selector);
                }
            });		 						    
        },			
	},
	coin_burst: function(target, amount){
	    if(amount != NaN && amount > 0){
    	    var gold_hive = $("#gold_hive"),
    	        coin_start_location = $(target).offset(),
                coin_end_destination = $("#jackpot").offset(),
                i = 0
                total_sprites = (amount < 20 ? amount : 20);

            while(i < total_sprites){
                gold_hive.append('<img src="/images/flying_coin.gif" alt="" id="coinjp_'+i+'" style="left:'+(parseInt(coin_start_location.left)+25)+'; top:'+(parseInt(coin_start_location.top)+60)+'; " class="flying_coin" />');

                var jump_back_animation_data = {
                    start: { 
                        x: (parseInt(coin_start_location.left)+25), 
                        y: (parseInt(coin_start_location.top)+60), angle: rand(0, 90), length: rand(-2, 2) 
                    },  
                    end: { 
                        x: (parseInt(coin_end_destination.left)+15), 
                        y:(parseInt(coin_end_destination.top)+15) 
                    }
                }

                $('#coinjp_'+i+'').animate({ path: new $.path.bezier(jump_back_animation_data), opacity: 0.2 }, 900, function(){
                    $(this).remove();
                })

                i++;
            }

            setTimeout(function(){
                $("#jackpot h3").increase(parseInt(amount), true);
                setTimeout(function(){
                    if($("#jackpot h3").text() == NaN){
                        console.error("Jackpot Not a Number :"+$("#jackpot h3").attr('data_amount'));
                    }
                }, 500);
            }, 600)
	    }
	},
	exp_burst: function(target, destination, amount){
	    var gold_hive = $("#misc_hive"),
            coin_start_location = $(target).offset(),
            coin_end_destination = $(destination).offset(),
            i = 0,
            total_sprites = (amount < 20 ? amount : 20);
        
        while(i < total_sprites){
            gold_hive.append('<img src="/images/exp_orb_'+rand(1, 3)+'.png" alt="" id="expjp_'+i+'" style="left:'+(parseInt(coin_start_location.left)+25)+'; top:'+(parseInt(coin_start_location.top)+60)+'; " class="flying_coin" />');
            
            var jump_back_animation_data = {
                start: { 
                    x: (parseInt(coin_start_location.left)+25), 
                    y: (parseInt(coin_start_location.top)+60), angle: rand(-80, 80), length: rand(-1, 1)
                },  
                end: { 
                    x: (parseInt(coin_end_destination.left)+30), 
                    y:(parseInt(coin_end_destination.top)+50) 
                }
            }
            
            $('#expjp_'+i+'').animate({ path: new $.path.bezier(jump_back_animation_data), opacity: 0.05 }, 700, function(){
                $(this).remove();
            })
            
            i++;
        }
	},
	events: {
	    dialog: function(text){
	        clearTimeout(this.dialog_fadeout);
	        $("#battle_queue_dialog").stop().text(text).fadeTo(200, 1, function(){
		        this.dialog_fadeout = setTimeout(function(){
		            $("#battle_queue_dialog").fadeTo(1000, 0);
		        }, 1500)
		    })
	    },
	    pre_battle_checks: function(data){
            // This is mostly programmatic-type fixes for animations

            if(typeof data.monster_defeated != 'undefined'){
                // This is no longer the active key!
                if(battle.active_monster_key == data.monster_defeated){
                    battle.active_monster_key = false;
                }
                
    			// Freeze the energy bar from filling once the mosnter has been killed.
    			$("#monster_"+data.monster_defeated+"_sprite_status .energy_condition div").stop(true);
				clearTimeout(battle_data['monsters'][data.monster_defeated]['attack_interval']);
    		}
	    },
        post_battle_checks: function(data){
            if(typeof data.exp_bonus != 'undefined' || typeof data.exp_animation != 'undefined'){
                animate.exp_burst('#monster_'+data.monster_defeated+'_sprite', '#player_'+data.caster_id+'_sprite', data.exp_bonus);

                // Once the orbs are done!
                setTimeout(function(){
                    if(typeof data.exp_animation != 'undefined'){
                        // Multiple level ups!
                        var level_i = 0;
                        for (var i=0; i < data.exp_animation.length; i++) {
                            setTimeout(function(){  
                                var current_level = level_i;                            
                                // Level them up!
                                if(data.exp_animation[current_level]['from_level'] < data.exp_animation[current_level]['to_level']){
                                    // (exp_to: 100 / from_level: 1 / percent: 100 / to_level: 2);
                                    
                                    var exp_txt_id = "exp"+rand(1, 1000000);
                                    $("#misc_hive").after('<strong class="exp_points" id="'+exp_txt_id+'" style="opacity:0;">+'+data.exp_animation[current_level]['exp_bonus']+'</strong>')
                                    $("#current_exp").increase(data.exp_animation[current_level]['exp_bonus']);

                                    var exp_start_location = $(".experience_bar").offset();

                                    $("#"+exp_txt_id).css({ left: exp_start_location.left+100, top: exp_start_location.top-20,  fontSize: "24px" });
                                    $("#"+exp_txt_id).animate({ opacity:1, fontSize: "20px", top: "+=15" }, 300, "linear", function(){
                                        var exp_tar = $("#"+exp_txt_id);
                                        setTimeout(function(){
                                            exp_tar.animate({ opacity: 0}, 500, "linear", function(){
                                                exp_tar.remove();
                                            });
                                        }, 1000);
                                    });
                                    
                                    $(".experience_bar div").animate({ width: '100%' }, 800, function(){
                                        
                                        $("#level_up_shine").fadeIn(800, function(){
                                            setTimeout(function(){
                                                $("#level_up_shine").fadeOut(400);
                                            }, 1000)
                                        });
                                        
                        				// Also add some additional level up animations!
                        				animate.character.cheer('#player_'+data.caster_id+'_sprite');
                                        
                                        setTimeout(function(){
                            				animate.character.idle('#player_'+data.caster_id+'_sprite');
                                        }, 1200);

                                        var animation_id = "animation"+rand(1, 1000000);
                                        var player_location = $('.player_container').offset()
                                        var portal_html = '<div id="front_glitter_'+animation_id+'"></div>';
                                            portal_html += '<div id="back_portal_'+animation_id+'"></div>';
                                            portal_html += '<div id="front_header_'+animation_id+'"></div>';

                                        $("#animation_hive").append(portal_html);

                                        $("#front_header_"+animation_id).css({
                                            position: "absolute",
                                            left: player_location.left-20,
                                            top: player_location.top+20,
                                            width: 120,
                                            height:120,
                                            background: "transparent url(/images/animations/levelup_header.png)no-repeat 3px -40px",
                                            zIndex: 15,
                                            opacity:0
                                        });

                                        $("#front_glitter_"+animation_id).css({
                                            position: "absolute",
                                            left: player_location.left-20,
                                            top: player_location.top+30,
                                            width: 120,
                                            height:120,
                                            background: "transparent url(/images/animations/star_animations.gif)no-repeat 3px -40px",
                                            zIndex: 14,
                                            opacity:0,
                                        });

                                        $("#back_portal_"+animation_id).css({
                                            position: "absolute",
                                            left: player_location.left-20,
                                            top: player_location.top-10,
                                            width: 120,
                                            height:120,
                                            background: "transparent url(/images/animations/level_up_portal_back.png)no-repeat 3px -5px",
                                            zIndex:5,
                                            opacity:0,
                                        });

                        		        $("#front_header_"+animation_id).animate({ opacity: 1, top: "-=35"}, 1000);
                        		        
                        		        $("#back_portal_"+animation_id).animate({ opacity: 1 }, 300, function(){
                            		        $("#front_glitter_"+animation_id).animate({ opacity: 1 }, 600, function(){
                            		            setTimeout(function(){
                            		                $("#front_glitter_"+animation_id+", #front_header_"+animation_id).animate({ opacity: 0 }, 800, function(){
                            		                    $(this).remove();
                                		                $("#back_portal_"+animation_id).fadeOut(1000, function(){
                                		                    $(this).remove();
                                		                })
                            		                })
                            		            }, 500);
                            		        });
                        		        });
                            		    
                                        // Full heal!
                                        battle.increase_hp('#player_'+data.caster_id+'_sprite', data.exp_animation[current_level]['heal_points']);
                                        
                                        $(".experience_bar div").width(0);
                                        $("#level_bubble").text(data.exp_animation[current_level]['to_level']);
                                        $("#next_level_exp").text(data.exp_animation[current_level]['new_exp_required']).attr('data_amount', data.exp_animation[current_level]['new_exp_required']);
                                    });
                                } else {
                                    // Just increase the XP
                                    battle.add_exp(data.exp_animation[current_level]['exp_bonus'], data.exp_animation[current_level]['percent']);
                                }
                                level_i++;
                            }, (i*2000));
                        };
                    } else {
                        battle.add_exp(data.exp_bonus, data.exp_percent);
                    }
                }, 600);
            }
            
            if(typeof data.monster_defeated != 'undefined'){
                var death_animation = false;
                
                if(battle_data['monsters'][data.monster_defeated]['animation_frame'] == "rat") death_animation = true;

    			$("#monster_"+data.monster_defeated).fadeOut(1000, function(){
    			    $("#monster_"+data.monster_defeated+"_sprite_status").fadeOut(300);
    			});
                
                // Reset the monsters structure.
    			$.each(battle_data['monsters'], function(pre_monster_key, pre_monster_data){
    				if(pre_monster_key == data.monster_defeated){
    					battle_data['monsters'][pre_monster_key] = false;
    					battle_data['total_monsters'] -= 1;
    				}
    			});

    			// Reset the action choices after a monster is defeated!
                var choices_html = "";
                $.each(battle_data['monsters'], function(pre_monster_key, pre_monster_data){
                    var monster_data = battle_data['monsters'][pre_monster_key];
                    if(monster_data != false && pre_monster_key != data.monster_defeated){
                        choices_html += '<a href="#" id="'+monster_data.battle_monster_id+'">'+monster_data.name+'</a>';
                    }
                });
    			
                $(".attack_choice").html(choices_html);
    		}

            // The battle_loss stuff is handled in the monster functionality sheet
            if(typeof data.battle_won != 'undefined'){
                animate.events.battle_won(data);
            } else {
                animate.character.idle('#player_'+data.caster_id+'_sprite');
                animate.run_queue();
            }
            
            if(death_animation === true){
                animate.process({
    	    		img_width: 520,
    	    		animation: 4,
    	    		selector: '#monster_'+data.monster_defeated+'_sprite',
    	    		time: 1200, 
    	    		message: "Monster defeated",
    	    		callback: function(){
    	    		    clearInterval(animate.animation_interval['#monster_'+data.monster_defeated+'_sprite']);
    	    		}
    			});
            } else {
    		    clearInterval(animate.animation_interval['#monster_'+data.monster_defeated+'_sprite']);
            }
        },
	    battle_won: function(data){
	        battle.hide_action_menu();
	        
            // console.log('Animation won!');
	        
	        clearTimeout(show_action_menu);
            
			setTimeout(function(){
			    if(battle_data.multiplayer){
			        for (var i = battle_data.characters.length - 1; i >= 0; i--){
			            animate.character.cheer('#player_'+battle_data.characters[i]+'_sprite');
			        };
			    } else {
    				animate.character.cheer('#player_'+data.caster_id+'_sprite');
			    }
				
				battle.sounds.play_sound('level_passed');
				if(battle.sounds_enabled == true) battle.battle_theme.fadeTo(10, 200);
                
                setTimeout(function(){
                    if(battle_data.multiplayer){
    			        for (var i = battle_data.characters.length - 1; i >= 0; i--){
                            animate.character.idle('#player_'+battle_data.characters[i]+'_sprite');
    			        };
    			    } else {
        				animate.character.idle('#player_'+data.caster_id+'_sprite');
    			    }
                    
                    sit_down_idle = setTimeout(function(){
                        animate.process({
                            img_width: 720,
                            animation: 5,
                            selector: '#player_'+data.caster_id+'_sprite',
                            time: 1200
                        });
                    }, 16000);
                }, 2500);
                setTimeout(function(){
                    // Offer the options!
                    $("#continue_waves").animate({ marginRight: 0 }, 1200, function(){
                        $(this).css({ zIndex: 9999, position: "relative" });
                    });
                    $("#retreat_waves").animate({ marginLeft: 0 }, 1800).css({ zIndex: 9999 });
                }, 1500);
                
                if(typeof data.jackpot_bonuses != 'undefined') battle.jackpot_bonuses(data.jackpot_bonuses);
			}, 500);

            $("#wave_completed_number").text(battle_data.wave);

            var letters = $("#wave_completed span");
            var letter_int = 0;

            letters.each(function(key, obj){
                setTimeout(function(){
                    $(letters[key]).animate({ opacity: 1}, 300);
                    letter_int++;
                }, (parseInt(key)*150))
            });
            
            // If they're not gone, fade 'em out!
            battle_data.fadeout_letters = setTimeout(function(){
                letters.stop().animate({ opacity: 0 }, 1000);
            }, 7500);
            
            // Show some fruits/items to use!
            
            battle_data.battle_won = false;
	    },
	    battle_loss: function(){
	        battle.hide_action_menu();
	        clearTimeout(show_action_menu);
            
            for (var i = battle_data['characters'].length - 1; i >= 0; i--){
                animate.character.faint('#player_'+battle_data['characters'][i]+'_sprite');
            };
	        	        
			setTimeout(function(){
    	        $(".enemy_container, .monster_status").fadeOut(200);
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
			}, 2000)
	    }
	},
    skills: {
        // animate_data:
        //     amount: 8
        //     amount_type: "positive"
        //     animation_key: "heal"
        //     description: "Pixeltweak uses Minor Heal!"
        //     target: "character"
        //     target_id: 1233
        // caster_id:1328
        // caster_type:"player"
        // response_type:"animate"
        // waiting_time:6500
        
        heal: function(data){            
		    animate.events.pre_battle_checks(data);                    
            var animation_id = "animation"+rand(1, 1000000);
		    var player_location = $('.player_container').offset()
            var portal_html = '<div id="front_portal_'+animation_id+'"></div>';
                portal_html += '<div id="back_portal_'+animation_id+'"></div>';

            $("#animation_hive").append(portal_html);

            $("#front_portal_"+animation_id).css({
                position: "absolute",
                left: player_location.left-20,
                top: player_location.top-10,
                width: 120,
                height:120,
                background: "transparent url(/images/animations/heal_portal_front.png)no-repeat 3px -40px",
                zIndex: 14,
                opacity:0,
            });
            
            $("#back_portal_"+animation_id).css({
                position: "absolute",
                left: player_location.left-20,
                top: player_location.top-10,
                width: 120,
                height:120,
                background: "transparent url(/images/animations/heal_portal_back.png)no-repeat 3px -5px",
                zIndex:5,
                opacity:0,
            });

		    setTimeout(function(){
		        $("#back_portal_"+animation_id).animate({ opacity: 1 }, 300, function(){
    		        $("#front_portal_"+animation_id).animate({ opacity: 1, top: "+=32" }, 600, function(){
    		            battle.increase_hp('#player_'+data.caster_id+'_sprite', data['animate_data']['amount']);
    		            setTimeout(function(){
    		                $("#front_portal_"+animation_id).animate({ opacity: 0, top: "-=30" }, 800, function(){
    		                    $(this).remove();
        		                $("#back_portal_"+animation_id).fadeOut(1000, function(){
        		                    $(this).remove();
        		                })
    		                })
    		            }, 500);
    		        });
		        });
		    }, 300);

            setTimeout(function() {
                animate.events.post_battle_checks(data);
            }, 1800);
            
            animate.process({
                img_width: 720,
                animation: 4,
                selector: '#player_'+data.caster_id+'_sprite',
				message: data.animate_data.description,
                time:1700,
				queue: true
            });
        },
        burn: function(data){
		    animate.events.pre_battle_checks(data);                    
            if(data.animate_data.miss == true)
            {                        
                battle.missed_target("#monster_"+data.animate_data.target_id+"_sprite");
                $("#monster_"+data.animate_data.target_id+"_sprite").parent().animate({ opacity: 0.5 }, 200, function(){
                    $(this).animate({ opacity: 1 }, 400);
                });
            } 
            else 
            {
                var animation_id = "animation"+rand(1, 1000000);
                var player_location = $("#monster_"+data.animate_data.target_id+"_sprite").offset()
                var burn_html = '<div id="front_burn_'+animation_id+'"></div>';
                    burn_html += '<div id="back_burn_'+animation_id+'"></div>';

                $("#animation_hive").append(burn_html);

                $("#front_burn_"+animation_id).css({
                    position: "absolute",
                    left: player_location.left-20,
                    top: player_location.top,
                    width: 120,
                    height:120,
                    background: "transparent url(/images/animations/burn.png)no-repeat -5px -40px",
                    zIndex: 14,
                    opacity:0,
                });

                $("#back_burn_"+animation_id).css({
                    position: "absolute",
                    left: player_location.left-20,
                    top: player_location.top-10,
                    width: 120,
                    height:120,
                    background: "transparent url(/images/animations/burn_spectacles.gif)no-repeat -15px -5px",
                    zIndex:5,
                    opacity:0
                })

                setTimeout(function(){
                    $("#back_burn_"+animation_id).animate({ opacity: 0.6}, 400);
                    battle.deplete_hp("#monster_"+data.animate_data.target_id+"_sprite", data['animate_data']['amount']);
                    animate.coin_burst("#monster_"+data.animate_data.target_id+"_sprite", parseInt(data.animate_data.jackpot_bonus));
                    $("#front_burn_"+animation_id).animate({ opacity: 0.9, top: "+=13" }, 600, function(){
                        $("#back_burn_"+animation_id).animate({ opacity: 0}, 800);
                        setTimeout(function(){
                            $("#front_burn_"+animation_id).animate({ opacity: 0 }, 800, function(){
                                $(this).remove();
                            })
                        }, 500);
                    });
                }, 300);
            }
            
            setTimeout(function() {
                animate.events.post_battle_checks(data);
            }, 1750);

            animate.process({
                img_width: 720,
                animation: 3,
                selector: '#player_'+data.caster_id+'_sprite',
                message: data.animate_data.description,
                time: 1400,
                queue: true
            });
        },
        slash: function(data){
		    animate.events.pre_battle_checks(data);                    
            var player_location = $('#player_'+data.caster_id+'_sprite').parent().position();
            
            setTimeout(function() {
                animate.events.post_battle_checks(data);
            }, 1600);

            animate.process({
                img_width: 720,
                animation: 0,
                selector: '#player_'+data.caster_id+'_sprite',
				message: data.animate_data.description,
                time:400,
				queue: true,
				move_x: (player_location.left+55),
                callback: function(){
                    if(data.animate_data.miss == true)
                    {                        
                        battle.missed_target("#monster_"+data.animate_data.target_id+"_sprite");
                        $("#monster_"+data.animate_data.target_id+"_sprite").parent().animate({ opacity: 0.5 }, 200, function(){
                            $(this).animate({ opacity: 1 }, 400);
                        });
                    } 
                    else 
                    {
                        battle.sounds.play_sound('rat_hit');
                        setTimeout(function(){                        
                            animate.coin_burst("#monster_"+data.animate_data.target_id+"_sprite", parseInt(data.animate_data.jackpot_bonus));
                        }, 200);

                        battle.deplete_hp("#monster_"+data.animate_data.target_id+"_sprite", data['animate_data']['amount']);
                        
                        if(data.animate_data.critical){
                            battle.critical_target("#monster_"+data.animate_data.target_id+"_sprite");
                        }
                    }
                    
                    animate.process({
                        img_width: 550, // -2 frames
    					animation: 3,
    					selector: '#player_'+data.caster_id+'_sprite',
    					time: 800,
    					callback: function(){
    					    $("#player_"+data.caster_id+"_sprite").addClass('flip_div').parent().stop().css({ left: function(){
    					        return (player_location.left+40)+'px';
    					    }});

                            animate.process({
            					animation: 0,
            					selector: '#player_'+data.caster_id+'_sprite',
            					time: 250,
            					move_x: (player_location.left-15),
            					// Go idle
            					callback: function(){
            					    $("#player_"+data.caster_id+"_sprite").removeClass('flip_div').parent().stop().css({ 
            					        left: function(){ return player_location.left+'px'; }
            					    });
            					}
            				});
                        }
    				});
                }
            });
        },
    },
    items: {
        hp_potion: function(data){
    	    animate.events.pre_battle_checks(data);                    
            setTimeout(function(){
                battle.increase_hp('#player_'+data.caster_id+'_sprite', data['animate_data']['amount']);
                setTimeout(function(){
                    animate.events.post_battle_checks(data);
                }, 750);
            }, 400)
            
            animate.process({
                img_width: 720,
                animation: 7,
                selector: '#player_'+data.caster_id+'_sprite',
				message: data.animate_data.description,
                time:1000,
				queue: true
            });
            
        },
        fruit_orange: function(data){
		    animate.events.pre_battle_checks(data);                    
            this.hp_potion(data)
        },
        fire_bomb: function(data){
		    animate.events.pre_battle_checks(data);                    
            var gold_hive = $("#misc_hive");
            var coin_start_location = $('#player_'+data.caster_id+'_sprite').offset();
            var coin_end_destination = $('#jackpot').offset();
            var i = 0;
            
            gold_hive.append('<img src="/images/exp_orb_1.png" alt="" id="expjp_bomb" style="left:'+(parseInt(coin_start_location.left)+25)+'; top:'+(parseInt(coin_start_location.top)+60)+'; " class="flying_coin" />');

            var jump_back_animation_data = {
                start: { 
                    x: (parseInt(coin_start_location.left)+25), 
                    y: (parseInt(coin_start_location.top)+60), 
                    angle: 140, 
                    length:-0.3
                },  
                end: { 
                    x: $("#battlefield").offset()['left']+($("#battlefield").width() / 1.4), 
                    y: $("#battlefield").offset()['top']+($("#battlefield").height() / 1.6), 
                }
            }

            $('#expjp_bomb').animate({ path: new $.path.bezier(jump_back_animation_data), opacity: 1 }, 700, function(){
                $(this).remove();
            })
            
            
            animate.process({
                img_width: 720,
                animation: 3,
                selector: '#player_'+data.caster_id+'_sprite',
				message: data.animate_data.description,
                time:1600,
				queue: true,
				callback: function(){                                        
				    animate.events.post_battle_checks(data);                    
				}
            });
        },
    },
};
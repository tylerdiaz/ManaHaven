battle['monster_structures']['bat'] = {
    defeated: function(){
        console.log('Rat defeated');
    },
    bite: function(json, key, monster_data){
        if(json == null){
			clearInterval(battle_data['monsters'][key]['attack']);
		} else {
			animate.process({
	    		img_width: 520,
	    		animation: 1,
	    		selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
	    		time: 500, 
	    		move_x: battle_data['monsters'][key]['distance']['x']-50,
	    		queue: true,
	    		message: json.animate_data.description,
	    		callback: function (){ // Bite him 
	    		    if(json.animate_data.miss == true)
                    {
                        battle.missed_target("#player_"+json.animate_data.target_id+"_sprite");
                        $("#player_"+json.animate_data.target_id+"_sprite").parent().animate({ opacity: 0.5 }, 200, function(){
                            $(this).animate({ opacity: 1 }, 400);
                        });
                    } 
                    else 
                    {
    					if(json.battle_lost == true){
    						animate.events.battle_loss(json);
    					} else {
    					    // If you're not dead, block!
     		    			animate.character.block("#player_"+json.animate_data.target_id+"_sprite");						    
    					}

    	    			battle.sounds.play_sound('rat_attack');
    	    			battle.deplete_hp("#player_"+json.animate_data.target_id+"_sprite", json.animate_data.amount, json.real_hp);
    	    			if(json.animate_data.critical) battle.critical_target("#player_"+json.animate_data.target_id+"_sprite");
                    }
                    
	    			animate.process({
	    				animation: 2,
	    				selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
	    				time: 250,
	    				callback: function (){ // Jump Back
	    					animate.process({
	    						animation: 3,
	    						selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
	    						time: 400,
	    						move_x: battle_data['monsters'][key]['distance']['x'],
	    						move_y: battle_data['monsters'][key]['distance']['y'],
	    						callback: function(){ // Go idle
	    							animate.run_queue();
	    							animate.process({
	    								animation: 0,
	    								selector: '#monster_'+monster_data.battle_monster_id+'_sprite',
	    								time: 1600,
	    								loop: true
	    							});
	    						}
	    					});
	    				}   
	    			}); 
	    		}
	    	});
		}
    }
}
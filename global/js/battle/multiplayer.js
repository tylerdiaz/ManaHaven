var multiplayer = {
    polling: {
        data: {
            battle_id: battle_data.battle_id,
            last_event: 0,
            ticker: 0
        },
        start: function(){
            $.ajaxComet.start({
                url: "/comet/multiplayer/battle.php",
                data: multiplayer.polling.data,
                delay: 500,
                timeout: 30000,
                callback: function(json){
                    multiplayer.polling.data.ticker++;
                    multiplayer.polling.data.last_event = json['last_event_id'];
                    var respond_events = json['new_events'];
                    for (var i=0; i < respond_events.length; i++) {
                        multiplayer.responses.render(respond_events[i]);
                    };
                    multiplayer.polling.start();
                }
            });
        },
        stop: function(){
            
        }
    },
    responses: {
        render: function(json){
            if((json.caster_type === 'player' && json.caster_id != battle_data.my_character_id) || (json.caster_type !== 'player' && battle_data.multiplayer_host !== true)){
                if(typeof multiplayer.responses[json.caster_type] === "function"){
                    multiplayer.responses[json.caster_type](json);
                } else {
                    console.error('Unknown caster type');
                }
            }
        },
        player: function(json){
            if(typeof json.error === 'undefined'){
                battle.process_json(json);
            } else {
                console.error('Player response error :: #'+json.caster_id);
            }
        },
        monster: function(json){
            if(typeof json.error === 'undefined'){
                var monster_key = json.caster_id,
                    monster_data = battle_data['monsters'][json.caster_id];

                battle.monster_structures[monster_data['animation_frame']][json.animate_data.animation_key](json, monster_key, monster_data);

                // Reload the energy bar!
                var energy_bar = $("#monster_"+monster_data.battle_monster_id+"_sprite_status .energy_condition div");
                energy_bar.stop().width(0).css('opacity', 0.7).animate({ width: "100%" }, (json.waiting_time-300), "linear", function(){
                    energy_bar.animate({'opacity': 1}, 200);
                });
            } else {
                console.error('Monster response error :: #'+json.caster_id);
            }
        }
    },
    render_response: function(json){
        
        // animate_data:
        //     amount: 2
        //     amount_type: "negative"
        //     animation_key: "bite"
        //     critical: false
        //     description: "Sunlight Bat used Bite!"
        //     miss: true
        //     target: "player"
        //     target_id: 13
        // caster_id: "2663"
        // caster_type: "monster"
        // response_type: "animate"
        // waiting_time: 4000
        
        if(battle_data.multiplayer_host !== true){
            if(json.caster_type == 'monster'){
                console.log(battle_data['monsters'][json.caster_id]);
            }
        }
    },
    
}
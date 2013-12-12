<script type="text/javascript">
    $(document).ready(function(){
        
        var number_words = ["one", "two", "three", "four"];
        var party_data = <?php echo $javascript_data ?>;
        var event_functions = {
            reload_online_list: function(users_online){
                $("#player_lobby div").addClass('empty_slot');
                for (var i = users_online.length - 1; i >= 0; i--){
                    var player_obj = $("#player_"+number_words[i]);
                    player_obj.attr('data-id', users_online[i]['user_id'])
                    player_obj.removeClass('empty_slot');
                    player_obj.find('img').attr('src', '/images/avatars/'+users_online[i]['user_id']+'.gif')
                    player_obj.find('h4').text(users_online[i]['username'])
                    player_obj.find('span').text("Level "+users_online[i]['level'])
                };
                
                // This might conflict with the JS async model. I'll gague on bug reports.
                $("#player_lobby div.empty_slot").each(function(){
                    $(this).attr('data-id', '0')
                    $(this).find('img').attr('src', '/images/avatars/avatar_default.gif')
                    $(this).find('h4').text("Empty")
                    $(this).find('span').text("")
                });
            },
            append_event: function(data){
                $(".grid_x").append('<div>Event #'+data.message+' by '+data.event_user+'</div>')
            },
            chat_bubble: function(data){
                var parent_obj = $('#player_lobby div[data-id="'+data.event_user_id+'"]').attr('id');
                $('#'+parent_obj+'_chat').find('span').html(data.message).parent().fadeIn(200, function(){
                    setTimeout(function(){
                        $('#'+parent_obj+'_chat').fadeOut(750);
                    }, 3500)
                });
            },
            start_countdown: function(data){
                document.title = "Battles are starting - ManaHaven"
                $("#countdown_frame").fadeIn(1500);
                var countdown_canvas = $('#battle_countdown')[0].getContext("2d");
                var seconds_elapsed = 0;
                
                $("#countdown_count").text(data.timer-seconds_elapsed);
                countdown_canvas.clearRect(0, 0, 100, 100);
                countdown_canvas.beginPath();
                countdown_canvas.arc(50, 50, 40, 0, Math.PI * 4, false);
                countdown_canvas.fillStyle = "rgba(0, 0, 0, 0.7)"; // line color
                countdown_canvas.fill(); // line color
                countdown_canvas.lineWidth = 10;
                countdown_canvas.strokeStyle = "#0D3F5B"; // line color
                countdown_canvas.stroke(); // line color
                countdown_canvas.closePath();

                countdown_canvas.beginPath();
                countdown_canvas.lineWidth = 10;
                countdown_canvas.strokeStyle = "#1A8EC6"; // line color
                countdown_canvas.arc(50, 50, 40, (1.5 * Math.PI), ((1.5 + (seconds_elapsed * 0.2)) * Math.PI), false);
                countdown_canvas.stroke();
                countdown_canvas.closePath();
                seconds_elapsed++;
                
                
                var countdown_process = setInterval(function(){
                    if(seconds_elapsed >= data.timer){
                        clearInterval(countdown_process);
                        redirect('battle');
                    }

                    $("#countdown_count").text(data.timer-seconds_elapsed);
                    countdown_canvas.clearRect(0, 0, 100, 100);
                    countdown_canvas.beginPath();
                    countdown_canvas.arc(50, 50, 40, 0, Math.PI * 4, false);
                    countdown_canvas.fillStyle = "rgba(0, 0, 0, 0.7)"; // line color
                    countdown_canvas.fill(); // line color
                    countdown_canvas.lineWidth = 10;
                    countdown_canvas.strokeStyle = "#0D3F5B"; // line color
                    countdown_canvas.stroke(); // line color
                    countdown_canvas.closePath();

                    countdown_canvas.beginPath();
                    countdown_canvas.lineWidth = 10;
                    countdown_canvas.strokeStyle = "#1A8EC6"; // line color
                    countdown_canvas.arc(50, 50, 40, (1.5 * Math.PI), ((1.5 + (seconds_elapsed * 0.2)) * Math.PI), false);
                    countdown_canvas.stroke();
                    countdown_canvas.closePath();
                    seconds_elapsed++;
                }, 1000);
            },
        }
        
        
        var comet_data = {
            url: "/comet/multiplayer/lobby.php",
            data: { party_id: party_data.id, last_event: party_data.last_event },
            callback: function(json){
                // Everytime the comet spits a response
                comet_data.data.last_event = json.last_event_id;
                for (var i = json.new_events.length - 1; i >= 0; i--){
                    // Functionality example: event_functions['reload_online_list'](['user1', 'user2'])
                    event_functions[json.new_events[i]['event_js_action']](json.new_events[i]['event_data']);
                };
                $.ajaxComet.start(comet_data);
            }
        }

        $.ajaxComet.start(comet_data);
        
        // Sign in to the party
        $.getJSON(baseurl+"multiplayer/join_party/"+party_data.id, function(json){
            event_functions.reload_online_list(json);
        });

        // Before you go, sign out!
        $(window).bind('beforeunload', function() {
            $.ajax({
                type: "GET",
                url: "/multiplayer/quick_leave_party/"+party_data.id,
                data: { json: 1 },
                cache: false,
                async: false,
                dataType: "json",
                timeout: 1500,
                success: function(json_topics){
                    console.log(json_topics);
                }
            });
        });
        
        function enable_chat(){
            $("#quick_chat_message").bind('keydown', function(e){
                if(e.keyCode == 13){
                    var message = $(this).val();
                    $("#quick_chat_message").val('')                                
                    $.post('/multiplayer/chat/'+party_data.id, { text: message }, function(){
                        $("#quick_chat_message").unbind('keydown');
                        $("#chat_box").animate({ opacity: 0.3 }, 300, function(){
                            setTimeout(function(){
                                $("#chat_box").animate({ opacity: 1});
                                enable_chat();
                            }, 4000);
                        })
                    });
                }
            })
        }
        
        $("#start_battle").live('click', function(){
            $(this).fadeOut(100);
            $.post('/multiplayer/start_battle/'+party_data.id, { starter: party_data.user.id });
            return false;
        })
        
        enable_chat();
        
    });
</script>
<style type="text/css" media="screen">
    #player_lobby {
        overflow:hidden;
        margin:50px 10px 30px;
    }
    #player_lobby div {
        float:left;
        width:95px;
        height:160px;
        text-align:center;
        color:#aaa;
        margin:0 5px;
        padding:0 10px 0;
        background-color: #111111;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(17, 17, 17, 1.00)), to(rgba(47, 47, 47, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(17, 17, 17, 1.00), rgba(47, 47, 47, 1.00));
        background-image: -moz-linear-gradient(top, rgba(17, 17, 17, 1.00), rgba(47, 47, 47, 1.00));
        background-image: -o-linear-gradient(top, rgba(17, 17, 17, 1.00), rgba(47, 47, 47, 1.00));
        background-image: -ms-linear-gradient(top, rgba(17, 17, 17, 1.00), rgba(47, 47, 47, 1.00));
        background-image: linear-gradient(top, rgba(17, 17, 17, 1.00), rgba(47, 47, 47, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#111111', EndColorStr='#2f2f2f');
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        border-top:1px solid #111;
        border-bottom:1px solid #666;
    }
    #player_lobby div img {
        margin-right:-20px;
    }
    #player_lobby div h5 {
        text-transform:capitalize;
        color:#ddd;
        background:#666;
        font-size:11px;
        padding:3px 0 3px;
        line-height:1;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }
    #player_lobby div h4 {
        text-transform:uppercase;
        color:#eee;
        font-size:13px;
    }
    #player_lobby div span {
        color:#888;
        font-size:11px;
    }
    #player_lobby #player_one h5 { background:#A70008; }
    #player_lobby #player_two h5 { background:#858004; }
    #player_lobby #player_three h5 { background:#108103; }
    #player_lobby #player_four h5 { background:#0B549B; }
    #invite_box {
        position:absolute; width:505px; overflow:hidden; padding:9px 12px; bottom:10px; right:5px; background:#222; font-size:12px; color:#ccc; border-top:1px solid #444; opacity:0.1; 
        -webkit-transition: all 400ms ease-in-out;
        -moz-transition: all 400ms ease-in-out;
        -o-transition: all 400ms ease-in-out;
        transition: all 400ms ease-in-out;
    }
    #main_lobby:hover #invite_box {
        opacity:1;
    }
    .up_arrow {
        width:0px;height:0px;border-left:5px solid transparent;border-right:5px solid transparent;border-bottom:5px solid #222;font-size:0px;line-height:0px; margin:-10px 0 0 5px; float:right;
    }
    .down_arrow {
        width:0px; height:0px; border-left:5px solid transparent; border-right:5px solid transparent; border-top:5px solid ; font-size:0px;line-height:0px; margin:0 0 -10px 5px;
    }
    .top_chat_box {
        min-width:100px; position:absolute; font-size:12px; color:white; padding:3px 6px 5px; display:none;
    }
    .bottom_chat_box {
        min-width:100px; max-width:230px; position:absolute; font-size:12px; color:white; padding:5px 6px 3px; text-align:right; float:right;  display:none;
    }
    .empty_slot {
        opacity:0.3 !important;
    }
    #countdown_frame {
        position:absolute;
        left:210px;
        top:30px;
        z-index:99;
        display:none;
    }
    #battle_countdown {
        position:absolute;
    }
    #countdown_count {
        position:absolute;
        width:100px;
        height:100px;
        text-align:center;
        line-height:100px;
        color:white;
        font-size:38px;
        font-weight:bold;
    }
</style>
<div style="height:350px; background:#111; padding:10px;">
    <div style="float:left; height:350px; width:530px; position:relative;" id="main_lobby">
        
        <div id="countdown_frame">
            <canvas id="battle_countdown" width="100" height="100"></canvas>
            <div id="countdown_count">10</div>
        </div>
        
        <!-- START: Chat bubbles-->
        <div class="top_chat_box" style="background:#690004; top:30px; left:20px" id="player_one_chat">
            <span class="chat_box_message">Testing</span>
            <div class="down_arrow" style="border-top-color:#690004"></div>
        </div>
        <div class="top_chat_box" style="background:#153F01; position:absolute; top:30px; left:270px;" id="player_three_chat">
            <span class="chat_box_message">Testing</span>
            <div class="down_arrow" style="border-top-color:#153F01"></div>
        </div>
        <div class="bottom_chat_box" style="background:#605F03; top:215px; right:270px;" id="player_two_chat">
            <div class="up_arrow" style="border-bottom-color:#605F03"></div>
            <span class="chat_box_message">Testing</span>
        </div>
        <div class="bottom_chat_box" style="background:#083E57; top:215px; right:20px;" id="player_four_chat">
            <div class="up_arrow" style="border-bottom-color:#083E57"></div>
            <span class="chat_box_message">Testing</span>
        </div>
        <!-- END: Chat bubbles -->
        
        <div id="player_lobby">
            <div id="player_one" class="empty_slot">
                <h5>Player Two</h5>
                <?php echo image('avatars/avatar_default.gif', 'width="90" height="90"') ?>
                <h4>Empty</h4>
                <span></span>
            </div>            
            <div id="player_two" class="empty_slot">
                <h5>Player Two</h5>
                <?php echo image('avatars/avatar_default.gif', 'width="90" height="90"') ?>
                <h4>Empty</h4>
                <span></span>
            </div>            
            <div id="player_three" class="empty_slot">
                <h5>Player Three</h5>
                <?php echo image('avatars/avatar_default.gif', 'width="90" height="90"') ?>
                <h4>Empty</h4>
                <span></span>
            </div>            
            <div id="player_four" class="empty_slot">
                <h5>Player four</h5>
                <?php echo image('avatars/avatar_default.gif', 'width="90" height="90"') ?>
                <h4>Empty</h4>
                <span></span>
            </div>            
        </div>
        
        <div id="invite_box">
            <label for="invite_link">Invite your friends: </label>
            <input type="text" name="invite_link" style="border:1px solid orange; background:#111; color:#fff; width:390px; padding:5px; margin-left:5px;" value="http://manahaven.com/multiplayer/party/<?php echo $party_id ?>/?key=<?php echo $party_key ?>" id="invite_link">
        </div>
    </div>
    <div style="float:left; height:320px; width:202px; border-left:2px solid #444; padding:15px 10px 10px; position:relative;">
        <h3 style="line-height:1; text-align:center; color:#eee; font-weight:normal;"><?php echo $party['username'] ?>'s party</h3>

        <p style="font-size:12px; color:#aaa; padding:10px 5px; margin:10px 0; border:1px solid #333; border-width:1px 0;"><strong>Tip #5</strong> - Potions really help survive longer into the battles. Remember to stock up before going on waves!</p>
        
        <div style="margin:20px 0; text-align:center">
            <?php if ($party['author_id'] == $this->session->userdata('id')): ?>
                <a href="#" id="start_battle" class="button huge">Start battling!</a>
            <?php else: ?>
                <p style="color:#aaa; font-size:12px;">Waiting for host to start game...</p>
            <?php endif ?>
        </div>
        
        <div style="position:absolute; bottom:5px; width:205px; height:24px; border-top:1px solid #333; padding:10px 5px; text-align:center; background:#181818" id="chat_box">
            <label for="quick_chat_message"><?php echo image('chat_cloud.png', 'width="24" height="24"') ?></label>
            <input type="text" style="border:1px solid #444; background:#111; padding:3px 3px; font-size:15px; color:#aaa; margin-left:5px; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; width:160px;" name="some_name"  id="quick_chat_message">
        </div>

    </div>
</div>
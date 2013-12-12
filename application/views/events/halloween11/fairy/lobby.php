<style type="text/css" media="screen">
    #player_lobby {
        background:orange;
        overflow:hidden;
        margin:30px 25px;
    }
    #player_lobby div {
        float:left;
        width:120px;
        height:160px;
        text-align:center;
    }
</style>
<div style="height:350px; background:#111; padding:10px;">
    <div style="float:left; height:350px; width:530px; position:relative;">
        <div id="player_lobby">
            <div id="player_one">
                <?php echo image('avatars/4.gif', 'width="90" height="90"') ?>
            </div>
            <div id="player_two">
                <?php echo image('avatars/4.gif', 'width="90" height="90"') ?>
            </div>
            <div id="player_three">
                <?php echo image('avatars/4.gif', 'width="90" height="90"') ?>
            </div>
            <div id="player_four">
                <?php echo image('avatars/4.gif', 'width="90" height="90"') ?>
            </div>            
        </div>
        <!-- <div style="position:absolute; width:205px; overflow:hidden; padding:5px; bottom:0px; right:0; background:#222; font-size:12px; color:#999; border-top:1px solid #444;">
            <label for="invite_link">Invite your friends to the party &darr;</label>
            <input type="text" name="invite_link" style="border:1px solid orange; background:#111; color:#fff; width:204px; padding:5px;" value="http://manahaven.com/multiplayer/invite" id="invite_link">
        </div> -->
        
    </div>
    <div style="float:left; height:320px; width:202px; border-left:2px solid #444; padding:15px 10px 10px; position:relative;">
        <h3 style="line-height:1; text-align:center; color:#eee; font-weight:normal;">Kawaii Hannah's party</h3>
        <p style="font-size:12px; color:#aaa; padding:10px 5px; margin:10px 0; border:1px solid #333; border-width:1px 0;"><strong>Tip #5</strong> - Potions really help survive longer into the battles. Remember to stock up before going on waves!</p>
        <div style="margin:20px 0; text-align:center">            
            <a href="#" class="button huge">Start battling!</a>
        </div>
        <div style="position:absolute; bottom:5px; width:205px; height:24px; border-top:1px solid #333; padding:10px 5px; text-align:center; background:#181818">
            <label for="quick_chat_message"><?php echo image('chat_cloud.png', 'width="24" height="24"') ?></label>
            <input type="text" style="border:1px solid #444; background:#111; padding:3px 3px; font-size:15px; color:#aaa; margin-left:5px; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; width:160px;" name="some_name"  id="quick_chat_message">
        </div>
    </div>
</div>
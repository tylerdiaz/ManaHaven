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

#cta_world_button {
    display:block;
    padding:13px 19px;
    font-size:22px;
    margin:0 15px 5px;
    color:#EFE0D9;
    text-shadow:-1px -1px 0 rgba(0, 0, 0, 0.5);
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    border:2px solid #360909;
    font-family:Lora;
    background: #a90329; /* Old browsers */
    background: -moz-linear-gradient(top, #a90329 0%, #8f0222 44%, #6d0019 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#a90329), color-stop(44%,#8f0222), color-stop(100%,#6d0019)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%); /* Opera11.10+ */
    background: -ms-linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%); /* IE10+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a90329', endColorstr='#6d0019',GradientType=0 ); /* IE6-9 */
    background: linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%); /* W3C */
    -webkit-box-shadow: inset 0px 0px 10px 0px rgba(255, 255, 255, 0.3);
    -moz-box-shadow: inset 0px 0px 10px 0px rgba(255, 255, 255, 0.3);
    box-shadow: inset 0px 0px 10px 0px rgba(255, 255, 255, 0.3);
    letter-spacing:-1px;
    -webkit-animation-duration: 2s;
    -webkit-animation-iteration-count: infinite;
    -webkit-animation-direction: alternate;
    -webkit-animation-timing-function: ease-in-out;
    -webkit-animation-name:'call_to_action';
}

#cta_world_button:active {
    background: #38030e; /* Old browsers */
    background: -moz-linear-gradient(top, #38030e 1%, #4f0516 31%, #68081e 72%, #7a0a24 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#38030e), color-stop(31%,#4f0516), color-stop(72%,#68081e), color-stop(100%,#7a0a24)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, #38030e 1%,#4f0516 31%,#68081e 72%,#7a0a24 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, #38030e 1%,#4f0516 31%,#68081e 72%,#7a0a24 100%); /* Opera11.10+ */
    background: -ms-linear-gradient(top, #38030e 1%,#4f0516 31%,#68081e 72%,#7a0a24 100%); /* IE10+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#38030e', endColorstr='#7a0a24',GradientType=0 ); /* IE6-9 */
    background: linear-gradient(top, #38030e 1%,#4f0516 31%,#68081e 72%,#7a0a24 100%); /* W3C */
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    color:#D8C1B6;
    position:relative;
    top:1px;
}


@-webkit-keyframes call_to_action {
    0% {
        -webkit-box-shadow:0px 0px 18px 2px #C0263A, inset 0px 0px 20px 2px rgba(255, 255, 255, 0.3);
        box-shadow:0px 0px 18px 2px #C0263A, inset 0px 0px 20px 2px rgba(255, 255, 255, 0.3);
    }
    100% {
        -webkit-box-shadow:inset 0px 0px 10px 0px rgba(255, 255, 255, 0.3);
        box-shadow:inset 0px 0px 10px 0px rgba(255, 255, 255, 0.3);
    }
}

#battle_tip {
    position:absolute;
    right:256px;
    bottom:45px;
    display:none;
}

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

.sub_world_title { overflow:hidden; line-height:36px; padding:1px 15px; background:rgba(0, 0, 0, 0.5); -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; }
</style>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        var world_data = {};
        var tip_multiplayer = "";
        
        function reload_main_world(){
            $("#avatar_box img.left").animate({ opacity: 1 })
            $("#world_navigation li").removeClass('active_parchment');
            $("#world_contents").load(baseurl+"world #world_contents");
        }
        
        $("#back_to_world").live('click', function(){
            reload_main_world();
            return false;
        })
        
        $("#world_navigation li").live('click', function(){
            var nav = $(this);
            if(nav.hasClass('coming_soon')){
                alert('This part of the game is under development, it\'ll be ready soon!');
                return false;
            } else {
                if(nav.hasClass('active_parchment')){
                    reload_main_world();
                    return false;
                } else {
                    $("#world_navigation li").removeClass('active_parchment')
                    nav.addClass('active_parchment')
                    $("#world_contents").load(baseurl+nav.attr('ajax'));
                    return false;
                }
            }
        });


		
    });
</script>
<div style="background:#222 url(/images/world_bg.jpg); padding:5px 0; -webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px; color:#ddd; position:relative;" class="clearfix">
    <div class="grid_4" style="height:380px;">
        <div id="world_contents">
            <div style="width:270px; height:120px; margin:140px 140px; text-align:center">
                <?php if( ! $this->system->is_staff()): ?>
                    <div style="background:#aaa; color:#333; font-size:18px; padding:12px 12px; -webkit-border-radius: 6px;
                    -moz-border-radius: 6px;
                    border-radius: 6px; border:2px solid #eee">
                        Battles in the making
                    </div>
                <?php else: ?>
                    <?php echo anchor('battle/start', 'Enter the Gauntlet', 'id="cta_world_button"') ?>
                <?php endif ?>
                <small></small>
            </div>
            <?php echo image('tips/tip1.gif', 'id="battle_tip"') ?>            
        </div>
    </div>
    <div class="grid_2" style="height:386px;">
        <ul id="world_navigation">
            <li id="shops" ajax="world/shop"><a href="#">Shops</a></li>
            <?php if( ! $this->system->is_staff()): ?>
                <li class="coming_soon" id="techniques" ajax="world/techniques"><a href="#">Techniques</a><span>Coming soon!</span></li>
            <?php else: ?>
                <li id="techniques" ajax="world/techniques"><a href="#">Techniques</a></li>
            <? endif; ?>
            <li class="coming_soon" id="forge"><a href="#">Forge</a> <span>Coming soon!</span></li>
            <li class="coming_soon" id="multiplayer"><a href="#">Quests</a> <span>Coming soon!</span></li>
            <?php if( ! $this->system->is_staff()): ?>
                <li id="story" class="coming_soon" ajax="inventory/index"><a href="#">Character</a> <span>Coming soon!</span></li>
            <?php else: ?>
                <li id="story" ajax="world/character"><a href="#">Character</a></li>
            <? endif; ?>
            <li class="coming_soon" id="scavenger"><a href="#">Adventure</a> <span>Coming soon!</span></li>
        </ul>
    </div>
</div>
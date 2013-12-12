<style type="text/css" media="screen">
    .skill_points, .stacked_points {
        background:#000; 
        padding:1px 7px; 
        border:1px solid #555; 
        font-size:17px; 
        color:#FDE576;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        width:18px;
        display:inline-block;
        text-align:center;
    }
    #skill_list {
        list-style:none; 
    }
    #skill_list li { margin:6px 0; }
    #skill_list li div {
        float:left;
        width:90px;
        text-align:right;
        margin-right:15px;
        padding:3px 0;
        color:#ccc;
    }
    .stacked_points {
        color:#FBD92D;
    }
    .decrease, .increase, .disabled_action {
        -webkit-border-radius: 18px;
        -moz-border-radius: 18px;
        border-radius: 18px;
        border:1px solid #777;
        line-height:1;
        padding:1px 4px 2px;
        font-weight:bold;
        margin:0 5px;
        width:10px;
        text-align:center;
        display:inline-block;
    }
    .decrease { color:#BB4F54; }
    .increase { color:#8CB947; }

    .decrease:hover { border-color:#BB4F54; text-decoration:none; color:white; }
    .increase:hover { border-color:#8CB947; text-decoration:none; color:white; }

    .decrease:active, .increase:active { border-color:#aaa; color:white; opacity:0.5; }
    .disabled_action { border-color:#777; color:#ddd; opacity:0.3; cursor:default; }
    .disabled_action:hover { text-decoration:none; color:#ddd }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        var starting_skill_points = <?php echo $this->system->userdata['skill_points'] ?>;
        var skill_points = <?php echo $this->system->userdata['skill_points'] ?>;
        var stats = {
            attack: <?php echo $this->system->userdata['attack'] ?>,
            defense: <?php echo $this->system->userdata['defense'] ?>,
            agility: <?php echo $this->system->userdata['agility'] ?>,
            strength: <?php echo $this->system->userdata['strength'] ?>,
            magick: <?php echo $this->system->userdata['magick'] ?>,
        }
        
        var stat_descriptions = {
            attack: "Your attack determines the amount of damage you will deal to your opponents",
            defense: "Your defense determines how much damage points can you prevent when being attacked",
            agility: "A high agility reduces the motion-sickness time after you use a skill and allows you to dodge some attacks",
            strength: "Your strength increases your total HP points even more, and increases your max weight capacity",
            magick: "Magick will help you learn your techniques faster, and will increase spell effectiveness."
        }

        var stats_modifications = {
            attack: 0,
            defense: 0,
            agility: 0,
            strength: 0,
            magick: 0,
        }
        
        $("#skill_list li").hover(function(){
            $("#stats_description strong").text($(this).find('div').text());
            $("#stats_description p").text(stat_descriptions[$(this).attr('id')]);
        });
        
        $(".increase").die('click');
        $(".increase").live('click', function(){
            if(skill_points > 0){
                $("#confirm_upgrades").fadeIn(400);
                skill_points--;
                stats[$(this).parent().attr('id')]++;
                stats_modifications[$(this).parent().attr('id')]++;
                
                $(this).parent().find('.stacked_points').text(stats[$(this).parent().attr('id')]);
                $(".skill_points").text((skill_points));
                
                // If there is no more skill points, disable the 
                if(skill_points <= 0){
                    $(".increase").removeClass('increase').addClass('disabled_action disabled_increase');
                }
                
                $(this).parent().find('.disabled_decrease').removeClass('disabled_action disabled_decrease').addClass('decrease');
            }
            return false;
        });
        
        $(".decrease").die('click');
        $(".decrease").live('click', function(){
            if(stats_modifications[$(this).parent().attr('id')] > 0){
                $("#confirm_upgrades").fadeIn(400);
                skill_points++;
                stats[$(this).parent().attr('id')]--;
                stats_modifications[$(this).parent().attr('id')]--;
                
                $(this).parent().find('.stacked_points').text(stats[$(this).parent().attr('id')]);
                $(".skill_points").text((skill_points));
                
                // If there is no more skill points, disable the 
                if(stats_modifications[$(this).parent().attr('id')] <= 0){
                    $(this).removeClass('decrease').addClass('disabled_decrease disabled_action');
                }
                
                $('.disabled_increase').removeClass('disabled_action disabled_increase').addClass('increase');
                
                if(skill_points == starting_skill_points){
                    $("#confirm_upgrades").fadeOut(300);
                }
            }
            return false;
        });
        
        $("#confirm_upgrades").unbind('click');
        $("#confirm_upgrades").bind('click', function(){
            if(skill_points < 1){
                $('#world_navigation li[ajax="world/character"]').find('span').fadeOut(200);
            } else {
                $('#world_navigation li[ajax="world/character"]').find('span').text(skill_points);
            }

            $.ajax({
                type: "POST",
                url: "/world/save_character_changes",
                data: { upgrade_skills: stats_modifications },
                dataType: "json",
                success: function(json){
                    stats_modifications = {
                        attack: 0,
                        defense: 0,
                        agility: 0,
                        strength: 0,
                        magick: 0,
                    }
                    $(".decrease").removeClass('decrease').addClass('disabled_decrease disabled_action');
                    $("#confirm_upgrades").fadeOut(500);
                    
                }
            });
            return false;
        });
    });
</script>
<div class="sub_world_title">
    <h3 class="left">Your Character</h3>
    <a href="#" id="back_to_world" class="right">&lsaquo; Back to World</a>
</div>
<div style="background:url(/images/backgrounds/character_template.png)no-repeat left -20px; width:520px; height:300px;">
    <div style="float:left; margin:20px 0 0 12px; width:145px; height:100px; text-align:center; color:#888; line-height:1.3">
         You are in level...
         <h3 style="font-size:38px; color:#FDD63D"><?php echo $this->system->userdata['level'] ?></h3>
         
         <div style="height:100px; margin:40px 0 0 5px; padding:10px 7px; text-align:left; font-size:12px;" id="stats_description">
            <strong></strong><br>
            <p style="color:#ddd"></p>
         </div>
    </div>
    <div style="float:left; width:300px; height:300px; margin-top:25px; margin-left:30px;">
        <h3 style="font-weight:normal; font-size:18px; margin-bottom:10px;">You have <span class="skill_points"><?php echo $this->system->userdata['skill_points'] ?></span> skill points left</h3>
        <ul id="skill_list">
            <li id="attack">
                <div>Attack:</div>
                <a href="#" class="disabled_action disabled_decrease">-</a>
                <span class="stacked_points"><?php echo $this->system->userdata['attack'] ?></span>
                <?php if ($this->system->userdata['skill_points'] > 0): ?>
                    <a href="#" class="increase">+</a>
                <?php else: ?>
                    <a href="#" class="disabled_action disabled_increase">+</a>
                <?php endif ?>
            </li>
            <li id="defense">
                <div>Defense:</div>
                <a href="#" class="disabled_action disabled_decrease">-</a>
                <span class="stacked_points"><?php echo $this->system->userdata['defense'] ?></span>
                <?php if ($this->system->userdata['skill_points'] > 0): ?>
                    <a href="#" class="increase">+</a>
                <?php else: ?>
                    <a href="#" class="disabled_action disabled_increase">+</a>
                <?php endif ?>
            </li>
            <li id="agility">
                <div>Agility:</div>
                <a href="#" class="disabled_action disabled_decrease">-</a>
                <span class="stacked_points"><?php echo $this->system->userdata['agility'] ?></span>
                <?php if ($this->system->userdata['skill_points'] > 0): ?>
                    <a href="#" class="increase">+</a>
                <?php else: ?>
                    <a href="#" class="disabled_action disabled_increase">+</a>
                <?php endif ?>
            </li>
            <li id="strength">
                <div>Strength:</div>
                <a href="#" class="disabled_action disabled_decrease">-</a>
                <span class="stacked_points"><?php echo $this->system->userdata['strength'] ?></span>
                <?php if ($this->system->userdata['skill_points'] > 0): ?>
                    <a href="#" class="increase">+</a>
                <?php else: ?>
                    <a href="#" class="disabled_action disabled_increase">+</a>
                <?php endif ?>
            </li>
            <li id="magick">
                <div>Magick:</div>
                <a href="#" class="disabled_action disabled_decrease">-</a>
                <span class="stacked_points"><?php echo $this->system->userdata['magick'] ?></span>
                <?php if ($this->system->userdata['skill_points'] > 0): ?>
                    <a href="#" class="increase">+</a>
                <?php else: ?>
                    <a href="#" class="disabled_action disabled_increase">+</a>
                <?php endif ?>
            </li>
        </ul>
        <br>
        <button class="right" style="display:none" id="confirm_upgrades">Confirm upgrades</button>
    </div>
</div>
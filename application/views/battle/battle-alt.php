<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("#battle_icon").toggle(function(){
            $("#available_skills li").first().animate({ top: "-2px", opacity: 0.5 }, 200)
            $("#available_skills li:nth-child(2)").animate({ top: "18px", opacity: 0.5, left: "43px"}, 200)
            $("#available_skills li:nth-child(3)").animate({ top: "55px", opacity: 0.5, left: "53px"}, 200)
            $("#available_skills li:nth-child(4)").animate({ top: "92px", opacity: 0.5, left: "43px" }, 200)
            $("#available_skills li:nth-child(5)").animate({ top: "118px", opacity: 0.5 }, 200)
            $("#available_skills li:nth-child(6)").animate({ top: "55px", opacity: 0.5, left: "90px" }, 200)
            $("#available_skills li:nth-child(7)").animate({ top: "18px", opacity: 0.5, left: "80px" }, 200)
            $("#available_skills li:nth-child(8)").animate({ top: "92px", opacity: 0.5, left: "80px" }, 200)
        }, function(){
            $("#available_skills li").animate({ top: "50px", opacity: 0, left: "5px" }, 200)
        });
        
        $("#available_skills li a").live('click', function(){
            var mommi_li = $(this).parent();
            var parent_id = mommi_li.attr('id');
            mommi_li.css({ opacity: 1 })
            $('#available_skills li[id!="'+parent_id+'"]').animate({opacity: 0 }, 200, function(){
                $('#available_skills li[id!="'+parent_id+'"]').css({ top: "50px", left: "5px" });
            });
        })
    });
</script>
<style type="text/css" media="screen">
    #battle_icon {
        background:transparent url(<?php echo site_url('images/battle_orb.png') ?>)no-repeat left top;
        display:block;
        width:81px;
        height:81px;
        position:absolute;
        left:100px;
        top:90px;
        z-index:9;
    }
    #battle_icon:hover { background:transparent url(<?php echo site_url('images/battle_orb.png') ?>)no-repeat left center; }
    #battle_icon:active { background:transparent url(<?php echo site_url('images/battle_orb.png') ?>)no-repeat left bottom; }
    #available_skills_container {
        position:absolute;
        left:140px;
        top:55px;
        z-index:8;
        height:160px;
        background:rgba(0, 0, 0, 0.5)
    }
    #available_skills {
        position:relative;
        height:160px;
        width:200px;
    }
    #available_skills li {
        list-style:none;
        float:left;
        position:absolute;
        top:50px;
        left:5px;
        opacity:0;
        border:2px solid black;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        overflow:hidden;
    }
    #available_skills li:hover {
        opacity:1 !important;
    }
</style>
<div class="grid_5" style="position:relative">
    <div style="width:140px; background:#222; float:left; height:240px;">
        <a href="#" id="battle_icon"></a>
        <div id="available_skills_container">
            <ul id="available_skills">
                <li id="skill_1"><a href="#"><?php echo image('skill_icons/arrow_shot') ?></a></li>
                <li id="skill_2"><a href="#"><?php echo image('skill_icons/arrow_shot') ?></a></li>
                <li id="skill_3"><a href="#"><?php echo image('skill_icons/arrow_shot') ?></a></li>
                <li id="skill_4"><a href="#"><?php echo image('skill_icons/arrow_shot') ?></a></li>
                <li id="skill_5"><a href="#"><?php echo image('skill_icons/arrow_shot') ?></a></li>
            </ul>
        </div>
    </div>
    <div style="height:240px; width:495px; float:left; background:orange; position:relative">
        <canvas width="495" height="240" id="fx_overlay" style="position:absolute; top:0; left:0; "></canvas>
        <canvas width="495" height="240" id="fx_canvas" style="position:absolute; top:0; left:0;"></canvas>
    </div>
</div>
<div class="grid_1">
    <h4 style="background: #AE1D26; color:#fff; text-align:center; padding:7px 0; 
    -webkit-box-shadow: inset 0px 0px 3px 1px rgba(0, 0, 0, 0.8);
    -moz-box-shadow: inset 0px 0px 3px 1px rgba(0, 0, 0, 0.8);
    box-shadow: inset 0px 0px 3px 1px rgba(0, 0, 0, 0.8);
    margin-bottom:15px; text-shadow:1px 1px 1px #000">Wave #1</h4>
    <h4>Objectives (1/3)</h4>
    <ul id="objective_list">
        <li class="completed"><?php echo image('task_completed.png') ?> <p>Defeat a monster</p></li>
        <li class="uncompleted"><?php echo image('task_uncompleted.png') ?> <p>Learn a technique</p></li>
        <li class="uncompleted"><?php echo image('task_uncompleted.png') ?> <p>Use a potion</p></li>
    </ul>
</div>
<style type="text/css">
    #user_header {
        margin:5px;
    }
    .online_stamp {
        background:#D1EC7D;
        color:#415310;
        line-height:1.2;
        padding:2px 6px 2px;
        font-size:12px;
        margin:7px 10px 0;
        display:inline-block;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .offline_stamp {
        background:#ddd;
        color:#777;
        line-height:1.2;
        padding:2px 6px 2px;
        font-size:12px;
        margin:7px 10px 0;
        display:inline-block;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .statistic_block {
        float:left;
        width:115px;
        background:#eee;
        margin:5px;
        text-align:center;
        padding:7px 0;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
    }
    .statistic_block span { font-size:12px; }
    .statistic_block h3 { font-size:30px; line-height:1.2; }
    
    .statistic_level { background:#FDD2D6; color:#610008; }
    .statistic_items { background:#D7FFC3; color:#3E6508; }
    .statistic_posts { background:#F6F3C4; color:#625F08; }
    .statistic_friends { background:#E3DBFC; color:#47367D; }
    .statistic_quests { background:#C6F1FE; color:#0A4E6B; }
</style>
<div class="grid_1">
    <canvas id="profile_avatar" width="110" height="130"></canvas>
</div>
<div class="grid_5">
    <div class="clearfix" id="user_header">
        <h2 id="profile_username" class="left"><?php echo $user_data['username'] ?></h2>
        <?php if ($user_data['last_activity'] > time()-1000): ?>
            <span class="left online_stamp">Online</span>
        <?php else: ?>
            <span class="left offline_stamp">Offline</span>
        <?php endif ?>
        <?php if($friendship && $user_data['last_activity'] > time()-1000): ?>
            <span class="right" style="color:#666; line-height:28px">Currently <?php echo $user_location ?></span>
        <?php endif; ?>
    </div>
    <div class="statistic_block statistic_level">
        <span>Level:</span>
        <h3><?php echo $user_data['level'] ?></h3>
    </div>
    <div class="statistic_block statistic_items">
        <span>Total items:</span>
        <h3>{total_items}</h3>
    </div>
    <div class="statistic_block statistic_posts">
        <span>Total posts:</span>
        <h3>{total_posts}</h3>
    </div>
    <div class="statistic_block statistic_friends">
        <span>Friends:</span>
        <h3>{total_friends}</h3>
    </div>
    <div class="statistic_block statistic_quests">
        <span>Quests finished:</span>
        <h3>0</h3>
    </div>
</div>
<br clear="all" />
<div style="margin-top:5px; border-top:3px solid #EDE2D0; background-color: #fff6ed;
background-image: -webkit-gradient(linear, left top, left bottom, to(rgba(255, 246, 237, 1.00)), from(rgba(255, 255, 255, 1.00)));
background-image: -webkit-linear-gradient(top, rgba(255, 246, 237, 1.00), rgba(255, 255, 255, 1.00));
background-image: -moz-linear-gradient(top, rgba(255, 246, 237, 1.00), rgba(255, 255, 255, 1.00));
background-image: -o-linear-gradient(top, rgba(255, 246, 237, 1.00), rgba(255, 255, 255, 1.00));
background-image: -ms-linear-gradient(top, rgba(255, 246, 237, 1.00), rgba(255, 255, 255, 1.00));
background-image: linear-gradient(top, rgba(255, 246, 237, 1.00), rgba(255, 255, 255, 1.00));
filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#fff6ed', EndColorStr='#ffffff');
min-height:240px; padding-top:5px" class="clearfix">
    <div class="grid_3">
        <div style="text-align:center; font-size:15px; margin:30px 10px; opacity:0.3">This half of our profiles are under development.</div>
    </div>
    <div class="grid_3">
        <div class="widget clearfix" id="allies">
            <div class="widget_title clearfix">
                <h3 class="left">Profile Comments (<span id="comment_count"><?php echo $total_comments ?></span>)</h3>
            </div>
            <ul id="profile_comments" class="list_couple">
            <?php if (count($comments) < 1): ?>
                </ul>
                <div id="no_comments" style="text-align:center; font-size:15px; margin:30px 0; opacity:0.3"><?php echo $user_data['username'] ?> has no comments yet!</div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <li id="comment_<?php echo $comment['comment_id'] ?>">
                        <?php echo image('avatars/thumbnails/'.$comment['commenter_id'].'.gif', 'class="c_image left"') ?>
                        <div class="c_title"><strong><?php echo anchor('profile/'.urlencode($comment['commenter_username']), $comment['commenter_username']); ?></strong> said: <span class="timestamp right"><?php echo human_time($comment['comment_timestamp']) ?>
                            <?php if ($this->session->userdata('id') && ($comment['commenter_id'] == $this->system->userdata['id'] || $user_data['id'] == $this->system->userdata['id'])): ?>
                                <a href="#" class="btn_delete delete_comment" data_comment_id="<?php echo $comment['comment_id'] ?>">x</a>
                            <?php endif ?>
                        </span></div>
                        <p><?php echo nl2br(sanitize($comment['comment_messsage'])) ?></p>
                    </li>
                <?php endforeach ?>
                </ul>
            <?php endif ?>
            <?php if ($this->session->userdata('id')): ?>
                <div id="comment_spam_error" style="display:none; font-size:12px; color:#543603; background:#ffc; text-align:center; padding:5px; border-top:1px solid orange;border-bottom:1px solid orange;">Woah, that's sure is a lot of comments! You should take a little break before posting another one!</div>
                <textarea name="message" class="post_textfield" id="comment_text" style="width:370px; margin:10px 3px" placeholder="Leave a comment..."></textarea>
                <span class="left" style="font-size:12px; line-height:36px; padding-left:10px;"><span id="chars_left">255</span> Characters left</span>
                <button type="submit" id="submit_comment" class="right mini">Publish Comment</button>
            <?php endif ?>
        </div>
    </div>
</div>
<style type="text/css">
    .list_couple li {
        list-style:none;
        clear:both;
        overflow:hidden;
        margin:3px 0;
    }
    .list_couple .c_image {
        margin-right:5px;
    }
    .list_couple .c_title {
        line-height:20px;
        font-size:12px;
        margin:4px 0 0;
        color:#444;
    }
    .list_couple p {
        overflow:hidden;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        var total_posted_comments = 0;
        var canvas = $('#profile_avatar')[0].getContext("2d");
        var small_avatar = new Image();
        small_avatar.src = "/images/avatars/<?php echo $user_data['id'] ?>.gif?<?php echo $user_data['last_saved_avatar'] ?>";
        small_avatar.onload = function() {
            canvas.globalAlpha = 0.1;
            canvas.drawImage(small_avatar, -30, -50, 180, 180);
            canvas.globalAlpha = 1;
            
            canvas.drawImage(small_avatar, 0, 10);
        };

        var chars_allowed = 255;
        
        $(".delete_comment").live('click', function(){
            var comment_id = $(this).attr('data_comment_id');
            var comment_obj = $(this);
            
            $.ajax({
                type: "POST",
                url: "/aether/delete_comment/",
                data: { id: comment_id },
                dataType: "json",
                success: function(json){
                    var parent_comment_obj = $("#comment_"+comment_id);
                
                    parent_comment_obj.animate({ backgroundColor: "#FCA1A5" }, 500, function(){
                        parent_comment_obj.fadeOut(500, function(){
                            
                            if(typeof json.comment_id != "undefined"){
                                var comment_html = "<li id=\"comment_"+json.comment_id+"\" style=\"background:rgba(255, 255, 255, 0)\"> \
                                    <img src=\"/images/avatars/thumbnails/"+json.commenter_id+".gif\" alt=\"\" class=\"c_image left\"> \
                                    <div class=\"c_title\"><strong>"+json.commenter_username+"</strong> said: <span class=\"timestamp right\">"+json.comment_timestamp+"</span></div> \
                                    <p>"+json.comment_messsage+"</p> \
                                </li>";

                                $("#profile_comments").append(comment_html);
                                
                                
                                $("#comment_"+json.comment_id).animate({ backgroundColor: "#DFF0FE" }, 500, function(){
                                    var new_comment_obj = $(this);
                                    setTimeout(function(){
                                        new_comment_obj.animate({ backgroundColor: "rgba(255, 255, 255, 0)" }, 1000);
                                    }, 1000)
                                });
                            }
                            
                        });
                    });
                }
            });
            return false;
        });
        
        $("#comment_text").keydown(function(e){
    		if(e.keyCode == 13 && e.shiftKey){
                $("#submit_comment").submit();
                return false;
            }
    	});
        
        $("#submit_comment").bind('submit click', function(e){
            if(total_posted_comments > 4){
                $("#comment_spam_error").slideDown("slow");
            } else {
                $("#submit_comment").html('<img src="/images/ajax/posting_ajax.gif" alt=""> Posting...').animate({ opacity: 0.5 });

                $.ajax({
                    type: "POST",
                    url: "/aether/post_comment/<?php echo $user_data['id'] ?>",
                    data: { comment: $("#comment_text").val() },
                    dataType: "json",
                    success: function(json){
                        var profile_comments = $("#profile_comments li");
                        if(profile_comments.length >= 5){
                            profile_comments.last().hide(0, function(){
                                $(this).remove();
                            });
                        } else {
                            console.log($("#profile_comments li").length);
                        }

                        $("#chars_left").text(255);

                        var comment_html = "<li id=\"comment_"+json.id+"\" style=\"background:rgba(255, 255, 255, 0)\"> \
                            <img src=\"/images/avatars/thumbnails/<?php echo $this->session->userdata('id') ?>.gif\" alt=\"\" class=\"c_image left\"> \
                            <div class=\"c_title\"><strong>"+json.username+"</strong> said: <span class=\"timestamp right\">Moments ago...</span></div> \
                            <p>"+json.message+"</p> \
                        </li>";

                        $("#submit_comment").text('Publish Comment').animate({ opacity: 1 });
                        $("#no_comments").fadeOut(500);

                        $("#comment_count").text(parseInt($("#comment_count").text())+1);
                        $("#comment_text").val('');
                        $("#profile_comments").prepend(comment_html)
                        $("#comment_"+json.id).css({ backgroundColor: "#ffa" });
                        var comment_obj = $("#comment_"+json.id);
                        setTimeout(function(){
                            comment_obj.animate({ backgroundColor: "rgba(255, 255, 255, 0)" }, 1500);
                        }, 500)

                        total_posted_comments++;
                        
                        if(total_posted_comments > 4){
                            $("#comment_spam_error").slideDown("slow");
                        }
                    },
                });
            }
            return false;
        })
        $("#comment_text").bind('keyup keypress', function(e){
            var total_chars = $("#comment_text").val().length;
            
            if(total_chars > chars_allowed){ 
                $("#chars_left").css('color', 'red')
            } else {
                $("#chars_left").css('color', 'black')
                $("#chars_left").text((chars_allowed-total_chars))
            }
        })
    });
</script>
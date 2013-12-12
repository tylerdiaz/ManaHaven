<script type="text/javascript" charset="utf-8">
    var highlighted_category = 0;
    $(document).ready(function(){
        // Test!
        $("ul.category_list").tabs("div.forum_content > div");
        
        $("ul.category_list li a").bind('click', function(){
            $("#create_topic").animate({opacity: 1});
        });
                
        $("#create_topic").live('click', function(){            
            highlighted_category = $('.category_list li a.current').attr('id');
            popup.create({
                title: "Create a new topic",
                content: { ajax: "/community/create_topic_template" },
                cancel_button: { label: 'Cancel' },
                data: { highlighted_category: highlighted_category },
                confirm_button: { 
                    label: 'Publish topic &rsaquo;', 
                    callback: function(e){
                        var forgot_category = 0; // false
                        
                        // If there is no category selected, let's dump it in the Gameplay Chat
                        if($("#category").val().length == 0){
                            $("#category").val('4');
                            forgot_category = 1; // true
                        }
                        
                        $.ajax({
                            type: "POST",
                            url: "/community/create_topic/",
                            data: { category: $("#category").val(), title: $("#topic_title").val(), message: $("#topic_message").val(), forgotten_category: forgot_category },
                            dataType: "json",
                            success: function(json){
                                if(typeof json.error != "undefined"){
                                    popup.report_error();
                                } else {
                                    redirect('community/topic/'+json.topic_id);
                                }
                            },
                            error: function(xhr, status, error){
                                popup.report_error(error);
                            }
                        });
                        
                        e.preventDefault();
                    } 
                }
            });
            return false;
        });
        
        $(".load_more_posts").bind('click', function(){
            var more_posts = $(this);
            var total_posts = parseInt(more_posts.attr('data_total_posts'));
            var start_html = more_posts.html();
            
            $.getJSON(baseurl+"community/load_more_posts/"+more_posts.attr('data_forum_id')+"/"+total_posts, function(json){
                more_posts.css({opacity: 0.5}).html('Loading new topics...');
                var special_ids = {};
                if(json.length < 10){ // Disable it!
                    more_posts.css({
                        opacity: 0.8, 
                        background: "#ddd", 
                        color: "#888", 
                        cursor: "default",
                        textDecoration: "underline",
                    }).html('All topics have been loaded :)');
                    more_posts.unbind('click');
                    more_posts.bind('click', function(){
                        return false;
                    })
                }

                more_posts.attr('data_total_posts', total_posts+(json.length));
                
                var timeout_i = 0;
                var post_html = "";
                for (var i=0; i < json.length; i++) {
                    special_ids[i] = 'tmp_tablerow_'+rand(0, 9999);
                    
                    post_html += '<tr class="topic_row" id="'+special_ids[i]+'" style="display:none;"> \
                                    <td> \
                                        '+json[i]['topic_link']+'<br> \
                                        <span> By '+json[i]['creator_username']+' &bull; <?php echo image('mini_chat.gif')?> '+json[i]['last_post_link']+'</span> \
                                    </td> \
                                    <td> \
                                        <?php echo image('mini_watch.gif')?> '+json[i]['timestamp']+'<br> \
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;By '+json[i]['author_link']+'</small> \
                                    </td> \
                                  </tr>';

                    setTimeout(function(){
                        $("#"+special_ids[timeout_i]).fadeIn(100, function(){
                            $(this).attr('id', '');
                        });

                        if(timeout_i+1 == json.length && (json.length >= 10)){
                            // Last one!
                            more_posts.css({opacity: 1}).html(start_html);
                        }
                        timeout_i++;
                    }, i*100);

                };
                $("#forum_table_"+more_posts.attr('data_forum_id')).append(post_html);
                
            });
            
            return false;
        });
        
        // Highlight your active topics
        setTimeout(function(){
            var my_topics = $('tr[data_author_id="'+user_id+'"]');

            for (var i = my_topics.length - 1; i >= 0; i--){
                var topic_tr_obj = $(my_topics[i]);
                var cached_last_post = local_db.get('topic_'+topic_tr_obj.attr('data_topic_id')+'_last_post');
                if(cached_last_post != null && parseInt(topic_tr_obj.attr('data_last_post'))+1 > cached_last_post){
                    topic_tr_obj.animate({
                        backgroundColor: "#ffa",
                        fontWeight: "bold"
                    }, 200);
                }
            };
        }, 500);
        
    });
</script>
<style type="text/css">
    .empty_category {
        background:#D0E8F1;
        padding:15px 25px;
        color:#4B7687;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        margin-top:5px;
        border:1px solid #C0DAE3;
        text-align:center;
    }
    .online_user img {
        width:34px;
        height:34px;
    }
    
    .custom_forum_content_mn {
        background-color: #e6efff;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(230, 239, 255, 1.00)), to(rgba(255, 255, 255, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(230, 239, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -moz-linear-gradient(top, rgba(230, 239, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -o-linear-gradient(top, rgba(230, 239, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -ms-linear-gradient(top, rgba(230, 239, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: linear-gradient(top, rgba(230, 239, 255, 1.00), rgba(255, 255, 255, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#e6efff', EndColorStr='#ffffff');
    }
    
    .custom_forum_content_br {
        background-color: #e8ddff;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(232, 221, 255, 1.00)), to(rgba(255, 255, 255, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(232, 221, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -moz-linear-gradient(top, rgba(232, 221, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -o-linear-gradient(top, rgba(232, 221, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -ms-linear-gradient(top, rgba(232, 221, 255, 1.00), rgba(255, 255, 255, 1.00));
        background-image: linear-gradient(top, rgba(232, 221, 255, 1.00), rgba(255, 255, 255, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#e8ddff', EndColorStr='#ffffff');
    }
    
    .custom_forum_content_fb {
        background-color: #f6eadd;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(246, 234, 221, 1.00)), to(rgba(255, 255, 255, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(246, 234, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -moz-linear-gradient(top, rgba(246, 234, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -o-linear-gradient(top, rgba(246, 234, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -ms-linear-gradient(top, rgba(246, 234, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: linear-gradient(top, rgba(246, 234, 221, 1.00), rgba(255, 255, 255, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#f6eadd', EndColorStr='#ffffff');
    }
    .custom_forum_content_gp {
        background-color: #f6f6dd;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(246, 246, 221, 1.00)), to(rgba(255, 255, 255, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(246, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -moz-linear-gradient(top, rgba(246, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -o-linear-gradient(top, rgba(246, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -ms-linear-gradient(top, rgba(246, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: linear-gradient(top, rgba(246, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#f6f6dd', EndColorStr='#ffffff');
    }
    .custom_forum_content_hg {
        background-color: #ddf6dd;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(221, 246, 221, 1.00)), to(rgba(255, 255, 255, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(221, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -moz-linear-gradient(top, rgba(221, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -o-linear-gradient(top, rgba(221, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: -ms-linear-gradient(top, rgba(221, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        background-image: linear-gradient(top, rgba(221, 246, 221, 1.00), rgba(255, 255, 255, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ddf6dd', EndColorStr='#ffffff');
        
    }
</style>
<div style="width:150px; float:left; padding-left:10px; height:300px; ">

    <?php $this->load->view('community/category_sidebar', array('forums' => $forums)) ?>

    <div style="margin:35px 0 30px 6px;">
        <a href="#" class="button" id="create_topic">Create a topic</a>
    </div>
    <div class="faded" id="who_is_online">
        <strong style="color:#999">Who's online? (<?php echo count($users_online) ?>)</strong><br>
        <?php foreach ($users_online as $user): ?>
            <?php echo anchor('profile/'.urlencode($user['username']), image('avatars/thumbnails/'.$user['user_id'].'.gif', 'width="30" height="30"'), 'title="'.$user['username'].'"'); ?>
        <?php endforeach ?>
    </div>
    <!-- <div style="margin-top:15px; font-size:12px; color:#666;">
        <strong style="color:#999">Username colors:</strong><br>
        <div style="width:10px; height:10px; background:orange; display:inline-block;"></div> Administrator<br>
        <div style="width:10px; height:10px; background:#CF359F; display:inline-block;"></div> Staff Member
    </div> -->
</div>
<div class="forum_content">
    <?php foreach ($topics as $forum_id => $topic_list): ?>
        <div class="custom_forum_content_<?php echo $forums[$forum_id+1]['clean_name'] ?>" style="display:<?php echo (($forum_id == 0) ? 'block' : 'none') ?>">
        <?php if (count($topic_list) > 0): ?>
            <table class="clean" id="forum_table_<?php echo ($forum_id+1) ?>">
                <tr>
                    <th>Topic Overview</th>
                    <th width="140">Last Post...</th>
                </tr>
                <?php foreach ($topic_list as $topic): ?>
                    <tr class="topic_row" data_topic_id="<?php echo $topic['topic_id'] ?>" data_last_post="<?php echo $topic['total_replies'] ?>" data_author_id="<?php echo $topic['author_id'] ?>">
                        <td><?php echo anchor('community/topic/'.$topic['topic_id'], $topic['title'], ' class="topic_title"')?><br><span> By <?php echo $topic['author_username'] ?> &bull; <?php echo image('mini_chat.gif')?> <?php echo anchor('community/topic/'.$topic['topic_id'].'/'.(floor(($topic['total_replies']-1)/12)*12), $topic['total_replies'].' &rsaquo;'); ?></span></td>
                        <td><?php echo image('mini_watch.gif')?> <?php echo human_time($topic['last_post_time']) ?><br><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;By <?php echo anchor('profile/'.urlencode($topic['last_post_by']), $topic['last_post_by']) ?></small></td>
                    </tr>
                <?php endforeach ?>
            </table>
            <?php if (count($topic_list) == 10): ?>
                <a href="#" class="load_more_posts" data_total_posts="<?php echo count($topic_list) ?>" data_forum_id="<?php echo ($forum_id+1) ?>" style="display:block; margin-top:5px; text-align:center; background:#D7F0FE; padding:6px 0; font-weight:bold;">Load more topics <span style="font-size:10px">&#9660;</span></a>
            <?php endif ?>
        <? else: ?>
            <div class="empty_category">
                <h3><?php echo image('ninja_icon.png', 'width="19" height="18"') ?> It sure is quiet in here...</h3>
                <p>You should break the silence by being the first to create a topic here!</p>
            </div>
        <?php endif ?>
        </div>
    <?php endforeach ?>
    <div class="new_topic_form" style="display:none">
        <form action="">
            <label for="">Title</label>
        </form>
    </div>
</div>
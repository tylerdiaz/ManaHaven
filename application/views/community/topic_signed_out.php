<div style="width:150px; float:left; padding-left:10px; height:300px; ">
    <?php $this->load->view('community/category_sidebar', array('forums' => $forums, 'unfocus' => TRUE)) ?>
</div>
<div class="forum_content">
    <div style="padding-top:0">
    <div class="topic_header">
        <?php echo image('avatars/thumbnails/'.$topic['author_id'].'.gif', 'class="author_avatar"') ?>
        <h3 class="topic_title"><span><?php echo $topic['title'] ?></span>
        </h3>
        <span class="extra_data"><?php echo image('mini_chat.gif', 'title="Total posts"')?> <span id="topic_post_count"><?php echo $topic['total_replies'] ?></span> &bull; <?php echo image('mini_date.gif', 'title="Created at..."')?> <?php echo date("F j, o", strtotime($topic['date_created'])) ?> &bull; Author:
        <?php echo anchor('profile/'.urlencode($topic['author_username']), $topic['author_username']) ?></span>
    </div>
    <style type="text/css">
        .offline_bubble {
            width:7px;
            height:7px;
            background:#ddd;
            -webkit-border-radius: 12px;
            -moz-border-radius: 12px;
            border-radius: 12px;
            float:left;
            margin-right:4px;
            margin-top:5px;
            border:1px solid #ccc;
        }
        .online_bubble {
            width:7px;
            height:7px;
            background:#BBE736;
            -webkit-border-radius: 12px;
            -moz-border-radius: 12px;
            border-radius: 12px;
            float:left;
            margin-right:4px;
            margin-top:5px;
            border:1px solid #9FCB29;
        }
        .editable_post .message:hover {
            background:#ffb;
            cursor:text;
        }
    </style>
    
    {topic_notice}
    
    <ul class="topic_posts">
        <?php foreach ($posts as $post): ?>
            <li class="post" id="<?php echo $post['post_id'] ?>">
                <?php echo image('avatars/'.cycle('', 'flip/').''.$post['author_id'].'.gif', 'class="post_avatar" width="90" height="90"')?>
                <div class="post_contents">
                    <div class="post_text">
                        <div class="post_data">
                            <?php $status = (in_array($post['author_id'], $users_online) ? 'online' : 'offline') ?>
                            <span class="left"><div class="<?php echo $status ?>_bubble status_bubble"></div>
                                <?php echo anchor('/profile/'.$post['author_username'], $post['author_username'], 'class="strong author_username" style="'.staff_color($post['author_id']).'"') ?> said:</span>
                            <span class="right"><?php echo image('mini_watch.gif')?> <span class="post_timestamp" post_timestamp="<?php echo date("o-m-d\TH:i:s", strtotime($post['post_time'])) ?>"><?php echo human_time($post['post_time']) ?></span> </span>
                        </div>
                        <div class="message"><?php echo simple_bbcode(nl2br(sanitize($post['text']))) ?></div>
                    </div>
                    <div class="post_close_bubble"></div>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
    
    <div style="text-align:center; color:#aaa; margin:10px 0 20px; padding: 20px 0; border-top:1px solid #ddd;">
        Psst... you should create an adventurer to be a part of awesome topics like this one.<br>
        <?php echo anchor('/home', 'Create your adventurer instantly'); ?>
    </div>
    </div>
    <br clear="all" />
</div> 
<script type="text/javascript">
    $(document).ready(function(){
        
        var at_page_bottom = false;
        
        $(window).scroll(function(){
            at_page_bottom = $(window).scrollTop() > parseInt($('.structure').height()-$(window).height()/0.7);
            //if(at_page_bottom == true) document.title = topic.title;
        })

        var topic = {
            id: <?php echo $topic['topic_id'] ?>,
            last_post_id: <?php echo ($topic['total_replies']+1) ?>,
            title: document.title,
            new_post_count: 0
        }

        var user = {
            username: null,
            user_id: 0
        }
        
        var poll_request;
        var poll_data = false;
        var last_post = "";
        var window_focused = false;
        var polling_time = 60000; // 1 minute
        
        function color_name(user_id){
            if(user_id == 4) return "color:#DA6506; background:#ffd;";
            if(user_id == 108 || user_id == 15 || user_id == 158) return "color:#CF359F; background:#FEE4F2;";
            return '';
        }
                
        function append_post(json, reward){
            $("#topic_post_count").increase(1);
            var new_post = $('.topic_posts li.post').first().clone();
            var total_posts = $('.topic_posts li.post').length; // (total_posts % 2 ? 'flip/' : '') to make sure we flip the avatars accordingly when they're loaded
            new_post.attr('id', json.post_id);
            if(json.user_id == user.user_id){
                new_post.addClass('editable_post');
            } else {
                new_post.removeClass('editable_post');
            }

            new_post.find('.post_avatar').attr('src', '/images/avatars/'+(total_posts % 2 ? 'flip/' : '')+json.user_id+'.gif');
            new_post.find('.message').html(decodeURIComponent(json.message));
            new_post.find('a.author_username').text(json.username).attr('href', '/profile/'+json.username).attr('style', color_name(json.user_id));
            // We can assume if they posted 5 seconds ago, they're online.
            new_post.find('.status_bubble').removeClass('offline_bubble').addClass('online_bubble')
            new_post.find('.post_timestamp').text("Moments ago...").attr('post_timestamp', json.post_timestamp);
            if(typeof reward != 'undefined'){
                new_post.find('span.right').append($('<img src="/images/coins.png" width="13" height="13" style="vertical-align:middle; margin-left:3px" /> <strong style="color:green">+'+reward+'</strong>'));
            }
            $(".topic_posts").append(new_post);
        }
        
        
        var double_check;
        
        $(window).focus(function() {
            document.title = topic.title;
            window_focused = true;
            topic.new_post_count = 0;
        });

        $(window).blur(function() {
            window_focused = false;
         });
        
        function start_polling(topic_id, last_post_id){
            if(poll_data === false){
                poll_data = {};
                poll_data.topic_id = topic.id;
                poll_data.last_post_id = topic.last_post_id;
            }
            
            topic_id = (typeof topic_id == 'undefined') ? poll_data['topic_id'] : topic.id;
            last_post_id = (typeof last_post_id == 'undefined') ? poll_data['last_post_id'] : topic.last_post_id;
            
    		poll_request = $.ajax({
    		    type: "GET",
    		    url: baseurl+"comet/active_topic.php",
    		    data: "topic_id="+topic_id+"&last_post="+last_post_id,
    			timeout: 30000,
    		    dataType: "json",
    		    success: function(json){
    		        
    		        if(typeof json.error == "undefined"){
    		            if(json.new_posts.length > 0){
            		        // Sometimes the server has a hiccup where it shows the user thier own post.
    		                $.each(json.new_posts, function(){
        		                if(typeof poll_data.last_post_id != "undefined"){
        		                    //console.log(this.post_id)
        		                    if(this.username != user.username){
                		                poll_data.last_post_id = this.topic_post_id;
                		                append_post(this);
                		                
                		                // Scroll to bottom of the page if you're looking at it.
                		                if(at_page_bottom === true) $.scrollTo($('.topic_posts li').last());
                		                
                		                // Show the topic title update
                		                if(window_focused == false){
                		                    topic.new_post_count += 1;
                		                    if(topic.new_post_count > 1){
                                                document.title = "New Posts ("+topic.new_post_count+") - "+topic.title;
                		                    } else {
                                                document.title = "New Post ("+topic.new_post_count+") - "+topic.title;        		                        
                		                    }
                		                }        		                        
        		                    }
        		                }
        		            })
        		            
        		            setTimeout(function(){
        		                start_polling();
        		            }, 1000);
    		            } else {
                            poll_request.abort();
                            last_post_id++; // Let's move on from this expired post, so we don't have to keep hanging on it
                            setTimeout(function(){
                                start_polling();
                            }, 4000);
    		            }
    		        } else {
    		            // Serious error. Don't poll any longer.
    		            alert("Oops: "+json.error);
    		            poll_request.abort();
    		        }
    		    },
    		    error: function(xhr, status, error){
                    setTimeout(function(){
                        start_polling();
                    }, 1000);
    		    }
    		});
    	}
        
        <?php if (strtotime($topic['last_post_time']) > time()-3600): ?>
            // setTimeout() is a hot fix for Safari to prevent the user from 
            // thinking the page loads endlessly from the loading icon
            setTimeout(function(){
                start_polling(topic.id, topic.last_post_id);                
            }, 500);
        <?php endif; ?>
        
        // Sometimes the user spends all their time in one topic, and may appear to be offline when they really aren't
        var topic_load_time = new Date();
        var start_timer = topic_load_time.getTime(); // tick tock, tick tock

        function time_on_page() { 
            var current_timer = new Date(); 
            var current_time = current_timer.getTime();  
            var time_difference = current_time - start_timer;
            return(parseInt(time_difference/1000)); 
        }
            
        // Sometimes the user spends all their time in one topic, and may appear to be offline when they really aren't
        setInterval(function(){
            $.getJSON(baseurl+"aether/update_online_status", { 
                time_on_page: time_on_page, 
                topic_id: topic.topic_id, 
                window_attention: window_focused, 
                new_posts_loaded: topic.new_post_count
            }, function(json){
                if(typeof json.notice != 'undefined'){
                    polling_time += 30000; // 30 seconds for each poll
                    // console.log(json.notice);
                }
            });
        }, polling_time);
    });
</script>
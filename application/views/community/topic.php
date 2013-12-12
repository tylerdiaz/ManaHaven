<div style="width:150px; float:left; padding-left:10px; height:300px; ">
    <?php $this->load->view('community/category_sidebar', array('forums' => $forums, 'unfocus' => TRUE)) ?>
</div>
<style type="text/css">
    .btn_edit {
        display:inline-block;
        width:16px;
        height:16px;
        background:transparent url(/images/edit.png)no-repeat 1px 1px;
        margin-left:5px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
    .btn_edit:hover {
        background:#000 url(/images/edit.png)no-repeat -17px 1px;
        margin-left:5px;
    }
    .edit_topic_title {
        margin-bottom:-2px;
    }
</style>
<div class="forum_content">
    <div style="padding-top:0">
    <div class="topic_header">
        <?php echo image('avatars/thumbnails/'.$topic['author_id'].'.gif', 'class="author_avatar"') ?>
        <h3 class="topic_title"><span><?php echo $topic['title'] ?></span>
            <?php if ($topic['author_id'] == $this->session->userdata('id')): ?>
                <a href="#" class="btn_edit edit_topic_title"></a>
            <? endif; ?>
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
        ul.auto-list{
        	display: none;
        	position: absolute;
        	top: 0px;
        	left: 10px;
        	border:1px solid #bbb;
        	background-color: white;
        	padding: 3px;
        	margin:0;
        	color:#555;
        	list-style:none;
        	-webkit-box-shadow: 1px 1px 0px 1px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 1px 1px 0px 1px rgba(0, 0, 0, 0.2);
            box-shadow: 1px 1px 0px 1px rgba(0, 0, 0, 0.2);
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            max-width:220px;
        }
        ul.auto-list > li:hover,
        ul.auto-list > li[data-selected=true]{ background-color: #E0FFBC; color:#111; }

        ul.auto-list > li{
            max-width:210px;
        	border-bottom:1px solid #ddd;
        	cursor:default;
        	padding:3px 2px;
        }

        mark { font-weight: bold; }
    </style>
    
    {topic_notice}
    
    <ul class="topic_posts">
        <?php foreach ($posts as $post): ?>
            <? $topic_posters[] = '@'.$post['author_username'].':'; ?>
            <li class="post <?php echo ($post['author_id'] == $this->system->userdata['id'] ? 'editable_post' : '') ?>" id="<?php echo $post['post_id'] ?>">
                <?php echo image('avatars/'.cycle('', 'flip/').''.$post['author_id'].'.gif?'.($topic['author_id'] == $this->session->userdata('id') ? $user['last_saved_avatar'] : ''), 'class="post_avatar" width="90" height="90"')?>
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
    
    <div style="clear:both; overflow:hidden;">
        <label for="my_post" style="font-size:13px; font-weight:bold; color:#555">Post a reply:</label><br>
        <textarea name="message" class="post_textfield" id="my_post" placeholder="What would you like to say..."></textarea>
        <button type="submit" id="submit_message" class="right">Submit Post</button>
    </div>
    <?php echo $this->pagination->create_links(); ?>
    </div>
    
</div>
<?php echo script('auto_suggest.js') ?>

<script type="text/javascript">

    Array.prototype.has = function(v){
        for (i=0;i<this.length;i++){
            if (this[i]==v) return i;
        }
        return false;
    }

    $(document).ready(function(){
        
        var topic_posters = <?php echo json_encode(array_values(array_unique($topic_posters)), JSON_NUMERIC_CHECK); ?>;
        
        $("#my_post").autocomplete({
        	wordCount:1,
			mode: "outter",
			wrap: 'b',
			on: {
				query: function(text, users_found){
					var suggested_users = [];
					for(var i=0; i < topic_posters.length; i++){
					    if( topic_posters[i] == "@"+username+":") continue;
					    if( suggested_users.length > 3) break;
 						if( topic_posters[i].toLowerCase().indexOf(text.toLowerCase()) == 0) suggested_users.push(topic_posters[i]);
					}

					users_found(suggested_users);		
				}
			}
		});

        // Let's define those variables!
        var poll_request, 
            poll_data = false,
            last_post = "",
            window_focused = false,
            polling_time = 60000, // 1 minute
            start_timer = new Date().getTime(), // tick tock, tick tock
            old_topic_title = '',
            topic = {
                id: <?php echo $topic['topic_id'] ?>,
                last_post_id: <?php echo ($topic['total_replies']+1) ?>,
                title: document.title,
                new_post_count: 0,
                author_id: <?php echo $topic['author_id'] ?>,
                author_username: "<?php echo $topic['author_username'] ?>"
            }
           
        // Reset the cached value! 
        local_db.set('topic_'+topic.id+'_last_post', topic.last_post_id, 1440)
        
        // Main window functions
        var window_obj = $(window);

        window_obj.scroll(function(){
            at_page_bottom = window_obj.scrollTop() > parseInt($('.structure').height()-window_obj.height()/0.7);
        });

        window_obj.focus(function(){
            document.title = topic.title;
            window_focused = true;
            topic.new_post_count = 0;
        });

        window_obj.blur(function(){
            window_focused = false;
        });
        
        
        function color_name(user_id){
            if(user_id == 4) return "color:#DA6506; background:#ffd;";
            if(user_id == 108 || user_id == 15 || user_id == 158) return "color:#CF359F; background:#FEE4F2;";
            return '';
        }
                
        function append_post(json, reward){
            $("#topic_post_count").increase(1);
            var new_post = $('.topic_posts li.post').first().clone();
            var total_posts = $('.topic_posts li.post').length; // this is to make sure we flip the avatars accordingly when they're loaded
            new_post.attr('id', json.post_id);
            if(json.user_id == user_id){
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
                var reward_html = '<img src="/images/coins.png" width="13" height="13" style="vertical-align:middle; margin-left:3px" /> ';
                reward_html += '<strong style="color:green">+'+reward+'</strong> ';
                if(json.super_bonus){
                    reward_html += '<span style="background-color:#C07905; color:white; font-size:12px; padding:1px 3px;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;">Super bonus!</span>';
                }
                new_post.find('span.right').append(reward_html);
            }
            
            $(".topic_posts").append(new_post);
            
            if(topic.author_id == user_id){
                local_db.set('topic_'+topic.id+'_last_post', topic.last_post_id, 1440);
            }
            
            if( ! topic_posters.has("@"+json.username+":")){
                topic_posters.push("@"+json.username+":");
            }
        }
        
        function submit_post(){
            if($("#submit_message").hasClass('running_post')){
                alert('Your other post is currently processing');
                return false;
            }
            
            // Kill the current polling request. The error handler will revive it in a second or two
            if(typeof poll_request != "undefined") poll_request.abort();
            
            topic.last_post_id++;
            
            var post = $("#my_post").val();
            var words = $("#submit_message").text();
            
            $("#submit_message").html('<img src="/images/ajax/posting_ajax.gif" alt=""> Posting...').animate({ opacity: 0.5 }).addClass('running_post');
            
            if(post.length > 2){
                if(post == last_post) {
                    $("#submit_message").text(words).animate({ opacity: 1 }).removeClass('running_post');
                    alert('We love your words, but please don\'t post the same thing twice!')
                } else {
                    $.ajax({
                        type: "POST",
                        url: "/community/create_post",
                        data: { message: encodeURIComponent($("#my_post").val()), topic_id: topic.id},
                        dataType: "json",
                        success: function(json){
                            last_post = post;
                            $("#submit_message").text(words).animate({ opacity: 1 }).removeClass('running_post');
                            $("#my_post").val('');
                            $("#my_post").attr('placeholder', 'What else would you like to say...');
                            append_post(json, json.reward);
                            $("#my_gold").increase(json.reward, true);

                            poll_data.last_post_id = json.topic_post_id;
                            
                            // If the 
                            setTimeout(function(){
                                if(typeof poll_request == "undefined") start_polling(topic.id, last_post);
                            }, 3500);

                            //console.log(poll_data.topic_id)
                        },
                        error: function(xhr, status, error){
                            $("#submit_message").text(words).animate({ opacity: 1 }).removeClass('running_post');
                            alert("AJAX error "+error+". Status: "+status);
                        }
                    });
                }
            } else {
                $("#submit_message").text(words).animate({ opacity: 1 }).removeClass('running_post');
                alert("That post is too short!");
            }
        }
        
        $("#my_post").keydown(function(e){
    		if(e.keyCode == 13 && e.shiftKey){
                submit_post();
                return false;
            }
    	});
        
        $("#submit_message").bind('submit, click', function(){
            submit_post();
            return false;
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
    		    url: "/comet/active_topic.php",
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
        		                    if(this.username != username){
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
        
        function time_on_page() { 
            var current_timer = new Date(); 
            var current_time = current_timer.getTime();  
            var time_difference = current_time - start_timer;
            return(parseInt(time_difference/1000)); 
        }
        
        // Sometimes the user spends all their time in one topic, and may appear to be offline when they really aren't
        setInterval(function(){
            $.getJSON("/aether/update_online_status", { 
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
        
        $(".edit_topic_title").live('click', function(){
            old_topic_title = $(this).parent();
            old_topic_title.hide();
            $(this).parent().parent().find('.author_avatar').after('<div id="editing_topic_title"><input type="text" name="new_topic_title" value="'+old_topic_title.find('span').text()+'" id="new_topic_title"> <input type="submit" id="save_new_title" value="Save changes"> or <a href="#" class="cancel_title_edit">Cancel edit</a><br></div>')
            return false;
        });
        
        $("#save_new_title").live('click', function(){
            $.ajax({
                type: "POST",
                url: "/community/change_topic_title",
                data: { topic_id: <?php echo $topic['topic_id'] ?>, new_topic_title: encodeURIComponent($("#new_topic_title").val()) },
                cache: false,
                async: true,
                dataType: "json",
                success: function(json){
                    $("#editing_topic_title").after('<h3><span>'+$("#new_topic_title").val()+'</span> <a href="#" class="btn_edit edit_topic_title"></a></h3>')
                    $("#editing_topic_title").remove();
                    
                    $("#quick_success").html('&#x2713; Your topic title has been edited!').fadeIn(500, function(){
                        setTimeout(function() {
                            $("#quick_success").fadeOut(1000);
                        }, 3000);
                    })
                },
                error: function(xhr, status, error){
                    alert("AJAX error "+error+". Status: "+status);
                }
            });

            return false;
        })
        
        $(".cancel_title_edit").live('click', function(){
            $("#editing_topic_title").after('<h3><span>'+old_topic_title.find('span').text()+'</span> <a href="#" class="btn_edit edit_topic_title"></a></h3>')
            $("#editing_topic_title").remove();

            return false;
        })
        
        // Edit your posts code
        $(".editable_post .message").live('click', function(){
            var textedit_height = parseInt($(this).height());
            var edit_post = $(this).closest('.editable_post').removeClass('editable_post').addClass('live_edit_post').css('overflow', 'hidden');
            
            $.ajax({
                type: "GET",
                url: "/community/get_post_data/"+edit_post.attr('id'),
                dataType: "json",
                success: function(json){
                    edit_post.css('overflow', 'auto')
                    edit_post.find('.message').html('<textarea class="post_textfield" style="height:'+(40+textedit_height)+'; max-height:350px;">'+json.text+'</textarea><br><div class="right" style="margin-top:10px"><a href="#" id="cancel_edit">Cancel editing</a> or <button type="submit" class="mini" id="save_post_changes">Save changes</button></div><br clear="all" />');
                    
                },
                error: function(xhr, status, error){
                    // alert("AJAX error "+error+". Status: "+status);
                }
            });

            return false;
        });
        
        $("#save_post_changes").live('click', function(){
            var edit_post = $(this).closest('.live_edit_post').removeClass('live_edit_post').addClass('editable_post');
            var new_content = edit_post.find('.post_textfield').val();
            
            if(new_content.split(' ').join('').length == 0){
                alert('Your post cannot be blank!');
            } else {
                $.ajax({
                    type: "POST",
                    url: "/community/edit_post_data/"+edit_post.attr('id'),
                    data: { text: encodeURIComponent(new_content) },
                    dataType: "json",
                    success: function(json){
                        edit_post.find('.message').html(decodeURIComponent(json.text));
                        $("#quick_success").html('&#x2713; Your post has been edited!').fadeIn(500, function(){
                            setTimeout(function() {
                                $("#quick_success").fadeOut(1000);
                            }, 3000);
                        })
                    },
                    error: function(xhr, status, error){
                        alert("AJAX error "+error+". Status: "+status);
                    }
                });
            }
            
            return false;            
        });
        
        $("#cancel_edit").live('click', function(){
            var edit_post = $(this).closest('.live_edit_post').removeClass('live_edit_post').addClass('editable_post');
            $.ajax({
                type: "GET",
                url: "/community/get_post_data/"+edit_post.attr('id')+'/1',
                dataType: "json",
                success: function(json){
                    edit_post.find('.message').html(decodeURIComponent(json.text));
                },
                error: function(xhr, status, error){
                    // alert("AJAX error "+error+". Status: "+status);
                }
            });
            
            return false;
        });
        
    });
</script>
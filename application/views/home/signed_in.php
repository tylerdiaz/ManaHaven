<div id="fb-root"></div>
<div class="grid_x activity_feed" id="asd4">
    <div class="notice" style="display:none" id="welcome_notice">
        <h4>Welcome to your new alpha-testing game!</h4>
        <p>We're so glad to have you here in our <em>secret</em> alpha version, you must have some pretty awesome contacts to have ended up here. ;)</p><br>
            <h4>Your characters are safe.</h4>
            We're still building the game at a really fast rate, so you might notice a broken thing or two. When you do, simply report it to the community and we'll get to fixing it. But don't worry, your characters won't be deleted or reseted even after we launch to the public, we promise.</p>   
            <a href="#" class="close_notice" id="show_intro_message"></a> 
    </div>
    <div class="notice">
        <h4>While you were gone...</h4>
        <ul class="list">
        </ul>
        <a href="#" class="close_notice"></a> 
    </div>
    <h3 style="padding:5px">Your Recent Activity</h3>
    <ul class="feed">
        <!-- Here go the friend requests -->
        <?php if (count($friend_requests) > 0): ?>
            <?php foreach ($friend_requests as $friend): ?>
                <li class="request">
                    <?php echo image('avatars/thumbnails/'.$friend['user_id'].'.gif', 'class="feed_icon"') ?>
                    <p class="request_description">
                        <?php echo anchor('profile/'.urlencode($friend['username']), $friend['username']) ?> wants to be your friend!
                    </p>
                    <?php echo anchor('friends/accept/'.$friend['id'], 'Accept friendship', 'class="button mini"') ?> &bull; 
                    <?php echo anchor('friends/decline/'.$friend['id'], 'Ignore') ?>
                </li>
            <?php endforeach ?>
        <?php endif ?>
        
        <!-- Here go the recent notifications -->
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <li>
                    <?php echo image('world/feed/multi.png', 'class="feed_icon"') ?>
                    <p>
                        <a href="#" class="strong">Tyler</a>, <a href="#" class="strong">Ayx</a> and <a href="#" class="strong">Ale</a> made it to wave 32!
                        <span class="timestamp">19 seconds ago</span>
                    </p>
                </li>
            <?php endforeach ?>
        <?php else: ?>
            <!-- No new feeds at this moment! -->
        <?php endif ?>
        <?php if ($this->system->is_staff()): ?>
            <li>
                <?php echo anchor('url/location', image('avatars/thumbnails/15.gif', 'class="feed_icon"')); ?>
                <p>
                    <a href="#" class="strong">Tyler</a> posted a comment in your profile.
                    <span class="timestamp">19 seconds ago &bull; <a href="#">View my profile</a></span>
                </p>
            </li>
            <li>
                <?php echo anchor('url/location', image('avatars/thumbnails/15.gif', 'class="feed_icon"')); ?>
                <p>
                    <a href="#" class="strong">Tyler</a> posted a comment in your profile.
                    <span class="timestamp">19 seconds ago &bull; <a href="#">View my profile</a></span>
                </p>
            </li>
            <li>
                <?php echo anchor('url/location', image('avatars/thumbnails/15.gif', 'class="feed_icon"')); ?>
                <p>
                    <a href="#" class="strong">Tyler</a> posted a comment in your profile.
                    <span class="timestamp">19 seconds ago &bull; <a href="#">View my profile</a></span>
                </p>
            </li>
            <li>
                <?php echo anchor('url/location', image('avatars/thumbnails/15.gif', 'class="feed_icon"')); ?>
                <p>
                    <a href="#" class="strong">Tyler</a> posted a comment in your profile.
                    <span class="timestamp">19 seconds ago &bull; <a href="#">View my profile</a></span>
                </p>
            </li>
            <li>
                <?php echo anchor('url/location', image('avatars/thumbnails/15.gif', 'class="feed_icon"')); ?>
                <p>
                    <a href="#" class="strong">Tyler</a> posted a comment in your profile.
                    <span class="timestamp">19 seconds ago &bull; <a href="#">View my profile</a></span>
                </p>
            </li>
            <li>
                <?php echo anchor('url/location', image('avatars/thumbnails/15.gif', 'class="feed_icon"')); ?>
                <p>
                    <a href="#" class="strong">Tyler</a> posted a comment in your profile.
                    <span class="timestamp">19 seconds ago &bull; <a href="#">View my profile</a></span>
                </p>
            </li>
        <?php endif ?>
    </ul>
    
    <br />
    
    <?php if (count($notifications)+count($friend_requests) > 7): ?>
        <a href="#" class="button mini">Load more notifications...</a>
    <?php endif ?>
    
    <br clear="all" />
    <br clear="all" />
</div>
<div class="grid_y widget_sidebar">
    <div class="widget clearfix" id="allies">
        <div class="widget_title clearfix">
            <h3 class="left">My Friends</h3>
            <?php echo anchor('friends', 'My friendlist', 'class="right"') ?>
        </div>
        <style type="text/css">
            .friend_thumbnail {
                margin:3px 0 3px -4px;
                border:1px solid white;
                display:block;
                float:left;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-transition: all 400ms ease;
                -moz-transition: all 400ms ease;
                -o-transition: all 400ms ease;
                transition: all 400ms ease;
            }
            .friend_thumbnail:hover {
                background:#E6F4FE;
                border-color:#A3CFEB;
                display:block;
                float:left;
                opacity:1;
            }
            .friend_offline { opacity:0.4; }
            .friend_online { background:#EDFFCC; }
        </style>
        
        <?php if (count($my_friends) > 0): ?>
            
            <?php foreach ($my_friends as $friend): ?>
                <?php echo anchor('profile/'.urlencode($friend['username']), image('avatars/thumbnails/'.$friend['user_id'].'.gif', 'class="feed_icon"'), 'title="'.$friend['username'].'" class="friend_thumbnail '.($friend['last_activity'] > time()-900 ? 'friend_online' : 'friend_offline').'"') ?>
            <?php endforeach ?>
        <?php elseif ($this->system->userdata['facebook_id'] == 0): ?>
            <?php $this->load->view('widgets/facebook_connect'); ?>
        <?php else: ?>
            <div class="empty_widget">
                <p>You haven't added any friends just yet!</p>
                <?php echo anchor('friends', 'Add a friend', 'class="button"') ?>
            </div>
        <?php endif ?>
    </div>
    <div class="widget clearfix" id="active_topics">
        <div class="widget_title clearfix">
            <h3 class="left">Active Topics</h3>
            <?php echo anchor('community', 'Visit the forums', 'class="right"') ?>
        </div>
        <ul class="link_list small_text">
            <?php foreach($latest_topics as $key => $topic): ?>
                <li id="feed_topic_<?php echo $key ?>">
                    <?php echo anchor('community/topic/'.$topic['topic_id'].'/'.(floor(($topic['total_replies']-1)/12)*12).'#'.$topic['last_post'], $topic['title']) ?><br />
                    <span class="topic_data"><?php echo image('mini_chat.gif')?> <?php echo $topic['total_replies'] ?> &mdash; <?php echo image('mini_watch.gif')?> <?php echo human_time($topic['last_post_time']) ?> by <?php echo $topic['last_post_by'] ?></span>
                </li>
            <? endforeach;?>
        </ul>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        
        var topic_posts_memory = {}, latest_topic_stream;
        
        function reformat_live_topic(topic_number, json_data){
            var feed_target = $("#feed_topic_"+topic_number);
            feed_target.find('a').html(json_data['topic_title']).attr('href', json_data['link_location'])
            feed_target.find('.topic_data').html('<img src="/images/mini_chat.gif" alt=""> '+json_data['total_replies']+' &mdash; <img src="/images/mini_watch.gif" alt=""> '+json_data['timestamp']+' by '+json_data['last_poster']);
            
            if(typeof topic_posts_memory[json_data['topic_title']] == "undefined" || topic_posts_memory[json_data['topic_title']] != json_data['total_replies']){
                // if timestamp is greater
                if(json_data['raw_timestamp'] > topic_posts_memory[json_data['topic_title']]){
                    feed_target.animate({ backgroundColor: "#ffb"}, 200);
                    setTimeout(function(){
                        feed_target.animate({ backgroundColor: "#fff"}, 500);
                    }, 300)
                }
                topic_posts_memory[json_data['topic_title']] = json_data['total_replies'];
            }
        }
        
        function start_loading_stream(){
            latest_topic_stream = setInterval(function(){
                $.ajax({
                    type: "GET",
                    url: "/home/get_latest_topics/4",
                    data: { json: 1 },
                    cache: false,
                    async: true,
                    dataType: "json",
                    timeout: 1500,
                    success: function(json_topics){
                        var topic_i = 0;
                        for (var i=0; i < json_topics.length; i++) {
                            setTimeout(function(){
                                reformat_live_topic(topic_i, json_topics[topic_i]);
                                topic_i++;       
                            }, i*400)
                        };
                    }
                });
            }, 10000);
        }
        
        start_loading_stream();
        
        $(window).focus(function(){
            start_loading_stream();
        });

        $(window).blur(function(){
            clearInterval(latest_topic_stream);
        });
        
        
    });
</script>
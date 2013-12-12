<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        function confirm_add_friend(username){
			popup.create({
				data: { username: username },
				title: "Quick Confirmation",
				content: "<b>"+username+"</b> will have to confirm your friend request so be sure they are able to recognize you! *You could also PM them to tell them who you are* <br><br>When a friendship is accepted the first time, both sides get a +5 <img src=\""+baseurl+"images/coins.png\" /> bonus!",
				cancel_button: { label: 'Close dialog' },
				confirm_button: { label: 'Send my friend request &rsaquo;', ajax: 'friends/send_request'}
			});				
		}
		
		$("#add_friend").live('click', function(){
			if($("#friend_name").val().length > 0){
				confirm_add_friend($("#friend_name").val());
			}
			return false;
		})
		
		$("#friend_name").keydown(function(e){
			if(e.keyCode == 13 && $("#friend_name").val().length > 0){
				confirm_add_friend($("#friend_name").val());
			}
		})
    });
</script>
<div id="fb-root"></div>
<div class="grid_x activity_feed">
    <div class="notice" style="display:block">
        <h4>Friendship is golden!</h4>
        <p>When you be-friend someone for the first time, you both get +5 gold!</p>
        <br>
        <h4>Invite friends &mdash; collect rewards</h4>
        <p>Invite your friends to obtain rare items and goodies, they can also give you gifts and help you get a lot further through waves of monsters. </p>
        <a href="#" class="close_notice"></a> 
    </div>
    <div style="padding:3px; overflow:hidden">
        <h3 class="left">My Friends</h3>
        <!-- <a href="#" class="right action_link">Edit my friendlist</a> -->
    </div>
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
    </ul>
    <?php if($friends != FALSE): ?>
        <ul class="friends_list">
            <?php foreach ($friends as $friend): ?>
                <li class="<?php echo ($friend['user_online'] ? 'online' : 'offline') ?>">
                    <a href="<?php echo site_url('profile/'.$friend['username']) ?>">
                        <?php echo image('avatars/'.$friend['user_id'].'.gif', 'class="avatar" width="90" height="90"') ?>
                        <strong><?php echo $friend['username'] ?></strong>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
<div class="grid_y widget_sidebar">
    
    <div class="widget clearfix" id="active_topics">
        <div class="widget_title clearfix">
            <h3 class="left">Add a Friend</h3>
        </div>
        <div style="padding:10px">
            <input type="text" name="username" class="text_input" style="width:180px; font-size:15px; float:left; margin:3px 5px 0 0;" placeholder="Username..." id="friend_name" />
            <button type="submit" class="mini" id="add_friend">Add</button>            
        </div>
    </div>
    <div style="margin:40px 0 50px; font-size:12px; color:#888; text-align:center;">
<!--         <?php echo anchor('friends/invite', 'Want more gold?', 'class="button huge"') ?><br>
        <div style="margin-top:10px">
            Invite your friends to get tons of prizes!
        </div>
 -->    </div>
    
    <style type="text/css" media="screen">
        .facebook_invite li {
            list-style:none;
            overflow:hidden;
            padding:6px 5px 12px;
            font-size:12px;
        }
        .facebook_invite li img {
            float:left;
            margin-right:8px;
        }
        .facebook_invite li .fb_name {
            display:block;
            margin-bottom:4px;
        }
    </style>
    <script type="text/javascript">
        var getNthWord = function(string, n){
            var words = string.split(" ");
            return words[n-1];
        }
        
        $(document).ready(function(){
            
            var mail_message = "\n\nI got invited into this new RPG game called ManaHaven. They're in private alpha, so you need a friend to invite you with a secret token to join. I only got a couple of tokens left so I suggest you join fast!\n\nhttp://manahaven.com/?token=<?php echo $this->system->userdata['beta_key']; ?>";
            
            $(".facebook_invite .button").live('click', function(){
                $(this).text('Inviting...').css({ opacity: 0.5, background: "#eee"})
            })
            
            $(".no_thanks").live('click', function(){
                var fb_id = $(this).parent().attr('fb_id');
                var facebook_li = $(this).parent();
                facebook_li.animate({opacity:0.2})
                $.ajax({
                    type: "GET",
                    url: "/friends/hide_fb_friend/"+fb_id,
                    dataType: "json",
                    success: function(json){
                        facebook_li.attr('fb_id', json.fb_id);
                        facebook_li.find('.fb_name').text(json.name);
                        facebook_li.find('img').attr('src', 'https://graph.facebook.com/'+json.fb_id+'/picture?type=square');
                        
                        facebook_li.find('a.button').text('Invite '+getNthWord(json.name, 1)).attr('href', "http://www.facebook.com/messages/"+json.fb_id+"?msg_prefill="+escape("Hi "+getNthWord(json.name, 1)+"!"+mail_message))
                        
                        
                        setTimeout(function(){
                            facebook_li.animate({opacity:1}, 200)
                        }, 600)
                    }
                });
            })
        });
    </script>
    <div class="widget clearfix" id="facebook_friends">
        <div class="widget_title clearfix">
            <h3 class="left">Facebook friends</h3>
            <!-- <a href="#" class="right">x Disconnect</a> -->
        </div>
        <?php if ($this->system->userdata['facebook_id'] > 0): ?>
            <ul class="facebook_invite">
                <?php foreach ($facebook_friends as $friend): ?>
                    <li fb_id="<?php echo $friend['fb_id'] ?>">
                        <img src="https://graph.facebook.com/<?php echo $friend['fb_id'] ?>/picture?type=square" width="45" height="45" />
                        <span class="fb_name"><?php echo $friend['name'] ?></span>
                        <a target="_blank" href="<?php echo "http://www.facebook.com/messages/".$friend['fb_id']."?msg_prefill=".urlencode("Hi ".first_word($friend['name'])."! 

I got invited into this new RPG game called ManaHaven. They're in private alpha, so you need a friend to invite you with a secret token to join. I only got a couple of tokens left so I suggest you join fast!

http://manahaven.com/?token=".$this->system->userdata['beta_key']); ?>" class="button mini">Invite <?php echo first_word($friend['name']) ?></a> &bull; <a href="#" class="no_thanks">No thanks</a>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php else: ?>
            <?php $this->load->view('widgets/facebook_connect'); ?>
        <?php endif ?>
    </div>
</div>
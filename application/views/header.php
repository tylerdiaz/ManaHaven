<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>ManaHaven - <?php echo (isset($page_title) ? $page_title : 'TITLE ERROR') ?></title>
        <?php echo stylesheet('main.css') ?>
        <?php echo script('jquery.js') ?>
        <script type="text/javascript"> var baseurl = "<?php echo base_url(); ?>", user_id = <?php echo $user['id'] ?>, username = "<?php echo $user['username'] ?>"; </script>
        <?php echo script('main.js') ?>
        <link rel="icon" type="image/png" href="/favicon.ico" />
    </head>
    <body id="<?php echo (isset($page_body) ? $page_body : 'empty') ?>">
        <div class="structure">
            <div class="header">
                <div id="user_bar">
                    <span class="left" style="min-width:90px; background:rgba(0, 0, 0, 0.4); margin:-5px 0 -2px; line-height:28px;  margin-left:-10px; height:28px; -moz-border-radius-topleft: 0px;
                    -moz-border-radius-topright: 0px;
                    -moz-border-radius-bottomright: 0px;
                    -moz-border-radius-bottomleft: 5px;
                    -webkit-border-radius: 0px 0px 0px 5px;
                    border-radius: 0px 0px 0px 5px; padding-left:11px; border-right:1px solid rgba(0, 0, 0, 0.3); padding-right:10px;">
                        Hi, <?php echo anchor('profile', $this->session->userdata('username'))?>!
                    </span>
                    <style type="text/css" media="screen">
                        .user_bar_link {
                            background:rgba(0, 0, 0, 0.2);
                            margin:-5px 0 -2px;
                            line-height:28px;
                            height:28px;
                            padding:0 11px;
                            font-size:12px;
                            border-right:1px solid rgba(0, 0, 0, 0.3);
                        }
                    </style>
                    <a href="/settings" class="left user_bar_link">Settings</a>
                    <a href="/auth/signout?token=<?php echo $scrty_token ?>" class="left user_bar_link" id="logout">Logout</a>
                    <!-- <a href="/auth/run_test_account/" class="right" id="run_test">Generate test account</a> -->
                </div>
                <h1 id="logo"><?php echo anchor('home', 'Manahaven') ?></h1>

				<div style="float:left; background:rgba(255, 255, 255, 0.5); color:#625403;  width:196px; height:55px; margin:10px 40px; opacity:0.8; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding:7px 10px; border:1px solid #dd0;">
                    <h4>Come back soon!</h4>
                    <p style="font-size:12px;">Updates will occur in just a couple of minutes!</p>
                </div>
                
                <div id="avatar_box" class="right">
                    <?php echo image('avatars/'.$this->session->userdata('id').'.gif?'.$user['last_saved_avatar'], 'class="left" width="90" height="90"')?>
                    <div id="avatar_pane_title">My Character</div>
                    <div class="avatar_pane">
                        <span class="left"><?php echo image('coins.png', 'id="gold_coins"')?> <strong id="my_gold" data_amount="<?php echo $user['gold'] ?>"><?php echo number_format($user['gold']); ?></strong></span>
                        <?php if($this->system->is_staff()): ?>
                            <a href="#" class="right"><?php echo image('golden_feather.png', 'width="14" height="16"') ?>  <strong id="my_feathers" data_amount="0">34</strong><!-- Donation currency could be here --></a>
                        <?php endif ?>
                    </div>
                    <div style="overflow:hidden; width:160px;">
                        <div id="level_bubble">
                            <?php echo $user['level']?>
                        </div>
                        <div class="experience_bar">
                            <div style="width:<?php echo percent($user['exp'], $user['next_level_exp'])?>%"></div>
                            <span class="progress_text"><span id="current_exp" data_amount="<?php echo $user['exp']?>"><?php echo $user['exp']?></span>/<span id="next_level_exp" data_amount="<?php echo $user['next_level_exp']?>"><?php echo $user['next_level_exp']?></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <ul id="navigation" class="clearfix">
                <li id="nav_home"><?php echo anchor('home', 'Home')?><?php echo ($user['notifications'] > 0 ? '<span class="notification_bubble">'.$user['notifications'].'</span>' : ''); ?></li>
                <li id="nav_community"><?php echo anchor('community', 'Community')?></li>
                <li id="nav_world"><?php echo anchor('world', 'World')?></li>
                <li id="nav_avatar"><?php echo anchor('avatar', 'My Avatar')?></li>
            </ul>
            <div style="clear:both"></div>
            <div id="body" class="clearfix">
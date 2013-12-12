<script type="text/javascript">
    $(document).ready(function(){
        $("#tooltip_checkpoints div img").tooltip({ effect: 'slide'});
    });
</script>


<div class="grid_x" style="height:150px; background-color:#E4FFD7;
background-image: -moz-radial-gradient(50% 45%, circle cover, #E4FFD7, #D4F9B4 100%);
background-image: -webkit-radial-gradient(50% 45%, circle cover, #E4FFD7, #D4F9B4 100%);
background-image: -o-radial-gradient(50% 45%, circle cover, #E4FFD7, #D4F9B4 100%);
background-image: -ms-radial-gradient(50% 45%, circle cover, #E4FFD7, #D4F9B4 100%);
background-image: radial-gradient(50% 45%, circle cover, #E4FFD7, #D4F9B4 100%)
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
-webkit-box-shadow: inset 0px 0px 0px 4px rgba(0, 0, 0, 0.1);
-moz-box-shadow: inset 0px 0px 0px 4px rgba(0, 0, 0, 0.1);
box-shadow: inset 0px 0px 0px 4px rgba(0, 0, 0, 0.1);
">
    <div style="padding:45px 15px; text-align:center;">
        <label for="invite_link" style="font-weight:bold; color:#95A974">Share this URL to get prizes:</label><br>
        <style type="text/css">
            #invite_link {
                font-size:16px; 
                width:350px; 
                border:1px solid rgba(0, 0, 0, 0.4); 
                padding:3px 5px;
            }
            #invite_link:focus {
                border:1px solid black;
                -webkit-box-shadow: 0px 0px 0px 3px #C8FF00;
                -moz-box-shadow: 0px 0px 0px 3px #C8FF00;
                box-shadow: 0px 0px 0px 3px #C8FF00;
                outline:none;
            }
        </style>
        <input type="text" value="http://manahaven.com/?invite=<?php echo $this->system->userdata['beta_key'] ?>" id="invite_link">        
    </div>
</div>
<div class="grid_y" style="height:150px;">
    <h3 style="color:#444; font-family:Georgia; font-weight:normal; padding:8px 5px; font-size:17px;">Your friends could make you rich</h3>
    <p style="padding:2px 5px; color:#777">For every friend you invite, you get +25 gold pieces! Plus, your friend gets to alpha-test an awesome game in the making. :D  </p>
</div>
<br clear="all" />
<br>
<div style="background:url(/images/progress_bg.jpg) -<?php echo ($this->system->userdata['invites_left'])*76 ?>px 0px; height:40px; margin:0 6px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; border:1px solid #697C3C; -webkit-box-shadow: 0px 0px 0px 3px #ddd;
-moz-box-shadow: 0px 0px 0px 3px #ddd;
box-shadow: 0px 0px 0px 3px #ddd; text-align:center; font-family:Helvetica; font-weight:bold; line-height:42px; font-size:22px; color:#555; text-shadow:0 0 4px #fff; margin-top:10px;">
<?php echo 10-$this->system->userdata['invites_left'] ?>/10
</div>
<div style="position:relative; margin:0 0 15px;" id="tooltip_checkpoints">
    <div style="position:absolute; left:63px; border-right:2px solid #ccc; padding:5px 4px 0 0;">
        <?php if ((10-$this->system->userdata['invites_left']) >= 1): ?>
            <?php echo image('trophy.png', 'title="+50 EXP"') ?>            
        <?php else: ?>
            <?php echo image('trophy.png', 'title="+50 EXP" style="opacity:0.5"') ?>            
        <?php endif ?>
    </div>
    <div style="position:absolute; left:214px; border-right:2px solid #ccc; padding:5px 4px 0 0;">
        <?php if ((10-$this->system->userdata['invites_left']) >= 3): ?>
            <?php echo image('trophy.png', 'title="+100 EXP +50 Gold"') ?>
        <?php else: ?>
            <?php echo image('trophy.png', 'title="+100 EXP +50 Gold" style="opacity:0.5"') ?>
        <?php endif ?>
    </div>
    <div style="position:absolute; left:368px; border-right:2px solid #ccc; padding:5px 4px 0 0;">
        <?php if ((10-$this->system->userdata['invites_left']) >= 3): ?>
            <?php echo image('trophy.png', 'title="+250 EXP"') ?>
        <?php else: ?>
            <?php echo image('trophy.png', 'title="+250 EXP" style="opacity:0.5"') ?>
        <?php endif ?>
    </div>
    <div style="position:absolute; left:519px; border-right:2px solid #ccc; padding:5px 4px 0 0;">
        <?php if ((10-$this->system->userdata['invites_left']) >= 3): ?>
            <?php echo image('trophy.png', 'title="+300 EXP +150 Gold"') ?>
        <?php else: ?>
            <?php echo image('trophy.png', 'title="+300 EXP +150 Gold" style="opacity:0.5"') ?>
        <?php endif ?>
    </div>
    <div style="position:absolute; left:745px; border-right:2px solid #ccc; padding:5px 4px 0 0;">
        <?php if ((10-$this->system->userdata['invites_left']) >= 3): ?>
            <?php echo image('trophy.png', 'title="+500 EXP"') ?>
        <?php else: ?>
            <?php echo image('trophy.png', 'title="+500 EXP" style="opacity:0.5"') ?>
        <?php endif ?>
    </div>
</div>
<br clear="all" />
<style type="text/css" media="screen">
.tooltip {
	display:none;
	background:rgba(0, 0, 0, 0.7);
	-webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
	font-size:12px;
	min-width:60px;
	padding:5px 20px;
	color:#fff;	
	text-align:center;
	border:1px solid #aaa;
	-webkit-box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.4);
    -moz-box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.4);
    box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.4);
}
    .leaderboards {
        list-style:none;
    }
    .leaderboards li {
        width:83px;
        float:left;
        background:#eee;
        border-right:1px solid #ddd;
        padding:5px;
        text-align:center;
        overflow:hidden;
        opacity:0.8;
    }
    .leaderboards li:first-child {
        width:83px;
        float:left;
        background: #fefcea; /* Old browsers */
        background: -moz-linear-gradient(top, #fefcea 0%, #f1da36 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fefcea), color-stop(100%,#f1da36)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #fefcea 0%,#f1da36 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #fefcea 0%,#f1da36 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #fefcea 0%,#f1da36 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fefcea', endColorstr='#f1da36',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #fefcea 0%,#f1da36 100%); /* W3C */
        border:2px solid #D9D17E;
        padding:10px;
        margin-top:-5px;
        text-align:center;
        overflow:hidden;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        opacity:1;
    }
    .leaderboards li:nth-child(2) {
        border-left:2px solid #ddd;
    }
    .username {
        font-size:12px;
        font-weight:bold;
        border-bottom:1px solid rgba(0, 0, 0, 0.2);
        margin-bottom:5px;
        color:#215A84;
        display:block;
    }
    .username:hover {
        color:#1D5FB3;
    }
    .score {
        text-shadow:-1px -1px 0 #fff;
        color:green;
    }
    
</style>
<div style="background:#D9F7FE; color:#084155; text-align:center; padding:10px;">
    Inviting a person that uses the same computer as you do will not grant you an invite point.
</div>
<div class="clearfix" style="padding:5px">
    <h3 class="left">This month's top inviters:</h3>
    <span class="right" style="background:#EEF7CF; color:#677529; font-size:12px; padding:2px 9px; -webkit-border-radius: 16px;
    -moz-border-radius: 16px;
    border-radius: 16px;">You are in placed #45</span>
</div>
<ul class="leaderboards">
    <?php foreach ($leaderboards as $user): ?>
        <li>
            <?php echo anchor('profile/'.urlencode($user['username']), $user['username'], 'class="username"'); ?>
            <?php echo image('avatars/thumbnails/'.$user['id'].'.gif') ?><br>
            <strong class="score"><?php echo (10-$user['invites_left']) ?></strong>
        </li>
    <?php endforeach ?>
</ul>
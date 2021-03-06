<h4>We meet again! One last step: Your new password.</h4>
<style type="text/css">
    .form { list-style:none; margin-top:10px; }
    .form li { margin:3px 0; }
    .form li label { font-size:13px; color:#555; font-weight:bold; float:left; width:120px; margin-right:10px; text-align:right; }
    .form li .text_input { border:1px solid #bbb; padding:2px 4px; font-size:13px; width:200px; }
    .form li .text_input:focus { 
        border:1px solid #86A751; 
        -webkit-box-shadow: 0px 0px 0px 2px #daebb5;
        -moz-box-shadow: 0px 0px 0px 2px #daebb5;
        box-shadow: 0px 0px 0px 2px #daebb5;
        outline:none;
     }
</style>
<p style="color:red"><?php echo $error; ?></p>
<p style="background:#C8ECA4; color:#3B6102; line-height:2; padding:0 10px;"><?php echo $success; ?></p>
<form action="/auth/password_key/<?php echo $key.'/?username='.$this->input->get('username') ?>" method="POST">
    
    <ul class="form">
        <li>
            <label for="f_pw">New Password:</label>
            <input type="password" name="password" class="text_input" id="f_pw">
        </li>
        <li>
            <label for="f_pwagain">&uarr; Confirm it:</label>
            <input type="password" name="password_confirm" class="text_input" value="" id="f_pwagain">
        </li>
    </ul>
    <br>
    <input type="submit" value="Set your new password" class="button" />
    <div style="background:#eee; padding:10px; color:#555; margin-top:40px; border-top:2px solid #ccc">
        <h5>Still having issues?</h5>
        <p>Sometimes the email doesn't arrive. Or it's simply just giving troubles. If so, go ahead and email Pixeltweak directly at tyler@manahaven.com and he'll get it sorted out for you. :D</p>
    </div>
</form>
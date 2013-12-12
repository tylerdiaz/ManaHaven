<div id="stream_settings">
    <h2 style="padding:10px 25px 0;">Your Settings</h2>
    <style type="text/css" media="screen">
        .side_list {
            list-style:none;
            margin:10px;
        }
        .side_list li {
            overflow:hidden;
            margin:5px 5px 10px;
            color:#333;
        }
        .side_list h5 {
            font-weight:normal;
            font-size:12px;
        }
        .label {
            float:left;
            width:200px;
            background:#eee;
            border:1px solid #ccc;
            border-right:3px solid #bbb;
            padding:15px 15px;
            margin-top:5px;
        }
        .label_content {
            overflow:hidden;
            padding:20px 15px;
        }
        ul.form li label {
            width:115px;
        }
        #new_again_pw {
            width:125px;
        }
        .error {
            color:red;
        }
        .success {
            color:green;
        }
    </style>
    
    {error}
    {success}
    <form action="/settings" method="post" accept-charset="utf-8">
    <ul class="side_list">
        <li>
            <div class="label">
                <h3>Email:</h3>
                <h5>Where could we get in touch?</h5>                
            </div>
            <div class="label_content">
                <ul class="form">
                    <li>
                        <label for="cur_email">Your e-mail:</label>
                        <input type="email" name="email" class="text_input" value="<?php echo $this->system->userdata['email'] ?>" id="cur_email">
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <div class="label">
                <h3>Password:</h3>
                <h5>A secure password is a happy password! Always remember to never share your password with anyone, we would never ask for it!</h5>                
            </div>
            <div class="label_content">
                <ul class="form">
                    <li>
                        <label for="cur_pw">Current Password:</label>
                        <input type="password" name="current_password" class="text_input" id="cur_pw">
                    </li>
                    <li>
                        <label for="new_pw">New Password:</label>
                        <input type="password" name="new_password" class="text_input" id="new_pw">
                    </li>
                    <li>
                        <label for="new_again_pw" style="width:190px; ">&uarr; Re-type:</label>
                        <input type="password" name="new_again_password" class="text_input" id="new_again_pw">
                    </li>
                </ul>
            </div>
        </li>
    </ul>
    <button class="right" type="submit">Save Changes</button>
    </form>
</div>
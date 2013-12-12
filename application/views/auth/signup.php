<div class="header_box">
    <h3>Sign up for my account</h3>
</div>
<div style="clear:both; overflow:hidden">
{errors}
</div>
<form action="<?=site_url('auth/signup')?>" method="post" id="signup_box">
<ul class="form">
    <li>
        <label for="signup_username">Username:</label>
        <input type="text" name="username" id="signup_username" class="text_input" />
        <div class="additional_tip tip_bubble">
            Only spaces, letters and numbers are allowed in your username.
        </div>
    </li>
    <li>
        <label for="signup_password">Password:</label>
        <input type="password" name="password" id="signup_password" class="text_input" />
    </li>
    <li>
        <label for="signup_email">Email:</label>
        <input type="text" name="email" id="signup_email" class="text_input" />
    </li>
    <li>
        <label for="beta_code">Beta Code:</label>
        <input type="text" name="betacode" id="beta_code" class="text_input" />
    </li>
    <li class="right">
        <button type="submit" id="some_name" class="regular_button">Create my Account</button>
    </li>
    <li class="small_text">
        By signing up you agree to our Terms of Service and our Private Policy.
    </li>
</ul>
</form>

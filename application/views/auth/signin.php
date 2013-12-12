{errors}

<h4>Sign in</h4>

<form action="<?php echo site_url('auth/signin') ?>" method="post" accept-charset="utf-8">
    <label for="username">Username:</label><input type="text" name="username" id="username" /><br>
    <label for="password">Password:</label><input type="password" name="password" id="password" /><br>
    <input type="submit" value="Sign in" />
</form>

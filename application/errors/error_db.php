<html>
<head>
<title>Database Error</title>
<style type="text/css">

body {
background-color:	#fff;
margin:				40px;
font-family:		Lucida Grande, Verdana, Sans-serif;
font-size:			12px;
color:				#000;
}

#content  {
border:				#999 1px solid;
background-color:	#fff;
padding:			20px 20px 12px 20px;
}

h1 {
font-weight:		normal;
font-size:			14px;
color:				#990000;
margin:				0 0 4px 0;
}
</style>
</head>
<body>
	<div id="content">
		<h1>BOOM! The database has exploded!</h1>
		<p>Pixeltweak has been notified, and will rush to get this fixed really soon. Hang tight!</p>
	</div>
</body>
</html>
<?php mail('pixelgrid@gmail.com', $heading, $heading."\n".$message) ?>
<?php

mysql_query("INSERT INTO `action_logs` (`id`, `username`, `user_id`, `userdata`, `ip`, `time`, `type`, `browser`, `action`, `data`) VALUES 
(NULL, 0, 0, 0, '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."', ".time().", 'data', '', 'database_error', '".mysql_real_escape_string($message)."');") or die('The database seems to be down, this might take a bit longer to restore. ):');

?>
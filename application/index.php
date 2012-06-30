<?php require_once('conf.php'); ?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title><? echo $conf['name']; ?></title>
		
		<link rel="stylesheet" href="<? echo $conf['css1']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css2']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css3']; ?>" type="text/css" media="all" />
		<link href="<? echo $conf['favicon']; ?>" rel="icon" type="image/x-icon" media="all" />
		
		<script type="text/javascript" src="<? echo $conf['jQ']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js1']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js2']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js3']; ?>"></script>
	</head>

	<body>
		<div id="shell">	
			
			<div id="header">
				<h1><? echo $conf['name']; ?></h1>
				<div class="right">
					<p>Welcome, <strong><span id="greeting"></span></strong></p>
					<p class="small-nav"><a href="<? echo $conf['help']; ?>">Help</a> / <a href="<? echo $conf['contact']; ?>">Contact</a> / <a href="<? echo $conf['zen']; ?>">Zen</a></p>
				</div>
			</div>
			
			<div id="navigation"></div>
			<div id="content"></div>
			
		</div>

		<div id="footer">
			<p><? echo $conf['footer']; ?> Design by <a href="http://chocotemplates.com">ChocoTemplates.com</a>.</p>
		</div>
		
		<div id="waste"></div>
		
	</body>
</html>
<?php require_once('conf.php'); ?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta name="Description" content="<? echo $conf['description']; ?>"/>
		
		<title><? echo $conf['name']; ?></title>
		
		<link rel="icon" href="<? echo $conf['css_favicon']; ?>" type="image/x-icon" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css_main']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css_tables']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css_editor']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css_tooltip']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css_modal']; ?>" type="text/css" media="all" />
		<link rel="stylesheet" href="<? echo $conf['css_ui']; ?>" type="text/css" media="all" />
		
		<script type="text/javascript" src="<? echo $conf['js_google']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_jQuery']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_scripts']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_editor']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_tables']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_modal']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_tooltip']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_ui']; ?>"></script>
		<script type="text/javascript" src="<? echo $conf['js_time']; ?>"></script>
		
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<? echo $conf['tracking']; ?>']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	</head>

	<body onLoad="init_all()">
		<div id="shell">	
			
			<div id="header">
				<h1><? echo $conf['name']; ?></h1>
				<div class="right">
					<p>Welcome, <strong><span id="greeting"></span></strong></p>
					<p class="small-nav"><a href="<? echo $conf['help']; ?>">Help</a> / <a href="<? echo $conf['contact']; ?>">Contact</a> / <a href="<? echo $conf['play']; ?>">Play</a></p>
				</div>
			</div>
			
			<div id="navigation"></div>
			<div id="content"></div>
			
		</div>

		<div id="footer">
			<p><? echo $conf['footer']; ?> Layout by <a href="http://www.chocotemplates.com">ChocoTemplates.com</a>.</p>
		</div>
		
		<div id="modal"></div>
	</body>
</html>
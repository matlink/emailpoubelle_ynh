<!DOCTYPE html>
<html lang="fr">
<head>
<title>emailPoubelle - Template-exemple</title>
<meta charset="UTF-8">
<meta name="description" content="">
<meta name="robots" content="index,follow,noarchive">
<link rel="stylesheet" href="template-exemple/style.css">
<!--[if IE]><script src="template-exemple/html5-ie.js"></script><![endif]--> 

<meta name = "viewport" content = "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<link rel="apple-touch-icon" href="/m/images/iphone.png" />
<meta name="format-detection" content="telephone=no" />
<!--  A free Google web font embed because android does not have the browser safe fonts -->
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Allerta">
<!-- Style sheet for NOT smart devices like older web phones  -->
<link href="/m/css/handheld.css" rel="stylesheet" type="text/css" media="handheld" />
<!-- Style sheet for smartphones and tablet devices -->
<link rel="stylesheet" href="/m/css/ios.css" media="screen" />
<!-- Style sheet for lousy incompatible frustrating IE mobile -->
<!--[if IEMobile]>
<link rel="stylesheet" href="/m/css/iemobile.css" media="screen" />
<![endif]-->
<script type="text/javascript">
window.scrollTo(0,1);
</script>







</head>
<body>
<!--[if lte IE 6]> 
<div class="alert-ie6" style="padding: 1em; background: #900; font-size: 1.1em; color: #fff;">
      	<p><strong>Attention ! </strong> Votre navigateur (Internet Explorer 6 ou 7) présente de sérieuses lacunes en terme de sécurité et de performances, dues à son obsolescence (il date de 2001).<br />En conséquence, ce site sera consultable mais de manière moins optimale qu'avec un navigateur récent (<a href="http://www.browserforthebetter.com/download.html" style="color: #fff;">Internet Explorer 8</a>, <a href="http://www.mozilla-europe.org/fr/firefox/" style="color: #fff;">Firefox 3</a>, <a href="http://www.google.com/chrome?hl=fr" style="color: #fff;">Chrome</a>, <a href="http://www.apple.com/fr/safari/download/" style="color: #fff;">Safari</a>,...)</p>
      </div>
<![endif]-->  
	<div id="a" style="float:left">
		<div id="b">
			<article style="float:left">
			<?php
				$lang = "en_GB";
				$workaround_lang = "C.UTF-8";
				putenv("LC_ALL=$workaround_lang");
				setlocale(LC_ALL, $workaround_lang);
				bindtextdomain("messages", "/var/www/emailpoubelle/locale/".$lang);
				bind_textdomain_codeset('messages', 'UTF-8');
				textdomain("messages");
			?>
				<h1><?php echo _("Emails poubelle libre") ?></h1>
				<p><?php echo _("Générer des emails poubelle sans contrainte de durée de vie")?>. </p>
				<?php 
				// Intégration dans votre site :
				if (file_exists('../conf.php')) {
					include('../conf.php');
				} else {
					include('../conf-dist.php');
				}
				include('../emailPoubelle.php'); 
				?>
		</div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Framework Domicilios</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Fabian Altahona">
	<!--styles -->
	<?php
	$assets = Asset::container('container')->bundle('lara_admin');
	?>
	<?php
	$assets->add('boostrap', 'css/bootstrap.css');
	$assets->add('boosresponsice', 'css/bootstrap-responsive.css');
	$assets->add('global', 'css/global.css');
	$assets->add('application', 'css/application.css');
	echo $assets->styles();
	?>
	<!-- Soporte para IE6-8 de elementos HTML5 -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!-- favicon and touch icons -->
	<link rel="shortcut icon" href="public/img/ico/favicon.ico">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="public/img/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="public/img/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="public/img/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="public/img/ico/apple-touch-icon-57-precomposed.png">
</head>
<body class="logged_out new">
	<div id="wrapper">
		{{ $content }}
		<div id="footer">
			<p>Powered by <a href="http://www.drakarstudio.net">Drakar Studio</a> 1.0</p>
		</div>
	</div>
</body>
</html>

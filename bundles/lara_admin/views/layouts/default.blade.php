<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Framework Domicilios</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">

	<!--styles -->
	<?php
	$assets = Asset::container('container')->bundle('lara_admin');
	?>

	{{ View::make('lara_admin::share.header', compact("assets")); }}
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
<body>
	<div class="row-fluid">
		<div id="wrapper">
			<div id="header"><h1 id="site_title">{{ Config::get('laraAdmin.title') }}</h1>
				<ul class="header-item" id="tabs">
					@foreach (Config::get('laraAdmin.models') as $key => $model)
						<?php
						$modelNameToolbar = $model;
						$modeTitleToolBar = $model;

						if ( ! is_numeric($key)) {
							$modelNameToolbar = $key;
						}

						if (isset($model) && is_array($model) && array_key_exists("title", $model)) {
							$modeTitleToolBar = $model["title"];
						}
						?>
						<li id="{{ $modelNameToolbar }}" class="@if ($title == $modelNameToolbar) current @endif">
							{{ HTML::link('lara_admin/models/'.$modelNameToolbar, $modeTitleToolBar) }}
						</li>
					@endforeach
				</ul>
				<p class="header-item" id="utility_nav">
					{{ HTML::link("lara_admin/logout", "Logout") }}
				</p>
			</div>
			<div id="title_bar">
				<div id="titlebar_left">
					<h2 id="page_title">
						@if (isset($title))
							{{ $title }}
						@endif
					</h2>
				</div>
				<div id="titlebar_right">
					<div class="action_items">
						@if( isset($title) )
							<span class="action_item">
								{{ HTML::link("lara_admin/models/$title/new", "New $title", array("class" => "btn")) }}
							</span>
						@endif
					</div>
				</div>
			</div>
			{{ $content }}
			<div id="footer">
				<p>Powered by <a href="http://www.drakarstudio.net">Drakar Studio</a> 1.0</p>
			</div>
		</div>
	</div>

	<div class="facebox hide" id="new_modal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2 id="page_title">Nuevo Usuario</h2>
		</div>
	</div>
	<div class="modal-footer"></div>
	<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	{{ View::make('lara_admin::share.script', compact("assets") ) }}
</body>
</html>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Bootstrap, from Twitter</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Le styles -->
		<link href="/css/bootstrap.css" rel="stylesheet">
		<style>
			body {
				padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
			}
		</style>
		<link href="/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="http://twitter.github.com/bootstrap/assets/css/docs.css" rel="stylesheet">

		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Le fav and touch icons -->
		<link rel="shortcut icon" href="/ico/favicon.ico">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="/ico/apple-touch-icon-57-precomposed.png">
	</head>

	<body>

	 	<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="#">Project name</a>
					<div class="nav-collapse collapse">
						<p class="navbar-text pull-right">
							Logged in as <a href="#" class="navbar-link">Username</a>
						</p>
						<ul class="nav">
							<li class="active"><a href="#">Home</a></li>
							<li><a href="#about">About</a></li>
							<li><a href="#contact">Contact</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>


		<div class="container">
			<div class="row">

				<section>
					<div class="span3 bs-docs-sidebar">
						<ul class="nav nav-list bs-docs-sidenav">
							<? foreach ($allClasses as $item) : ?>
							<li<?=($item == $className) ?' class="active"':''?>><a href="/admin/<?=$item?>/index"><i class="icon-chevron-right"></i> <?=ucfirst($item)?></a></li>
							<? endforeach ?>
					</div>
				</section>
			
				<div class="span8">

						<? if (isset($status)): ?>
						<div class="alert alert-<?=$status['type']?>">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?= $status['message'] ?>
						</div>
						<? endif ?>

						<?= $view ?>

				</span> <!-- /container -->
			</div>
		</div>

		<!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> <!-- or use local jquery -->
		<script src="/js/datatables.js"></script>
		<script src="http://jquery-datatables-row-reordering.googlecode.com/svn/trunk/media/js/jquery-ui.js"></script>
		<script src="/js/bootstrap.js"></script>
		<script src="/js/bootbox.js"></script>


		<script>
			/* Default class modification */
			$.extend( $.fn.dataTableExt.oStdClasses, {
				"sSortAsc": "header headerSortDown",
				"sSortDesc": "header headerSortUp",
				"sSortable": "header"
			} );


			/* Table initialisation */
			$(document).ready(function() {

				$("a.delete").click(function(e) {
					e.preventDefault();
					var url = ($(this).attr('href'));
					bootbox.dialog("Weet u zeker dat u dit item wilt verwijderen?", [{
	                    "label" : "Nee toch maar niet"
	                }, {
	                    "label" : "Ja dat weet ik zeker",
	                    "callback": function() {
	                    	window.location.href = url;
	                    }
	                }]);

				});

				$('#datalist').dataTable( {
					'sPaginationType': 'bootstrap',
					"sDom": "<'row'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>",
					 "aoColumnDefs": [ { 'bSortable': false, 'aTargets': $('th#actions').index() } ]
					}).rowReordering( { 
						sURL:"",
						sRequestType: "POST" ,
						fnAlert: function(text){
							alert( text) ;
						}	
					}
				);
			});


		</script>

	</body>
</html>

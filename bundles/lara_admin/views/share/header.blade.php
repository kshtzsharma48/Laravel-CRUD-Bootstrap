<?php

$assets->add('bootstrap', 'css/bootstrap.css');
$assets->add('bootstrap-responsive', 'css/bootstrap-responsive.css');
$assets->add('global', 'css/global.css');
$assets->add('application', 'css/application.css');
$assets->add('jquery.ui', 'css/ui/jquery.ui.core.css');
$assets->add('jquery.ui.themes', 'css/ui/jquery.ui.theme.css');
$assets->add('datepicker', 'css/ui/jquery.ui.datepicker.css');
$assets->add('datepicker-addon', 'css/datepicked-addon.css');
$assets->add('jquery.ui.slider', 'css/jquery.ui.slider.css');

echo $assets->styles();
<?php

$assets->add('jquery', 'js/jquery-1.8.0.min.js');
$assets->add('application', 'js/application.js');
$assets->add('jquery.ui', 'js/ui/jquery.ui.core.js');
$assets->add('jquerycustom', 'js/ui/jquery-ui-1.8.23.custom.js');
$assets->add('datepicker', 'js/ui/jquery.ui.datepicker.js');
$assets->add('timepicker', 'js/jquery-ui-timepicker-addon.js');
$assets->add('slider', 'js/jquery.ui.slider.js');

echo $assets->scripts();
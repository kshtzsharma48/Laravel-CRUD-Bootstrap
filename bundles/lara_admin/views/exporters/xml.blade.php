<?php

header('Content-Type: text/xml');
if ($isDownload != null) {
	header('Content-Disposition: attachment; filename="data.xml"');
	header('Content-Transfer-Encoding: binary');
}

echo $content;
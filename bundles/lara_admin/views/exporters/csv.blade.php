<?php

header('Content-Type: text/x-comma-separated-values');
if ($isDownload != null) {
	header('Content-Disposition: attachment; filename="data.csv"');
	header('Content-Transfer-Encoding: binary');
}

echo $content;
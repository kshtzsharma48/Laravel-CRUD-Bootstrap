<?php

header('Content-Type: application/json');
if ($isDownload != null) {
	header('Content-Disposition: attachment; filename="data.json"');
	header('Content-Transfer-Encoding: binary');
}

echo $content;
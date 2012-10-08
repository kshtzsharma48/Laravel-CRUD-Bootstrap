<?php

class ImageHelper
{
	public static function  getSizePath($imagePath, $key)
	{
		$extension = File::extension($imagePath);
		$fileName = preg_replace("/\.$extension/", "", $imagePath);

		return "$fileName$key/" . $fileName . "_$key.$extension";
	}

}
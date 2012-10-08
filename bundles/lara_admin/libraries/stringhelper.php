<?php

class StringHelper
{
	function snakeToCamel($val)
	{
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
	}

}
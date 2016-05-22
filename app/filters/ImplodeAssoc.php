<?php

namespace App\Filters;

class ImplodeAssoc
{
	public static function process($array, $elementsDelimiter, $keyValueDelimiter)
	{
		$count = count($array);
		$str = '';
		foreach ($array as $field => $value)
		{
			$count--;
			$str .= $field . $keyValueDelimiter . $value;
			if ($count)
			{
				$str .= $elementsDelimiter;
			}
		}
		return $str;
	}
}

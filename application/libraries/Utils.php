<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils
{
	public function array_filter_items($data, $filter_function, $filter_function_args=FALSE)
	{
		$items = array();

		foreach ($data as $item)
		{
			if ($filter_function($item, $filter_function_args) === TRUE)
			{
				$items[] = $item;
			}
		}

		return $items;
	}

	public function array_group_by_index($data, $group_index)
	{
		$groups = array();

		foreach ($data as $item)
		{
			$new_group = TRUE;

			foreach ($groups as $k => $v)
			{
				if ($item[$group_index] == $k)
				{
					$groups[$k][] = $item;
					$new_group = FALSE;
					break;
				}
			}

			if ($new_group)
			{
				$groups[$item[$group_index]] = array($item);
			}
		}

		return $groups;
	}

	public function normalize_string($string)
	{
		$string = preg_replace('/[^a-zA-z0-9 ]/', ' ', $string);
		$string = mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
		$string = str_replace(' ', '', $string);

		return $string;
	}

	public function normalize_time($time)
	{
		return $time ? date('Y-m-d', strtotime($time)) : date('Y-m-d');
	}
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils
{
    function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->config('gcm');
        $this->CI->load->driver('cache', array('adapter' => $this->CI->config->item('cache_default_driver')));
    }

    public function cURL($url, $args=FALSE, $username=FALSE, $password=FALSE, $timeout=FALSE)
    {
        $args && ($args = '?' . http_build_query($args)) || $args = '';

        $cache_id = md5("cURL::$url$args");
        $response = $this->CI->cache->get($cache_id);

        if ( ! $response)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url . $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);

            if (($username) && ($password))
            {
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            }

            if ($timeout)
            {
                curl_setopt($ch, CURLOPT_CONNECTTIEMOUT, $timeout);
            }

            $data = curl_exec($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);

            $response = (object) array(
                'data' => $data,
                'info' => $info
            );
            $this->CI->cache->save($cache_id, $response);
        }

        return $response;
    }

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

    public function ap_time($string)
    {
        $date = strtotime($string);
        $hh = date('g', $date);
        $mm = date('i', $date);
        $ampm = date('a', $date);

        $mm = $mm !== "00" ? ":$mm" : "";

        $ampm = "{$ampm[0]}.m.";

        return "$hh$mm $ampm";
    }

    public function uuid_make($string) {
        $md5 = md5($string);
        $uuid_parts = array(
            substr($md5, 0, 8),
            substr($md5, 8, 4),
            substr($md5, 12, 4),
            substr($md5, 16, 4),
            substr($md5, 20)
        );

        $uuid = implode('-', $uuid_parts);

        return $uuid;
    }
}

<?php
namespace Jaspersoft\Tool;

class Util
{
    /**
     * This function will create an HTTP query string that may include repeated values
     * @param $params
     * @return string
     */
    public static function query_suffix($params)
    {
        foreach ($params as $k => $v) {
            if (is_bool($v)) {
                $params[$k] = ($v) ? 'true' : 'false';
            }
        }
        $url = http_build_query($params, null, '&');
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $url);
    }


}
<?php

class Curl
{   	

    function setup()
    {
        $header = array();
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] =  "Cache-Control: max-age=0";
        $header[] =  "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";


        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
    	curl_setopt($this->curl,CURLOPT_AUTOREFERER, true);
    	curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);	
    }


    function get($url)
    { 
    	$this->curl = curl_init($url);
    	$this->setup();

    	return $this->request();
    }

    function getAll($reg,$str)
    {
    	preg_match_all($reg,$str,$matches);
    	return $matches[1];
    }

    function postForm($url, $fields, $referer='')
    {
    	$this->curl = curl_init($url);
    	$this->setup();
    	curl_setopt($this->curl, CURLOPT_URL, $url);
    	curl_setopt($this->curl, CURLOPT_POST, 1);
    	curl_setopt($this->curl, CURLOPT_REFERER, $referer);
    	curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
    	return $this->request();
    }

    function getInfo($info)
    {
    	$info = ($info == 'lasturl') ? curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL) : curl_getinfo($this->curl, $info);
    	return $info;
    }

    function request()
    {
    	return curl_exec($this->curl);
    }
}

$curl = new Curl();
$html = $curl->get("https://pre.corrupt-net.org/search.php?search=".$_GET['q']);

$output = strip_tags($html);
$output = explode("&nbsp;", $output);
$pretime = $output[8];

date_default_timezone_set('Europe/London');
$from_time = date_create($pretime);
$to_time = date_create(date("Y-m-d H:i:s"));

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'h',
        'i' => 'm',
        's' => 's',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = "<b>" . $diff->$k . "</b>" . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? "Pre: " . implode(' ', $string) . " ago" : '-';
}

echo time_elapsed_string($pretime, true);

?>

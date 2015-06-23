<?php

/**
 * 
 * A minimalist php sdk for GITHUB SOAP API v3 
 * 
 * Classname : Github
 * Filename  : github.php
 * Author    : esclkm, Vignesh
 * 
 */
if (!function_exists('curl_init'))
{
	throw new Exception('Github needs the CURL PHP extension.');
}
if (!function_exists('json_decode'))
{
	throw new Exception('Github needs the JSON PHP extension.');
}

class Github
{

	//Client ID provided by github
	protected $app_id;
	//Client Secret provided by github
	protected $app_secret;
	//папка кэша
	protected $cache_dir = NULL;
	//папка картинок
	protected $files_dir = NULL;	
	/*
	 * constructor - checks if any of the data members r available and aims at obtaining an access token
	 * */

	function __construct($app_id, $app_secret)
	{
		$this->user = FALSE;

		//Sets app id and app secret
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;

	}

	/*
	 * curls the given url
	 * */

	protected function curl($method, $url, $postdata = NULL, $json = true, $cache = true, $debug = false)
	{
		$file = $this->cache_dir."github_".md5($url).sha1($url).".json";

		if($cache && $this->cache_dir && $method == "GET" && file_exists($file) && (time() - 6 * 60 * 60 < filemtime($file))) 
		{
			$output = file_get_contents($file);
			$header  = array();
			$errmsg  = '';
			$err = ''; 
			if ($output == "404")
			{
				$header['http_code'] = '404';
			}
		}

		else
		{
		//	cot_print($method, $url);
			$ch = curl_init($url);
			$headers =	array(
				'Accept: application/json', 
			);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			if ($postdata != NULL)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			}
			$output = curl_exec($ch);
			$header  = curl_getinfo($ch);
			$errmsg  = curl_error( $ch );
			$err = curl_error($ch); 
			curl_close($ch);
			
			if($debug)
			{
				return array($url, json_decode($output, true), 'header', $header, 'err', $err, 'errmsg', $errmsg);
			}
			//cot_print($url, json_decode($output, true), 'header', $header, 'err', $err, 'errmsg', $errmsg);
			//if($url == "https://api.github.com/repos/esclkm/rssreader/contents/scr2")
			//	cot_print($output, $header, $errmsg);
		}
		if($header['http_code'] == '302')
		{
			return $header['redirect_url'];
		}
		if($header['http_code'] == '403' || $header['http_code'] == '404')
		{
			if($cache && $this->cache_dir && $method == "GET") 
			{
				file_put_contents($file,"404");
			}
			return false;
		}
		if ($output)
		{	
			global $cfg;

			if($cache && $this->cache_dir && $method == "GET" && count($header) > 0 ) 
			{
				file_put_contents($file,$output);
			}
			if($json)
			{
				return json_decode($output, true);
			}
			return $output;
		}
		return FALSE;
	}
	
	public function download($url)
	{
		if(!$this->files_dir)
		{
			return $url;
		}
		$path_parts = pathinfo($url);		
		$ext = $path_parts['extension'];
		$file_name = "github_".md5($url).sha1($url);
		$file = $this->files_dir.$file_name.".".$ext;
		
		set_time_limit(0);
		
		if(file_exists($file) && (time() - 7 * 24 * 60 * 60 < filemtime($file))) 
		{
			return $file;
		}
		# open file to write
		$fp = fopen ($file, 'w+');
		# start curl
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		# set return transfer to false
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
		curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		# increase timeout to download big file
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		# write data to local file
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		# execute curl
		curl_exec( $ch );
		# close curl
		curl_close( $ch );
		# close local file
		fclose( $fp );

		if (filesize($file) > 0)
		{
			return $file;
		}
		return $url;
	}
	
	/*
	 * Call to api
	 * */

	public function api($method, $path, $params = NULL, $postdata = NULL, $json = true, $cache = true, $debug = false)
	{
		$path = ($path[0] == '/') ? "{$path}" : "/{$path}";
		$url = "https://api.github.com".$path;

		if (is_array($params))
		{
		//	$params = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
			$params = http_build_query($params, '', '&');
		}
		if($this->app_id && $this->app_secret)
		{
			$params .= (!empty($params)) ? "&" : "";
			$params .= 'client_id='.$this->app_id.'&client_secret='.$this->app_secret;
		}
		if(!empty($params))
		{
			$url .= '?'.$params;
		}

		return $this->curl($method, $url, $postdata, $json, $cache, $debug);
	}
	
	public function rate()
	{
		$rate = $this->api("GET", "rate_limit", NULL, NULL, true, false );
		return $rate['rate'];
	}
	
	/*
	 * Setter functions
	 * 
	 * */	
	public function setCacheDir($dir)
	{
		if($dir)
		{
			$dir = ($dir[mb_strlen($dir)-1] == '/') ? $dir : $dir."/";
			$this->cache_dir = $dir;
			if(!file_exists($this->cache_dir))
			{
				@mkdir($this->cache_dir);
			}
		}		
	}
	public function setFilesDir($dir)
	{
		if($dir)
		{
			$dir = ($dir[mb_strlen($dir)-1] == '/') ? $dir : $dir."/";
			$this->files_dir = $dir;
			if(!file_exists($this->files_dir))
			{
				@mkdir($this->files_dir);
			}
		}		
	}	
}

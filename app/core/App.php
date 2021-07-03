<?php 
class App
{
	protected $controller = 'home';
	protected $method = 'index';
	protected $params = [];



	public function __construct()
	{
		$url = $this->parseUrl();
		if( isset($url[0]) )
		{
			if( file_exists("../app/controllers/{$url[0]}.php") )
			{
				$this->controller = $url[0];
				unset($url[0]);
			}
		}

		require_once "../app/controllers/{$this->controller}.php";
		$this->controller = new $this->controller;

		// method
		if( isset($url[1]) )
		{
			if( method_exists($this->controller, $url[1]) )
			{
				$this->method = $url[1];
				unset($url[1]);
			}	
		}
		
		//param
		$this->params = $url ? array_values($url) : [];

		// thuc thi method 			[class, method], params)
		//home/index/12
		// thuc thi phuong thuc index co tham so truyen vao la 12 trong class Home
		call_user_func_array([$this->controller, $this->method], $this->params);
	}


	public function parseUrl()
	{
		if( isset($_GET['url']) )
		{
			return $url = explode( '/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL) );
		}
	}
}
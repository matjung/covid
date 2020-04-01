<?php namespace php\github;

class phpHubResult
{	public $url;
	public $err;
	public $errmsg;
	public $info;
	public $response;
	public $request;
	public $success;
	public $failed;
	public $http_code;
	public $content_type;
	public $content_length;
	public $urlip;
	
	public function __construct(string $url, $err,string $errmsg,array $info, $response,array $request,bool $success) 
	{
		$this->url=$url;
		$this->err=$err;
		$this->errmsg=$errmsg;
		$this->info=$info;
		$this->response=$response;
		$this->request=$request;
		$this->success=$success;
		$this->failed=! $success;
		$this->initInfo();
	}
	
	private function initInfo() 
	{
		if(isset($this->info["http_code"])) { $this->http_code=$this->info["http_code"];}
		if(isset($this->info["content_type"])) { $this->content_type=$this->info["content_type"];}
		if(isset($this->info["download_content_length"])) { $this->content_length=$this->info["download_content_length"];}
		if(isset($this->info["primary_ip"])) { $this->urlip=$this->info["primary_ip"];}
	}
	
	public function __toString()
	{
		if(gettype($this->response) == "object") { return json_encode($this->response);	}
		if(gettype($this->response) == "string") { return $this-response;}
		if(gettype($this->response) == "array") { $o=new \Stdclass();$o->array=$this->response; return json_encode($o); }
		/*echo gettype($this->response)."<br>";*/
		return NULL;
	}
}
?>

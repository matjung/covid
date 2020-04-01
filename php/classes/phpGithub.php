<?php namespace php\github;
require_once("phpHubResult.php");
require_once("phpGithubServiceArray.php");

/*

https://smee.io/HZYgCsgVj0W3Luu

*/
class phpGithub
{
	public $hubhost = "https://api.github.com/";
	public $hubgetrepo = "https://api.github.com/repos/{owner}/{repo}";
	// https://api.github.com/repos/openZH/covid_19/contents/fallzahlen_kanton_total_csv
	public $hubgetcontent = "https://api.github.com/repos/{repo}/contents/{path}";
	// https://developer.github.com/apps/building-oauth-apps/authorizing-oauth-apps/#redirect-urls
	public $huboauthauthorize ="https://github.com/login/oauth/authorize?client_id={client_id}&scope={scope}&redirect_uri={redirect_uri}&state={state}";
	
	public $CURLOPT_USERAGENT ="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36";
	public $CURLOPT_RETURNTRANSFER=true;
	private $curl_channel;
	private $curl_open=false;
	private $curl_info;
	private $curl_last_message;
	private $curl_last_err_message;
	private $curl_options=array();
	private $rootContents;
	private $hasContents;
	
	public function __construct() 
	{
		$this->addCurlConfig(CURLOPT_USERAGENT,$this->getPhpUseragent());
		$this->addCurlConfig(CURLOPT_RETURNTRANSFER, true);
		$this->hasContents=false;
	}
	
	function __destruct()
	{
		if($this->curl_open) 
		{
			curl_close($this->getCurlChannel());
		}
	}
	public function getHubhost() 
		{ 
			return $this->hubhost;
		}
	public function setHubhost(string $hubhost) 
		{ 
			$this->hobbost=$hubhost;
		}
	public function getPhpUseragent() 
		{ 
			return $this->CURLOPT_USERAGENT;
		}
	public function setPhpUseragent(string $useragent) 
		{ 
			$this->CURLOPT_USERAGENT=$useragent;
		}
				
	private function setLastCall(array $curl_info)
	{
		$this->curl_info=$curl_info;
	}

	private function getLastCall()
	{
		return $this->curl_info;
	}
	
	private function setLastErrMessage(string $err_message)
	{
		$this->curl_last_err_message=$err_message;
	}
	
	public function getLastErrorMessage()
	{
		return $this->curl_last_err_message;
	}
	
	private function getCurlChannel() 
	{
		if($this->curl_channel == null)
		{
			$this->curl_channel = curl_init();
			$this->curl_open=true;
		} else {
			return $this->curl_channel;
		}
	}
	public function addCurlConfig(int $key,string $value)
	{
		$ch=$this->getCurlChannel();
		curl_setopt($this->curl_channel,$key,$value);
		$this->curl_options[$key]=$value;
	}

	// CURLOPT_HTTPHEADER,array ("Accept: application/json")
	public function addCurlHeader(int $key,array $header)
	{
		$ch=$this->getCurlChannel();
		curl_setopt($this->curl_channel,$key,$header);
		$this->curl_options[$key]=$header;
	}	
	
	public function returnRoot()
	{
		$ch=$this->getCurlChannel();
		curl_setopt($ch, CURLOPT_URL, $this->getHubhost());
		$output = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$this->setLastErrMessage("$err $errmsg");
		if($err == 0) 
		{
			$this->setLastCall(curl_getinfo($ch));
			return new phpHubResult($this->getHubhost(),$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,true);
		}
		return new phpHubResult($this->getHubhost(),$err,$errmsg,false,false,$this->curl_options,false);
	}
	/* https://api.github.com/repos/{owner}/{repo} */
	public function returnRepository(string $owner,string $repo)
	{
		$url=$this->hubgetrepo;
		$url=str_replace("{owner}",$owner,$url);
		$url=str_replace("{repo}",$repo,$url);
		$ch=$this->getCurlChannel();
		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$this->setLastErrMessage("$err $errmsg");
		if($err == 0) 
		{
			$this->setLastCall(curl_getinfo($ch));
			if($this->getLastCall()["http_code"] == 404) 
			{ 
				$this->setLastErrMessage($this->getLastCall()["http_code"]." ".json_decode($output)->message);
				return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,false); 
			}
			return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,true);
		}
		return new phpHubResult($url,$err,$errmsg,false,false,$this->curl_options,false);
	}
	
	public function returnRootContent($repo)
	{
		if(gettype($repo=="object")) 
		{ 
			$repo=$repo->contents_url; 
			$repo=str_replace("{+path}","",$repo);
		}
		$url=$repo;
		$ch=$this->getCurlChannel();
		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$this->setLastErrMessage("$err $errmsg");
		if($err == 0) 
		{
			$this->setLastCall(curl_getinfo($ch));
			if($this->getLastCall()["http_code"] == 404) 
			{ 
				$this->setLastErrMessage($this->getLastCall()["http_code"]." ".json_decode($output)->message);
				return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,false); 
			}
			$this->rootContents=json_decode($output);
			$this->hasContents=true;
			return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,true);
		}
		return new phpHubResult($url,$err,$errmsg,false,false,$this->curl_options,false);
	}
	public function returnContent($repo,string $path)
	{
		$url=null;
		if(gettype($repo) == "object") 
		{ 
			$content=$repo->contents_url; 
			$content=str_replace("{+path}",$path,$content);
			$url=$content;
		}
		if(gettype($repo) == "string")
		{
				$url=$this->hubgetcontent;
				$url=str_replace("{repo}",$repo,$url);
				$url=str_replace("{path}",$path,$url);
		}
		if($url == null) { $errmsg="Parameters repo and path did not lead to an URL";$url="no URL";$err=404; return new phpHubResult($url,$err,$errmsg,false,false,$this->curl_options,false);}
		$ch=$this->getCurlChannel();
		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$this->setLastErrMessage("$err $errmsg");
		if($err == 0) 
		{
			$this->setLastCall(curl_getinfo($ch));
			if($this->getLastCall()["http_code"] == 404) 
			{ 
				$this->setLastErrMessage($this->getLastCall()["http_code"]." ".json_decode($output)->message);
				return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,false); 
			}
			return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,true);
		}
		return new phpHubResult($url,$err,$errmsg,false,false,$this->curl_options,false);
	}
	
	public function hasContentsFolder($contents = null) 
	{
		if($contents == null && $this->hasContents) 
		{ 
			$contents=$this->rootContents;
		}
		if(gettype($contents) == "array") {
			foreach($contents as $content)
			{
				if($content->type =="dir") { return true;}
			}
		}
		return false;
	}
	
	public function getContentsIterator($contents = null) 
	{
		if($contents == null && $this->hasContents) 
		{ 
			$contents=$this->rootContents;
		}
		if(gettype($contents) == "array") {
			return new \ArrayIterator($contents);
		}
	}
	
	/* https://github.com/settings/apps  -> OAuth Apps -> Create New
	   public $huboauthauthorize ="https://github.com/login/oauth/authorize?client_id={client_id}&redirect_uri={redirect_uri}&state={state}";
		Sample redirect_url = http://localhost:1080/webs/githubcallback.php/  
		access_token=9a439ff6d9ad91b9839265ee30ebb60830f7fe7c&scope=&token_type=bearer" 
	*/
	public function requestAccessToken($client_id,$scope,$redirect_uri,$state=true)
	{
		if($state) { $state=bin2hex(openssl_random_pseudo_bytes(32));}
		$url=$this->huboauthauthorize;
		$url=str_replace("{client_id}",$client_id,$url);
		$url=str_replace("{scope}",$scope,$url);
		$url=str_replace("{redirect_uri}",$redirect_uri,$url);
		$url=str_replace("{state}",$state,$url);
		header("Location: $url");
	}

	public function convertCodeToToken($client_id,$client_secret,$code,$state,$redirect_uri)
	{
		$ch=$this->getCurlChannel();
		$url="https://github.com/login/oauth/access_token";
		curl_setopt($ch, CURLOPT_URL, $url);
		$postFields["client_id"]=$client_id;
		$postFields["client_secret"]=$client_secret;
		$postFields["code"]=$code;
		$postFields["redirect_uri"]=$redirect_uri;
		$postFields["state"]=$state;
		curl_setopt($ch,CURLOPT_POSTFIELDS, $postFields);
		$output = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$this->setLastErrMessage("$err $errmsg");
		if($err == 0) 
		{
			$this->setLastCall(curl_getinfo($ch));
			if($this->getLastCall()["http_code"] == 404) 
			{ 
				$this->setLastErrMessage($this->getLastCall()["http_code"]." ".json_decode($output)->message);
				return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,false); 
			}
			return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),$output,$this->curl_options,true);
		}
		return new phpHubResult($url,$err,$errmsg,false,false,$this->curl_options,false);

	}
	
	
	public function callWebService($service)
	{
		global $gh;
		if(array_key_exists($service,$gh))
		{
		echo $gh[$service];
		$url=$gh[$service];
		$ch=$this->getCurlChannel();
		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);
		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$this->setLastErrMessage("$err $errmsg");
		if($err == 0) 
		{
			$this->setLastCall(curl_getinfo($ch));
			if($this->getLastCall()["http_code"] == 404) 
			{ 
				$this->setLastErrMessage($this->getLastCall()["http_code"]." ".json_decode($output)->message);
				return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,false); 
			}
			if($this->getLastCall()["http_code"] == 401) 
			{ 
				$this->setLastErrMessage($this->getLastCall()["http_code"]." ".json_decode($output)->message);
				return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,false); 
			}
			
			return new phpHubResult($url,$err,$errmsg,$this->getLastCall(),json_decode($output),$this->curl_options,true);
		}
		return new phpHubResult($url,$err,$errmsg,false,false,$this->curl_options,false);
		}
		else
		{
			return new phpHubResult($service,404,"404 $service not found in web service catalog",false,false,$this->curl_options,false);
		}
	}
}

?>

<?php namespace php\github;
require_once("phpHubResult.php");
class phpGithubContent
{
	private $content;
	public $isObject=false;
	public $isArray=false;
	public $isFile=false;
	public $isDir=false;
	public function __construct($content) 
	{
		if(gettype($content) == "array") 
		{ 
			$this->isArray=true;
			$this->isDir=true;
		}
		if(gettype($content) == "object") 
		{ 
			$this->isObject=true;
			$this->isFile=true;
		}
		$this->content=$content;
	}	
	
	public function count()
	{
		if($this->isDir)
		{
			return count($this->content);
		} else {
			return null;
		}
	}
	
	public function returnEntry(int $no)
	{
		if($this->isDir)
		{
			if($no <= $this->count())
			{
				return $this->content[$no];
			} else{
				return -1;
			}
		} else
		{
			return null;
		}
	}
	public function getIterator() 
	{
		if($this->isArray) 
		{
			return new \ArrayIterator($this->content);
		}
	}

	public function hasFilenameLike($like)
	{
		foreach($this->getIterator() as $entry)
		{
			if($entry->type == "file" && strpos($entry->name,$like) !== false)
			{
				return true;
			}
		}
		return false;
	}
	
	public function cntFilenameLike($like,$cnt=0)
	{
		foreach($this->getIterator() as $entry)
		{
			if($entry->type == "file" && strpos($entry->name,$like) !== false)
			{
				$cnt++;
			}
		}
		return $cnt;
	}

	public function getPathnameLike($like,$ff=1)
	{
		$cnt=0;
		foreach($this->getIterator() as $entry)
		{
			if($entry->type == "file" && strpos($entry->name,$like) !== false)
			{
				$cnt++;
				if($cnt == $ff) 
				{
					return $entry->path;
				}
			}
		}
		return null;
	}
		public function getEncodedContent()
		{
		if($this->content->type == "file" && $this->content->encoding == "base64") 
		{
			return str_replace("\xEF\xBB\xBF",'',base64_decode($this->content->content));
		}
		}
		
		public function getFilename()
		{
			if($this->content->type == "file")
			{
				return $this->content->name;
			}
		}
		public function getFilesize()
		{
			if($this->content->type == "file")
			{
				return $this->content->size;
			}
		}

		public function getFilepath()
		{
			if($this->content->type == "file")
			{
				return $this->content->path;
			}
		}

		public function getContentProperty(string $property)
		{
			if($this->content->type == "file")
			{
				return $this->content->$property;
			}
		}		
}	

?>
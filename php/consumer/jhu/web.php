<?php
use php\github as hub;
ini_set('default_charset', 'utf-8');
error_log("web.php");
require_once("local.inc");
require_once("phpGithub.php");
require_once("phpGithubContent.php");
require_once("hubHelper.php");

$repo="CSSEGISandData/COVID-19";
$path="csse_covid_19_data/csse_covid_19_daily_reports";

/* 
2020-04-06 - the class might fast become deprecated/obsolete
class phpGithub is based on GitHub API v3 - a class for GitHub API v4 - is work in progress 

*/

$hub = new php\github\phpGithub();
if(gethostname()=="AspireE15") { $hub->AddCurlConfig(CURLOPT_CAINFO, LOCAL_CURLOPT_CAINFO);}
$response=$hub->returnContent($repo,$path);

if($response->success) 
{ 
	$files=new php\github\phpGithubContent($response->response);
	if($files->isDir)
	{
		$counter=$files->count();
		$counter-=2;
		$fileEntry=$files->returnEntry($counter);
		returnJsonDocument($fileEntry);
	} 
		else
	{
		return returnJsonError(500, "Unexpected github format change in $repo/$path");
	}
} 
	else 
{
	// if something went wrong, return 400 to web services client
	return returnJsonError(400,$hub->getLastErrorMessage());
}

function returnJsonError(int $code,string $message)
{
	http_response_code($code);
	if(!headers_sent())
	{
		header("Content-Type: application/json");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: *");
		header("Access-Control-Expose-Headers: *");
	}
	echo "{\"error\":{\"code\":$code,\"message\":\"$message\"}}";
}

function returnJsonDocument($entry)
{
	if(!headers_sent())
	{
		$DT = new DateTime('NOW');
		$DTF = $DT->format('c');
		header("Content-Type: application/json");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: *");
		header("Access-Control-Expose-Headers: *");
		header("X-Host-Time: $DTF");
		header("X-Host-Slave:{$_SERVER['SERVER_NAME']}");
		
	}
	$json=new Stdclass();
	$json->response=new Stdclass();
	$json->response->source=$entry->download_url;
	$json->response->path=$entry->path;
	$json->response->name=$entry->name;
	$json->response->size=$entry->size;
	$json->response->sha=$entry->sha;
	$json->response->html_url=$entry->html_url;
	echo json_encode($json);
}
?>
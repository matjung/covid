<?php
use php\github as hub;
$loc_class_path="..\classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $loc_class_path);
include("phpGithub.php");
include("phpGithubContent.php");
include("hubHelper.php");
require_once("phpGithubServiceArray.php");

$client_id="c8ea8d4a38b59ae43915";
$client_secret="9c4fa2882e084d94488929e7cd8bfafb55acda7c";
$scope="user";


$hub = new php\github\phpGithub();
echo $hub->getHubhost();
$hub->AddCurlConfig(CURLOPT_CAINFO, "G:/xampp2/php/extras/cacert.pem");

$client_id="c8ea8d4a38b59ae43915";
$client_secret="9c4fa2882e084d94488929e7cd8bfafb55acda7c";
$code="42e000a070ed4c0e3c30";
$state="774fcf407328c3f6171b6fef347c69a3a3eb7642e6b46f18fdd03a6dcaa9ecaa";

#access_token=9fa5dfc9896f830a9e495519ac05c3a4d3bc620f&scope=user&token_type=bearer" 

#$response=$hub->convertCodeToToken($client_id,$client_secret,$code,"http://localhost:1080/webs/info/phpGithubOAuthCallback.php/",$state);
#$this->addCurlHeader(CURLOPT_HTTPHEADER,array("Accept: application/vnd.github.machine-man-preview+json"));
#access_token=b6eea12a6e8247c755a37ab061866e90d5485923&scope=user&token_type=bearer" 
$hub->AddCurlHeader(CURLOPT_HTTPHEADER, array("Authorization: token d5bcf90d9d7ef67d769edeb062e7c1a5683531e5","Accept: application/vnd.github.machine-man-preview+json"));

$response=$hub->callWebService("current_user_url");
if($response->success) { 
#	echo $response->response;
#	print_r($response->info);
# client_id=c8ea8d4a38b59ae43915 redirect_uri=localhost:1080/webs/info/githubclient.php
# https://github.com/login/oauth/authorize?client_id=c8ea8d4a38b59ae43915&redirect_uri=http://localhost:1080/webs/info/githubcallback.php/
#
# http://localhost:1080/webs/info/phpGithubOAuthCallback.php/?code=4d92099722a93314d21a&state=afbf7744b2afcd516030ac5ca9959daae4aca4509aa72a864227a86f47224825
#
	echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";
	hub\helper::printTableFromArray($response->response);
	$content=new php\github\phpGithubContent($response->response);
	echo $content->isArray." Array <br>";
	echo $content->isObject." Object <br>";
	print_r($response->info);
	echo "<hr>";
	var_dump($response->response);
} 
	else 
{
	echo "Request failed<br>";
	echo $response;
}

die;

$response=$hub->returnRoot();

if($response->success) { 
#	echo $response->response;
#	print_r($response->info);
	echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";
	hub\helper::printTableFromArray($response->response);
	$content=new php\github\phpGithubContent($response->response);
	echo $content->isArray." Array <br>";
	echo $content->isObject." Object <br>";
	hub\helper::printAssociativeArrayFromRootAsPhpSourceCode($response->response,"gh");

}
die;
/* https://github.com/openZH/covid_19/tree/master/fallzahlen_kanton_total_csv */
#$response=$hub->returnRepository("CSSEGISandData","COVID-19");
$response=$hub->returnRepository("openZH","covid_19");
if($response->success) { 
	echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";
#	hub\helper::printTableFromArray($response->info);
#	hub\helper::printTableFromArray($response->response);
	$repo=$response->response;
#	$response=$hub->returnRootContent($repo);
	$response=$hub->returnContent($repo, "fallzahlen_kanton_total_csv");
	if($response->success) { 
		echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";
		$contents=$response->response;
		echo $hub->hasContentsFolder()."<hr>";
#		hub\helper::printTableFromJson($contents);
		foreach($hub->getContentsIterator($contents) as $entry)
		{
#			echo $entry->type ." ". $entry->name ."<br>";
		}
		$files=new php\github\phpGithubContent($contents);
		foreach($files->getIterator() as $entry)
		{
#			echo $entry->type ." ". $entry->name ." " . $entry->path . "<br>";
		}
		echo $files->hasFilenameLike("ZH");
		echo "<br>".$files->cntFilenameLike("ZH");
		echo $files->getPathnameLike("ZH");
	}
}

$response=$hub->returnContent($repo, $files->getPathnameLike("ZH"));
if($response->success) { 
	echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";
#	echo $response;
#	hub\helper::printTableFromArray($response->response);
	$files=new php\github\phpGithubContent($response->response);
	echo $files->isArray."<br>";
	echo $files->isObject."<br>";
	echo $files->getEncodedContent();
	echo $files->getContentProperty("download_url");
}
	
	
echo $hub->getLastErrorMessage();
?>
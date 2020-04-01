<?php
use php\github as hub;
$loc_class_path="..\classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $loc_class_path);
include("phpGithub.php");
include("phpGithubContent.php");
include("hubHelper.php");
require_once("phpGithubServiceArray.php");

/* 
It is best, if you get your own client_it and client_secret from github
https://github.com/settings/apps  -> OAuth Apps -> Create New 
*/

$client_id="c8ea8d4a38b59ae43915";
$client_secret="9c4fa2882e084d94488929e7cd8bfafb55acda7c";
$scope="user";
$github_callback="http://localhost:1080/webs/info/githubclient_current_user.php/";

/* request access code from github - Step 1*/

if( count($_POST) == 0 && count($_GET) == 0)  
{
	$hub = new php\github\phpGithub();
	$hub->AddCurlConfig(CURLOPT_CAINFO, "G:/xampp2/php/extras/cacert.pem");
	$hub->requestAccessToken($client_id,$scope,$github_callback);
}

/* convert access code into token from github - Stel 2 */

if( count($_POST) >= 0 && count($_GET) == 2) 
{
	$hub = new php\github\phpGithub();
	$hub->AddCurlConfig(CURLOPT_CAINFO, "G:/xampp2/php/extras/cacert.pem");
	$response=$hub->convertCodeToToken($client_id,$client_secret,$_GET["code"],$_GET["state"],$github_callback);
	if($response->success) 
	{
		if(strpos($response->info["content_type"],"application/x-www-form-urlencoded") !== false)
		{
			parse_str($response->response,$oauth);
			print_r($oauth);
			$token=$oauth["access_token"];
			$Authorization="Authorization: token $token";
			$hub->AddCurlConfig(CURLOPT_HTTPGET, true);
			$hub->AddCurlHeader(CURLOPT_HTTPHEADER, array($Authorization,"Accept: application/vnd.github.machine-man-preview+json"));
			
			/* call API / Web Service that requires Authorization - Step 3 */
			$response=$hub->callWebService("current_user_url");
		}
	}		
}

/* process github current user response */

if($response->success) 
{ 
	echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";
	hub\helper::printTableFromArray($response->response);
} 
	else 
{
	echo "Request failed<br>";
	echo $response;
}
	
echo $hub->getLastErrorMessage();

?>
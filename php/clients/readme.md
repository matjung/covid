This folder contains sample php clients to work with api.github.com

[Mark Down Cheat Sheet](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet)

[Usage Documentation at phpclasses.org](https://www.phpclasses.org/package/11581-PHP-Get-responses-to-requests-to-the-Github-API.html)

* php/clients/githubclient.php
* php/clients/githubclient_current_user.php.php

<?php
$loc_class_path="../classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $loc_class_path);
include("phpGithub.php");

$hub = new php\github\phpGithub();
echo $hub->getHubhost();
$hub->AddCurlConfig(CURLOPT_CAINFO, "/path/to/curl/php/cacert.pem");

$response=$hub->returnRoot();
if($response->success) { 
#	echo $response->response; 
#	print_r($response->info);
	echo $response->urlip ." " . $response->url ." " . $response->http_code . "<br>";

}
echo $hub->getLastErrorMessage();
?>

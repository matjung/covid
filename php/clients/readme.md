This folder contains sample php clients to work with api.github.com

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

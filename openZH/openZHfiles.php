<?php
use php\github as hub;
ini_set('default_charset', 'utf-8');
ini_set("error_log","log/openZH.log");
error_log("openZHfiles.php");

$repo="openZH/covid_19";
$content="fallzahlen_kanton_total_csv";
$filename="fallzahlen_kanton.json";
$exportFolder="openZH";

if(file_exists($filename)) {
	if (time()-filemtime($filename) > 3600 * 24) {
		updateFile($repo,$content,$filename);
		checkFile($filename,$exportFolder);
	} else {
		returnFile($filename);
	}
} else {
	openzh_run_github($repo,$content,$filename);
	checkFile($filename,$exportFolder);
}

function returnFile($filename) {
include("../classes/phpGithubContent.php");
include("../classes/hubHelper.php");
$input=file_get_contents($filename);
$json=json_decode($input);
$files = new php\github\phpGithubContent($json);
$DT = new DateTime('NOW');
$DTF = $DT->format('c');
header("Content-Type: text/plain");
header("Access-Control-Allow-Origin: *");
header("X-Source: https://raw.githubusercontent.com/openZH/covid_19/master/fallzahlen_kanton_total_csv");
header("X-Time: $DTF");

echo "name,size,path,download_url\n";
foreach($files->getIterator() as $entry) 
{
	if(hasProperties($entry,["name","path","size","type","download_url"]))
	{
		if($entry->type == "file")
		{
			echo $entry->name . "," . $entry->size . "," . $entry->path . "," . $entry->download_url."\n";
		}
	}
}

}


function openzh_run_github($repo,$content,$filename)
{
include("../classes/phpGithub.php");
include("../classes/phpGithubContent.php");
include("../classes/hubHelper.php");
$hub = new php\github\phpGithub();
error_log($hub->getHubhost());
error_log(gethostname());
if(gethostname()=="AspireE15") { $hub->AddCurlConfig(CURLOPT_CAINFO, "G:/xampp2/php/extras/cacert.pem"); }
$response=$hub->returnContent($repo,$content);
if($response->success) { 
	error_log($response->urlip ." " . $response->url ." " . $response->http_code);
	file_put_contents($filename, json_encode($response->response));
} else 
{
return returnError($hub->getLastErrorMessage());
}
}	

function updateFile($repo,$content,$filename)
{
	openzh_run_github($repo,$content,$filename);
	returnFile($filename);
}

function returnError($errmsg) 
{
header("Content-Type: text/plain; charset=utf-8");
http_response_code(501);
echo $errmsg;
}

function checkFile($filename,$path)
{
error_log("checkFile $filename in $path");	
if(file_exists($filename))
{
	$content=file_get_contents($filename);
	$d=date("Y_m_d_");
	$path=str_replace("/",DIRECTORY_SEPARATOR,$path);
	$mkdir=(pathinfo($path)["dirname"] == ".") ? pathinfo($path)["filename"] : pathinfo($path)["dirname"];
	$path = $path . DIRECTORY_SEPARATOR . $d . $filename;	
	if(file_exists($mkdir) === FALSE) 
	{
		mkdir($mkdir,0777,true);
	}
	if(file_exists($path) === FALSE)
	{
	$f=file_put_contents($path, $content);
	}
} // end of file exists
} // end of function

function hasProperties($obj,$arr) { 
if(is_array($arr) && is_object($obj)) {
	$cnt1=count($arr);
	$cnt2=0;
	foreach($arr as $key) {
		if(property_exists($obj,$key)) { $cnt2++; }
	}
	if($cnt1 == $cnt2) { return true;}
} 
return false;
}
?>
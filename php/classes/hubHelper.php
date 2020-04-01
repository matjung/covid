<?php namespace php\github;

class helper
{
	public static function printTableFromArray($array)
	{
		echo "<table>";
		foreach($array as $key => $value) {
			echo "<tr><td>$key";
			if(is_array($value)) { $value=implode(",",$value);}
			if(is_object($value)) { $value="";}
			echo "<td>$value</tr>";
		}
		echo "</table>";
	}
	
	public static function printTableFromJson($json)
	{
		if(gettype($json)=="string") { $json=json_decode($json);}
		echo "<table>";
		foreach($json as $key) {
			echo "<tr><td>";
			helper::printTableFromArray($key);
			echo "</td></tr>";
		}
	}
	
	public static function printAssociativeArrayFromRootAsPhpSourceCode($rootResponse,$arrayVariableName)
	{
		foreach($rootResponse as $key => $value) 
		{
			echo '$'."$arrayVariableName".'["'.$key.'"] = "'.$value.'"'.";\n";
		}
	}
}

?>
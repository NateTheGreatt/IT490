<?php
include("inc/db.php");

$array = json_decode($_POST['data'], true);

print_r($array);

$SkidID = $array['SkidID'];
$SkidWeight = $array['SkidWeight'];
$SkidContents = mysql_escape_String($array['SkidContents']);

$sql = "
UPDATE cargo
SET 
 SkidWeight='$SkidWeight',
 SkidContents='$SkidContents'
WHERE
 SkidID=$SkidID
";

mysql_query($sql) or die ("Error in query: ".mysql_error())
?>
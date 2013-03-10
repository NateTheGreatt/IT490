<?php
include("inc/db.php");

$array = json_decode($_POST['data'], true);

//print_r($array);

$values = "";
$values = implode("','",$array);

$sql = "
INSERT INTO 
	flight
VALUES
	('','$values')
";

mysql_query($sql) or die ("Error in query: ".mysql_error());

echo "Row added.<br /><br />";
echo "DestinationCode: ".$array['DestinationCode']."<br />";
echo "OriginCode: ".$array['OriginCode']."<br />";
echo "TailNumber: ".$array['TailNumber']."<br />";
echo "CrewID: ".$array['CrewID']."<br />";
echo "SkidID: ".$array['SkidID']."<br />";
echo "DepartureTime: ".$array['DepartureTime']."<br />";
echo "ArrivalTime: ".$array['ArrivalTime']."<br />";

?>
<?php
include("inc/db.php");

$array = json_decode($_POST['data'], true);

$FlightNumber = $array['FlightNumber'];
$DestinationCode = mysql_escape_String($array['DestinationCode']);
$OriginCode = mysql_escape_String($array['OriginCode']);
$TailNumber = $array['TailNumber'];
$CrewID = $array['CrewID'];
$SkidID = $array['SkidID'];
$DepartureTime = $array['DepartureTime'];
$ArrivalTime = $array['ArrivalTime'];

$sql = "
UPDATE flight 
SET 
	DestinationCode='$DestinationCode',
	OriginCode='$OriginCode',
	TailNumber=$TailNumber,
	CrewID=$CrewID,
	SkidID=$SkidID,
	DepartureTime='$DepartureTime',
	ArrivalTime='$ArrivalTime'
WHERE
	FlightNumber=$FlightNumber
";

mysql_query($sql) or die ("Error in query: ".mysql_error());

echo "Row updated.<br /><br />";
echo "FlightNumber: ".$array['FlightNumber']."<br />";
echo "DestinationCode: ".$array['DestinationCode']."<br />";
echo "OriginCode: ".$array['OriginCode']."<br />";
echo "TailNumber: ".$array['TailNumber']."<br />";
echo "CrewID: ".$array['CrewID']."<br />";
echo "SkidID: ".$array['SkidID']."<br />";
echo "DepartureTime: ".$array['DepartureTime']."<br />";
echo "ArrivalTime: ".$array['ArrivalTime']."<br />";

?>
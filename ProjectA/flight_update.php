<?php
include("inc/db.php");

$array = json_decode($_POST['data'], true);

print_r($array);

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

mysql_query($sql) or die ("Error in query: ".mysql_error())
?>
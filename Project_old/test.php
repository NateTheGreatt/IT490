<?php 

 
// connect to the MySQL database server 
$db = mysql_connect('localhost', 'root', 'root') or die("Connection Error: " . mysql_error()); 
 
// select the database 
mysql_select_db('project1') or die("Error connecting to db."); 
 

 
// the actual query for the grid data 
$SQL = "SELECT FlightNumber,DestinationCode,OriginCode,TailNumber,CrewID,SkidID,DepartureTime,ArrivalTime FROM flight"; 
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 
 
// we should set the appropriate header information. Do not forget this.
// header("Content-type: text/xml;charset=utf-8");
 
$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .= "<rows>";
$s .= "<page></page>";
$s .= "<total></total>";
$s .= "<records></records>";
 
// be sure to put text data in CDATA
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $s .= "<row id='". $row['FlightNumber']."'>";            
    $s .= "<cell>". $row['FlightNumber']."</cell>";
    $s .= "<cell>". $row['DestinationCode']."</cell>";
    $s .= "<cell>". $row['OriginCode']."</cell>";
    $s .= "<cell>". $row['TailNumber']."</cell>";
    $s .= "<cell>". $row['CrewID']."</cell>";
    $s .= "<cell>". $row['SkidID']."</cell>";
    $s .= "<cell>". $row['DepartureTime']."</cell>";
    $s .= "<cell><![CDATA[". $row['ArrivalTime']."]]></cell>";
    $s .= "</row>";
}
$s .= "</rows>"; 
 
echo $s;
?>
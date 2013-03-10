<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>SlickGrid example 3: Editing</title>
  <link rel="stylesheet" href="js/SlickGrid/slick.grid.css" type="text/css"/>
  <link rel="stylesheet" href="js/SlickGrid/css/smoothness/jquery-ui-1.8.16.custom.css" type="text/css"/>
  <link rel="stylesheet" href="css/examples.css" type="text/css"/>
  <style>
    .cell-title {
      font-weight: bold;
    }

    .cell-effort-driven {
      text-align: center;
    }
  </style>
</head>
<body>
<div style="position:relative">
  <div style="width:600px;">
    <div id="myGrid" style="width:100%;height:500px;"></div>
  </div>

  <div class="options-panel">
    <h2>Demonstrates:</h2>
    <ul>
      <li>adding basic keyboard navigation and editing</li>
      <li>custom editors and validators</li>
      <li>auto-edit settings</li>
    </ul>

    <h2>Options:</h2>
    <button onclick="grid.setOptions({autoEdit:true})">Auto-edit ON</button>
    &nbsp;
    <button onclick="grid.setOptions({autoEdit:false})">Auto-edit OFF</button>
  </div>
</div>

<script src="js/SlickGrid/lib/firebugx.js"></script>

<script src="js/SlickGrid/lib/jquery-1.7.min.js"></script>
<script src="js/SlickGrid/lib/jquery-ui-1.8.16.custom.min.js"></script>
<script src="js/SlickGrid/lib/jquery.event.drag-2.0.min.js"></script>

<script src="js/SlickGrid/slick.core.js"></script>
<script src="js/SlickGrid/plugins/slick.cellrangedecorator.js"></script>
<script src="js/SlickGrid/plugins/slick.cellrangeselector.js"></script>
<script src="js/SlickGrid/plugins/slick.cellselectionmodel.js"></script>
<script src="js/SlickGrid/slick.formatters.js"></script>
<script src="js/SlickGrid/slick.editors.js"></script>
<script src="js/SlickGrid/slick.grid.js"></script>
<script src="js/SlickGrid/slick.grid.js"></script>
<script src="js/json2.js"></script>

<?php

include("inc/db.php");

// This collects all of the data from the flight table and makes it into a JSON object

$flightData = "";
$i = 0;
$query = "SELECT FlightNumber,DestinationCode,OriginCode,TailNumber,CrewID,SkidID,DepartureTime,ArrivalTime FROM flight";
$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $flightData .= '
        data['.$i.'] = {
            FlightNumber: "'.$row['FlightNumber'].'",
            DestinationCode: "'.$row['DestinationCode'].'",
            OriginCode: "'.$row['OriginCode'].'",
            TailNumber: "'.$row['TailNumber'].'",
            CrewID: "'.$row['CrewID'].'",
            SkidID: "'.$row['SkidID'].'",
            DepartureTime: "'.$row['DepartureTime'].'",
            ArrivalTime: "'.$row['ArrivalTime'].'"
        };
    ';

    $i++;
}


// This collects all AirportCodes and formats them like so: "DAL, LAX, NWK, etc"

$airportOptions = "";
$i = 0;
$query = "SELECT AirportCode FROM airport";
$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $airportOptions .= $row['AirportCode'];
	if($i < $rows) {
		$airportOptions .= ",";
	}
	$i++;
}


// Collects all TailNumbers and formats them like above

$tailNumberOptions = "";
$i = 0;
$query = "SELECT TailNumber FROM aircraft";
$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $tailNumberOptions .= $row['TailNumber'];
	if($i < $rows) {
		$airportOptions .= ",";
	}
	$i++;
}


// Collects all CrewIDs

$crewIdOptions = "";
$i = 0;
$query = "SELECT CrewID FROM aircrew";
$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $crewIdOptions .= $row['CrewID'];
	if($i < $rows) {
		$airportOptions .= ",";
	}
	$i++;
}


// Collects all SkidIDs

$skidIdOptions = "";
$i = 0;
$query = "SELECT SkidID FROM cargo";
$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $skidIdOptions .= $row['SkidID'];
	if($i < $rows) {
		$airportOptions .= ",";
	}
	$i++;
}

?>

<script>
  function requiredFieldValidator(value) {
    if (value == null || value == undefined || !value.length) {
      return {valid: false, msg: "This is a required field"};
    } else {
      return {valid: true, msg: null};
    }
  }

  var grid;
  var data = [];
  var columns = [
    {id: "FlightNumber", name: "FlightNumber", field: "FlightNumber", width: 120, cssClass: "cell-title"},
    {id: "DestinationCode", name: "DestinationCode", field: "DestinationCode", width: 100, options:"<?php echo $airportOptions; ?>", editor: Slick.Editors.SelectOption},
    {id: "OriginCode", name: "OriginCode", field: "OriginCode", options:"<?php echo $airportOptions; ?>", editor: Slick.Editors.SelectOption},
    {id: "TailNumber", name: "TailNumber", field: "TailNumber", width: 80, options:"<?php echo $tailNumberOptions; ?>", editor: Slick.Editors.SelectOption},
    {id: "CrewID", name: "CrewID", field: "CrewID", minWidth: 60, options:"<?php echo $crewIdOptions; ?>", editor: Slick.Editors.SelectOption},
    {id: "SkidID", name: "SkidID", field: "SkidID", minWidth: 60, options:"<?php echo $skidIdOptions; ?>", editor: Slick.Editors.SelectOption},
    {id: "DepartureTime", name: "DepartureTime", field: "DepartureTime", width: 100, editor: Slick.Editors.Text},
    {id: "ArrivalTime", name: "ArrivalTime", field: "ArrivalTime", width: 100, editor: Slick.Editors.Text},
  ];
  var options = {
    editable: true,
    enableAddRow: true,
    enableCellNavigation: true,
    asyncEditorLoading: false,
    autoEdit: false
  };

  $(function () {
    <?php echo $flightData ?>

    grid = new Slick.Grid("#myGrid", data, columns, options);

    grid.setSelectionModel(new Slick.CellSelectionModel());

    grid.onAddNewRow.subscribe(function (e, args) {
      var item = args.item;
      grid.invalidateRow(data.length);
      data.push(item);
      grid.updateRowCount();
      grid.render();
    });
	
	grid.onCellChange.subscribe(function (e,args) {
		var json = JSON.stringify(args['item']); 
		$.ajax({
			type: "POST",
			url: "flight_update.php",
			data: "data="+json,
			dataType: 'json',
			cache: false,
			beforeSend: function(x) {
				if (x && x.overrideMimeType) {
					x.overrideMimeType("application/json;charset=UTF-8");
				}
				console.log(json);
			},
			success: function(html) {
				console.log(html);
			},
			error:function (ts) { alert(ts.responseText) }
		});
	});
	
  })
</script>
</body>
</html>

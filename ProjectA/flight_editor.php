<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Project A</title>
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
	<script src="js/SlickGrid/slick.dataview.js"></script>
	<script src="js/SlickGrid/slick.remotemodel.js"></script>
	<script src="js/json2.js"></script>
	<script src="js/jquery.jsonp.js"></script>
	<script src="js/jquery-ui-timepicker-addon.js"></script>
  
	<?php

	include("inc/db.php");

	// This collects all of the data from the flight table and makes it into a JSON object
	
	$flightData = "";
	$i = 0;
	$query = "SELECT FlightNumber,DestinationCode,OriginCode,TailNumber,CrewID,SkidID,DepartureTime,ArrivalTime FROM flight";
	if(isset($_REQUEST['searchQuery']) && $_REQUEST['searchQuery'] != '') {
		$searchQuery = $_REQUEST['searchQuery'];
		$searchItem = $_REQUEST['searchItem'];
		$query .= " WHERE $searchItem = '$searchQuery'";
	}
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
	
	$last_flight_id = $i+1;

	
	// Creates an array of all AirportCodes

	$airportOptions = array();
	$i = 0;
	$query = "SELECT AirportCode FROM airport";
	$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
	$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		array_push($airportOptions, $row['AirportCode']);
	}


	// Creates an array of all TailNumbers

	$tailNumberOptions = array();
	$i = 0;
	$query = "SELECT TailNumber FROM aircraft";
	$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
	$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		array_push($tailNumberOptions, $row['TailNumber']);
	}


	// Creates an array of all CrewIDs

	$crewIdOptions = array();
	$i = 0;
	$query = "SELECT CrewID FROM aircrew";
	$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
	$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		array_push($crewIdOptions, $row['CrewID']);
	}


	// Creates an array of all SkidIDs

	$skidIdOptions = array();
	$i = 0;
	$query = "SELECT SkidID FROM cargo";
	$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
	$rows = mysql_num_rows($result) or die($query."<br/><br/>".mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		array_push($skidIdOptions, $row['SkidID']);
	}

	?>
  
  
</head>
<body>

<div style="position:relative">
	<div style="width:800px;">
		<div class="grid-header">
			<label>Search for:</label>
			<span style="float:right;display:inline-block;">
				<form action="flight_editor.php" method="post">
					<select name="searchItem">
						<option value="TailNumber">Aircraft Tail Number</option>
						<option value="OriginCode">Origin Code</option>
						<option value="DestinationCode">Destination Code</option>
						<option value="CrewID">Crew ID</option>
						<option value="SkidID">Cargo Skid ID</option>
					</select>
					<input type="text" name="searchQuery" id="txtSearch" />
					<input type="submit" value="Search" />
				</form>
			</span>
		</div>
		<br />
		<div id="myGrid" style="width:100%;height:500px;"></div>
	</div>

	<div class="add-new-row">
		<table>
			<tr>
				<td>Destination Code</td>
				<td>Origin Code</td>
				<td>Tail Number</td>
				<td>Crew ID</td>
				<td>Skid ID</td>
				<td>Departure Time</td>
				<td>Arrival Time</td>
			</tr>
			<tr>
				<td>
					<select id="destinationcode">
						<?php foreach($airportOptions as $val) echo "<option value='".$val."'>".$val."</option>"; ?>
					</select>
				</td>
				<td>
					<select id="origincode">
						<?php foreach($airportOptions as $val) echo "<option value='".$val."'>".$val."</option>"; ?>
					</select>
				</td>
				<td>
					<select id="tailnumber">
						<?php foreach($tailNumberOptions as $val) echo "<option value='".$val."'>".$val."</option>"; ?>
					</select>
				</td>
				<td>
					<select id="crewid">
						<?php foreach($crewIdOptions as $val) echo "<option value='".$val."'>".$val."</option>"; ?>
					</select>
				</td>
				<td>
					<select id="skidid">
						<?php foreach($skidIdOptions as $val) echo "<option value='".$val."'>".$val."</option>"; ?>
					</select>
				</td>
				<td>
					<input type="text" id="departuretime" />
				</td>
				<td>
					<input type="text" id="arrivaltime" />
				</td>
			</tr>
		</table>
		<br />
		<button onclick="grid.addNewRow()">Add Row</button>
	</div>

	<div id="ajax-panel">
		<h2>Ajax Response</h2>
		<div id="ajax-response"></div>
	</div>
</div>

<script>

	$(function() {
		$('#departuretime').datetimepicker({
			showSecond: true,
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss'
		});
		$('#arrivaltime').datetimepicker({
			showSecond: true,
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss'
		});
	});

	function requiredFieldValidator(value) {
		if (value == null || value == undefined || !value.length) {
			return {valid: false, msg: "This is a required field"};
		} else {
			return {valid: true, msg: null};
		}
	}

	var grid;
	var dataView;
	var data = [];
	var columns = [
		{id: "FlightNumber", name: "FlightNumber", field: "FlightNumber", width: 120, cssClass: "cell-title"},
		{id: "DestinationCode", name: "DestinationCode", field: "DestinationCode", width: 100, options:"<?php echo implode(",",$airportOptions); ?>", editor: Slick.Editors.SelectOption},
		{id: "OriginCode", name: "OriginCode", field: "OriginCode", options:"<?php echo implode(",",$airportOptions); ?>", editor: Slick.Editors.SelectOption},
		{id: "TailNumber", name: "TailNumber", field: "TailNumber", width: 80, options:"<?php echo implode(",",$tailNumberOptions); ?>", editor: Slick.Editors.SelectOption},
		{id: "CrewID", name: "CrewID", field: "CrewID", minWidth: 60, options:"<?php echo implode(",",$crewIdOptions); ?>", editor: Slick.Editors.SelectOption},
		{id: "SkidID", name: "SkidID", field: "SkidID", minWidth: 60, options:"<?php echo implode(",",$skidIdOptions); ?>", editor: Slick.Editors.SelectOption},
		{id: "DepartureTime", name: "DepartureTime", field: "DepartureTime", width: 100, editor: Slick.Editors.Text},
		{id: "ArrivalTime", name: "ArrivalTime", field: "ArrivalTime", width: 100, editor: Slick.Editors.Text},
	];
	var options = {
		editable: true,
		enableCellNavigation: true,
		asyncEditorLoading: false,
		autoEdit: false,
		forceFitColumns: true
	};
  
  
	$(function () {
	<?php echo $flightData ?>

	dataView = new Slick.Data.DataView();
	grid = new Slick.Grid("#myGrid", data, columns, options);

	grid.setSelectionModel(new Slick.CellSelectionModel());

    grid.init();
	
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
				//console.log(json);
			},
			success: function(html) {
				//console.log(html);
				$("#ajax-response").html(html);
				
			},
			error:function (ts) { 
				//console.log(ts);
				$("#ajax-response").html(ts.responseText);
			}
		});
	});
	
	
	grid.addNewRow = function() {
		
		destination = $('#destinationcode option:selected').text();
		origin = $('#origincode option:selected').text();
		tailnumber = $('#tailnumber option:selected').text();
		crewid = $('#crewid option:selected').text();
		skidid = $('#skidid option:selected').text();
		departuretime = $('#departuretime').val();
		arrivaltime = $('#arrivaltime').val();
		
		var json = {
			DestinationCode: destination,
			OriginCode: origin,
			TailNumber: tailnumber,
			CrewID: crewid,
			SkidID: skidid,
			DepartureTime: departuretime,
			ArrivalTime: arrivaltime
		};
		
		json = JSON.stringify(json);
		$.ajax({
			type: "POST",
			url: "flight_insert.php",
			data: "data="+json,
			dataType: 'json',
			cache: false,
			beforeSend: function(x) {
				if(x && x.overrideMimeType) {
					x.overrideMimeType("application/json;charset=UTF-8");
				}
			},
			success: function(html) {
				//console.log(html);
				$("#ajax-response").html(html);
				
			},
			error:function (ts) { 
				//console.log(ts);
				$("#ajax-response").html(ts.responseText);
			}
		});
		
		var json = {
			FlightNumber: <?php echo $last_flight_id; ?>,
			DestinationCode: destination,
			OriginCode: origin,
			TailNumber: tailnumber,
			CrewID: crewid,
			SkidID: skidid,
			DepartureTime: departuretime,
			ArrivalTime: arrivaltime
		};
		
		data.push(json);
		grid.updateRowCount();
		grid.invalidate();
		grid.render();
	};
		
  })
</script>
</body>
</html>

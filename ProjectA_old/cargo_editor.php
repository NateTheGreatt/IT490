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

// This collects all of the data from the cargo table and makes it into a JSON object

$cargoData = "";
$i = 0;
$query = "SELECT SkidID,SkidWeight,SkidContents FROM cargo";
$result = mysql_query($query) or die($query."<br/><br/>".mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $cargoData .= '
        data['.$i.'] = {
	    SkidID: "'.$row['SkidID'].'",
	    SkidWeight: "'.$row['SkidWeight'].'",
	    SkidContents: "'.$row['SkidContents'].'"
        };
    ';

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
    {id: "SkidID", name: "SkidID", field: "SkidID", width: 100, editor: Slick.Editors.Text},
    {id: "SkidWeight", name: "SkidWeight", field: "SkidWeight", width: 100, editor: Slick.Editors.Text},
    {id: "SkidContents", name: "SkidContents", field: "SkidContents", width: 200, editor: Slick.Editors.Text},
  ];
  var options = {
    editable: true,
    enableAddRow: true,
    enableCellNavigation: true,
    asyncEditorLoading: false,
    autoEdit: false
  };

  $(function () {
    <?php echo $cargoData ?>

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
			url: "cargo_update.php",
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

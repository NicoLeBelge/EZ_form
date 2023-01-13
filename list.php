<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste</title>
	<style>
		table tr:hover {background-color: grey;}
	</style>
</head>
<body>
<?php
include ('../ChessMOOC/_local-connect/connect.php');
echo "<h3>Liste des enregistrements</h3>";

$qtxt="SELECT * FROM formitable;"; 
$reponse = $conn->query($qtxt);

$reponse->closeCursor();	
/* let's do it with other method */
$reponse = $conn->query($qtxt);
$event_list = $reponse->fetchAll(PDO::FETCH_ASSOC);
$event_list_str = json_encode ($event_list);
?>

<!== this is where the list will be displayed ==>


<table id="record_list" ></table>

<script type="text/javascript" src="./JS/smartTable.js"></script>
<script type="text/javascript">
	let record_list = JSON.parse(`<?=$event_list_str?>`);

	console.log (record_list);
	let EventsTableSettings = {
		"headArray" : ["id", "Prénom", "Nom"],
		"activeHeader" :"",
		"colData" : ["id", "firstname", "lastname"],
		"active" : false,
		"colSorted" : -1
	};
	/* let's add .rowLink to allow click on a row */
	record_list.forEach((element) =>{
		let dest = "view.php?id=" + element.id.toString(10);
		element.rowLink=dest;
	});
	var EventsTable = new smartTable (
		"record_list", 
		record_list,
		EventsTableSettings
	);
</script>
<a href="createform.php" > <button> NEW </button> </a>
</body>
</html>
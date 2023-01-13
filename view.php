<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche</title>
</head>
<body>
<h1>Fiche Personnelle</h1>
<?php
include ('../ChessMOOC/_local-connect/connect.php');

$id = $_GET["id"];
$qtxt="SELECT * FROM formitable WHERE id=$id;"; 
$req = $conn->query($qtxt);
$reponse = $req->fetch(PDO::FETCH_ASSOC);

// foreach($req as $item)
// {
// 	echo $item
// }
echo "<pre>";
var_dump($reponse);
echo "</pre>";



?>
<BR/>
<a href="editform.php?id=<?=$id?>" > <button> EDIT </button> </a>


</body>
</html>
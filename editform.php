<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>Document</title>
</head>
<body>
<h1> Edition </h1>
    <?php
    $classfile = "." .  DIRECTORY_SEPARATOR .'Class' . DIRECTORY_SEPARATOR . 'Smartform.php';
    require $classfile;
    $form_content_obj = json_decode(file_get_contents('form_content.json'),true);	
    $myform = new Smartform($form_content_obj);
    require "../chessMOOC/_local-connect/connect.php";
    $id = $_GET["id"];
    $qtxt = "SELECT * FROM formitable WHERE id=$id";
    $req = $conn->query($qtxt);
    $reponse = $req->fetch(PDO::FETCH_ASSOC);
    $myform->Complete_with_PDO($reponse);
    // bon, ben, ça marche, les entries contiennent les bonnes données    
    // $entrylist = $myform->entries;
    // echo "------------------------<br/><pre>";
    // var_dump($entrylist);
    // echo "</pre>";
    $myform->echo_form();
    ?>
</body>
</html>
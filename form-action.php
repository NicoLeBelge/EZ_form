<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=<, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php
    $classfile = "." .  DIRECTORY_SEPARATOR .'Class' . DIRECTORY_SEPARATOR . 'Smartform.php';
    require $classfile;
    $form_content_obj = json_decode(file_get_contents('form_content.json'),true);	
    $myform = new Smartform($form_content_obj);

    echo "<pre>"; var_dump($_POST); echo "</pre>";
    $myform->Complete_with_POST($_POST);
    if ($myform->editmode) //ici bon, ça trouve CREATE alors que ça vient de EDIT.
    {
        echo "The action page receives data from from in EDIT mode<BR/>";
        $sql_string = $myform->updateSQL();
    } else {
        echo "The action page receives data from from in CREATE mode<BR/>";
        $sql_string = $myform->insertSQL();
    }

    // echo "----------------------<BR/>";
    // echo "<pre>"; var_dump($myform);echo "</pre>";
    
    

    include "../ChessMOOC/_local-connect/connect.php";
    $req = $conn->prepare($sql_string);
    if ($req->execute()) 
    {
        echo $sql_string . " → success" ; 
    } else {
        echo $sql_string . " → FAIL !" ; 
    }


    ?>
</body>
</html>
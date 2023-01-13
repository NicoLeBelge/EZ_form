<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=<, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Ceci est un paragraphe</p>
    <?php
    $classfile = "." .  DIRECTORY_SEPARATOR .'Class' . DIRECTORY_SEPARATOR . 'Smartform.php';
    require $classfile;
    $form_content_obj = json_decode(file_get_contents('form_content.json'),true);	
    var_dump($form_content_obj);
    $myform = new Smartform($form_content_obj);
    $myform->showitems();

    
    ?>
</body>
</html>
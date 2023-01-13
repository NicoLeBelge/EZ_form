<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>Document</title>
</head>
<body>
<h1> Creation </h1>
    <?php
    $classfile = "." .  DIRECTORY_SEPARATOR .'Class' . DIRECTORY_SEPARATOR . 'Smartform.php';
    require $classfile;
    $form_content_obj = json_decode(file_get_contents('form_content.json'),true);	
    $myform = new Smartform($form_content_obj);
    $myform->echo_form();
    ?>
</body>
</html>
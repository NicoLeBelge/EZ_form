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
    $classfile = "." .  DIRECTORY_SEPARATOR .'Class' . DIRECTORY_SEPARATOR . 'Smartform_test.php';
    require $classfile;
    $form_content_obj = json_decode(file_get_contents('form_content_test.json'),true);	
    $myform = new Smartform($form_content_obj);
    
    //echo "<pre>"; var_dump($myform); echo "</pre>";

    ?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=], initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo "Public/Css/" . $controller . "/" . $controller . ".css" ?>">
    <script src="<?php echo "Public/Css/" . $controller . "/" . $controller . ".css" ?>"></script>
</head>
<body>

    <?php require_once($_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . "App/View/Pages/" . $controller . "/Body.php")  ?>
</body>
</html>
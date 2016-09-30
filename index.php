<!DOCTYPE html>
<html lang="en">
<head>
    <title>Monitoring</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style.css">
   <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 28.09.16
 * Time: 11:57
 */
session_start();

include_once "view.php";

// Проверяем, пусты ли переменные логина и id пользователя
if (empty($_SESSION['login']) or empty($_SESSION['id']))
{
    //Если пусты, то выводим форму входа.
    $html = html(0);
    echo $html;

}
else  //Иначе проверяем пользователя по базе.
{
    $sql = db();
    $query = $sql->prepare("SELECT * FROM users WHERE login = ?");
    $query->execute([$_SESSION['login']]);
    foreach ($query as $row)
    {
        $name = $row["login"];
    }
    // Если пользователь есть в базе, то мы выводим ссылку
    $html = html(1);
    echo $html;
}
?>
</body>
</html>
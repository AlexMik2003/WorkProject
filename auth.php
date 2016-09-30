<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 28.09.16
 * Time: 12:00
 */
session_start();

include_once  "view.php";

//заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == '') {
        unset($login);
    }
}

//заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
if (isset($_POST['password'])) {
    $password=$_POST['password'];
    if ($password =='')
    {
        unset($password);
    }
}

if (empty($login) or empty($password)) //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
{
    $error = error(0);
    exit($error);
}

//если логин и пароль введены,то обрабатываем их
$login = stripslashes($login);
$login = htmlspecialchars($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);
$login = trim($login);
$password = trim($password);
$password = md5($password);

//извлекаем из базы все данные о пользователе с введенным логином
$sql = db();
$query = $sql->prepare("SELECT * FROM users WHERE login = :login");
$query->bindValue(":login",$login);
$query->execute();
foreach ($query as $row)
{
    if (empty($row["password"]))
    {
        //если пользователя с введенным логином не существует
        $error = error(1);
        exit($error);
    }
    else {
        //если существует, то сверяем пароли
        if ($row["password"]==$password) {
            //если пароли совпадают, то запускаем пользователю сессию
            $_SESSION['login']=$row["login"];
            $_SESSION['id']=$row["id"];
            header("Location:index.php");
        }
        else {
            //если пароли не совпали
            $error = error(1);
            exit($error);
        }
    }
}


<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 29.09.16
 * Time: 13:11
 */

//Подключение к базе
function db()
{
    $host = "localhost";
    $dbname = "monitoring";
    $user = "root";
    $password = "dfvgbh99";

    $db = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $sql = new PDO($db, $user, $password, $opt);
    return $sql;
}

//Функция вывода инофрмации на странице в зависимости от ввода пользователя
function html($html)
{
    $sql = db();

    $view = "";
    switch ($html) {
        case 0:  //Форма входа на сайт
            $view .= '
            <div class="auth">
            <form action="auth.php" method="post">
                <table>
                    <tr>
                        <td><label>Login: </label></td>
                        <!--<td><input type="text" id="login" name="login" size="15" maxlength="15" placeholder="Enter your name..."></td>-->
                         <td>
                        <select id="login" name="login">
                        <option disabled selected>Ваш логин:</option>';
                        $query = $sql->prepare("SELECT * FROM users");
                        $query->execute();
                        foreach ($query as $row) {
                            $view .= "<option>" . $row['login'] . "</option>";
                        }
                    $view .= '</select>
                    </td>
                    </tr>
                    <tr>
                        <td><label>Password: </label></td>
                        <td><input type="password" id="password" name="password" size="15" maxlength="15"></td>
                    </tr>
                    <tr>
                        <td id="enter" colspan="2"><input type="submit" value="Войти" ></td>
                    </tr>
                </table>
            </form>
         </div>';
            break;
        case 1:  //Форма ввода и выбора информации
            $view .= '
              <div class="point">
               <p id="back"><a href="exit.php">Выйти</a></p>
                <form action="" method="post">
                <table>
                    <tr>
                        <td colspan="2" id="select">
                        <select name="point">
                        <option disabled selected>Выберите название точки:</option>';
            $query = $sql->prepare("SELECT * FROM point");
            $query->execute();
            foreach ($query as $row) {
                $view .= "<option>" . $row['point_name'] . "</option>";
            }
            $view .= '</select>
                    </td>
                    </tr>';

            $query = $sql->prepare("SELECT * FROM tovar");
            $query->execute();
            foreach ($query as $row) {
                $view .= "
                        <tr>
                    <td id='label'><label>" . $row['tovar'] . "</label></td>
                    <td><input type='text' name='" . $row["id"] . "' id='price' class='price' size='15' maxlength='15' placeholder='12345.67' pattern='^\d+\.?(\d{1,2})?$\'></td>
                    <!-- Скрипт проверки введенной цены-->
                              <script> 
                var flag = false;
                $('.price').on({
                    input: function(e){
                        var re =/^\d+\.?(\d{1,2})?$/ig,
                            cVal = $.trim($(this).val());
                        $(this).val(cVal.replace(/,/g, '.'));
                        if(flag){
                            var cut = cVal.match(/^\d+\.?(\d{1,2})?/ig),
                                clearVal =  cut !== null ? cut : '';
                            $(this).val(clearVal);
                            return false;
                        }
                        if(!re.test(cVal)){
                            $(this).val(cVal.substr(0,cVal.length-1));
                        }
                    },
                    paste: function(){
                        flag = true;
                    },
                    blur: function(){
                        var cVal = $.trim($(this).val());
                        if(/\.$/.test(cVal)){
                            $(this).val(cVal.substr(0,cVal.length-1));
                        }
                    }
                });
            </script>
                   </tr>";
            }
            $view .= '<tr><td colspan="2"><input type="hidden" id="login" name="login" value="'.$_SESSION["login"].'"></td></tr>
                 <tr>
                <td id="enter" colspan="2"><input id="point" type="submit" value="Сохранить">
                </td>
                </tr>
                </table>                
                </form>';
            if(isset($_POST["point"])) //Запись данных в БД
            {
                $data = date("Y-m-d H:i:s");
                $array = $_REQUEST;
                $point = $array["point"];
                $query_point = $sql->prepare("SELECT id FROM point WHERE point_name='$point'");
                $query_point->execute();
                $point_id = $query_point->fetchColumn(0);
                $login = $array["login"];
                $query_user = $sql->prepare("SELECT id FROM users WHERE login='$login'");
                $query_user->execute();
                $user_id = $query_user->fetchColumn(0);
                unset($array["point"]);
                unset($array["login"]);
                for($i=0;$i<count($array);$i++)
                {
                    $ins = $sql->prepare("INSERT INTO price (price,data,id_tovar,id_point,id_user) VALUES (:price,:data,:id_tovar,:id_point,:id_user)");
                    $ins->bindParam(":price",$array[$i+1]);
                    $ins->bindParam(":data",$data);
                    $t = $i++;
                    $ins->bindParam(":id_tovar",$t);
                    $ins->bindParam(":id_point",$point_id);
                    $ins->bindParam(":id_user",$user_id);
                    $ins->execute();

                }
            }
            break;
    }

    return $view;
}


//Вывод страницы ошибок
function error($error)
{
    $err ='<!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Monitoring</title>
            <meta charset="utf-8">
            <link rel="stylesheet" type="text/css" href="style.css">
            <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        </head>
        <body>';

    switch ($error)
    {
        case 0:
            $err .= '
            <div class="error">
                <h3>Вы ввели не всю информацию, вернитесь назад и заполните все поля!
                    <p id="back"><a href="index.php">Назад</a></p></h3>   
            </div>';
            break;
        case 1:
            $err .= '
            <div class="error">
                <h3><i>LOGIN</i> или <i>PASSWORD</i> неверный!
                <p id="back"><a href="index.php">Назад</a></p></h3>   
            </div>';
            break;
    }

    $err .= '
    </body>
        </html>';
    return $err;
}
<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 28.09.16
 * Time: 12:01
 */
session_start();
session_destroy();
header("Location:index.php");
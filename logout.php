<?php

session_start();

unset($_SESSION['login']);
unset($_SESSION['usuarios']);
unset($_SESSION['nome']);
unset($_SESSION['tipo']);
unset($_SESSION['local']);

header('location:index.php');
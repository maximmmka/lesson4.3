<?php
require'connect.php';
require 'functions.php';
session_start();
if (!empty($_POST['doSignUp'])){
    doSignUp($pdo,$_POST['login'],($_POST['pass']));
}

if (!empty($_POST['checkUser'])){
  $userExist = checkUser($pdo,$_POST['login'],($_POST['pass']));
  if($userExist){
    $_SESSION['user'] = $userExist;
    echo'<meta http-equiv="refresh" content="0; url=index.php">';
    die;
  }
  echo $userExist;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>вход</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<form id="loginForm" action="login.php" method="post">

		<div class="field">		
	    <div class="input"><input class="input" placeholder="Логин" type="text" name="login" value="" id="login" required="required"></div>
		</div>

		<div class="field">
			<div class="input"><input class="input" placeholder="Пароль" type="password" name="pass" value="" id="pass" required="required"></div>
		</div>

		<div class="submit">
	    <button class="send" type="submit" name="checkUser" value="DO!">Вход</button>
	    <button class="send" type="submit" name="doSignUp" value="DO!">Добавить пользователя</button>
		</div>

	</form>
</body>
</html>
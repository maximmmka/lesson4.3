<?php

	$host = 'localhost';
	$user = 'root';
	$pass = '';
	$useDB = 'tasks';
	$taskTable = 'task';
    $userTable = 'user';
	
	$pdo = new PDO("mysql:$host;dbname=$useDB","$user","$pass");
   
    
    

<?php
session_start();
$SUID = $_SESSION['user']['id'];
define (SUID , $_SESSION['user']['id']);

function needAuth(){
    if(!isset($_SESSION['user'])){
        header('HTTP/1.1 403 Need authentification');
        echo'<meta http-equiv="refresh" content="0; url=login.php">';
        die;
    }
}

function create($pdo,$task){
    global $useDB,$taskTable,$SUID;  
       $pdo->query("INSERT INTO $useDB.$taskTable( user_id,assigned_user_id,description, is_done, date_added)"
        . " VALUES ('$SUID',NULL,'$task','0',NOW())");   
    reload();

}
function delete($pdo,$id){
    global $useDB,$taskTable;
    $pdo->query("DELETE FROM $useDB.$taskTable WHERE id=$id");
    reload();
}

function done($pdo,$id){
    global $useDB,$taskTable;
    $pdo->query("UPDATE $useDB.$taskTable SET `is_done`= 1 WHERE id=$id");
    reload();
}

function changeTask($pdo,$id,$newValue){
    global $useDB,$taskTable;
    $newVal = $pdo->prepare("UPDATE $useDB.$taskTable SET description = :NW WHERE id=$id");
    $newVal->execute(array(':NW'=>$newValue));
    reload();
}

function doSignUp($pdo,$login,$pass){
    global $useDB,$userTable;   
    $newUser = $pdo->prepare("INSERT INTO $useDB.$userTable(login,password) VALUES(:login,:pass)");
    $newUser->execute(array(':login'=>trim($login),':pass'=>trim(password_hash($pass,PASSWORD_DEFAULT))));
    echo'Пользователь "'.$_POST['login'].'" добавлен.';    
}

function checkUser($pdo,$login,$pass){
    global $useDB,$userTable;
    $res = $pdo->query("SELECT id_user,login,password FROM $useDB.$userTable");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    if(($login===$row['login'])&&(password_verify($pass, $row['password']))){
        return array('login'=>$login,'id'=>$row['id_user']);
    }
}
 echo 'Не верный логин или пароль.';    
}

function transmit($pdo,$idUser,$idTask){
    global $useDB,$taskTable;
    $pdo->query("UPDATE $taskTable SET assigned_user_id =$idUser WHERE id = $idTask");
    reload();
}

function reload(){
     echo'<meta http-equiv="refresh" content="2;URL=/">';
}
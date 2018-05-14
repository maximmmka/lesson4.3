<?php
  require'connect.php';
  require 'functions.php';
  needAuth();
       
  $utf8 = $pdo->query("SET NAMES 'utf8';");
  $use = $pdo->query("USE $useDB");       
                
  if(isset($_GET['task'])&&!empty($_GET['task'])){
    $task = $_GET['task'];
  }else{
    if(isset($_GET['task'])&&empty($_GET['task'])){
      $message = '<span class="default alert">Задана пустая задача</span>';
    }
  }

  $sort = isset($_GET['selectSort'])  ? $_GET['selectSort']  : 'id';
  $allowed = array("id", "date_added", "description","is_done");
  $key = array_search($sort,$allowed);
  $orderBy=$allowed[$key];
                
  if(isset($_GET['action'])&&(($_GET['action'])=='delete')){
    delete($pdo,intval($_GET['id']));
  }
                
  if(isset($_GET['action'])&&(($_GET['action'])=='done')){
    done($pdo,intval($_GET['id']));
  }
                
  if (isset($_POST['changeTask'])&&isset($_POST['newValue'])){
    changeTask($pdo,intval($_POST['id']),$_POST['newValue']);
  }
                
  if (isset($_POST['transmit']) && !empty($_POST['selectNewUser']) ){
    transmit($pdo,intval($_POST['selectNewUser']),intval($_POST['taskId']));
  }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>cписок дел</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body> 
  <h1 class="headers">Добро пожаловать, <?= $_SESSION['user']['login'] ?></h1>

  <h2 class="headers">Список ваших дел</h2>
        
  <?php
    if(isset($_GET['action'])&&($_GET['action'])=='change'){
      $val = read($pdo,intval($_GET['id']));
      $id = intval($_GET['id']);
  ?>

  <form action="" method="POST">
    <input type="text" name="newValue" placeholder="Выберите задание" value="<?php echo $val;?>">
    <input type="submit" name="changeTask" value="Изменить задачу">
    <input type="hidden" name="id" value="<?php echo $id;?>">
  </form>
  <?php
    }else{?>
      <form action="">         
        <input class="input" type="text" name="task" placeholder="Описание задачи">
        <input class="send" type="submit" name="create" value="Добавить">
      </form>
  <?php }?>          
             
  <table class="table">
    <thead class="headers">
      <tr>
        <td class="tableHeaders"> Описание задачи </td>
        <td class="tableHeaders"> Дата добавления </td>
        <td class="tableHeaders"> Статус </td>
        <td class="tableHeaders"> Опции </td>
        <td class="tableHeaders"> Передать задачу</td>
      </tr>
    </thead>
    
    <tbody>
    <?php
      global $useDB,$taskTable,$SUID;
      $showAll = $pdo->query("SELECT * FROM $useDB.$taskTable WHERE user_id= $SUID ORDER BY $orderBy");

      foreach( $showAll  as  $tasks ){                    
        if($tasks['is_done']==0){
          $isDone = '<span class=inProgress> В процессе </span>';
        }else{
          if($tasks['is_done']==1){
            $isDone = '<span class=made> Выполнено </span>';
          }
        }
                
        if($task==$tasks['description']){
          $exist = 1;
        }
         
        if($task==$tasks['description']){
          $value = 1;
        }
    ?>
    <tr>
     <td class="slot"><?php echo $tasks['description']?></td>
     <td class=slot><?php echo $tasks['date_added']; ?></td>
     <td class=slot><?php echo $isDone; ?></td>                  
     <td class=slot>
       <a class="links made" href="/?action=done&id=<?php echo $tasks['id'];?>"> Выполнить </a>
       <a class="links delite" href="/?action=delete&id=<?php echo $tasks['id'];?>"> Удалить </a>
     </td>
     <td class="slot">    
       <form action="/" method="POST">
         <select name="selectNewUser"><option value="">Выберите пользователя</option>
           <?php
             $u = $pdo->query("SELECT id_user,login FROM $useDB.$userTable");
              while ($user = $u->fetch(PDO::FETCH_ASSOC)){
               echo'<option value="'.$user['id_user'].'">'.$user['login'].'</option>';
              }
            ?>
          <input type="hidden" name="taskId" value="<?php echo $tasks['id'];?>">
          <input class="made links" type="submit" name="transmit" value="Передать">
        </form></td></tr>
       <?php } ?>
    
       <?php 
        if(($exist == 0) &&(!empty($task))){
          create($pdo,$task);           
        }
       ?>
      </tbody>           
            
</table>
  <div class="wapper"> 
    <h2 class="headers">Список переданных вам дел</h2>
  </div>
  
<table class="table">
  <thead class="headers">
<tr>
  <td class="tableHeaders"> Описание задачи </td>
  <td class="tableHeaders"> Добавлена пользователем </td>
  <td class="tableHeaders"> Дата добавления </td>
  <td class="tableHeaders"> Статус </td>
  <td class="tableHeaders"> Опции </td>
</tr>
 </thead>
   <tbody>
        <?php
        global $useDB,$taskTable,$SUID;
        $showAll = $pdo->query("SELECT * FROM $useDB.$taskTable JOIN $useDB.$userTable "
          . "ON assigned_user_id = $userTable.id_user AND assigned_user_id = $SUID ORDER BY $taskTable.$orderBy ");
        
         while ($tasks = $showAll->fetch(PDO::FETCH_ASSOC)){                  
            if($tasks['is_done']==0){
              $isDone = '<span class=inProgress> В процессе </span>';
            }else{if($tasks['is_done']==1){
              $isDone = '<span class=made> Выполнено </span>';
            }
         }
        $u = $tasks['user_id'];   
        $creator = $pdo->query("SELECT login FROM $useDB.$userTable WHERE id_user = $u");
          while($row = $creator->fetch(PDO::FETCH_ASSOC)){
            $cre = $row['login'];

            if($task==$tasks['description']){
            $value = 1;
            }
            echo
            '<tr>             
              <td class=slot>'.$tasks['description'].'</td>
              <td class=slot>'.$cre.'</td>
              <td class=slot>'.$tasks['date_added'].'</td>
              <td class=slot>'.$isDone.'</td>
              <td class=slot>
                <a class=links href="/?action=done&id='.$tasks['id'].'"><span class=made> Выполнить </span></a>
                <a class="links delite" href="/?action=delete&id='.$tasks['id'].'"> Удалить </a>
              </td>
            </tr>';
          }        
         }      
       ?>                 
     </tbody>
   </table>
  
    <div class="wapper">
      <a class="send" href="logout.php">Выйти</a>   
    </div>    
  </body>
</html>

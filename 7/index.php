<?php
 function filter_input_data($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
  }
  function filter_output_data($output){
      return htmlentities($output, ENT_QUOTES, 'UTF-8');
  }
  
  session_start();
  if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  $token = $_SESSION['csrf_token'];
  header('Content-Type: text/html; charset=UTF-8');
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();
    if (!empty($_COOKIE['save'])) {
      setcookie('save', '', 100000);
      setcookie('login', '', 100000);
      setcookie('pass', '', 100000);
      $messages[] = 'Спасибо, результаты сохранены.';
  
      if (!empty($_COOKIE['pass'])) {
        $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
          и паролем <strong>%s</strong> для изменения данных.',
          strip_tags($_COOKIE['login']),
          strip_tags($_COOKIE['pass']));
    }
    }
  $errors = array();
  $errors['name'] = !empty($_COOKIE['name_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['year'] = !empty($_COOKIE['year_error']);
  $errors['pol'] = !empty($_COOKIE['pol_error']);
  $errors['limb'] = !empty($_COOKIE['limb_error']);
  $errors['ability'] = !empty($_COOKIE['ability_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['check'] = !empty($_COOKIE['check_error']);

  if ($errors['name']) {
    setcookie('name_error', '', 100000);
    $messages[] = '<div class="error">введите имя.</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">введите email.</div>';
  }
  if ($errors['year']) {
    setcookie('year_error', '', 100000);
    $messages[] = '<div class="error">введите год.</div>';
  }
  if ($errors['pol']) {
    setcookie('pol_error', '', 100000);
    $messages[] = '<div class="error">введите пол.</div>';
  }
  if ($errors['limb']) {
    setcookie('limb_error', '', 100000);
    $messages[] = '<div class="error">введите кол-во конечностей.</div>';
  }
  if ($errors['ability']) {
    setcookie('ability_error', '', 100000);
    $messages[] = '<div class="error">введите суперспособность.</div>';
  }
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    $messages[] = '<div class="error">введите биографию.</div>';
  }
        if ($errors['check']) {
    setcookie('check_error', '', 100000);
    $messages[] = '<div class="error">Ознакомьтесь с соглашением.</div>';
  }

  $values = array();
  $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['year'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
  $values['pol'] = empty($_COOKIE['pol_value']) ? '' : $_COOKIE['pol_value'];
  $values['limb'] = empty($_COOKIE['limb_value']) ? '' : $_COOKIE['limb_value'];
  $values['ability'] = empty($_COOKIE['ability_value']) ? array() : json_decode($_COOKIE['ability_value']);
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['check'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];

  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      !empty($_SESSION['login'])) {
    $user = 'u52828';
    $pass = '9210682';
    $db = new PDO('mysql:host=localhost;dbname=u52828', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    try{
      $get=$db->prepare("select * from form where id=?");
      $get->bindParam(1,$_SESSION['uid']);
      $get->execute();
      $inf=$get->fetchALL();
      $values['name']=$inf[0]['name'];
      $values['email']=$inf[0]['email'];
      $values['year']=$inf[0]['year'];
      $values['pol']=$inf[0]['pol'];
      $values['limb']=$inf[0]['limbs'];
      $values['bio']=$inf[0]['bio'];

      $get2=$db->prepare("select name_id from super where per_id=?");
      $get2->bindParam(1,$_SESSION['uid']);
      $get2->execute();
      $inf2=$get2->fetchALL();
      for($i=0;$i<count($inf2);$i++){
        if($inf2[$i]['name_id']=='1'){
          $values['1']=1;
        }
        if($inf2[$i]['name_id']=='2'){
          $values['2']=1;
        }
        if($inf2[$i]['name_id']=='3'){
          $values['3']=1;
        }
		if($inf2[$i]['name_id']=='4'){
          $values['4']=1;
        }
      }
    }
    catch(PDOException $e){
      print('Error: '.$e->getMessage());
      exit();
    }
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }
  if (file_exists('form.php')) {
    include('form.php');}
}

else{
    if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) !== 'u52828.kubsu-dev.ru') {
        die('Invalid referer');
      }
  if(!empty($_POST['logout'])){
    session_destroy();
    header('Location: index.php');
  }
  else{
    if ($_SESSION['csrf_token'] !== $_POST['token']) {
        die('Invalid CSRF token');
      }
    $regex_name='/[a-z,A-Z,а-я,А-Я,-]*$/';
    $regex_email='/[a-z]+\w*@[a-z]+\.[a-z]{2,4}$/';
	
$errors = FALSE;
if (empty($_POST['name']) or !preg_match($regex_name,$_POST['name'])) {
  setcookie('name_error', '1', time() + 24 * 60 * 60);
  setcookie('name_value', '', 100000);
  $errors = TRUE;
}
else {
  setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  setcookie('name_error','',100000);
}

if (empty($_POST['email']) || !preg_match($regex_email, $_POST['email'])) {
  setcookie('email_error', '1', time() + 24 * 60 * 60);
  setcookie('email_value', '', 100000);
  $errors = TRUE;
}
else {
  setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  setcookie('email_error','',100000);
}

if (empty($_POST['year']) || !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
  setcookie('year_error', '1', time() + 24 * 60 * 60);
  setcookie('year_value', '', 100000);
  $errors = TRUE;
}
else {
  setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
  setcookie('year_error','',100000);
}

if (empty($_POST['pol']) || ($_POST['pol']!='m' && $_POST['pol']!='f')) {
  setcookie('pol_error', '1', time() + 24 * 60 * 60);
  setcookie('pol_value', '', 100000);
  $errors = TRUE;
}
else {
  setcookie('pol_value', $_POST['pol'], time() + 30 * 24 * 60 * 60);
  setcookie('pol_error','',100000);
}
if (empty($_POST['limb']) || ($_POST['limb']!='1' && $_POST['limb']!='2' && $_POST['limb']!='3')) {
   setcookie('limb_error', '1', time() + 24 * 60 * 60);
   setcookie('limb_value', '', 100000);
   $errors = TRUE;
}
else {
  setcookie('limb_value', $_POST['limb'], time() + 30 * 24 * 60 * 60);
  setcookie('limb_error','',100000);
}

foreach ($_POST['ability'] as $ability) {
  if (!is_numeric($ability) || !in_array($ability, [1, 2, 3, 4])) {
    setcookie('ability_error', '1', time() + 24 * 60 * 60);
	setcookie('ability_value', '', 100000);
    $errors = TRUE;
    break;
  }
}
if (!empty($_POST['ability'])) {
  setcookie('ability_value', json_encode($_POST['ability']), time() + 24 * 60 * 60);
  setcookie('ability_error', '', time() + 24 * 60 * 60);
}

if (empty($_POST['bio']) || !preg_match('/^[0-9A-Za-z0-9А-Яа-я,\.\s]+$/', $_POST['bio'])) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
	setcookie('bio_value', '', time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
}
else {
  setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
  setcookie('bio_error', '', time() + 24 * 60 * 60);
}

if (!isset($_POST['check'])) {
    setcookie('check_error', '1', time() + 24 * 60 * 60);
	setcookie('check_value', '', time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
}
else {
  setcookie('check_value', $_POST['check'], time() + 30 * 24 * 60 * 60);
    setcookie('check_error', '', time() + 24 * 60 * 60);
}

if ($errors) {
	setcookie('save','',100000);
    header('Location: login.php');
}
    else {
      setcookie('name_error', '', 100000);
      setcookie('email_error', '', 100000);
      setcookie('year_error', '', 100000);
      setcookie('pol_error', '', 100000);
      setcookie('limb_error', '', 100000);
      setcookie('ability_error', '', 100000);
	  	  setcookie('check_error', '', 100000);
    }
	
	$user = 'u52828';
    $pass = '9210682';
    $db = new PDO('mysql:host=localhost;dbname=u52828', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login']) and !$errors) {
    $app_id=$_SESSION['uid'];
    $upd=$db->prepare("update form set name=?,email=?,year=?,pol=?,limbs=?,bio=? where id=?");
    $upd->execute(array(filter_input_data($_POST['name']),filter_input_data($_POST['email']),filter_input_data($_POST['year']),filter_input_data($_POST['pol']),filter_input_data($_POST['limb']),filter_input_data($_POST['bio']),$app_id));
    $del=$db->prepare("delete from super where per_id=?");
    $del->execute(array($app_id));
	  $stmt = $db->prepare("INSERT INTO super SET per_id = ?, name_id=?");
	  foreach ($_POST['ability'] as $ability) {
		$stmt->execute([$app_id,$ability ]);
	  }
  }
  else {
    if(!$errors){
      $login = 'N'.substr(uniqid(),-6);
      $password = substr(md5(uniqid()),0,15);
      $hashed=password_hash($password,PASSWORD_DEFAULT);
      print($hashed);
      setcookie('login', $login);
      setcookie('pass', $password);
      try {
        $stmt = $db->prepare("INSERT INTO form SET name=?,email=?,year=?,pol=?,limbs=?,bio=?");
        $stmt -> execute(array(filter_input_data($_POST['name']),filter_input_data($_POST['email']),filter_input_data($_POST['year']),filter_input_data($_POST['pol']),filter_input_data($_POST['limb']),filter_input_data($_POST['bio'])));
        $app_id=$db->lastInsertId();
        //$pwr=$db->prepare("INSERT INTO super SET name_id=?,per_id=?");
        //foreach($pwrs as $power){ 
        //  $pwr->execute(array($power,$id));
        //}
		  $stmt = $db->prepare("INSERT INTO super SET per_id = ?, name_id=?");

  foreach ($_POST['ability'] as $ability) {
    $stmt->execute([$app_id,filter_input_data($ability)]);
  }
        $usr=$db->prepare("insert into users set per_id=?,login=?,passwrld=?");
        $usr->execute(array($app_id,filter_input_data($login),filter_input_data($hashed)));
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    }
  }
    if(!$errors){
      setcookie('save', '1');
    }
    header('Location: ./');
  }

}
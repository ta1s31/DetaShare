<?php

require_once "./lib/db.php";
require_once "./lib/functions.php";

function insertDeta($pdo, $params){
    //データ追加
    $sql = "INSERT INTO deta (uploadTime, machine, name, memo, path) VALUES (:uploadTime, :machine, :name, :memo, :path)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header('Content-Type: text/plain; charset=UTF-8', true, 200);
    header('Location: ./index.php?state=created');
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $error = NULL;

    $uploadTime = date('Y-m-d H:i:s');
    $machine = '';
    $name = $_POST['name'];
    $memo = $_POST['memo'];
    $file_path = '#';
    $allowext = ['jpeg', 'jpg', 'png', 'HEIF', 'gif', 'mov', 'mp4', 'mp3', 'aac', 'avi', 'txt', 'zip', ];

    //マシン判定
    preg_match('/Mozilla\/5\.0 \((.*); .*\) /',  $_SERVER['HTTP_USER_AGENT'], $m);
    $machine = $m[1];

    //text関係処理
    if($name === ''){
        $error[] = 'タイトルがありません';
    }

    //ファイル処理
    if( is_uploaded_file($_FILES['file_deta']['tmp_name']) ){
        //保存先
        $filename = $_FILES['file_deta']['name'];
        if(isallowExt($filename, $allowext)){
            $encpath = "./deta/".updateRandomString($filename).'.'.substr($filename, strrpos($filename, '.') + 1);
        }else{
            $encpath = "./deta/".updateRandomString($filename).'.txt';
        }
       
        if(move_uploaded_file($_FILES['file_deta']['tmp_name'], $encpath)){
            //正常
            $file_path = $encpath;
        }else{
            $error[] = "保存に失敗しました。";
            
        }
    }
    
    //エラーがなければデータ追加
    if(!isset($error)){
        insertDeta($pdo, array(':uploadTime' => $uploadTime, ':machine' => $machine, ':name' => $name, ':memo' => $memo, ':path' => $file_path));
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Deta Share</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    <h1>データを追加</h1>
    <?php if(isset($error)) : ?>
        <?php foreach($error as $e) { ?>
            <p><?php echo $e ?> </p>
        <?php } ?>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="タイトル">
        <input type="text" name="memo" placeholder="コメント, メモ">
        <input type="file" name="file_deta">
        <input type="submit" value="regist">
    </form>
</body>
</html>
<?php

require_once './lib/db.php';
require_once "./lib/functions.php";

// -- detabase -> id, uploadTime, name, path, memo, machine --

//sqlですべてのデータを取得する
$machineSql = "SELECT * FROM deta";
$machinestmt = $pdo->prepare($machineSql);
$machinestmt->execute();

$result = $machinestmt->fetchAll();

$machines = array();
foreach ( $result as $machine ) {
    $machine_address = $machine['machine'];
    $deta_id = $machine['id'];
    $machines[$machine_address][$deta_id] = $machine;
}

$message = NULL;
if( $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['state']) ){
    $state = $_GET['state'];
    if(isset($_SERVER['HTTP_REFERER'])){
        $referer = $_SERVER['HTTP_REFERER'];
        if( preg_match('/delete\.php/', $referer) && $state === 'deleted' ){
            $message = "正常に削除されました";
        }elseif( preg_match('/new\.php/', $referer) && $state === 'created' ){
            $message = "正常に追加されました";
        }else{
            $message = NULL;
        }
    }
}

// new deta
    function insertDeta($pdo, $params){
        //データ追加
        $sql = "INSERT INTO deta (uploadTime, machine, name, memo, path) VALUES (:uploadTime, :machine, :name, :memo, :path)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header('Content-Type: text/plain; charset=UTF-8', true, 200);
        header('Location: ./index.php');
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

        //メモがurlならリンクにする
        if(is_valid_url($memo)){
            $file_path = $memo;
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
    <link rel="stylesheet" type="text/css" media="screen" href="./assets/stylesheet/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="./assets/stylesheet/index.css" />
    
</head>
<body>
    <div class="ds_header">
        <h1 class="header_title">Deta Share</h1>
        <!-- <p id="addDeta" onclick="location.href='./new.php'">データを追加</p> -->
        <p id="addDeta">データを追加</p>
    </div>
    <div class="line"></div>
    <?php if(isset($message)) : ?>
        <p class="notice"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php foreach($machines as $machine_address => $machineName) { ?>
        <h2 class="machine"><?php echo h($machine_address); ?> </h2>
        <div class="line"></div>
        <div class="card_list">
            <?php foreach($machineName as $row) { ?>
                <div class="card">
                    <div class="card_content" onclick="location.href='<?php echo h($row['path']);?>';">
                        <p class="file_name"> <?php echo h($row['name']); ?> </p>
                        <div class="subline"></div>
                        <p class="shareText"> <?php echo h($row['memo']); ?> </p>
                        <p class="uploadTime"> <?php echo h($row['uploadTime']); ?> </p>
                    </div>
                    <p class="deletebtn" onclick="execPost('./delete.php', {'id':'<?php echo $row['id'];?>'});">削除</p>
                </div>
            <?php } ?>
        </div>
        <div class="line"></div>
    <?php } ?>

    <div class="new_deta_form">
        <div class="new_deta_form_text">
            <h1>データを追加</h1>
            <div class="line"></div>
            <?php if(isset($error)) : ?>
                <?php foreach($error as $e) { ?>
                    <p class="ds_errors"><?php echo $e ?> </p>
                <?php } ?>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="タイトル"><br>
                <input type="text" name="memo" placeholder="コメント, メモ"><br>
                <input type="file" name="file_deta"><br>
                <input type="submit" value="追加"><br>
            </form>
        </div>
    </div>
    
    <script src="./assets/js/functions.js"></script>
    <script src="./assets/js/addDeta.js"></script>
</body>
</html>
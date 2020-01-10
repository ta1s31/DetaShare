<?php

require_once "./lib/db.php";
require_once "./lib/functions.php";

session_start();

function insertDeta($pdo, $params){
    //データ追加
    $sql = "INSERT INTO deta (uploadTime, machine, name, memo, path, filetype ) VALUES (:uploadTime, :machine, :name, :memo, :path, :filetype )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $error = NULL;

    $uploadTime = date('Y-m-d H:i:s');
    $machine = '';
    $name = $_POST['name'];
    $memo = $_POST['memo'];
    $file_path = '#';
    $filetype = '';
    $allowext = ['jpeg', 'jpg', 'png', 'heif', 'gif', 'mov', 'mp4', 'mp3', 'aac', 'avi', 'txt', 'zip', 'pdf'];

    //マシン判定
    preg_match('/Mozilla\/5\.0 \((.*); .*\) /',  $_SERVER['HTTP_USER_AGENT'], $m);
    $machine = $m[1];

    //text関係処理
    if($name === ''){
        $error[] = 'no title';
    }

    //メモがurlならリンクにする
    if(is_valid_url($memo)){
        $file_path = $memo;
    }

    //ファイル処理
    try {
        if( is_uploaded_file($_FILES['file_deta']['tmp_name']) ){
            //保存先
            $filename = mb_strtolower( $_FILES['file_deta']['name'] , 'utf-8' );  //拡張子確認のため全て小文字に

            if(isallowExt($filename, $allowext)){
                $encpath = "./deta/".updateRandomString($filename).'.'.substr($filename, strrpos($filename, '.') + 1);
            }else{
                $encpath = "./deta/".updateRandomString($filename).'-'.substr($filename, strrpos($filename, '.') + 1).'.txt';
            }
        
            if(move_uploaded_file($_FILES['file_deta']['tmp_name'], $encpath)){
                //正常
                $file_path = $encpath;
                $filetype = $_FILES['file_deta']['type'];
            }else{
                $error[] = "保存に失敗しました。";
                
            }
        }
    }catch(RuntimeException $e){
        $e->getMessage();
    }
    
    //エラーがなければデータ追加
    if(!isset($error)){
        $_SESSION['addDetaStatus'] = '正常に作成されました。';
        insertDeta($pdo, array(':uploadTime' => $uploadTime, ':machine' => $machine, ':name' => $name, ':memo' => $memo, ':path' => $file_path, ':filetype' => $filetype));
        header('Content-Type: text/plain; charset=UTF-8', true, 200);
        header('Location: ./index.php');
        exit();
    }else{
        $_SESSION['validateError'] = 'タイトルがありません。';
        header('Content-Type: text/plain; charset=UTF-8', true, 200);
        header('Location: ./index.php');
        exit();
    }
}

?>
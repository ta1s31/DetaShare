<?php
    require_once "./lib/db.php";

    session_start();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_POST['id'];
        $params = array(':id' => $id);
        //fileが存在した場合fileを削除
        $fileSql = "SELECT * FROM deta WHERE id = :id";
        $filestmt = $pdo->prepare($fileSql);
        $filestmt->execute($params);

        $result = $filestmt->fetch();
        if($result['path'] !== '#') {
            unlink( $result['path'] );
        }
        //dbから削除
        $sql = "DELETE FROM deta WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $_SESSION['addDetaStatus'] = '正常に削除されました';

        header('Content-Type: text/plain; charset=UTF-8', true, 200);
        header('Location: ./index.php');
        exit();
    }
?>
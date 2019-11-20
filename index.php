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
    <script src="./assets/js/functions.js"></script>
</head>
<body>
    <div class="ds_header">
        <h1 class="header_title">Deta Share</h1>
        <p id="addDeta" onclick="location.href='./new.php'">データを追加</p>
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
    
    
</body>
</html>
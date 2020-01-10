<?php

require_once './lib/db.php';
require_once "./lib/functions.php";

session_start();

// -- detabase -> id, uploadTime, name, path, memo, machine --

//sqlですべてのデータを取得する
$machineSql = "SELECT * FROM deta";
$machinestmt = $pdo->prepare($machineSql);
$machinestmt->execute();

$result = $machinestmt->fetchAll();

$machines = array();
foreach ( $result as $machine ) {
    $machine_address = $machine['machine']; //UA
    $deta_id = $machine['id'];
    $machines[$machine_address][$deta_id] = $machine;
}

$message = NULL;
if( $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['addDetaStatus']) ){
    $message = $_SESSION['addDetaStatus'];
    unset($_SESSION['addDetaStatus']);
}

$validateError = NULL;
if( $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['validateError']) ){
    $validateError = $_SESSION['validateError'];
    unset($_SESSION['validateError']);
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
        <p id="addDeta" onclick="toggleNewDetaForm();" class="showDetaFormBtn"></p>
        
        <!-- <p id="addDeta">データを追加</p> -->
    </div>
    <div class="line"></div>
    <?php if(isset($message)) : ?>
        <p class="notice"><?= h($message); ?></p>
    <?php endif; ?>
    <div class="line"></div>

    <?php foreach($machines as $machine_address => $machineName) { ?>
        <h2 class="machine"><?= h($machine_address); ?> </h2>
        <div class="line"></div>
        <div class="card_list">
            <?php foreach($machineName as $row) { ?>
                <div class="card">
                    <div class="card_content" onclick="location.href='<?= h($row['path']);?>';">
                        <p class="file_name"> <?= h($row['name']); ?> </p>
                        <div class="subline"></div>
                        <p class="shareText">
                            <?php if(!is_valid_url($row['memo'])) {
                                echo h($row['memo']);
                            }else{
                                ?><a href=<?= h($row['memo']); ?>><?= h($row['memo']) ?></a>
                            <?php }
                            ?>
                        </p>
                        <p class="uploadTime"> <?php echo h($row['uploadTime']); ?> </p>
                        <?php if(!empty($row['filetype'])) : ?>
                            <a href="<?= h($row['path']); ?>"><img src="./assets/images/media.png " alt="media"></a>
                        <?php endif; ?>
                    </div>
                    
                    <p class="deletebtn" onclick="execPost('./delete.php', {'id':'<?= $row['id'];?>'});">削除</p>
                </div>
            <?php } ?>
        </div>
        <div class="line"></div>
    <?php } ?>

    <div class="new_deta_form">
        <div class="new_deta_form_text">
            <h1>データを追加</h1>
            <div class="line"></div>
            <?php if(isset($validateError)) : ?>
                <script>window.onload = function () {
                    showDetaForm();
                }</script>
                <p class="ds_errors">
                    <?= h($validateError) ?>
                </p>
            <?php endif; ?>
            <div class="line"></div>
            <form action="./new.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="タイトル"><br>
                <input type="text" name="memo" placeholder="コメント, メモ"><br>
                <input type="file" name="file_deta"><br>
                <input type="submit" id="addDetaBtn" value="追加"><br>
            </form>
        </div>
    </div>

    <div class="host_address"><?= h(exec("/sbin/ifconfig en0 | grep 'inet ' | cut -d ' ' -f2")); ?></div>
    <script src="./assets/js/functions.js" defer></script>
    <script src="./assets/js/addDeta.js" defer></script>
</body>
</html>
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
                                ?><a href=<?= h($row['memo']); ?>><?= h($row['memo']) ?></a> <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="external-link-alt" class="svg-inline--fa fa-external-link-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="" d="M432,320H400a16,16,0,0,0-16,16V448H64V128H208a16,16,0,0,0,16-16V80a16,16,0,0,0-16-16H48A48,48,0,0,0,0,112V464a48,48,0,0,0,48,48H400a48,48,0,0,0,48-48V336A16,16,0,0,0,432,320ZM488,0h-128c-21.37,0-32.05,25.91-17,41l35.73,35.73L135,320.37a24,24,0,0,0,0,34L157.67,377a24,24,0,0,0,34,0L435.28,133.32,471,169c15,15,41,4.5,41-17V24A24,24,0,0,0,488,0Z"></path></svg>
                            <?php }
                            ?>
                        </p>
                        <p class="uploadTime"> <?php echo h($row['uploadTime']); ?> </p>
                        <?php if(!empty($row['filetype'])) : ?>
                            <div class="is_uploadfile">
                                <a href="<?= h($row['path']); ?>"><img src="./assets/images/media.png " alt="media"></a>
                            </div>
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
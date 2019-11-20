# DetaShare
ローカル端末で外部サービスを使わずデータ共有をします
※開発途中です
# 導入
1. MAMP 又は XAMPP 等 webServer, php, mysqlが使える環境を用意
1. MAMP, XAMPPの場合phpMyAdminで`DetaShare`というデータベースを作る。 要はMySQLで`DetaShare`というdb作る
1. lib/db.php の $usernameと$password にある情報でMySQLのユーザーを作る
1. Detashareでdetashare.sqlをインポートする
1. Webサーバー起動

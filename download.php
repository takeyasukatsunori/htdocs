
<?php

// 変数の初期化
$csv_data = null;
$option = null;
$message_array = array();
$limit = null;
$stmt = null;


session_start();
// 取得件数
if( !empty($_GET['limit']) ) {

	if( $_GET['limit'] === "10" ) {
		$limit = 10;
	} elseif( $_GET['limit'] === "30" ) {
		$limit = 30;
	}
}
// データベースに接続
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
    );
    $pdo = new PDO('mysql:charset=UTF8;dbname=test;host=localhost', 'root', 'katsu2002682168',$option);

// データベースに接続
$mysqli = new mysqli( 'localhost', 'root', 'katsu2002682168', 'test');

	
//ダウンロード
	$sql = "SELECT * FROM bbs ORDER BY id DESC";
	$res = $mysqli->query($sql);

    if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
    $mysqli->close();

    // // // メッセージのデータを取得する
    // if( !empty($limit) ) {

    //     // SQL作成
    //     $stmt = $pdo->prepare(" SELECT * FROM bbs ORDER BY id DESC LIMIT :limit");

    //     // 値をセット
    //     $stmt->bindValue( ':limit', $_GET['limit'], PDO::PARAM_INT);

    // } else {
    //     $stmt = $pdo->prepare("SELECT * FROM bbs ORDER BY  id DESC");
    // }
    // // SQLクエリの実行
    // $stmt->execute();
    // $message_array = $stmt->fetchAll();

    // // データベースの接続を閉じる
	// 	$sql = null;
	// 	$pdo = null;

// 出力の設定
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=メッセージデータ.csv");
header("Content-Transfer-Encoding: binary");
// CSVデータを作成
if( !empty($message_array) ) {
		
    // 1行目のラベル作成
    $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";
    
    foreach( $message_array as $value ) {

        // データを1行ずつCSVファイルに書き込む
        $csv_data .= '"' . $value['id'] . '","' . $value['name'] . '","' . $value['message'] . '","' . $value['date'] . "\"\n";

    }

print $csv_data;
} else {

	// ログインページへリダイレクト
	header("Location: ./admin.php");
	exit;
}

return;
?>
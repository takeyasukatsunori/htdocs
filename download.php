
<?php
// メッセージを保存するファイルのパス設定
define( 'password', 'katsunori');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();
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
try {
    $pdo = new PDO('mysql:charset=UTF8;dbname=board;host=localhost', 'root', 'katsu2002682168');

} catch(PDOException $e) {

    // 接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
}

// ここにログインページを作りたい

if( !empty($_POST['btn_submit']) ) {
    if( !empty($_POST['admin_password']) && $_POST['admin_password'] === password ) {
		$_SESSION['admin_login'] = true;
	} else {
		$error_message[] = 'ログインに失敗しました。';
	}
	
	if( empty($error_message) ) {

		// データベースに接続
		$mysqli = new mysqli( 'localhost', 'root', 'katsu2002682168', 'test');
        
		
		// 接続エラーの確認
		if( $mysqli->connect_errno ) {
			$error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
		} else {

			// 文字コード設定
			$mysqli->set_charset('utf8');
			
			// 書き込み日時を取得
			$now_date = date("Y-m-d H:i:s");
			
			// データを登録するSQL作成
			$sql = "INSERT INTO bbs (name, message, date, ip) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date','$_SERVER[REMOTE_ADDR]')";
            print $sql;
            print  $_SERVER["REMOTE_ADDR"] ;
			
			// データを登録
			$res = $mysqli->query($sql);

		
			// データベースの接続を閉じる
			$mysqli->close();
		}
	}
}

// データベースに接続
$mysqli = new mysqli( 'localhost', 'root', 'katsu2002682168', 'test');

	
// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

	$sql = "SELECT * FROM bbs ORDER BY id DESC";
	$res = $mysqli->query($sql);

    if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
// var_dump($message_array) ;
    $mysqli->close();
}
    // メッセージのデータを取得する
    if( !empty($limit) ) {

        // SQL作成
        $stmt = $pdo->prepare("SELECT * FROM bbs ORDER BY id ASC LIMIT :limit");

        // 値をセット
        $stmt->bindValue( ':limit', $_GET['limit'], PDO::PARAM_INT);

    } else {
        $stmt = $pdo->prepare("SELECT * FROM bbs ORDER BY id ASC");
    }
    // SQLクエリの実行
    $stmt->execute();
    $message_array = $stmt->fetchAll();
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
}

print $csv_data;

?>
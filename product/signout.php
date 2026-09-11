<?php
# **********************************************************
# サインアウトページ
# **********************************************************
# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );

# ==========================================================
# 変数の定義
# ==========================================================

# ==========================================================
# 主処理
# ==========================================================
$g_Log->notice( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

foreach( $_POST as $key => $value ){
	$g_Log->debug( "POST : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

foreach( $_SESSION as $key => $value ){
	$g_Log->debug( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

$g_Log->notice( "セッション情報をクリアします。", __FUNCTION__, basename( __FILE__ ) );
$_SESSION = array();

$g_Log->notice( "セッション情報を破棄します。", __FUNCTION__, basename( __FILE__ ) );
session_destroy();

$g_Log->notice( "トップページへリダイレクトします。", __FUNCTION__, basename( __FILE__ ) );
header("Location: ./");
exit();

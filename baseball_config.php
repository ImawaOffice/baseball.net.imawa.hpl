<?php
# **********************************************************
#  baseball_config.php
# **********************************************************
# ==========================================================
# LOG設定
# ==========================================================
$g_Log_Level = 8;	// ログレベル（0: 無効、1: エラーのみ、2: エラー＋警告、4: エラー＋警告＋情報、8: エラー＋警告＋情報＋デバッグ情報）
$g_Log_Level = 4;	// ログレベル（0: 無効、1: エラーのみ、2: エラー＋警告、4: エラー＋警告＋情報、8: エラー＋警告＋情報＋デバッグ情報）
$g_Log_Path = __DIR__ . DIRECTORY_SEPARATOR . 'Log'; // ログファイルパス
$g_Log_Suffix = 'baseball.log'; // ログファイル拡張子

# ==========================================================
# 環境設定
# ==========================================================
$g_env = "develop";

if( $_SERVER[ "SERVER_NAME" ] == "hpl.imawa.net" ){
	$g_env = "production";
}

# ==========================================================
# CONFIG_DB設定
# ==========================================================
$g_DB_Config = null;
$g_DB_Config_DbType   = "sqlite"; // 'sqlite' または 'mysql'
$g_DB_Config_HostName = __DIR__ . DIRECTORY_SEPARATOR . "baseball_{$g_env}.db";
$g_DB_Config_DbPort   = 3306; // MySQLのポート番号
$g_DB_Config_Database = ''; // SQLiteは不要
$g_DB_Config_Username = ''; // SQLiteは不要
$g_DB_Config_Password = ''; // SQLiteは不要

# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'Class_Log.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'Class_Database.php' );

# ==========================================================
# セッションの開始
# ==========================================================
if( session_status() === PHP_SESSION_NONE ){
	$g_Log->notice( "セッション開始", __FUNCTION__, basename( __FILE__ ) );
	session_start();
}

foreach( $_SESSION as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->debug( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

# ==========================================================
# CONFIGデータベース接続
# ==========================================================
$g_DB_Config = new Class_Database( $g_DB_Config_DbType, $g_DB_Config_HostName );

# ==========================================================
# CONFIG情報取得
# ==========================================================
$SQL = null;
$SQL .= "SELECT ";
$SQL .= "  config_key ";
$SQL .= ", config_value ";
$SQL .= ", config_type ";
$SQL .= "  FROM config ";
$SQL .= " WHERE is_enabled = 1 ";

$dataTable = $g_DB_Config->select( $SQL );

foreach( $dataTable as $dataRow ){
	$key   = $dataRow[ 'config_key' ];
	$value = $dataRow[ 'config_value' ];
	$type  = $dataRow[ 'config_type' ];

	switch( $type ){
		case 'INTEGER':
			$CONFIG[ $key ] = intval( $value );
			break;
		case 'TEXT':
			$CONFIG[ $key ] = strval( $value );
			break;
		default:
			$CONFIG[ $key ] = $value;
			break;
	}
}

foreach( $CONFIG as $key => $value ){
	$g_Log->debug( "CONFIG : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

# ==========================================================
# データベース接続
# ==========================================================
$g_DB  = new Class_Database( $CONFIG[ "DbType" ], $CONFIG[ "DbHostName" ], $CONFIG[ "DbPort" ], $CONFIG[ "DbName" ], $CONFIG[ "DbUsername" ], $CONFIG[ "DbPassword" ] );

# ----------------------------------------------------------
# メニュー情報取得
# ----------------------------------------------------------
$headerId = getMenuId( "HEADER" );
$HEADER_MENU = getMenu( $headerId );
$footerId = getMenuId( "FOOTER" );
$FOOTER_MENU = getMenu( $footerId );

# ==========================================================
# 親メニューID取得関数
# @param string $p_menuName メニュー名
# @return int メニューID
# ==========================================================
function getMenuId( $p_menuName = "" ){

	global $g_Log;
	$g_Log->notice( "親メニューIDの取得 : " . $p_menuName, __FUNCTION__, basename( __FILE__ ) );

	$role = 0;
	if( isset( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ) ){
		$role = $_SESSION[ 'AUTH_ROLE_LEVEL' ];
	}

	$SQL = null;
	$SQL .= "SELECT ";
	$SQL .= "  menu_id ";
	$SQL .= "FROM menu ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "AND parent_id = 0 ";
	$SQL .= "AND role_min <= :role ";
	$SQL .= "AND role_max >= :role ";
	if( $p_menuName != "" ){
		$SQL .= "AND menu_name = :menu_name ";
	}
	$SQL .= "ORDER BY display_order ASC ";

	$SQL_Parameters = array();
	$SQL_Parameters[ 'role' ] = $role;
	$SQL_Parameters[ 'menu_name' ] = $p_menuName;

	$dataTable = array();

	try {

		global $g_DB_Config;
		$dataTable = $g_DB_Config->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$g_Log->error( "親メニューIDの取得でエラーが発生しました。" . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return 0;
	}

	foreach( $dataTable as $dataRow ){
		$menuId = $dataRow[ 'menu_id' ];
	}

	return $menuId;
}

# ==========================================================
# メニュー情報取得関数
# @param int $p_menuParentId 親メニューID
# @return int メニューID
# ==========================================================
function getMenu( $p_menuParentId = 0 ) {
	
	global $g_DB_Config, $g_Log;

	$g_Log->notice( "メニュー情報の再帰取得 : " . $p_menuParentId, __FUNCTION__, basename( __FILE__ ) );

	$role = 0;
	if( isset( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ) ){
		$role = $_SESSION[ 'AUTH_ROLE_LEVEL' ];
	}

	$SQL = "";
	$SQL .= "SELECT 'menu' || menu_id AS menu_cd ";
	$SQL .= ", menu_id ";
	$SQL .= ", menu_name ";
	$SQL .= ", menu_url ";
	$SQL .= ", parent_id ";
	$SQL .= ", display_order ";
	$SQL .= "FROM menu ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "AND parent_id = :parent_id ";
	$SQL .= "AND role_min <= :role ";
	$SQL .= "AND role_max >= :role ";
	$SQL .= "ORDER BY display_order ASC ";
	$SQL .= ", parent_id ";
	$SQL .= ", menu_id ";

	$SQL_Parameters = array(
		'parent_id' => $p_menuParentId,
		'role' => $role
	);

	$dataTable = array();

	try {
		$dataTable = $g_DB_Config->select($SQL, $SQL_Parameters);
	} catch ( Exception $e ) {
		$g_Log->error("メニュー情報の取得でエラーが発生しました。\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ));
		return array();
	}

	$result = array();

	foreach ( $dataTable as $dataRow ) {
		$children = getMenu( $dataRow[ 'menu_id' ] );
		$dataRow[ 'children' ] = $children;
		$result[] = $dataRow;
	}

	return $result;
}

# ==========================================================
# HTMLのエスケープ関数
# @param string $p_string 文字列
# @return string エスケープされた文字列
# ==========================================================
function htmlEscape( $p_string = "" ){

	$html = htmlspecialchars( $p_string, ENT_QUOTES, 'UTF-8' );
	$string = nl2br( $html );

	return $string;
}
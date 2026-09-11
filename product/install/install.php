<?php
# **********************************************************
#  install.php
# **********************************************************
# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'baseball_config.php');

# ==========================================================
# 変数の設定
# ==========================================================
$g_Config_DDL = array(
	"table_config_createTable.sql",
	"table_menu_DropTable.sql",
	"table_menu_createTable.sql",
);

$g_Config_DML = array(
	"table_config_insertTable_{$g_env}.sql",
	"table_menu_insertTable_{$g_env}.sql",
);

$g_DB_DDL = array(
	"table_baseball_information_createTable.sql",
	"table_baseball_role_createTable.sql",
	"table_baseball_user_createTable.sql",
	"table_baseball_tournament_createTable.sql",
	"table_baseball_team_CreateTable.sql",
	"table_baseball_game_CreateTable.sql",
);

$g_DB_DML = array(
	"table_baseball_role_insertTable.sql",
);


# ==========================================================
# 主処理
# ==========================================================
foreach( $g_Config_DDL as $SQL_File ) {
	if( ! setConfig_DDL( $SQL_File ) ){
		$g_Log->error( "テーブルの作成に失敗しました: {$SQL_File}", __FUNCTION__, basename( __FILE__ ) );
		exit;
	}
}	

foreach( $g_Config_DML as $SQL_File ) {
	if( ! setConfig_DML( $SQL_File ) ){
		$g_Log->error( "設定データの挿入に失敗しました: {$SQL_File}", __FUNCTION__, basename( __FILE__ ) );
		exit;
	}
}
foreach( $g_DB_DDL as $SQL_File ) {
	if( ! setDB_DDL( $SQL_File ) ){
		$g_Log->error( "テーブルの作成に失敗しました: {$SQL_File}", __FUNCTION__, basename( __FILE__ ) );
		exit;
	}
}	

foreach( $g_DB_DML as $SQL_File ) {
	if( ! setDB_DML( $SQL_File ) ){
		$g_Log->error( "設定データの挿入に失敗しました: {$SQL_File}", __FUNCTION__, basename( __FILE__ ) );
		exit;
	}
}

echo "インストールが完了しました。\n";

# ==========================================================
# 設定データベース
# ==========================================================
function setConfig_DDL( $p_SQLFile = null ) {

	global $g_Log;
	$g_Log->debug( "設定データベース", __FUNCTION__, basename( __FILE__ ) );

	global $g_DB_Config;

	$SQL = file_get_contents( $p_SQLFile );

	try {
		$stmt = $g_DB_Config->execute( $SQL );
		$g_Log->debug( "{$p_SQLFile}を実行しました", __FUNCTION__, basename( __FILE__ ) );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。 : {$p_SQLFile}" );
		}
	}
	catch( PDOException $e ){
		$g_Log->error( "{$p_SQLFile}の実行に失敗しました" . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 設定データベース
# ==========================================================
function setConfig_DML( $p_SQLFile = null ) {

	global $g_Log;
	$g_Log->debug( "設定データベース", __FUNCTION__, basename( __FILE__ ) );

	global $g_DB_Config;
	global $g_env;

	$SQL = file_get_contents( $p_SQLFile );

	try {
		$stmt = $g_DB_Config->execute( $SQL );
		$g_Log->debug( "{$p_SQLFile}を実行しました", __FUNCTION__, basename( __FILE__ ) );

		if( $stmt === false ){
			$g_Log->warning( "SQLの実行に失敗しました。 : {$p_SQLFile}", __FUNCTION__, basename( __FILE__ ) );
		}
	}
	catch( PDOException $e ){
		$g_Log->error( "{$p_SQLFile}の実行に失敗しました" . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}


# ==========================================================
# 設定データベース
# ==========================================================
function setDB_DDL( $p_SQLFile = null ) {

	global $g_Log;
	$g_Log->debug( "DDL実行", __FUNCTION__, basename( __FILE__ ) );

	global $g_DB;

	$SQL = file_get_contents( $p_SQLFile );

	try {
		$stmt = $g_DB->execute( $SQL );
		$g_Log->debug( "{$p_SQLFile}を実行しました", __FUNCTION__, basename( __FILE__ ) );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。 : {$p_SQLFile}" );
		}
	}
	catch( PDOException $e ){
		$g_Log->error( "{$p_SQLFile}の実行に失敗しました" . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 設定データベース
# ==========================================================
function setDB_DML( $p_SQLFile = null ) {

	global $g_Log;
	$g_Log->debug( "DML実行", __FUNCTION__, basename( __FILE__ ) );

	global $g_DB;

	$SQL = file_get_contents( $p_SQLFile );

	try {
		$stmt = $g_DB->execute( $SQL );
		$g_Log->debug( "{$p_SQLFile}を実行しました", __FUNCTION__, basename( __FILE__ ) );
		
		if( $stmt === false ){
			$g_Log->warning( "SQLの実行に失敗しました。 : {$p_SQLFile}", __FUNCTION__, basename( __FILE__ ) );
		}
	}
	catch( PDOException $e ){
		$g_Log->error( "{$p_SQLFile}の実行に失敗しました" . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

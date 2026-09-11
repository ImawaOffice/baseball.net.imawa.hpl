<?php
# **********************************************************
# トップページ
# **********************************************************

# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );

# ==========================================================
# 変数の定義
# ==========================================================
$g_Log->notice( "S : " . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

foreach( $_POST as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->notice( "POST : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

foreach( $_SESSION as $key => $value ){
	if( is_array( $value ) || is_object( $value ) ){
		$value = print_r( $value, true );
	}
	$g_Log->notice( "SESSION : {$key} = {$value}", __FUNCTION__, basename( __FILE__ ) );
}

# ==========================================================
# 主処理
# ==========================================================
if( initProc() ){
	// リクエスト処理
	if( requestProc() ) {
		// チェック処理
		if( checkProc() ) {
			// セット処理
			if( setProc() ) {
			}
		}
	}
}
// 終了処理
if( endProc() ) {
}

# ==========================================================
# 主処理
# ==========================================================

# ==========================================================
# 初期設定
# ==========================================================
function initProc() {

	global $g_Log;
	$g_Log->notice( "初期処理", __FUNCTION__, basename( __FILE__ ) );

	// 認証チェック
	if( ! isAuthenticated() ) {
		$g_Log->notice( "認証されていないためログインページへリダイレクト", __FUNCTION__, basename( __FILE__ ) );
		header( "Location: ./signin" );
		exit();
	}

		
	// セッション変数の初期化
	$sessionList = [
		'FROM_VIEW_STATE'               => 0,		// 表示ステータス(0: 初期画面)
		'FORM_ERR_MSG'                  => '',		// エラーメッセージ
		'FORM_BUTTON_DELETE'            => '削除',	// 削除ボタン
		'FORM_TOURNAMENT_RULE_ID'       => 0,		// 大会規定ファイルID
		'FORM_TOURNAMENT_RULE_FILENAME' => '',		// 大会規定ファイル名
		'FORM_GAME_RULE_ID'             => 0,		// 試合規定ファイルID
		'FORM_GAME_RULE_FILENAME'       => '',		// 試合規定ファイル名
	];

	// セッション変数の設定
	foreach ( $sessionList as $name => $value ) {
		$_SESSION[ $name ] = $value;
	}

	return true;
}

# ==========================================================
# リクエスト処理
# ==========================================================
function requestProc() {

	global $g_Log;
	$g_Log->notice( "リクエスト処理", __FUNCTION__, basename( __FILE__ ) );
	$g_Log->notice( "REQUEST_METHOD : " . $_SERVER[ 'REQUEST_METHOD' ], __FUNCTION__, basename( __FILE__ ) );

	// GETリクエストの場合は処理なし
	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' ) {
		$g_Log->notice( "GETリクエストのため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// GET, POSTリクエスト以外の場合
	if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
		$g_Log->notice( "GET/POSTリクエスト以外のため以降の処理なし", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkProc() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 追加チェック
	if( ! checkAdd() ) {
		$g_Log->notice( "追加チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 更新チェック
	if( ! checkUpdate() ) {
		$g_Log->notice( "更新チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 削除チェック
	if( ! checkDelete() ) {
		$g_Log->notice( "削除チェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 追加チェック処理
# ==========================================================
function checkAdd() {

	global $g_Log;
	$g_Log->notice( "追加チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 追加ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'add_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->notice( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 更新チェック処理
# ==========================================================
function checkUpdate() {

	global $g_Log;
	$g_Log->notice( "更新チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 更新ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'update_button', 'POST' ) ) {
		return true;
	}

	if( ! checkForm() ) {
		$g_Log->notice( "フォームのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 削除チェック処理
# ==========================================================
function checkDelete() {

	global $g_Log;
	$g_Log->notice( "削除チェック処理", __FUNCTION__, basename( __FILE__ ) );
	
	// 削除ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'delete_button', 'POST' ) ) {
		return true;
	}

	$g_Log->notice( "POST : delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );

	// 削除対象のID
	if ( deleteTournamentId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->notice( "大会登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会登録内容の削除に失敗しました。";
		$g_Log->notice( "{$_SESSION[ 'FORM_ERR_MSG' ]} ： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# フォームチェック処理
# ==========================================================
function checkForm(){
	
	global $g_Log;
	$g_Log->notice( "フォームチェック処理", __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# セット処理
# ==========================================================
function setProc() {

	global $g_Log;
	$g_Log->notice( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'FROM_VIEW_STATE' ] = trim( $_POST[ 'view_state' ] );
	$g_Log->notice( "[ FROM_VIEW_STATE ] : " . $_SESSION[ 'FROM_VIEW_STATE' ], __FUNCTION__, basename( __FILE__ ) );

	// 更新処理
	if( isset( $_POST[ 'update_button' ] ) && $_POST[ 'update_button' ] !== "" ) {
		$attachmentType = 0;
		$postName = '';
		switch ( $_POST[ 'update_button' ] ) {
			case '2':	// 大会規定ファイル
				$attachmentType = 2;
				$postName = 'tournament_rule';
				break;
			case '3':	// 試合規定ファイル
				$attachmentType = 3;
				$postName = 'game_rule';
				break;
			default:
				$g_Log->notice( "不正な更新対象のため以降の処理なし： update_button = " . $_POST[ 'update_button' ], __FUNCTION__, basename( __FILE__ ) );
				return false;
		}

		uploadAttachement( $postName, $attachmentType );
	}

	return true;
}

# ==========================================================
# 大会登録内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function getTournamentRuleFile(){

	global $g_Log;
	$g_Log->notice( "大会規定ファイル取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	// アップロードしたファイルの書込み
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   attachment_id ";
	$SQL .= " , attachment_type ";
	$SQL .= " , physical_file_name ";
	$SQL .= " , file_name ";
	$SQL .= " , file_path ";
	$SQL .= " , file_size ";
	$SQL .= " , mime_type ";
	$SQL .= " , file_description ";
	$SQL .= "   FROM baseball_attachment ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND attachment_type = 2 ";	// attachment_typeが2のレコードを大会規定ファイルとして扱う
	$SQL .= "  ORDER BY attachment_id DESC ";	// 取得したレコードのうち、attachment_idが最大のレコードを大会規定ファイルとして扱う
	$SQL .= "  LIMIT 1 ";	// 取得件数を1件に限定
	
	$SQL_Parameters = array(
	);
	
	$_SESSION[ 'FORM_TOURNAMENT_RULE_ID' ]       = 0;		// 大会規定ファイルID
	$_SESSION[ 'FORM_TOURNAMENT_RULE_FILENAME' ] = '';		// 大会規定ファイル名

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$_SESSION[ 'FORM_TOURNAMENT_RULE_ID' ] = $row[ 'attachment_id' ];
			$_SESSION[ 'FORM_TOURNAMENT_RULE_FILENAME' ] = $row[ 'file_name' ];
			$g_Log->notice( "SESSION[ 'FORM_TOURNAMENT_RULE_ID' ] : {$row[ 'attachment_id' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'FORM_TOURNAMENT_RULE_FILENAME' ] : {$row[ 'file_name' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会規定ファイルの取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合規定ファイル取得処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function getGameRuleFile(){

	global $g_Log;
	$g_Log->notice( "試合規定ファイル取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	// アップロードしたファイルの書込み
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   attachment_id ";
	$SQL .= " , attachment_type ";
	$SQL .= " , physical_file_name ";
	$SQL .= " , file_name ";
	$SQL .= " , file_path ";
	$SQL .= " , file_size ";
	$SQL .= " , mime_type ";
	$SQL .= " , file_description ";
	$SQL .= "   FROM baseball_attachment ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND attachment_type = 3 ";	// attachment_typeが3のレコードを試合規定ファイルとして扱う
	$SQL .= "  ORDER BY attachment_id DESC ";	// 取得したレコードのうち、attachment_idが最大のレコードを試合規定ファイルとして扱う
	$SQL .= "  LIMIT 1 ";	// 取得件数を1件に限定
	$SQL_Parameters = array(
	);
	
	$_SESSION[ 'FORM_GAME_RULE_ID' ]       = 0;		// 試合規定ファイルID
	$_SESSION[ 'FORM_GAME_RULE_FILENAME' ] = '';		// 試合規定ファイル名

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$_SESSION[ 'FORM_GAME_RULE_ID' ] = $row[ 'attachment_id' ];
			$_SESSION[ 'FORM_GAME_RULE_FILENAME' ] = $row[ 'file_name' ];
			$g_Log->notice( "SESSION[ 'FORM_GAME_RULE_ID' ] : {$row[ 'attachment_id' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
			$g_Log->notice( "SESSION[ 'FORM_GAME_RULE_FILENAME' ] : {$row[ 'file_name' ]} セットしました", __FUNCTION__, basename( __FILE__ ) );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合規定ファイルの取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 大会登録内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function addTournament(){

	global $g_Log;
	$g_Log->notice( "大会登録内容追加処理", __FUNCTION__, basename( __FILE__ ) );

	$startDate = isValidDate( $_POST[ 'tournament_startDate' ] );
	$endDate = isValidDate( $_POST[ 'tournament_endDate' ] );
	$tournamentTitle = trim( $_POST[ 'tournament_title' ] );
	$tournamentText = trim( $_POST[ 'tournament_text' ] );
	$tournament1Teams = ( int )$_POST[ 'tournament1_teams' ];
	$tournament1Text = trim( $_POST[ 'tournament1_text' ] );
	$tournament2Teams = ( int )$_POST[ 'tournament2_teams' ];
	$tournament2Text = trim( $_POST[ 'tournament2_text' ] );
	$tournament3Teams = ( int )$_POST[ 'tournament3_teams' ];
	$tournament3Text = trim( $_POST[ 'tournament3_text' ] );
	$attachmentId = uploadAttachement( 'file_name' );

	// SQL文作成
	$SQL = "";
	$SQL .= " INSERT INTO baseball_tournament ";
	$SQL .= " ( tournament_title ";
	$SQL .= " , tournament_text ";
	$SQL .= " , tournament_start_date ";
	$SQL .= " , tournament_end_date ";
	$SQL .= " , tournament1_teams ";
	$SQL .= " , tournament1_text ";
	$SQL .= " , tournament2_teams ";
	$SQL .= " , tournament2_text ";
	$SQL .= " , tournament3_teams ";
	$SQL .= " , tournament3_text ";
	$SQL .= " , tournament_attachment_id ";
	$SQL .= " , is_enabled ";
	$SQL .= " , created_at ";
	$SQL .= " , created_by ";
	$SQL .= " , updated_at ";
	$SQL .= " , updated_by ";
	$SQL .= " ) ";
	$SQL .= " VALUES ";
	$SQL .= " ( :tournament_title ";
	$SQL .= " , :tournament_text ";
	$SQL .= " , :tournament_start_date ";
	$SQL .= " , :tournament_end_date ";
	$SQL .= " , :tournament1_teams ";
	$SQL .= " , :tournament1_text ";
	$SQL .= " , :tournament2_teams ";
	$SQL .= " , :tournament2_text ";
	$SQL .= " , :tournament3_teams ";
	$SQL .= " , :tournament3_text ";
	$SQL .= " , :tournament_attachment_id ";
	$SQL .= " , 1 ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :created_by ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :updated_by ";
	$SQL .= " ) ";
	
	$SQL_Parameters = array(
		'tournament_title' => $tournamentTitle,
		'tournament_text' => $tournamentText,
		'tournament_start_date' => $startDate,
		'tournament_end_date' => $endDate,
		'created_by' => basename( __FILE__ ),
		'updated_by' => basename( __FILE__ ),
		'tournament1_teams' => $tournament1Teams,
		'tournament1_text' => $tournament1Text,
		'tournament2_teams' => $tournament2Teams,
		'tournament2_text' => $tournament2Text,
		'tournament3_teams' => $tournament3Teams,
		'tournament3_text' => $tournament3Text,
		'tournament_attachment_id' => $attachmentId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 大会登録内容更新処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateTournament(){

	global $g_Log;
	$g_Log->notice( "大会登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$tournamentId = ( int )$_POST[ 'tournament_id' ];
	$tournamentTitle = trim( $_POST[ 'tournament_title' ] );
	$tournamentText = trim( $_POST[ 'tournament_text' ] );
	$startDate = isValidDate( $_POST[ 'tournament_startDate' ] );
	$endDate = isValidDate( $_POST[ 'tournament_endDate' ] );
	$tournament1Teams = ( int )$_POST[ 'tournament1_teams' ];
	$tournament1Text = trim( $_POST[ 'tournament1_text' ] );
	$tournament2Teams = ( int )$_POST[ 'tournament2_teams' ];
	$tournament2Text = trim( $_POST[ 'tournament2_text' ] );
	$tournament3Teams = ( int )$_POST[ 'tournament3_teams' ];
	$tournament3Text = trim( $_POST[ 'tournament3_text' ] );
	$attachmentId = uploadAttachement( 'file_name', 1 );
	if ( $attachmentId == 0 ) {
		if( isset( $_POST[ 'tournament_attachment_id' ] ) ) {
			$attachmentId = ( int )$_POST[ 'tournament_attachment_id' ];
		}
	}

	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_tournament ";
	$SQL .= " SET tournament_title = :tournament_title ";
	$SQL .= "   , tournament_text = :tournament_text ";
	$SQL .= "   , tournament_start_date = :tournament_start_date ";
	$SQL .= "   , tournament_end_date = :tournament_end_date ";
	$SQL .= "   , tournament1_teams = :tournament1_teams ";
	$SQL .= "   , tournament1_text = :tournament1_text ";
	$SQL .= "   , tournament2_teams = :tournament2_teams ";
	$SQL .= "   , tournament2_text = :tournament2_text ";
	$SQL .= "   , tournament3_teams = :tournament3_teams ";
	$SQL .= "   , tournament3_text = :tournament3_text ";
	$SQL .= "   , tournament_attachment_id = :tournament_attachment_id ";
	$SQL .= "   , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "   , updated_by = :updated_by ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	
	$SQL_Parameters = array(
		'tournament_title' => $tournamentTitle,
		'tournament_text' => $tournamentText,
		'tournament_start_date' => $startDate,
		'tournament_end_date' => $endDate,
		'tournament1_teams' => $tournament1Teams,
		'tournament1_text' => $tournament1Text,
		'tournament2_teams' => $tournament2Teams,
		'tournament2_text' => $tournament2Text,
		'tournament3_teams' => $tournament3Teams,
		'tournament3_text' => $tournament3Text,
		'tournament_attachment_id' => $attachmentId,
		'updated_by' => basename( __FILE__ ),
		'tournament_id' => $tournamentId,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会登録内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 終了処理
# ==========================================================
function endProc() {

	global $g_Log;
	$g_Log->notice( "終了処理", __FUNCTION__, basename( __FILE__ ) );

	getTournamentRuleFile();
	getGameRuleFile();

	// データベース切断
	if( ! dbDisConnect() ) {
		return false;
	}

	return true;
}

# ==========================================================
# データベース切断
# ==========================================================
function dbDisConnect() {

	global $g_Log;
	$g_Log->notice( "データベース切断処理", __FUNCTION__, basename( __FILE__ ) );

	global $g_SQLite, $g_DB1_DbType, $g_DB1_HostName;
	
	if ( $g_SQLite !== null ) {
		$g_SQLite = null;
		$g_Log->notice( "データベースから切断しました： $g_DB1_DbType:$g_DB1_HostName", __FUNCTION__, basename( __FILE__ ) );
	}

	return true;
}
?>

<?php
	// HTMLヘッダーの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "html_head.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>
<body>

<?php
	// ページヘッダーの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "page_header.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>

<main class="page-main">

<?php
	// ルールメンテナンスの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_rulemaintenance.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>

</main>

<?php
	// ページフッターの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "page_footer.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>

<?php
	// スクロールトップボタンの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "scrolltop.php";
	if ( file_exists( $filename ) ){
		require_once( $filename );
	}
?>
</body>
</html>

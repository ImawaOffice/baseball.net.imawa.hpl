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
		'FORM_VIEW_STATE'    => 0,		// 表示ステータス(0: 初期画面)
		'FORM_ERR_MSG'       => '',		// エラーメッセージ
		'FORM_PAGE_OFFSET'   => 0,		// 表示オフセット
		'FORM_PAGE_TOTAL'    => 0,		// 総ページ数
		'FORM_PAGE_ROWS'     => 10,		// 表示行数
		'FORM_PAGE_CURRENT'  => 1,		// ページ番号
		'FORM_PAGE_TOP'      => 1,		// ページの最初
		'FORM_PAGE_LAST'     => 1,		// ページの最後
		'FORM_PAGE_PREVIOUS' => 1,		// 前のページ
		'FORM_PAGE_NEXT'     => 1,		// 次のページ
		'FORM_BUTTON_ADD'    => '追加',	// 追加ボタン
		'FORM_BUTTON_UPDATE' => '更新',	// 更新ボタン
		'FORM_BUTTON_DELETE' => '削除',	// 削除ボタン
		'FORM_DOWNLOAD_ENTRY' => '参加登録一覧ダウンロード',	// ダウンロードエントリー
		'FORM_TOURNAMENT_ID' => '',		// 大会ID
		'FORM_TEAM_ID'       => '',		// チームID
		'FORM_TEAM_NAME'     => '',		// チーム名
		'FORM_TEAM_MANAGER'  => '',		// 責任者名
		'FORM_TEAM_CONTACT'  => '',		// 連絡先
		'FORM_TEAM_ACCESS_CODE' => '',	// アクセスコード
		'FORM_TOURNAMENT1_TEXT' => '総当たり戦参加可能チーム数',	// 総当たり戦参加可能チーム数
		'FORM_TOURNAMENT1_TEAMS' => 0,	// 総当たり戦参加可能チーム数
		'FORM_TEAM_ALLOW_COUNT' => 0,	// 総当たり戦参加チーム数
		'FORM_TEAM_ACCESS_CODE' => '',	// アクセスコード
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
		$g_Log->notice( "GETリクエストのため大会ID抽出", __FUNCTION__, basename( __FILE__ ) );

		$FirstFlag = false;

		foreach ( getTournamentList() as $tournament ){
			if ( ! $FirstFlag ) {
				$_SESSION[ 'FORM_TOURNAMENT_ID' ] = $tournament[ 'tournament_id' ];
				$g_Log->notice( "[ FORM_TOURNAMENT_ID ] : " . $_SESSION[ 'FORM_TOURNAMENT_ID' ], __FUNCTION__, basename( __FILE__ ) );
				$FirstFlag = true;
			}
		}
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

	$_SESSION[ 'FORM_TOURNAMENT_ID' ] = checkNumeric( $_POST[ 'tournament_id' ] );

	// ページネーションのチェック
	if( ! checkPageNation() ){
		$_SESSION[ 'FORM_ERR_MSG' ] = "ページネーションエラー";
		$g_Log->notice( "ページネーションのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

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


	if( checkRequest( 'download_entry' ) ) {
		$g_Log->notice( "参加チームエントリー一覧ダウンロードボタンが押されました : TOURNAMENT_ID = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
		downloadEntry( $_POST[ 'tournament_id' ] );
		return false;
	}

	if( checkRequest( 'button_mail_allow' ) ) {
		$g_Log->notice( "参加チームメール送信ボタンが押されました : TOURNAMENT_ID = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
		sendMail_Allow( $_POST[ 'tournament_id' ] );
		return false;
	}

	if( checkRequest( 'button_mail_deny' ) ) {
		$g_Log->notice( "不参加チームメール送信ボタンが押されました : TOURNAMENT_ID = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
		sendMail_Deny( $_POST[ 'tournament_id' ] );
		return false;
	}

	return true;
}

# ==========================================================
# 参加登録チーム一覧ダウンロード処理
# @param int $p_tournamentId 大会ID
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function downloadEntry( $p_tournamentId ){

	global $g_Log;
	$g_Log->notice( "参加登録チーム一覧ダウンロード処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  tournament_id ";
	$SQL .= ", team_id ";
	$SQL .= ", team_name ";
	$SQL .= ", team_manager ";
	$SQL .= ", team_contact ";
	$SQL .= ", team_tel ";
	$SQL .= ", team_email ";
	$SQL .= ", team_access_cd ";
	$SQL .= ", tournament1_attend ";
	$SQL .= ", tournament2_attend ";
	$SQL .= ", tournament3_attend ";
	$SQL .= ", CASE tournament1_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament1_attend_name ";
	$SQL .= ", CASE tournament2_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament2_attend_name ";
	$SQL .= ", CASE tournament3_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament3_attend_name ";
	$SQL .= ", team_meta ";
	$SQL .= "FROM baseball_team ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "ORDER BY team_id ASC ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		// 出力先
		$output = fopen( 'php://output', 'w' );
		stream_filter_prepend( $output, 'convert.iconv.UTF-8/CP932' );

		// CSVヘッダー
		header( 'Content-Type: text/csv; charset=Shift_JIS' );
		header( 'Content-Disposition: attachment; filename="teamList.csv"' );

		// ヘッダー行
		fputcsv( $output, [ 
			'ID', 
			'大会概要承諾', 
			'抽選承諾', 
			'キャンセル待ち',
			'滋賀県内チーム',
			'宿泊希望',
			'宿泊同意',
			'昼食希望',
			'夕食希望',
			'宿泊選手数',
			'宿泊指導者数',
			'宿泊その他数',
			'宿泊駐車場利用数',
			'アレルギー',
			'アレルギー詳細',
			'チーム名',
			'チーム名（カナ）',
			'チーム代表者名',
			'チーム代表者名（カナ）',
			'チーム代表者電話番号',
			'チーム代表者メールアドレス',
			'大会連絡担当者名',
			'大会連絡担当者名（カナ）',
			'大会連絡担当者電話番号',
			'大会連絡担当者メールアドレス',
			'登録選手人数',
			'指導者人数',
			'保護者人数',
			'マイカー利用',
			'マイカー台数',
			'マイクロバス利用',
			'マイクロバス台数',
			'公共交通機関利用',
			'昼食1日目お弁当',
			'昼食1日目屋台',
			'昼食2日目お弁当',
			'昼食2日目屋台',
			'昼食3日目お弁当',
			'昼食3日目屋台',
			'チーム実績',
			'チームPR',
			'大会情報入手方法',
			'伝達事項等'
			], ',', '"', '', "\r\n" );

		foreach ( $dataTable as $row ) {
			$jsonData = json_decode( $row[ 'team_meta' ], true );

			$tournament_agree = "";
			if( isset( $jsonData[ 'tournament_agree' ] ) ){
				$tournament_agree = $jsonData[ 'tournament_agree' ] == "yes" ? "承諾" : "";
			}

			$tournament_lottery_agree = "";
			if( isset( $jsonData[ 'tournament_lottery_agree' ] ) ){
				$tournament_lottery_agree = $jsonData[ 'tournament_lottery_agree' ] == "yes" ? "承諾" : "";
			}

			$waitlist_preference = "";
			if( isset( $jsonData[ 'waitlist_preference' ] ) ){
				$waitlist_preference = $jsonData[ 'waitlist_preference' ] == "yes" ? "はい" : "いいえ";
			}
			$shiga_preference = "";
			if( isset( $jsonData[ 'shiga_preference' ] ) ){
				$shiga_preference = $jsonData[ 'shiga_preference' ] == "yes" ? "滋賀県内" : "滋賀県外";
			}

			$stay_preference = "";
			if( isset( $jsonData[ 'stay_preference' ] ) ){
				$stay_preference = $jsonData[ 'stay_preference' ] == "yes" ? "はい" : "いいえ";
			}

			$stay_agree = "";
			if( isset( $jsonData[ 'stay_agree' ] ) ){
				$stay_agree = $jsonData[ 'stay_agree' ] == "yes" ? "承諾" : "";
			}

			$lunch_preference = "";
			if( isset( $jsonData[ 'lunch_preference' ] ) ){
				$lunch_preference = $jsonData[ 'lunch_preference' ] == "yes" ? "はい" : "いいえ";
			}

			$dinner_preference = "";
			if( isset( $jsonData[ 'dinner_preference' ] ) ){
				$dinner_preference = $jsonData[ 'dinner_preference' ] == "yes" ? "はい" : "いいえ";
			}

			$stay_player_count = "";
			if( isset( $jsonData[ 'stay_player_count' ] ) ){
				$stay_player_count = $jsonData[ 'stay_player_count' ];
			}

			$stay_coach_count = "";
			if( isset( $jsonData[ 'stay_coach_count' ] ) ){
				$stay_coach_count = $jsonData[ 'stay_coach_count' ];
			}

			$stay_other_count = "";
			if( isset( $jsonData[ 'stay_other_count' ] ) ){
				$stay_other_count = $jsonData[ 'stay_other_count' ];
			}

			$stay_car_count = "";
			if( isset( $jsonData[ 'stay_car_count' ] ) ){
				$stay_car_count = $jsonData[ 'stay_car_count' ];
			}

			$allergy = "";
			if( isset( $jsonData[ 'allergy' ] ) ){
				$allergy = $jsonData[ 'allergy' ] == "yes" ? "はい" : "いいえ";
			}

			$allergy_info = "";
			if( isset( $jsonData[ 'allergy_info' ] ) ){
				$allergy_info = $jsonData[ 'allergy_info' ];
			}

			$team_name = "";
			if( isset( $jsonData[ 'team_name' ] ) ){
				$team_name = $jsonData[ 'team_name' ];
			}

			$team_name_kana = "";
			if( isset( $jsonData[ 'team_name_kana' ] ) ){
				$team_name_kana = $jsonData[ 'team_name_kana' ];
			}

			$team_manager = "";
			if( isset( $jsonData[ 'team_manager' ] ) ){
				$team_manager = $jsonData[ 'team_manager' ];
			}

			$team_manager_kana = "";
			if( isset( $jsonData[ 'team_manager_kana' ] ) ){
				$team_manager_kana = $jsonData[ 'team_manager_kana' ];
			}

			$team_manager_tel = "";
			if( isset( $jsonData[ 'team_manager_tel' ] ) ){
				$team_manager_tel = $jsonData[ 'team_manager_tel' ];
			}

			$team_manager_email = "";
			if( isset( $jsonData[ 'team_manager_email' ] ) ){
				$team_manager_email = $jsonData[ 'team_manager_email' ];
			}

			$tournament_contact = "";
			if( isset( $jsonData[ 'tournament_contact' ] ) ){
				$tournament_contact = $jsonData[ 'tournament_contact' ];
			}

			$tournament_contact_kana = "";
			if( isset( $jsonData[ 'tournament_contact_kana' ] ) ){
				$tournament_contact_kana = $jsonData[ 'tournament_contact_kana' ];
			}

			$tournament_contact_tel = "";
			if( isset( $jsonData[ 'tournament_contact_tel' ] ) ){
				$tournament_contact_tel = $jsonData[ 'tournament_contact_tel' ];
			}

			$tournament_contact_email = "";
			if( isset( $jsonData[ 'tournament_contact_email' ] ) ){
				$tournament_contact_email = $jsonData[ 'tournament_contact_email' ];
			}

			$player_count = "";
			if( isset( $jsonData[ 'player_count' ] ) ){
				$player_count = $jsonData[ 'player_count' ];
			}

			$manager_count = "";
			if( isset( $jsonData[ 'manager_count' ] ) ){
				$manager_count = $jsonData[ 'manager_count' ];
			}

			$parent_count = "";
			if( isset( $jsonData[ 'parent_count' ] ) ){
				$parent_count = $jsonData[ 'parent_count' ];
			}

			$mycar_preference = "";
			if( isset( $jsonData[ 'mycar_preference' ] ) ){
				$mycar_preference = $jsonData[ 'mycar_preference' ] == "yes" ? "はい" : "";
			}

			$mycar_count = "";
			if( isset( $jsonData[ 'mycar_count' ] ) ){
				$mycar_count = $jsonData[ 'mycar_count' ];
			}

			$microbus_preference = "";
			if( isset( $jsonData[ 'microbus_preference' ] ) ){
				$microbus_preference = $jsonData[ 'microbus_preference' ] == "yes" ? "はい" : "";
			}

			$microbus_count = "";
			if( isset( $jsonData[ 'microbus_count' ] ) ){
				$microbus_count = $jsonData[ 'microbus_count' ];
			}

			$public_transport_preference = "";
			if( isset( $jsonData[ 'public_transport_preference' ] ) ){
				$public_transport_preference = $jsonData[ 'public_transport_preference' ] == "yes" ? "はい" : "";
			}

			$lunch_day1_lunchbox = "";
			if( isset( $jsonData[ 'lunch_day1_lunchbox' ] ) ){
				$lunch_day1_lunchbox = $jsonData[ 'lunch_day1_lunchbox' ] == "yes" ? "希望" : "";
			}

			$lunch_day1_stall = "";
			if( isset( $jsonData[ 'lunch_day1_stall' ] ) ){
				$lunch_day1_stall = $jsonData[ 'lunch_day1_stall' ] == "yes" ? "希望" : "";
			}

			$lunch_day2_lunchbox = "";
			if( isset( $jsonData[ 'lunch_day2_lunchbox' ] ) ){
				$lunch_day2_lunchbox = $jsonData[ 'lunch_day2_lunchbox' ] == "yes" ? "希望" : "";
			}

			$lunch_day2_stall = "";
			if( isset( $jsonData[ 'lunch_day2_stall' ] ) ){
				$lunch_day2_stall = $jsonData[ 'lunch_day2_stall' ] == "yes" ? "希望" : "";
			}

			$lunch_day3_lunchbox = "";
			if( isset( $jsonData[ 'lunch_day3_lunchbox' ] ) ){
				$lunch_day3_lunchbox = $jsonData[ 'lunch_day3_lunchbox' ] == "yes" ? "希望" : "";
			}

			$lunch_day3_stall = "";
			if( isset( $jsonData[ 'lunch_day3_stall' ] ) ){
				$lunch_day3_stall = $jsonData[ 'lunch_day3_stall' ] == "yes" ? "希望" : "";
			}

			$team_achievements = "";
			if( isset( $jsonData[ 'team_achievements' ] ) ){
				$team_achievements = $jsonData[ 'team_achievements' ];
			}

			$team_pr = "";
			if( isset( $jsonData[ 'team_pr' ] ) ){
				$team_pr = $jsonData[ 'team_pr' ];
			}

			$information_source = '';
			if ( isset( $jsonData[ 'information_source' ] ) ) {
				switch ( $jsonData[ 'information_source' ] ) {
					case 'mouth':
						$information_source = 'クチコミ';
						break;
					case 'sns':
						$information_source = 'SNS';
						break;
					case 'homepage':
						$information_source = 'ホームページ';
						break;
					case 'introduction':
						$information_source = '知人の紹介';
						break;
					case 'other':
						$information_source = 'その他';
						break;
				}
			}

			$notes = "";
			if( isset( $jsonData[ 'notes' ] ) ){
				$notes = $jsonData[ 'notes' ];
			}

			fputcsv( $output, [
				$row[ 'team_id' ],
				$tournament_agree,
				$tournament_lottery_agree,
				$waitlist_preference,
				$shiga_preference,
				$stay_preference,
				$stay_agree,
				$lunch_preference,
				$dinner_preference,
				$stay_player_count,
				$stay_coach_count,
				$stay_other_count,
				$stay_car_count,
				$allergy,
				$allergy_info,
				$team_name,
				$team_name_kana,
				$team_manager,
				$team_manager_kana,
				$team_manager_tel,
				$team_manager_email,
				$tournament_contact,
				$tournament_contact_kana,
				$tournament_contact_tel,
				$tournament_contact_email,
				$player_count,
				$manager_count,
				$parent_count,
				$mycar_preference,
				$mycar_count,
				$microbus_preference,
				$microbus_count,
				$public_transport_preference,
				$lunch_day1_lunchbox,
				$lunch_day1_stall,
				$lunch_day2_lunchbox,
				$lunch_day2_stall,
				$lunch_day3_lunchbox,
				$lunch_day3_stall,
				$team_achievements,
				$team_pr,
				$information_source,
				$notes
			], ',', '"', '', "\r\n" );
		}

		fclose( $output );
		exit;


	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $dataTable;
}

# ==========================================================
# ページネーションチェック処理
# ==========================================================
function checkPageNation() {

	global $g_Log;
	$g_Log->notice( "ページネーションチェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 表示行数
	$pageRows	  = ( int )( $_POST[ 'page_rows' ] ?? $_SESSION[ 'FORM_PAGE_ROWS' ] ?? 2 );
	$_SESSION[ 'FORM_PAGE_ROWS' ]     = $pageRows;
	$g_Log->notice( "[ FORM_PAGE_ROWS ] : $pageRows => " . $_SESSION[ 'FORM_PAGE_ROWS' ], __FUNCTION__, basename( __FILE__ ) );

	// 総件数
	$recordsCount = getTournamentCount();
	$pageTotal    = ( int )ceil( $recordsCount / $pageRows );
	$_SESSION[ 'FORM_PAGE_TOTAL' ]    = $pageTotal;
	$g_Log->notice( "[ FORM_PAGE_TOTAL ] : $pageTotal => " . $_SESSION[ 'FORM_PAGE_TOTAL' ], __FUNCTION__, basename( __FILE__ ) );
	
	// 現在ページ
	$pageCurrent  = ( int )( $_POST[ 'page_current' ] ?? $_SESSION[ 'FORM_PAGE_CURRENT' ] ?? 1 );
	$_SESSION[ 'FORM_PAGE_CURRENT' ]  = $pageCurrent;
	$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );

	// ページの最初
	if ( isset( $_POST[ 'page_top' ] ) ) {
		$pageCurrent = 1;
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pageTop      = 1;
	$_SESSION[ 'FORM_PAGE_TOP' ]      = $pageTop;
	$g_Log->notice( "[ FORM_PAGE_TOP ] : $pageTop => " . $_SESSION[ 'FORM_PAGE_TOP' ], __FUNCTION__, basename( __FILE__ ) );

	// ページの最後
	if ( isset( $_POST[ 'page_last' ] ) ) {
		$pageCurrent = $pageTotal;
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pageLast     = $pageTotal;
	$_SESSION[ 'FORM_PAGE_LAST' ]     = $pageLast;
	$g_Log->notice( "[ FORM_PAGE_LAST ] : $pageLast => " . $_SESSION[ 'FORM_PAGE_LAST' ], __FUNCTION__, basename( __FILE__ ) );

	// ページ前へ
	if ( isset( $_POST[ 'page_previous' ] ) ) {
		$pageCurrent = max( 1, $pageCurrent - 1 );
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	if ( isset( $_POST[ 'page_next' ] ) ) {
		$pageCurrent = min( $pageTotal, $pageCurrent + 1 );
		$_SESSION[ 'FORM_PAGE_CURRENT' ] = $pageCurrent;
		$g_Log->notice( "[ FORM_PAGE_CURRENT ] : $pageCurrent => " . $_SESSION[ 'FORM_PAGE_CURRENT' ], __FUNCTION__, basename( __FILE__ ) );
	}

	$pagePrevious = max( 1, $pageCurrent - 1 );
	$_SESSION[ 'FORM_PAGE_PREVIOUS' ] = $pagePrevious;
	$g_Log->notice( "[ FORM_PAGE_PREVIOUS ] : $pagePrevious => " . $_SESSION[ 'FORM_PAGE_PREVIOUS' ], __FUNCTION__, basename( __FILE__ ) );

	$pageNext     = min( $pageTotal, $pageCurrent + 1 );
	$_SESSION[ 'FORM_PAGE_NEXT' ]     = $pageNext;
	$g_Log->notice( "[ FORM_PAGE_NEXT ] : $pageNext => " . $_SESSION[ 'FORM_PAGE_NEXT' ], __FUNCTION__, basename( __FILE__ ) );

	// 表示オフセット
	$pageOffset = ( $pageCurrent - 1 ) * $pageRows;
	$_SESSION[ 'FORM_PAGE_OFFSET' ]   = $pageOffset;
	$g_Log->notice( "[ FORM_PAGE_OFFSET ] : $pageOffset => " . $_SESSION[ 'FORM_PAGE_OFFSET' ], __FUNCTION__, basename( __FILE__ ) );

	return true;
}

# ==========================================================
# 追加チェック処理
# ==========================================================
function checkAdd() {

	global $g_Log;
	$g_Log->notice( "追加チェック処理", __FUNCTION__, basename( __FILE__ ) );

	// 追加ボタンでサブミットされてなければ処理をスキップ
	if( ! checkRequest( 'add_button' ) ) {
		return true;
	}

	if( ! checkForm( "ADD" ) ) {
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
	if( ! checkRequest( 'update_button' ) ) {
		return true;
	}

	if( ! checkForm( "UPDATE" ) ) {
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

	// 削除対象のID
	if ( deleteTeamId( $_POST[ 'delete_button' ] ) ) {
		$g_Log->notice( "チーム登録内容を削除しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
	} else {
		$g_Log->notice( "チーム登録内容の削除に失敗しました： delete_button = " . $_POST[ 'delete_button' ], __FUNCTION__, basename( __FILE__ ) );
		$_SESSION[ 'FORM_ERR_MSG' ] = "チーム登録内容の削除に失敗しました。";
		return false;
	}

	return true;
}

# ==========================================================
# フォームチェック処理
# ==========================================================
function checkForm( $p_action = "" ) {
	
	global $g_Log;
	$g_Log->notice( "フォームチェック処理 : $p_action", __FUNCTION__, basename( __FILE__ ) );

	switch ( $p_action ) {
		case "ADD":
			$g_Log->notice( "追加のフォームチェック", __FUNCTION__, basename( __FILE__ ) );
			break;
		case "UPDATE":
			$g_Log->notice( "更新のフォームチェック", __FUNCTION__, basename( __FILE__ ) );
			break;
		default:
			$g_Log->notice( "不明なアクションのフォームチェック : $p_action", __FUNCTION__, basename( __FILE__ ) );
			return false;
	}

	// 大会ID
	if( ! checkRequest( 'team_tournament_id', 'POST' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会IDエラー";
		$g_Log->notice( "大会IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_tournament_id' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会IDエラー";
		$g_Log->notice( "大会IDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チームID(更新時のみチェック)
	if( $p_action === "UPDATE" ) {
		if( ! checkRequest( 'team_id' ) ) {
			$_SESSION[ 'FORM_ERR_MSG' ] = "チームIDエラー";
			$g_Log->notice( "チームIDのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}

	// チーム名
	if( ! checkRequest( 'team_name' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "チーム名エラー";
		$g_Log->notice( "チーム名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_name' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "チーム名エラー";
		$g_Log->notice( "チーム名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム責任者名
	if( ! checkRequest( 'team_manager' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "責任者名エラー";
		$g_Log->notice( "チーム責任者名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_manager' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "責任者名エラー";
		$g_Log->notice( "チーム責任者名のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム連絡先
	if( ! checkRequest( 'team_tel' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "電話番号エラー";
		$g_Log->notice( "電話番号のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_tel' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "電話番号エラー";
		$g_Log->notice( "電話番号のチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム連絡先
	if( ! checkRequest( 'team_email' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "メールアドレスエラー";
		$g_Log->notice( "メールアドレスのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_email' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "メールアドレスエラー";
		$g_Log->notice( "メールアドレスのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// アクセスコード
	if( ! checkRequest( 'team_access_code' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "アクセスコードエラー";
		$g_Log->notice( "アクセスコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_access_code' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "アクセスコードエラー";
		$g_Log->notice( "アクセスコードのチェックに失敗しました", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# メール送信処理
# ==========================================================
function sendMail_Allow( $p_tournamentId ) {

	global $g_Log;
	$g_Log->notice( "メール送信処理 : TOURNAMENT_ID = " . $p_tournamentId, __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = $p_tournamentId;

	getTournament1Teams( $tournament_id );
	getTournament1Allow( $tournament_id );

	if( $_SESSION[ 'FORM_TEAM_ALLOW_COUNT' ] != $_SESSION[ 'FORM_TOURNAMENT1_TEAMS' ] ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = "大会の参加チーム数が一致しません。";
		$g_Log->notice( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$postedAt = date( 'Y-m-d H:i:s' );

	// メールアドレス取得
	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   team_email AS email ";
	$SQL .= "   FROM baseball_team ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND tournament_id = :tournament_id ";
	$SQL .= "    AND tournament1_attend = 1 ";
	$SQL .= " UNION ALL ";
	$SQL .= " SELECT ";
	$SQL .= "   email ";
	$SQL .= "   FROM baseball_user ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND role_level IN ( 100, 1000 ) ";
	
	$SQL_Parameters = array(
		"tournament_id" => $tournament_id
	);
	
	$toArray = array();
	$bccArray = array();

	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'FORM_ERR_MSG' ] = '管理者のメールアドレスが登録されていません。';
			$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach ( $dataTable as $row ) {
			if ( filter_var( $row[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
				$g_Log->notice( "有効なメールアドレスを取得しました : " . $row[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
				$bccArray[] = $row[ 'email' ];
			} else {
				$g_Log->warning( "無効なメールアドレスをスキップしました : " . $row[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
				continue;
			}
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'メールアドレスの取得処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 大会名の取得
	$tournamentName = '';
	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   tournament_id ";
	$SQL .= " , CONCAT( tournament_title, ' ', tournament_text ) AS tournament_name ";
	$SQL .= " FROM baseball_tournament ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND tournament_id = :tournament_id ";
	
	$SQL_Parameters = array(
		"tournament_id" => $tournament_id
	);

	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'FORM_ERR_MSG' ] = '大会情報が登録されていません。';
			$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . $tournament_id, __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach ( $dataTable as $row ) {
			$tournamentName = $row[ 'tournament_name' ];
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会情報の取得処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$toAddress = '';
	$ccAddress = '';
	$bccAddress = implode( ',', $bccArray );

	// メール送信処理
	$mailer = new Class_PHPMailer();
	$subject = "【HPL】" . $tournamentName . " - " . $postedAt;
	$message = "";
	$message .= "<p>※このメールアドレスは送信専用です。</p>";
	$message .= "<br>";
	$message .= "<p>この度は、お申込みいただき誠に有難うございます。</p>";
	$message .= "<p>抽選の結果、本大会へのエントリーが確定致しましたのでメールにてお知らせ致します。</p>";
	$message .= "<p>大会出場にあたり下記内容の書類の提出をお願い申し上げます。</p>";
	$message .= "<p>・チーム登録簿</p>";
	$message .= "<p>・大会グッズ販売</p>";
	$message .= "<p>・お弁当申込書</p>";
	$message .= "<br>";
	$message .= "<p>宿泊利用の団体様は、宿泊の手続きをして頂きます。</p>";
	$message .= "<p>有限会社第一観光サービス</p>";
	$message .= "<p>TEL077-583-6752 FAX077-583-4747</p>";
	$message .= "<p>shigatabi@joshitrip.net</p>";
	$message .= "<p>上記までご連絡お願い致します。</p>";
	$message .= "<p>「ホタルのまちMORIYAMA2026の大会に出場する、〇〇〇（チーム名）です」</p>";
	$message .= "<p>とお伝えいただけると、スムーズに手続きに進めます。</p>";
	$message .= "<br>";
	$message .= "<p>ご質問等がありましたら、お気軽にお問い合わせください。</p>";

	if ( $mailer->sendMail( $toAddress, $subject, $message, $ccAddress, $bccAddress ) ) {
		$g_Log->notice( "大会参加申し込み内容を送信しました [TO: " . $toAddress . ", CC: " . $ccAddress . ", BCC: " . $bccAddress . "]", __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会参加申し込み内容の送信に失敗しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : [TO: " . $toAddress . ", CC: " . $ccAddress . ", BCC: " . $bccAddress . "]", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$g_Log->notice( "メール送信処理が完了しました : [TO: " . $toAddress . ", CC: " . $ccAddress . ", BCC: " . $bccAddress . "]", __FUNCTION__, basename( __FILE__ ) );

	createGame( $tournament_id );

	$_SESSION[ 'FORM_ERR_MSG' ] = '大会の参加メールを送信しました。';

	return true;
}

# ==========================================================
# メール送信処理
# ==========================================================
function sendMail_Deny( $p_tournamentId ) {

	global $g_Log;
	$g_Log->notice( "メール送信処理 : TOURNAMENT_ID = " . $p_tournamentId, __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = $p_tournamentId;

	$postedAt = date( 'Y-m-d H:i:s' );

	// メールアドレス取得
	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   team_email AS email ";
	$SQL .= "   FROM baseball_team ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND tournament_id = :tournament_id ";
	$SQL .= "    AND tournament1_attend = 0 ";
	$SQL .= " UNION ALL ";
	$SQL .= " SELECT ";
	$SQL .= "   email ";
	$SQL .= "   FROM baseball_user ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND role_level IN ( 100, 1000 ) ";
	
	$SQL_Parameters = array(
		"tournament_id" => $tournament_id
	);
	
	$toArray = array();
	$bccArray = array();

	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'FORM_ERR_MSG' ] = '管理者のメールアドレスが登録されていません。';
			$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach ( $dataTable as $row ) {
			if ( filter_var( $row[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
				 $g_Log->notice( "有効なメールアドレスを取得しました : " . $row[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
				 $bccArray[] = $row[ 'email' ];
			} else {
				$g_Log->warning( "無効なメールアドレスをスキップしました : " . $row[ 'email' ], __FUNCTION__, basename( __FILE__ ) );
				continue;
			}
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'メールアドレスの取得処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 大会名の取得
	$tournamentName = '';
	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   tournament_id ";
	$SQL .= " , CONCAT( tournament_title, ' ', tournament_text ) AS tournament_name ";
	$SQL .= " FROM baseball_tournament ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND tournament_id = :tournament_id ";
	
	$SQL_Parameters = array(
		"tournament_id" => $tournament_id
	);

	try {

		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		if( count( $dataTable ) == 0 ){
			$_SESSION[ 'FORM_ERR_MSG' ] = '大会情報が登録されていません。';
			$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . $tournament_id, __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		foreach ( $dataTable as $row ) {
			$tournamentName = $row[ 'tournament_name' ];
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会情報の取得処理でエラーが発生しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . "\n" . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	$toAddress = '';
	$ccAddress = '';
	$bccAddress = implode( ',', $bccArray );

	// メール送信処理
	$mailer = new Class_PHPMailer();
	$subject = "【HPL】" . $tournamentName . " - " . $postedAt;
	$message = "";
	$message .= "<p>※このメールアドレスは送信専用です。</p>";
	$message .= "<br>";
	$message .= "<p>この度は、お申込みいただき誠に有難うございます。</p>";
	$message .= "<p>抽選の結果、本大会へのエントリーは申し訳ございませんが見送らさせて頂きます。</p>";
	$message .= "<p>来年も大会実施予定ですので、よろしくお願いいたします。</p>";
	
	if ( $mailer->sendMail( $toAddress, $subject, $message, $ccAddress, $bccAddress ) ) {
		$g_Log->notice( "大会参加申し込み内容を送信しました [TO: " . $toAddress . ", CC: " . $ccAddress . ", BCC: " . $bccAddress . "]", __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会参加申し込み内容の送信に失敗しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : [TO: " . $toAddress . ", CC: " . $ccAddress . ", BCC: " . $bccAddress . "]", __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$g_Log->notice( "メール送信処理が完了しました : [TO: " . $toAddress . ", CC: " . $ccAddress . ", BCC: " . $bccAddress . "]", __FUNCTION__, basename( __FILE__ ) );
	$_SESSION[ 'FORM_ERR_MSG' ] = '大会の落選メールを送信しました。';

	return true;
}

# ==========================================================
# チーム内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function createGame( $p_tournamentId){

	global $g_Log;
	$g_Log->notice( "チーム内容追加処理 : TOURNAMENT ID = " . $p_tournamentId, __FUNCTION__, basename( __FILE__ ) );

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_game ";
	$SQL .= " SET is_enabled = 0 ";
	$SQL .= "   , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= "   , updated_by = :updated_by ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND tournament_id = :tournament_id ";
	$SQL .= "   AND game_class = 1 "; // game_class = 1 は予選リーグの試合を示す
	
	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
		'updated_by' => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '総当たり戦の試合削除に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// SQL文作成
	$SQL = "";
	$SQL .= "SELECT ";
	$SQL .= "  tournament_id ";
	$SQL .= ", team_id ";
	$SQL .= ", team_name ";
	$SQL .= ", team_manager ";
	$SQL .= ", team_contact ";
	$SQL .= ", team_tel ";
	$SQL .= ", team_email ";
	$SQL .= ", team_access_cd ";
	$SQL .= ", tournament1_attend ";
	$SQL .= ", tournament2_attend ";
	$SQL .= ", tournament3_attend ";
	$SQL .= ", CASE tournament1_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament1_attend_name ";
	$SQL .= ", CASE tournament2_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament2_attend_name ";
	$SQL .= ", CASE tournament3_attend WHEN 1 THEN '参加' ELSE '不参加' END AS tournament3_attend_name ";
	$SQL .= "FROM baseball_team ";
	$SQL .= "WHERE is_enabled = 1 ";
	$SQL .= "  AND tournament_id = :tournament_id ";
	$SQL .= "  AND tournament1_attend = 1 ";
	$SQL .= "ORDER BY team_name ASC ";
	$SQL .= ", team_id ASC ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	$dataTable2 = $dataTable;
	$insertValues = array();

	for( $i = 0; $i < 8; $i++ ) {	// game_blockは予選リーグのブロックを示す（今回は最大8ブロックまで対応）
		$gameBlock = $i + 1;
		$gameCount = 1;
		for( $j = 0; $j < 3; $j++ ) { // game_countは同一カードの試合数を示す（今回は総当たり戦のため最大6試合まで対応）
			$gameCount = $j + 1;
			$values = "";
			$values .= " ( {$tournamentId} ";
			$values .= " , 1 ";	// game_class = 1 は予選リーグの試合を示す
			$values .= " , {$gameBlock} ";	// game_block は予選リーグのブロックを示す
			$values .= " , {$gameCount} ";	// game_count は同一カードの試合数を示す（今回は総当たり戦のため1）
			$values .= " , '第{$gameBlock}ブロック 第{$gameCount}試合' ";	// game_name は未定のため空文字
			$values .= " , CAST('1000-01-01 00:00:00' AS DATETIME) ";	// game_date は未定のため現在のタイムスタンプを使用
			$values .= " , '' ";	// game_place は未定のためNULL
			$values .= " , 0 ";	// winner_id は試合前のため0
			$values .= " , 0 ";	// loser_id は試合前のため0
			$values .= " , 0 ";	// team1_id は試合前のためチームIDをそのまま使用
			$values .= " , 0 ";	// team2_id は試合前のためチームIDをそのまま使用
			$values .= " , '' ";	// team1_name は試合前のためチーム名をそのまま使用
			$values .= " , '' ";	// team2_name は試合前のためチーム名をそのまま使用
			$values .= " , 0 ";	// team1_score は試合前のためスコアは0
			$values .= " , 0 ";	// team2_score は試合前のためスコアは0
			$values .= " , 0 ";	// team1_last_game_id は試合前のため最後の試合IDは0
			$values .= " , 0 ";	// team2_last_game_id は試合前のため最後の試合IDは0
			$values .= " , 1 ";	// is_enabledは新規登録の際は常に1
			$values .= " , CURRENT_TIMESTAMP ";	// created_at は現在のタイムスタンプを使用
			$values .= " , '" . basename( __FILE__ ) . "' ";	// created_by は作成者を使用
			$values .= " , CURRENT_TIMESTAMP ";	// updated_at は現在のタイムスタンプを使用
			$values .= " , '" . basename( __FILE__ ) . "' ";	// updated_by は更新者を使用
			$values .= " ) ";
			$insertValues[] = $values;
		}
	}

	// SQL文作成
	$SQL = "";
	$SQL .= " INSERT INTO baseball_game ";
	$SQL .= " ( tournament_id ";
	$SQL .= " , game_class ";
	$SQL .= " , game_block ";
	$SQL .= " , game_count ";
	$SQL .= " , game_name ";
	$SQL .= " , game_date ";
	$SQL .= " , game_place ";
	$SQL .= " , winner_id ";
	$SQL .= " , loser_id ";
	$SQL .= " , team1_id ";
	$SQL .= " , team2_id ";
	$SQL .= " , team1_name ";
	$SQL .= " , team2_name ";
	$SQL .= " , team1_score ";
	$SQL .= " , team2_score ";
	$SQL .= " , team1_last_game_id ";
	$SQL .= " , team2_last_game_id ";
	$SQL .= " , is_enabled ";
	$SQL .= " , created_at ";
	$SQL .= " , created_by ";
	$SQL .= " , updated_at ";
	$SQL .= " , updated_by ";
	$SQL .= " ) ";
	$SQL .= " VALUES ";
	$flagFirst = true;
	foreach ( $insertValues as $values ) {
		if ( ! $flagFirst ) {
			$SQL .= " , ";
		}
		$flagFirst = false;
		$SQL .= $values;
		$g_Log->notice( "SQL Values : " . $values, __FUNCTION__, basename( __FILE__ ) );
	}
	
	$SQL_Parameters = array(
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# セット処理
# ==========================================================
function setProc() {

	global $g_Log;
	$g_Log->notice( "セット処理", __FUNCTION__, basename( __FILE__ ) );

	$_SESSION[ 'FORM_VIEW_STATE' ] = checkNumeric( $_POST[ 'view_state' ] );
	$_SESSION[ 'FORM_TOURNAMENT_ID' ] = checkNumeric( $_POST[ 'tournament_id' ] );
	$_SESSION[ 'FORM_TEAM_ID' ] = checkNumeric( $_POST[ 'team_id' ] );
	$_SESSION[ 'FORM_TEAM_NAME' ] = checkText( $_POST[ 'team_name' ] );
	$_SESSION[ 'FORM_TEAM_MANAGER' ] = checkText( $_POST[ 'team_manager' ] );
	$_SESSION[ 'FORM_TEAM_TEL' ] = checkText( $_POST[ 'team_tel' ] );
	$_SESSION[ 'FORM_TEAM_EMAIL' ] = checkText( $_POST[ 'team_email' ] );
	$_SESSION[ 'FORM_TEAM_ACCESS_CODE' ] = checkText( $_POST[ 'team_access_code' ] );
	$_SESSION[ 'FORM_BUTTON_ADD' ] = checkText( $_POST[ 'add_button' ], false );
	$_SESSION[ 'FORM_BUTTON_UPDATE' ] = checkText( $_POST[ 'update_button' ], false );
	$_SESSION[ 'FORM_BUTTON_DELETE' ] = checkText( $_POST[ 'delete_button' ], false );

	// 追加処理
	if( isset( $_POST[ 'add_button' ] ) && $_POST[ 'add_button' ] !== "" ) {
		if ( addTeam() ) {
			$g_Log->notice( "大会登録内容を追加しました", __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "大会登録内容の追加に失敗しました", __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会登録内容の追加に失敗しました。";
			return false;
		}
	}

	// 更新処理
	if( isset( $_POST[ 'update_button' ] ) && $_POST[ 'update_button' ] !== "" ) {
		if ( updateTeam() ) {
			$g_Log->notice( "大会登録内容を更新しました： tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
		} else {
			$g_Log->notice( "大会登録内容の更新に失敗しました： tournament_id = " . $_POST[ 'tournament_id' ], __FUNCTION__, basename( __FILE__ ) );
			$_SESSION[ 'FORM_ERR_MSG' ] = "大会登録内容の更新に失敗しました。";
			return false;
		}
	}

	return true;
}

# ==========================================================
# チーム内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function addTeam(){

	global $g_Log;
	$g_Log->notice( "チーム内容追加処理", __FUNCTION__, basename( __FILE__ ) );

	$team_tournament_id = checkNumeric( $_POST[ 'team_tournament_id' ] );
	$team_id            = ""; // チームIDは自動採番のため空文字
	$team_name          = checkText( $_POST[ 'team_name' ] );
	$team_manager       = checkText( $_POST[ 'team_manager' ] );
	$team_tel           = checkText( $_POST[ 'team_tel' ] );
	$team_email         = checkText( $_POST[ 'team_email' ] );
	$team_access_cd     = checkText( $_POST[ 'team_access_code' ] );
	$team_tournament1_attend = isset( $_POST[ 'team_tournament1_attend' ] ) ? 1 : 0;

	// SQL文作成
	$SQL = "";
	$SQL .= " INSERT INTO baseball_team ";
	$SQL .= " ( team_name ";
	$SQL .= " , team_manager ";
	$SQL .= " , team_contact ";
	$SQL .= " , team_tel ";
	$SQL .= " , team_email ";
	$SQL .= " , team_access_cd ";
	$SQL .= " , tournament_id ";
	$SQL .= " , tournament1_attend ";
	$SQL .= " , is_enabled ";
	$SQL .= " , created_at ";
	$SQL .= " , created_by ";
	$SQL .= " , updated_at ";
	$SQL .= " , updated_by ";
	$SQL .= " ) ";
	$SQL .= " VALUES ";
	$SQL .= " ( :team_name ";
	$SQL .= " , :team_manager ";
	$SQL .= " , CONCAT( 'TEL: ', :team_tel, ' / EMAIL: ', :team_email ) "; // 連絡先は責任者名、電話番号、メールアドレスをスペース区切りで結合
	$SQL .= " , :team_tel ";
	$SQL .= " , :team_email ";
	$SQL .= " , :team_access_cd ";
	$SQL .= " , :tournament_id ";
	$SQL .= " , :tournament1_attend ";
	$SQL .= " , 1 "; // is_enabledは新規登録の際は常に1
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :created_by ";
	$SQL .= " , CURRENT_TIMESTAMP ";
	$SQL .= " , :updated_by ";
	$SQL .= " ) ";
	
	$SQL_Parameters = array(
		'team_name' => $team_name,
		'team_manager' => $team_manager,
		'team_tel' => $team_tel,
		'team_email' => $team_email,
		'team_access_cd' => $team_access_cd,
		'tournament_id' => $team_tournament_id,
		'created_by' => basename( __FILE__ ),
		'updated_by' => basename( __FILE__ ),
		'tournament1_attend' => $team_tournament1_attend,
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の追加に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# チーム登録内容更新処理
# @param int $p_teamId 更新対象のチームID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function updateTeam(){

	global $g_Log;
	$g_Log->notice( "チーム登録内容更新処理", __FUNCTION__, basename( __FILE__ ) );

	$team_tournament_id = checkNumeric( $_POST[ 'team_tournament_id' ] );
	$team_id            = checkNumeric( $_POST[ 'team_id' ] );
	$team_name          = checkText( $_POST[ 'team_name' ] );
	$team_manager       = checkText( $_POST[ 'team_manager' ] );
	$team_tel           = checkText( $_POST[ 'team_tel' ] );
	$team_email         = checkText( $_POST[ 'team_email' ] );
	$team_access_cd     = checkText( $_POST[ 'team_access_code' ] );
	$team_tournament1_attend = isset( $_POST[ 'team_tournament1_attend' ] ) ? 1 : 0;
	
	// SQL文作成
	$SQL = "";
	$SQL .= " UPDATE baseball_team ";
	$SQL .= " SET team_name = :team_name ";
	$SQL .= " , team_manager = :team_manager ";
	$SQL .= " , team_contact = CONCAT( 'TEL: ', :team_tel, ' / EMAIL: ', :team_email ) "; // 連絡先は責任者名、電話番号、メールアドレスをスペース区切りで結合
	$SQL .= " , team_tel = :team_tel ";
	$SQL .= " , team_email = :team_email ";
	$SQL .= " , team_access_cd = :team_access_cd ";
	$SQL .= " , tournament1_attend = :tournament1_attend ";
	$SQL .= " , updated_at = CURRENT_TIMESTAMP ";
	$SQL .= " , updated_by = :updated_by ";
	$SQL .= " WHERE is_enabled = 1 ";
	$SQL .= "   AND tournament_id = :tournament_id ";
	$SQL .= "   AND team_id = :team_id ";
	
	$SQL_Parameters = array(
		'team_id' => $team_id,
		'team_name' => $team_name,
		'team_manager' => $team_manager,
		'team_tel' => $team_tel,
		'team_email' => $team_email,
		'team_access_cd' => $team_access_cd,
		'tournament_id' => $team_tournament_id,
		'tournament1_attend' => $team_tournament1_attend,
		'updated_by' => basename( __FILE__ ),
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム登録内容の更新に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# 試合登録内容取得処理
# @param int $p_tournamentId 大会ID
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getTournament1Teams( $p_tournamentId ){

	global $g_Log;
	$g_Log->notice( "試合結果登録内容取得処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );

	$tournament1Teams = 0;
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return $tournament1Teams;
	}

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   tournament_id ";
	$SQL .= " , tournament_title ";
	$SQL .= " , tournament_text ";
	$SQL .= " , tournament_start_date ";
	$SQL .= " , tournament_end_date ";
	$SQL .= " , tournament_parent_id ";
	$SQL .= " , tournament1_teams ";
	$SQL .= " , tournament1_text ";
	$SQL .= " , tournament2_teams ";
	$SQL .= " , tournament2_text ";
	$SQL .= " , tournament3_teams ";
	$SQL .= " , tournament3_text ";
	$SQL .= " , tournament_attachment_id ";
	$SQL .= "   FROM baseball_tournament ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND tournament_id = :tournament_id ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach ( $dataTable as $row ) {
			$tournament1Teams = $row[ 'tournament1_teams' ];
			$_SESSION[ 'FORM_TOURNAMENT1_TEAMS' ] = $row[ 'tournament1_teams' ];
			if( $row[ 'tournament1_text' ] !== "" ) {
				$_SESSION[ 'FORM_TOURNAMENT1_TEXT' ] = $row[ 'tournament1_text' ];
			}
		}
		$g_Log->notice( "[ FORM_TOURNAMENT1_TEAMS ] : " . $_SESSION[ 'FORM_TOURNAMENT1_TEAMS' ], __FUNCTION__, basename( __FILE__ ) );
		$g_Log->notice( "[ FORM_TOURNAMENT1_TEXT ] : " . $_SESSION[ 'FORM_TOURNAMENT1_TEXT' ], __FUNCTION__, basename( __FILE__ ) );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合結果登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $tournament1Teams;
}

# ==========================================================
# 試合登録内容取得処理
# @param int $p_tournamentId 大会ID
# @param int $p_pageCurrent 現在のページ番号
# @param int $p_pageRows 1ページの表示行数
# @return array 取得成功すればデータ配列、失敗すればfalse
# ==========================================================
function getTournament1Allow( $p_tournamentId ){

	global $g_Log;
	$g_Log->notice( "試合結果登録内容取得処理 : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );

	$tournament1Teams = 0;
	
	// 大会IDチェック
	if( ! is_numeric( $p_tournamentId ) ){
		$g_Log->notice( "大会IDが数値ではありません : tournamentId={$p_tournamentId}", __FUNCTION__, basename( __FILE__ ) );
		return $tournament1Teams;
	}

	$tournamentId = ( int )$p_tournamentId;

	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   tournament_id ";
	$SQL .= " , COUNT( tournament1_attend ) AS tournament1_attend_count "; // 総当たり戦参加チーム数は、tournament1_attendの件数でカウント
	$SQL .= "   FROM baseball_team ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND tournament_id = :tournament_id ";
	$SQL .= "    AND tournament1_attend = 1 ";
	$SQL .= "  GROUP BY tournament_id ";

	$SQL_Parameters = array(
		'tournament_id' => $tournamentId,
	);

	$dataTable = array();

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach ( $dataTable as $row ) {
			$tournament1Teams = $row[ 'tournament1_attend_count' ];
			$_SESSION[ 'FORM_TEAM_ALLOW_COUNT' ] = $row[ 'tournament1_attend_count' ];
		}
		$g_Log->notice( "[ FORM_TEAM_ALLOW_COUNT ] : " . $_SESSION[ 'FORM_TEAM_ALLOW_COUNT' ], __FUNCTION__, basename( __FILE__ ) );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '試合結果登録内容の取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
	}

	return $tournament1Teams;
}

# ==========================================================
# 終了処理
# ==========================================================
function endProc() {

	global $g_Log;
	$g_Log->notice( "終了処理", __FUNCTION__, basename( __FILE__ ) );

	getTournament1Teams( $_SESSION[ 'FORM_TOURNAMENT_ID' ] );
	getTournament1Allow( $_SESSION[ 'FORM_TOURNAMENT_ID' ] );

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
	// チームメンテナンスの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_teammaintenance.php";
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

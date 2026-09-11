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
# 初期設定
# ==========================================================
function initProc() {

	global $g_Log;
	$g_Log->notice( "初期処理", __FUNCTION__, basename( __FILE__ ) );

	// セッション変数の初期化
	$sessionList = [
		'FORM_ERR_MSG'        => '',		// エラーメッセージ
		'FROM_VIEW_STATE'    => 0,		// 表示ステータス(0: 初期画面)
		'FORM_BUTTON_SEND'   => 'SEND',	// 送信ボタン
		'FROM_TOURNAMENT_ID' => '',		// トーナメントID
		'FORM_TEAM_NAME'     => '',		// チーム名
		'FORM_TEAM_MANAGER'  => '',		// 責任者名
		'FORM_TEAM_TEL'      => '',		// 電話番号
		'FORM_TEAM_EMAIL'    => '',		// メールアドレス
		'FORM_AGREE'         => '',		// 同意（ハニーポット）
		'FORM1_TOURNAMENT_AGREE'         => '',		// 大会概要(1:同意)
		'FORM1_TOURNAMENT_LOTTERY_AGREE' => '',	// 抽選(1:同意)
		'FORM1_WAITLIST_PREFERENCE'      => '',	// キャンセル待ち(yes/no)
		'FORM2_SHIGA_PREFERENCE'          => '',	// 滋賀県内/滋賀県外(yes/no)
		'FORM3_STAY_PREFERENCE'           => '',	// 宿泊利用(yes/no)
		'FORM4_STAY_AGREE'                => '',	// 宿泊同意(1:同意)
		'FORM4_DINNER_PREFERENCE'         => '',	// 夕食オプション(yes/no)
		'FORM4_STAY_PLAYER_COUNT'         => 1,	// 宿泊人数(選手:1～20)
		'FORM4_STAY_COACH_COUNT'          => 1,	// 宿泊人数(指導者:1～5)
		'FORM4_STAY_OTHER_COUNT'          => 1,	// 宿泊人数(その他:1～50)
		'FORM4_STAY_CAR_COUNT'            => 0,	// 宿泊台数(0～20)
		'FORM4_ALLERGY'                   => '',	// 食事に関するアレルギー(yes/no)
		'FORM4_ALLERGY_INFO'              => '',	// 食事に関するアレルギー情報
		'FORM5_TEAM_NAME'                 => '',	// チーム名
		'FORM5_TEAM_NAME_KANA'            => '',	// チーム名（カナ）
		'FORM5_TEAM_MANAGER'              => '',	// 責任者名
		'FORM5_TEAM_MANAGER_KANA'         => '',	// 責任者名（カナ）
		'FORM5_TEAM_MANAGER_TEL'          => '',	// 責任者電話番号
		'FORM5_TEAM_MANAGER_EMAIL'        => '',	// 責任者メールアドレス
		'FORM5_TOURNAMENT_CONTACT'        => '',	// 大会連絡先
		'FORM5_TOURNAMENT_CONTACT_KANA'   => '',	// 大会連絡先（カナ）
		'FORM5_TOURNAMENT_CONTACT_TEL'    => '',	// 大会連絡先電話番号
		'FORM5_TOURNAMENT_CONTACT_EMAIL'  => '',	// 大会連絡先メールアドレス
		'FORM5_PLAYER_COUNT'               => 1,	// 選手人数(1～20)
		'FORM5_MANAGER_COUNT'              => 1,	// 指導者人数(1～5)
		'FORM5_PARENT_COUNT'               => 0,	// 保護者人数(0～50)
		'FORM5_MYCAR_PREFERENCE'          => '',	// 自家用車利用(yes/no)
		'FORM5_MYCAR_COUNT'               => 0,	// 自家用車台数(0～50)
		'FORM5_MICROBUS_PREFERENCE'        => '',	// マイクロバス利用(yes/no)
		'FORM5_MICROBUS_COUNT'             => 0,	// マイクロバス台数(0～50)
		'FORM5_PUBLIC_TRANSPORT_PREFERENCE' => '',	// 公共交通機関利用(yes/no)
		'FORM5_LUNCH_DAY1_LUNCHBOX'		=> '',	// 昼食1日目お弁当(yes/no)
		'FORM5_LUNCH_DAY1_STALL'			=> '',	// 昼食1日目屋台(yes/no)
		'FORM5_LUNCH_DAY2_LUNCHBOX'		=> '',	// 昼食2日目お弁当(yes/no)
		'FORM5_LUNCH_DAY2_STALL'			=> '',	// 昼食2日目屋台(yes/no)
		'FORM5_LUNCH_DAY3_LUNCHBOX'		=> '',	// 昼食3日目お弁当(yes/no)
		'FORM5_LUNCH_DAY3_STALL'			=> '',	// 昼食3日目屋台(yes/no)
		'FORM5_TEAM_ACHIEVEMENTS'         => '',	// チームの実績
		'FORM5_TEAM_PR'                   => '',	// チームPR
		'FORM5_INFORMATION_SOURCE'         => '',	// 大会情報の入手方法(mouth/sns/homepage/introduction/other)
		'FORM5_NOTES'                     => '',	// 大会事務局への伝達事項及びご質問
	];

	// セッション変数の設定
	foreach ( $sessionList as $name => $value ) {
		$_SESSION[ $name ] = $value;
	}
	
	// CSRFトークン生成（なければ作る）
	if ( empty( $_SESSION[ 'RES_TOKEN' ] ) ) {
		$_SESSION[ 'RES_TOKEN' ] = bin2hex( random_bytes( 32 ) );
		$g_Log->notice( "CSRFトークンを生成しました : " . $_SESSION[ 'RES_TOKEN' ], __FUNCTION__, basename( __FILE__ ) );
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

	// 連続投稿の制限チェック
	if( ! rateLimitOrFail( pathinfo( __FILE__, PATHINFO_FILENAME ) ) ) {
		return false;
	}

	// ハニーポットでスパムチェック
	if( ! verifyHoneypotOrFail( $_POST[ 'agree' ] ?? '' ) ) {
		return false;
	}

	// CSRFトークンでスパムチェック
	if( ! verifyCsrfOrFail( $_POST[ 'token' ] ?? '', $_SESSION[ 'RES_TOKEN' ] ?? '' ) ) {
		return false;
	}

	// Formのチェック
	if( ! checkForm() ) {
		return false;
	}

	return true;
}

# ==========================================================
# チェック処理
# ==========================================================
function checkForm() {

	global $g_Log;
	$g_Log->notice( "チェック処理", __FUNCTION__, basename( __FILE__ ) );

	if( ! checkRequest( 'view_state', false ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '参加する大会を選択して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkRequest( 'token' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '不正なトークンを検出しました。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 大会IDのチェック
	if( ! checkRequest( 'tournament_id' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '参加する大会を選択して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkNumeric( $_POST[ 'tournament_id' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '参加する大会の選択が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// チーム名のチェック
	if( ! checkRequest( 'team_name' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム名を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_name' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム名の入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 責任者名のチェック
	if( ! checkRequest( 'team_manager' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '責任者名を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_manager' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '責任者名の入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 連絡先のチェック
	if( ! checkRequest( 'team_manager_tel' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '電話番号を入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_manager_tel' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '電話番号の入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// メールアドレスのチェック
	if( ! checkRequest( 'team_manager_email' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'メールアドレスを入力して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	if( ! checkText( $_POST[ 'team_manager_email' ] ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'メールアドレスの入力が不正です。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	// 同意のチェック
	if( ! checkRequest( 'tournament_agree' ) ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '同意を確認して下さい。';
		$g_Log->warning( $_SESSION[ 'FORM_ERR_MSG' ], __FUNCTION__, basename( __FILE__ ) );
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

	$_SESSION[ 'FROM_VIEW_STATE' ] = trim( $_POST[ 'view_state' ] );
	$_SESSION[ 'FROM_TOURNAMENT_ID' ] = trim( $_POST[ 'tournament_id' ] );
	$_SESSION[ 'FORM_TEAM_NAME' ] = trim( $_POST[ 'team_name' ] );
	$_SESSION[ 'FORM_TEAM_MANAGER' ] = trim( $_POST[ 'team_manager' ] );
	$_SESSION[ 'FORM_TEAM_TEL' ] = trim( $_POST[ 'team_manager_tel' ] );
	$_SESSION[ 'FORM_TEAM_EMAIL' ] = trim( $_POST[ 'team_manager_email' ] );

	$_SESSION[ 'FORM1_TOURNAMENT_AGREE' ]            = isset( $_POST[ 'tournament_agree' ] ) ? trim( $_POST[ 'tournament_agree' ] ) : '';         // 大会概要(1:同意)
	$_SESSION[ 'FORM1_TOURNAMENT_LOTTERY_AGREE' ]    = isset( $_POST[ 'tournament_lottery_agree' ] ) ? trim( $_POST[ 'tournament_lottery_agree' ] ) : '';	// 抽選(1:同意)
	$_SESSION[ 'FORM1_WAITLIST_PREFERENCE' ]         = isset( $_POST[ 'waitlist_preference' ] ) ? trim( $_POST[ 'waitlist_preference' ] ) : '';	// キャンセル待ち(yes/no)
	$_SESSION[ 'FORM2_SHIGA_PREFERENCE' ]            = isset( $_POST[ 'shiga_preference' ] ) ? trim( $_POST[ 'shiga_preference' ] ) : '';	// 滋賀県内/滋賀県外(yes/no)
	$_SESSION[ 'FORM3_STAY_PREFERENCE' ]             = isset( $_POST[ 'stay_preference' ] ) ? trim( $_POST[ 'stay_preference' ] ) : '';	// 宿泊利用(yes/no)
	$_SESSION[ 'FORM4_STAY_AGREE' ]                  = isset( $_POST[ 'stay_agree' ] ) ? trim( $_POST[ 'stay_agree' ] ) : '';	// 宿泊同意(1:同意)
	$_SESSION[ 'FORM4_DINNER_PREFERENCE' ]           = isset( $_POST[ 'dinner_preference' ] ) ? trim( $_POST[ 'dinner_preference' ] ) : '';	// 夕食オプション(yes/no)
	$_SESSION[ 'FORM4_STAY_PLAYER_COUNT' ]           = isset( $_POST[ 'stay_player_count' ] ) ? trim( $_POST[ 'stay_player_count' ] ) : '';	// 宿泊人数(選手:1～20)
	$_SESSION[ 'FORM4_STAY_COACH_COUNT' ]            = isset( $_POST[ 'stay_coach_count' ] ) ? trim( $_POST[ 'stay_coach_count' ] ) : '';	// 宿泊人数(指導者:1～5)
	$_SESSION[ 'FORM4_STAY_OTHER_COUNT' ]            = isset( $_POST[ 'stay_other_count' ] ) ? trim( $_POST[ 'stay_other_count' ] ) : '';	// 宿泊人数(その他:1～50)
	$_SESSION[ 'FORM4_STAY_CAR_COUNT' ]              = isset( $_POST[ 'stay_car_count' ] ) ? trim( $_POST[ 'stay_car_count' ] ) : '';	// 宿泊台数(0～20)
	$_SESSION[ 'FORM4_ALLERGY' ]                     = isset( $_POST[ 'allergy' ] ) ? trim( $_POST[ 'allergy' ] ) : '';	// 食事に関するアレルギー(yes/no)
	$_SESSION[ 'FORM4_ALLERGY_INFO' ]                = isset( $_POST[ 'allergy_info' ] ) ? trim( $_POST[ 'allergy_info' ] ) : '';	// 食事に関するアレルギー情報
	$_SESSION[ 'FORM5_TEAM_NAME' ]                   = isset( $_POST[ 'team_name' ] ) ? trim( $_POST[ 'team_name' ] ) : '';	// チーム名
	$_SESSION[ 'FORM5_TEAM_NAME_KANA' ]              = isset( $_POST[ 'team_name_kana' ] ) ? trim( $_POST[ 'team_name_kana' ] ) : '';	// チーム名（カナ）
	$_SESSION[ 'FORM5_TEAM_MANAGER' ]                = isset( $_POST[ 'team_manager' ] ) ? trim( $_POST[ 'team_manager' ] ) : '';	// 責任者名
	$_SESSION[ 'FORM5_TEAM_MANAGER_KANA' ]           = isset( $_POST[ 'team_manager_kana' ] ) ? trim( $_POST[ 'team_manager_kana' ] ) : '';	// 責任者名（カナ）
	$_SESSION[ 'FORM5_TEAM_MANAGER_TEL' ]            = isset( $_POST[ 'team_manager_tel' ] ) ? trim( $_POST[ 'team_manager_tel' ] ) : '';	// 責任者電話番号
	$_SESSION[ 'FORM5_TEAM_MANAGER_EMAIL' ]          = isset( $_POST[ 'team_manager_email' ] ) ? trim( $_POST[ 'team_manager_email' ] ) : '';	// 責任者メールアドレス
	$_SESSION[ 'FORM5_TOURNAMENT_CONTACT' ]          = isset( $_POST[ 'tournament_contact' ] ) ? trim( $_POST[ 'tournament_contact' ] ) : '';	// 大会連絡先
	$_SESSION[ 'FORM5_TOURNAMENT_CONTACT_KANA' ]     = isset( $_POST[ 'tournament_contact_kana' ] ) ? trim( $_POST[ 'tournament_contact_kana' ] ) : '';	// 大会連絡先（カナ）
	$_SESSION[ 'FORM5_TOURNAMENT_CONTACT_TEL' ]      = isset( $_POST[ 'tournament_contact_tel' ] ) ? trim( $_POST[ 'tournament_contact_tel' ] ) : '';	// 大会連絡先電話番号
	$_SESSION[ 'FORM5_TOURNAMENT_CONTACT_EMAIL' ]    = isset( $_POST[ 'tournament_contact_email' ] ) ? trim( $_POST[ 'tournament_contact_email' ] ) : '';	// 大会連絡先メールアドレス
	$_SESSION[ 'FORM5_PLAYER_COUNT' ]                = isset( $_POST[ 'player_count' ] ) ? trim( $_POST[ 'player_count' ] ) : '';	// 選手人数(1～20)
	$_SESSION[ 'FORM5_MANAGER_COUNT' ]               = isset( $_POST[ 'manager_count' ] ) ? trim( $_POST[ 'manager_count' ] ) : '';	// 指導者人数(1～5)
	$_SESSION[ 'FORM5_PARENT_COUNT' ]                = isset( $_POST[ 'parent_count' ] ) ? trim( $_POST[ 'parent_count' ] ) : '';	// 保護者人数(0～50)
	$_SESSION[ 'FORM5_MYCAR_PREFERENCE' ]            = isset( $_POST[ 'mycar_preference' ] ) ? trim( $_POST[ 'mycar_preference' ] ) : '';	// 自家用車利用(yes/no)
	$_SESSION[ 'FORM5_MYCAR_COUNT' ]                 = isset( $_POST[ 'mycar_count' ] ) ? trim( $_POST[ 'mycar_count' ] ) : '';	// 自家用車台数(0～50)
	$_SESSION[ 'FORM5_MICROBUS_PREFERENCE' ]         = isset( $_POST[ 'microbus_preference' ] ) ? trim( $_POST[ 'microbus_preference' ] ) : '';	// マイクロバス利用(yes/no)
	$_SESSION[ 'FORM5_MICROBUS_COUNT' ]              = isset( $_POST[ 'microbus_count' ] ) ? trim( $_POST[ 'microbus_count' ] ) : '';	// マイクロバス台数(0～50)
	$_SESSION[ 'FORM5_PUBLIC_TRANSPORT_PREFERENCE' ] = isset( $_POST[ 'public_transport_preference' ] ) ? trim( $_POST[ 'public_transport_preference' ] ) : '';	// 公共交通機関利用(yes/no)
	$_SESSION[ 'FORM5_LUNCH_DAY1_LUNCHBOX' ]         = isset( $_POST[ 'lunch_day1_lunchbox' ] ) ? trim( $_POST[ 'lunch_day1_lunchbox' ] ) : '';	// 昼食1日目お弁当(yes/no)
	$_SESSION[ 'FORM5_LUNCH_DAY1_STALL' ]            = isset( $_POST[ 'lunch_day1_stall' ] ) ? trim( $_POST[ 'lunch_day1_stall' ] ) : '';	// 昼食1日目屋台(yes/no)
	$_SESSION[ 'FORM5_LUNCH_DAY2_LUNCHBOX' ]         = isset( $_POST[ 'lunch_day2_lunchbox' ] ) ? trim( $_POST[ 'lunch_day2_lunchbox' ] ) : '';	// 昼食2日目お弁当(yes/no)
	$_SESSION[ 'FORM5_LUNCH_DAY2_STALL' ]            = isset( $_POST[ 'lunch_day2_stall' ] ) ? trim( $_POST[ 'lunch_day2_stall' ] ) : '';	// 昼食2日目屋台(yes/no)
	$_SESSION[ 'FORM5_LUNCH_DAY3_LUNCHBOX' ]         = isset( $_POST[ 'lunch_day3_lunchbox' ] ) ? trim( $_POST[ 'lunch_day3_lunchbox' ] ) : '';	// 昼食3日目お弁当(yes/no)
	$_SESSION[ 'FORM5_LUNCH_DAY3_STALL' ]            = isset( $_POST[ 'lunch_day3_stall' ] ) ? trim( $_POST[ 'lunch_day3_stall' ] ) : '';	// 昼食3日目屋台(yes/no)
	$_SESSION[ 'FORM5_TEAM_ACHIEVEMENTS' ]           = isset( $_POST[ 'team_achievements' ] ) ? trim( $_POST[ 'team_achievements' ] ) : '';	// チームの実績
	$_SESSION[ 'FORM5_TEAM_PR' ]                     = isset( $_POST[ 'team_pr' ] ) ? trim( $_POST[ 'team_pr' ] ) : '';	// チームPR
	$_SESSION[ 'FORM5_INFORMATION_SOURCE' ]          = isset( $_POST[ 'information_source' ] ) ? trim( $_POST[ 'information_source' ] ) : '';	// 大会情報の入手方法(mouth/sns/homepage/introduction/other)
	$_SESSION[ 'FORM5_NOTES' ]                       = isset( $_POST[ 'notes' ] ) ? trim( $_POST[ 'notes' ] ) : '';	// 大会事務局への伝達事項及びご質問

	// チーム情報登録
	if( ! setTeamInfo() ) {
		return false;
	}

	// メール送信
	if( ! sendMail() ) {
		return false;
	}

	// 登録完了後はセッション変数をクリア
	$_SESSION[ 'RES_TOKEN' ] = bin2hex( random_bytes( 32 ) );
	unset( $_SESSION[ 'FORM_TEAM_NAME' ] );
	unset( $_SESSION[ 'FORM_TEAM_MANAGER' ] );
	unset( $_SESSION[ 'FORM_TEAM_TEL' ] );
	unset( $_SESSION[ 'FORM_TEAM_EMAIL' ] );
	unset( $_SESSION[ 'FORM1_TOURNAMENT_AGREE' ] );
	unset( $_SESSION[ 'FORM1_TOURNAMENT_LOTTERY_AGREE' ] );
	unset( $_SESSION[ 'FORM1_WAITLIST_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM2_SHIGA_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM3_STAY_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM4_STAY_AGREE' ] );
	unset( $_SESSION[ 'FORM4_LUNCH_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM4_DINNER_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM4_STAY_PLAYER_COUNT' ] );
	unset( $_SESSION[ 'FORM4_STAY_COACH_COUNT' ] );
	unset( $_SESSION[ 'FORM4_STAY_OTHER_COUNT' ] );
	unset( $_SESSION[ 'FORM4_STAY_CAR_COUNT' ] );
	unset( $_SESSION[ 'FORM4_ALLERGY' ] );
	unset( $_SESSION[ 'FORM4_ALLERGY_INFO' ] );
	unset( $_SESSION[ 'FORM5_TEAM_NAME' ] );
	unset( $_SESSION[ 'FORM5_TEAM_NAME_KANA' ] );
	unset( $_SESSION[ 'FORM5_TEAM_MANAGER' ] );
	unset( $_SESSION[ 'FORM5_TEAM_MANAGER_KANA' ] );
	unset( $_SESSION[ 'FORM5_TEAM_MANAGER_TEL' ] );
	unset( $_SESSION[ 'FORM5_TEAM_MANAGER_EMAIL' ] );
	unset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT' ] );
	unset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_KANA' ] );
	unset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_TEL' ] );
	unset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_EMAIL' ] );
	unset( $_SESSION[ 'FORM5_PLAYER_COUNT' ] );
	unset( $_SESSION[ 'FORM5_MANAGER_COUNT' ] );
	unset( $_SESSION[ 'FORM5_PARENT_COUNT' ] );
	unset( $_SESSION[ 'FORM5_MYCAR_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM5_MYCAR_COUNT' ] );
	unset( $_SESSION[ 'FORM5_MICROBUS_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM5_MICROBUS_COUNT' ] );
	unset( $_SESSION[ 'FORM5_PUBLIC_TRANSPORT_PREFERENCE' ] );
	unset( $_SESSION[ 'FORM5_LUNCH_DAY1_LUNCHBOX' ] );
	unset( $_SESSION[ 'FORM5_LUNCH_DAY1_STALL' ] );
	unset( $_SESSION[ 'FORM5_LUNCH_DAY2_LUNCHBOX' ] );
	unset( $_SESSION[ 'FORM5_LUNCH_DAY2_STALL' ] );
	unset( $_SESSION[ 'FORM5_LUNCH_DAY3_LUNCHBOX' ] );
	unset( $_SESSION[ 'FORM5_LUNCH_DAY3_STALL' ] );
	unset( $_SESSION[ 'FORM5_TEAM_ACHIEVEMENTS' ] );
	unset( $_SESSION[ 'FORM5_TEAM_PR' ] );
	unset( $_SESSION[ 'FORM5_INFORMATION_SOURCE' ] );
	unset( $_SESSION[ 'FORM5_NOTES' ] );

	return true;
}

# ==========================================================
# チーム情報登録処理
# ==========================================================
function setTeamInfo() {

	global $g_Log;
	$g_Log->notice( "チーム情報登録処理", __FUNCTION__, basename( __FILE__ ) );

	// 6桁までの数字をランダム生成
	$randomNumber = random_int( 0, 999999 );

	// 6桁の認証コード生成
	$randomCode = str_pad( strval( $randomNumber ), 6, '0', STR_PAD_LEFT );

	$g_Log->notice( "チームアクセスコードを作成しました : " . $randomCode, __FUNCTION__, basename( __FILE__ ) );

	// セッション変数から値を取得
	$tournament_id  = $_SESSION[ 'FROM_TOURNAMENT_ID' ];
	$team_name      = $_SESSION[ 'FORM_TEAM_NAME' ];
	$team_manager   = $_SESSION[ 'FORM_TEAM_MANAGER' ];
	$team_tel       = $_SESSION[ 'FORM_TEAM_TEL' ];
	$team_email     = $_SESSION[ 'FORM_TEAM_EMAIL' ];
	$team_contact	= "TEL: " . $team_tel . " / EMAIL: " . $team_email;
	$team_access_cd = $randomCode;
	$_SESSION[ 'RES_TEAM_ACCESS_CD' ] = $team_access_cd;
	$_SESSION[ 'RES_TEAM_CONTACT' ] = $team_contact;

	$tournament_agree = $_SESSION[ 'FORM1_TOURNAMENT_AGREE' ];         // 大会概要(1:同意)
	$tournament_lottery_agree = $_SESSION[ 'FORM1_TOURNAMENT_LOTTERY_AGREE' ];    // 抽選(1:同意)
	$waitlist_preference = $_SESSION[ 'FORM1_WAITLIST_PREFERENCE' ];         // キャンセル待ち(yes/no)
	$shiga_preference = $_SESSION[ 'FORM2_SHIGA_PREFERENCE' ];            // 滋賀県内/滋賀県外(yes/no)
	$stay_preference = $_SESSION[ 'FORM3_STAY_PREFERENCE' ];             // 宿泊利用(yes/no)
	$stay_agree = $_SESSION[ 'FORM4_STAY_AGREE' ];                  // 宿泊同意(1:同意)
	$dinner_preference = $_SESSION[ 'FORM4_DINNER_PREFERENCE' ];           // 夕食オプション(yes/no)
	$stay_player_count = $_SESSION[ 'FORM4_STAY_PLAYER_COUNT' ];           // 宿泊人数(選手:1～20)
	$stay_coach_count = $_SESSION[ 'FORM4_STAY_COACH_COUNT' ];            // 宿泊人数(指導者:1～5)
	$stay_other_count = $_SESSION[ 'FORM4_STAY_OTHER_COUNT' ];            // 宿泊人数(その他:1～50)
	$stay_car_count = $_SESSION[ 'FORM4_STAY_CAR_COUNT' ];              // 宿泊台数(0～20)
	$allergy = $_SESSION[ 'FORM4_ALLERGY' ];                     // 食事に関するアレルギー(yes/no)
	$allergy_info = $_SESSION[ 'FORM4_ALLERGY_INFO' ];                // 食事に関するアレルギー情報
	$team_name = $_SESSION[ 'FORM5_TEAM_NAME' ];                   // チーム名
	$team_name_kana = $_SESSION[ 'FORM5_TEAM_NAME_KANA' ];          // チーム名（カナ）
	$team_manager = $_SESSION[ 'FORM5_TEAM_MANAGER' ];                // 責任者名
	$team_manager_kana = $_SESSION[ 'FORM5_TEAM_MANAGER_KANA' ];     // 責任者名（カナ）
	$team_manager_tel = $_SESSION[ 'FORM5_TEAM_MANAGER_TEL' ];            // 責任者電話番号
	$team_manager_email = $_SESSION[ 'FORM5_TEAM_MANAGER_EMAIL' ];          // 責任者メールアドレス
	$tournament_contact = $_SESSION[ 'FORM5_TOURNAMENT_CONTACT' ];          // 大会連絡先
	$tournament_contact_kana = $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_KANA' ];     // 大会連絡先（カナ）
	$tournament_contact_tel = $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_TEL' ];      // 大会連絡先電話番号
	$tournament_contact_email = $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_EMAIL' ];    // 大会連絡先メールアドレス
	$player_count = $_SESSION[ 'FORM5_PLAYER_COUNT' ];                // 選手人数(1～20)
	$manager_count = $_SESSION[ 'FORM5_MANAGER_COUNT' ];               // 指導者人数(1～5)
	$parent_count = $_SESSION[ 'FORM5_PARENT_COUNT' ];                // 保護者人数(0～50)
	$mycar_preference = $_SESSION[ 'FORM5_MYCAR_PREFERENCE' ];            // 自家用車利用(yes/no)
	$mycar_count = $_SESSION[ 'FORM5_MYCAR_COUNT' ];                 // 自家用車台数(0～50)
	$microbus_preference = $_SESSION[ 'FORM5_MICROBUS_PREFERENCE' ];         // マイクロバス利用(yes/no)
	$microbus_count = $_SESSION[ 'FORM5_MICROBUS_COUNT' ];              // マイクロバス台数(0～50)
	$public_transport_preference = $_SESSION[ 'FORM5_PUBLIC_TRANSPORT_PREFERENCE' ]; // 公共交通機関利用(yes/no)
	$lunch_day1_lunchbox = $_SESSION[ 'FORM5_LUNCH_DAY1_LUNCHBOX' ];         // 昼食1日目お弁当(yes/no)
	$lunch_day1_stall = $_SESSION[ 'FORM5_LUNCH_DAY1_STALL' ];            // 昼食1日目屋台(yes/no)
	$lunch_day2_lunchbox = $_SESSION[ 'FORM5_LUNCH_DAY2_LUNCHBOX' ];         // 昼食2日目お弁当(yes/no)
	$lunch_day2_stall = $_SESSION[ 'FORM5_LUNCH_DAY2_STALL' ];            // 昼食2日目屋台(yes/no)
	$lunch_day3_lunchbox = $_SESSION[ 'FORM5_LUNCH_DAY3_LUNCHBOX' ];         // 昼食3日目お弁当(yes/no)
	$lunch_day3_stall = $_SESSION[ 'FORM5_LUNCH_DAY3_STALL' ];            // 昼食3日目屋台(yes/no)
	$team_achievements = $_SESSION[ 'FORM5_TEAM_ACHIEVEMENTS' ];           // チームの実績
	$team_pr = $_SESSION[ 'FORM5_TEAM_PR' ];                     // チームPR
	$information_source = $_SESSION[ 'FORM5_INFORMATION_SOURCE' ];          // 大会情報の入手方法(mouth/sns/homepage/introduction/other)
	$notes = $_SESSION[ 'FORM5_NOTES' ];                       // 大会事務局への伝達事項及びご質問

	$jsonData = array(
		"tournament_id"  => $tournament_id,
		"team_name" => $team_name,
		"team_manager"   => $team_manager,
		"team_tel"       => $team_tel,
		"team_email"     => $team_email,
		"team_contact"   => $team_contact,
		"team_access_cd" => $team_access_cd,
		"tournament_agree" => $tournament_agree,
		"tournament_lottery_agree" => $tournament_lottery_agree,
		"waitlist_preference" => $waitlist_preference,
		"shiga_preference" => $shiga_preference,
		"stay_preference" => $stay_preference,
		"stay_agree" => $stay_agree,
		"dinner_preference" => $dinner_preference,
		"stay_player_count" => $stay_player_count,
		"stay_coach_count" => $stay_coach_count,
		"stay_other_count" => $stay_other_count,
		"stay_car_count" => $stay_car_count,
		"allergy" => $allergy,
		"allergy_info" => $allergy_info,
		"team_name_kana" => $team_name_kana,
		"team_manager_kana" => $team_manager_kana,
		"team_manager_tel" => $team_manager_tel,
		"team_manager_email" => $team_manager_email,
		"tournament_contact" => $tournament_contact,
		"tournament_contact_kana" => $tournament_contact_kana,
		"tournament_contact_tel" => $tournament_contact_tel,
		"tournament_contact_email" => $tournament_contact_email,
		"player_count" => $player_count,
		"manager_count" => $manager_count,
		"parent_count" => $parent_count,
		"mycar_preference" => $mycar_preference,
		"mycar_count" => $mycar_count,
		"microbus_preference" => $microbus_preference,
		"microbus_count" => $microbus_count,
		"public_transport_preference" => $public_transport_preference,
		"lunch_day1_lunchbox" => $lunch_day1_lunchbox,
		"lunch_day1_stall" => $lunch_day1_stall,
		"lunch_day2_lunchbox" => $lunch_day2_lunchbox,
		"lunch_day2_stall" => $lunch_day2_stall,
		"lunch_day3_lunchbox" => $lunch_day3_lunchbox,
		"lunch_day3_stall" => $lunch_day3_stall,
		"team_achievements" => $team_achievements,
		"team_pr" => $team_pr,
		"information_source" => $information_source,
		"notes" => $notes
	);

	// SQL文作成
	$SQL = "";
	$SQL .= "INSERT INTO baseball_team ";
	$SQL .= "( team_name ";
	$SQL .= ", team_manager ";
	$SQL .= ", team_contact ";
	$SQL .= ", team_access_cd ";
	$SQL .= ", team_tel ";
	$SQL .= ", team_email ";
	$SQL .= ", tournament_id ";
	$SQL .= ", team_meta ";
	$SQL .= ", is_enabled ";
	$SQL .= ", created_at ";
	$SQL .= ", created_by ";
	$SQL .= ", updated_at ";
	$SQL .= ", updated_by ";
	$SQL .= ") ";
	$SQL .= "VALUES ";
	$SQL .= "( :team_name ";
	$SQL .= ", :team_manager ";
	$SQL .= ", :team_contact ";
	$SQL .= ", :team_access_cd ";
	$SQL .= ", :team_tel ";
	$SQL .= ", :team_email ";
	$SQL .= ", :tournament_id ";
	$SQL .= ", :team_meta ";
	$SQL .= ", :is_enabled ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :created_by ";
	$SQL .= ", CURRENT_TIMESTAMP ";
	$SQL .= ", :updated_by ";
	$SQL .= ") ";

	$SQL_Parameters = array(
		"team_name"      => $team_name,
		"team_manager"   => $team_manager,
		"team_contact"   => $team_contact,
		"team_access_cd" => $team_access_cd,
		"team_tel"       => $team_tel,
		"team_email"     => $team_email,
		"tournament_id"  => $tournament_id,
		"team_meta"      => json_encode( $jsonData, JSON_UNESCAPED_UNICODE ),
		"is_enabled"     => 1,
		"created_by"     => basename( __FILE__ ),
		"updated_by"     => basename( __FILE__ )
	);

	try {
		global $g_DB;
		$stmt = $g_DB->execute( $SQL, $SQL_Parameters );

		if( $stmt === false ){
			throw new PDOException( "SQLの実行に失敗しました。" );
		}

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = 'チーム情報の登録処理でエラーが発生しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return true;
}

# ==========================================================
# メール送信処理
# ==========================================================
function sendMail() {

	global $g_Log;
	$g_Log->notice( "メール送信処理", __FUNCTION__, basename( __FILE__ ) );

	$tournament_id  = $_SESSION[ 'FROM_TOURNAMENT_ID' ];
	$team_name      = $_SESSION[ 'FORM_TEAM_NAME' ];
	$team_manager   = $_SESSION[ 'FORM_TEAM_MANAGER' ];
	$team_tel       = $_SESSION[ 'FORM_TEAM_TEL' ];
	$team_email     = $_SESSION[ 'FORM_TEAM_EMAIL' ];
	$team_access_cd = $_SESSION[ 'RES_TEAM_ACCESS_CD' ];
	$team_contact	= $_SESSION[ 'RES_TEAM_CONTACT' ];

	$postedAt = date( 'Y-m-d H:i:s' );

	// メールアドレス取得
	// SQL文作成
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   email ";
	$SQL .= " , role_level ";
	$SQL .= "   FROM baseball_user ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND role_level IN ( 100, 1000 ) ";
	
	$SQL_Parameters = array();
	
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
			$bccArray[] = $row[ 'email' ];
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

	$toAddress = $team_email;
	$ccAddress = '';
	$bccAddress = implode( ',', $bccArray );

	// メール送信処理
	$mailer = new Class_PHPMailer();
	$subject = '【HPL】大会参加申し込みを受け付けました。' . $postedAt;
	$message = "";
	$message .= "<p>参加大会名 : " . $tournamentName . "</p>";
	$message .= "<br>";
	$message .= "<p>登録日時 : " . $postedAt . "</p>";
	$message .= "<br>";
	$message .= "<p>お申込みいただき誠に有難うございます。</p>";
	$message .= "<p>申込完了しました。</p>";
	$message .= "<p>参加結果が出るまで少々お待ちください。</p>";
	$message .= "<br>";
	$message .= "<p>チーム名 : " . $team_name . "</p>";
	$message .= "<p>責任者名 : " . $team_manager . "</p>";
	$message .= "<p>電話番号 : " . $team_tel . "</p>";
	$message .= "<p>メールアドレス : " . $team_email . "</p>";

	if ( $mailer->sendMail( $toAddress, $subject, $message, $ccAddress, $bccAddress ) ) {
		$g_Log->notice( "大会参加申し込み内容を送信しました", __FUNCTION__, basename( __FILE__ ) );
	} else {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会参加申し込み内容の送信に失敗しました。';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . $team_email, __FUNCTION__, basename( __FILE__ ) );
		return false;
	}
	
	$g_Log->notice( "メール送信処理が完了しました : " . $team_email, __FUNCTION__, basename( __FILE__ ) );
	$_SESSION[ 'FORM_ERR_MSG' ] = '大会の参加受付が完了しました。';

	return true;
}

# ==========================================================
# 終了処理
# ==========================================================
function endProc() {

	global $g_Log;
	$g_Log->notice( "終了処理", __FUNCTION__, basename( __FILE__ ) );

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
	// 大会参加申し込みの読み込み
	$filename = __DIR__ . DIRECTORY_SEPARATOR . "article_team-entry.php";
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

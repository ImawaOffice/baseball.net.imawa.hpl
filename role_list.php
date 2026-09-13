<?php
# **********************************************************
# 権限マスタ管理
# **********************************************************

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'role_controller.php' );

// ----------------------------------------------------------
// 画面の開始処理
// ----------------------------------------------------------
// ここでは認証・権限チェックと、Controller の初期化を行う。
// 画面そのものの責務は、DB操作や業務判断をコントローラへ委譲し、
// 画面の入口として必要な処理だけを残す。
$g_Log->notice( 'S : ' . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

if( ! isAuthenticated() ) {
    header( 'Location: ./signin' );
    exit();
}

if( (int)( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ?? 0 ) !== 1000 ) {
    header( 'Location: ./error' );
    exit();
}

if( empty( $_SESSION[ 'ROLE_LIST_TOKEN' ] ) ) {
    $_SESSION[ 'ROLE_LIST_TOKEN' ] = bin2hex( random_bytes( 32 ) );
}

if( empty( $_SESSION[ 'ROLE_REQUEST_GUARD' ] ) ) {
    $_SESSION[ 'ROLE_REQUEST_GUARD' ] = bin2hex( random_bytes( 8 ) );
}

$roleRequestGuard = $_SESSION[ 'ROLE_REQUEST_GUARD' ];

unset( $_SESSION[ 'ROLE_REQUEST_LOCK' ] );
unset( $_SESSION[ 'ROLE_REQUEST_LOCK_EXPIRES_AT' ] );

$roleController = new RoleController( $g_DB );

// ----------------------------------------------------------
// Controller の実行
// ----------------------------------------------------------
// 画面入口では、HTTPメソッドとリクエスト情報を Controller に渡し、
// 表示に必要なデータをまとめて受け取る。
// これにより、role_list.php は「何を呼ぶか」だけに集中できる。
$viewData = $roleController->executeRequest(
    $_POST,
    $_SESSION[ 'ROLE_LIST_TOKEN' ] ?? ''
);

$roleRequestGuard = $_SESSION[ 'ROLE_REQUEST_GUARD' ] ?? bin2hex( random_bytes( 8 ) );

$message = $viewData[ 'message' ] ?? '';
$error = $viewData[ 'error' ] ?? '';
$roleList = $viewData[ 'roleList' ] ?? [];

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'html_head.php' );

// ----------------------------------------------------------
// 共通ヘッダーの表示
// ----------------------------------------------------------
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'page_header.php' );

// ----------------------------------------------------------
// View の呼び出し
// ----------------------------------------------------------
// 表示は view に委譲する。
// ここでは表示用データを $viewData としてまとめて渡し、
// role_view.php はそのデータを使って描画だけを担当する。
$viewData = [
    'message' => $message,
    'error' => $error,
    'roleList' => $roleList,
    'requestGuard' => $roleRequestGuard,
];

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'role_view.php' );

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'page_footer.php' );
?>

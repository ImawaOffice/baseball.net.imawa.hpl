<?php
# **********************************************************
# メニュー管理
# **********************************************************

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_function.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'menu_controller.php' );

$g_Log->notice( 'S : ' . basename( __FILE__ ), __FUNCTION__, basename( __FILE__ ) );

if( ! isAuthenticated() ) {
    header( 'Location: ./signin' );
    exit();
}

if( (int)( $_SESSION[ 'AUTH_ROLE_LEVEL' ] ?? 0 ) !== 1000 ) {
    header( 'Location: ./error' );
    exit();
}

if( empty( $_SESSION[ 'MENU_LIST_TOKEN' ] ) ) {
    $_SESSION[ 'MENU_LIST_TOKEN' ] = bin2hex( random_bytes( 32 ) );
}

if( empty( $_SESSION[ 'MENU_REQUEST_GUARD' ] ) ) {
    $_SESSION[ 'MENU_REQUEST_GUARD' ] = bin2hex( random_bytes( 8 ) );
}

$menuRequestGuard = $_SESSION[ 'MENU_REQUEST_GUARD' ];

unset( $_SESSION[ 'MENU_REQUEST_LOCK' ] );
unset( $_SESSION[ 'MENU_REQUEST_LOCK_EXPIRES_AT' ] );

// メニューはSQLite(config DB)、権限候補はMySQL(baseball DB)を参照する。
$menuController = new MenuController( $g_DB_Config, $g_DB );

$viewData = $menuController->executeRequest(
    $_POST,
    $_SESSION[ 'MENU_LIST_TOKEN' ] ?? ''
);

$menuRequestGuard = $_SESSION[ 'MENU_REQUEST_GUARD' ] ?? bin2hex( random_bytes( 8 ) );

$message = $viewData[ 'message' ] ?? '';
$error = $viewData[ 'error' ] ?? '';
$menuList = $viewData[ 'menuList' ] ?? [];
$roleLevelOptions = $viewData[ 'roleLevelOptions' ] ?? [];

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'html_head.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'page_header.php' );

$viewData = [
    'message' => $message,
    'error' => $error,
    'menuList' => $menuList,
    // 権限マスターから取得した選択肢をViewへ引き継ぐ。
    'roleLevelOptions' => $roleLevelOptions,
    'requestGuard' => $menuRequestGuard,
];

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'menu_view.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'page_footer.php' );
?>

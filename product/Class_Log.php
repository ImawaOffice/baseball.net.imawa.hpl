<?php
# **********************************************************
# Logを使用するクラス
# **********************************************************
$g_Log = new Class_Log();

class Class_Log {
	#=======================================================
	# 変数定義
	#=======================================================
	private $LogPath;
	private $LogFileName;
	private $LogFileSuffix;
	private $LogFilePath;
	private $LogLevel;
	// エラーレベルを文字列に変換
	private $LogLevelString = [
		E_ERROR => '*ERROR',
		E_WARNING => '*WARNING',
		E_NOTICE => 'NOTICE',
		E_USER_ERROR => 'ERROR',
		E_USER_WARNING => 'WARNING',
		E_USER_NOTICE => 'DEBUG',
	];

	#=======================================================
	# コンストラクタ
	#=======================================================
	public function __construct() {

		// 設定ファイル読み込み
		require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_config.php' );
		global $g_Log_Path, $g_Log_Suffix, $g_Log_Level;

		// ログパス設定
		$this->LogPath = $g_Log_Path;
		$this->LogFileSuffix = $g_Log_Suffix;
		$this->LogLevel = $g_Log_Level;
		$this->LogFileName = "";
		$this->LogFilePath = "";

		// ログパスの存在確認
		if( ! $this->existsLogPath( true ) ) {
			throw new Exception( "ログパスの作成に失敗しました。 LogPath : {$this->LogPath}" );
		}

		// ログファイル名の生成
		$this->LogFileName = $this->createLogFileName( $this->LogFileSuffix );

		// PHP.ini設定
		$this->configure();

	}

	#=======================================================
	# ログ出力パス確認
	# @param p_MakeDirectory ディレクトリが存在しない場合に作成するかどうか
	# @return bool チェック結果(true: 存在する、false: 存在しない)
	#=======================================================
	private function existsLogPath( $p_MakeDirectory = true ) {

		if ( ! is_dir( $this->LogPath ) ) {	// ディレクトリが存在しない場合
			if ( $p_MakeDirectory ) {	// ディレクトリを作成する場合
				mkdir( $this->LogPath, 0777, true );	// 再帰的にディレクトリ作成
			} else {
				return false;
			}
		}
		return true;
	}

	#=======================================================
	# ログファイル名の生成
	# @param p_suffix ログファイル名の接尾辞
	# @return ログファイル名
	#=======================================================
	private function createLogFileName( $p_suffix = "" ) {

		// ログファイル名の接頭辞作成
		$prefix = date( "Ymd" );

		// ログ接尾辞の拡張子確認
		$filename = $p_suffix;
		$extension = pathinfo( $filename, PATHINFO_EXTENSION );

		// 拡張子が存在しない場合は"log"を設定
		if ( ! $extension ) {
			$extension = "log";
		}

		// 拡張子を除いたベース名取得
		$basename = basename( $filename, '.' . $extension );

		// ログファイル名生成
		$logFileName = $prefix . "_" . $basename . "." . $extension;

		$this->LogFileName = $logFileName;

		// フルパス生成
		$fullpath = rtrim( $this->LogPath, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . ltrim( $this->LogFileName, DIRECTORY_SEPARATOR );
		$this->LogFilePath = $fullpath;

		return $this->LogFileName;
	}

	#=======================================================
	# PHP.ini設定
	# @param	void
	# @return	void
	#=======================================================
	private function configure() {

		// クラス内でPHP設定を変更
		ini_set( 'date.timezone', 'Asia/Tokyo' );
		ini_set( 'display_errors', 'Off' );
		ini_set( 'log_errors', 'On' );
		ini_set( 'error_log', $this->LogFilePath );

		// エラーハンドラ登録
		$this->register();
	}

	#=======================================================
	# エラーハンドラの登録
	# @param	void
	# @return	void
	#=======================================================
	private function register() {
		
		set_error_handler( [ $this, 'handleError' ] );

		// 致命的エラーもキャッチするためshutdownハンドラ登録
		register_shutdown_function( [ $this, 'handleShutdown' ] );
	}

	#=======================================================
	# シャットダウンハンドラ（致命的エラー対応）
	#=======================================================
	public function handleShutdown() {
		
		$error = error_get_last();
		
		if ( $error !== null ) {
			$type = $error[ 'type' ];

			// 致命的エラーのみログ出力
			if ( in_array( $type, [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ] ) ) {
				$msg = "SHUTDOWN ERROR[ $type ]  : {$error['message']} in {$error['file']} on line {$error['line']}";
				$this->error( $msg, __FUNCTION__, basename( __FILE__ ) );
		
				// エラー画面に遷移（error.phpを想定）
				if ( ! headers_sent() ) {
					header( 'Location: ./error.php' );
					exit;
				}
			}
		}
	}

	#=======================================================
	# エラーハンドラの設定
	# @param	$p_errno エラーナンバー
	# @param	$p_errstr エラーメッセージ
	# @param	$p_errfile エラー発生ファイル
	# @param	$p_errline エラー発生行番号
	# @return	void
	#=======================================================
	public function handleError( $p_errno, $p_errstr, $p_errfile, $p_errline ) {
	
		$errorMessages = "HANDLE ERROR[ $p_errno ]  : $p_errstr in $p_errfile on line $p_errline";
	
		$this->error( $errorMessages, __FUNCTION__, basename( __FILE__ ) );

	}

	#=======================================================
	# ログレベルのチェック
	# @param	$p_errorLevel エラーレベル
	# @return	bool チェック結果(true: 出力対象、false: 出力対象外)
	#=======================================================
	private function checkLogLevel( $p_errorLevel ) {

		// ログレベル（0: 無効、1: エラーのみ、2: エラー＋警告、4: エラー＋警告＋情報、8: エラー＋警告＋情報＋デバッグ情報）

		// ログレベルチェック
		switch ( $p_errorLevel ) {
			case E_ERROR:
			case E_USER_ERROR:
				switch( $this->LogLevel ) {	// 0: 無効は出力対象外
					case 0:
						return false;
					default:
						return true;
				}
				break;
			case E_WARNING:
			case E_USER_WARNING:
				switch( $this->LogLevel ) {	// 0: 無効、1: エラーのみは出力対象外
					case 0:
					case 1:
						return false;
					default:
						return true;
				}
				break;
			case E_NOTICE:
				switch( $this->LogLevel ) {	// 0: 無効、1: エラーのみ、2: エラー＋警告は出力対象外
					case 0:
					case 1:
					case 2:
						return false;
					default:
						return true;
				}
				break;
			case E_USER_NOTICE:
				switch( $this->LogLevel ) {	// 0: 無効、1: エラーのみ、2: エラー＋警告、4: エラー＋警告＋情報は出力対象外
					case 0:
					case 1:
					case 2:
					case 4:
						return false;
					default:
						return true;
				}
			default:	// 未定義のエラーレベルは出力対象外
				return false;
		}

		return true;
	}

	#=======================================================
	# ログファイルへの書込み（エラー）
	# @param	$p_Message メッセージ
	# @return	void
	#=======================================================
	public function error( $p_Message, $p_callerFunction = '', $p_callerFile = '' ) {
		
		if( $p_callerFile === '' ) {
			// 呼び出し元ファイル名取得
			$trace = debug_backtrace();
			$p_callerFile = isset( $trace[ 0 ][ 'file' ] ) ? basename( $trace[ 0 ][ 'file' ] ) : '';
		}
		
		$this->writeLog( $p_Message, E_USER_ERROR, $p_callerFile, $p_callerFunction );

	}

	#=======================================================
	# ログファイルへの書込み（警告）
	# @param	$p_Message メッセージ
	# @return	void
	#=======================================================
	public function warning( $p_Message, $p_callerFunction = '', $p_callerFile = '' ) {
		
		if( $p_callerFile === '' ) {
			// 呼び出し元ファイル名取得
			$trace = debug_backtrace();
			$p_callerFile = isset( $trace[ 0 ][ 'file' ] ) ? basename( $trace[ 0 ][ 'file' ] ) : '';
		}
		
		$this->writeLog( $p_Message, E_USER_WARNING, $p_callerFile, $p_callerFunction );
		
	}

	#=======================================================
	# ログファイルへの書込み（情報）
	# @param	$p_Message メッセージ
	# @return	void
	#=======================================================
	public function notice( $p_Message, $p_callerFunction = '', $p_callerFile = '' ) {
		
		if( $p_callerFile === '' ) {
			// 呼び出し元ファイル名取得
			$trace = debug_backtrace();
			$p_callerFile = isset( $trace[ 0 ][ 'file' ] ) ? basename( $trace[ 0 ][ 'file' ] ) : '';
		}
		
		$this->writeLog( $p_Message, E_NOTICE, $p_callerFile, $p_callerFunction );
		
	}

	#=======================================================
	# ログファイルへの書込み（デバッグ情報）
	# @param	$p_Message メッセージ
	# @return	void
	#=======================================================
	public function debug( $p_Message, $p_callerFunction = '', $p_callerFile = '' ) {
		
		if( $p_callerFile === '' ) {
			// 呼び出し元ファイル名取得
			$trace = debug_backtrace();
			$p_callerFile = isset( $trace[ 0 ][ 'file' ] ) ? basename( $trace[ 0 ][ 'file' ] ) : '';
		}

		$this->writeLog( $p_Message, E_USER_NOTICE, $p_callerFile, $p_callerFunction );
		
	}

	#=======================================================
	# ログファイルへの書込み
	# @param	$p_Message メッセージ
	# @param	$p_errorLevel エラーレベル
	# @param	$p_callerFile 呼び出し元ファイル名
	# @return	void
	#=======================================================
	public function writeLog( $p_Message, $p_errorLevel = E_USER_NOTICE, $p_callerFile = '', $p_callerFunction = '' ) {

		// ログレベルチェック
		if ( ! $this->checkLogLevel( $p_errorLevel ) ) {
			return;
		}

		// エラーレベルを文字列に変換
		$errorLevel = $this->LogLevelString[ $p_errorLevel ] ?? 'UNKNOWN';

		// 日時を取得
		$timestamp = date( 'Y-m-d H:i:s' );

		// ログメッセージ作成
		$message = "";
		$message .= "[$timestamp] ";
		$message .= "[$errorLevel] ";
		$message .= $p_callerFile === "" ? "" : "[$p_callerFile] ";
		$message .= $p_callerFunction === "" ? "" : "[$p_callerFunction] ";
		$message .= "$p_Message";
		$message .= PHP_EOL;
		
		// ファイル名の設定
		$this->createLogFileName( $this->LogFileSuffix );

		// ファイルに書き込み
		error_log( $message, 3, $this->LogFilePath );

	}
}
?>

<?php
# **********************************************************
# データベースを使用するクラス
# **********************************************************
# ==========================================================
# 必要なファイル読み込み
# ==========================================================
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'Class_Log.php' );

# ==========================================================
# 変数の定義
# ==========================================================

# ==========================================================
# クラスの定義
# Class_Logを継承
# ==========================================================
class Class_Database extends Class_Log {
	#=======================================================
	# 変数定義
	#=======================================================
	private $DbType; // 'sqlite' または 'mysql'
	private $DbHost;
	private $DbPort = 3306;
	private $DbName;
	private $DbUser;
	private $DbPass;
	private $PDO;
	private $Log;

	#=======================================================
	# コンストラクタ
	# @param	String	p_DbType 	データベースタイプ('sqlite' または 'mysql')
	# @param	String	p_HostName	ホスト名
	# @param	Integer	p_DbPort	ポート番号
	# @param	String	p_Database	データベース名
	# @param	String	p_Username	ユーザー名
	# @param	String	p_Password	パスワード
	# @return	void
	#=======================================================
	public function __construct( $p_DbType = 'sqlite', $p_HostName = '', $p_DbPort = 3306, $p_Database = '', $p_Username = '', $p_Password = '' ) {

		// ログクラス生成
		$this->Log = new Class_Log();

		// ログパス設定
		$this->DbType  = $p_DbType;
		$this->DbHost  = $p_HostName;
		$this->DbPort  = $p_DbPort;
		$this->DbName  = $p_Database;
		$this->DbUser  = $p_Username;
		$this->DbPass  = $p_Password;
		$this->PDO     = null;

		// データベース接続
		$this->openDatabase();

	}

	#=======================================================
	# データベースを開く
	# @param	void
	# @return	bool チェック結果(true: OK、false: NG)
	#=======================================================
	public function openDatabase() {
		
		$Db_Type = $this->DbType;
		$Db_Host = $this->DbHost;
		$Db_Port = $this->DbPort;
		$Db_Name = $this->DbName;
		$Db_User = $this->DbUser;
		$Db_Pass = $this->DbPass;
		$Db_Conn = null;

		switch( $Db_Type ) {
			case 'mysql':
				$Db_Conn = "$Db_Type:host=$Db_Host;port=$Db_Port;dbname=$Db_Name;charset=utf8";
				break;
			case 'sqlite':
				$Db_Conn = "$Db_Type:$Db_Host";
				break;
			default:
				$this->Log->error( "サポートされていないデータベースタイプです： $Db_Type", __FUNCTION__, basename( __FILE__ ) );
				return false;
		}

		try {
			$this->PDO = new PDO( "$Db_Conn", $Db_User, $Db_Pass );
			$this->Log->debug( "データベースに接続しました： $Db_Conn", __FUNCTION__, basename( __FILE__ ) );

			// PDOでエラーが発生したとき、例外（Exception）としてスローする
			$this->PDO->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch( PDOException $e ){
			$this->Log->error( "データベースの接続に失敗しました： {$Db_Conn}" . PHP_EOL .$e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		return true;
	}

	#=======================================================
	# データベースを閉じる
	# @param	void
	# @return	void
	#=======================================================
	public function closeDatabase() {
	
		$this->PDO = null;
		$this->Log->debug( "データベース接続を閉じました。", __FUNCTION__, basename( __FILE__ ) );

	}

	#=======================================================
	# SELECT
	# @param	String	p_SQL		実行するSQL文
	# @param	array	p_Parameters	バインドするパラメータの配列
	# @return	void
	#=======================================================
	public function select( $p_SQL = "", $p_Parameters = array() ) {

		$this->Log->debug( "SQL : $p_SQL", __FUNCTION__, basename( __FILE__ ) );

		$stmt = null;

		try {
			$stmt = $this->PDO->prepare( $p_SQL );

			foreach ( $p_Parameters as $key => $value ) {
				// データ型を自動判別してバインド
				$paramType = null;
				if ( is_null( $value ) ) {
					$paramType = PDO::PARAM_NULL;
				} elseif ( is_int( $value ) ) {
					$paramType = PDO::PARAM_INT;
				} else {
					$paramType = PDO::PARAM_STR; // 数値文字列やその他は文字列扱い
				}
				$this->Log->debug( "Bind : $key = $value", __FUNCTION__, basename( __FILE__ ) );

				$stmt->bindValue( ":$key", $value, $paramType );
			}

			$dataTable = array();

			if( $stmt->execute() ){
				$dataTable = $stmt->fetchAll( PDO::FETCH_ASSOC );
			}

			$this->Log->debug( "SQL : " . count( $dataTable ) . " 件取得しました。", __FUNCTION__, basename( __FILE__ ) );

		} catch ( Exception $e ) {
			$this->Log->error( $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		return $dataTable;
	}

	#=======================================================
	# EXECUTE
	# @param	String	p_suffix ログファイル名の接尾辞
	# @return	String	ログファイル名
	#=======================================================
	public function execute( $p_SQL = "", $p_Parameters = array() ) {

		$this->Log->debug( "SQL : $p_SQL", __FUNCTION__, basename( __FILE__ ) );

		$stmt = null;
		$rowCount = 0;

		try {
			$stmt = $this->PDO->prepare( $p_SQL );

			foreach ( $p_Parameters as $key => $value ) {
				// データ型を自動判別してバインド
				$paramType = null;
				if ( is_null( $value ) ) {
					$paramType = PDO::PARAM_NULL;
				} elseif ( is_int( $value ) ) {
					$paramType = PDO::PARAM_INT;
				} else {
					$paramType = PDO::PARAM_STR; // 数値文字列やその他は文字列扱い
				}
				$this->Log->debug( "Bind : $key = $value", __FUNCTION__, basename( __FILE__ ) );

				$stmt->bindValue( ":$key", $value, $paramType );
			}

			$stmt->execute();
			$rowCount = $stmt->rowCount();

		} catch ( Exception $e ) {
			$this->Log->error( $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		return $rowCount;
	}

	#=======================================================
	# EXECUTE2
	# @param	String	p_suffix ログファイル名の接尾辞
	# @return	String	ログファイル名
	#=======================================================
	public function execute2( $p_SQL = "", $p_Parameters = array() ) {

		$this->Log->debug( "SQL : $p_SQL", __FUNCTION__, basename( __FILE__ ) );

		$stmt = null;
		$rowCount = 0;
		$new_id = 0;

		$returnValue = array(
			"rowCount" => 0,
			"newId"    => 0
		);

		try {
			$stmt = $this->PDO->prepare( $p_SQL );

			foreach ( $p_Parameters as $key => $value ) {
				// データ型を自動判別してバインド
				$paramType = null;
				if ( is_null( $value ) ) {
					$paramType = PDO::PARAM_NULL;
				} elseif ( is_int( $value ) ) {
					$paramType = PDO::PARAM_INT;
				} else {
					$paramType = PDO::PARAM_STR; // 数値文字列やその他は文字列扱い
				}
				$this->Log->debug( "Bind : $key = $value", __FUNCTION__, basename( __FILE__ ) );

				$stmt->bindValue( ":$key", $value, $paramType );
			}

			$stmt->execute();
			$rowCount = $stmt->rowCount();
			$new_id = $this->PDO->lastInsertId();
			$this->Log->debug( "SQL : " . $rowCount . " 件実行しました。新しいID : " . $new_id, __FUNCTION__, basename( __FILE__ ) );
		} catch ( Exception $e ) {
			$this->Log->error( $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
			return false;
		}

		$returnValue["rowCount"] = $rowCount;
		$returnValue["newId"] = $new_id;
		return $returnValue;
	}
}
?>

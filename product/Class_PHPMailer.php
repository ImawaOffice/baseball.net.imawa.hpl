<?php
# **********************************************************
# PHPMailer.php
# PHPMailerを使用するクラス
# **********************************************************
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'PHPMailer/src/Exception.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'PHPMailer/src/PHPMailer.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'PHPMailer/src/SMTP.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'Class_Log.php' );

class Class_PHPMailer {
	#=======================================================
	# 変数定義
	#=======================================================
	private $mail;
	private $Log;

	#=======================================================
	# コンストラクタ
	#=======================================================
	public function __construct() {
		
		// ログクラス生成
		$this->Log = new Class_Log();

		// PHPMailerオブジェクト生成
		$this->mail = null;

		try {
			$this->mail = new PHPMailer( true );
		} catch ( Exception $e ) {
			$this->Log->error( "PHPMailerオブジェクトの生成に失敗しました。 Error: " . $e->getMessage(), __FUNCTION__, basename( __FILE__ ) );
			throw new Exception( "PHPMailerオブジェクトの生成に失敗しました。 Error: " . $e->getMessage() );
		}

		// 設定ファイル読み込み
		require_once( __DIR__ . DIRECTORY_SEPARATOR . 'baseball_config.php' );
		global $CONFIG;

		// サーバ設定
		$SMTP_Host        = $CONFIG[ 'SMTP_Host' ];
		$SMTP_Username    = $CONFIG[ 'SMTP_Username' ];
		$SMTP_Password    = $CONFIG[ 'SMTP_Password' ];
		$SMTP_Port        = $CONFIG[ 'SMTP_Port' ];
		$SMTP_Secure      = $CONFIG[ 'SMTP_Secure' ];
		$MAIL_FromAddress = $CONFIG[ 'Mail_FromAddress' ];
		$MAIL_FromName    = $CONFIG[ 'Mail_FromName' ];

		$this->mail->isSMTP();
		$this->mail->Host       = $SMTP_Host;
		$this->mail->SMTPAuth   = true;
		$this->mail->Username   = $SMTP_Username;
		$this->mail->Password   = $SMTP_Password;
		$this->mail->Port       = $SMTP_Port;
		$this->Log->debug( "SMTP HOST: {$SMTP_Host}, Port: {$SMTP_Port}, Secure: {$SMTP_Secure}", __FUNCTION__, basename( __FILE__ ) );

		$secure = strtolower( $SMTP_Secure );

		if ( $secure === 'ssl' ) {
			$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		} else {
			$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		}
		
		// 送信者設定
		$this->mail->setFrom( $MAIL_FromAddress, $MAIL_FromName );

		$this->Log->debug( "SMTP From: {$MAIL_FromAddress}, FromName: {$MAIL_FromName}", __FUNCTION__, basename( __FILE__ ) );

	}
	#=======================================================
	# メール送信
	#=======================================================
	public function sendMail( $to, $subject, $body, $cc = null, $bcc = null ) {

		$this->Log->debug( "To: $to, Subject: $subject, Body: $body, CC: $cc, BCC: $bcc", __FUNCTION__, basename( __FILE__ ) );

		$toArray = explode( ',', $to );
		$ccArray = explode( ',', $cc ?? '' );
		$bccArray = explode( ',', $bcc ?? '' );

		try {
			$this->mail->clearAllRecipients();
			$this->mail->CharSet = 'UTF-8';
			$this->mail->Encoding  = 'base64';
			foreach ( $toArray as $toaddress ) {
				if ( ! empty( trim( $toaddress ) ) ) {
					$this->mail->addAddress( trim( $toaddress ) );
				}
			}
			foreach ( $ccArray as $ccAddress ) {
				if ( ! empty( trim( $ccAddress ) ) ) {
					$this->mail->addCC( trim( $ccAddress ) );
				}
			}
			foreach ( $bccArray as $bccAddress ) {
				if ( ! empty( trim( $bccAddress ) ) ) {
					$this->mail->addBCC( trim( $bccAddress ) );
				}
			}
			$this->mail->isHTML( true );
			$this->mail->Subject = mb_encode_mimeheader( $subject, 'UTF-8' );
			$this->mail->Body    = $body;
			$this->mail->send();
			$this->Log->notice( "Mail sent successfully to: $to, Subject: $subject", __FUNCTION__, basename( __FILE__ ) );
			return true;
		} catch ( Exception $e ) {
			$this->Log->error( "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}", __FUNCTION__, basename( __FILE__ ) );
			return false;
		}
	}
}
?>

<link rel="stylesheet" href="markdown-style.css">

# 野球大会管理システム 内部設計書

<script>
	document.documentElement.style.setProperty( '--pageHeaderTitle', `"野球大会管理システム 内部設計書"` );
	document.documentElement.style.setProperty( "--pageFooterDate", `"2026-06-21"` );
	document.documentElement.style.setProperty( "--pageFooterAuthor", `&copy; "Imawa Office"` );
</script>

## 目次 <!-- omit in toc -->

<div class="column2">

1. [1. 文書概要](#1-文書概要)
2. [2. システムアーキテクチャ](#2-システムアーキテクチャ)
	1. [2.1. ファイル構成](#21-ファイル構成)
	2. [2.2. 処理フロー共通パターン](#22-処理フロー共通パターン)
3. [3. クラス設計](#3-クラス設計)
	1. [3.1. Class\_Log](#31-class_log)
	2. [3.2. Class\_Database](#32-class_database)
	3. [3.3. Class\_PHPMailer](#33-class_phpmailer)
4. [4. データベース設計](#4-データベース設計)
	1. [4.1. DB構成概要](#41-db構成概要)
	2. [4.2. ER図（テキスト）](#42-er図テキスト)
	3. [4.3. テーブル定義](#43-テーブル定義)
		1. [4.3.1. config（設定テーブル）](#431-config設定テーブル)
		2. [4.3.2. menu（メニューテーブル）](#432-menuメニューテーブル)
		3. [4.3.3. baseball\_role（権限テーブル）](#433-baseball_role権限テーブル)
		4. [4.3.4. baseball\_user（ユーザーテーブル）](#434-baseball_userユーザーテーブル)
		5. [4.3.5. baseball\_tournament（大会テーブル）](#435-baseball_tournament大会テーブル)
		6. [4.3.6. baseball\_team（チームテーブル）](#436-baseball_teamチームテーブル)
		7. [4.3.7. baseball\_game（試合テーブル）](#437-baseball_game試合テーブル)
		8. [4.3.8. baseball\_information（お知らせテーブル）](#438-baseball_informationお知らせテーブル)
		9. [4.3.9. baseball\_inquiry（問い合わせテーブル）](#439-baseball_inquiry問い合わせテーブル)
		10. [4.3.10. baseball\_attachment（添付ファイルテーブル）](#4310-baseball_attachment添付ファイルテーブル)
5. [5. セッション設計](#5-セッション設計)
	1. [5.1. 認証セッション変数](#51-認証セッション変数)
	2. [5.2. フォームセッション変数（共通）](#52-フォームセッション変数共通)
6. [6. 設定管理設計](#6-設定管理設計)
	1. [6.1. config テーブルキー一覧](#61-config-テーブルキー一覧)
7. [7. メニュー設計](#7-メニュー設計)
	1. [7.1. メニュー階層構造](#71-メニュー階層構造)
8. [8. 認証・権限設計](#8-認証権限設計)
	1. [8.1. 認証チェック処理](#81-認証チェック処理)
	2. [8.2. 権限レベル定義](#82-権限レベル定義)
9. [9. ログ設計](#9-ログ設計)
	1. [9.1. ログレベル定義](#91-ログレベル定義)
	2. [9.2. ログファイル命名規則](#92-ログファイル命名規則)
10. [10. セキュリティ設計](#10-セキュリティ設計)
11. [11. 環境設定設計](#11-環境設定設計)
12. [12. 変更履歴](#12-変更履歴)

</div>

---

## 1. 文書概要

| 項目 | 内容 |
|:--|:--|
| 文書名 | 野球大会管理システム 内部設計書 |
| システム名 | HPL 野球大会管理システム |
| 作成日 | 2026-06-21 |
| バージョン | 1.0 |
| 作成者 | Imawa Office |

本書は、野球大会管理システムの内部設計（アーキテクチャ・クラス設計・DB設計）を定義する。  
実装者がソースコードを読み解き、改修・保守を行うための技術仕様を記載する。

> 本書は現行ソースコードと DB 定義を正本として扱い、実装済みの処理・テーブル・セッション・権限制御を中心に整理した内容である。試作ロジックや将来拡張の項目は、将来拡張または補足として区別する。

---

## 2. システムアーキテクチャ

### 2.1. ファイル構成

```
baseball/
├── baseball_config.php      # システム設定・DB接続・セッション・メニュー取得
├── baseball_function.php    # 共通関数群（バリデーション・認証ヘルパー等）
├── Class_Log.php            # ログ出力クラス（グローバル $g_Log）
├── Class_Database.php       # DB抽象化クラス（PDO ラッパー）
├── Class_PHPMailer.php      # メール送信クラス（PHPMailer ラッパー）
├── html_head.php            # HTML <head> 共通部品
├── page_header.php          # ページヘッダー共通部品
├── page_footer.php          # ページフッター共通部品
├── scrollTop.php            # スクロールトップボタン
├── error.php                # エラーページ
│
├── index.php                # トップページ（サインアップ処理含む）
├── signin.php               # サインイン
├── signout.php              # サインアウト
├── signup.php               # サインアップ（メール認証・PW設定）
├── passwordchange.php       # パスワード変更
├── profile.php              # プロフィール
├── contact.php              # お問い合わせ
├── contactlist.php          # お問い合わせ一覧
├── tournamentoverview.php   # 大会概要
├── tournamentresults.php    # 大会結果（トーナメント表）
├── tournamentrules.php      # 大会規定
├── gamerules.php            # 試合規定
├── gameschedule.php         # 試合スケジュール（ルーター）
├── gameschedule1.php        # 試合スケジュール（総当たり）
├── gameschedule2.php        # 試合スケジュール（ベスト8）
├── gameschedule3.php        # 試合スケジュール（裏トーナメント）
├── gameschedule4.php        # 試合スケジュール（決勝）
├── gamematch.php            # 試合対戦
├── gamemaintenance.php      # 試合管理（本部）
├── tournamentmaintenance.php # 大会管理（本部）
├── teammaintenance.php      # チーム管理（本部）
├── rulemaintenance.php      # ルール管理（本部）
├── userlist.php             # ユーザー一覧（管理者）
├── teamentry.php            # チームエントリー
│
├── article_information.php  # お知らせ記事（index に include）
├── article_tournament-rules.php  # 大会規定記事
├── article_game-rules.php        # 試合規定記事
├── article_sponsor.php           # スポンサー記事
├── article_sns.php               # SNS 記事
├── article_tournament-overview.php # 大会概要記事
├── article_tournament-results.php  # 大会結果記事
├── article_*.php            # その他 article 系部品
│
├── install/                 # DB インストール用 SQL
├── attachment/              # アップロードファイル保存先
├── images/                  # 画像ファイル
├── Log/                     # ログ出力先
└── documents/               # 設計・資料
```

### 2.2. 処理フロー共通パターン

全ての処理PHPファイルは以下の共通パターンで実装されている：

```php
require_once('baseball_function.php');  // または baseball_config.php

// 主処理
if( initProc() ){
    if( requestProc() ) {
        if( checkProc() ) {
            if( setProc() ) { }
        }
    }
}
if( endProc() ) { }

// HTML出力部
require_once('html_head.php');
// <body> 〜 </body>
require_once('page_header.php');
// メインコンテンツ
require_once('page_footer.php');
```

| 関数名 | 役割 |
|:--|:--|
| `initProc()` | 初期化処理・認証チェック・セッション変数初期化 |
| `requestProc()` | リクエスト種別判定（GET/POST）。GETは処理スキップ |
| `checkProc()` | 入力バリデーション・ビジネスロジックチェック |
| `setProc()` | DB更新・メール送信等の副作用処理 |
| `endProc()` | 終了処理（DB切断等） |

---

## 3. クラス設計

### 3.1. Class_Log

**ファイル：** `Class_Log.php`  
**役割：** アプリケーション全体のログ出力を管理する

**主要メソッド**

| メソッド | 引数 | 説明 |
|:--|:--|:--|
| `__construct()` | - | `baseball_config.php` から設定読み込み・ログパス確認 |
| `notice($msg, $func, $file)` | メッセージ・関数名・ファイル名 | 情報ログ出力（レベル4以上） |
| `debug($msg, $func, $file)` | メッセージ・関数名・ファイル名 | デバッグログ出力（レベル8以上） |
| `warning($msg, $func, $file)` | メッセージ・関数名・ファイル名 | 警告ログ出力（レベル2以上） |
| `error($msg, $func, $file)` | メッセージ・関数名・ファイル名 | エラーログ出力（常に） |

**グローバル変数**

| 変数名 | 説明 |
|:--|:--|
| `$g_Log` | グローバルログインスタンス（`Class_Log.php` 読み込み時に自動生成） |
| `$g_Log_Level` | ログ出力レベル（`baseball_config.php` で設定） |
| `$g_Log_Path` | ログファイル出力ディレクトリ |
| `$g_Log_Suffix` | ログファイル名接尾辞（`baseball.log`） |

---

### 3.2. Class_Database

**ファイル：** `Class_Database.php`  
**役割：** SQLite / MySQL を PDO で抽象化したDBアクセスクラス

**継承：** `Class_Log` を継承

**コンストラクタ引数**

| 引数名 | 型 | デフォルト | 説明 |
|:--|:--|:--|:--|
| `$p_DbType` | string | `'sqlite'` | `'sqlite'` または `'mysql'` |
| `$p_HostName` | string | `''` | ホスト名（MySQL）またはファイルパス（SQLite） |
| `$p_DbPort` | int | `3306` | MySQL のポート番号 |
| `$p_Database` | string | `''` | データベース名（MySQL） |
| `$p_Username` | string | `''` | ユーザー名（MySQL） |
| `$p_Password` | string | `''` | パスワード（MySQL） |

**主要メソッド**

| メソッド | 返り値 | 説明 |
|:--|:--|:--|
| `openDatabase()` | bool | PDO 接続を確立する |
| `select($sql, $params)` | array | SELECT クエリを実行し、連想配列の配列を返す |
| `execute($sql, $params)` | PDOStatement\|false | INSERT/UPDATE/DELETE を実行する |

**グローバル変数**

| 変数名 | 説明 |
|:--|:--|
| `$g_DB_Config` | 設定DB接続インスタンス（SQLite; config・menu テーブル） |
| `$g_DB` | 業務DB接続インスタンス（MySQL; 各業務テーブル） |

---

### 3.3. Class_PHPMailer

**ファイル：** `Class_PHPMailer.php`  
**役割：** PHPMailer ライブラリをラップしたメール送信クラス

**主要メソッド**

| メソッド | 引数 | 返り値 | 説明 |
|:--|:--|:--|:--|
| `sendMail($to, $subject, $body)` | 宛先・件名・本文(HTML) | bool | SMTPでメール送信。成功時 true |

**SMTP設定ソース：** `config` テーブルの以下キーを参照

| 設定キー | 説明 |
|:--|:--|
| `SMTP_Host` | SMTPサーバーホスト |
| `SMTP_Username` | SMTPユーザー名 |
| `SMTP_Password` | SMTPパスワード |
| `SMTP_Port` | SMTPポート番号 |
| `SMTP_Secure` | 暗号化方式（`ssl` / `tls`） |
| `Mail_FromAddress` | 送信元メールアドレス |
| `Mail_FromName` | 送信元名 |

---

## 4. データベース設計

### 4.1. DB構成概要

| DB | 種別 | ファイル名 | 用途 |
|:--|:--|:--|:--|
| 設定DB | SQLite | `baseball_develop.db` / `baseball_production.db` | config・menu テーブル |
| 業務DB | MySQL（本番）/ MySQL（開発） | `LAA0938815-hpl` | 業務テーブル全般 |

**環境判定：**  
`$_SERVER["SERVER_NAME"] == "hpl.imawa.net"` → production  
それ以外 → develop

---

### 4.2. ER図（テキスト）

```
config (設定)
menu (メニュー)

baseball_role (権限)
    └─ baseball_user.role_level → baseball_role.role_level

baseball_tournament (大会)
    ├─ baseball_team.tournament_id → baseball_tournament.tournament_id
    └─ baseball_game.tournament_id → baseball_tournament.tournament_id

baseball_team (チーム)
    ├─ baseball_game.team1_id → baseball_team.team_id
    └─ baseball_game.team2_id → baseball_team.team_id

baseball_game (試合)
    ├─ baseball_game.winner_id → baseball_team.team_id
    └─ baseball_game.loser_id → baseball_team.team_id

baseball_attachment (添付ファイル)
    └─ baseball_tournament.tournament_attachment_id → baseball_attachment.attachment_id

baseball_information (お知らせ) ← 独立
baseball_inquiry (問い合わせ) ← 独立
```

---

### 4.3. テーブル定義

#### 4.3.1. config（設定テーブル）

**DB：** 設定DB（SQLite）  
**用途：** システム設定値（DB接続情報・SMTP設定・著作権表示等）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| config_key | TEXT | ○ | - | 設定キー（主キー） |
| config_value | TEXT | ○ | - | 設定値 |
| config_type | TEXT | ○ | - | データ型（`TEXT` / `INTEGER`） |
| is_enabled | INTEGER | ○ | 1 | 有効フラグ |
| created_at | TEXT | ○ | `datetime('now','localtime')` | 作成日時 |
| created_by | TEXT | - | - | 作成者 |
| updated_at | TEXT | ○ | `datetime('now','localtime')` | 更新日時（トリガー自動更新） |
| updated_by | TEXT | - | - | 更新者 |

---

#### 4.3.2. menu（メニューテーブル）

**DB：** 設定DB（SQLite）  
**用途：** ヘッダー・フッターのナビゲーションメニュー定義

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| menu_id | INTEGER | ○ | - | メニューID（主キー） |
| menu_name | TEXT | ○ | - | メニュー表示名 |
| menu_url | TEXT | ○ | - | リンクURL |
| parent_id | INTEGER | ○ | 0 | 親メニューID（0 = ルート） |
| display_order | INTEGER | ○ | 0 | 表示順 |
| role_min | INTEGER | ○ | 0 | 表示最小権限レベル |
| role_max | INTEGER | ○ | 0 | 表示最大権限レベル |
| is_enabled | INTEGER | ○ | 0 | 有効フラグ |
| created_at | TEXT | ○ | `datetime('now','localtime')` | 作成日時 |
| updated_at | TEXT | ○ | `datetime('now','localtime')` | 更新日時 |

**メニュー構造（主要）**

| menu_id | parent_id | 表示名 | 最小権限 | URL |
|:--:|:--:|:--|:--:|:--|
| 1000 | 0 | HEADER（ルート） | 0 | - |
| 1010 | 1000 | ホーム | 0 | ./ |
| 1200 | 1000 | 本部メニュー | 100 | # |
| 1300 | 1000 | サインイン | 0 | # （未ログイン時のみ表示） |
| 1400 | 1000 | ユーザ情報 | 1 | # |
| 2000 | 0 | FOOTER（ルート） | 0 | - |

---

#### 4.3.3. baseball_role（権限テーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| role_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| role_name | VARCHAR(255) | ○ | - | 権限名称 |
| role_level | INT | ○ | - | 権限レベル（UNIQUE） |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成日時 |
| created_by | VARCHAR(255) | - | - | 作成者 |
| updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 更新日時 |
| updated_by | VARCHAR(255) | - | - | 更新者 |

**初期データ**

| role_name | role_level |
|:--|:--:|
| 一般 | 0 |
| 登録済み | 1 |
| 監督 | 10 |
| 本部 | 100 |
| システム管理者 | 1000 |

---

#### 4.3.4. baseball_user（ユーザーテーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| user_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| user_cd | VARCHAR(255) | ○ | - | ユーザーコード（メールアドレス、UNIQUE） |
| password | VARCHAR(255) | ○ | - | パスワードハッシュ（`password_hash`） |
| username | VARCHAR(255) | ○ | - | ユーザー名 |
| email | VARCHAR(255) | ○ | - | メールアドレス |
| role_level | INT | ○ | 0 | 権限レベル |
| verification_cd | VARCHAR(255) | ○ | `''` | 認証コード（6桁数字） |
| verification_period | INT | ○ | 0 | 認証有効期限（Unixタイムスタンプ） |
| signin_at | DATETIME | - | - | 最終サインイン日時 |
| signin_by | VARCHAR(255) | - | - | 最終サインインIPまたは端末情報 |
| is_enabled | INT | ○ | 0 | 有効フラグ（0:無効/1:有効） |
| created_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成日時 |
| created_by | VARCHAR(255) | - | - | 作成者 |
| updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 更新日時 |
| updated_by | VARCHAR(255) | - | - | 更新者 |

---

#### 4.3.5. baseball_tournament（大会テーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| tournament_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| tournament_title | VARCHAR(255) | ○ | - | 大会名称 |
| tournament_text | VARCHAR(255) | ○ | - | 大会説明 |
| tournament_start_date | DATE | ○ | - | 大会開始日 |
| tournament_end_date | DATE | ○ | - | 大会終了日 |
| tournament_parent_id | INT | ○ | 0 | 親大会ID（0=なし） |
| tournament1_teams | INT | ○ | 0 | トーナメント1参加チーム数 |
| tournament1_text | VARCHAR(255) | ○ | `''` | トーナメント1説明 |
| tournament2_teams | INT | ○ | 0 | トーナメント2参加チーム数 |
| tournament2_text | VARCHAR(255) | ○ | `''` | トーナメント2説明 |
| tournament3_teams | INT | ○ | 0 | トーナメント3参加チーム数 |
| tournament3_text | VARCHAR(255) | ○ | `''` | トーナメント3説明 |
| tournament_attachment_id | INT | ○ | 0 | 添付ファイルID |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at / updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成・更新日時 |

---

#### 4.3.6. baseball_team（チームテーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| team_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| team_name | VARCHAR(255) | ○ | - | チーム名 |
| team_manager | VARCHAR(255) | ○ | - | 監督名 |
| team_contact | VARCHAR(255) | ○ | - | 担当者名 |
| team_access_cd | VARCHAR(255) | ○ | - | アクセスコード（チーム固有） |
| team_tel | VARCHAR(255) | ○ | - | 電話番号 |
| team_email | VARCHAR(255) | ○ | - | メールアドレス |
| tournament_id | INT | ○ | 0 | 所属大会ID |
| tournament1_attend | INT | ○ | 0 | トーナメント1参加フラグ |
| tournament2_attend | INT | ○ | 0 | トーナメント2参加フラグ |
| tournament3_attend | INT | ○ | 0 | トーナメント3参加フラグ |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at / updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成・更新日時 |

---

#### 4.3.7. baseball_game（試合テーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| game_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| tournament_id | INT | ○ | - | 大会ID |
| game_class | INT | ○ | 0 | 試合区分（0:通常/1:裏T等） |
| game_block | INT | ○ | 1 | ブロック番号 |
| game_count | INT | ○ | 1 | 試合番号 |
| game_name | VARCHAR(255) | ○ | - | 試合名称 |
| game_date | DATETIME | ○ | - | 試合日時 |
| game_place | VARCHAR(255) | ○ | - | 試合場所 |
| winner_id | INT | ○ | - | 勝者チームID |
| loser_id | INT | ○ | - | 敗者チームID |
| team1_id | INT | ○ | - | チーム1 ID |
| team2_id | INT | ○ | - | チーム2 ID |
| team1_name | VARCHAR(255) | ○ | - | チーム1名（非正規化） |
| team2_name | VARCHAR(255) | ○ | - | チーム2名（非正規化） |
| team1_score | INT | ○ | - | チーム1得点 |
| team2_score | INT | ○ | - | チーム2得点 |
| team1_last_game_id | INT | ○ | - | チーム1の前試合ID（トーナメント連鎖） |
| team2_last_game_id | INT | ○ | - | チーム2の前試合ID（トーナメント連鎖） |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at / updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成・更新日時 |

---

#### 4.3.8. baseball_information（お知らせテーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| information_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| title | VARCHAR(255) | ○ | - | タイトル |
| content | TEXT | ○ | - | 本文 |
| post_date | DATE | ○ | - | 投稿日 |
| reference_start_date | DATE | - | - | 表示開始日 |
| reference_end_date | DATE | - | - | 表示終了日 |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at / updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成・更新日時 |

---

#### 4.3.9. baseball_inquiry（問い合わせテーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| inquiry_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| inquiry_at | DATETIME | ○ | CURRENT_TIMESTAMP | 問い合わせ日時 |
| name | VARCHAR(255) | ○ | - | 氏名 |
| phone | VARCHAR(255) | - | - | 電話番号 |
| email | VARCHAR(255) | - | - | メールアドレス |
| message | TEXT | ○ | - | 問い合わせ内容 |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at / updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成・更新日時 |

---

#### 4.3.10. baseball_attachment（添付ファイルテーブル）

**DB：** 業務DB（MySQL）

| カラム名 | 型 | NOT NULL | デフォルト | 説明 |
|:--|:--|:--:|:--|:--|
| attachment_id | INT AUTO_INCREMENT | ○ | - | 主キー |
| attachment_type | INT | ○ | 0 | ファイル種別 |
| physical_file_name | VARCHAR(255) | ○ | - | サーバー上の物理ファイル名 |
| file_name | VARCHAR(255) | ○ | - | 表示用ファイル名 |
| file_path | VARCHAR(255) | ○ | - | ファイルパス（attachment/ 以下） |
| file_size | INT | ○ | 0 | ファイルサイズ（バイト） |
| mime_type | VARCHAR(255) | ○ | - | MIMEタイプ |
| file_description | VARCHAR(255) | ○ | - | ファイル説明 |
| is_enabled | INT | ○ | 0 | 有効フラグ |
| created_at / updated_at | DATETIME | ○ | CURRENT_TIMESTAMP | 作成・更新日時 |

---

## 5. セッション設計

### 5.1. 認証セッション変数

| 変数名 | 型 | 説明 | 設定タイミング |
|:--|:--|:--|:--|
| `AUTH_USER_ID` | int | ログイン中のユーザーID | サインイン成功時 |
| `AUTH_USER_CD` | string | ユーザーコード（メールアドレス） | サインイン成功時 |
| `AUTH_USERNAME` | string | ユーザー名 | サインイン成功時 |
| `AUTH_ROLE_LEVEL` | int | 権限レベル | サインイン成功時 |

### 5.2. フォームセッション変数（共通）

| 変数名 | 型 | 説明 |
|:--|:--|:--|
| `view_state` | int | 画面表示ステータス（ウィザード用） |
| `error_msg` | string | エラーメッセージ |
| `send_button` | string | 送信ボタンのラベル |
| `backto` | int | 前ステップに戻るフラグ |
| `FORM_PAGE_CURRENT` | int | 現在ページ番号（ページング） |
| `FORM_PAGE_ROWS` | int | 1ページ表示行数 |
| `FORM_PAGE_TOTAL` | int | 総ページ数 |

---

## 6. 設定管理設計

### 6.1. config テーブルキー一覧

| config_key | config_type | 説明 |
|:--|:--|:--|
| CopyRight | TEXT | 著作権表示文字列 |
| SMTP_Host | TEXT | SMTPサーバーホスト名 |
| SMTP_Username | TEXT | SMTPユーザー名 |
| SMTP_Password | TEXT | SMTPパスワード |
| SMTP_Port | INTEGER | SMTPポート番号 |
| SMTP_Secure | TEXT | 暗号化方式（`ssl`/`tls`） |
| Mail_FromAddress | TEXT | 送信元メールアドレス |
| Mail_FromName | TEXT | 送信元名 |
| DbType | TEXT | 業務DBタイプ（`mysql`/`sqlite`） |
| DbHostName | TEXT | 業務DBホスト名 |
| DbPort | INTEGER | 業務DBポート番号 |
| DbName | TEXT | 業務DB名 |
| DbUsername | TEXT | 業務DBユーザー名 |
| DbPassword | TEXT | 業務DBパスワード |

---

## 7. メニュー設計

### 7.1. メニュー階層構造

`baseball_config.php` にて、ページ読み込み時にメニューを動的取得する。

```php
$headerId = getMenuId( "HEADER" );   // HEADER ルートのメニューIDを取得
$HEADER_MENU = getMenu( $headerId ); // 再帰的にサブメニューを取得

$footerId = getMenuId( "FOOTER" );
$FOOTER_MENU = getMenu( $footerId );
```

**`getMenuId()` 処理**  
- `menu` テーブルから `parent_id = 0` かつ `menu_name` 一致のレコードを取得
- `role_min <= AUTH_ROLE_LEVEL <= role_max` でフィルタリング

**`getMenu()` 処理**  
- `menu` テーブルから `parent_id` を再帰的にフォロー
- `display_order ASC` でソート

---

## 8. 認証・権限設計

### 8.1. 認証チェック処理

`baseball_function.php` の `isAuthenticated()` 関数を全ての保護ページで使用する。

```php
function isAuthenticated() {
    return isset($_SESSION['AUTH_USER_ID']) && $_SESSION['AUTH_USER_ID'] > 0;
}
```

**権限チェック例：**

```php
// initProc() 内での認証・権限チェックパターン
if( !isAuthenticated() ) {
    header("Location: ./signin");
    exit();
}
if( $_SESSION['AUTH_ROLE_LEVEL'] < 100 ) {
    header("Location: ./error");
    exit();
}
```

### 8.2. 権限レベル定義

| 画面グループ | 必要な最小 role_level |
|:--|:--:|
| 公開ページ（閲覧のみ） | 0 |
| ログイン必須（プロフィール等） | 1 |
| 監督機能（試合入力） | 10 |
| 本部機能（大会・チーム・試合管理） | 100 |
| 管理者機能（ユーザー管理） | 1000 |

---

## 9. ログ設計

### 9.1. ログレベル定義

| $g_Log_Level | 出力対象 |
|:--:|:--|
| 0 | ログ出力なし |
| 1 | ERROR のみ |
| 2 | ERROR + WARNING |
| 4 | ERROR + WARNING + NOTICE（本番推奨） |
| 8 | 全て（ERROR + WARNING + NOTICE + DEBUG） |

### 9.2. ログファイル命名規則

```
Log/YYYYMMDD_baseball.log
```

- 日次でファイルが生成される
- `trigger_config_updated_at_after_update` などのDB更新も記録
- ログエントリ例：`[2026-06-21 10:00:00] NOTICE initProc() index.php : 初期処理`

---

## 10. セキュリティ設計

| 対策項目 | 実装方法 |
|:--|:--|
| SQLインジェクション対策 | PDO プリペアドステートメント（`:param` バインド） |
| XSS対策 | `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')` によるサニタイズ |
| パスワード管理 | `password_hash()` / `password_verify()` を使用 |
| メールアドレス検証 | `filter_var($email, FILTER_VALIDATE_EMAIL)` |
| メールアドレスサニタイズ | `filter_var($email, FILTER_SANITIZE_EMAIL)` |
| セッション固定攻撃対策 | サインイン成功後に `session_regenerate_id(true)` を使用（推奨） |
| CSRF対策 | フォームにトークン埋め込み（実装推奨） |
| ディレクトリトラバーサル | ファイルパスは `__DIR__` + `DIRECTORY_SEPARATOR` で絶対指定 |
| 認証チェック | 全保護ページで `isAuthenticated()` を `initProc()` で必ず確認 |

---

## 11. 環境設定設計

| 環境 | 判定条件 | SQLite DB ファイル名 | ログレベル |
|:--|:--|:--|:--:|
| 本番（production） | `SERVER_NAME == 'hpl.imawa.net'` | `baseball_production.db` | 4 |
| 開発（develop） | 上記以外 | `baseball_develop.db` | 4（8に変更可） |

両環境で共通の `config` テーブル構造を使用するが、接続先の業務DB・SMTP設定は各 `table_config_insertTable_*.sql` で異なる。

---

## 12. 変更履歴

| バージョン | 日付 | 変更者 | 変更内容 |
|:--|:--|:--|:--|
| 1.0 | 2026-06-21 | Imawa Office | 初版作成 |

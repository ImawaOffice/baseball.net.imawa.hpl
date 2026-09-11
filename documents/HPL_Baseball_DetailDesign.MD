<link rel="stylesheet" href="markdown-style.css">

# 野球大会管理システム 詳細設計書

<script>
	document.documentElement.style.setProperty( '--pageHeaderTitle', `"野球大会管理システム 詳細設計書"` );
	document.documentElement.style.setProperty( "--pageFooterDate", `"2026-06-21"` );
	document.documentElement.style.setProperty( "--pageFooterAuthor", `&copy; "Imawa Office"` );
</script>

## 目次 <!-- omit in toc -->

<div class="column2">

1. [1. 文書概要](#1-文書概要)
2. [2. 共通処理詳細設計](#2-共通処理詳細設計)
	1. [2.1. baseball\_function.php 関数仕様](#21-baseball_functionphp-関数仕様)
		1. [`checkRequest()`](#checkrequest)
		2. [`isValidDate()`](#isvaliddate)
		3. [`checkText()`](#checktext)
		4. [`checkNumeric()`](#checknumeric)
		5. [`isAuthenticated()`](#isauthenticated)
		6. [`checkAuthenticate()`](#checkauthenticate)
		7. [`checkUserCd()`](#checkusercd)
		8. [`checkPassword()`（baseball\_function.php 版）](#checkpasswordbaseball_functionphp-版)
		9. [`getClientIp()`](#getclientip)
		10. [`rateLimitOrFail()`](#ratelimitorfail)
	2. [2.2. baseball\_config.php 処理フロー](#22-baseball_configphp-処理フロー)
3. [3. 認証処理詳細設計](#3-認証処理詳細設計)
	1. [3.1. サインアップ処理（index.php / signup.php）](#31-サインアップ処理indexphp--signupphp)
		1. [STEP 0: メールアドレス入力](#step-0-メールアドレス入力)
		2. [STEP 1: 認証コード入力](#step-1-認証コード入力)
		3. [STEP 2: パスワード設定](#step-2-パスワード設定)
		4. [`setMailAddress()` UPSERT SQL](#setmailaddress-upsert-sql)
	2. [3.2. サインイン処理（signin.php）](#32-サインイン処理signinphp)
	3. [3.3. サインアウト処理（signout.php）](#33-サインアウト処理signoutphp)
	4. [3.4. パスワード変更処理（passwordchange.php）](#34-パスワード変更処理passwordchangephp)
4. [4. 大会管理詳細設計](#4-大会管理詳細設計)
	1. [4.1. 大会管理処理（tournamentmaintenance.php）](#41-大会管理処理tournamentmaintenancephp)
5. [5. チーム管理詳細設計](#5-チーム管理詳細設計)
	1. [5.1. チーム管理処理（teammaintenance.php）](#51-チーム管理処理teammaintenancephp)
6. [6. 試合管理詳細設計](#6-試合管理詳細設計)
	1. [6.1. 試合管理処理（gamemaintenance.php）](#61-試合管理処理gamemaintenancephp)
	2. [6.2. 試合スケジュール処理（gameschedule\*.php）](#62-試合スケジュール処理gameschedulephp)
7. [7. レート制限設計](#7-レート制限設計)
8. [8. SQLクエリ設計](#8-sqlクエリ設計)
	1. [8.1. ユーザー認証クエリ](#81-ユーザー認証クエリ)
	2. [8.2. サインアップ（UPSERT）クエリ](#82-サインアップupsertクエリ)
	3. [8.3. パスワード登録クエリ](#83-パスワード登録クエリ)
	4. [8.4. 認証コードチェッククエリ](#84-認証コードチェッククエリ)
	5. [8.5. サインイン記録更新クエリ](#85-サインイン記録更新クエリ)
	6. [8.6. メニュー取得クエリ](#86-メニュー取得クエリ)
9. [9. バリデーション関数詳細](#9-バリデーション関数詳細)
10. [10. エラーメッセージ一覧](#10-エラーメッセージ一覧)
11. [11. インストール手順](#11-インストール手順)
	 1. [11.1. 設定DB（SQLite）のセットアップ](#111-設定dbsqliteのセットアップ)
	 2. [11.2. 業務DB（MySQL）のセットアップ](#112-業務dbmysqlのセットアップ)
	 3. [11.3. ディレクトリ・ファイル権限](#113-ディレクトリファイル権限)
	 4. [11.4. 環境確認](#114-環境確認)
12. [12. 変更履歴](#12-変更履歴)

</div>

---

## 1. 文書概要

| 項目 | 内容 |
|:--|:--|
| 文書名 | 野球大会管理システム 詳細設計書 |
| システム名 | HPL 野球大会管理システム |
| 作成日 | 2026-06-21 |
| バージョン | 1.0 |
| 作成者 | Imawa Office |

本書は、野球大会管理システムの詳細設計（関数仕様・処理フロー・SQL設計）を定義する。  
内部設計書（`HPL_Baseball_InternalDesign.MD`）と合わせて参照すること。

---

## 2. 共通処理詳細設計

### 2.1. baseball_function.php 関数仕様

#### `checkRequest()`

```
関数名  : checkRequest
目的    : POST/GETリクエストのパラメータ存在・空文字チェック
引数    : $p_name        - チェック対象のリクエストキー名
         $p_isEmptyCheck - true: 空文字NG / false: 空文字OK (default: true)
         $p_request      - 'POST' または 'GET' (default: 'POST')
返り値  : bool (true: OK, false: NG)
```

**処理フロー：**

```
1. $p_request に応じて $_POST / $_GET を参照する
2. isset() でキーの存在確認 → 未設定なら false
3. $p_isEmptyCheck=true の場合、empty() で空文字確認 → 空なら false
4. true を返す
```

---

#### `isValidDate()`

```
関数名  : isValidDate
目的    : 日付文字列のフォーマット・有効性チェック
引数    : $p_date - チェック対象の日付文字列
返り値  : string|false - 正規化された 'Y-m-d' 形式の文字列、または false
```

**対応フォーマット：** `Y-m-d`、`Y/m/d`、`Ymd`

---

#### `checkText()`

```
関数名  : checkText
目的    : テキスト値のNULL・空文字チェック
引数    : $p_text        - チェック対象の文字列
         $p_checkEmpty  - true: 空文字NG (default: true)
返り値  : string|false - trim済み文字列、または false
```

---

#### `checkNumeric()`

```
関数名  : checkNumeric
目的    : 数値の妥当性・範囲チェック
引数    : $p_text        - チェック対象の値
         $p_min         - 最小値 (default: 0)
         $p_max         - 最大値 (default: PHP_INT_MAX)
         $p_checkEmpty  - true: 空文字NG (default: true)
返り値  : int|false - 整数値、または false
```

---

#### `isAuthenticated()`

```
関数名  : isAuthenticated
目的    : セッションによるログイン状態確認
引数    : なし
返り値  : bool
```

**判定条件：**  
`isset($_SESSION['AUTH_ROLE_LEVEL'])` かつ `!empty($_SESSION['AUTH_ROLE_LEVEL'])`

---

#### `checkAuthenticate()`

```
関数名  : checkAuthenticate
目的    : ユーザーコード・パスワードによる認証処理
引数    : $p_userCd    - ユーザーコード（メールアドレス）
         $p_password  - 平文パスワード
返り値  : bool
```

**処理フロー：**

```
1. baseball_user テーブルから user_cd でユーザーを取得
   （baseball_role と LEFT JOIN して role_name も取得）
2. is_enabled = 1 確認
3. password_verify($password, $hash) でパスワード照合
4. 成功時、以下のセッション変数にセット：
   AUTH_USER_ID, AUTH_USER_CD, AUTH_USER_NAME,
   AUTH_PWD_HASH, AUTH_EMAIL, AUTH_ROLE_LEVEL,
   AUTH_ROLE_NAME, AUTH_SIGNIN_AT, AUTH_SIGNIN_BY
5. baseball_user の signin_at / signin_by を CURRENT_TIMESTAMP で更新
6. 失敗時、認証セッション変数を全て null にクリア
```

---

#### `checkUserCd()`

```
関数名  : checkUserCd
目的    : ユーザーコード（メールアドレス）のNULL・空文字チェック
引数    : $p_userCd - チェック対象のユーザーコード
返り値  : bool
```

---

#### `checkPassword()`（baseball_function.php 版）

```
関数名  : checkPassword（baseball_function.php）
目的    : パスワードのNULL・空文字・長さチェック
引数    : $p_password - チェック対象のパスワード
返り値  : bool
制約    : 6文字以上
```

---

#### `getClientIp()`

```
関数名  : getClientIp
目的    : クライアントIPアドレスの取得
引数    : なし
返り値  : string (IPアドレス文字列)
```

**取得優先順：** `HTTP_X_REAL_IP` → `REMOTE_ADDR` → `'0.0.0.0'`

---

#### `rateLimitOrFail()`

```
関数名  : rateLimitOrFail
目的    : フォーム送信のレート制限（ブルートフォース対策）
引数    : $p_FormName - フォーム識別名
返り値  : bool (制限内なら true、超過なら false)
制約    : 60秒以内に5回まで
```

**実装方式：**  
- クライアントIPのMD5をファイル名とした JSON ファイルを `Log/` に保存
- ファイル内にタイムスタンプ配列を記録
- `window秒（60秒）`以内のエントリ数が `maxRequests（5）`を超えたら false

---

### 2.2. baseball_config.php 処理フロー

```
1. ログ設定変数 ($g_Log_Level, $g_Log_Path, $g_Log_Suffix) 定義
2. 環境判定 ($g_env = "develop" or "production")
3. Class_Log.php, Class_Database.php を require_once
4. セッション開始（未開始の場合のみ）
5. 設定DB (SQLite) に接続 → $g_DB_Config
6. config テーブルから is_enabled=1 の全設定取得 → $CONFIG 連想配列
7. 業務DB接続 ($g_DB) ← $CONFIG の DbType, DbHostName 等を使用
8. メニュー取得:
   getMenuId("HEADER") → $HEADER_MENU
   getMenuId("FOOTER") → $FOOTER_MENU
```

---

## 3. 認証処理詳細設計

### 3.1. サインアップ処理（index.php / signup.php）

サインアップ処理は `index.php` に実装されており、3ステップのウィザード形式になっている。

#### STEP 0: メールアドレス入力

**view_state = 0 の時に POST された場合**

```
[requestPOST()]
  1. $_POST['view_state'] → SESSION['view_state'] (数値化)
  2. $_POST['backto']     → SESSION['backto']
  3. $_POST['email']      → SESSION['email'] (FILTER_SANITIZE_EMAIL)
  4. $_POST['code']       → SESSION['code'] (htmlspecialchars)
  5. $_POST['password']   → SESSION['password'] (htmlspecialchars)
  6. view_state = view_state - backto (戻るボタン対応)

[checkProc()]
  - checkViewState() : view_state を 0〜2 の範囲に丸め込む
  - checkMailAddress() : filter_var(FILTER_VALIDATE_EMAIL)

[setProc()]
  - setVerificationCode()  : random_int(0, 999999) → 6桁ゼロ埋め文字列生成 → SESSION['VerificationCode']
  - setVerificationPeriod(): time() + 3600 → SESSION['verificationPeriod']
  - setMailAddress()       : baseball_user テーブルへ UPSERT
  - sendMail()             : PHPMailer で認証コードメール送信
  - setViewState(1)        : view_state を 1 に更新
```

#### STEP 1: 認証コード入力

**view_state = 1 の時に POST された場合**

```
[checkProc()]
  - checkVerificationCode() :
      baseball_user から email と verification_period >= (time()-3600) で検索
      DBの verification_cd と SESSION['code'] が一致するか確認

[setProc()]
  - setViewState(2) : view_state を 2 に更新
```

#### STEP 2: パスワード設定

**view_state = 2 の時に POST された場合**

```
[checkProc()]
  - checkPassword() : 6文字以上チェック

[setProc()]
  - setPassword() :
      password_hash($password, PASSWORD_DEFAULT) でハッシュ化
      baseball_user テーブルを UPDATE (password, is_enabled=1)
  - setViewState(0) : view_state を 0 に戻す
```

---

#### `setMailAddress()` UPSERT SQL

```sql
INSERT INTO baseball_user
( user_cd, password, username, email, role_id,
  verification_cd, verification_period, is_enabled,
  created_at, created_by, updated_at, updated_by )
VALUES
( :user_cd, :password, :username, :email, :role_id,
  :verification_cd, :verification_period, :is_enabled,
  CURRENT_TIMESTAMP, :created_by, CURRENT_TIMESTAMP, :updated_by )
ON DUPLICATE KEY UPDATE
  verification_cd     = :verification_cd
, verification_period = :verification_period
, is_enabled          = :is_enabled
, updated_at          = CURRENT_TIMESTAMP
, updated_by          = :updated_by
```

**バインドパラメータ：**

| パラメータ | 値 |
|:--|:--|
| `:user_cd` | `$_SESSION['email']` |
| `:password` | `""` (空文字、後でパスワード登録) |
| `:username` | `""` |
| `:email` | `$_SESSION['email']` |
| `:role_id` | `0` |
| `:verification_cd` | `$_SESSION['VerificationCode']` |
| `:verification_period` | `$_SESSION['verificationPeriod']` |
| `:is_enabled` | `0` |

---

### 3.2. サインイン処理（signin.php）

```
[initProc()]
  1. isAuthenticated() チェック → ログイン済みならトップへリダイレクト
  2. SESSION 変数初期化 (error_msg='', view_state=0, send_button='サインイン')

[requestProc()]
  1. GET リクエストなら処理スキップ（画面表示のみ）
  2. POST リクエストのみ以降の処理実行

[checkProc()]
  1. checkUserCd($_POST['email'])    : NULL・空文字チェック
  2. checkPassword($_POST['password']): NULL・空文字・6文字以上チェック

[setProc()]
  1. checkAuthenticate($userCd, $password):
     a. baseball_user + baseball_role JOIN で SELECT
     b. password_verify() でハッシュ照合
     c. 成功時 AUTH_* セッション変数にセット
     d. signin_at / signin_by を UPDATE
  2. 成功時 → トップページへリダイレクト
```

---

### 3.3. サインアウト処理（signout.php）

```
[initProc()]
  1. isAuthenticated() チェック → 未ログインならトップへリダイレクト

[setProc()]
  1. session_unset()    : 全セッション変数を削除
  2. session_destroy()  : セッションを破棄
  3. トップページへリダイレクト
```

---

### 3.4. パスワード変更処理（passwordchange.php）

```
[initProc()]
  1. isAuthenticated() チェック → 未認証なら signin へリダイレクト

[checkProc()]
  1. 現在パスワード : password_verify() で照合
  2. 新パスワード   : 6文字以上チェック
  3. 確認パスワード : 新パスワードと一致チェック

[setProc()]
  1. password_hash($newPassword, PASSWORD_DEFAULT)
  2. baseball_user テーブルの password カラムを UPDATE
```

---

## 4. 大会管理詳細設計

### 4.1. 大会管理処理（tournamentmaintenance.php）

**処理モード（FROM_VIEW_STATE）**

| FROM_VIEW_STATE | 説明 |
|:--:|:--|
| 0 | 一覧表示 |
| 1 | 追加フォーム表示 |
| 2 | 編集フォーム表示 |
| 3 | 削除確認 |

**セッション変数（FORM_*）**

| 変数名 | 型 | 初期値 | 説明 |
|:--|:--|:--|:--|
| FROM_VIEW_STATE | int | 0 | 表示ステータス |
| FORM_ERR_MSG | string | `''` | エラーメッセージ |
| FORM_PAGE_ROWS | int | 10 | 1ページの表示行数 |
| FORM_PAGE_CURRENT | int | 1 | 現在のページ番号 |
| FORM_PAGE_OFFSET | int | 0 | SELECT OFFSET 値 |
| FORM_PAGE_TOTAL | int | 0 | 総ページ数 |
| FORM_PAGE_TOP | int | 1 | 最初のページ番号 |
| FORM_PAGE_LAST | int | 1 | 最後のページ番号 |
| FORM_PAGE_PREVIOUS | int | 1 | 前ページ番号 |
| FORM_PAGE_NEXT | int | 1 | 次ページ番号 |
| RES_ADD_BUTTON | string | `'追加'` | 追加ボタンラベル |

**一覧表示クエリ（概要）**

```sql
SELECT tournament_id, tournament_title, tournament_text,
       tournament_start_date, tournament_end_date, is_enabled
  FROM baseball_tournament
 ORDER BY tournament_start_date DESC
 LIMIT :limit OFFSET :offset
```

**入力バリデーション**

| フィールド | チェック内容 |
|:--|:--|
| tournament_title | 必須・テキスト |
| tournament_text | 必須・テキスト |
| tournament_start_date | 必須・日付形式（Y-m-d / Y/m/d / Ymd） |
| tournament_end_date | 必須・日付形式・start_date 以降 |
| tournament1_teams | 任意・数値・0以上 |
| tournament2_teams | 任意・数値・0以上 |
| tournament3_teams | 任意・数値・0以上 |

---

## 5. チーム管理詳細設計

### 5.1. チーム管理処理（teammaintenance.php）

**処理モード：** 大会管理と同一の FROM_VIEW_STATE パターン（0:一覧、1:追加、2:編集、3:削除）

**一覧表示クエリ（概要）**

```sql
SELECT t.team_id, t.team_name, t.team_manager,
       t.team_contact, t.team_access_cd,
       t.team_tel, t.team_email,
       t.tournament_id, t.is_enabled,
       tr.tournament_title
  FROM baseball_team t
  LEFT JOIN baseball_tournament tr ON t.tournament_id = tr.tournament_id
 ORDER BY t.tournament_id ASC, t.team_id ASC
 LIMIT :limit OFFSET :offset
```

**入力バリデーション**

| フィールド | チェック内容 |
|:--|:--|
| team_name | 必須・テキスト |
| team_manager | 必須・テキスト |
| team_contact | 任意・テキスト |
| team_access_cd | 必須・テキスト（チーム固有コード） |
| team_tel | 任意・テキスト |
| team_email | 任意・メール形式 |
| tournament_id | 必須・数値・1以上 |

---

## 6. 試合管理詳細設計

### 6.1. 試合管理処理（gamemaintenance.php）

**処理モード：** FROM_VIEW_STATE パターン（0:一覧、1:追加、2:編集、3:削除）

**一覧表示クエリ（概要）**

```sql
SELECT g.game_id, g.tournament_id, g.game_class,
       g.game_block, g.game_count, g.game_name,
       g.game_date, g.game_place,
       g.team1_id, g.team1_name, g.team1_score,
       g.team2_id, g.team2_name, g.team2_score,
       g.winner_id, g.loser_id, g.is_enabled
  FROM baseball_game g
 WHERE g.tournament_id = :tournament_id
 ORDER BY g.game_class ASC, g.game_block ASC,
          g.game_count ASC
 LIMIT :limit OFFSET :offset
```

**入力バリデーション**

| フィールド | チェック内容 |
|:--|:--|
| tournament_id | 必須・数値・1以上 |
| game_class | 必須・数値・0以上 |
| game_block | 必須・数値・1以上 |
| game_count | 必須・数値・1以上 |
| game_name | 必須・テキスト |
| game_date | 必須・日時形式 |
| game_place | 必須・テキスト |
| team1_id | 必須・数値・1以上 |
| team2_id | 必須・数値・1以上（team1_id と異なる値） |
| team1_score | 任意・数値・0以上 |
| team2_score | 任意・数値・0以上 |

---

### 6.2. 試合スケジュール処理（gameschedule*.php）

| ファイル | 対象トーナメント種別 | game_class 値 |
|:--|:--|:--:|
| gameschedule1.php | 総当たり戦 | 0 |
| gameschedule2.php | ベスト8（勝者トーナメント） | 1 |
| gameschedule3.php | 裏トーナメント（敗者復活戦） | 2 |
| gameschedule4.php | 決勝戦 | 3 |

**試合連鎖設計（トーナメント進行）**

- `team1_last_game_id` / `team2_last_game_id` で前試合との連鎖を管理
- 前試合の `winner_id` を次試合の `team1_id` / `team2_id` に自動設定
- 1回戦敗退チームは自動で裏トーナメント（game_class=2）に転記

---

## 7. レート制限設計

`rateLimitOrFail($p_FormName)` 関数の詳細仕様

```
制限値:
  maxRequests  = 5
  windowSeconds = 60

実装:
  1. getClientIp() でクライアントIPを取得
  2. Log/{FormName}_form_{md5(IP)}.json をアクセス記録ファイルとして使用
  3. ファイルが存在すれば読み込み、なければ空配列
  4. 現在時刻 - windowSeconds 以内のタイムスタンプのみフィルタリング
  5. 件数 >= maxRequests なら false（HTTP 429 相当）を返す
  6. 現在タイムスタンプを追加してファイルに書き戻す
  7. true を返す

適用箇所:
  - サインアップフォーム（index.php）
  - サインインフォーム（signin.php）
  - お問い合わせフォーム（contact.php）
```

---

## 8. SQLクエリ設計

### 8.1. ユーザー認証クエリ

```sql
SELECT u.user_id, u.user_cd, u.password,
       u.username, u.email,
       u.role_level, u.signin_at, u.signin_by, u.is_enabled,
       IFNULL( r.role_name, '不正な権限' ) AS role_name
  FROM baseball_user u
  LEFT JOIN baseball_role r ON u.role_level = r.role_level
 WHERE u.is_enabled = 1
   AND u.user_cd = :user_cd
```

---

### 8.2. サインアップ（UPSERT）クエリ

```sql
INSERT INTO baseball_user
( user_cd, password, username, email, role_id,
  verification_cd, verification_period, is_enabled,
  created_at, created_by, updated_at, updated_by )
VALUES
( :user_cd, :password, :username, :email, :role_id,
  :verification_cd, :verification_period, :is_enabled,
  CURRENT_TIMESTAMP, :created_by, CURRENT_TIMESTAMP, :updated_by )
ON DUPLICATE KEY UPDATE
  verification_cd     = :verification_cd
, verification_period = :verification_period
, is_enabled          = :is_enabled
, updated_at          = CURRENT_TIMESTAMP
, updated_by          = :updated_by
```

---

### 8.3. パスワード登録クエリ

```sql
UPDATE baseball_user
   SET password   = :password
     , is_enabled = 1
     , updated_at = CURRENT_TIMESTAMP
     , updated_by = :updated_by
 WHERE user_cd = :user_cd
```

---

### 8.4. 認証コードチェッククエリ

```sql
SELECT verification_cd
  FROM baseball_user
 WHERE email             = :email
   AND verification_period >= :period
```

`period` = `time() - 3600`（現在時刻から60分前のUNIXタイムスタンプ）

---

### 8.5. サインイン記録更新クエリ

```sql
UPDATE baseball_user
   SET signin_at = CURRENT_TIMESTAMP
     , signin_by = :signin_by
 WHERE is_enabled = 1
   AND user_id    = :user_id
```

---

### 8.6. メニュー取得クエリ

```sql
-- ルートメニューID取得
SELECT menu_id
  FROM menu
 WHERE is_enabled = 1
   AND parent_id  = 0
   AND role_min  <= :role
   AND role_max  >= :role
   AND menu_name  = :menu_name
 ORDER BY display_order ASC

-- サブメニュー取得
SELECT 'menu' || menu_id AS menu_cd,
       menu_id, menu_name, menu_url, parent_id, display_order
  FROM menu
 WHERE is_enabled = 1
   AND parent_id  = :parent_id
   AND role_min  <= :role
   AND role_max  >= :role
 ORDER BY display_order ASC, parent_id, menu_id
```

---

## 9. バリデーション関数詳細

全バリデーション関数の仕様まとめ

| 関数名 | ファイル | 対象 | エラーメッセージ例 |
|:--|:--|:--|:--|
| `checkRequest()` | baseball_function.php | リクエストパラメータ存在確認 | （ログのみ） |
| `isValidDate()` | baseball_function.php | 日付形式・有効性 | （bool返却） |
| `checkText()` | baseball_function.php | テキストNULL・空文字 | （bool返却） |
| `checkNumeric()` | baseball_function.php | 数値・範囲 | （bool返却） |
| `checkUserCd()` | baseball_function.php | ユーザーコード必須 | 'ユーザコードを入力して下さい。' |
| `checkPassword()` | baseball_function.php | パスワード必須・6文字以上 | 'パスワードは6文字以上で入力して下さい。' |
| `checkMailAddress()` | index.php | メール形式チェック | 'メールアドレスの形式が正しくありません' |
| `checkVerificationCode()` | index.php | 認証コードDB照合・期限 | '認証コードが正しくありません。' |
| `checkPassword()` | index.php | パスワード6文字以上 | 'パスワードが正しくありません。' |
| `checkViewState()` | index.php | view_state 0〜2 範囲丸め込み | （自動補正） |

---

## 10. エラーメッセージ一覧

| 発生箇所 | エラーメッセージ |
|:--|:--|
| サインアップ：メールアドレス未入力 | 'メールアドレスを入力して下さい。' |
| サインアップ：メールアドレス形式不正 | 'メールアドレスの形式が正しくありません' |
| サインアップ：認証コード不正 | '認証コードが正しくありません。' |
| サインアップ：未登録メールアドレス | '登録されていないメールアドレスです。' |
| サインアップ：認証コード送信完了 | '登録されたメールアドレスに認証コードを送付しました。' |
| サインアップ：パスワード不正 | 'パスワードが正しくありません。' |
| サインアップ：メール送信失敗 | '認証コードの送信に失敗しました。' |
| サインアップ：内部エラー | '内部エラー：認証コードまたは有効期限が未設定です。' |
| サインアップ：DB登録エラー | 'メールアドレス登録処理でエラーが発生しました: {message}' |
| サインアップ：パスワードDB登録エラー | 'パスワードの登録処理でエラーが発生しました: {message}' |
| サインイン：ユーザー未入力 | 'ユーザコードを入力して下さい。' |
| サインイン：パスワード未入力 | 'パスワードを入力して下さい。' |
| サインイン：パスワード短い | 'パスワードは6文字以上で入力して下さい。' |
| サインイン：認証失敗 | 'ユーザコードまたはパスワードが正しくありません。' |
| サインイン：DBエラー | 'データベースエラーが発生しました。' |
| レート制限超過 | （HTTP 429、フォーム再表示） |

---

## 11. インストール手順

### 11.1. 設定DB（SQLite）のセットアップ

```bash
# install/ ディレクトリの SQL を実行する（例：phpMyAdmin等での実行）
# SQLite 設定DB
1. table_config_createTable.sql       → config テーブル作成
2. table_config_insertTable_develop.sql  → 開発環境設定の投入
   または
   table_config_insertTable_production.sql → 本番環境設定の投入
3. table_menu_createTable.sql         → menu テーブル作成
4. table_menu_insertTable_develop.sql → メニューデータ投入
   または
   table_menu_insertTable_production.sql
```

### 11.2. 業務DB（MySQL）のセットアップ

```sql
-- 以下の順で実行する
1. table_baseball_role_createTable.sql
2. table_baseball_role_insertTable.sql
3. table_baseball_user_createTable.sql
4. table_baseball_tournament_createTable.sql
5. table_baseball_team_CreateTable.sql
6. table_baseball_game_CreateTable.sql
7. table_baseball_information_createTable.sql
8. table_baseball_inquiry_CreateTable.sql
9. table_baseball_attachment_CreateTable.sql

-- 必要に応じてAlterTableも実行
10. table_baseball_user_AlterTable.sql
11. table_baseball_tournament_AlterTable.sql
12. table_baseball_team_AlterTable.sql
13. table_baseball_game_AlterTable.sql
14. table_baseball_attachment_AlterTable.sql
```

### 11.3. ディレクトリ・ファイル権限

| パス | 必要な権限 |
|:--|:--|
| `Log/` | Web サーバーから書き込み可（0777） |
| `attachment/` | Web サーバーから書き込み可（0777） |
| `baseball_develop.db` / `baseball_production.db` | Web サーバーから読み書き可 |

### 11.4. 環境確認

```
- PHP 8.x
- PDO 拡張（pdo_sqlite, pdo_mysql）が有効
- PHPMailer ライブラリが PHPMailer/ ディレクトリに配置済み
- SMTP サーバーへの接続が可能
```

---

## 12. 変更履歴

| バージョン | 日付 | 変更者 | 変更内容 |
|:--|:--|:--|:--|
| 1.0 | 2026-06-21 | Imawa Office | 初版作成 |

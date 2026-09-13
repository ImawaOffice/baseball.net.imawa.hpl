<link rel="stylesheet" href="markdown-style.css">

# 野球大会管理システム 外部設計書

<script>
	document.documentElement.style.setProperty( '--pageHeaderTitle', `"野球大会管理システム 外部設計書"` );
	document.documentElement.style.setProperty( "--pageFooterDate", `"2026-06-21"` );
	document.documentElement.style.setProperty( "--pageFooterAuthor", `&copy; "Imawa Office"` );
</script>

## 目次 <!-- omit in toc -->

<div class="column2">

1. [1. 文書概要](#1-文書概要)
2. [2. システム概要](#2-システム概要)
	1. [2.1. システム構成図](#21-システム構成図)
	2. [2.2. 利用環境](#22-利用環境)
3. [3. 利用者区分・権限一覧](#3-利用者区分権限一覧)
4. [4. 画面一覧](#4-画面一覧)
5. [5. 画面遷移図](#5-画面遷移図)
6. [6. 各画面仕様](#6-各画面仕様)
	1. [6.1. トップページ（index）](#61-トップページindex)
	2. [6.2. サインアップ（signup）](#62-サインアップsignup)
	3. [6.3. サインイン（signin）](#63-サインインsignin)
	4. [6.4. サインアウト（signout）](#64-サインアウトsignout)
	5. [6.5. パスワード変更（passwordchange）](#65-パスワード変更passwordchange)
	6. [6.6. プロフィール（profile）](#66-プロフィールprofile)
	7. [6.7. お問い合わせ（contact）](#67-お問い合わせcontact)
	8. [6.8. お問い合わせ一覧（contactlist）](#68-お問い合わせ一覧contactlist)
	9. [6.9. 大会概要（tournamentoverview）](#69-大会概要tournamentoverview)
	10. [6.10. 大会結果（tournamentresults）](#610-大会結果tournamentresults)
	11. [6.11. 大会規定（tournamentrules）](#611-大会規定tournamentrules)
	12. [6.12. 試合規定（gamerules）](#612-試合規定gamerules)
	13. [6.13. 試合スケジュール（gameschedule）](#613-試合スケジュールgameschedule)
	14. [6.14. 試合対戦（gamematch）](#614-試合対戦gamematch)
	15. [6.15. 大会管理（tournamentmaintenance）](#615-大会管理tournamentmaintenance)
	16. [6.16. チーム管理（teammaintenance）](#616-チーム管理teammaintenance)
	17. [6.17. 試合管理（gamemaintenance）](#617-試合管理gamemaintenance)
	18. [6.18. ルール管理（rulemaintenance）](#618-ルール管理rulemaintenance)
	19. [6.19. ユーザー一覧（userlist）](#619-ユーザー一覧userlist)
	20. [6.20. チームエントリー（teamentry）](#620-チームエントリーteamentry)
7. [7. 共通UI仕様](#7-共通ui仕様)
	1. [7.1. ページヘッダー](#71-ページヘッダー)
	2. [7.2. ページフッター](#72-ページフッター)
	3. [7.3. ナビゲーションメニュー](#73-ナビゲーションメニュー)
8. [8. 入出力インターフェース](#8-入出力インターフェース)
	1. [8.1. メール送信仕様](#81-メール送信仕様)
	2. [8.2. ファイル添付仕様](#82-ファイル添付仕様)
9. [9. エラー処理仕様](#9-エラー処理仕様)
10. [10. 変更履歴](#10-変更履歴)

</div>

---

## 1. 文書概要

| 項目 | 内容 |
|:--|:--|
| 文書名 | 野球大会管理システム 外部設計書 |
| システム名 | HPL 野球大会管理システム |
| 対象 | HPL 野球大会管理システム 運営・管理Webシステム |
| 作成日 | 2026-06-21 |
| バージョン | 1.0 |
| 作成者 | Imawa Office |

本書は、野球大会管理システムの外部設計（画面設計・インターフェース設計）を定義する。  
要件定義書（`HPL_Baseball_RequirementsDefinition.MD`）に基づき、利用者視点での画面・操作・入出力インターフェースを規定する。

> 本書は現行ソースコードと DB 定義を正本として扱い、画面名・権限・導線・入出力仕様を利用者視点で整理した内容である。現行実装に存在しない操作や将来拡張の案は、将来拡張または参考情報として分離して記載する。

---

## 2. システム概要

### 2.1. システム構成図

```
[ユーザーブラウザ]
        │ HTTPS
        ▼
[Webサーバー (Apache/XAMPP)]
        │
   ┌────┴────────────────────────┐
   │   PHP 8.x アプリケーション   │
   │  ┌──────────────────────┐   │
   │  │   baseball_config.php│   │  ← 設定・DB接続・メニュー管理
   │  │   baseball_function.php  │  ← 共通関数群
   │  │   Class_Database.php │   │  ← DB抽象化クラス
   │  │   Class_Log.php      │   │  ← ログクラス
   │  │   Class_PHPMailer.php│   │  ← メール送信クラス
   │  └──────────────────────┘   │
   └─────────────┬───────────────┘
                 │ PDO
        ┌────────┴────────┐
        │                 │
   [SQLite DB]       [MySQL DB]
   (設定DB/開発)     (本番環境)
```

### 2.2. 利用環境

| 項目 | 内容 |
|:--|:--|
| サーバーサイド | PHP 8.x |
| データベース（開発） | SQLite |
| データベース（本番） | MySQL |
| フロントエンド | HTML5, CSS3, JavaScript (jQuery) |
| CSSフレームワーク | Bootstrap（使用予定） |
| メール | PHPMailer |
| 対応ブラウザ | Chrome, Firefox, Edge, Safari（最新版） |
| ホスト（本番） | hpl.imawa.net |

---

## 3. 利用者区分・権限一覧

| 権限名 | role_level | 概要 |
|:--|:--:|:--|
| 一般 | 0 | 未ログインユーザー。公開情報の閲覧のみ可能 |
| 登録済み | 1 | メールアドレス登録済みだがパスワード未設定 |
| 監督 | 10 | 各チームの責任者。自チームの試合入力が可能 |
| 本部 | 100 | 大会運営管理者。全機能（大会/チーム/試合管理）が利用可能 |
| システム管理者 | 1000 | システム全体の管理。ユーザー管理含む全機能が利用可能 |

---

## 4. 画面一覧

| 画面ID | 画面名 | ファイル名 | 対象権限 | 概要 |
|:--|:--|:--|:--|:--|
| SCR-001 | トップページ | index.php | 全員 | サインアップ処理・サイトトップ表示 |
| SCR-002 | サインアップ | signup.php | 一般 | メールアドレス登録・認証コード入力・パスワード設定 |
| SCR-003 | サインイン | signin.php | 一般 | メールアドレス・パスワードによる認証 |
| SCR-004 | サインアウト | signout.php | 登録済以上 | セッション破棄・ログアウト |
| SCR-005 | パスワード変更 | passwordchange.php | 登録済以上 | パスワードの変更 |
| SCR-006 | プロフィール | profile.php | 登録済以上 | ユーザー情報の確認・編集 |
| SCR-007 | お問い合わせ | contact.php | 全員 | 問い合わせフォーム |
| SCR-008 | お問い合わせ一覧 | contactlist.php | 本部以上 | 問い合わせ一覧の確認 |
| SCR-009 | 大会概要 | tournamentoverview.php | 全員 | 大会の概要・スケジュール表示 |
| SCR-010 | 大会結果 | tournamentresults.php | 全員 | トーナメント表・試合結果表示 |
| SCR-011 | 大会規定 | tournamentrules.php | 全員 | 大会規定の表示 |
| SCR-012 | 試合規定 | gamerules.php | 全員 | 試合規定の表示 |
| SCR-013 | 試合スケジュール | gameschedule.php | 全員 | 試合日程一覧の表示 |
| SCR-014 | 試合対戦 | gamematch.php | 監督以上 | 試合対戦の入力・結果確認 |
| SCR-015 | 大会管理 | tournamentmaintenance.php | 本部以上 | 大会情報のCRUD |
| SCR-016 | チーム管理 | teammaintenance.php | 本部以上 | チーム情報のCRUD |
| SCR-017 | 試合管理 | gamemaintenance.php | 本部以上 | 試合情報のCRUD |
| SCR-018 | ルール管理 | rulemaintenance.php | 本部以上 | 大会規定・試合規定のCRUD |
| SCR-019 | ユーザー一覧 | userlist.php | システム管理者 | ユーザー情報の一覧・管理 |
| SCR-020 | チームエントリー | teamentry.php | 監督以上 | チームの大会エントリー |

---

## 5. 画面遷移図

```
[トップページ (index)]
    │
    ├─(未ログイン)──────────→ [サインアップ (signup)]
    │                              ├─ STEP1: メールアドレス入力
    │                              │    └─ 認証コード送信メール
    │                              ├─ STEP2: 認証コード入力
    │                              └─ STEP3: パスワード設定
    │                                   └─ 完了→ [サインイン]
    │
    ├─(未ログイン)──────────→ [サインイン (signin)]
    │                              └─ 認証成功 → リダイレクト
    │
    ├─(ログイン済み)─────────→ [サインアウト (signout)]
    │
    ├─(全員)────────────────→ [お問い合わせ (contact)]
    │
    ├─(全員)────────────────→ [大会概要 (tournamentoverview)]
    ├─(全員)────────────────→ [大会結果 (tournamentresults)]
    ├─(全員)────────────────→ [大会規定 (tournamentrules)]
    ├─(全員)────────────────→ [試合規定 (gamerules)]
    ├─(全員)────────────────→ [試合スケジュール (gameschedule)]
    │
    ├─(登録済み以上)─────────→ [プロフィール (profile)]
    ├─(登録済み以上)─────────→ [パスワード変更 (passwordchange)]
    ├─(監督以上)─────────────→ [試合対戦 (gamematch)]
    ├─(監督以上)─────────────→ [チームエントリー (teamentry)]
    │
    ├─(本部以上)─────────────→ [大会管理 (tournamentmaintenance)]
    ├─(本部以上)─────────────→ [チーム管理 (teammaintenance)]
    ├─(本部以上)─────────────→ [試合管理 (gamemaintenance)]
    ├─(本部以上)─────────────→ [ルール管理 (rulemaintenance)]
    └─(本部以上)─────────────→ [お問い合わせ一覧 (contactlist)]
         │
         └─(システム管理者)────→ [ユーザー一覧 (userlist)]
```

---

## 6. 各画面仕様

### 6.1. トップページ（index）

| 項目 | 内容 |
|:--|:--|
| ファイル | index.php |
| URL | / または /index |
| 対象権限 | 全員 |
| 概要 | サイトのメインページ。各コンテンツを include で構成する |

**表示コンテンツ（includeファイル）**

| 順序 | コンテンツ名 | ファイル |
|:--:|:--|:--|
| 1 | お知らせ | article_information.php |
| 2 | 大会規定 | article_tournament-rules.php |
| 3 | 試合規定 | article_game-rules.php |
| 4 | スポンサー | article_sponsor.php |
| 5 | ソーシャルメディア | article_sns.php |

**サインアップ処理（POSTリクエスト）**

| 表示ステータス | 処理内容 | 次ステータス |
|:--:|:--|:--:|
| 0 | メールアドレス入力・認証コード送信 | 1 |
| 1 | 認証コード入力・確認 | 2 |
| 2 | パスワード入力・登録完了 | 0 |

---

### 6.2. サインアップ（signup）

| 項目 | 内容 |
|:--|:--|
| ファイル | signup.php |
| URL | /signup |
| 対象権限 | 一般（未ログイン） |

**入力フィールド**

| フィールド名 | 種類 | 必須 | バリデーション |
|:--|:--|:--:|:--|
| email | メールアドレス | ○ | メール形式チェック・FILTER_SANITIZE_EMAIL |
| code | 認証コード（6桁） | ○ | DB照合・有効期限60分 |
| password | パスワード | ○ | 6文字以上 |

**処理フロー**

```
STEP1: メールアドレス入力
  → 6桁認証コード生成（random_int）
  → baseball_user テーブルに UPSERT
  → メール送信（PHPMailer）
  → view_state = 1 に遷移

STEP2: 認証コード入力
  → DBの verification_cd と照合
  → 有効期限（60分）チェック
  → view_state = 2 に遷移

STEP3: パスワード設定
  → password_hash() でハッシュ化して保存
  → is_enabled = 1 に更新
  → view_state = 0 に戻る
```

---

### 6.3. サインイン（signin）

| 項目 | 内容 |
|:--|:--|
| ファイル | signin.php |
| URL | /signin |
| 対象権限 | 一般（未ログイン） |

**入力フィールド**

| フィールド名 | 種類 | 必須 | バリデーション |
|:--|:--|:--:|:--|
| email | メールアドレス | ○ | メール形式チェック |
| password | パスワード | ○ | 6文字以上 |

**処理フロー**

```
1. メールアドレスでDBからユーザー取得
2. is_enabled = 1 確認
3. password_verify() でパスワード照合
4. SESSION に認証情報（AUTH_USER_ID, AUTH_ROLE_LEVEL等）設定
5. トップページへリダイレクト
```

---

### 6.4. サインアウト（signout）

| 項目 | 内容 |
|:--|:--|
| ファイル | signout.php |
| URL | /signout |
| 対象権限 | 登録済み以上 |

```
session_unset() → session_destroy() → トップページへリダイレクト
```

---

### 6.5. パスワード変更（passwordchange）

| 項目 | 内容 |
|:--|:--|
| ファイル | passwordchange.php |
| URL | /passwordchange |
| 対象権限 | 登録済み以上（ログイン必須） |

**入力フィールド**

| フィールド名 | 種類 | 必須 | バリデーション |
|:--|:--|:--:|:--|
| current_password | 現在のパスワード | ○ | password_verify() で照合 |
| new_password | 新しいパスワード | ○ | 6文字以上 |
| confirm_password | 確認用パスワード | ○ | new_passwordと一致 |

---

### 6.6. プロフィール（profile）

| 項目 | 内容 |
|:--|:--|
| ファイル | profile.php |
| URL | /profile |
| 対象権限 | 登録済み以上（ログイン必須） |

**表示・編集項目**

| フィールド名 | 概要 |
|:--|:--|
| username | ユーザー名 |
| email | メールアドレス（変更不可） |
| role_level | 権限（表示のみ） |

---

### 6.7. お問い合わせ（contact）

| 項目 | 内容 |
|:--|:--|
| ファイル | contact.php |
| URL | /contact |
| 対象権限 | 全員 |

**入力フィールド**

| フィールド名 | 種類 | 必須 | バリデーション |
|:--|:--|:--:|:--|
| name | お名前 | ○ | テキスト・XSSサニタイズ |
| phone | 電話番号 | - | テキスト |
| email | メールアドレス | - | メール形式 |
| message | メッセージ | ○ | テキスト・XSSサニタイズ |

送信後、`baseball_inquiry` テーブルに登録し、管理者へメール通知。

---

### 6.8. お問い合わせ一覧（contactlist）

| 項目 | 内容 |
|:--|:--|
| ファイル | contactlist.php |
| URL | /contactlist |
| 対象権限 | 本部以上（ログイン必須） |

`baseball_inquiry` テーブルの一覧をページング表示。

---

### 6.9. 大会概要（tournamentoverview）

| 項目 | 内容 |
|:--|:--|
| ファイル | tournamentoverview.php |
| URL | /tournamentoverview |
| 対象権限 | 全員 |

`baseball_tournament` テーブルから大会情報を取得・表示。

---

### 6.10. 大会結果（tournamentresults）

| 項目 | 内容 |
|:--|:--|
| ファイル | tournamentresults.php |
| URL | /tournamentresults |
| 対象権限 | 全員 |

`baseball_game` テーブルからトーナメント結果を取得し、試合結果を表示。

---

### 6.11. 大会規定（tournamentrules）

| 項目 | 内容 |
|:--|:--|
| ファイル | tournamentrules.php |
| URL | /tournamentrules |
| 対象権限 | 全員 |

大会規定をルールテーブルから取得・表示。

---

### 6.12. 試合規定（gamerules）

| 項目 | 内容 |
|:--|:--|
| ファイル | gamerules.php |
| URL | /gamerules |
| 対象権限 | 全員 |

試合規定をルールテーブルから取得・表示。

---

### 6.13. 試合スケジュール（gameschedule）

| 項目 | 内容 |
|:--|:--|
| ファイル | gameschedule.php（gameschedule1〜4.php） |
| URL | /gameschedule |
| 対象権限 | 全員 |

`baseball_game` テーブルから試合日程を取得・表示。トーナメント種別ごとにサブページ（1〜4）が存在。

---

### 6.14. 試合対戦（gamematch）

| 項目 | 内容 |
|:--|:--|
| ファイル | gamematch.php |
| URL | /gamematch |
| 対象権限 | 監督以上（ログイン必須） |

試合の対戦カード確認・スコア入力。

---

### 6.15. 大会管理（tournamentmaintenance）

| 項目 | 内容 |
|:--|:--|
| ファイル | tournamentmaintenance.php |
| URL | /tournamentmaintenance |
| 対象権限 | 本部以上（ログイン必須） |

**機能一覧**

| 機能 | 説明 |
|:--|:--|
| 一覧表示 | 大会一覧をページング表示 |
| 追加 | 新規大会の登録 |
| 編集 | 大会情報の更新 |
| 削除 | 論理削除（is_enabled = 0） |

**入力フィールド**

| フィールド名 | 型 | 必須 | 説明 |
|:--|:--|:--:|:--|
| tournament_title | テキスト | ○ | 大会名称 |
| tournament_text | テキスト | ○ | 大会説明 |
| tournament_start_date | 日付 | ○ | 開始日 |
| tournament_end_date | 日付 | ○ | 終了日 |
| tournament1_teams | 数値 | - | トーナメント1チーム数 |
| tournament2_teams | 数値 | - | トーナメント2チーム数 |
| tournament3_teams | 数値 | - | トーナメント3チーム数 |
| is_enabled | 数値 | ○ | 有効フラグ |

---

### 6.16. チーム管理（teammaintenance）

| 項目 | 内容 |
|:--|:--|
| ファイル | teammaintenance.php |
| URL | /teammaintenance |
| 対象権限 | 本部以上（ログイン必須） |

**入力フィールド**

| フィールド名 | 型 | 必須 | 説明 |
|:--|:--|:--:|:--|
| team_name | テキスト | ○ | チーム名 |
| team_manager | テキスト | ○ | 監督名 |
| team_contact | テキスト | - | 担当者名 |
| team_access_cd | テキスト | ○ | アクセスコード（チーム固有） |
| team_tel | テキスト | - | 電話番号 |
| team_email | テキスト | - | メールアドレス |
| tournament_id | 数値 | ○ | 所属大会ID |

---

### 6.17. 試合管理（gamemaintenance）

| 項目 | 内容 |
|:--|:--|
| ファイル | gamemaintenance.php |
| URL | /gamemaintenance |
| 対象権限 | 本部以上（ログイン必須） |

**入力フィールド**

| フィールド名 | 型 | 必須 | 説明 |
|:--|:--|:--:|:--|
| tournament_id | 数値 | ○ | 大会ID |
| game_class | 数値 | ○ | 試合区分（表/裏トーナメント等） |
| game_block | 数値 | ○ | ブロック番号 |
| game_count | 数値 | ○ | 試合番号 |
| game_name | テキスト | ○ | 試合名称 |
| game_date | 日時 | ○ | 試合日時 |
| game_place | テキスト | ○ | 試合場所 |
| team1_id | 数値 | ○ | チーム1 ID |
| team2_id | 数値 | ○ | チーム2 ID |
| team1_score | 数値 | - | チーム1得点 |
| team2_score | 数値 | - | チーム2得点 |

---

### 6.18. ルール管理（rulemaintenance）

| 項目 | 内容 |
|:--|:--|
| ファイル | rulemaintenance.php |
| URL | /rulemaintenance |
| 対象権限 | 本部以上（ログイン必須） |

大会規定・試合規定のテキスト管理（登録・編集・削除）。

---

### 6.19. ユーザー一覧（userlist）

| 項目 | 内容 |
|:--|:--|
| ファイル | userlist.php |
| URL | /userlist |
| 対象権限 | システム管理者 |

`baseball_user` テーブルの一覧表示・権限変更・有効/無効切り替え。

---

### 6.20. チームエントリー（teamentry）

| 項目 | 内容 |
|:--|:--|
| ファイル | teamentry.php |
| URL | /teamentry |
| 対象権限 | 監督以上（ログイン必須） |

チームの大会エントリー登録・確認。

---

## 7. 共通UI仕様

### 7.1. ページヘッダー

- ファイル：`page_header.php`
- 内容：ロゴ、グローバルナビゲーション（`$HEADER_MENU`）、ログイン状態表示
- メニューは `menu` テーブル（親ID = HEADER）から role_level に基づいて動的生成

### 7.2. ページフッター

- ファイル：`page_footer.php`
- 内容：フッターリンク（`$FOOTER_MENU`）、コピーライト表示
- メニューは `menu` テーブル（親ID = FOOTER）から動的生成

### 7.3. ナビゲーションメニュー

- `menu` テーブルの `role_min`/`role_max` で権限フィルタリング
- `display_order` で表示順を管理
- 階層構造（`parent_id` で親子関係）

---

## 8. 入出力インターフェース

### 8.1. メール送信仕様

| 項目 | 内容 |
|:--|:--|
| クラス | Class_PHPMailer.php |
| 用途 | 認証コード送信、問い合わせ通知 |
| 形式 | HTML メール |
| 送信元 | CONFIG テーブルの設定値 |

**メール種別**

| 種別 | 件名 | 送信タイミング |
|:--|:--|:--|
| 認証コード通知 | 【HPL】メールアドレス認証コードのお知らせ | サインアップ STEP1 完了時 |
| 問い合わせ通知 | （問い合わせ内容） | お問い合わせ送信時 |

### 8.2. ファイル添付仕様

| 項目 | 内容 |
|:--|:--|
| テーブル | baseball_attachment |
| 保存先 | attachment/ ディレクトリ |
| 対応用途 | 大会情報への添付ファイル（スケジュール等） |
| カラム | physical_file_name, file_name, file_path, mime_type, file_size |

---

## 9. エラー処理仕様

| エラー種別 | 処理方法 |
|:--|:--|
| 未ログインアクセス | signin ページへリダイレクト |
| 権限不足 | error.php へリダイレクト |
| バリデーションエラー | SESSION['error_msg'] にメッセージ設定・画面再表示 |
| DB エラー | error_msg にメッセージ設定、Class_Log でエラーログ出力 |
| 一般エラー | error.php 表示 |

---

## 10. 変更履歴

| バージョン | 日付 | 変更者 | 変更内容 |
|:--|:--|:--|:--|
| 1.0 | 2026-06-21 | Imawa Office | 初版作成 |

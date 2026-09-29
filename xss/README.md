# 🛡️ XSS Security Lab (XSS脆弱性学習用Webアプリケーション)

PHPで作成された、クロスサイトスクリプティング（XSS: Cross-Site Scripting）の発生メカニズム、脅威、および安全な対策手法を実践・検証できる学習用Webアプリケーション（やられアプリ）です。

---

## 🚀 起動方法

### 方法 1: バッチファイルから起動（おすすめ）
フォルダ内の `start_server.bat` をダブルクリックします。自動的にブラウザで `http://localhost:8080` が開きます。

### 方法 2: コマンドラインから起動
PowerShell または コマンドプロンプトで以下のコマンドを実行します:

```powershell
# XAMPPのPHPを利用する場合
C:\xampp\php\php.exe -S localhost:8080 -t public
```

起動後、ブラウザで [http://localhost:8080](http://localhost:8080) にアクセスします。

---

## 🎯 搭載機能・学習モジュール

| モジュール | ページ | 主な学習内容 |
| :--- | :--- | :--- |
| **🏠 ダッシュボード** | `index.php` | XSSの基本概念、3大分類の俯瞰、学習ガイド |
| **⚡ 反射型XSS** | `reflected.php` | GETパラメータの出力、ブラックリスト置換の穴（大文字/イベントハンドラ）、`htmlspecialchars` の効果 |
| **💾 格納型XSS** | `stored.php` | 掲示板への投稿永続化（SQLite）、全閲覧者への影響、DB初期化リセット機能 |
| **🌐 DOM型XSS** | `dom.php` | `location.hash` の処理、`innerHTML` vs `textContent` のリアルタイム比較、サーバーに届かない攻撃の特性 |
| **🧩 コンテキスト別XSS** | `context.php` | 属性値からの脱出（`value="..."`）、`javascript:` 疑似プロトコル（`href`）、JS内変数展開（`json_encode`） |
| **🔒 多層防御・CSP** | `defenses.php` | Content Security Policy（CSP）のON/OFF検証、Cookieの `HttpOnly` 属性によるセッション保護、`htmlspecialchars` フラグ比較 |
| **📝 理解度クイズ** | `quiz.php` | 5問の選択式クイズと詳細解説、スコア自動採点 |
| **📖 対策ガイド** | `guide.php` | IPA「安全なウェブサイトの作り方」＆ OWASPに準拠した実装標準チートシート |

---

## 🎛️ セキュリティレベル切り替え機能

画面上部の切り替えバーから、いつでもリアルタイムにセキュリティ設定を変更できます。

- 🔴 **脆弱モード (Low / Vulnerable)**: 入力値・出力をそのまま反映（脆弱性が発生する状態）
- 🟡 **不完全対策モード (Medium / Weak Filter)**: 単純な `<script>` 除去など、不十分なブラックリスト処理の穴を検証
- 🟢 **安全モード (High / Secure)**: `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` やコンテキスト別対策を正しく適用した状態

---

## 📂 ディレクトリ構成

```text
c:\Users\fujir\Documents\xss\
├── start_server.bat        # Windows用1クリック起動バッチ
├── start_server.ps1        # PowerShell用起動スクリプト
├── README.md               # 本ドキュメント
├── public/                 # ドキュメントルート (Web公開領域)
│   ├── index.php           # 概要・ダッシュボード
│   ├── reflected.php       # 反射型XSS実習
│   ├── stored.php          # 格納型XSS実習
│   ├── dom.php             # DOM型XSS実習
│   ├── context.php         # コンテキスト別XSS実習
│   ├── defenses.php        # 多層防御・CSP・Cookie実習
│   ├── quiz.php            # 理解度チェッククイズ
│   ├── guide.php           # 対策チートシート
│   ├── api/
│   │   └── reset_db.php    # DB初期化用API
│   └── assets/
│       ├── css/style.css   # モダンダークUIスタイルシート
│       └── js/app.js       # フロントエンド対話ロジック
├── includes/               # 共通PHPモジュール
│   ├── header.php          # ナビゲーション・レベル切替バー
│   ├── footer.php          # 共通フッター
│   ├── db.php              # SQLiteデータベース接続・初期化
│   └── helper.php          # セキュリティレベル管理・エスケープ関数
└── data/                   # SQLiteデータ保存領域 (自動生成)
```

---

## 🛡️ 安全への配慮
本アプリケーションは完全なローカル環境（`localhost`）で動作する教育・自己学習用ツールです。外部通信を行わず、安全なテスト用サンプル値（ワンクリック入力チップ）が用意されているため、安全かつ体系的にWebセキュリティの基礎を学ぶことができます。

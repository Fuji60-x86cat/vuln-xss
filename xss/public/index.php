<?php
$page_title = 'ホーム / 概要';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">🛡️ XSS Security Lab へようこそ</h1>
            <p class="page-subtitle">
                Webアプリケーションにおける代表的な脆弱性<strong>「クロスサイトスクリプティング（XSS: Cross-Site Scripting）」</strong>の発生原因、影響、そして安全な対策方法を体験しながら学べる学習用サンドボックスです。
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="reflected.php" class="btn btn-primary">⚡ 実習をはじめる</a>
            <a href="guide.php" class="btn btn-secondary">📖 対策ガイドを見る</a>
        </div>
    </div>
</div>

<!-- Notice Alert -->
<div class="alert-box info">
    <span style="font-size: 1.2rem;">💡</span>
    <div>
        <strong>学習の進め方:</strong>
        画面上部の <strong>「セキュリティ設定バー」</strong> から、いつでも【🔴 脆弱】・【🟡 不完全】・【🟢 安全】を切り替えられます。各演習ページで同じ入力値を送信し、挙動の違いや実際のPHPソースコードの変化を比較検証してみましょう。
    </div>
</div>

<!-- 3 Main Types of XSS -->
<h2 style="font-size: 1.4rem; margin-bottom: 1rem; margin-top: 2rem;">📚 XSSの3大分類と学習モジュール</h2>
<div class="grid-3">
    <!-- Reflected XSS Card -->
    <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span class="badge badge-danger">基礎レベル</span>
                <span style="font-size: 1.5rem;">⚡</span>
            </div>
            <h3 class="card-title">1. 反射型XSS (Reflected)</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                URLパラメータやフォームの入力値が、サーバーを経由してそのままレスポンスHTMLに埋め込まれることで発生します。検索機能やエラー表示で多く見られます。
            </p>
            <ul style="color: var(--text-muted); font-size: 0.85rem; padding-left: 1.2rem; margin-bottom: 1.5rem; line-height: 1.6;">
                <li>GETパラメータの無害化漏れ</li>
                <li>罠リンクを踏ませる手口</li>
                <li><code>htmlspecialchars()</code> の効果</li>
            </ul>
        </div>
        <a href="reflected.php" class="btn btn-secondary btn-sm" style="width: 100%;">演習へ進む &rarr;</a>
    </div>

    <!-- Stored XSS Card -->
    <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span class="badge badge-danger">高危険度</span>
                <span style="font-size: 1.5rem;">💾</span>
            </div>
            <h3 class="card-title">2. 格納型XSS (Stored)</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                悪意ある入力値がデータベース等に永続保存され、それを閲覧した全てのユーザーのブラウザ上でスクリプトが自動実行される極めて危険度の高い脆弱性です。
            </p>
            <ul style="color: var(--text-muted); font-size: 0.85rem; padding-left: 1.2rem; margin-bottom: 1.5rem; line-height: 1.6;">
                <li>掲示板・コメント欄の実装</li>
                <li>永続化データの無害化タイミング</li>
                <li>1クリックでDBリセット機能完備</li>
            </ul>
        </div>
        <a href="stored.php" class="btn btn-secondary btn-sm" style="width: 100%;">演習へ進む &rarr;</a>
    </div>

    <!-- DOM-based XSS Card -->
    <div class="glass-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span class="badge badge-warning">フロントエンド</span>
                <span style="font-size: 1.5rem;">🌐</span>
            </div>
            <h3 class="card-title">3. DOM型XSS (DOM-based)</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                サーバー側ではなく、ブラウザ上のJavaScriptが危険なシンク（<code>innerHTML</code>, <code>eval</code>等）に未処理のデータを渡すことで発生します。
            </p>
            <ul style="color: var(--text-muted); font-size: 0.85rem; padding-left: 1.2rem; margin-bottom: 1.5rem; line-height: 1.6;">
                <li><code>location.hash</code> / <code>search</code>の処理</li>
                <li><code>innerHTML</code> vs <code>textContent</code></li>
                <li>クライアント側での安全なDOM構築</li>
            </ul>
        </div>
        <a href="dom.php" class="btn btn-secondary btn-sm" style="width: 100%;">演習へ進む &rarr;</a>
    </div>
</div>

<!-- Advanced & Defenses Section -->
<h2 style="font-size: 1.4rem; margin-bottom: 1rem; margin-top: 2.5rem;">🎯 応用コンテキスト &amp; 多層防御</h2>
<div class="grid-2">
    <!-- Context-based XSS -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span class="badge badge-info">コンテキスト別注意点</span>
            <span style="font-size: 1.5rem;">🧩</span>
        </div>
        <h3 class="card-title">コンテキスト別XSS演習</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
            HTML本文だけでなく、<strong>HTMLタグの属性値（<code>value="..."</code>）</strong>、<strong>リンク先（<code>href="javascript:..."</code>）</strong>、<strong>JavaScript内変数</strong>など、出力場所（コンテキスト）に応じた固有の落とし穴と対策を学びます。
        </p>
        <a href="context.php" class="btn btn-secondary btn-sm">コンテキスト演習へ &rarr;</a>
    </div>

    <!-- Defenses & CSP -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span class="badge badge-success">モダンセキュリティ</span>
            <span style="font-size: 1.5rem;">🔒</span>
        </div>
        <h3 class="card-title">対策技術 &amp; CSPラボ</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
            Content Security Policy（CSP）の設定や、セッションCookieの <strong><code>HttpOnly</code> 属性</strong> による保護効果をインタラクティブに検証できます。
        </p>
        <a href="defenses.php" class="btn btn-secondary btn-sm">対策ラボへ &rarr;</a>
    </div>
</div>

<!-- Architecture & Safety Info -->
<div class="glass-card" style="margin-top: 2rem; background: rgba(15, 23, 42, 0.4);">
    <h3 class="card-title">⚙️ 本環境の仕様と安全性について</h3>
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.75rem;">
        本アプリはPHP 8.2ビルトインサーバーおよびSQLiteで動作する完全ローカル完結型の学習環境です。外部への不要な通信は行わず、安全にWeb脆弱性のメカニズムと防御コードを試作・検証できます。
    </p>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.85rem; color: var(--text-muted);">
        <span>✓ データベース: SQLite (自動初期化)</span>
        <span>✓ セッション管理: PHPネイティブセッション</span>
        <span>✓ IPA「安全なウェブサイトの作り方」準拠</span>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

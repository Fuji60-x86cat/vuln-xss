<?php
$page_title = '対策技術 & CSPラボ';
require_once __DIR__ . '/../includes/helper.php';

// Handle CSP toggle
if (isset($_GET['toggle_csp'])) {
    $_SESSION['csp_enabled'] = !($_SESSION['csp_enabled'] ?? false);
    header('Location: defenses.php');
    exit;
}

$csp_active = $_SESSION['csp_enabled'] ?? false;

// If CSP active, send the header for this response
if ($csp_active) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">🔒 多層防御ラボ (CSP &amp; Cookie Security)</h1>
            <p class="page-subtitle">
                エスケープ処理に加え、現代のWebセキュリティで必須とされる多層防御技術（Content Security Policy, HttpOnly Cookie）を実践・検証します。
            </p>
        </div>
        <div>
            <?php if ($csp_active): ?>
                <span class="badge badge-success">🛡️ CSP 有効化中 (Active)</span>
            <?php else: ?>
                <span class="badge badge-warning">⚠️ CSP 無効 (Disabled)</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Section 1: Content Security Policy (CSP) Lab -->
    <div class="glass-card">
        <h3 class="card-title">🛡️ 1. Content Security Policy (CSP) 実験</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
            CSPは、ブラウザが読み込み・実行を許可するリソース（JavaScript, 画像, CSS等）の送信元をサーバーのレスポンスヘッダで制限する仕組みです。インラインスクリプトの無断実行を強力に阻止します。
        </p>

        <div style="margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.3); padding: 0.75rem 1rem; border-radius: 8px;">
                <div>
                    <span style="font-weight: 600; color: #fff;">現在のCSPヘッダ設定:</span>
                    <span style="color: <?= $csp_active ? 'var(--accent-emerald)' : 'var(--accent-amber)' ?>; font-weight: 700; margin-left: 0.5rem;">
                        <?= $csp_active ? '有効 (ON)' : '無効 (OFF)' ?>
                    </span>
                </div>
                <a href="?toggle_csp=1" class="btn <?= $csp_active ? 'btn-secondary' : 'btn-primary' ?> btn-sm">
                    <?= $csp_active ? 'CSP を無効化する' : 'CSP を有効化する' ?>
                </a>
            </div>
        </div>

        <?php if ($csp_active): ?>
            <div class="alert-box success">
                <div>
                    <strong>送信中のCSPヘッダ:</strong><br>
                    <code style="font-size:0.75rem; word-break: break-all; color:#a7f3d0;">
                        Content-Security-Policy: default-src 'self'; script-src 'self' ...
                    </code>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-box warning">
                <div>
                    <strong>CSPが無効です:</strong><br>
                    インラインの <code>&lt;script&gt;</code> や HTMLイベントハンドラ（<code>onload</code>, <code>onerror</code>）が制限なく実行できる状態です。
                </div>
            </div>
        <?php endif; ?>

        <div class="code-container" style="margin-top: 1rem;">
            <div class="code-header">
                <span>推奨される基本的なCSPディレクティブ例</span>
            </div>
            <pre class="code-content"><code><span class="comment"># インラインスクリプト・eval を禁止し、自サイトのJSファイルのみ許可</span>
<span class="func">Content-Security-Policy</span>: 
  <span class="keyword">default-src</span> <span class="string">'self'</span>; 
  <span class="keyword">script-src</span> <span class="string">'self'</span>; 
  <span class="keyword">object-src</span> <span class="string">'none'</span>; 
  <span class="keyword">base-uri</span> <span class="string">'self'</span>;</code></pre>
        </div>
    </div>

    <!-- Section 2: Cookie Security & HttpOnly -->
    <div class="glass-card">
        <h3 class="card-title">🍪 2. Cookieの保護と HttpOnly 属性</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
            XSS攻撃の代表的な被害は「セッションID Cookieの窃盗」です。Cookieに <code>HttpOnly</code> 属性を付与することで、JavaScript（<code>document.cookie</code>）からのアクセスを遮断し、セッション乗っ取りを防止します。
        </p>

        <div style="background: var(--bg-input); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; color: #fff;">現在発行されているCookie一覧（サーバー側視点）:</div>
            <ul style="font-size: 0.85rem; color: var(--text-secondary); list-style: none;">
                <li style="margin-bottom: 0.4rem;">
                    🔴 <code>demo_user_cookie</code> &rarr; <span class="badge badge-danger">HttpOnly なし（漏洩リスクあり）</span>
                </li>
                <li>
                    🟢 <code>secret_auth_cookie</code> &rarr; <span class="badge badge-success">HttpOnly あり（JSから不可視）</span>
                </li>
            </ul>
        </div>

        <button type="button" id="btn-inspect-cookies" class="btn btn-secondary" style="width: 100%;">
            🔍 ブラウザの JavaScript (document.cookie) で確認する
        </button>

        <div id="cookie-display" class="output-box" style="display: none; margin-top: 1rem; font-size: 0.85rem;">
            <!-- JS inspection output -->
        </div>

        <div class="code-container" style="margin-top: 1rem;">
            <div class="code-header">
                <span>PHPでの安全なCookie発行コード</span>
                <span class="badge badge-success">SECURE</span>
            </div>
            <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="func">setcookie</span>(<span class="string">'session_id'</span>, <span class="variable">$token</span>, [
    <span class="string">'expires'</span>  =&gt; <span class="func">time</span>() + 3600,
    <span class="string">'path'</span>     =&gt; <span class="string">'/'</span>,
    <span class="string">'domain'</span>   =&gt; <span class="string">'example.com'</span>,
    <span class="string">'secure'</span>   =&gt; <span class="keyword">true</span>,      <span class="comment">// 🔒 HTTPS通信時のみ送信</span>
    <span class="string">'httponly'</span> =&gt; <span class="keyword">true</span>,      <span class="comment">// 🛡️ JavaScriptからの参照を禁止（XSS対策）</span>
    <span class="string">'samesite'</span> =&gt; <span class="string">'Lax'</span>     <span class="comment">// 🌐 CSRF対策</span>
]);
<span class="keyword">?&gt;</span></code></pre>
        </div>
    </div>
</div>

<!-- Section 3: htmlspecialchars Flags Matrix -->
<div class="glass-card" style="margin-top: 1.5rem;">
    <h3 class="card-title">⚙️ 3. PHP <code>htmlspecialchars()</code> のフラグ詳細比較</h3>
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
        <code>htmlspecialchars()</code> は第2引数のフラグによって変換対象となる文字が大きく変わります。安全のため必ず <code>ENT_QUOTES | ENT_SUBSTITUTE</code> を指定します。
    </p>

    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-secondary);">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.6rem 0.8rem; color: #fff;">指定フラグ</th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;"><code>&amp;</code>, <code>&lt;</code>, <code>&gt;</code></th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;">ダブルクォート <code>&quot;</code></th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;">シングルクォート <code>&#039;</code></th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;">安全性評価</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 0.6rem 0.8rem; font-family: var(--font-mono); color: var(--accent-rose);">ENT_NOQUOTES</td>
                <td style="padding: 0.6rem 0.8rem; color: #10b981;">変換</td>
                <td style="padding: 0.6rem 0.8rem; color: #f43f5e;">変換しない</td>
                <td style="padding: 0.6rem 0.8rem; color: #f43f5e;">変換しない</td>
                <td style="padding: 0.6rem 0.8rem;"><span class="badge badge-danger">極めて危険 (属性値で脱出される)</span></td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 0.6rem 0.8rem; font-family: var(--font-mono); color: var(--accent-amber);">ENT_COMPAT (旧デフォルト)</td>
                <td style="padding: 0.6rem 0.8rem; color: #10b981;">変換</td>
                <td style="padding: 0.6rem 0.8rem; color: #10b981;">変換</td>
                <td style="padding: 0.6rem 0.8rem; color: #f43f5e;">変換しない</td>
                <td style="padding: 0.6rem 0.8rem;"><span class="badge badge-warning">シングルクォート属性値で危険</span></td>
            </tr>
            <tr>
                <td style="padding: 0.6rem 0.8rem; font-family: var(--font-mono); color: var(--accent-emerald); font-weight: 600;">ENT_QUOTES (推奨)</td>
                <td style="padding: 0.6rem 0.8rem; color: #10b981;">変換</td>
                <td style="padding: 0.6rem 0.8rem; color: #10b981;">変換 (<code>&amp;quot;</code>)</td>
                <td style="padding: 0.6rem 0.8rem; color: #10b981;">変換 (<code>&amp;#039;</code>)</td>
                <td style="padding: 0.6rem 0.8rem;"><span class="badge badge-success">推奨（完全エスケープ）</span></td>
            </tr>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

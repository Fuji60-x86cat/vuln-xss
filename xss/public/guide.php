<?php
$page_title = 'XSS対策総合ガイド・チートシート';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">📖 XSS対策総合ガイド &amp; チートシート</h1>
            <p class="page-subtitle">
                IPA「安全なウェブサイトの作り方」および OWASP Cheat Sheet Series に準拠した、安全なWebアプリケーション開発のための実装標準ガイドです。
            </p>
        </div>
        <div>
            <span class="badge badge-success">IPA / OWASP 準拠</span>
        </div>
    </div>
</div>

<!-- 1. Fundamental Countermeasures -->
<div class="glass-card">
    <h2 style="font-size: 1.3rem; margin-bottom: 1rem; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
        <span>🛡️</span> 1. 根本的対策（コンテキスト別エスケープと無害化）
    </h2>

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Context A -->
        <div style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: 8px; border-left: 3px solid var(--accent-emerald);">
            <h3 style="font-size: 1.05rem; color: #38bdf8; margin-bottom: 0.4rem;">① HTML要素の本文（テキストノード）に出力する場合</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                <code>&lt;p&gt;{$val}&lt;/p&gt;</code> や <code>&lt;div&gt;{$val}&lt;/div&gt;</code> などのHTML要素内に出力する際は、HTML特殊文字を実体参照に変換します。
            </p>
            <div class="code-container">
                <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="comment">// 必須: ENT_QUOTES と UTF-8 を明示</span>
<span class="func">echo</span> <span class="func">htmlspecialchars</span>(<span class="variable">$input</span>, ENT_QUOTES | ENT_SUBSTITUTE, <span class="string">'UTF-8'</span>);
<span class="keyword">?&gt;</span></code></pre>
            </div>
        </div>

        <!-- Context B -->
        <div style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: 8px; border-left: 3px solid var(--accent-emerald);">
            <h3 style="font-size: 1.05rem; color: #38bdf8; margin-bottom: 0.4rem;">② HTMLタグの属性値（value, title, placeholderなど）に出力する場合</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                属性値は<strong>必ずダブルクォート（<code>"..."</code>）で囲み</strong>、<code>ENT_QUOTES</code> を指定してダブルクォートを確実に <code>&amp;quot;</code> に変換します。
            </p>
            <div class="code-container">
                <pre class="code-content"><code><span class="comment">&lt;!-- ⭕ 推奨: 必ずダブルクォートで囲み、htmlspecialchars(..., ENT_QUOTES, 'UTF-8') を適用 --&gt;</span>
&lt;input type="text" name="keyword" value="<span class="keyword">&lt;?php</span> <span class="func">echo</span> <span class="func">htmlspecialchars</span>(<span class="variable">$keyword</span>, ENT_QUOTES, <span class="string">'UTF-8'</span>); <span class="keyword">?&gt;</span>"&gt;</code></pre>
            </div>
        </div>

        <!-- Context C -->
        <div style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: 8px; border-left: 3px solid var(--accent-emerald);">
            <h3 style="font-size: 1.05rem; color: #38bdf8; margin-bottom: 0.4rem;">③ リンク先（href）や画像（src）のURLに出力する場合</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                <code>javascript:</code> 疑似プロトコルは <code>htmlspecialchars()</code> では防げません。URLの先頭が <code>http://</code>、<code>https://</code>、または同一オリジンの相対パスであることを正規表現でホワイトリスト検証します。
            </p>
            <div class="code-container">
                <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="keyword">function</span> <span class="func">safe_url</span>(<span class="variable">$url</span>) {
    <span class="comment">// http, https, または / から始まる相対パスのみ許可</span>
    <span class="keyword">if</span> (<span class="func">preg_match</span>(<span class="string">'/^(https?:\/\/|\/)/i'</span>, <span class="variable">$url</span>)) {
        <span class="keyword">return</span> <span class="func">htmlspecialchars</span>(<span class="variable">$url</span>, ENT_QUOTES, <span class="string">'UTF-8'</span>);
    }
    <span class="keyword">return</span> <span class="string">'#'</span>; <span class="comment">// 不正なURLの場合は安全なフォールバック</span>
}
<span class="keyword">?&gt;</span>
&lt;a href="<span class="keyword">&lt;?php</span> <span class="func">echo</span> <span class="func">safe_url</span>(<span class="variable">$user_website</span>); <span class="keyword">?&gt;</span>"&gt;Webサイト&lt;/a&gt;</code></pre>
            </div>
        </div>

        <!-- Context D -->
        <div style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: 8px; border-left: 3px solid var(--accent-emerald);">
            <h3 style="font-size: 1.05rem; color: #38bdf8; margin-bottom: 0.4rem;">④ JavaScriptコード内の変数にPHPから値を渡す場合</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                <code>json_encode()</code> に特殊文字エスケープフラグ（<code>JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT</code>）を指定して展開するか、HTML要素の <code>data-*</code> 属性経由で渡します。
            </p>
            <div class="code-container">
                <pre class="code-content"><code>&lt;script&gt;
<span class="comment">// 🛡️ 安全: json_encode に HEXフラグを付与</span>
<span class="keyword">const</span> <span class="variable">userData</span> = <span class="keyword">&lt;?php</span> <span class="func">echo</span> <span class="func">json_encode</span>(<span class="variable">$userData</span>, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); <span class="keyword">?&gt;</span>;
&lt;/script&gt;</code></pre>
            </div>
        </div>
    </div>
</div>

<!-- 2. Defense-in-Depth -->
<div class="glass-card" style="margin-top: 1.5rem;">
    <h2 style="font-size: 1.3rem; margin-bottom: 1rem; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
        <span>🔒</span> 2. 保険的対策・多層防御（Defense-in-Depth）
    </h2>

    <div class="grid-2">
        <div style="background: rgba(0,0,0,0.25); padding: 1rem; border-radius: 8px;">
            <h3 style="font-size: 1rem; color: #fff; margin-bottom: 0.4rem;">🍪 Cookie属性（HttpOnly / Secure / SameSite）</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                セッションIDなどの重要Cookieには必ず <code>HttpOnly</code> 属性を付与し、JavaScriptからの窃取を防ぎます。
            </p>
            <div style="font-family:var(--font-mono); font-size:0.75rem; color:#a5b4fc;">
                Set-Cookie: session_id=...; Secure; HttpOnly; SameSite=Lax
            </div>
        </div>

        <div style="background: rgba(0,0,0,0.25); padding: 1rem; border-radius: 8px;">
            <h3 style="font-size: 1rem; color: #fff; margin-bottom: 0.4rem;">🛡️ Content Security Policy (CSP)</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                インラインスクリプトの実行を原則禁止し、信頼できるオリジンからのみスクリプトをロードするように制限します。
            </p>
            <div style="font-family:var(--font-mono); font-size:0.75rem; color:#a5b4fc;">
                Content-Security-Policy: default-src 'self'; script-src 'self'
            </div>
        </div>

        <div style="background: rgba(0,0,0,0.25); padding: 1rem; border-radius: 8px;">
            <h3 style="font-size: 1rem; color: #fff; margin-bottom: 0.4rem;">📜 HTTPレスポンスヘッダ</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                Content-Typeに文字コードを正しく指定し、MIMEタイプの誤認（MIME Sniffing）を禁止します。
            </p>
            <div style="font-family:var(--font-mono); font-size:0.75rem; color:#a5b4fc;">
                Content-Type: text/html; charset=UTF-8<br>
                X-Content-Type-Options: nosniff
            </div>
        </div>

        <div style="background: rgba(0,0,0,0.25); padding: 1rem; border-radius: 8px;">
            <h3 style="font-size: 1rem; color: #fff; margin-bottom: 0.4rem;">🔍 入力値バリデーション</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                数値や日付、選択肢など仕様上想定される型・フォーマットであることをサーバー側で厳格に検証します。
            </p>
            <div style="font-family:var(--font-mono); font-size:0.75rem; color:#a5b4fc;">
                filter_var($input, FILTER_VALIDATE_INT)
            </div>
        </div>
    </div>
</div>

<!-- 3. Anti-patterns -->
<div class="glass-card" style="margin-top: 1.5rem; border-color: rgba(244,63,94,0.3);">
    <h2 style="font-size: 1.3rem; margin-bottom: 1rem; color: var(--danger-text); display: flex; align-items: center; gap: 0.5rem;">
        <span>🚫</span> 3. やってはいけないNG対策（アンチパターン）
    </h2>

    <ul style="color: var(--text-secondary); font-size: 0.9rem; padding-left: 1.25rem; line-height: 1.8;">
        <li>
            <strong style="color: #fff;">❌ ブラックリスト方式による禁止タグの除去:</strong><br>
            <code>str_replace('&lt;script&gt;', '', $val)</code> などは、大文字小文字（<code>&lt;SCRIPT&gt;</code>）、多重ネスト（<code>&lt;scr&lt;script&gt;ipt&gt;</code>）、別タグ（<code>&lt;img onerror=...&gt;</code>, <code>&lt;svg onload=...&gt;</code>）で容易に回避されます。
        </li>
        <li>
            <strong style="color: #fff;">❌ DB保存時に入力値をエスケープしてしまうこと:</strong><br>
            保存時にエスケープすると、CSV出力やAPI配信、メール送信時に <code>&amp;amp;</code> 等の文字化けや二重エスケープ崩れが発生します。エスケープは<strong>「画面出力時（HTML生成時）」</strong>に行うのが原則です。
        </li>
        <li>
            <strong style="color: #fff;">❌ クォートなしの属性値配置:</strong><br>
            <code>&lt;input value=&lt;?php echo $val; ?&gt;&gt;</code> のようにクォートを省略すると、空白文字（スペース）を挟むだけで新しい属性（<code>onfocus=...</code>）を注入されてしまいます。
        </li>
    </ul>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

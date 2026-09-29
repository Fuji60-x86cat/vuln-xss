<?php
$page_title = 'DOM型XSS (DOM-based XSS)';
require_once __DIR__ . '/../includes/header.php';

$sec_lvl = get_sec_level();
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">🌐 シナリオ 3: DOM型XSS (DOM-based XSS)</h1>
            <p class="page-subtitle">
                サーバー側を経由せず、ブラウザ上のJavaScriptが危険なDOM操作（<code>innerHTML</code> や <code>eval</code> など）を行うことで発生するフロントエンド固有の脆弱性です。
            </p>
        </div>
        <div>
            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
        </div>
    </div>
</div>

<!-- Architecture explanation card -->
<div class="glass-card" style="margin-bottom: 1.5rem;">
    <h3 class="card-title">🔍 DOM型XSSの特徴: 「サーバーに届かない攻撃」</h3>
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.75rem;">
        URLのハッシュフラグメント（<code>#</code>以降）などはサーバーに送信されないため、<strong>サーバー側のWAFやPHP側のエスケープ処理では検知・防御することができません。</strong>
    </p>
    <div style="background: rgba(0,0,0,0.3); border-radius: 8px; padding: 0.75rem 1rem; font-family: var(--font-mono); font-size: 0.8rem; color: #67e8f9; border-left: 3px solid var(--accent-cyan);">
        [Source (入力源): location.hash / search] &rarr; [ブラウザ内JS処理] &rarr; [Sink (危険な出力先): innerHTML / eval] &rarr; [スクリプト実行]
    </div>
</div>

<div class="grid-2">
    <!-- Left Column: Interactive DOM XSS Lab -->
    <div>
        <!-- Demo 1: Hash / Dynamic Welcome Demo -->
        <div class="glass-card">
            <h3 class="card-title">🧪 実習 1: URLハッシュからの動的ユーザー名表示</h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem;">
                URLの末尾（<code>#user=...</code>）からユーザー名を取得して画面に反映するスクリプトです。
            </p>

            <div class="form-group">
                <label for="dom-hash-input" class="form-label">ユーザー名を入力（またはURL末尾を変更）:</label>
                <input type="text" id="dom-hash-input" class="form-input" placeholder="例: ゲストユーザー" value="Alice">
            </div>

            <button type="button" id="btn-update-hash" class="btn btn-primary" style="width: 100%;">URLハッシュを更新して反映</button>

            <div style="margin-top: 1.25rem;">
                <label class="form-label">テスト用サンプル値:</label>
                <div class="payload-chips-container">
                    <button type="button" class="payload-chip" data-target="#dom-hash-input" data-payload="ゲスト">通常テキスト</button>
                    <button type="button" class="payload-chip" data-target="#dom-hash-input" data-payload="<u style='color:#38bdf8;'>下線付きユーザー名</u>">HTML下線タグ</button>
                    <button type="button" class="payload-chip" data-target="#dom-hash-input" data-payload="<img src=x onerror=&quot;alert('DOM XSS 発火！ (innerHTML)')&quot;>">&lt;img onerror&gt;</button>
                </div>
            </div>

            <!-- Output Box -->
            <div style="margin-top: 1.5rem;">
                <label class="form-label">JavaScriptによる描画結果:</label>
                <div class="output-box" id="dom-welcome-output">
                    <!-- Dynamic greeting inserted here -->
                </div>
            </div>
        </div>

        <!-- Demo 2: Real-time Sink Comparison Playground -->
        <div class="glass-card">
            <h3 class="card-title">🔬 実習 2: <code>innerHTML</code> vs <code>textContent</code> リアルタイム比較</h3>
            
            <div class="form-group">
                <label for="live-input" class="form-label">リアルタイム入力欄:</label>
                <input type="text" id="live-input" class="form-input" placeholder="&lt;img src=x onerror=alert(1)&gt; などを入力...">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div>
                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--accent-rose); margin-bottom: 0.3rem;">
                        ❌ innerHTML (危険)
                    </div>
                    <div class="output-box" id="live-innerhtml-box" style="border-color: rgba(244,63,94,0.4);"></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-bottom: 0.3rem;">
                        🛡️ textContent (安全)
                    </div>
                    <div class="output-box" id="live-textcontent-box" style="border-color: rgba(16,185,129,0.4);"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Code Breakdown & Secure DOM APIs -->
    <div>
        <div class="glass-card">
            <h3 class="card-title">💻 クライアント側JavaScriptの脆弱コードと対策</h3>

            <div class="tabs-container">
                <div class="tabs-nav">
                    <button class="tab-btn <?= $sec_lvl === 'low' ? 'active' : '' ?>" data-tab="dom-low">🔴 危険な実装 (Sink)</button>
                    <button class="tab-btn <?= $sec_lvl === 'high' ? 'active' : '' ?>" data-tab="dom-high">🟢 安全な実装 (Safe API)</button>
                </div>

                <!-- Low Tab -->
                <div id="dom-low" class="tab-pane <?= $sec_lvl === 'low' ? 'active' : '' ?>">
                    <div class="alert-box danger">
                        <div>
                            <strong>危険なDOM操作（Sink）:</strong>
                            <code>location.hash</code> などの未検証データを <code>innerHTML</code> に代入すると、HTMLパースが行われ悪意あるタグが実行されます。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>vulnerable_dom.js</span>
                            <span class="badge badge-danger">VULNERABLE</span>
                        </div>
                        <pre class="code-content"><code><span class="comment">// ❌ 危険: URLのハッシュを取得</span>
<span class="keyword">const</span> <span class="variable">hash</span> = <span class="variable">window</span>.<span class="variable">location</span>.<span class="variable">hash</span>.<span class="func">substring</span>(1);
<span class="keyword">const</span> <span class="variable">params</span> = <span class="keyword">new</span> <span class="func">URLSearchParams</span>(<span class="variable">hash</span>);
<span class="keyword">const</span> <span class="variable">username</span> = <span class="variable">params</span>.<span class="func">get</span>(<span class="string">'user'</span>) || <span class="string">'ゲスト'</span>;

<span class="comment">// ❌ 危険: innerHTML に直接挿入</span>
<span class="highlight-bad"><span class="variable">document</span>.<span class="func">getElementById</span>(<span class="string">'greeting'</span>).<span class="variable">innerHTML</span> = <span class="string">"ようこそ、"</span> + <span class="variable">username</span> + <span class="string">" さん！"</span>;</span></code></pre>
                    </div>
                </div>

                <!-- High Tab -->
                <div id="dom-high" class="tab-pane <?= $sec_lvl === 'high' ? 'active' : '' ?>">
                    <div class="alert-box success">
                        <div>
                            <strong>安全なDOM操作（テキストとして扱う）:</strong>
                            <code>textContent</code> や <code>innerText</code>、または <code>document.createTextNode()</code> を使用します。ブラウザはこれらをHTMLタグとして解釈せず、純粋な文字列として安全に画面描画します。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>secure_dom.js</span>
                            <span class="badge badge-success">SECURE</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">const</span> <span class="variable">hash</span> = <span class="variable">window</span>.<span class="variable">location</span>.<span class="variable">hash</span>.<span class="func">substring</span>(1);
<span class="keyword">const</span> <span class="variable">params</span> = <span class="keyword">new</span> <span class="func">URLSearchParams</span>(<span class="variable">hash</span>);
<span class="keyword">const</span> <span class="variable">username</span> = <span class="variable">params</span>.<span class="func">get</span>(<span class="string">'user'</span>) || <span class="string">'ゲスト'</span>;

<span class="comment">// 🛡️ 安全: textContent を使用（HTMLとして評価されない）</span>
<span class="highlight-good"><span class="variable">document</span>.<span class="func">getElementById</span>(<span class="string">'greeting'</span>).<span class="variable">textContent</span> = <span class="string">"ようこそ、"</span> + <span class="variable">username</span> + <span class="string">" さん！"</span>;</span></code></pre>
                    </div>
                </div>
            </div>

            <!-- DOM Source and Sink Table -->
            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <h4 style="font-size: 1rem; margin-bottom: 0.75rem; color: #fff;">📋 注意すべき Source と Sink の対応表</h4>
                
                <table style="width:100%; font-size: 0.8rem; border-collapse: collapse; margin-bottom: 1rem; color: var(--text-secondary);">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.4rem 0.6rem; color: #fff;">危険な Source (入力元)</th>
                            <th style="padding: 0.4rem 0.6rem; color: #fff;">危険な Sink (代入先)</th>
                            <th style="padding: 0.4rem 0.6rem; color: #fff;">安全な代替手法</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 0.4rem 0.6rem;"><code>location.hash</code> / <code>search</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-rose);"><code>element.innerHTML</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-emerald);"><code>element.textContent</code></td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 0.4rem 0.6rem;"><code>document.referrer</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-rose);"><code>document.write()</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-emerald);"><code>DOM作成 (appendChild)</code></td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 0.4rem 0.6rem;"><code>window.name</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-rose);"><code>eval(string)</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-emerald);"><code>JSON.parse()</code></td>
                        </tr>
                        <tr>
                            <td style="padding: 0.4rem 0.6rem;"><code>postMessage (data)</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-rose);"><code>location.href = data</code></td>
                            <td style="padding: 0.4rem 0.6rem; color: var(--accent-emerald);"><code>URLプロトコル検証 (http/https)</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side execution logic for DOM XSS demonstration
(function() {
    const secLevel = <?= json_encode($sec_lvl) ?>;
    const btnUpdateHash = document.getElementById('btn-update-hash');
    const inputHash = document.getElementById('dom-hash-input');
    const outputGreeting = document.getElementById('dom-welcome-output');

    function renderGreeting() {
        const hash = window.location.hash.substring(1);
        const params = new URLSearchParams(hash);
        let user = params.get('user');
        
        if (!user) {
            user = inputHash.value || 'ゲスト';
        } else {
            inputHash.value = user;
        }

        if (secLevel === 'high') {
            // 🟢 SECURE
            outputGreeting.textContent = "ようこそ、" + user + " さん！（安全に描画中）";
        } else {
            // 🔴 VULNERABLE (innerHTML sink)
            outputGreeting.innerHTML = "ようこそ、" + user + " さん！（innerHTMLで描画中）";
        }
    }

    if (btnUpdateHash) {
        btnUpdateHash.addEventListener('click', () => {
            const val = inputHash.value;
            window.location.hash = 'user=' + encodeURIComponent(val);
            renderGreeting();
        });
    }

    // Initialize on page load and hashchange
    window.addEventListener('hashchange', renderGreeting);
    renderGreeting();

    // Live playground
    const liveInput = document.getElementById('live-input');
    const liveInnerBox = document.getElementById('live-innerhtml-box');
    const liveTextBox = document.getElementById('live-textcontent-box');

    if (liveInput && liveInnerBox && liveTextBox) {
        liveInput.addEventListener('input', () => {
            const val = liveInput.value;
            liveInnerBox.innerHTML = val || '<em style="color:var(--text-muted);">プレビュー表示</em>';
            liveTextBox.textContent = val || 'プレビュー表示';
        });
    }
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

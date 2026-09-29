<?php
$page_title = '反射型XSS (Reflected XSS)';
require_once __DIR__ . '/../includes/header.php';

$query = isset($_GET['q']) ? $_GET['q'] : '';
$has_search = isset($_GET['q']);
$sec_lvl = get_sec_level();
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">⚡ シナリオ 1: 反射型XSS (Reflected XSS)</h1>
            <p class="page-subtitle">
                検索キーワードやエラーメッセージなど、URLのGETパラメータに含まれる値がそのままHTMLに出力される脆弱性です。
            </p>
        </div>
        <div>
            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
        </div>
    </div>
</div>

<!-- Architecture explanation card -->
<div class="glass-card" style="margin-bottom: 1.5rem;">
    <h3 class="card-title">🔍 反射型XSSの仕組み</h3>
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.75rem;">
        攻撃者が悪意あるJavaScriptを含むURLリンクを作成し、被害者にクリックさせることで、被害者のブラウザ上で攻撃コードが実行されます。
    </p>
    <div style="background: rgba(0,0,0,0.3); border-radius: 8px; padding: 0.75rem 1rem; font-family: var(--font-mono); font-size: 0.8rem; color: #a5b4fc; border-left: 3px solid var(--accent-indigo);">
        [ユーザーのクリック] &rarr; GET /reflected.php?q=&lt;script&gt;...&lt;/script&gt; &rarr; [サーバーが出力を無加工で返却] &rarr; [ユーザーのブラウザがHTMLとして実行]
    </div>
</div>

<div class="grid-2">
    <!-- Left Column: Interactive Demonstration Area -->
    <div>
        <div class="glass-card">
            <h3 class="card-title">🧪 検索機能デモ</h3>
            
            <form method="GET" action="reflected.php">
                <div class="form-group">
                    <label for="search-input" class="form-label">検索キーワード (q):</label>
                    <input type="text" id="search-input" name="q" class="form-input" placeholder="検索したい単語を入力..." value="<?= h($query) ?>" autofocus>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">検索実行</button>
            </form>

            <div style="margin-top: 1.25rem;">
                <label class="form-label">安全なテスト用入力サンプル（クリックで自動入力）:</label>
                <div class="payload-chips-container">
                    <button type="button" class="payload-chip" data-target="#search-input" data-payload="Webセキュリティ入門">通常キーワード</button>
                    <button type="button" class="payload-chip" data-target="#search-input" data-payload="<b>太字タグのテスト</b>">HTMLタグ (太字)</button>
                    <button type="button" class="payload-chip" data-target="#search-input" data-payload="<script>alert('Reflected XSS 成功！')</script>">&lt;script&gt; タグ</button>
                    <button type="button" class="payload-chip" data-target="#search-input" data-payload="<SCRIPT>alert('大文字scriptでバイパス')</SCRIPT>">&lt;SCRIPT&gt; (大文字)</button>
                    <button type="button" class="payload-chip" data-target="#search-input" data-payload="<img src=x onerror=&quot;alert('img onerror で実行')&quot;>">&lt;img onerror&gt;</button>
                </div>
            </div>
        </div>

        <!-- Search Result Output Box -->
        <div class="glass-card">
            <h3 class="card-title">🖥️ 検索結果の表示エリア</h3>
            
            <?php if ($has_search): ?>
                <div style="margin-bottom: 0.75rem; color: var(--text-secondary); font-size: 0.9rem;">
                    検索結果: 
                </div>

                <div class="output-box" id="search-result-container">
                    <?php
                    if ($sec_lvl === 'low') {
                        // 🔴 VULNERABLE: Direct raw echo without escaping
                        echo "「 " . $query . " 」の検索結果: 0 件見つかりました。";
                    } elseif ($sec_lvl === 'medium') {
                        // 🟡 WEAK FILTER: Simple str_replace blacklist
                        $filtered = weak_filter($query);
                        echo "「 " . $filtered . " 」の検索結果: 0 件見つかりました。";
                    } else {
                        // 🟢 SECURE: Proper htmlspecialchars escaping
                        echo "「 " . h($query) . " 」の検索結果: 0 件見つかりました。";
                    }
                    ?>
                </div>

                <?php if ($sec_lvl === 'low' && strpos($query, '<') !== false): ?>
                    <div class="execution-notice">
                        <span>⚠️</span>
                        <span>HTML/スクリプトタグがそのままDOMに挿入されています。開発者ツールのElementsタブでHTML構造を確認してください。</span>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">
                    上部の検索フォームからキーワードを入力して検索を実行してください。
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Code Analysis & Comparison -->
    <div>
        <div class="glass-card">
            <h3 class="card-title">💻 サーバー側PHPコードの検証</h3>
            
            <div class="tabs-container">
                <div class="tabs-nav">
                    <button class="tab-btn <?= $sec_lvl === 'low' ? 'active' : '' ?>" data-tab="tab-low">🔴 脆弱コード (Low)</button>
                    <button class="tab-btn <?= $sec_lvl === 'medium' ? 'active' : '' ?>" data-tab="tab-medium">🟡 不完全コード (Medium)</button>
                    <button class="tab-btn <?= $sec_lvl === 'high' ? 'active' : '' ?>" data-tab="tab-high">🟢 安全コード (High)</button>
                </div>

                <!-- Tab Low -->
                <div id="tab-low" class="tab-pane <?= $sec_lvl === 'low' ? 'active' : '' ?>">
                    <div class="alert-box danger">
                        <div>
                            <strong>脆弱性の原因:</strong>
                            <code>$_GET['q']</code> の入力値を無加工・無エスケープで直接 <code>echo</code> しています。ブラウザはこれを正規のHTMLタグとしてパースするため、JavaScriptが実行されてしまいます。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>reflected_low.php</span>
                            <span class="badge badge-danger">VULNERABLE</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="variable">$query</span> = <span class="variable">$_GET</span>[<span class="string">'q'</span>] ?? <span class="string">''</span>;

<span class="comment">// ❌ 危険: エスケープせずに直接出力</span>
<span class="highlight-bad"><span class="func">echo</span> <span class="string">"&lt;p&gt;「 "</span> . <span class="variable">$query</span> . <span class="string">" 」の検索結果&lt;/p&gt;"</span>;</span>
<span class="keyword">?&gt;</span></code></pre>
                    </div>
                </div>

                <!-- Tab Medium -->
                <div id="tab-medium" class="tab-pane <?= $sec_lvl === 'medium' ? 'active' : '' ?>">
                    <div class="alert-box warning">
                        <div>
                            <strong>不完全な理由（ブラックリストの穴）:</strong>
                            <code>str_replace('&lt;script&gt;', '', ...)</code> で小文字の <code>&lt;script&gt;</code> のみを削除しています。大文字 <code>&lt;SCRIPT&gt;</code> や <code>&lt;img onerror=...&gt;</code> などのイベントハンドラタグで容易に回避（バイパス）されてしまいます。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>reflected_medium.php</span>
                            <span class="badge badge-warning">FLAWED FILTER</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="variable">$query</span> = <span class="variable">$_GET</span>[<span class="string">'q'</span>] ?? <span class="string">''</span>;

<span class="comment">// ⚠️ 危険: 不完全なブラックリスト置換</span>
<span class="variable">$filtered</span> = <span class="func">str_replace</span>(<span class="string">'&lt;script&gt;'</span>, <span class="string">''</span>, <span class="variable">$query</span>);
<span class="variable">$filtered</span> = <span class="func">str_replace</span>(<span class="string">'&lt;/script&gt;'</span>, <span class="string">''</span>, <span class="variable">$filtered</span>);

<span class="highlight-bad"><span class="func">echo</span> <span class="string">"&lt;p&gt;「 "</span> . <span class="variable">$filtered</span> . <span class="string">" 」の検索結果&lt;/p&gt;"</span>;</span>
<span class="keyword">?&gt;</span></code></pre>
                    </div>
                </div>

                <!-- Tab High -->
                <div id="tab-high" class="tab-pane <?= $sec_lvl === 'high' ? 'active' : '' ?>">
                    <div class="alert-box success">
                        <div>
                            <strong>安全な対策（完全なエスケープ）:</strong>
                            <code>htmlspecialchars($query, ENT_QUOTES, 'UTF-8')</code> を使用して、特殊文字（<code>&lt;</code>, <code>&gt;</code>, <code>&amp;</code>, <code>&quot;</code>, <code>&#039;</code>）をHTML実体参照に変換します。ブラウザはこれらをコードではなく単なる文字列として描画します。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>reflected_secure.php</span>
                            <span class="badge badge-success">SECURE</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="variable">$query</span> = <span class="variable">$_GET</span>[<span class="string">'q'</span>] ?? <span class="string">''</span>;

<span class="comment">// 🛡️ 安全: HTML特殊文字を確実にエスケープ</span>
<span class="highlight-good"><span class="func">echo</span> <span class="string">"&lt;p&gt;「 "</span> . <span class="func">htmlspecialchars</span>(<span class="variable">$query</span>, ENT_QUOTES | ENT_SUBSTITUTE, <span class="string">'UTF-8'</span>) . <span class="string">" 」の検索結果&lt;/p&gt;"</span>;</span>
<span class="keyword">?&gt;</span></code></pre>
                    </div>
                </div>
            </div>

            <!-- Learning Key Takeaways -->
            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <h4 style="font-size: 1rem; margin-bottom: 0.5rem; color: #fff;">💡 学習のポイント</h4>
                <ul style="color: var(--text-secondary); font-size: 0.85rem; padding-left: 1.2rem; line-height: 1.6;">
                    <li>ブラックリスト方式（禁止文字の除去）は抜け穴が多く、根本的な対策になりません。</li>
                    <li>HTML出力時には必ず <code>ENT_QUOTES</code> を指定してシングルクォートもエスケープ対象に含めます。</li>
                    <li>文字コードに <code>'UTF-8'</code> を明示的に指定します。</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
$page_title = 'コンテキスト別XSS (Context-based XSS)';
require_once __DIR__ . '/../includes/header.php';

$sec_lvl = get_sec_level();

// Context 1: Attribute Value
$input_attr = isset($_GET['attr_val']) ? $_GET['attr_val'] : 'guest_user';

// Context 2: URL Link (href)
$input_url = isset($_GET['url_val']) ? $_GET['url_val'] : 'https://example.com/profile';

// Context 3: JavaScript context
$input_js = isset($_GET['js_val']) ? $_GET['js_val'] : 'Alice';
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">🧩 シナリオ 4: コンテキスト別XSS (Context-based XSS)</h1>
            <p class="page-subtitle">
                「<code>htmlspecialchars()</code> を通せば万全」という誤解を解消し、出力先の文脈（HTML本文・属性値・URL・JavaScript内）に応じた適切な防御手法を学びます。
            </p>
        </div>
        <div>
            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
        </div>
    </div>
</div>

<!-- Overview banner -->
<div class="alert-box warning" style="margin-bottom: 2rem;">
    <span style="font-size: 1.2rem;">⚠️</span>
    <div>
        <strong>超重要:</strong>
        XSS対策は「どこに出力するか（コンテキスト）」によって必要な処理が異なります。HTML本文用のエスケープだけでは防げない代表例（属性値エスケープ漏れ、<code>javascript:</code> スキーム、JavaScript変数への直接展開）を以下で体験できます。
    </div>
</div>

<div class="grid-3">
    <!-- Context 1: Attribute Injection -->
    <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <span class="badge badge-danger" style="margin-bottom: 0.5rem;">Case 1</span>
            <h3 class="card-title">🏷️ HTML属性値コンテキスト</h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem;">
                <code>&lt;input value="..."&gt;</code> 等の属性値に埋め込む際、ダブルクォートで属性から抜け出されるケース。
            </p>

            <form method="GET" action="context.php">
                <input type="hidden" name="url_val" value="<?= h($input_url) ?>">
                <input type="hidden" name="js_val" value="<?= h($input_js) ?>">

                <div class="form-group">
                    <label class="form-label">ニックネーム:</label>
                    <input type="text" id="attr-input" name="attr_val" class="form-input" value="<?= h($input_attr) ?>">
                </div>

                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">属性値を反映</button>
            </form>

            <div style="margin-top: 1rem;">
                <label class="form-label" style="font-size:0.75rem;">テスト用サンプル値:</label>
                <div class="payload-chips-container">
                    <button type="button" class="payload-chip" data-target="#attr-input" data-payload="taro&quot; autofocus onfocus=&quot;alert('Attribute XSS!')">属性脱出 (onfocus)</button>
                    <button type="button" class="payload-chip" data-target="#attr-input" data-payload="taro&quot; onmouseover=&quot;alert('Hover XSS')">マウスオーバー</button>
                </div>
            </div>

            <!-- Output preview -->
            <div style="margin-top: 1.25rem;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">生成されたHTML:</div>
                <div class="output-box" style="font-size: 0.85rem;">
                    <?php if ($sec_lvl === 'low'): ?>
                        <!-- 🔴 VULNERABLE: Direct echo inside attribute -->
                        ユーザー入力フォーム:<br>
                        <input type="text" class="form-input" style="margin-top:0.4rem;" value="<?php echo $input_attr; ?>">
                    <?php else: ?>
                        <!-- 🟢 SECURE: ENT_QUOTES escaping -->
                        ユーザー入力フォーム:<br>
                        <input type="text" class="form-input" style="margin-top:0.4rem;" value="<?php echo htmlspecialchars($input_attr, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
            <strong>対策:</strong> <code>ENT_QUOTES</code> を指定して <code>"</code> と <code>'</code> も実体参照化する。
        </div>
    </div>

    <!-- Context 2: URL Scheme (href) -->
    <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <span class="badge badge-warning" style="margin-bottom: 0.5rem;">Case 2</span>
            <h3 class="card-title">🔗 リンク先 (href) コンテキスト</h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem;">
                <code>&lt;a href="..."&gt;</code> にユーザー入力URLをセットする場合、<code>javascript:</code> 疑似プロトコルによる攻撃。
            </p>

            <form method="GET" action="context.php">
                <input type="hidden" name="attr_val" value="<?= h($input_attr) ?>">
                <input type="hidden" name="js_val" value="<?= h($input_js) ?>">

                <div class="form-group">
                    <label class="form-label">WebサイトURL:</label>
                    <input type="text" id="url-input" name="url_val" class="form-input" value="<?= h($input_url) ?>">
                </div>

                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">URLを反映</button>
            </form>

            <div style="margin-top: 1rem;">
                <label class="form-label" style="font-size:0.75rem;">テスト用サンプル値:</label>
                <div class="payload-chips-container">
                    <button type="button" class="payload-chip" data-target="#url-input" data-payload="javascript:alert('javascript: スキームで発火！')">javascript: スキーム</button>
                    <button type="button" class="payload-chip" data-target="#url-input" data-payload="https://example.com">安全なURL (https)</button>
                </div>
            </div>

            <!-- Output preview -->
            <div style="margin-top: 1.25rem;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">生成されたリンク:</div>
                <div class="output-box" style="font-size: 0.85rem;">
                    <?php if ($sec_lvl === 'low' || $sec_lvl === 'medium'): ?>
                        <!-- 🔴 VULNERABLE: htmlspecialchars alone does NOT block javascript: -->
                        <a href="<?php echo htmlspecialchars($input_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-primary">🔗 リンクをクリックして開く</a>
                    <?php else: ?>
                        <!-- 🟢 SECURE: Validate URL scheme -->
                        <?php $safe_url = sanitize_url_safe($input_url); ?>
                        <a href="<?php echo $safe_url; ?>" class="btn btn-sm btn-primary">🔗 リンクをクリックして開く</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
            <strong>対策:</strong> <code>http://</code> または <code>https://</code> で始まることの正規表現ホワイトリスト検証。
        </div>
    </div>

    <!-- Context 3: JavaScript Variable Injection -->
    <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <span class="badge badge-danger" style="margin-bottom: 0.5rem;">Case 3</span>
            <h3 class="card-title">⚡ JavaScript内変数コンテキスト</h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem;">
                PHPから <code>&lt;script&gt; let user = "<?= '...' ?>"; &lt;/script&gt;</code> に値を直接展開し、文字列リテラルを終了させて別命令を差し込む手口。
            </p>

            <form method="GET" action="context.php">
                <input type="hidden" name="attr_val" value="<?= h($input_attr) ?>">
                <input type="hidden" name="url_val" value="<?= h($input_url) ?>">

                <div class="form-group">
                    <label class="form-label">JS内ユーザー名:</label>
                    <input type="text" id="js-input" name="js_val" class="form-input" value="<?= h($input_js) ?>">
                </div>

                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">JS変数を反映</button>
            </form>

            <div style="margin-top: 1rem;">
                <label class="form-label" style="font-size:0.75rem;">テスト用サンプル値:</label>
                <div class="payload-chips-container">
                    <button type="button" class="payload-chip" data-target="#js-input" data-payload="Alice&quot;; alert('JS文字列脱出！'); //">文字列脱出 ("; alert(); //)</button>
                    <button type="button" class="payload-chip" data-target="#js-input" data-payload="</script><script>alert('タグ強制終了')</script>">&lt;/script&gt; 強制終了</button>
                </div>
            </div>

            <!-- Output preview -->
            <div style="margin-top: 1.25rem;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">JS実行結果表示:</div>
                <div class="output-box" id="js-context-output" style="font-size: 0.85rem;">
                    <!-- Rendered by inline script -->
                </div>
            </div>
        </div>

        <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
            <strong>対策:</strong> <code>json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)</code> または HTMLの <code>data-*</code> 属性経由で渡す。
        </div>
    </div>
</div>

<!-- Inline Script Context Execution -->
<?php if ($sec_lvl === 'low'): ?>
    <script>
        // 🔴 VULNERABLE: Direct string interpolation
        try {
            let currentUser = "<?php echo $input_js; ?>";
            document.getElementById('js-context-output').textContent = "JS変数 currentUser の値: " + currentUser;
        } catch(e) {
            document.getElementById('js-context-output').innerHTML = "<span style='color:var(--accent-rose);'>構文エラー / 攻撃コードが実行されました</span>";
        }
    </script>
<?php else: ?>
    <script>
        // 🟢 SECURE: Proper JSON encoding with HEX flags
        let currentUser = <?php echo json_encode($input_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        document.getElementById('js-context-output').textContent = "JS変数 currentUser の値: " + currentUser + " （安全に渡されました）";
    </script>
<?php endif; ?>

<!-- Detailed Code Matrix -->
<div class="glass-card" style="margin-top: 2rem;">
    <h3 class="card-title">📖 コンテキスト別エスケープ・対策早見表</h3>
    
    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-secondary);">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.6rem 0.8rem; color: #fff;">出力コンテキスト</th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;">HTML例</th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;">危険な攻撃パターン</th>
                <th style="padding: 0.6rem 0.8rem; color: #fff;">安全な対策コード (PHP)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 0.6rem 0.8rem; font-weight: 600; color: #fff;">HTML本文</td>
                <td style="padding: 0.6rem 0.8rem;"><code>&lt;p&gt;{$val}&lt;/p&gt;</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-rose);"><code>&lt;script&gt;</code>, <code>&lt;img onerror&gt;</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-emerald);"><code>htmlspecialchars($val, ENT_QUOTES, 'UTF-8')</code></td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 0.6rem 0.8rem; font-weight: 600; color: #fff;">属性値 (クォート囲み)</td>
                <td style="padding: 0.6rem 0.8rem;"><code>&lt;input value="{$val}"&gt;</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-rose);"><code>" onfocus="alert(1)</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-emerald);"><code>htmlspecialchars($val, ENT_QUOTES, 'UTF-8')</code>（※ENT_QUOTES必須）</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 0.6rem 0.8rem; font-weight: 600; color: #fff;">リンク先 (href / src)</td>
                <td style="padding: 0.6rem 0.8rem;"><code>&lt;a href="{$url}"&gt;</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-rose);"><code>javascript:alert(1)</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-emerald);"><code>http://</code> / <code>https://</code> スキームのホワイトリスト検証 + エスケープ</td>
            </tr>
            <tr>
                <td style="padding: 0.6rem 0.8rem; font-weight: 600; color: #fff;">JavaScript内データ</td>
                <td style="padding: 0.6rem 0.8rem;"><code>let data = {$data};</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-rose);"><code>"; alert(1); //</code>, <code>&lt;/script&gt;</code></td>
                <td style="padding: 0.6rem 0.8rem; color: var(--accent-emerald);"><code>json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)</code></td>
            </tr>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

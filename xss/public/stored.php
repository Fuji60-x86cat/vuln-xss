<?php
$page_title = '格納型XSS (Stored XSS)';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = get_db_connection();
$sec_lvl = get_sec_level();
$feedback_msg = '';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_comment'])) {
    $author = trim($_POST['author'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');

    if ($author !== '' && $body !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments (author, title, body, created_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$author, $title, $body, date('Y-m-d H:i:s')]);
        $feedback_msg = 'コメントを投稿しました！';
    } else {
        $feedback_msg = 'お名前とコメント本文を入力してください。';
    }
}

// Fetch all comments
$stmt = $pdo->query("SELECT * FROM comments ORDER BY id DESC");
$comments = $stmt->fetchAll();
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">💾 シナリオ 2: 格納型XSS (Stored / Persistent XSS)</h1>
            <p class="page-subtitle">
                掲示板やプロフィール、商品レビュー等で、データベースに保存された悪意ある文字列がページ閲覧者全員のブラウザで自動実行される重大な脆弱性です。
            </p>
        </div>
        <div>
            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
        </div>
    </div>
</div>

<!-- Threat mechanism explanation -->
<div class="glass-card" style="margin-bottom: 1.5rem;">
    <h3 class="card-title">🔍 格納型XSSの脅威と影響</h3>
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.75rem;">
        攻撃者が一度投稿すると、被害者に罠リンクを踏ませることなく、該当ページを開いた<strong>すべての一般ユーザーや管理者</strong>に対して一斉に攻撃コードが実行されます。
    </p>
    <div style="background: rgba(0,0,0,0.3); border-radius: 8px; padding: 0.75rem 1rem; font-family: var(--font-mono); font-size: 0.8rem; color: #fca5a5; border-left: 3px solid var(--accent-rose);">
        [攻撃者の投稿] &rarr; [DBに永続保存] &rarr; [他の一般ユーザーが閲覧] &rarr; [全員のブラウザでスクリプトが自動実行]
    </div>
</div>

<?php if ($feedback_msg): ?>
    <div class="alert-box success">
        <span>✅</span>
        <div><?= h($feedback_msg) ?></div>
    </div>
<?php endif; ?>

<div class="grid-2">
    <!-- Left Column: Guestbook Board & Post Form -->
    <div>
        <!-- Post Form Card -->
        <div class="glass-card">
            <h3 class="card-title">✍️ 掲示板への新規投稿</h3>
            
            <form method="POST" action="stored.php">
                <input type="hidden" name="post_comment" value="1">
                
                <div class="form-group">
                    <label for="author" class="form-label">お名前 / 投稿者:</label>
                    <input type="text" id="author" name="author" class="form-input" placeholder="例: 匿名ユーザー" required>
                </div>

                <div class="form-group">
                    <label for="title" class="form-label">タイトル:</label>
                    <input type="text" id="title" name="title" class="form-input" placeholder="例: 初めての投稿" value="テスト投稿">
                </div>

                <div class="form-group">
                    <label for="body" class="form-label">本文 / メッセージ:</label>
                    <textarea id="body" name="body" class="form-textarea" placeholder="メッセージを入力してください..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">コメントを投稿する</button>
            </form>

            <div style="margin-top: 1.25rem;">
                <label class="form-label">テスト用サンプル値（クリックで入力欄に反映）:</label>
                <div class="payload-chips-container">
                    <button type="button" class="payload-chip" data-target="#body" data-payload="こんにちは！セキュリティの勉強中です。">通常メッセージ</button>
                    <button type="button" class="payload-chip" data-target="#body" data-payload="<h3 style=&quot;color:#38bdf8;&quot;>HTML見出しタグ</h3>">HTMLタグ装飾</button>
                    <button type="button" class="payload-chip" data-target="#body" data-payload="<script>alert('格納型XSS発火: 閲覧者全員に届きます！')</script>">&lt;script&gt; アラート</button>
                    <button type="button" class="payload-chip" data-target="#body" data-payload="<img src=invalid-img onerror=&quot;alert('格納型 img onerror 発火！')&quot;>">&lt;img onerror&gt;</button>
                </div>
            </div>
        </div>

        <!-- Stored Comments List -->
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 class="card-title" style="margin-bottom: 0;">💬 投稿一覧 (<?= count($comments) ?> 件)</h3>
                <button id="btn-reset-db" class="btn btn-secondary btn-sm" title="データベースを初期状態に戻します">🔄 データを初期状態にリセット</button>
            </div>

            <div id="comments-container">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card">
                        <div class="comment-meta">
                            <span class="comment-author">
                                👤 
                                <?php
                                if ($sec_lvl === 'low') {
                                    echo $comment['author'];
                                } elseif ($sec_lvl === 'medium') {
                                    echo weak_filter($comment['author']);
                                } else {
                                    echo h($comment['author']);
                                }
                                ?>
                            </span>
                            <span>🕒 <?= h($comment['created_at']) ?></span>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.4rem; color: #e2e8f0;">
                            <?php
                            if ($sec_lvl === 'low') {
                                echo $comment['title'];
                            } elseif ($sec_lvl === 'medium') {
                                echo weak_filter($comment['title']);
                            } else {
                                echo h($comment['title']);
                            }
                            ?>
                        </div>
                        <div class="comment-body">
                            <?php
                            if ($sec_lvl === 'low') {
                                // 🔴 VULNERABLE: Direct raw echo
                                echo nl2br($comment['body']);
                            } elseif ($sec_lvl === 'medium') {
                                // 🟡 WEAK FILTER: Weak blacklist
                                echo nl2br(weak_filter($comment['body']));
                            } else {
                                // 🟢 SECURE: htmlspecialchars escaping
                                echo nl2br(h($comment['body']));
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Code Analysis -->
    <div>
        <div class="glass-card">
            <h3 class="card-title">💻 格納型XSSの防御とソースコード比較</h3>

            <div class="tabs-container">
                <div class="tabs-nav">
                    <button class="tab-btn <?= $sec_lvl === 'low' ? 'active' : '' ?>" data-tab="stored-low">🔴 脆弱コード (Low)</button>
                    <button class="tab-btn <?= $sec_lvl === 'medium' ? 'active' : '' ?>" data-tab="stored-medium">🟡 不完全コード (Medium)</button>
                    <button class="tab-btn <?= $sec_lvl === 'high' ? 'active' : '' ?>" data-tab="stored-high">🟢 安全コード (High)</button>
                </div>

                <!-- Low Tab -->
                <div id="stored-low" class="tab-pane <?= $sec_lvl === 'low' ? 'active' : '' ?>">
                    <div class="alert-box danger">
                        <div>
                            <strong>脆弱性の原因:</strong>
                            DBから取得したデータをそのまま <code>echo $row['body'];</code> で出力しています。DB内に格納された悪意あるスクリプトが全閲覧者のブラウザ上で評価・実行されます。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>stored_display_low.php</span>
                            <span class="badge badge-danger">VULNERABLE</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="comment">// DBからコメント一覧を取得</span>
<span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="func">query</span>(<span class="string">"SELECT * FROM comments ORDER BY id DESC"</span>);
<span class="keyword">while</span> (<span class="variable">$row</span> = <span class="variable">$stmt</span>-><span class="func">fetch</span>()) {
    <span class="comment">// ❌ 危険: エスケープせず直接出力</span>
    <span class="highlight-bad"><span class="func">echo</span> <span class="string">"&lt;div class='comment'&gt;"</span> . <span class="variable">$row</span>[<span class="string">'body'</span>] . <span class="string">"&lt;/div&gt;"</span>;</span>
}
<span class="keyword">?&gt;</span></code></pre>
                    </div>
                </div>

                <!-- Medium Tab -->
                <div id="stored-medium" class="tab-pane <?= $sec_lvl === 'medium' ? 'active' : '' ?>">
                    <div class="alert-box warning">
                        <div>
                            <strong>不完全な理由:</strong>
                            <code>&lt;script&gt;</code> だけを除去しても、<code>&lt;img src=x onerror=...&gt;</code> や <code>&lt;svg onload=...&gt;</code>、<code>&lt;iframe src=...&gt;</code> などで無力化されます。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>stored_display_medium.php</span>
                            <span class="badge badge-warning">FLAWED</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="keyword">while</span> (<span class="variable">$row</span> = <span class="variable">$stmt</span>-><span class="func">fetch</span>()) {
    <span class="comment">// ⚠️ 危険: ブラックリスト置換（imgやsvgで簡単にバイパス可能）</span>
    <span class="variable">$safe</span> = <span class="func">str_replace</span>([<span class="string">'&lt;script&gt;'</span>, <span class="string">'&lt;/script&gt;'</span>], <span class="string">''</span>, <span class="variable">$row</span>[<span class="string">'body'</span>]);
    <span class="highlight-bad"><span class="func">echo</span> <span class="string">"&lt;div class='comment'&gt;"</span> . <span class="variable">$safe</span> . <span class="string">"&lt;/div&gt;"</span>;</span>
}
<span class="keyword">?&gt;</span></code></pre>
                    </div>
                </div>

                <!-- High Tab -->
                <div id="stored-high" class="tab-pane <?= $sec_lvl === 'high' ? 'active' : '' ?>">
                    <div class="alert-box success">
                        <div>
                            <strong>安全な対策（出力時の確実なエスケープ）:</strong>
                            表示時に <code>htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8')</code> を行います。改行を反映したい場合は <code>nl2br()</code> を併用します。
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>stored_display_secure.php</span>
                            <span class="badge badge-success">SECURE</span>
                        </div>
                        <pre class="code-content"><code><span class="keyword">&lt;?php</span>
<span class="keyword">while</span> (<span class="variable">$row</span> = <span class="variable">$stmt</span>-><span class="func">fetch</span>()) {
    <span class="comment">// 🛡️ 安全: 出力時に htmlspecialchars で無害化</span>
    <span class="variable">$escaped</span> = <span class="func">htmlspecialchars</span>(<span class="variable">$row</span>[<span class="string">'body'</span>], ENT_QUOTES | ENT_SUBSTITUTE, <span class="string">'UTF-8'</span>);
    <span class="highlight-good"><span class="func">echo</span> <span class="string">"&lt;div class='comment'&gt;"</span> . <span class="func">nl2br</span>(<span class="variable">$escaped</span>) . <span class="string">"&lt;/div&gt;"</span>;</span>
}
<span class="keyword">?&gt;</span></code></pre>
                    </div>
                </div>
            </div>

            <!-- Important Architecture Q&A -->
            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <h4 style="font-size: 1rem; margin-bottom: 0.5rem; color: #fff;">❓ よくある疑問: 「DB保存時」と「画面出力時」、どこでエスケープすべき？</h4>
                <div style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.6;">
                    <p style="margin-bottom: 0.5rem;">
                        <strong>答え: 原則として「画面出力時（HTML生成時）」に行います。</strong>
                    </p>
                    <ul style="padding-left: 1.2rem; color: var(--text-muted);">
                        <li>DBにはプレーンな元データを保存しておくことで、APIやメール送信、CSV出力などHTML以外の用途でも二重エスケープなどの崩れを起こさず再利用できます。</li>
                        <li>出力先（HTML本文、属性値、JSON、SQL等）によって適用すべきエスケープ手法が異なるため、出力直前のコンテキストに合わせてエスケープするのが鉄則です。</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

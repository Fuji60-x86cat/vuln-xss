<?php
$page_title = 'XSS理解度クイズ';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">📝 XSS理解度チェッククイズ</h1>
            <p class="page-subtitle">
                これまで学習した各XSSの発生メカニズム、エスケープ処理の原則、多層防御の知識をクイズ形式で確認しましょう。
            </p>
        </div>
        <div>
            <span class="badge badge-info">全 5 問</span>
        </div>
    </div>
</div>

<div class="glass-card" id="quiz-app">
    <div id="quiz-container">
        <!-- Question 1 -->
        <div class="quiz-question" data-q="1" data-correct="2">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem; color: #fff;">
                第1問: データベースに保存された攻撃コードが、該当ページを開いた全ユーザーのブラウザで実行されるXSSの種類はどれ？
            </h3>
            <div class="quiz-options">
                <div class="quiz-option" data-idx="0">1. 反射型XSS (Reflected XSS)</div>
                <div class="quiz-option" data-idx="1">2. DOM型XSS (DOM-based XSS)</div>
                <div class="quiz-option" data-idx="2">3. 格納型XSS (Stored / Persistent XSS)</div>
                <div class="quiz-option" data-idx="3">4. ブラインドSQLインジェクション</div>
            </div>
            <div class="quiz-explanation alert-box info" style="display:none; margin-top: 0.75rem;">
                <strong>解説 (正解: 3):</strong> 格納型XSSは掲示板やプロフィールなどに悪意あるスクリプトが永続化され、アクセスした全ユーザーに自動的に被害が及ぶ極めて影響度の高い脆弱性です。
            </div>
        </div>

        <!-- Question 2 -->
        <div class="quiz-question" data-q="2" data-correct="1">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem; color: #fff;">
                第2問: PHPで <code>htmlspecialchars()</code> を使用する際、シングルクォート（<code>'</code>）も安全にエスケープするために指定すべきフラグはどれ？
            </h3>
            <div class="quiz-options">
                <div class="quiz-option" data-idx="0">1. <code>ENT_NOQUOTES</code></div>
                <div class="quiz-option" data-idx="1">2. <code>ENT_QUOTES</code></div>
                <div class="quiz-option" data-idx="2">3. <code>ENT_COMPAT</code></div>
                <div class="quiz-option" data-idx="3">4. <code>ENT_IGNORE</code></div>
            </div>
            <div class="quiz-explanation alert-box info" style="display:none; margin-top: 0.75rem;">
                <strong>解説 (正解: 2):</strong> <code>ENT_QUOTES</code> を指定することでダブルクォート（<code>"</code>）とシングルクォート（<code>'</code>）の両方が実体参照に変換され、属性値内での脱出攻撃を防御できます。
            </div>
        </div>

        <!-- Question 3 -->
        <div class="quiz-question" data-q="3" data-correct="0">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem; color: #fff;">
                第3問: <code>&lt;a href="..."&gt;</code> のリンク先URLにユーザー入力を埋め込む際、<code>htmlspecialchars()</code> だけでは防げない攻撃手法はどれ？
            </h3>
            <div class="quiz-options">
                <div class="quiz-option" data-idx="0">1. <code>javascript:</code> 疑似プロトコルを用いたスクリプト実行</div>
                <div class="quiz-option" data-idx="1">2. <code>&lt;script&gt;</code> タグの直接挿入</div>
                <div class="quiz-option" data-idx="2">3. ダブルクォートによる属性値の脱出</div>
                <div class="quiz-option" data-idx="3">4. 特殊文字 <code>&amp;</code> の不正エンコード</div>
            </div>
            <div class="quiz-explanation alert-box info" style="display:none; margin-top: 0.75rem;">
                <strong>解説 (正解: 1):</strong> <code>javascript:alert(1)</code> には <code>&lt;</code> や <code>&gt;</code> などのHTML特殊文字が含まれないため、<code>htmlspecialchars()</code> を素通りします。対策にはスキーム（<code>http://</code>, <code>https://</code>）のホワイトリスト検証が必要です。
            </div>
        </div>

        <!-- Question 4 -->
        <div class="quiz-question" data-q="4" data-correct="3">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem; color: #fff;">
                第4問: URLのハッシュ（<code>#...</code>）を利用したDOM型XSSに対して、サーバー側のWAFやPHPのバリデーションが無力な理由として正しいものはどれ？
            </h3>
            <div class="quiz-options">
                <div class="quiz-option" data-idx="0">1. ハッシュ値は常に暗号化されて送信されるから</div>
                <div class="quiz-option" data-idx="1">2. PHPはハッシュ値を自動的に安全な文字に変換するから</div>
                <div class="quiz-option" data-idx="2">3. WAFはGETリクエストの検査に対応していないから</div>
                <div class="quiz-option" data-idx="3">4. URLのハッシュフラグメント（#以降）はHTTPリクエストとしてサーバーに送信されないから</div>
            </div>
            <div class="quiz-explanation alert-box info" style="display:none; margin-top: 0.75rem;">
                <strong>解説 (正解: 4):</strong> ブラウザの仕様上、URLの <code>#</code> 以降（フラグメント）はサーバーへのHTTPリクエストに含まれず、ブラウザ内でのみ処理されます。そのためクライアント側JSでの <code>textContent</code> 適用など安全な実装が不可欠です。
            </div>
        </div>

        <!-- Question 5 -->
        <div class="quiz-question" data-q="5" data-correct="1">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem; color: #fff;">
                第5問: セッション管理用Cookieに <code>HttpOnly</code> 属性を設定する主な目的はどれ？
            </h3>
            <div class="quiz-options">
                <div class="quiz-option" data-idx="0">1. HTTP通信のみを許可し、HTTPSを遮断するため</div>
                <div class="quiz-option" data-idx="1">2. JavaScriptの <code>document.cookie</code> からの参照を禁止し、XSSによるセッション奪取を防ぐため</div>
                <div class="quiz-option" data-idx="2">3. データベースへのSQLインジェクションを防止するため</div>
                <div class="quiz-option" data-idx="3">4. Cookieの有効期限を自動的に無限化するため</div>
            </div>
            <div class="quiz-explanation alert-box info" style="display:none; margin-top: 0.75rem;">
                <strong>解説 (正解: 2):</strong> <code>HttpOnly</code> 属性を付与されたCookieは、万が一XSS脆弱性によって攻撃者のJavaScriptが実行された場合でもブラウザから保護され、セッションIDの盗難を防ぎます。
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
        <div id="quiz-score" style="font-size: 1.1rem; font-weight: 700; color: #fff;">
            回答状況: <span id="answered-count">0</span> / 5 問
        </div>
        <button id="btn-reset-quiz" class="btn btn-secondary btn-sm">クイズをやり直す</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const questions = document.querySelectorAll('.quiz-question');
    let answered = 0;
    let score = 0;

    questions.forEach(q => {
        const correctIdx = parseInt(q.getAttribute('data-correct'), 10);
        const options = q.querySelectorAll('.quiz-option');
        const expl = q.querySelector('.quiz-explanation');

        options.forEach(opt => {
            opt.addEventListener('click', () => {
                if (q.classList.contains('locked')) return;
                q.classList.add('locked');
                answered++;

                const selectedIdx = parseInt(opt.getAttribute('data-idx'), 10);
                if (selectedIdx === correctIdx) {
                    opt.classList.add('correct');
                    score++;
                } else {
                    opt.classList.add('wrong');
                    options[correctIdx].classList.add('correct');
                }

                if (expl) expl.style.display = 'block';

                document.getElementById('answered-count').textContent = answered;
                if (answered === questions.length) {
                    document.getElementById('quiz-score').innerHTML = `🎉 採点結果: <span style="color:var(--accent-emerald); font-size:1.4rem;">${score} / ${questions.length} 点 (${Math.round((score/questions.length)*100)}%)</span>`;
                }
            });
        });
    });

    document.getElementById('btn-reset-quiz').addEventListener('click', () => {
        window.location.reload();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

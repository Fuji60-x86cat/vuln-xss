<?php
// includes/db.php - SQLite database connection and initial schema

function get_db_path() {
    $data_dir = __DIR__ . '/../data';
    if (!is_dir($data_dir)) {
        mkdir($data_dir, 0777, true);
    }
    return $data_dir . '/app.sqlite';
}

function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        $db_path = get_db_path();
        $is_new = !file_exists($db_path);
        
        $pdo = new PDO('sqlite:' . $db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($is_new) {
            init_database($pdo);
        }
    }
    return $pdo;
}

function init_database($pdo) {
    // Comments table for Stored XSS demonstration
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            author TEXT NOT NULL,
            title TEXT NOT NULL,
            body TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Profile table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS profiles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            bio TEXT,
            website TEXT,
            avatar_color TEXT DEFAULT '#6366f1',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Insert sample initial data
    reset_sample_data($pdo);
}

function reset_sample_data($pdo = null) {
    if ($pdo === null) {
        $pdo = get_db_connection();
    }
    
    $pdo->exec("DELETE FROM comments;");
    $pdo->exec("DELETE FROM sqlite_sequence WHERE name='comments';");

    $stmt = $pdo->prepare("INSERT INTO comments (author, title, body, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Alice (一般ユーザー)', 'ようこそ！', 'この掲示板はXSSの脆弱性学習用デモです。気軽に投稿テストしてみてください。', date('Y-m-d H:i:s', strtotime('-2 hours'))]);
    $stmt->execute(['Bob (セキュリティ学習者)', '質問です', 'JavaScriptの実行とHTMLタグの解釈の違いについて学んでいます。', date('Y-m-d H:i:s', strtotime('-1 hours'))]);
    $stmt->execute(['Charlie (管理者)', 'お知らせ', 'システムメンテナンス完了のお知らせです。タグのエスケープ処理を確認中。', date('Y-m-d H:i:s', strtotime('-10 minutes'))]);

    $pdo->exec("DELETE FROM profiles;");
    $pdo->exec("DELETE FROM sqlite_sequence WHERE name='profiles';");
    $stmt = $pdo->prepare("INSERT INTO profiles (username, bio, website, avatar_color) VALUES (?, ?, ?, ?)");
    $stmt->execute(['security_ninja', 'Webセキュリティ勉強中のエンジニアです。', 'https://example.com/ninja', '#3b82f6']);
}

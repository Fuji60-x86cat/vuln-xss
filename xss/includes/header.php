<?php
// includes/header.php
require_once __DIR__ . '/helper.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$badge = get_sec_level_badge();
$sec_lvl = get_sec_level();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? h($page_title) . ' - ' : '' ?>XSS Security Lab (XSS学習・実験ラボ)</title>
    <meta name="description" content="PHPで学ぶクロスサイトスクリプティング（XSS）の脆弱性学習・実験用Webアプリケーション">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Header Navigation -->
<header class="header-navbar">
    <div class="nav-container">
        <a href="index.php" class="brand-logo">
            <span>🛡️</span>
            <span>XSS Security Lab</span>
            <span class="brand-badge">PHP 8.2</span>
        </a>

        <nav>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link <?= $current_page === 'index' ? 'active' : '' ?>">🏠 概要</a></li>
                <li><a href="reflected.php" class="nav-link <?= $current_page === 'reflected' ? 'active' : '' ?>">⚡ 反射型XSS</a></li>
                <li><a href="stored.php" class="nav-link <?= $current_page === 'stored' ? 'active' : '' ?>">💾 格納型XSS</a></li>
                <li><a href="dom.php" class="nav-link <?= $current_page === 'dom' ? 'active' : '' ?>">🌐 DOM型XSS</a></li>
                <li><a href="context.php" class="nav-link <?= $current_page === 'context' ? 'active' : '' ?>">🧩 コンテキスト別</a></li>
                <li><a href="defenses.php" class="nav-link <?= $current_page === 'defenses' ? 'active' : '' ?>">🔒 対策&CSP</a></li>
                <li><a href="quiz.php" class="nav-link <?= $current_page === 'quiz' ? 'active' : '' ?>">📝 理解度クイズ</a></li>
                <li><a href="guide.php" class="nav-link <?= $current_page === 'guide' ? 'active' : '' ?>">📖 対策ガイド</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Security Level Control Bar -->
<div class="mode-bar">
    <div class="mode-bar-container">
        <div class="mode-bar-status">
            <span style="color:var(--text-muted);font-weight:600;">現在のセキュリティ設定:</span>
            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
            <span style="color:var(--text-secondary);font-size:0.8rem;margin-left:0.5rem;"><?= $badge['desc'] ?></span>
        </div>
        <div class="mode-buttons">
            <a href="?set_level=low" class="mode-btn <?= $sec_lvl === 'low' ? 'active low' : '' ?>" title="対策なし：入力値がそのままHTMLに出力される">🔴 脆弱 (Low)</a>
            <a href="?set_level=medium" class="mode-btn <?= $sec_lvl === 'medium' ? 'active medium' : '' ?>" title="不完全なブラックリスト置換（scriptタグのみ削除など）">🟡 不完全 (Medium)</a>
            <a href="?set_level=high" class="mode-btn <?= $sec_lvl === 'high' ? 'active high' : '' ?>" title="完全なhtmlspecialchars・コンテキスト別エスケープ適用">🟢 安全 (High)</a>
        </div>
    </div>
</div>

<main class="main-content">

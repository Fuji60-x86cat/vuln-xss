// public/assets/js/app.js - Interactive UI and learning helpers

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tab switching logic
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-tab');
            const parent = btn.closest('.tabs-container') || document;
            
            parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            parent.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            
            btn.classList.add('active');
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // 2. Payload Chip Quick-Insert
    const payloadChips = document.querySelectorAll('.payload-chip');
    payloadChips.forEach(chip => {
        chip.addEventListener('click', () => {
            const targetSelector = chip.getAttribute('data-target');
            const payload = chip.getAttribute('data-payload');
            if (targetSelector && payload !== null) {
                const targetInput = document.querySelector(targetSelector);
                if (targetInput) {
                    targetInput.value = payload;
                    targetInput.focus();
                    
                    // Trigger input event for live bindings
                    targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    // Flash effect
                    targetInput.style.borderColor = 'var(--accent-cyan)';
                    setTimeout(() => {
                        targetInput.style.borderColor = '';
                    }, 600);
                }
            }
        });
    });

    // 3. Database Reset confirmation
    const resetDbBtn = document.getElementById('btn-reset-db');
    if (resetDbBtn) {
        resetDbBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (confirm('投稿データを初期状態にリセットしますか？')) {
                try {
                    const res = await fetch('api/reset_db.php', { method: 'POST' });
                    const data = await res.json();
                    if (data.success) {
                        alert('データを初期状態にリセットしました。');
                        window.location.reload();
                    } else {
                        alert('リセットに失敗しました: ' + (data.error || '不明なエラー'));
                    }
                } catch (err) {
                    alert('通信エラーが発生しました。');
                }
            }
        });
    }

    // 4. Cookie Inspector helper
    const btnInspectCookies = document.getElementById('btn-inspect-cookies');
    const cookieDisplay = document.getElementById('cookie-display');
    if (btnInspectCookies && cookieDisplay) {
        btnInspectCookies.addEventListener('click', () => {
            const cookies = document.cookie;
            if (cookies) {
                cookieDisplay.innerHTML = `<strong>document.cookie で取得できた値:</strong><br><code>${escapeHtml(cookies)}</code><br><span style="color:var(--text-muted);font-size:0.8rem;">※ HttpOnly属性が付与されたCookie（secret_auth_cookie）はJavaScriptから隠蔽されているため、ここには表示されません。</span>`;
            } else {
                cookieDisplay.innerHTML = `<em>document.cookie からアクセス可能なCookieはありません。</em>`;
            }
            cookieDisplay.style.display = 'block';
        });
    }
});

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        switch (m) {
            case '&': return '&amp;';
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '"': return '&quot;';
            case "'": return '&#039;';
            default: return m;
        }
    });
}

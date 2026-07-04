document.addEventListener('DOMContentLoaded', function () {

    // ── Close dropdowns when clicking outside ─────────────────────
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.khas-profile-wrapper')) {
            document.querySelectorAll('.khas-dropdown').forEach(d => {
                d.classList.remove('show');
            });
        }
    });

    // ── Password show/hide toggle ──────────────────────────────────
    document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // ── Auto-scroll message thread to bottom ──────────────────────
    const thread = document.getElementById('msg-thread');
    if (thread) thread.scrollTop = thread.scrollHeight;

    // ── Confirm before delete ──────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });
});
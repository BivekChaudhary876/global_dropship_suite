export function initNavProgress() {
    const bar = document.getElementById('nav-progress');
    if (!bar) return;

    function start() {
        bar.style.width = '0%';
        bar.classList.add('active');
        requestAnimationFrame(() => { bar.style.width = '70%'; });
    }

    document.addEventListener('click', e => {
        const a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.href.startsWith('javascript:') || a.hasAttribute('download')) return;
        if (new URL(a.href, location.href).origin !== location.origin) return;
        start();
    });

    document.addEventListener('submit', e => {
        if (e.target.tagName === 'FORM' && !e.target.hasAttribute('data-no-progress')) start();
    });

    window.addEventListener('pageshow', () => {
        bar.style.width = '100%';
        setTimeout(() => bar.classList.remove('active'), 250);
    });
}

export function initScrollReveal() {
    const items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    const io = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    items.forEach(el => io.observe(el));
}

export function addToCartAjax(url, quantity, buttonEl) {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    if (buttonEl) buttonEl.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: quantity || 1 }),
    })
        .then(res => res.json())
        .then(data => {
            const countEl = document.getElementById('cart-count');
            countEl.textContent = data.count;
            countEl.classList.remove('pulse');
            void countEl.offsetWidth;
            countEl.classList.add('pulse');

            const pill = document.getElementById('cart-pill');
            pill.classList.remove('hit');
            void pill.offsetWidth;
            pill.classList.add('hit');

            const toast = document.getElementById('toast');
            document.getElementById('toast-text').textContent = data.message || 'Added to cart';
            toast.classList.add('show');
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => toast.classList.remove('show'), 2200);
        })
        .finally(() => { if (buttonEl) buttonEl.disabled = false; });
}

export function rippleOn(btn, e) {
    const rect = btn.getBoundingClientRect();
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
    btn.style.position = 'relative';
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 550);
}

export function initZoomLens() {
    const wrap = document.getElementById('pd-zoom-wrap');
    const img = document.getElementById('pd-zoom-img');
    if (!wrap || !img) return;

    wrap.addEventListener('mousemove', e => {
        const r = wrap.getBoundingClientRect();
        const x = ((e.clientX - r.left) / r.width) * 100;
        const y = ((e.clientY - r.top) / r.height) * 100;
        img.style.transformOrigin = `${x}% ${y}%`;
        img.style.transform = 'scale(1.8)';
    });

    wrap.addEventListener('mouseleave', () => { img.style.transform = 'scale(1)'; });
}

export function initOrderStatusUpdater() {
    const select = document.getElementById('order-status-select');
    if (!select) return;

    const STATUS_COLORS = {
        pending: ['#C98A2C', 'rgba(201,138,44,0.14)'],
        paid: ['#2F6E4E', 'rgba(47,110,78,0.14)'],
        shipped: ['#2F6E4E', 'rgba(47,110,78,0.14)'],
        completed: ['#12172B', 'rgba(18,23,43,0.08)'],
        cancelled: ['#FF5A3C', 'rgba(255,90,60,0.12)'],
    };

    select.addEventListener('change', function () {
        const orderId = this.dataset.orderId;
        const newStatus = this.value;
        const badgeWrap = document.getElementById('order-status-badge');
        const previous = badgeWrap.innerHTML;
        const token = document.querySelector('meta[name="csrf-token"]').content;

        badgeWrap.innerHTML = '<span class="status-badge status-updating">Updating...</span>';

        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ status: newStatus }),
        })
            .then(res => {
                if (!res.ok) throw new Error('Request failed');
                return res.json();
            })
            .then(data => {
                const [color, bg] = STATUS_COLORS[data.status] || ['#12172B', 'rgba(18,23,43,0.08)'];
                badgeWrap.innerHTML = `<span class="status-badge" style="color:${color};background:${bg};">${data.label}</span>`;
            })
            .catch(() => {
                badgeWrap.innerHTML = previous;
                alert('Could not update status.');
            });
    });
}

export function initAccountDropdown() {
    const trigger = document.getElementById('account-trigger');
    const dropdown = document.getElementById('account-dropdown');
    if (!trigger || !dropdown) return;

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && e.target !== trigger) {
            dropdown.classList.remove('open');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') dropdown.classList.remove('open');
    });
}

export function initThemeToggle() {
    const toggleBtn = document.getElementById('theme-switch');
    if (!toggleBtn) return;

    const root = document.documentElement;

    if (root.getAttribute('data-theme') === 'dark') {
        toggleBtn.classList.add('on');
    }

    toggleBtn.addEventListener('click', () => {
        const isDark = root.getAttribute('data-theme') === 'dark';
        if (isDark) {
            root.removeAttribute('data-theme');
            localStorage.removeItem('theme');
            toggleBtn.classList.remove('on');
        } else {
            root.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            toggleBtn.classList.add('on');
        }
    });
}

window.addToCartAjax = addToCartAjax;
window.rippleOn = rippleOn;

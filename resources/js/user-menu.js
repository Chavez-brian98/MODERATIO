const initUserMenu = () => {
    const root = document.querySelector('[data-user-menu]');

    if (!root) {
        return;
    }

    const toggle = root.querySelector('[data-user-menu-toggle]');
    const dropdown = root.querySelector('[data-user-menu-dropdown]');

    if (!toggle || !dropdown || toggle.dataset.userMenuBound === 'true') {
        return;
    }

    toggle.dataset.userMenuBound = 'true';

    const close = () => {
        dropdown.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', () => {
        const isOpen = !dropdown.classList.contains('hidden');

        if (isOpen) {
            close();

            return;
        }

        dropdown.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !dropdown.classList.contains('hidden')) {
            close();
            toggle.focus();
        }
    });
};

export { initUserMenu };

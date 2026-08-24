let container = null;
let activeTrigger = null;

const ensureContainer = () => {
    if (!container) {
        container = document.createElement('div');
        container.setAttribute('data-permissions-modal-container', '');
        container.setAttribute('aria-hidden', 'true');
        document.body.appendChild(container);
    }

    return container;
};

const closeModal = () => {
    if (!container) {
        return;
    }

    container.innerHTML = '';
    container.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
    activeTrigger?.focus();
    activeTrigger = null;
};

const buildFormData = (form, matrix) => {
    const formData = new FormData(form);

    if ((form.dataset.permissionsMode || 'ids') !== 'states') {
        return formData;
    }

    let inherited = [];

    try {
        inherited = JSON.parse(matrix.dataset.inherited || '[]');
    } catch {
        inherited = [];
    }

    matrix.querySelectorAll('input[type="checkbox"][data-permission-id]').forEach((checkbox) => {
        const permissionId = Number(checkbox.dataset.permissionId);
        const state = checkbox.checked ? 'allow' : (inherited.includes(permissionId) ? 'deny' : 'inherit');

        formData.append(`permissions[${permissionId}]`, state);
    });

    return formData;
};

const bindModalBehaviour = (modal) => {
    const form = modal.querySelector('[data-permissions-form]');
    const superToggle = modal.querySelector('[data-super-admin-toggle]');
    const matrix = modal.querySelector('[data-permissions-matrix]');
    const lockedHint = modal.querySelector('[data-permissions-locked-hint]');

    if (superToggle && matrix) {
        const applySuperState = () => {
            matrix.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.disabled = superToggle.checked;

                if (superToggle.checked) {
                    checkbox.checked = true;
                }
            });

            matrix.classList.toggle('opacity-40', superToggle.checked);
            lockedHint?.classList.toggle('hidden', !superToggle.checked);
        };

        superToggle.addEventListener('change', applySuperState);
        applySuperState();
    }

    if (!form || !matrix || form.dataset.permissionsBound === 'true') {
        return;
    }

    form.dataset.permissionsBound = 'true';

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitButton = form.querySelector('[data-permissions-submit]');
        submitButton?.setAttribute('disabled', 'disabled');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: buildFormData(form, matrix),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            await Swal.fire({
                title: 'Permisos actualizados',
                text: 'Los permisos se guardaron correctamente.',
                icon: 'success',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#D76AA2',
                timer: 2500,
                timerProgressBar: true,
            });

            window.location.reload();
        } catch (error) {
            submitButton?.removeAttribute('disabled');

            Swal.fire({
                title: 'Error',
                text: 'No se pudieron guardar los permisos. Inténtalo de nuevo.',
                icon: 'error',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#dc2626',
            });
        }
    });
};

const openPermissionsModal = async (button) => {
    const url = button.dataset.permissionsRole || button.dataset.permissionsUser;

    if (!url || typeof Swal === 'undefined') {
        return;
    }

    activeTrigger = document.activeElement;

    const modalContainer = ensureContainer();
    modalContainer.innerHTML = '';

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'text/html',
            },
        });

        if (!response.ok) {
            return;
        }

        modalContainer.innerHTML = await response.text();

        const modal = modalContainer.querySelector('[data-role-permissions-modal]');
        if (!modal) {
            closeModal();
            return;
        }

        modalContainer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        const focusable = modal.querySelectorAll('button:not([disabled]), input:not([disabled]), [href]');
        const firstFocusable = focusable[0];
        const lastFocusable = focusable[focusable.length - 1];
        firstFocusable?.focus();

        modal.addEventListener('click', (event) => {
            if (event.target === modal.querySelector('[data-modal-backdrop]')) {
                closeModal();
            }
        });

        modal.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
                return;
            }

            if (event.key === 'Tab' && focusable.length > 0) {
                if (event.shiftKey && document.activeElement === firstFocusable) {
                    event.preventDefault();
                    lastFocusable.focus();
                } else if (!event.shiftKey && document.activeElement === lastFocusable) {
                    event.preventDefault();
                    firstFocusable.focus();
                }
            }
        });

        modal.querySelectorAll('[data-modal-close]').forEach((element) => {
            element.addEventListener('click', closeModal);
        });

        bindModalBehaviour(modal);
    } catch (error) {
        closeModal();
    }
};

export const initPermissionsModal = () => {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-permissions-role], [data-permissions-user]');

        if (trigger) {
            openPermissionsModal(trigger);
        }
    });
};

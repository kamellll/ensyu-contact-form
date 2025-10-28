
(() => {
    const modal = document.getElementById('detailModal');
    if (!modal) return;
    const panel = modal.querySelector('.modal__panel');
    const overlay = modal.querySelector('.modal__overlay');
    //const mName = document.getElementById('m-name');
    const fields = {
        id: document.getElementById('m-id'),
        name: document.getElementById('m-name'),
        email: document.getElementById('m-email'),
        gender: document.getElementById('m-gender'),
        category: document.getElementById('m-category'),
        tel: document.getElementById('m-tel'),
        address: document.getElementById('m-address'),
        building: document.getElementById('m-building'),
        detail: document.getElementById('m-detail'),
    };

    function openModal() { modal.hidden = false; panel.focus(); }
    function closeModal() { modal.hidden = true; }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-detail');

        if (btn) {
            fields.id.value = btn.dataset.id || '';
            fields.name.textContent = btn.dataset.name || '';
            fields.email.textContent = btn.dataset.email || '';
            fields.gender.textContent = btn.dataset.gender || '';
            fields.category.textContent = btn.dataset.category || '';
            fields.tel.textContent = btn.dataset.tel || '';
            fields.address.textContent = btn.dataset.address || '';
            fields.building.textContent = btn.dataset.building || '';
            fields.detail.textContent = btn.dataset.detail || '';
            openModal();
            return;
        }
        if (e.target.closest('[data-close]')) closeModal();
    });
})();
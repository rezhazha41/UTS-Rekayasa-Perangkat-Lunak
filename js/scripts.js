// Menampilkan modal
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('show');
}

// Menyembunyikan modal
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('show');
}

// Mengelola klik di luar modal
window.onclick = function(event) {
    const modal = document.querySelector('.modal.show');
    if (modal && event.target === modal) {
        hideModal(modal.id);
    }
}

// Menyembunyikan modal saat tombol Esc ditekan
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.querySelector('.modal.show');
        if (modal) {
            hideModal(modal.id);
        }
    }
});

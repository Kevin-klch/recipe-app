function openInfoModal() {
    document.getElementById('info-modal').classList.add('show');
}

function closeInfoModal() {
    document.getElementById('info-modal').classList.remove('show');
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('info-modal');

    if (event.target === modal) {
        closeInfoModal();
    }
});
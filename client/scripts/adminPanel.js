document.addEventListener('DOMContentLoaded', () => {

    // счетчик обьявлений
    const subtitle = document.querySelector('.subtitle');
    const deleteButtons = document.querySelectorAll('.delete-btn');

    function updateCounter() {
        const rows = document.querySelectorAll('tbody tr');
        subtitle.textContent = rows.length + ' inzerátů';
    }

    updateCounter();

    // удаление обьявлегия
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            row.remove();
            updateCounter();
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const subtitle = document.querySelector('.subtitle');

    function counter() {
        const count = document.querySelectorAll('.card').length;
        subtitle.textContent = count + ' inzerátů';
    }

    counter();

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = btn.closest('.card');

            card.classList.add('removing');

            setTimeout(function() {
                card.remove();

                counter(); 
                
            }, 200);
        });
    });
});
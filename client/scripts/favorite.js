import {config} from "./config.js";

document.addEventListener('DOMContentLoaded', async function() {
    if(sessionStorage.getItem("user_id")==null){
        window.location.href = "/Zasuvka/client/login.html"
    }

    const response = await fetch(config.API_URL+"/favorites", {
        headers:{
            "Content-Type":"application/json"
        },
        method:"GET",
        credentials:"include"
    })
    if(response.ok){
        const data = await response.json()
        const favoriteList = data.list.items;
        let containerHTML = ``;
        for (let item of favoriteList){
            containerHTML+=`
                <div class="card">
                    <div class="card-image-container">
                        <img src="${config.API_STATIC+item.photo}" alt="iPhone" class="card-image">
                        <button class="remove-heart-btn" title="Odebrat">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#dc2626" stroke="#dc2626">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="card-content">
                        <div class="card-header">
                            <h3 class="product-name">${item.name}</h3>
                            <div class="product-price">${item.price} Kč</div>
                        </div>

                        <button class="view-button"><a href="/Zasuvka/client/product.html?id=${item.productId}">Zobrazit</a></button>
                    </div>
                </div>
            `
        }
        document.getElementById("item_container").innerHTML = containerHTML;
    }
    const heartButtons = document.querySelectorAll('.remove-heart-btn');
    const subtitle = document.querySelector('.subtitle');

    function counter() {
        const count = document.querySelectorAll('.card').length;
        subtitle.textContent = count + ' inzerátů';
    }

    counter();

    heartButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = btn.closest('.card');
            
            card.classList.add('removing');

            setTimeout(function() {
                card.remove();
                
                counter()
                
            }, 200);
        });
    });
});
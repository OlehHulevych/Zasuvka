import {config} from "./config.js";
import {deleteMyProduct} from "./deleteMyProduct.js";



document.addEventListener('DOMContentLoaded', async function() {
    if(!sessionStorage.getItem("user_id")){
        window.location.href = "/Zasuvka/client/login.html"
    }
    const response = await fetch(config.API_URL+"/productByUser", {
        headers:{
            "Content-Type":"application/json"
        },
        method:"GET",
        credentials:"include"
    })
    if(response.ok){
        const data = await response.json()
        let favoriteList = data.items;
        console.log(favoriteList)
        console.log(data)
        let containerHTML = ``;
        for (let item of Object.values(favoriteList)){
            containerHTML+=`
                <div id="item_element" class="card" data-id="${item.id}">
                <div class="card-image-container">
                    <img src="${config.API_STATIC+item.photos[0]}" alt="Product" class="card-image">
                    
                    <a href="/Zasuvka/client/edit.html?id=${item.id}" class="icon-btn edit-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>

                    <button id="remove_button" class="icon-btn delete-btn" title="Smazat">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
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
            </div>`
            document.getElementById("item_container").innerHTML = containerHTML;
            const heartButton = document.getElementById("remove_button")
            heartButton.addEventListener("click",async(e)=>{
                e.preventDefault();
                const deletedId = document.getElementById("item_element").dataset.id;
                console.log(deletedId)
                await deleteMyProduct(deletedId);
                const elementToRemove = document.querySelector(`div[data-id="${deletedId}"]`);
                elementToRemove.remove();
            })

        }

    }

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
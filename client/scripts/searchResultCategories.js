import {config} from "./config.js";

document.addEventListener('DOMContentLoaded', async (e) => {
    e.preventDefault();
    const newUrlParams = new URLSearchParams(window.location.search);
    const search = newUrlParams.get("search") ?? null;
    const category = newUrlParams.get("category") ?? null;
    const low_cost = newUrlParams.get("low_cost")?? null;
    const big_cost = newUrlParams.get("big_cost")?? null;
    let current_page = newUrlParams.get("page")?? 1;

    let products = []
    let totalPages = 1

    try{
        const response = await fetch(config.API_URL+`/product?search=${search}&category=${category}&low_cost=${low_cost}&big_cost=${big_cost}&page=${current_page}`,{
            headers:{
                "Content-Type":"application/json"
            },
            method:"GET"

        })
        if(response.ok){
            const data = await response.json();
            products = data.products.items;
            console.log(data)
            totalPages = data.products.totalPages;
        }

    }
    catch (error){
        console.error(error)
    }
    let container = document.getElementById("productsList");
    let newHTML = ``
    products.forEach(product => {
        newHTML+=`
            <div class="card">
                <div class="card-image-container">
                    <img src="${config.API_STATIC+product.photos[0]}" alt="Product" class="card-image">
                </div>
                <div class="card-content">
                    <div class="card-header">
                        <h3 class="product-name">${product.name}</h3>
                        <div class="product-price">${product.price} Kč</div>
                    </div>
                    <div class="card-meta">
                        <div class="icon2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <p>${product.author}</p>
                        </div>
                    </div>
                    <button class="view-button"><a href="/Zasuvka/client/product.html?id=${product.id}">Zobrazit</a></button>
                </div>
            </div>
        `
    } )
    container.innerHTML = newHTML;





    // счетчик обьявлений
    const cards = document.querySelectorAll('.card');
    const subtitle = document.getElementById('resultCount');
    
    subtitle.textContent = cards.length + ' výsledků';


    // при нажатии на лого переходит на главную страницу
    const backButton = document.querySelector(".logo-container")

    backButton.addEventListener("click", function() {
        window.location.href = "index.html"
    })


    // при нажатии на сердце или человека перейдет туда

    // сердце
    const iconButtonFavorites = document.querySelector(".favorites")

    iconButtonFavorites.addEventListener("click", function() {
        window.location.href = "/Zasuvka/client/favorite.html"
    })

    // человек
    const iconButtonProfil = document.querySelector(".profil")

    iconButtonProfil.addEventListener("click", function() {
        window.location.href = "/Zasuvka/client/profil.html"
    })


    // при нажатии на категорию появляется параметр с ней в адресной строке
    const categoriesButtons = document.querySelectorAll(".categories-button");

    categoriesButtons.forEach(button => {
        button.addEventListener("click", () => {
            const category = button.dataset.category;
            const url = new URL(window.location.href);

            url.searchParams.set("category", category);
            window.location.href = url.toString();
        });
    });


    const search_bar = document.getElementById("search_bar");
    search_bar.addEventListener("submit",()=>{
        const query = searchBarForm.elements['search'].value;
        window.location.href = `/Zasuvka/client/result.html?search=${query}`
    })


    // пагинация
    const paginationContainer = document.getElementById('pagination');
    let newPaginationHtml = ``
    console.log(totalPages)
    for(let i=1;i<=totalPages;i++){
        if(i==current_page){
            newPaginationHtml+=`
             <button class="page-btn active">${i}</button>
        `
        }
        else{
            newPaginationHtml+=`
             <button class="page-btn">${i}</button>
        `
        }

    }
    paginationContainer.innerHTML = newPaginationHtml

    const paginationButtons = document.querySelectorAll(".page-btn")
    paginationButtons.forEach(button=>{
        button.addEventListener('click',(e)=>{
            e.preventDefault();
            const page = button.innerText;
            window.location.href = `/Zasuvka/client/result.html?search=${search}&page=${page}&category=${category}&low_cost=${low_cost}&big_cost=${big_cost}`
        })
    })


    /*function showPage(page) {
        const start = (page - 1) * itemsPerPage;
        const end = page * itemsPerPage;

        cards.forEach((card, index) => {
            if (index >= start && index < end) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }*/
});
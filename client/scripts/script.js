import {config} from "./config.js";
//mobile search bar functionality
let mobileSearchBarButton = document.getElementById('mobile_search_button');
mobileSearchBarButton.addEventListener('click', ()=>{
    let mobileSearchBar = document.getElementById('mobile_search_bar')
    mobileSearchBar.classList.add('active')
})

document.getElementById('cancel_button').addEventListener('click',(e)=>{
    e.preventDefault();
    let mobileSearchBar = document.getElementById('mobile_search_bar')
    mobileSearchBar.classList.remove('active')
})

//user menu functionality
let user_button = document.getElementById("user_button")
let user_menu =  document.getElementById("user_menu");
let user_menu_cancel_menu = document.getElementById("user_menu_cancel_button")
user_button.addEventListener("click", ()=>{
    user_menu.classList.add("active");
})
user_menu_cancel_menu.addEventListener("click", ()=>{
    user_menu.classList.remove("active");
})

//loading content
document.addEventListener("DOMContentLoaded",async()=>{
    const response = await fetch(config.API_URL + "/product",{
        method:"GET",
        headers:{
            "Content-Type":"application/json"
        },
        credentials:"include"
    });

    const data = await response.json();
    //console.log(data)
    console.log(data)
    let box = document.getElementById("ad_container");
    let productHtml = '';
    
    data.products.items.forEach((item)=>{
        
        productHtml += ` <div class="ad_item">
                    <div class="img_container">
                        <img src="${config.API_STATIC}${item.photos[0]}" alt="">
                    </div>
                    <div class="ad_information">
                        <div class="ad_name">${item.name}</div>
                        <div class="ad_state">Used</div>
                        <div class="ad_price">${item.price} ${item.currency}</div>
                        <div class="ad_author">${item.author}</div>
                    </div> 
                       </div>`



    })
    box.innerHTML = productHtml;
})

//                    <img src="${config.API_STATIC}${item.photos[0]}" alt="">
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

let searchBarForm = document.getElementById("search_bar")

searchBarForm.addEventListener("submit",(e)=>{
    e.preventDefault()
    const query = searchBarForm.elements['search'].value;
    window.location.href = `/Zasuvka/client/result.html?query=${encodeURIComponent(query)}`
})

const category_buttons = document.querySelectorAll(".category-item");

category_buttons.forEach((category_button)=>{
    category_button.addEventListener("click",(e)=>{
        e.preventDefault()
        const query = category_button.dataset.category;
        window.location.href = `/Zasuvka/client/result.html?category=${encodeURIComponent(query)}`
    })
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
                        <a href="/Zasuvka/client/product.html?id=${item.id}" class="ad_name">${item.name}</a>
                        <div class="ad_price">${item.price} Kč</div>
                        <div class="ad_author">${item.author}</div>
                    </div> 
                       </div>`



    })
    box.innerHTML = productHtml;
    const userResponse = await fetch(config.API_URL+"/user/authorize",{
        method:"GET"
    });
    if(userResponse.ok){
        const data = await userResponse.json();
        console.log(data);
        const {user} = data;
        sessionStorage.setItem('user_name', user.name)
        sessionStorage.setItem('user_email', user.email)
        sessionStorage.setItem('user_phone', user.phone)
        sessionStorage.setItem('photoPath', user.photoPath);







    }
    else{
        const data = await userResponse.json();
        console.log(data)
    }

    const add_product_button = document.querySelector('a.add_product_button')

    //user profile functionality
    const manage_button = document.getElementById("manage_user_button")
    if(sessionStorage.getItem('user_id')){
        manage_button.classList.add('hidden');
        const photoPath = sessionStorage.getItem('photoPath')
        const avatarPhoto = document.querySelector('.user_avatar')
        const user_profile = document.querySelector(".user_profile")
        user_profile.classList.add('active')
        avatarPhoto.src = config.API_STATIC + photoPath;
        add_product_button.classList.add('active')
    }

    let x = window.matchMedia("(max-width:590px)")
    console.log(x)
    x.addEventListener("change", ()=>{
        if(x.matches){
            add_product_button.innerHTML = "<i class=\"fa-solid fa-plus\"></i>"
        }
        else{
            add_product_button.innerHTML = "Add Product"
        }


    })



})



//                    <img src="${config.API_STATIC}${item.photos[0]}" alt="">
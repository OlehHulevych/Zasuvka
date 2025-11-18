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
    let container = document.getElementById("ad_container");
    let productHtml = '';
    
    container.innerHTML = data.products.items.map(item=>{

    })
})
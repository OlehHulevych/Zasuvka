import {config} from "./config";

export const searchFunction = async (e,query)=>{
    e.preventDefault()
    let container = document.getElementsByClassName(".container")
    const response = await fetch(config.API_URL+`/product?search=${query}`,{
        method:"GET",
        headers:{
            'Content-Type':'application/json'
        }
    })
    if(response.ok){
        const data = await response.json();
        let productHTML = ''
        data.products.forEach((item)=>{
            productHTML+=`<div class="product-card">
        <div class="card-image" style="background: url('${item.photos[0]}')">
        
        </div>
        <div class="card-body">
            <div class="card-row-top">
                <div class="product-title">${item.name}</div>
                <div class="product-price">$${item.price}</div>
            </div>
            <div class="card-row-mid">


            </div>
            <div class="product-desc">
                ${item.description}
            </div>
            <div class="card-actions">
                <button class="btn-add">View Details</button>
                <button class="btn-view">Add to favorites</button>
            </div>
        </div>
    </div>`
        })
        container.innerHTML = productHTML;
    }
    else{
        console.log(response)
    }

}
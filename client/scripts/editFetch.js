import {config} from "./config.js";

export const editFetch = async (productId) =>{
    let editItem = null
    const response = await fetch(config.API_URL + `/product/id?id=${productId}`);
    if(response.ok){
        const data = await response.json();
        editItem = data.item;
        console.log("The item is fetched")
    }

    document.getElementById("name").value = editItem?.name
    document.getElementById("description").value = editItem?.description
    document.getElementById("price").value = editItem?.price
    document.getElementById("mainImage").src = config.API_STATIC+editItem?.photos[0];
    const image_container = document.getElementById("image_container");
    let containerHTML = ``
    for(let photo of editItem?.photos){
        containerHTML+=`<div class="images-wrapper">
                        <img src="${config.API_STATIC+photo}" class="image2">
                        <div data-path=${photo} class="delete-images-icon" title="Smazat">×</div>
                    </div>`
    }
    image_container.innerHTML = containerHTML;



}
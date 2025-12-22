import {config} from "./config.js";

export const fetchProducts = async (page)=>{
    let products = []
    let totalPages = 1
    try{
        const response = await fetch(config.API_URL+`/product?page=${page}`,{
            headers:{
                "Content-Type":"application/json"
            }
        });
        if(response.ok){
            const data = await response.json()
            products = data.products.items;
            totalPages = data.products.totalPages;
        }
        else{
            console.log(response)
        }
    }
    catch (e){
        console.error(e)
    }

    let container = document.getElementById("product_container")
    let newHtml = ``

    products.forEach(product=>{
        newHtml+=`
            <tr>
                <td class="id-col">#${product.id}</td>
                <td class="name-col">${product.name}</td>
                <td>${product.category}</td>
                <td class="price-col">${product.price} Kč</td>
                <td>${product.location}</td>
                <td>${product.author}</td>
                <td class="actions-col">
                    <button class="action-btn view-btn"><a href="/Zasuvka/client/product.html?id=${product.id}"><i class="fa-regular fa-eye"></i></a></button>
                    <button data-id=${product.id} id="delete_product_button" class="action-btn delete-btn"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            </tr>
        `
    })
    container.innerHTML = newHtml;
    let pagination_block = document.getElementById("product_pagination_block");
    let newPaginationHTML = ``;
    for(let i=1;i<=totalPages;i++){
        if(i==page){
            newPaginationHTML+=`
                <button id="product-page-button" class="page-btn active">${i}</button>
            `
        }
        else{
            newPaginationHTML+=`
                <button id="product-page-button" class="page-btn">${i}</button>
            `
        }
    }
    pagination_block.innerHTML = newPaginationHTML;
    const product_pagination_buttons = document.querySelectorAll("#product-page-button")
    product_pagination_buttons.forEach(button=>{
        button.addEventListener("click",async()=>{
            console.log(button.innerText)
            await fetchProducts(button.innerText)
        })
    })

}

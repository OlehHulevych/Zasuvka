import {config} from "./config.js";
import {checkInProducts} from "./checkProductInFavorites.js";
import {addOrDeleteProductFromFavorites} from "./addProductToFavorites.js";


const contactButton = document.querySelector(".contact-button");
const contactInfo = document.querySelector(".contact-info")
const canselButton = document.querySelector(".cansel-button")

contactButton.addEventListener("click", function() {
    contactInfo.style.display = "flex"
    canselButton.style.display = "block"
    contactButton.style.display = "none"
} )

canselButton.addEventListener("click", function() {
    contactInfo.style.display = "none"
    canselButton.style.display = "none"
    contactButton.style.display = "block"
})




const parsingProduct = async (name,description,photos,price,userId, location) =>{
    document.getElementById("product_name").innerText = name;
    document.getElementById("product_description").innerText = description
    document.getElementById("product_price").innerText = price+"Kč";
    document.getElementById("location").innerText = location

    const response = await fetch(config.API_URL+`/user/id?id=${userId}`);
    if(response.ok){
        const data = await response.json();
        const user = data.user;
        document.getElementById("product_seller_name").innerText = user.name;
        document.getElementById("product_seller_phone").innerText = "Whatsapp "+user.phone
        document.getElementById("product_seller_photo").src = config.API_STATIC+user.photoPath;
    }

    let imageSelectorContainer = document.getElementById("image_container");
    let imageContainerHTML = ``
    console.log(photos)
    for(let photo of photos){
        imageContainerHTML+=`<button class="foto-button">
                            <img src="${config.API_STATIC}${photo}" alt="foto1" class="image2">
                        </button>`
    }

    imageSelectorContainer.innerHTML = imageContainerHTML;
    document.getElementById("main_photo").src=config.API_STATIC+photos[0];
}




document.addEventListener("DOMContentLoaded", async (e)=>{
    const newUrlParams = new URLSearchParams(window.location.search);
    const product_id = newUrlParams.get("id");
    const response = await fetch(config.API_URL+`/product/id?id=${product_id}`,{
        headers:{
            "Content-Type":"application/json"
        },
        method:"GET"
    });
    if(response.ok){
        const data = await response.json();
        await parsingProduct(data.item.name, data.item.description, data.item.photos, data.item.price,data.item.userId, data.item.location)
        const isInFavorites = await checkInProducts(data.item.id);
        if(isInFavorites){
            document.getElementById("favoriteCheckbox").checked = true;
        }

    }

    const mainImage = document.querySelector(".main-image");
    const images2 = document.querySelectorAll(".image2");

    images2.forEach(smallImg => {
        smallImg.addEventListener("click", function() {
            mainImage.src = smallImg.src;
        });
    });
    const favoriteCheckBox = document.getElementById("favoriteCheckbox");
    favoriteCheckBox.addEventListener("click", async ()=>{
        if(sessionStorage.getItem('user_id')==null){
            favoriteCheckBox.checke=false;
            window.location.href = "/Zasuvka/client/login.html"

        }
        else{
            if(!favoriteCheckBox.checked){
                await addOrDeleteProductFromFavorites(true, product_id)
            }
            else{
                await addOrDeleteProductFromFavorites(false,product_id)
            }
        }

    })
})
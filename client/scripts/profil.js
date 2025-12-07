import {config} from "./config.js"

const edit = document.querySelector(".edit");
const blockView = document.querySelector(".blockView");
const blockView2 = document.querySelector(".blockView2");
const cancel = document.querySelector(".cansel");


edit.addEventListener('click', () => {
    blockView.style.display = "none";
    blockView2.style.display = "block";
});

cancel.addEventListener('click', () => {
    blockView.style.display = "block";
    blockView2.style.display = "none";
});


document.addEventListener("DOMContentLoaded", ()=>{
    let name = document.getElementById("name")
    let email = document.getElementById("email");
    let phone = document.getElementById("phone");
    let avatar = document.getElementById("avatar");

    if(!sessionStorage.getItem("user_id")){
        window.location.href = "/Zasuvka/client/index.html"
    }
    name.value = sessionStorage.getItem("user_name")
    email.value = sessionStorage.getItem("user_email")
    phone.value = sessionStorage.getItem("user_phone")
    avatar.src = config.API_STATIC + sessionStorage.getItem("photoPath")
})
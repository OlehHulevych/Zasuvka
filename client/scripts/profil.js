import {config} from "./config.js"
import {logout} from "./logout.js";

const edit = document.querySelector(".edit");
const blockView = document.querySelector(".blockView");
const blockView2 = document.querySelector(".blockView2");
const cancel = document.querySelector(".cansel");
const logout_button = document.getElementById("logout_button")


edit.addEventListener('click', () => {
    blockView.style.display = "none";
    blockView2.style.display = "block";
});

cancel.addEventListener('click', () => {
    blockView.style.display = "block";
    blockView2.style.display = "none";
});

logout_button.addEventListener('click',async (e)=>{
    e.preventDefault();
    await logout();
})




document.addEventListener("DOMContentLoaded", ()=>{

    let name2 = document.querySelector(".name");
    let email = document.querySelector("#email");
    let phone = document.querySelector("#phone");
    let avatar = document.querySelector(".avatar#avatar");

    if(!sessionStorage.getItem("user_id")){
        window.location.href = "/~hulevole/Zasuvka/client/login.html"
    }

    name2.textContent = sessionStorage.getItem("user_name")
    email.value = sessionStorage.getItem("user_email")
    phone.value = sessionStorage.getItem("user_phone")
    avatar.src = config.API_STATIC + sessionStorage.getItem("photoPath")

    let updateForm = document.getElementById("update_form")
    updateForm.addEventListener("submit", async (e)=>{
        e.preventDefault()
        const formData = new FormData(updateForm)
        for (const [key, value] of [...formData.entries()]) {
            // 1. Handle String Inputs (Trim and check for empty)
            if (typeof value === 'string') {
                if (value.trim() === "") {
                    formData.delete(key);
                }
            }
            // 2. Handle File Inputs (Check if size is 0)
            else if (value instanceof File) {
                if (value.size === 0) {
                    formData.delete(key);
                }
            }
        }



        console.log(formData)



        const response = await fetch(config.API_URL+"/user/update",{
            method:"POST",
            body:formData,
            credentials:"include"
        })
        if(response.ok){
            const result = await response.json()
            console.log(result)
            window.location.href = "/~hulevole/Zasuvka/client"
        }
        else{
            console.log(response)
        }
    })
})
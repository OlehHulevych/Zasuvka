import {config} from "./config.js";

document.addEventListener("DOMContentLoaded",()=>{
    if(sessionStorage.getItem('user_id')==null){
        window.location.href = "/~hulevole/Zasuvka/client/login.html"
    }
})
const warning_block = document.querySelector("div.warning_block")
const addForm = document.getElementById("Add_Product_Form")
addForm.addEventListener('submit', async(e)=>{
    e.preventDefault();
    const newFormData = new FormData(addForm);
    console.log(newFormData)
        const response = await fetch(config.API_URL + "/product", {
            method:"POST",
            body:newFormData,
            credentials:"include"
        })
        if(response.ok){
            const data = await response.json();
            console.log(data);
            window.location.href = "/~hulevole/Zasuvka/client/"
        }
        else{
            console.log(response)
        }



})
import {config} from "./config.js";

document.addEventListener("DOMContentLoaded",(e)=>{
    e.preventDefault()
    const reg_form = document.getElementById("registration-form")
    const warning_block = document.querySelector(".warning_block")
    reg_form.addEventListener("submit", async(e)=>{
        e.preventDefault();
        const formData  = new FormData(reg_form);
        const data = Object.fromEntries(formData)
        console.log(data);
        if(!formData.name || !formData.email || !formData.phone || !formData.password || !formData){
            warning_block.classList.add("active")
            warning_block.innerHTML = "Folmulář neni vyplněný"
        }
        if(!formData.checkbox !== "on"){
            warning_block.classList.add("active")
            warning_block.innerHTML = "Nesouhlasili jste s našimi podminkami"
        }

        console.log(data)
        try{
            const response = await fetch(config.API_URL + "/user/register", {
                method:'POST',
                body:formData,
                credentials:"include"
            })
            if(response.ok){
                warning_block.classList.remove("active")
                const result = await response.json();
                console.log('Success', result);
                window.location.href = "/Zasuvka/Client/login.html"
            }
            else{
                const result = await response.json();
                console.log(result)
            }
        }
        catch (error){
            console.log(error)
        }

    })
})
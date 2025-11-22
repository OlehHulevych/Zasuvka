import {config} from "./config.js";

document.addEventListener("DOMContentLoaded",(e)=>{
    e.preventDefault()
    const reg_form = document.getElementById("registration-form")
    const warning_block = document.querySelector(".warning_block")
    reg_form.addEventListener("submit", async(e)=>{
        e.preventDefault();
        const formData  = new FormData(reg_form);
        if(!formData.name || !formData.email || !formData.phone || !formData.password || !formData){
            warning_block.classList.add("active")
            warning_block.innerHTML = "Folmulář neni vyplněný"
        }
        if(!formData.checkbox.checked){
            warning_block.classList.add("active")
            warning_block.innerHTML = "Nesouhlasili jste s našimi podminkami"
        }
        const data = Object.fromEntries(formData)
        console.log(data)
        try{
            const response = await fetch(config.API_URL + "/user/registration", {
                method:'POST',
                body:JSON.stringify(data)
            })
            if(response.ok){
                warning_block.classList.remove("active")
                const result = await response.json();
                console.log('Success', result);
            }
            else{
                console.error('Server Error', response.status)
            }
        }
        catch (error){
            console.error("Network Error", error)
        }

    })
})
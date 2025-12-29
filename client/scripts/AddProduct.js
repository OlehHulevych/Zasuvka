import {config} from "./config.js";
import {validateString} from "./Validation";

document.addEventListener("DOMContentLoaded",()=>{
    if(sessionStorage.getItem('user_id')==null){
        window.location.href = "/~hulevole/Zasuvka/client/login.html"
    }
})
const warning_block = document.querySelector(".warning_block")
const addForm = document.getElementById("Add_Product_Form")
addForm.addEventListener('submit', async(e)=>{
    e.preventDefault();
    const newFormData = new FormData(addForm);
    let check = false;
    if(!check) {
        for (const [key, value] of newFormData) {
            let validation = validateString(value);
            console.log(validation)
            if (value === "" || value.name === "" || value.size === 0 || validation) {
                document.getElementById(key).classList.add("input-invalid")
                let newKey = ""
                switch (key) {
                    case "name":
                        newKey = "Jmeno"
                        break;
                    case "description":
                        newKey = "popis"
                        break
                    case "category":
                        newKey = "kategorie"
                        break;
                    case "password":
                        newKey = "Heslo";
                        break
                    case "photos[]":
                        newKey = "Obrázky"
                        break;
                }
                if (validation) {
                    warning_block.classList.add("active")
                    warning_block.innerText = " " + newKey + " má špatný format"
                    check =false;
                    break;
                } else {
                    warning_block.classList.add("active")
                    warning_block.innerText += " " + newKey + " "
                    check = false
                    break;
                }


            } else {
                check = true;
            }

        }
    }
        console.log(newFormData)
    if(check){
        const response = await fetch(config.API_URL + "/product", {
            method: "POST",
            body: newFormData,
            credentials: "include"
        })
        if (response.ok) {
            const data = await response.json();
            console.log(data);
            window.location.href = "/~hulevole/Zasuvka/client/"
        } else {
            const data = await response.json();
            warning_block.classList.add("active")
            warning_block.innerText = data.message;
        }
    }




})
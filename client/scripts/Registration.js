import {config} from "./config.js";

document.addEventListener("DOMContentLoaded",(e)=>{
    e.preventDefault()
    const reg_form = document.getElementById("registration-form")
    const warning_block = document.querySelector(".warning_block")
    reg_form.addEventListener("submit", async(e)=>{
        e.preventDefault();
        const formData  = new FormData(reg_form);
        const data = Object.fromEntries(formData)
        let check = false
        let condition = false
        console.log(data);
        if(!check){
            for(const[key,value] of formData){
                if (value===""||value.name === "" || value.size === 0) {
                    document.getElementById(key).classList.add("input-invalid")
                    let newKey=""
                    switch (key){
                        case "name":
                            newKey = "jmeno"
                            break;
                        case "email":
                            newKey = "email"
                            break
                        case "phone":
                            newKey = "čislo telefonu"
                            break;
                        case "password":
                            newKey = "heslo";
                            break
                        case "photo":
                            newKey = "obrázek"
                            break;
                    }
                    warning_block.classList.add("active")
                    warning_block.innerText+=" "+newKey+" "

                }
                else{
                    check = true;
                }

            }


        }

        console.log(condition)
        if(check){
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
                    window.location.href = "/~hulevole/Zasuvka/client/login.html"
                }
                else{
                    const result = await response.json();
                    warning_block.innerText = result.message;
                }
            }
            catch (error){
                console.log(error)
            }
        }


    })
})
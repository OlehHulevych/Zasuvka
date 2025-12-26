import {config} from "./config.js";

document.addEventListener("DOMContentLoaded", (e)=>{
    e.preventDefault();
    const loginForm = document.getElementById("login_form");
    loginForm.addEventListener("submit", async(e)=>{
        e.preventDefault();
        const formData = new FormData(loginForm)
        let warning_block =  document.querySelector(".warning_block");
        let check = false
        if(!check){
            for(const[key,value] of formData){
                if (value===""||value.name === "" || value.size === 0) {
                    document.getElementById(key).classList.add("input-invalid")
                    let newKey=""
                    switch (key){
                        case "email":
                            newKey = "email"
                            break
                        case "password":
                            newKey = "heslo";
                            break

                    }
                    warning_block.classList.add("active")
                    warning_block.innerText+=" "+newKey+" "

                }
                else{
                    check = true;
                }

            }


        }
        if(check){
            try{
                const response = await fetch(config.API_URL+"/user/login",{
                    method:"POST",
                    body:formData,
                    credentials:"include"
                })
                if(response.ok){
                    const data = await response.json();
                    console.log(data)
                    const userId = data.user_id;
                    sessionStorage.setItem("user_id", userId);
                    console.log(sessionStorage.getItem("user_id"));
                    window.location.href = "/~hulevole/Zasuvka/client"
                }
                else{
                    const data = await response.json();
                    console.log(data.message);
                    document.querySelector(".warning_block").classList.add("active")
                    document.querySelector(".warning_block").innerText = data.message;
                }
            }
            catch (error){
                console.log(error);
            }
        }


    })
})
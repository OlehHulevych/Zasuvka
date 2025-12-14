import {config} from "./config.js";

document.addEventListener("DOMContentLoaded", (e)=>{
    e.preventDefault();
    const loginForm = document.getElementById("login_form");
    loginForm.addEventListener("submit", async(e)=>{
        e.preventDefault();
        const formData = new FormData(loginForm)
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
                window.location.href = "/Zasuvka/client"
            }
            else{
                const data = await response.json();
                console.log(data.message);
                document.querySelector(".warning").classList.add("active")
            }
        }
        catch (error){
            console.log(error);
        }

    })
})
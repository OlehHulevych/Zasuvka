import {config} from "./config";

document.addEventListener("DOMContentLoaded",async (e)=>{
    e.preventDefault()
    if(sessionStorage.getItem('user_id')==null){
        window.location.href = "/~hulevole/Zasuvka/client/login.html"
    }
    const response = await fetch(config.API_URL+"/favoriteItem",{
        method:"GET",
        credentials:"include"
    })
    if(response.ok){
        const data = await response.json();
    }
    else{
        console.log(response)
    }
})
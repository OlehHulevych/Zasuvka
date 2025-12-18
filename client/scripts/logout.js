import {config} from "./config.js";

export const logout = async () =>{
    const response = await fetch(config.API_URL+"/user/logout",{
        headers:{
            "Content-Type":"application/json",
        },
        credentials:"include"
    })
    if(response.ok){
        const data = await response.json();
        console.log(data);
        sessionStorage.clear();
        window.location.href="/Zasuvka/client/"

    }
    else{
        console.error(response)
    }
}
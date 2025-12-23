import {config} from "./config.js";

export const checkInProducts = async(productId) =>{
if(sessionStorage.getItem("user_id")!=null){
    const response =  await fetch(config.API_URL+"/favorites",{
        headers:{
            "Content-Type":"application/json"
        },
        method:"GET",
        credentials:"include"
    });
    if(response.ok){
        const data = await response.json();
        const favoriteList = data.list.items;
        for(let item of Object.values(favoriteList)){
            if(item.productId === productId){
                return true;
            }
        }
    }
    }
else{
    return false;
}


}
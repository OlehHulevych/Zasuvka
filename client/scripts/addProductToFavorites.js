import {config} from "./config.js";

export const addOrDeleteProductFromFavorites = async (condition, id) =>{
        const response = await fetch(config.API_URL+`/favoriteItem?id=${id}`,{
            headers:{
                "Content-Type":"application/json"
            },
            method:condition?"DELETE":"POST",
            credentials:"include"
        });
        if(response.ok){
            console.log("The item was added of deleted")
        }

}
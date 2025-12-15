import {config} from "./config.js";

export  const deleteMyProduct = async (id) =>{
    console.log(id)
    const response = await fetch(config.API_URL + `/product?id=${id}`,{
        headers:{
            "Content-Type":"application/json"
        },
        method:"DELETE",
        credentials:"include"
    })
    if(response.ok){
        console.log("The item was deleted")
    }
    else{
        console.log(response)
        console.log("Something went wrong")
    }
}
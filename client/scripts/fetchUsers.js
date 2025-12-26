import {config} from "./config.js";

export const fetchUsers = async (page)=>{
    let products = []
    let totalPages = 1
    try{
        const response = await fetch(config.API_URL+`/admin/user?page=${page}`,{
            headers:{
                "Content-Type":"application/json"
            }
        });
        if(response.ok){
            const data = await response.json()
            products = data.users.items;
            totalPages = data.users.totalPages;
        }
        else{
            console.log(response)
        }
    }
    catch (e){
        console.error(e)
    }

    let container = document.getElementById("user_container")
    let newHtml = ``

    products.forEach(user=>{
        newHtml+=`
            <tr>
                <td class="id-col">#${user.id}</td>
                <td class="name-col">${user.name}</td>
                <td>${user.email}</td>
                <td><span class="role-badge user">${user.role}</span></td>
                <td class="actions-col">
                    <button data-id=${user.id} id="user_promote_button" class="action-btn promote-btn" title="Povýšit na Admina"><i class="fa-solid fa-user-shield"></i></button>
                    <button data-id=${user.id} id="user_delete_button" class="action-btn ban-btn" title="Zabanovat"><i class="fa-solid fa-ban"></i></button>
                </td>
            </tr>
            <tr>
        `
    })
    container.innerHTML = newHtml;
    let pagination_block = document.getElementById("user_pagination_block");
    let newPaginationHTML = ``;
    for(let i=1;i<=totalPages;i++){
        if(i==page){
            newPaginationHTML+=`
                <button id="user-page-button" class="page-btn active">${i}</button>
            `
        }
        else{
            newPaginationHTML+=`
                <button id="user-page-button" class="page-btn">${i}</button>
            `
        }
    }
    pagination_block.innerHTML = newPaginationHTML;
    const user_promote_buttons = document.querySelectorAll("#user_promote_button");
    user_promote_buttons.forEach(button=>{
        button.addEventListener("click", async()=>{
            const id = button.dataset.id;
            try{
                const response = await fetch(config.API_URL+`/admin/user/promote?id=${id}`, {
                    headers:{
                        "Content-Type":"application/json"
                    },
                    method:"GET",
                    credentials:"include"
                })
                if(response.ok){
                    const data = await response.json();
                    console.log(data);
                }
                else{
                    console.log(response)
                }
            }
            catch (e){
                console.error(e);
            }
        })
    })
    const product_pagination_buttons = document.querySelectorAll("#user-page-button")
    product_pagination_buttons.forEach(button=>{
        button.addEventListener("click",async()=>{
            console.log(button.innerText)
            await fetchUsers(button.innerText)
        })
    })

}

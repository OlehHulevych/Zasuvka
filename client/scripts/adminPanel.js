import {config} from "./config.js";
import {fetchProducts} from "./fetchProducts.js";

document.addEventListener('DOMContentLoaded', async () => {

    if(sessionStorage.getItem("user_id")==null){
        window.location.href = "/Zasuvka/client/login.html"
    }
    let user = null
    try{
        const response = await fetch(config.API_URL+"/user/authorize",{
            headers:{
                "Content-Type":"application/json"
            },
            method:"GET"
        })
        if(response.ok){
            const data = await response.json();
            user = data.user;
        }
        else{
            console.log(response)
        }
    }
    catch (e){
        console.error(e)
    }

    if(user?.role!=="admin"){
        window.location.href = "/Zasuvka/client/login.html"
    }

    await fetchProducts(1);

    const delete_product_buttons = document.querySelectorAll("#delete_product_button");
    delete_product_buttons.forEach(button=>{
        button.addEventListener("click", async(e)=>{
            const id = button.dataset.id;
            try{
                const response = await fetch(config.API_URL+`/product?id=${id}`,{
                    headers:{
                        "Content-Type":"application/json"
                    },
                    method:"DELETE"
                })
                if(response.ok){
                    const data = await response.json();
                    console.log(data);
                }
                else{
                    console.log(response)
                }
            }
            catch (error){
                console.log(error)
            }
        })
    })

    
    // клики на кнопки
    const btnAds = document.getElementById('btn-ads');
    const btnUsers = document.getElementById('btn-users');
    const adsSection = document.getElementById('ads-section');
    const usersSection = document.getElementById('users-section');
    
    // Клик на Inzeráty
    btnAds.addEventListener('click', () => {
        
        btnAds.classList.add('active');
        btnUsers.classList.remove('active');
        
        adsSection.style.display = 'block';
        usersSection.style.display = 'none';
    });
    
    // Клик на uživatele
    btnUsers.addEventListener('click', () => {
        btnUsers.classList.add('active');
        btnAds.classList.remove('active');
        
        usersSection.style.display = 'block';
        adsSection.style.display = 'none';
    });


    // счетчик обьявлений
    function updateCounter() {
        const adsRows = document.querySelectorAll('#ads-section tbody tr');
        const adsSubtitle = document.getElementById('ads-subtitle');
        if(adsSubtitle) adsSubtitle.textContent = adsRows.length + ' inzerátů';

        const usersRows = document.querySelectorAll('#users-section tbody tr');
        const usersSubtitle = document.getElementById('users-subtitle');
        if(usersSubtitle) usersSubtitle.textContent = usersRows.length + ' uživatelů';
    }

    updateCounter();

    // удаление обьявлегия  
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            row.remove();
            updateCounter();
        });
    });

    const banButtons = document.querySelectorAll('.ban-btn');
    banButtons.forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const icon = button.querySelector('i');
            
            row.classList.toggle('banned');

            if (row.classList.contains('banned')) {
                icon.className = 'fa-solid fa-lock-open';
                button.style.color = '#6b7280';
            } else {
                icon.className = 'fa-solid fa-ban';
                button.style.color = '';
            }
        });
    });
})
//mobile search bar functionality
let mobileSearchBarButton = document.getElementById('mobile_search_button');
mobileSearchBarButton.addEventListener('click', ()=>{
    let mobileSearchBar = document.getElementById('mobile_search_bar')
    mobileSearchBar.classList.add('active')
})

document.getElementById('cancel_button').addEventListener('click',(e)=>{
    e.preventDefault();
    let mobileSearchBar = document.getElementById('mobile_search_bar')
    mobileSearchBar.classList.remove('active')
})
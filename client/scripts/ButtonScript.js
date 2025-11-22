const upload_button = document.getElementById("photo")
const custom_button = document.querySelector(".custom_button")

upload_button.addEventListener("change", ()=>{
    if(upload_button.value){
        custom_button.innerHTML = upload_button.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
    }else{
        custom_button.innerHTML = "Zvolte Foto"
    }
})
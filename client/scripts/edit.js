import {editFetch} from "./editFetch.js";

document.addEventListener("DOMContentLoaded", async()=>{
    let deletedImages = [];
    const newUrlParams = new URLSearchParams(window.location.search);
    const product_id = newUrlParams.get("id");
    await editFetch(product_id);

    const deleteImage = document.querySelectorAll('.delete-images-icon');

    const imagesContainer = document.getElementById("image_container")
    const addPhotoInput = document.getElementById("newPhoto")

    const btnChangeMain = document.getElementById("btnChangeMain")
    const mainPhotoInput = document.getElementById("newPhoto")

    btnChangeMain.addEventListener("click", function() {
        mainPhotoInput.click()
    })


// при загрузке главной фотки она заменяет старую
    mainPhotoInput.addEventListener('change', function() {
        if (this.files[0]) {
            mainImage.src = URL.createObjectURL(this.files[0]);
        }
    });

// при нажатии кнопки добавления второстепенных фото откроется проводник
    const btnAddPhoto = document.getElementById("btnChangeMain")

    btnAddPhoto.addEventListener("click", function() {
        addPhotoInput.click()
    })

    addPhotoInput.addEventListener('change', function() {
        for(let photo of this.files){
            const src = URL.createObjectURL(photo);

            const newContainer = document.createElement('div');
            newContainer.classList.add('images-wrapper');
            newContainer.innerHTML = `
            <img src="${src}" class="image2">
            <div class="delete-images-icon" data-path = ${src} data-name = ${photo.name} title="Smazat">×</div>
        `;

            imagesContainer.appendChild(newContainer);

        }

    });

    imagesContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-images-icon')) {
            const wrapper = e.target.closest('.images-wrapper');
            wrapper.classList.add('remove');

            if (e.target.dataset.path) {
                const dt = new DataTransfer();
                let path = e.target.dataset.path
                if(path.startsWith("blob")){
                    let files = addPhotoInput.files;
                    const fileName = e.target.dataset.name;
                    for(let file of files){
                        if(file.name !== fileName){
                            dt.items.add(file)
                        }
                    }
                    addPhotoInput.files = dt.files;
                    console.log("added Image was deleted")
                }
                else{
                    deletedImages.push(e.target.dataset.path);
                }

            }

            console.log(deletedImages);
        }
    });

    let editForm = document.getElementById("edit_form")
    editForm.addEventListener("submit",async(e)=>{
        e.preventDefault();
        let newFormData=  new FormData(editForm);
        newFormData.append("deletePhotos",deletedImages);
        console.log(newFormData);
    })


})




// при нажатии на zpet возвращает назад
const backButton = document.querySelector(".back-button")

backButton.addEventListener("click", function() {
    window.history.back()
})




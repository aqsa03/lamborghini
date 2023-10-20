//import './bootstrap';

import Alpine from 'alpinejs';
import 'flowbite';

window.Alpine = Alpine;

Alpine.start();


document.addEventListener("DOMContentLoaded", ev => {
    const deleteLink = document.querySelectorAll(".delete-link");
    deleteLink.forEach(link => {
        const message = link.dataset.deleteMessage ?? "Sei sicuro?";
        link.addEventListener("click", ev => {
            ev.preventDefault();
            const confirmStatus = confirm(message);
            if (confirmStatus === true) {
                return link.closest("form").submit();
            }
            return false;
        });
    });
})

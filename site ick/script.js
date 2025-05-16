const shoes = [
    { id: 1, image: "image/1.png" },
    { id: 2, image: "image/2.png" },
    { id: 3, image: "image/3.png" },
    { id: 4, image: "image/4.png" }
];

const mainImage = document.querySelector('.shoe-image');
const icons = document.querySelectorAll('.icon');

function updateImage(shoeId) {
    const selectedShoe = shoes.find(shoe => shoe.id === shoeId);
    if (!selectedShoe) return; // sécurité

    mainImage.src = selectedShoe.image;

    icons.forEach(icon => icon.classList.remove('active'));

    // bonne interpolation avec backticks
    const activeIcon = document.querySelector(`[data-shoe="${shoeId}"]`);
    if (activeIcon) activeIcon.classList.add('active');
}

icons.forEach(icon => {
    icon.addEventListener('click', () => {
        const shoeId = parseInt(icon.getAttribute('data-shoe'), 10);
        updateImage(shoeId);
    });
});

// charge l’image 1 au démarrage
updateImage(1);

const mainImage = document.querySelector('.shoe-image');
const mainProductName = document.querySelector('.main-product-name');
const icons = document.querySelectorAll('.icon');

function updateMainDisplay(icon) {
    const imageSrc = icon.querySelector('img').getAttribute('src');
    const productName = icon.getAttribute('data-name');
    mainImage.src = imageSrc;
    mainProductName.textContent = productName;

    icons.forEach(ic => ic.classList.remove('active'));
    icon.classList.add('active');
}

icons.forEach(icon => {
    icon.addEventListener('click', () => {
        updateMainDisplay(icon);
    });
});

// Optionnel : initialise l'affichage au chargement
if (icons.length > 0) {
    updateMainDisplay(icons[0]);
}
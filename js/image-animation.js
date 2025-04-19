document.addEventListener("DOMContentLoaded", function() {
    const changingImage = document.querySelector('.changing-image');
    if (changingImage) {
        const availableImages = JSON.parse(changingImage.dataset.images || '[]');
        if (availableImages.length > 0) {
            setInterval(function() {
                const currentSrc = changingImage.src;
                let newImage;
                do {
                    newImage = availableImages[Math.floor(Math.random() * availableImages.length)];
                } while (newImage === currentSrc);

                changingImage.style.opacity = '0';
                setTimeout(function() {
                    changingImage.src = newImage;
                    changingImage.style.opacity = '1';
                }, 500);
            }, 7000);
        }
    }
});
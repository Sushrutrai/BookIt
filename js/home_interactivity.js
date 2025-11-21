const hero = document.getElementById('hero');

// Array of images
const images = [
     "../Images/nepathya.jpg",
    "../Images/theedgeband.jpg",
    "../Images/monkeyTemple.jpg",
    "../Images/sabinrai.jpg",
    "../Images/rockhead.jpg",
    "../Images/Albatross.jpg"
];

let index = 0;

// Set initial background
hero.style.backgroundImage = `url("${images[index]}")`;

// RIGHT BUTTON – Next image
document.getElementById('right').addEventListener("click", () => {
    index = (index + 1) % images.length; // loops back to first image
    hero.style.backgroundImage = `url("${images[index]}")`;
});

// LEFT BUTTON – Previous image
document.getElementById('left').addEventListener("click", () => {
    index = (index - 1 + images.length) % images.length; // loops back to last image
    hero.style.backgroundImage = `url("${images[index]}")`;
});

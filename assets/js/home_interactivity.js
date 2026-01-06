const hero = document.getElementById('hero');

// Array of images
const images = [
     "../../assets/images/nepathya.jpg",
    "../assets/images/theedgeband.jpg",
    "../assets/images/monkeyTemple.jpg",
    "../assets/images/sabinrai.jpg",
    "../assets/images/rockhead.jpg",
    "../assets/images/Albatross.jpg","../assets/images/Hero_Image_Temp3.jpg"
];

let index = 0;


hero.style.backgroundImage = `url("${images[index]}")`;

// RIGHT BUTTON – Next image
document.getElementById('right').addEventListener("click", () => {
    index = (index + 1) % images.length; 
    hero.style.backgroundImage = `url("${images[index]}")`;
});

// LEFT BUTTON – Previous image
document.getElementById('left').addEventListener("click", () => {
    index = (index - 1 + images.length) % images.length; 
    hero.style.backgroundImage = `url("${images[index]}")`;
});


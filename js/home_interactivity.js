const hero = document.getElementById('hero');

// Image list
const images = [
    "../Images/nepathya.jpg",
    "../Images/theedgeband.jpg",
    "../Images/monkeyTemple.jpg",
    "../Images/sabinrai.jpg",
    "../Images/rockhead.jpg",
    "../Images/Albatross.jpg","../Images/Hero_Image_Temp3.jpg"
];

// Event date + location
const eventDetails = [
    { date: "2025-01-10", location: "Kathmandu" },
    { date: "2025-01-12", location: "Pokhara" },
    { date: "2025-01-15", location: "Butwal" },
    { date: "2025-01-18", location: "Biratnagar" },
    { date: "2025-01-20", location: "Dharan" },
    { date: "2025-01-22", location: "Janakpur" }
];

let index = 0;

function updateHero() {
    hero.style.backgroundImage = `url("${images[index]}")`;
    document.getElementById("eventDetails").innerHTML =
        `📅 ${eventDetails[index].date} &nbsp; | &nbsp; 📍 ${eventDetails[index].location}`;
}

// Initial load
updateHero();

// RIGHT Button
document.getElementById('right').addEventListener("click", () => {
    index = (index + 1) % images.length;
    updateHero();
});

// LEFT Button
document.getElementById('left').addEventListener("click", () => {
    index = (index - 1 + images.length) % images.length;
    updateHero();
});

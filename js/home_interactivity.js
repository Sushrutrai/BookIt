const hero=document.getElementById('hero');
const content=document.getElementById('content');

console.log("Js is active");

document.getElementById('left').addEventListener("click", () => {
    hero.style.backgroundImage = 'url("../Images/Hero_Image_Temp4.jpg")';
    //content.innerHTML='<h1 class="bigger_text">This is albatross!</h1>'
    
});
document.getElementById('right').addEventListener("click", () => {
    hero.style.backgroundImage = 'url("../Images/Hero_Image_Temp2.jpg")';
    //content.innerHTML='<h1 class="bigger_text">This is Nepathya!</h1>'
   
});
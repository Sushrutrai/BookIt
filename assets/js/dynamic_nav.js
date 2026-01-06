const navbar=document.querySelector('.navigation');
const menu=document.querySelector('.svg');


menu.addEventListener('click',()=>{
    navbar.classList.toggle('active')
    menu.classList.toggle('active')
    
})


document.addEventListener('click',(e)=>{
    if(!menu.contains(e.target)&& !navbar.contains(e.target)){
    navbar.classList.remove('active')
    }
})
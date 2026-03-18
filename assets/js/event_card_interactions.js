const favourite=document.querySelector('.favourite');
favourite.addEventListener('click',()=>{
    favourite.classList.toggle('is_active')
    console.log('click')
    if(favourite.classList.contains('is_active')){
        alert('Added to favourite')
    }else{
        alert('Removed from favourite')
    }
})  
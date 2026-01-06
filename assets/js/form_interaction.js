function showForm(formid) {
    document.querySelectorAll(".form-box").forEach(form=>form.classList.remove("active"));
    document.getElementById(formid).classList.add("active");
}
const password=document.getElementById('register-password')
const password_strength=document.getElementById('password_strength')
let strength="";
function checkPasswordStrength(){
    let len = password.value.length;
    if(len===0){
        password_strength.style.color="";
        password_strength.textContent="";
        return;
    }else{
        if(len>=10){
            strength="Strong";
            password_strength.style.color="green"
        }else if(len>=5){
            strength="Medium";
            password_strength.style.color="orange"
        }else if(len<5){
            strength="Weak";
            password_strength.style.color="red";
        }
          password_strength.textContent=strength;
    }
}
  
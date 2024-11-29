let password_holder=document.getElementById("password_holder")
let button = document.getElementById("button")
let message = document.getElementById("message")

message.innerText='';
message.classList.remove("error_msg","success_msg")



document.getElementById("cpword").addEventListener('input', validatePasswords);

function validatePasswords(){
   const pword = document.getElementById("pword")
const c_pword=document.getElementById("cpword")

message.innerText = '';
message.classList.remove("error_msg", "success_msg");

if (pword.value !== c_pword.value){
message.innerText="Passwords do not match";
message.classList.add("error_msg");
return false
}
else{
message.innerText="password matched sucessfully";
message.classList.add("success_msg")
return true
}

}

    

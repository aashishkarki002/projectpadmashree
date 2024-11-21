let password_holder=document.getElementById("password_holder")
let button = document.getElementById("button")
let message = document.getElementById("message")
message.classList.remove("error_msg","sucess_msg")
button.addEventListener("click", authenticate);
function authenticate(){
    let pword = document.getElementById("pword").value
let c_pword=document.getElementById("c_pword").value

if(pword!=c_pword){
message.innerText="Passwords do not match"
message.classList.add("error_msg")
return false
}
if(pword.value.lenght<8){
    message.innerText = "Password must be at least 8 characters long!";
    message.classList.add("error_msg");
    return false;


}
message.innerText="Password matched successfully"
message.classList.add("success_msg")
}
document.getElementById("cpword").addEventListener('input', validatePasswords);
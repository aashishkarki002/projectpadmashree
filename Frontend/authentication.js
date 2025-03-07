document.addEventListener('DOMContentLoaded', function() {
  const firstNameInput = document.querySelector('.firstname');
  const lastNameInput = document.querySelector('.lastname');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('c_password');
  const emailMsg = document.getElementById('email__msg');
  const pwdMsg = document.getElementById('msg');
  const pwdValidate = document.getElementById('validate');
  const errorMsg = document.getElementById('message');
  const submitButton = document.getElementById('button');
  const form = document.querySelector('form');

  // Create message elements for name fields
  const firstNameMsg = document.createElement('div');
  firstNameMsg.id = 'firstName__msg';
  firstNameInput.parentNode.insertAdjacentElement('afterend', firstNameMsg);

  const lastNameMsg = document.createElement('div');
  lastNameMsg.id = 'lastName__msg';
  lastNameInput.parentNode.insertAdjacentElement('afterend', lastNameMsg);

  // First Name validation
  firstNameInput.addEventListener('blur', function() {
      const firstName = firstNameInput.value.trim();
      const nameRegex = /^[A-Za-z]{2,30}$/;

      if (firstName === '') {
          firstNameMsg.textContent = 'First name is required';
          firstNameMsg.style.color = 'red';
          firstNameInput.style.borderColor = 'red';
          return false;
      } else if (!nameRegex.test(firstName)) {
          firstNameMsg.textContent = 'Name should only contain letters (2-30 characters)';
          firstNameMsg.style.color = 'red';
          firstNameInput.style.borderColor = 'red';
          return false;
      } else {
          firstNameMsg.textContent = 'Valid name';
          firstNameMsg.style.color = 'green';
          firstNameInput.style.borderColor = 'green';
          return true;
      }
  });

  // Last Name validation
  lastNameInput.addEventListener('blur', function() {
      const lastName = lastNameInput.value.trim();
      const nameRegex = /^[A-Za-z]{2,30}$/;

      if (lastName === '') {
          lastNameMsg.textContent = 'Last name is required';
          lastNameMsg.style.color = 'red';
          lastNameInput.style.borderColor = 'red';
          return false;
      } else if (!nameRegex.test(lastName)) {
          lastNameMsg.textContent = 'Name should only contain letters (2-30 characters)';
          lastNameMsg.style.color = 'red';
          lastNameInput.style.borderColor = 'red';
          return false;
      } else {
          lastNameMsg.textContent = 'Valid name';
          lastNameMsg.style.color = 'green';
          lastNameInput.style.borderColor = 'green';
          return true;
      }
  });

  // Email validation
  emailInput.addEventListener('blur', function() {
      const email = emailInput.value.trim();
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

      if (email === '') {
          emailMsg.textContent = 'Email is required';
          emailMsg.style.color = 'red';
          emailInput.style.borderColor = 'red';
          return false;
      } else if (!emailRegex.test(email)) {
          emailMsg.textContent = 'Please enter a valid email address';
          emailMsg.style.color = 'red';
          emailInput.style.borderColor = 'red';
          return false;
      } else {
          emailMsg.textContent = 'Valid email';
          emailMsg.style.color = 'green';
          emailInput.style.borderColor = 'green';
          return true;
      }
  });

  // Password validation
  passwordInput.addEventListener('input', function() {
      const password = passwordInput.value;
      
      // Clear previous validation messages
      pwdValidate.innerHTML = '';
      
      // Create validation list
      const validationList = document.createElement('ul');
      validationList.style.listStyleType = 'none';
      validationList.style.padding = '0';
      validationList.style.margin = '5px 0';
      
      // Check minimum length
      const lengthItem = document.createElement('li');
      if (password.length >= 8) {
          lengthItem.textContent = '✓ At least 8 characters';
          lengthItem.style.color = 'green';
      } else {
          lengthItem.textContent = '✗ At least 8 characters';
          lengthItem.style.color = 'red';
      }
      validationList.appendChild(lengthItem);
      
      // Check uppercase letter
      const uppercaseItem = document.createElement('li');
      if (/[A-Z]/.test(password)) {
          uppercaseItem.textContent = '✓ At least one uppercase letter';
          uppercaseItem.style.color = 'green';
      } else {
          uppercaseItem.textContent = '✗ At least one uppercase letter';
          uppercaseItem.style.color = 'red';
      }
      validationList.appendChild(uppercaseItem);
      
      // Check lowercase letter
      const lowercaseItem = document.createElement('li');
      if (/[a-z]/.test(password)) {
          lowercaseItem.textContent = '✓ At least one lowercase letter';
          lowercaseItem.style.color = 'green';
      } else {
          lowercaseItem.textContent = '✗ At least one lowercase letter';
          lowercaseItem.style.color = 'red';
      }
      validationList.appendChild(lowercaseItem);
      
      // Check number
      const numberItem = document.createElement('li');
      if (/\d/.test(password)) {
          numberItem.textContent = '✓ At least one number';
          numberItem.style.color = 'green';
      } else {
          numberItem.textContent = '✗ At least one number';
          numberItem.style.color = 'red';
      }
      validationList.appendChild(numberItem);
      
      // Check special character
      const specialItem = document.createElement('li');
      if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
          specialItem.textContent = '✓ At least one special character';
          specialItem.style.color = 'green';
      } else {
          specialItem.textContent = '✗ At least one special character';
          specialItem.style.color = 'red';
      }
      validationList.appendChild(specialItem);
      
      // Add the validation list to the page
      pwdValidate.appendChild(validationList);
      
      // Update password message
      if (password.length >= 8 && 
          /[A-Z]/.test(password) && 
          /[a-z]/.test(password) && 
          /\d/.test(password) && 
          /[!@#$%^&*(),.?":{}|<>]/.test(password)) {
          pwdMsg.textContent = 'Strong password';
          pwdMsg.style.color = 'green';
          passwordInput.style.borderColor = 'green';
          return true;
      } else if (password.length > 0) {
          pwdMsg.textContent = 'Password does not meet requirements';
          pwdMsg.style.color = 'red';
          passwordInput.style.borderColor = 'red';
          return false;
      } else {
          pwdMsg.textContent = 'Password is required';
          pwdMsg.style.color = 'red';
          passwordInput.style.borderColor = 'red';
          return false;
      }
  });

  // Confirm password validation
  confirmPasswordInput.addEventListener('input', function() {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      
      if (confirmPassword === '') {
          errorMsg.textContent = 'Please confirm your password';
          errorMsg.style.color = 'red';
          confirmPasswordInput.style.borderColor = 'red';
          return false;
      } else if (password !== confirmPassword) {
          errorMsg.textContent = 'Passwords do not match';
          errorMsg.style.color = 'red';
          confirmPasswordInput.style.borderColor = 'red';
          return false;
      } else {
          errorMsg.textContent = 'Passwords match';
          errorMsg.style.color = 'green';
          confirmPasswordInput.style.borderColor = 'green';
          return true;
      }
  });

  // Form submission validation
  form.addEventListener('submit', function(event) {
      const firstName = firstNameInput.value.trim();
      const lastName = lastNameInput.value.trim();
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      const checkbox = document.querySelector('.checkbox').checked;
      
      // Validate first name
      const nameRegex = /^[A-Za-z]{2,30}$/;
      if (firstName === '') {
          event.preventDefault();
          alert('First name is required');
          firstNameInput.focus();
          return false;
      } else if (!nameRegex.test(firstName)) {
          event.preventDefault();
          alert('First name should only contain letters (2-30 characters)');
          firstNameInput.focus();
          return false;
      }
      
      // Validate last name
      if (lastName === '') {
          event.preventDefault();
          alert('Last name is required');
          lastNameInput.focus();
          return false;
      } else if (!nameRegex.test(lastName)) {
          event.preventDefault();
          alert('Last name should only contain letters (2-30 characters)');
          lastNameInput.focus();
          return false;
      }
      
      // Validate email
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
      if (email === '') {
          event.preventDefault();
          alert('Email is required');
          emailInput.focus();
          return false;
      } else if (!emailRegex.test(email)) {
          event.preventDefault();
          alert('Please enter a valid email address');
          emailInput.focus();
          return false;
      }
      
      // Validate password
      if (password === '') {
          event.preventDefault();
          alert('Password is required');
          passwordInput.focus();
          return false;
      } else if (
          password.length < 8 || 
          !/[A-Z]/.test(password) || 
          !/[a-z]/.test(password) || 
          !/\d/.test(password) || 
          !/[!@#$%^&*(),.?":{}|<>]/.test(password)
      ) {
          event.preventDefault();
          alert('Password does not meet requirements');
          passwordInput.focus();
          return false;
      }
      
      // Validate confirm password
      if (confirmPassword === '') {
          event.preventDefault();
          alert('Please confirm your password');
          confirmPasswordInput.focus();
          return false;
      } else if (password !== confirmPassword) {
          event.preventDefault();
          alert('Passwords do not match');
          confirmPasswordInput.focus();
          return false;
      }
      
      // Validate terms checkbox
      if (!checkbox) {
          event.preventDefault();
          alert('Please agree to the terms and conditions');
          return false;
      }
      
      // If all validations pass, form will submit
      return true;
  });
});
let button = document.getElementById("button");
let message = document.getElementById("message");
let msg = document.getElementById("msg");
let email = document.getElementById("email");

msg.innerText = "";
message.innerText = "";
message.classList.remove("error_msg", "success_msg");

document.getElementById("password").addEventListener("input", validatePassword);

function validatePassword() {
   if (password.value.length < 8) {
      msg.innerText = "Password must be at least 8 characters long";
      msg.classList.add("error_msg"); 
      button.disabled = true;
      return false;
    }
    else {
      button.disabled = false;
      msg.innerText = "";
    }
 
   const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  if (!strongPassword.test(password.value)) {
    msg.innerText =
      "Password must include uppercase, lowercase, number, and special character";
    msg.classList.add("error_msg");
    button.disabled = true;
    return false;
  }
}

document.getElementById("c_password").addEventListener("input", validatePasswords);

function validatePasswords() {
  const password = document.getElementById("password");
  const c_password = document.getElementById("c_password");

  message.innerText = "";
  message.classList.remove("error_msg", "success_msg");

 
  if (password.value !== c_password.value) {
    message.innerText = "Passwords do not match";
    message.classList.add("error_msg");
    return false;
  } else {
    message.innerText = "password matched sucessfully";
    message.classList.add("success_msg");
    button.disabled = false;
    return true;
  }
}
 document.getElementById("email").addEventListener("input", validateEmail);
function validateEmail() {
  
   const emailMsg = document.getElementById("email_msg");
 
  
   const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
   if (!emailRegex.test(email.value)) {
     emailMsg.innerText = "Invalid email format";
     emailMsg.classList.add("error_msg");
     button.disabled = true;
     return false;
   }
 
  
   const commonDomains = ["gmail.com", "yahoo.com", "outlook.com"];
   const emailParts = email.value.split("@");
   if (emailParts.length > 1) {
     const domain = emailParts[1];
     if (!commonDomains.includes(domain)) {
      
       emailMsg.classList.add("error_msg");
     }
   } else {
     emailMsg.innerText = "";
   }
 
   emailMsg.classList.remove("error_msg");
   button.disabled = false;
   return true;
 }
 
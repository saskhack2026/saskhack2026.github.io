// Register Event Listeners for the sign up input cells
document.getElementById("email").addEventListener("blur", emailHandler);
document.getElementById("confirm-email").addEventListener("blur", cEmailHandler);
document.getElementById("username").addEventListener("blur", usernameHandler);
document.getElementById("password").addEventListener("blur", passwordHandler);
document.getElementById("confirm-password").addEventListener("blur", cPwdHandler);
document.getElementById("profilephoto").addEventListener("change", avatarHandler);
// Register a submit event to the form element in the signup page 
// and assign validateSignup as the event handler
document.getElementById("signup").addEventListener("submit", validSignUp);
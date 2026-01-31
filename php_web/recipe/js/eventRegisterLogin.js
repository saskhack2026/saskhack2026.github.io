// input listeners
document.getElementById("email").addEventListener("blur", emailHandler);
document.getElementById("password").addEventListener("blur", passwordHandler);
// Register a submit event to the form element in the login page 
// and assign validateLogin as the event handler
document.getElementById("login").addEventListener("submit", validateLogin);
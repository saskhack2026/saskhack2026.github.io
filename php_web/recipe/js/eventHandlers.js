/* Utility Functions for all pages*/
// Show and hide error messages functions
function showError(element, fieldName, customMsg) {
	const errMsg = document.getElementById(`err-${fieldName}`);
	if (errMsg) {
		errMsg.textContent = customMsg;
		errMsg.classList.remove("hidden");
		element.classList.add("invalid");
		errMsg.classList.remove("valid");
	}
}
function hideError(element, fieldName) {
	const errMsg = document.getElementById(`err-${fieldName}`);
	if (errMsg) {
		errMsg.classList.add("hidden");
		element.classList.remove("invalid");
	}
}
//Show the file name of the profile picture
function showInfo(element, fieldName, message) {
	const errMsg = document.getElementById(`err-${fieldName}`);
	if (errMsg) {
		errMsg.textContent = message;
		errMsg.classList.remove("hidden");
		element.classList.remove("invalid");
		errMsg.classList.add("valid");
	}
}
/* Functions for Login and Signup */
// Validate input functions
function validateEmail(email) {
	// Source: HTML5 specification email validation
	// https://html.spec.whatwg.org/multipage/forms.html#valid-e-mail-address
	let emailRegEx = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
	return emailRegEx.test(email) && email.length <= 254;
}
// Validate that the password is 6 characters or more and 
// contains 1 or more non-letter that isn't a space
function validatePassword(password) {
	let passRegEx = /^(?=.*[^a-zA-Z ]).{6,}$/;
	return passRegEx.test(password);
}
function validateUsername(username) {
	let usernameRegEx = /^[a-zA-Z0-9_]+$/;
	return usernameRegEx.test(username);
}
function validateAvatar(avatar) {
	// Check if file is selected and has valid image extension
	return avatar.files.length === 1 && avatar.files[0].type.startsWith('image/');
}
// Validate login on submit
function validateLogin(event) {
	console.log("validateLogin called!");
	let email = document.getElementById("email");
	let password = document.getElementById("password");
	let formIsValid = true;
	if (!validateEmail(email.value)) {
		showError(email, "email", "Email format is invalid");
		formIsValid = false;
	} else {
		hideError(email, "email");
	}
	if (!validatePassword(password.value)) {
		showError(password, "pass", "Password must have 6 or more characters, at least 1 non-letter, and no spaces.");
		formIsValid = false;
	} else {
		hideError(password, "pass");
	}
	// prevent login if the email or password are invalid
	if (!formIsValid) {
		event.preventDefault();
	}
}
/* Event handlers for signup form*/
// display an error message if the email input is empty or invalid
function emailHandler(event) {
	let email = event.target;
	// display an error message if the email input is empty or invalid
	if (event.type === 'blur' || event.type === 'input') {
		if (email.value === "") {
			showError(email, "email", "Email is required");
		} else if (!validateEmail(email.value)) {
			showError(email, "email", "Email format is invalid");
		} else {
			hideError(email, "email");
		}
	} else {
		hideError(email, "email");
	}
}
// check that the two email addresses match
function cEmailHandler(event) {
	let email = document.getElementById("email");
	let confirmEmail = event.target;
	if (email.value !== confirmEmail.value) {
		showError(confirmEmail, "confirm-email", "Emails do not match.");
	} else {
		hideError(confirmEmail, "confirm-email");
	}
}
// display an error message if the username input is empty or invalid
function usernameHandler(event) {
	let username = event.target;
	if (event.type === 'blur' || event.type === 'input') {
		if (username.value === "") {
			showError(username, "username", "A username is required");
		} else if (!validateUsername(username.value)) {
			showError(username, "username", "Invalid username.");
		} else {
			hideError(username, "username");
		}
	} else {
		hideError(username, "username");
	}
}
// display an error message if the password input is empty or invalid
function passwordHandler(event) {
	let password = event.target;
	if (event.type === 'blur' || event.type === 'input') {
		if (password.value === "") {
			showError(password, "pass", "A password is required.");
		}
		else if (!validatePassword(password.value)) {
			showError(password, "pass", "Password must have 6 or more characters, at least 1 non-letter, and no spaces.");
		} else {
			hideError(password, "pass");
		}
	}
}
// check that the password and confirmation match
function cPwdHandler(event) {
	let password = document.getElementById("password");
	let confirmPassword = event.target;
	if (password.value !== confirmPassword.value) {
		showError(confirmPassword, "confirm-password", "Passwords do not match.");
	} else {
		hideError(confirmPassword, "confirm-password");
	}
}
// display the name of the image file if it is valid and error message if
// it is missing or invalid
function avatarHandler(event) {
	let avatar = event.target;
	if (avatar.files.length === 0) {
		showError(avatar, "profilephoto", "No image file selected.");
	} else if (avatar.files.length === 1) {
		let selectedImg = avatar.files[0];
		if (selectedImg.type.startsWith('image/')) {
			// display the valid image file name in the error message space
			showInfo(avatar, "profilephoto", selectedImg.name);
		}
		else {
			showError(avatar, "profilephoto", "Invalid file type.");
		}
	} else {
		showError(avatar, "profilephoto", "Invalid file type.");
	}
}
// Validate Sign up on submit
function validSignUp(event) {
	let email = document.getElementById("email");
	let confirmEmail = document.getElementById("confirm-email");
	let username = document.getElementById("username");
	let password = document.getElementById("password");
	let confirmPassword = document.getElementById("confirm-password");
	let avatar = document.getElementById("profilephoto");
	let signUpIsValid = true;
	// Validate email
	if (!validateEmail(email.value)) {
		showError(email, "email", "Email format is invalid");
		signUpIsValid = false;
	} else {
		hideError(email, "email");
	}
	// Validate email confirmation
	if (email.value !== confirmEmail.value) {
		showError(confirmEmail, "confirm-email", "Emails do not match.");
		signUpIsValid = false;
	} else {
		hideError(confirmEmail, "confirm-email");
	}
	// Validate username
	if (!validateUsername(username.value)) {
		showError(username, "username", "Invalid username.");
		signUpIsValid = false;
	} else {
		hideError(username, "username");
	}
	// Validate password
	if (!validatePassword(password.value)) {
		showError(password, "pass", "Password must have 6 or more characters, at least 1 non-letter, and no spaces.");
		signUpIsValid = false;
	} else {
		hideError(password, "pass");
	}
	// Validate password confirmation
	if (password.value !== confirmPassword.value) {
		showError(confirmPassword, "confirm-password", "Passwords do not match.");
		signUpIsValid = false;
	} else {
		hideError(confirmPassword, "confirm-password");
	}
	// Validate avatar (only if a file is selected)
	if (!validateAvatar(avatar)) {
		showError(avatar, "profilephoto", "Missing or invalid image file.");
		signUpIsValid = false;
	} else {
		hideError(avatar, "profilephoto");
	}
	// prevent submission if any fields are invalid or missing
	if (signUpIsValid === false) {
		event.preventDefault();
	}
}
/* Dynamic character count*/
// adapted from https://www.codexworld.com/live-character-counter-javascript/
function countChars(obj, maxLength) {
	let strLength = obj.value.length;
	let charRemain = (maxLength - strLength);
	document.getElementById("charNum").innerHTML = charRemain + ' characters remaining';
}
/*Validation for Create Recipe Page*/
// check that the recipe name is more than 0 characters and no more than 256
function validateRecipeName(recipeName) {
	// console.log("name length checked");
	// console.log(recipeName.length);
	return recipeName.length <= 256 && recipeName.length > 0;
}
// show a dynamic character count of how many characters are remaining
function recipeNameLengthHandler(event) {
	let name = event.target;
	countChars(name, 256);
}
// prevent submission of a recipe with an invalid or missing title
function recipeNameHandler(event) {
	let recipeName = document.getElementById("name").value;
	if (!validateRecipeName(recipeName)) {
		event.preventDefault();
	}
}
/*Validation for Recipe Notes*/
// check that the recipe note is more than 0 characters and no more than 1300
function validateRecipeNote(note) {
	return note.length <= 1300 && note.length > 0;
}
// show a dynamic character count of how many characters are remaining
function recipeNoteLengthHandler(event) {
	let note = event.target;
	countChars(note, 1300);
}
// prevent submission of an invalid recipe note
function recipeNoteHandler(event) {
	let note = document.getElementById("add-note").value;
	// console.log("submit being checked");
	if (!validateRecipeNote(note)) {
		// console.log("invalid submit");
		event.preventDefault();
	}
}
function escapeHtml(text) {
	const div = document.createElement('div');
	div.textContent = text;
	return div.innerHTML;
}
/* Check for new recipes after 90 seconds*/
function getLatestDateTime() {
	let recipeGrid = document.getElementById('list-main');
	let firstRecipe = recipeGrid.querySelector('.list-recipe');
	if (firstRecipe) {
		return firstRecipe.getAttribute('data-creation-time');
	}
	return null;
}
function checkForNewRecipes() {
	const latestDateTime = getLatestDateTime();
	if (!latestDateTime) {
		return;
	}
	//Request for new recipes
	fetch(`${window.location.pathname}?ajax=new_recipes&since=${encodeURIComponent(latestDateTime)}`)
		.then(response => response.json())
		.then(data => {
			if (data.success && data.new_recipes && data.new_recipes.length > 0) {
				addNewRecipes(data.new_recipes);
				updateTotalCount(data.new_recipes.length);
			}
			else if (data.error) {
				showError(data.error, "recipe-fetch", "Error fetching new recipes");
			}
			else {
				// No new recipes, just update timestamp
				updateLastUpdatedTime();
			}
		})
		.catch(error => {
			console.error('AJAX request failed:', error);
		});
}
function addNewRecipes(newRecipes) {
	let recipeGrid = document.getElementById('list-main');
	newRecipes.forEach(recipe => {
		const recipeItem = document.createElement('div');
		recipeItem.classList.add('list-recipe');
		recipeItem.setAttribute('data-recipe', JSON.stringify(recipe));
		recipeItem.setAttribute('data-creation-time', recipe.creation_time);
		recipeItem.innerHTML = `
		<div class="recipe-title">${escapeHtml(recipe.recipe_name)}</div>
            <div class="recipe-details">
                ${recipe.creator_id != currentUserId ? `Creator: ${escapeHtml(recipe.username)}<br />` : ''}
                Created: ${escapeHtml(recipe.creation_time)}<br />
                Last Note: ${recipe.last_note_date ? escapeHtml(recipe.last_note_date) : 'No notes yet'}<br />
                Notes: ${recipe.note_count || 0}<br />
            </div>
            ${recipe.creator_id == currentUserId ? `
                <a href="access.php?id=${encodeURIComponent(recipe.recipe_id)}" class="btn recipe-access">Access</a>
                <a href="view-recipe.php?id=${encodeURIComponent(recipe.recipe_id)}" class="btn recipe-access">View</a>
            ` : `
                <a href="view-recipe.php?id=${encodeURIComponent(recipe.recipe_id)}" class="btn recipe-view">View</a>
            `}
        `;
		// Insert the new recipe after the create button
		const createButton = recipeGrid.querySelector('.create-btn');
		if (createButton) {
			createButton.insertAdjacentElement('afterend', recipeItem);
			console.log("Before the button");
		} else {
			//insert at beginning if create button not found
			recipeGrid.insertBefore(recipeItem, recipeGrid.firstChild);
			console.log("After the button");
		}
	});
}
function updateTotalCount(newRecipeCount) {
	totalRecipeCount += newRecipeCount;
	const totalElement = document.getElementById('totalCount');
	if (totalElement) {
		totalElement.textContent = totalRecipeCount;
	}
}
function updateLastUpdatedTime() {
	const now = new Date();
	const lastUpdatedElement = document.getElementById('lastUpdated');
	if (lastUpdatedElement) {
		lastUpdatedElement.textContent = now.toLocaleDateString() + ' ' + now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
	}
}
/* AJAX Functions for Recipe Notes */
// Load existing notes for a recipe
function loadNotes(recipeId) {
	fetch(`notes-ajax.php?recipe_id=${recipeId}`)
		.then(response => response.text())
		.then(data => {
			document.getElementById('note-container').innerHTML = data;
		})
		.catch(error => {
			console.error('Error loading notes:', error);
		});
}
// Submit new note
function submitNote(formElement, recipeId) {
	const noteText = document.getElementById('add-note').value;
	let formData = new FormData(formElement);
	// validate the note
	if (!validateRecipeNote(noteText)) {
		showError(document.getElementById('add-note'), 'note', 'Note must be between 1 and 1300 characters.');
		return;
	}
	// Debug: Log what's in the FormData
	for (let [key, value] of formData.entries()) {
		console.log(key, value);
	}
	fetch(`notes-ajax.php?recipe_id=${recipeId}`, {
		method: 'POST',
		body: formData,
		cache: 'no-cache'
	})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Add new note to the list
				addNewNoteToList(data.newNote);
				// Reset form and clear errors
				formElement.reset();
				hideError(document.getElementById('add-note'), 'note');
				// Reset the character counter
				document.getElementById("charNum").innerHTML = '1300 characters remaining';
			} else {
				showError(document.getElementById('add-note'), 'note', data.error || 'Failed to add note');
			}
		})
		.catch(error => {
			console.error('Error submitting note:', error);
			showError(document.getElementById('add-note'), 'note', 'Network error occurred');
		});
}
// Add new note to the note container
function addNewNoteToList(note) {
	const noteHTML = `
        <div class="note-container">
            <div class="note-profile">
                <img src="${escapeHtml(note.photo_url)}" class="profile-pic" 
                    alt="profile picture of ${escapeHtml(note.author)}" />
                <div class="created-details">
                    <ul>
                        <li>${escapeHtml(note.author)}</li>
                        <li>${escapeHtml(note.timestamp)}</li>
                    </ul>
                </div>
            </div>
            <div class="note">
                ${escapeHtml(note.content).replace(/\n/g, '<br>')}
            </div>
        </div>
    `;
	// Insert at the beginning of notes container
	const addNoteForm = document.querySelector('.add-note');
	
	console.log("Add note form found:", addNoteForm); // Debug line
	
	if (addNoteForm) {
		// Insert the new note before the add-note form (after existing notes)
		addNoteForm.insertAdjacentHTML('beforebegin', noteHTML);
		console.log("Note added successfully"); // Debug line
	} else {
		console.log("Could not find add-note form!"); // Debug line
	}
}
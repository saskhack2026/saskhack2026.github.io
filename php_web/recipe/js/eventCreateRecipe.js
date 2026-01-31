//restrict the name of the recipe to 256 characters
document.getElementById("name").maxLength = "256";
//display a dynamic count of how many available characters are left 
document.getElementById("name").addEventListener("keyup", recipeNameLengthHandler);
//prevent submission of a recipe with a blank title
document.getElementById("recipe-save").addEventListener("submit", recipeNameHandler);

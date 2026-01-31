// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // loadNotes(recipePage);
    setupNoteForm(recipePage);
});

function setupNoteForm(recipeId) {
    const noteTextarea = document.getElementById("add-note");
    const noteForm = document.querySelector('#note-form');
    
    if (noteTextarea) {
        // Set character limit
        noteTextarea.maxLength = "1300";
        
        //display a dynamic count of how many available characters are left 
        noteTextarea.addEventListener("keyup", recipeNoteLengthHandler);
    }
    // submit event listener
    if (noteForm) {
        noteForm.addEventListener('submit', function(event) {
            event.preventDefault();
            submitNote(this, recipeId);
        });
    }
}
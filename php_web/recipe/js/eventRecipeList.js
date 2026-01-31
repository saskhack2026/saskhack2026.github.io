// Timer starts automatically when page loads - checks every 90 seconds
// setInterval(checkForNewRecipes, 90000);

// Test every 10 seconds instead of 90 for debugging
setInterval(checkForNewRecipes, 10000);

// Add this for debugging
console.log('Auto-refresh timer started - checking every 10 seconds');
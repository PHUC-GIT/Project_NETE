function refreshTime() {
  var date = new Date();

  var dateOptions = {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  };
  // Get date parts based on the specified timezone
  var parts = new Intl.DateTimeFormat('en-US', dateOptions).formatToParts(date);
  
  // Extract and map the parts to YYYY/MM/DD format
  var year = parts.find(p => p.type === 'year').value;
  var month = parts.find(p => p.type === 'month').value;
  var day = parts.find(p => p.type === 'day').value;
  
  var formattedDate = year + "/" + month + "/" + day; // 2025/12/02

  var timeOptions = {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  };
  // Use toLocaleString just for the time portion
  var timeString = date.toLocaleString("en-US", timeOptions); // 9:44 AM (or similar)

  // Combine and Display
  var formattedString = formattedDate + " | " + timeString.replace(", ", "");
  timeDisplay.innerHTML = formattedString;
}

// Run once immediately, then every second
refreshTime();
setInterval(refreshTime, 1000);

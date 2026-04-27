let currentSize = 15; 

function changeSize(step) {
  currentSize += step;
  
  // Keep it within a readable range
  if (currentSize < 10) currentSize = 10;
  if (currentSize > 40) currentSize = 40;
  
  document.getElementById("text_view").style.fontSize = currentSize + "px";
}

function changeSizeEdit(step) {
  currentSize += step;
  
  // Keep it within a readable range
  if (currentSize < 10) currentSize = 10;
  if (currentSize > 40) currentSize = 40;
  
  document.getElementById("text_form").style.fontSize = currentSize + "px";
}
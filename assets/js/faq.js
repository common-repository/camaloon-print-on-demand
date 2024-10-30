function copyChecklistClipboard() {
  /* Get the text field */
  var copyText = document.getElementById("checklistClipboard");

  /* Copy the text inside the text field */
  navigator.clipboard.writeText(copyText.textContent.replace(/ /g,''));
}

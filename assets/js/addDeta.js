//cssのvalueを取得する
function getStyleSheetValue(element, property) {
  if (!element || !property) {
    return null;
  }

  var style = window.getComputedStyle(element);
  var value = style.getPropertyValue(property);

  return value;
}

function toggleNewDetaForm() {
  var newDetaForm = document.querySelector(".new_deta_form");
  var newDetaFormDisplayStyle = getStyleSheetValue(newDetaForm, 'display');

  if(newDetaFormDisplayStyle == "none"){
    newDetaForm.style.display = "block";
  }else{
    newDetaForm.style.display = "none";
  }
}

// const addDetaBtn = document.querySelector(".addDeta");
// addDetaBtn.onclick = function() {
//   toggleNewDetaForm();
// }
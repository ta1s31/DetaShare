//cssのvalueを取得する
function getStyleSheetValue(element, property) {
  if (!element || !property) {
    return null;
  }

  var style = window.getComputedStyle(element);
  var value = style.getPropertyValue(property);

  return value;
}

function showDetaForm() {
  var detaForm = document.querySelector(".new_deta_form");
  var addBtn = document.querySelector("#addDeta");
  addBtn.classList.toggle('showDetaFormBtn');
  addBtn.classList.toggle('hiddenDetaFormBtn');
  detaForm.style.display = 'block';
}

function hiddenDetaForm() {
  var detaForm = document.querySelector(".new_deta_form");
  var addBtn = document.querySelector("#addDeta");
  addBtn.classList.toggle('showDetaFormBtn');
  addBtn.classList.toggle('hiddenDetaFormBtn');
  detaForm.style.display = 'none';
}

function toggleNewDetaForm() {
  var detaForm = document.querySelector(".new_deta_form");
  var detaFormDisplayStyle = getStyleSheetValue(detaForm, 'display');

  detaFormDisplayStyle == "none" ? showDetaForm() : hiddenDetaForm();
}

// const addDetaBtn = document.querySelector(".addDeta");
// addDetaBtn.onclick = function() {
//   toggleNewDetaForm();
// }
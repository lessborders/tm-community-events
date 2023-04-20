var captcha;
function generate() {
  // Clear old input
  document.getElementById("captcha_submit").value = "";

  // Access the element to store
  // the generated captcha
  captcha = document.getElementById("captcha_image");
  var uniquechar = "";
  var length = 6;

  const randomchar = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  // Generate captcha for length of
  // 5 with random character
  for (let i = 1; i < length + 1; i++) {
    uniquechar += randomchar.charAt(Math.random() * randomchar.length);
  }

  // Store generated input
  captcha.innerHTML = uniquechar;
  document.getElementById("captcha_check").value = uniquechar;
}

function checkCaptcha(e) {
  const usr_input = document.getElementById("captcha_submit").value;
  const captcha_check = document.getElementById("captcha_check").value;

  // Check whether the input is equal
  // to generated captcha or not
  if (usr_input != captcha_check) {
    e.preventDefault();
    alert("Captcha failed.");
    generate();
    return false;
  }

  return true;
}

generate();

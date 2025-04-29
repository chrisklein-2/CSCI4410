let captcha;

function generate() {
    document.getElementById("submit").value = ""; //clear input

    captcha = document.getElementById("captchaImage"); //access element, store the generated captcha
    let uniquechar = "";

    const randomchar = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()";

    for (let i = 0; i < 4; i++) {
        uniquechar += randomchar.charAt(Math.floor(Math.random() * randomchar.length));
    }

    captcha.innerHTML = uniquechar; //store input
}

function printmsg() {
    const user_input = document.getElementById("submit").value;
    const resultDisplay = document.getElementById("result");

    if (user_input === captcha.innerHTML) {
        resultDisplay.innerHTML = "Matched";
        return true; //allow form submission
    } else {
        resultDisplay.innerHTML = "Captcha not matched!";
        resultDisplay.style.color = "red";
        generate(); //refresh captcha
        return false; //stop form submission
    }
}
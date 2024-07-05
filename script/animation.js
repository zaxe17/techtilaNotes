const wrapper = document.querySelector(".wrapper");
const registerLink = document.querySelector(".register-link");
const loginLink = document.querySelector(".login-link");
const forgotLink = document.querySelector(".forgot-link");
const Login_in_forgot = document.querySelector(".LoginForgot");

/* LOGIN LINK */
loginLink.onclick = () => {
    wrapper.classList.remove('active');
    document.title = "Techtilanotes | Login";
}

/* REGISTER LINK */
registerLink.onclick = () => {
    wrapper.classList.add('active');
    document.title = "Techtilanotes | Sign Up";
}

/* FORGOT PASSWORD LINK */
forgotLink.onclick = () => {
    wrapper.classList.add('hello');
    document.title = "Techtilanotes | Forgot Password";
}

Login_in_forgot.onclick = () => {
    wrapper.classList.remove('hello');
    document.title = "Techtilanotes | Forgot Password";
}

/* SCROLL */
let sections = document.querySelectorAll('section');
let navLinks = document.querySelectorAll('header nav a');

window.onscroll = () => {
    wrapper.classList.remove('active');

    let middleOfViewport = window.innerHeight / 4;
    
    sections.forEach(sec => {
        let top = window.scrollY;
        let offset = sec.offsetTop;
        let height = sec.offsetHeight;
        let id = sec.getAttribute('id');

        if(top + middleOfViewport >= offset && top + middleOfViewport < offset + height) {
            navLinks.forEach(links => {
                links.classList.remove('active');
                document.querySelector('header nav a[href*=' + id + ']').classList.add('active');
            });

            document.title = "Techtilanotes | " + id;
            sec.classList.add('show-animate');
        }
        else {
            sec.classList.remove('show-animate');
        }
    });

    let header = document.querySelector('header');

    header.classList.toggle('sticky', window.scrollY > 100);
}
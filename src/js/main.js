const menuBtn = document.querySelector(".menu__toggle-btn");
const navList = document.querySelector(".navbar__list");
const navItms = document.querySelectorAll(".nav-item");
const navProgress = document.querySelector(".navbar__progress-bar");
const nav = document.querySelector(".header");
const body = document.querySelector("body");
const imgs = document.querySelectorAll('.projects__item-img');
const sections = document.querySelectorAll('.section');
const navMobileBtn = document.querySelector('a[data-button="left"]');
const form = document.querySelector('form');
const copyDate = document.querySelector('.copy-date');



const closeMenu = () => {
    navList.setAttribute('data-visible', false);
    menuBtn.setAttribute('aria-expanded', false)
}
const expandMenu = () => {
    navList.setAttribute('data-visible',
        true);
    menuBtn.setAttribute('aria-expanded', true)
}

const handleMenu = () => {
    let checkActive = navList.getAttribute(
        'data-visible'
    );
    if (checkActive === "true") {
        closeMenu();
    } else {
        expandMenu();
    }

}
const handleMobileNavMenu = () => {
    let navbar = document.querySelector('.navbar-bottom');
    let anchors = document.querySelectorAll('.navbar-bottom ul li a')
    let checkIsVisible = window.getComputedStyle(navbar).getPropertyValue('display');
    if (checkIsVisible === 'none') {
        anchors.forEach((anchor) => {
            anchor.setAttribute('aria-hidden', true);
        })
    } else {
        anchors.forEach((anchor) => {
            anchor.setAttribute('aria-hidden', false)
        })
    }
}
const handleNavProgress = () => {
    let scrollPosition = window.scrollY;
    let compStyle = window.getComputedStyle(body);
    let bodyHeight = compStyle.getPropertyValue('height');
    let progress = scrollPosition / parseInt(bodyHeight, 10);
    let secureWidthStyle = window.getComputedStyle(navProgress);

    if (progress > 1) {
        let progress = 1;
        navProgress.style.width = `${progress *100}%`
    } else {
        let secureWidth = secureWidthStyle.getPropertyValue('width')
        if (progress > 0.6 && parseInt(secureWidth, 10) >= parseInt(compStyle.getPropertyValue('width'), 10)) {
            navProgress.style.width = '100%';
        } else {
            navProgress.style.width = `${progress  *170}%`
        }
    }

}

navItms.forEach(item => {
    item.addEventListener('click', closeMenu)
});

const handleImg = () => {
    imgs.forEach(img => {
        const url = img.getAttribute('data-src');
        img.style.backgroundImage = `url(${url})`;
    })
}

const observerOptions = {
    root: null,
    threshold: 0.5,
}
const menuLis = document.querySelectorAll('.navbar-bottom>ul>li');
const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            const check = window.getComputedStyle(menuLis[1]).getPropertyValue('display');
            if (entry.isIntersecting && check === "list-item") {
                if (entry.target.classList.contains('landpage')) {
                    navMobileBtn.innerHTML = 'oferta';
                    navMobileBtn.setAttribute('href', '#offer')
                } else if (entry.target.classList.contains('offer')) {
                    navMobileBtn.innerHTML = 'projekty';
                    navMobileBtn.setAttribute('href', '#projects')
                } else if (entry.target.classList.contains('projects')) {
                    menuLis[0].style.width = "100%";
                    menuLis[1].style.display = "none";
                    navMobileBtn.innerHTML = 'kontakt';
                    navMobileBtn.setAttribute('href', '#contact');
                }
            } else if (entry.isIntersecting && entry.target.classList.contains('contact') && check === 'none') {
                navMobileBtn.innerHTML = 'wyślij';
                navMobileBtn.setAttribute('href', '#contact')
                navMobileBtn.addEventListener('click', submitForm);
            } else if (entry.isIntersecting && entry.target.classList.contains('projects')) {
                navMobileBtn.innerHTML = 'kontakt';
                navMobileBtn.setAttribute('href', '#contact');
                navMobileBtn.removeEventListener('click', submitForm);
            } else if (entry.isIntersecting && check === 'none' && !entry.target.classList.contains('projects') && !entry.target.classList.contains('contact')) {
                menuLis[1].style.display = "list-item";
                menuLis[1].style.width = "50%";
                menuLis[0].style.width = "50%";
                navMobileBtn.innerHTML = 'projekty'
                navMobileBtn.setAttribute('href', '#projects')
                navMobileBtn.removeEventListener('click', submitForm);

            }

        })
    },
    observerOptions);



sections.forEach(sec => {
    observer.observe(sec)
})



const clearForm = () => {
    const inputs = document.querySelectorAll('.contact__form-input');
    inputs.forEach(input => {
        input.value = "";
    });
}
const submitForm = () => {
    const form = document.querySelector('form');
    form.submit();
}

const handleCopyDate = () => {
    const todayDate = new Date;
    copyDate.innerHTML = todayDate.getFullYear();
}

menuBtn.addEventListener('click', handleMenu);
window.addEventListener('scroll', handleNavProgress);
window.addEventListener('load', handleImg);
window.addEventListener('load', handleCopyDate);
window.addEventListener('load', handleMobileNavMenu);
window.addEventListener('resize', handleMobileNavMenu);
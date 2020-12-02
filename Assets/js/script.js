let mainNavLinks = document.querySelectorAll(".anchors ul li a");
let mainSections = document.querySelectorAll(".wrapper section");

let lastId;
let cur = [];

// This should probably be throttled.
// Especially because it triggers during smooth scrolling.
// https://lodash.com/docs/4.17.10#throttle
// You could do like...
// window.addEventListener("scroll", () => {
//    _.throttle(doThatStuff, 100);
// });
// Only not doing it here to keep this Pen dependency-free.

window.addEventListener("scroll", event => {
    let fromTop = window.scrollY;

    mainNavLinks.forEach(link => {
        let section = document.getElementById(link.hash.substring(1));
        section = section.parentElement;

        if (
            section.offsetTop <= fromTop + 81 &&
            section.offsetTop + section.offsetHeight > fromTop + 81
        ) {
            link.classList.add("active");
        } else {
            link.classList.remove("active");
        }
    });
});
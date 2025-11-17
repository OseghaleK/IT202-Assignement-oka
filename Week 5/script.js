const hoverBox = document.getElementById("hoverBox");
const heading = document.getElementById("dynamicHeading");

hoverBox.addEventListener("mouseover", () => {
    heading.style.display = "block";
});

hoverBox.addEventListener("mouseout", () => {
    heading.style.display = "none";
});
window.addEventListener("load", handler);

function handler() {
	let hamburger = document.getElementById("hamburger-menu");

	hamburger.addEventListener("click", () => {
		let aside = document.getElementById("aside");
		aside.classList.toggle("active");
	});
}

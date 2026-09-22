/* VS Contact Form scripts */

/* Form reset */
window.addEventListener("pageshow", function() {
	if(document.getElementById("vscf-form")) {
		document.getElementById("vscf-form").reset();
	}
});

/* Form anchor */
document.addEventListener("DOMContentLoaded", function() {
	if(document.getElementById("vscf-anchor")) {
		document.getElementById("vscf-anchor").scrollIntoView({behavior:"smooth",block:"center"});
	}
});

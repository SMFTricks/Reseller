/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

let localVariant = localStorage.getItem('st_theme_variant');
let toggleVariant = document.querySelectorAll('.theme-variant-toggle');

if (!smf_member_id && localVariant !== null) {
	document.documentElement.dataset.mode = localVariant;
}

function switchVariant(variant) {
	document.documentElement.dataset.variant = variant;

	// User
	if (smf_member_id !== 0) {
		smf_setThemeOption('theme_variant', variant, smf_theme_id, smf_session_id, smf_session_var, null);
	}
	// Guest
	else {
		localStorage.setItem('st_theme_variant', variant);
	}
}

// Toggle theme variant
toggleVariant.forEach(toggle => {
	toggle.addEventListener('click', e => {
		e.preventDefault();
		switchVariant(toggle.dataset.variant)
	});
})

// SCEditor
$(document).ready(function() {
	$(toggleVariant).each((index, toggle) => {
		$(toggle).click(() => {
			$('.sceditor-container iframe').each(function() {
				$(this).contents().find('html').attr('data-variant', $(toggle).data('variant'));
			});
		});
	});
});
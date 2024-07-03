/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

let localMode = localStorage.getItem('st_theme_mode');
let toggleMode = document.querySelectorAll('.theme-mode-toggle');

if (!smf_member_id && localMode !== null) {
	document.documentElement.dataset.mode = localMode;
}

function switchMode(mode) {
	document.documentElement.dataset.mode = mode;

	// User
	if (smf_member_id !== 0) {
		smf_setThemeOption('st_theme_mode', mode, smf_theme_id, smf_session_id, smf_session_var, null);
	}
	// Guest
	else {
		localStorage.setItem('st_theme_mode', mode);
	}
}

// Toggle theme mode
toggleMode.forEach(toggle => {
	toggle.addEventListener('click', e => {
		e.preventDefault();
		switchMode(toggle.dataset.mode)
	});
})

// SCEditor
$(document).ready(function() {
	$(toggleMode).each((index, toggle) => {
		$(toggle).click(() => {
			$('.sceditor-container iframe').each(function() {
				$(this).contents().find('html').attr('data-mode', $(toggle).data('mode'));
			});
		});
	});
});
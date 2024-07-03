/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

// Some theme bits
$(function() {
	// Info center tabs
	if ($.isFunction($.fn.tabs)) {
		$('#info_center_blocks').tabs();
	}

	// Replace stats icon
	$("img[src=\'"+smf_images_url+"/icons/stats_info.png\']").replaceWith("<span class=\'main_icons stats\'></span>");

	// Change the behaviour of the notify button
	$('.normal_button_strip_notify').next().find('a').click(function (e) {
		var $obj = $(this);
		// All of the sub buttons are now without the active class if they had it.
		$('.notify_dropdown .viewport .overview a').removeClass('active');
		// Toggle this new selection as active
		$obj.toggleClass('active');
		e.preventDefault();
		ajax_indicator(true);
		// New Text
		var new_text = $obj.find('em').text();
		var new_text_lCase = new_text.toLowerCase();
		$.get($obj.attr('href') + ';xml', function () {
			ajax_indicator(false);
			$('.normal_button_strip_notify > span:last-child').text(new_text);
			$('.normal_button_strip_notify > span:first-child').removeClass();
			$('.normal_button_strip_notify > span:first-child').addClass('main_icons ' + new_text_lCase);
		});

		return false;
	});

	// Change the behaviour of the notify button
	$('.button_item_notify').next().find('a').click(function (e) {
		var $obj = $(this);
		// All of the sub buttons are now without the active class if they had it.
		$('[data-button-dropdown="notify_dropdown"] a').removeClass('active');
		// Toggle this new selection as active
		$obj.toggleClass('active');
		e.preventDefault();
		ajax_indicator(true);

		// Get the icon from this item
		var noti_icon = $obj.find('span.main_icons');

		$.get($obj.attr('href') + ';xml', function () {
			ajax_indicator(false);

			// Replace the element with the new one
			$('.button_item_notify > span.main_icons').replaceWith(noti_icon.clone());
		});

		return false;
	});

	// Likes on quickbuttons
	$(document).on('click', 'ul.quickbuttons li.post_like_button > a', function(event){
		var obj = $(this);
		event.preventDefault();
		ajax_indicator(true);
		$.ajax({
			type: 'GET',
			url: obj.attr('href') + ';js=1',
			headers: {
				"X-SMF-AJAX": 1
			},
			xhrFields: {
				withCredentials: typeof allow_xhjr_credentials !== "undefined" ? allow_xhjr_credentials : false
			},
			cache: false,
			dataType: 'html',
			success: function(html){
				obj.parent().replaceWith($(html).first('li'));
			},
			error: function (html){
			},
			complete: function (){
				ajax_indicator(false);
			}
		});

		return false;
	});

	// Likes count for messages.
	$(document).on('click', '.buttonlike_count', function(e){
		e.preventDefault();
		var title = $(this).find('em').text();
			url = $(this).attr('href') + ';js=1';
		return reqOverlayDiv(url, title, 'like');
	});

	// Color Picker Menu and Theme Mode
	$('#colorpicker_menu, #modepicker_menu').each(function(index, item) {
		$(item).prev().click(function(e) {
			e.stopPropagation();
			e.preventDefault();

			// Close any other top_menu from top_info
			const openMenu = document.querySelectorAll('.top_menu.visible');
			for (const listItem of openMenu) {
				if (listItem !== item) {
					listItem.classList.remove('visible');
				}
			}

			// IF a link was clicked
			let options = item.querySelectorAll('ul > li > a');
			for (const option of options) {
				option.addEventListener('click', e => {
					option.classList.add('active');
					for (const other of options) {
						if (other !== option) {
							other.classList.remove('active');
						}
					}
				});
			}

			if ($(item).is(':visible')) {
				$(item).css('display', 'none');
				return true;
			}
			$(item).css('display', 'block');
			$(item).css('top', $(this).offset().top + $(this).height());
		});
		$(document).click(function() {
			$(item).css('display', 'none');
		});
	});

	// Fixing the other popups because of the flexbox stuff...
	$('#profile_menu, #pm_menu, #alerts_menu').each(function(index, item) {
		$(item).prev().click(function(e) {
			$(item).css('top', $(this).offset().top + $(this).height());
		});
	});

	// Linktree toggler
	$(document).on('click', '.navigate_section ul li.trigger a', function(e){
		$('.navigate_section ul li').toggleClass('show');
	});

	// Menu improvements
	$('.mobile_user_menu, .mobile_act, .mobile_generic_menu_1, .mobile_generic_menu_1_tabs').click(function() {
		if ($('#mobile_user_menu, #mobile_action, #mobile_generic_menu_1, #mobile_generic_menu_1_tabs').is(':visible') == true) {
			$(document).mouseup(function (e) {
				if ($('#mobile_user_menu, #mobile_action, #mobile_generic_menu_1, #mobile_generic_menu_1_tabs').has(e.target).length === 0)
					$('#mobile_user_menu, #mobile_action, #mobile_generic_menu_1, #mobile_generic_menu_1_tabs').hide();
			}).keyup(function(e){
				if (e.keyCode == 27)
					$('#mobile_user_menu, #mobile_action, #mobile_generic_menu_1, #mobile_generic_menu_1_tabs').hide();
			});
		}
	});
});

// Variants in the pick theme area
function profileChangeVariant(sVariant)
{
	document.getElementById('theme_thumb_' + smf_theme_id).src = vThumbnails[sVariant];
}

/** Buttonlist **/
document.addEventListener('DOMContentLoaded', () => {
	const dropMenus = document.querySelectorAll('.buttonlist li > .top_menu');
	for (const item of dropMenus) {
		const prevElement = item.previousElementSibling;
		prevElement.addEventListener('click', e => {
			e.stopPropagation();
			e.preventDefault();

			// Close other buttonlist menus
			const openMenu = document.querySelectorAll('.buttonlist li > .top_menu.visible');
			for (const listItem of openMenu) {
				if (listItem !== item) {
					listItem.classList.remove('visible');
				}
			}

			if (item.classList.contains('visible')) {
				item.classList.remove('visible')
				return true;
			}

			item.classList.add('visible');
		});

		item.querySelector('.close').addEventListener('click', () => item.classList.remove('visible'));
		document.addEventListener('click', e => {
			if (!item.firstElementChild.contains(e.target)) {
				item.classList.remove('visible');
			}
		});
		document.addEventListener('keydown', e => {
			if (e.key === 'Escape') {
				item.classList.remove('visible');
			}
		});
	}

	const settingsTabs = document.querySelector('#st_settings_tabs');
	if (settingsTabs) {
		const settingsHash = window.location.hash;
		const tabs = settingsTabs.querySelectorAll('button');
		for (const tab of tabs) {
			// Disable tab navigation but keep it accessible
			if (!tab.classList.contains('active'))
				tab.setAttribute('tabindex', '-1');
			if (settingsHash) {
				tab.classList.remove('active');
				tab.setAttribute('aria-selected', 'false');
				settingsTabs.querySelector('#' + tab.getAttribute('aria-controls')).classList.remove('active', 'show');
			}
			tab.addEventListener('click', e => {
				st_activateTab(settingsTabs, tabs, tab, e);
			});
			tab.addEventListener('keydown', e => {
				// Navigate tabs with arrow keys
				if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
					const index = Array.from(tabs).indexOf(tab);
					const nextTab = e.key === 'ArrowRight' ? tabs[index + 1] || tabs[0] : tabs[index - 1] || tabs[tabs.length - 1];
					nextTab.focus();
					st_activateTab(settingsTabs, tabs, nextTab, e);
				}
			});
		}

		// Activate the tab if there is a hash
		if (settingsHash) {
			const tab = settingsTabs.querySelector('button[aria-controls="' + settingsHash.replace('#', '') + '"]');
			if (tab) {
				// only if it exists
				st_activateTab(settingsTabs, tabs, tab, null);
			}
			// If nothing was activated, activate the first one
			if (!settingsTabs.querySelector('button.active')) {
				settingsTabs.querySelector('#b_settingtype-main').classList.add('active');
				settingsTabs.querySelector('#b_settingtype-main').setAttribute('aria-selected', 'true');
				settingsTabs.querySelector('#settingtype-main').classList.add('active', 'show');
			}
		}
	}
});

function st_activateTab(settingsTabs, tabs, tab, e) {
	tabs.forEach(tab => {
		tab.classList.remove('active');
		tab.setAttribute('aria-selected', 'false');
		tab.tabIndex = -1;
	});
	tab.setAttribute('aria-selected', 'true');
	tab.tabIndex = 0;
	tab.classList.add('active');
	settingsTabs.querySelectorAll('#themesettings_tabsContent > div').forEach(e => {
		e.classList.remove('active', 'show');
		if (e.id === tab.getAttribute('aria-controls')) {
			e.classList.add('active', 'show');
		}
	});

	// Add the type as an anchor to the action
	let form = settingsTabs.parentElement;
	form.action = form.action.split('#')[0] + '#' + tab.getAttribute('aria-controls');
}
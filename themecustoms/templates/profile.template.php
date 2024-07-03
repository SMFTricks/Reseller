<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

/**
 * Callback function for profile covers
 */
function template_profile_cover() : void
{
	global $txt, $context;

	echo '
		<dt>
			<strong><label for="id_cover">' , $txt['st_profile_cover_upload'], '</label></strong><br>
			', $context['st_profile_cover_upload_description'], '
		</dt>
		<dd>
			<input type="hidden" name="id_cover">
			<input type="file" size="44" name="id_cover" id="id_cover" accept="image/gif, image/jpeg, image/jpg, image/png, image/webp">
		</dd>';
}
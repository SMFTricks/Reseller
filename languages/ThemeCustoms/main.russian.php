<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

// Additional Settings
$txt['current_theme'] = 'Настройки темы';
$txt['st_main'] = 'Основные';
$txt['st_additional_settings'] = 'Дополнительные настройки';
$txt['st_info_center'] = 'Инфоцентр';
$txt['st_sidebar'] = 'Сайдбар';
$txt['st_header'] = 'Шапка';
$txt['st_footer'] = 'Подвал';
$txt['st_boards'] = 'Разделы';

// Regular description
$txt['st_about'] = 'О нас';
$txt['st_description_default'] = 'Добро пожаловать в сообщество!';
$txt['st_description'] = 'Описание форума';
/* Argument: $txt['st_description_default'] */
$txt['st_description_desc'] = 'Добавьте описание форума. По умолчанию: <span class="smalltext">%1$s</span>';
$txt['st_description_title'] = 'Заголовок описания';
/* Argument: $txt['st_about'] */
$txt['st_description_title_desc'] = 'Используйте собственный заголовок для описания. По умолчанию: <span class="smalltext">%1$s</span>';

// Separate sticky topics
$txt['st_sticky_topic'] = 'Важные темы';
$txt['st_normal_topic'] = 'Обычные темы';
$txt['st_separate_sticky_locked'] = 'Отделить закреплённые темы';
$txt['st_separate_sticky_locked_desc'] = 'Благодаря этому вы можете разделить закреплённые темы и обычные темы.';

// Topic actions and information
$txt['st_nocomments'] = 'Для этой темы нет комментариев. Хотите стать первым?';
$txt['st_topic_locked'] = 'Извините, эта тема закрыта. Отвечать могут только администраторы и модераторы.';

// Servers
$txt['st_servers_title'] = 'Серверы SA';
$txt['st_server_samp_ver'] = 'Версия SAMP';
$txt['st_server_1_enable'] = 'Включить сервер 1';
$txt['st_server_2_enable'] = 'Включить сервер 2';
$txt['st_server_3_enable'] = 'Включить сервер 3';
$txt['st_server_1_guests'] = 'Разрешить гостям видеть информацию о сервере';
$txt['st_server_2_guests'] = 'Разрешить гостям видеть информацию о сервере';
$txt['st_server_3_guests'] = 'Разрешить гостям видеть информацию о сервере';
$txt['st_server_1_name'] = 'Хост 1 сервера';
$txt['st_server_2_name'] = 'Хост 2 сервера';
$txt['st_server_3_name'] = 'Хост 3 сервера';
$txt['st_server_1_ip'] = 'IP 1 сервера';
$txt['st_server_2_ip'] = 'IP 2 сервера';
$txt['st_server_3_ip'] = 'IP 3 сервера';
$txt['st_server_1_mode'] = 'Игровой режим 1 сервера';
$txt['st_server_2_mode'] = 'Игровой режим 2 сервера';
$txt['st_server_3_mode'] = 'Игровой режим 3 сервера';
$txt['st_server_link'] = 'Ссылка на сервер';
$txt['st_server_link_desc'] = 'Установите ссылку на сервер, если она доступна';
$txt['st_server_discord'] = 'Ссылка на Discord';
$txt['st_server_discord_desc'] = 'Установите отдельную ссылку на Discord, если она доступна';
$txt['st_game_mode'] = 'Игровой режим: ';
$txt['st_game_players'] = 'Игроки: ';
$txt['st_game_hostname'] = 'Хост: ';
$txt['st_game_serverip'] = 'IP сервера: ';
$txt['st_game_sampver'] = 'Версия SAMP: ';

// Main Menu
$txt['st_menu_settings'] = 'Настройка меню';
$txt['st_disable_menu_icons'] = 'Отключить иконки в главном меню';
$txt['st_disable_menu_icons_desc'] = 'Это отключит иконки главного меню, изображения и классы.';
$txt['st_remove_items'] = 'Удалить пункты меню';
$txt['st_remove_items_desc'] = 'Здесь вы можете поместить любое действие, которое хотите удалить из массива меню.<br><span class="smalltext">Несколько значений нужно разделять запятой. Например: <i>admin,profile,mlist</i></span>';
$txt['st_enable_community'] = 'Отображать кнопку «Сообщество»';
$txt['st_community'] = 'Сообщество';
$txt['forum'] = 'Форум';
$txt['st_enable_community_desc'] = 'Это добавит кнопку «Сообщество», которая будет находиться внутри всех ваших кнопок форума, кроме тех, которые вы укажете ниже. Кнопка «Начало» в меню сохранится, но вы можете удалить её с помощью другой настройки.';
$txt['st_not_community'] = 'Исключить действия из сообщества';
$txt['st_not_community_desc'] = 'Исключите из кнопки «Сообщество» любой элемент, используя действие.<br><span class="smalltext">Несколько значений нужно разделять запятой. Например: <i>admin,moderate</i></span>';
$txt['st_community_forum'] = 'Перенаправление кнопки «Сообщество» на ?action=forum';
$txt['st_community_forum_desc'] = 'Используйте этот параметр, если вы установили мод портала, иначе он отобразит страницу ошибки.';
$txt['st_collapse_menu'] = 'Всегда показывать боковое меню в режиме просмотра рабочего стола';
$txt['st_footer_actions'] = 'Действия в нижнем колонтитуле меню';
$txt['st_footer_actions_desc'] = 'Здесь вы можете указать любое действие, которое хотите отобразить в нижнем колонтитуле/нижнем меню. Несколько значений нужно разделять запятой. Например: <i>admin,profile,mlist</i>';
$txt['st_loginlogout_menu'] = 'Добавить кнопки %1$s, %2$s и %3$s в главное меню';

// Special Menu
$txt['st_special_menu'] = 'Специальные пункты меню';

// Avatar settings
$txt['st_avatar_settings'] = 'Настройка аватаров';
$txt['st_enable_avatars_boards'] = 'Отображать аватары в разделах';
$txt['st_enable_avatars_topics'] = 'Отображать аватары в списке тем';
$txt['st_enable_avatars_recent'] = 'Отображать аватары в списке последних сообщений (инфоцентр)';
$txt['st_enable_avatars_online'] = 'Отображать аватары в списке «Кто онлайн»';

// General strings
$txt['st_stats'] = 'Статистика';
$txt['st_golink'] = 'Ссылка «Перейти»';
$txt['st_menu'] = 'Меню';
$txt['st_remember'] = 'Запомнить меня';
$txt['close'] = 'Закрыть';
$txt['st_gotop'] = 'Перейти вверх';
$txt['st_config'] = 'Конфигурация';
$txt['st_information'] = 'Информация';
$txt['st_tasks'] = 'Задачи';
$txt['pm'] = 'Личные сообщения';
$txt['see_all'] = 'Отобразить все';
$txt['see_more'] = 'Отобразить ещё';
$txt['st_all_rights'] = 'Все права защищены';
$txt['st_other_settings'] = 'Другие настройки';
$txt['st_join'] = 'Присоединиться';
$txt['sort_by'] = 'Сортировка';
$txt['st_profile_cover'] = 'Обложка профиля';
$txt['st_news_prom'] = 'Новости и акции';
$txt['st_previous'] = 'Предыдущая';
$txt['st_next'] = 'Следующая';

// Colorpicker
$txt['st_colorpicker1_admin'] = 'Выберите основной цвет темы';
$txt['st_colorpicker1_profile'] = 'Выберите основной цвет темы';
$txt['st_colorpicker2_admin'] = 'Выберите дополнительный цвет темы';
$txt['st_colorpicker2_profile'] = 'Выберите дополнительный цвет темы';
$txt['st_colorpicker_allowuser'] = 'Разрешить пользователям менять цвет темы';
$txt['st_colpick_primary'] = 'Основной цвет';
$txt['st_colpick_secondary'] = 'Вторичный цвет';

// Top links
$txt['st_custom_links'] = 'Пользовательские ссылки';
$txt['st_custom_links_enabled'] = 'Включить пользовательские ссылки';
$txt['st_custom_link_other'] = 'Другие ссылки';
$txt['st_custom_link_title'] = 'Название для ссылки';
$txt['st_custom_link_url'] = 'Задайте URL-адрес для ссылки';
$txt['st_custom_link'] = 'Пользовательский URL %d';
$txt['st_custom_link_default'] = 'Ссылка %d';

// Header
$txt['st_header_background'] = 'URL-адрес фона шапки';
$txt['st_header_background_desc'] = 'Пользовательский фон для шапки. <i>Рекомендуется только в том случае, если изображения карусели отключены</i>';
$txt['st_enable_mainheader'] = 'Отображать шапку';
$txt['st_enable_mainheader_desc'] = 'Выводит основной заголовок над деревом ссылок с фоновым изображением. <i>Он не будет отображаться, если включена карусель</i>';
$txt['st_mainheader_custom'] = 'URL-адрес пользовательского изображения заголовка';
$txt['st_mainheader_custom_desc'] = 'Заменит изображение по умолчанию на то, которое указано в URL-адресе.';

// Info Center
$txt['st_statistics_background'] = 'URL-адрес фона блока статистики';
$txt['st_statistics_background_desc'] = 'Пользовательский фон для статистики';
$txt['st_disable_info_center'] = 'Отключить инфоцентр';
$txt['st_disable_info_center_desc'] = 'Это отключит информационный центр, независимо от разрешений.';
$txt['st_info_center_position'] = 'Позиция';
$txt['st_info_center_position_desc'] = 'Выберите расположение информационного центра. Вы можете настроить его так, чтобы он выглядел как боковая панель.';
$txt['st_info_center_right'] = 'Справа';
$txt['st_info_center_left'] = 'Слева';
$txt['st_info_center_top'] = 'Сверху';
$txt['st_info_center_bottom'] = 'Снизу';
$txt['st_info_center_width'] = 'Ширина';
$txt['st_info_center_width_desc'] = 'Установите пользовательскую ширину для информационного центра. Пример: <i>300px, 50%</i>. <span class="smalltext">По умолчанию: 100%</span>';
$txt['st_info_center_columns'] = 'Количество колонок';
$txt['st_info_center_columns_desc'] = 'Установите количество колонок для информационного центра. Это не работает для левых или правых позиций.';

// Social Networks
$txt['st_social'] = 'Социальные сети';
$txt['st_facebook'] = 'Facebook';
$txt['st_facebook_username'] = 'Имя пользователя Facebook';
$txt['st_twitter'] = 'Twitter';
$txt['st_twitter_username'] = 'Имя пользователя Twitter';
$txt['st_youtube'] = 'YouTube';
$txt['st_youtube_link'] = 'Ссылка на Youtube';
$txt['st_tiktok'] = 'TikTok';
$txt['st_tiktok_username'] = 'Имя пользователя TikTok';
$txt['st_instagram'] = 'Instagram';
$txt['st_instagram_username'] = 'Имя пользователя Instagram';
$txt['st_discord'] = 'Discord';
$txt['st_discord_link'] = 'Ссылка на Discord';
$txt['st_twitch'] = 'Twitch';
$txt['st_twitch_username'] = 'Имя пользователя Twitch';
$txt['st_steam'] = 'Steam';
$txt['st_steam_link'] = 'Ссылка на Steam';
$txt['st_github'] = 'GitHub';
$txt['st_github_link'] = 'Ссылка на GitHub';
$txt['st_linkedin'] = 'LinkedIn';
$txt['st_linkedin_link'] = 'Ссылка на LinkedIn';
$txt['st_rss_url'] = 'Адрес ленты RSS';
$txt['st_rss'] = 'Лента RSS';
$txt['st_social_desc'] = 'Оставьте пустым для отключения.';
/* Argument: $scripturl */
$txt['st_rss_url_desc'] = 'URL-адрес форума по умолчанию: <em>%1$s?action=.xml;type=rss</em>';

// Categories
$txt['st_catcover_enable'] = 'Отображать обложки в категориях';
/* Argument: $settings['theme_dir'] */
$txt['st_catcover_enable_desc'] = 'Будет отображена обложка для каждой категории.<br>Если вам нужно загрузить их вручную, вы можете сделать это по этому пути:<br><em class="smalltext">%1$s/images/catcover/{category id}.jpg</em>.<br>Если у вас есть дополнение Categories Cover («Обложка категорий»), после включения этого параметра у вас появится возможность загрузки ниже.';
$txt['st_enable_colcategories'] = 'Отображать категории в колонках';
$txt['st_enable_colcategories_desc'] = 'Будет отображаться по две категории в строке';

// Theme Effects
$txt['st_enable_tooltips'] = 'Включить всплывающие подсказки заголовков';
$txt['st_enable_tooltips_desc'] = 'Это включит всплывающие подсказки при наведении курсора мыши, если элемент имеет атрибут заголовка.';
$txt['st_enable_nice_scroll'] = 'Включить NiceScroll';
$txt['st_enable_nice_scroll_desc'] = 'Этот параметр изменит стиль полосы прокрутки.';
$txt['st_disable_theme_effects'] = 'Отключить эффекты темы';
$txt['st_disable_theme_effects_desc'] = 'Эта опция отключит анимацию, используемую в теме.';
$txt['st_enable_tooltips'] = 'Включить всплывающие подсказки заголовков';

// SM Descriptive Bar
$txt['st_list_of_topics'] = 'Список тем';
$txt['st_welcome_to'] = 'Добро пожаловать';
$txt['st_list_topics'] = 'Список тем раздела';
$txt['st_disc_topic'] = 'Это обсуждение темы';
$txt['st_the_board'] = 'раздел';

// Width
$txt['st_custom_width'] = 'Ширина форума';
$txt['st_custom_width_desc'] = 'Установите ширину форума. Примеры: 950px, 80%, 1240px.';

// Site Color
$txt['st_site_color'] = 'Цвет сайта';
$txt['st_site_color_desc'] = 'Указывает на предлагаемый цвет, который пользовательские агенты (браузер) должны использовать для настройки отображения страницы или окружающего пользовательского интерфейса. <em class="smalltext">По умолчанию: #567c8f</em>';

// Color Variants
$txt['st_color'] = 'Настройка цвета';
$txt['st_color_variants'] = 'Цветовые варианты темы';
$txt['st_color_variants_javascript'] = 'Использовать JavaScript для изменения цвета';
$txt['st_color_variants_javascript_desc'] = 'Это изменит вариант темы с использованием JavaScript и автоматически обновит профиль.';
$txt['variant_pick'] = 'Выберите вариант цвета';
$txt['variant_default'] = 'По умолчанию';
$txt['variant_red'] = 'Красный';
$txt['variant_green'] = 'Зелёный';
$txt['variant_blue'] = 'Синий';
$txt['variant_yellow'] = 'Жёлтый';
$txt['variant_orange'] = 'Оранжевый';
$txt['variant_purple'] = 'Фиолетовый';
$txt['variant_pink'] = 'Розовый';
$txt['variant_brown'] = 'Коричневый';
$txt['variant_grey'] = 'Серый';
$txt['variant_black'] = 'Чёрный';
$txt['variant_white'] = 'Белый';

// Dark Mode
$txt['st_theme_mode'] = 'Режим темы';
$txt['st_theme_mode_select'] = 'Выберите режим темы';
$txt['st_theme_mode_default'] = 'Режим темы по умолчанию';
$txt['st_theme_mode_default_desc'] = 'Если выбор пользователя отключен, этот режим будет использоваться по умолчанию и будет заблокирован.';
$txt['st_enable_dark_mode'] = 'Разрешить пользователям выбирать режим';
$txt['st_dark_mode'] = 'Тёмный режим';
$txt['st_light_mode'] = 'Светлый режим';
$txt['st_auto_mode'] = 'Автоматический режим';

// Theme information
$txt['st_themeinfo_details'] = 'Сведения о теме';
$txt['st_themeinfo_author'] = 'Автор';
$txt['st_themeinfo_author_dashboard'] = 'Дашборд автора';
$txt['st_themeinfo_name'] = 'Название темы';
$txt['st_themeinfo_version'] = 'Версия темы';
$txt['st_themeinfo_github'] = 'Репозиторий Github';
$txt['st_themeinfo_github_desc'] = 'Трекер ошибок и многое другое.';
$txt['st_themeinfo_support'] = 'Поддержка';
$txt['st_themeinfo_support_topic'] = 'Тема поддержки';
$txt['st_themeinfo_support_topic_desc'] = 'Поддержка и обсуждение этой темы.';
$txt['st_themeinfo_support_board'] = 'Форум поддержки';
$txt['st_themeinfo_support_board_desc'] = 'Форум поддержки и обсуждение этой темы.';
$txt['st_themeinfo_review'] = 'Обзор';
$txt['st_themeinfo_review_desc'] = 'Страница этой темы в каталоге тем.';
$txt['st_themeinfo_smfversion'] = 'Версия SMF';

// Addons
$txt['themecustoms_addon_package'] = 'Дополнения к теме';
$txt['install_themecustoms_addon'] = 'Установить дополнение';
$txt['uninstall_themecustoms_addon'] = 'Удалить дополнение';

// CDN
$txt['st_cdn_source'] = 'Источник CDN';
$txt['st_cdn_source_desc'] = 'Выберите источник для загрузки этой библиотеки или скрипта.';
$txt['st_cdn_local'] = 'Локальный';
$txt['st_cdn_google'] = 'Google';
$txt['st_cdn_cloudflare'] = 'Cloudflare';
$txt['st_fonts'] = 'Шрифты';
$txt['st_fonts_desc'] = 'Это определит источник, используемый для загрузки любых пользовательских шрифтов, используемых в теме.';
$txt['st_jqueryui'] = 'jQuery UI';
$txt['st_fontawesome'] = 'Font Awesome';
<?php
/*
Available variables
%plural_name%   - category name in plural (e.g. Mexican Restaurants)
%page%          - the page number
%limit%         - the number of items per page
%state_name%    - the state name
%state_abbr%    - two letter state abbreviation (e.g. CA)
%city_name%     - the city name
%neighborhood_name% - the neighborhood name
%meta_desc_str% - this variable will be replaced with the first 3 business names (e.g. "Joey's place, Jane's Café, Mike's Joint")
*/

// case: category = defined, location = country (e.g. mexican restaurant in United States)
$txt_html_title_1 = "%plural_name% в %default_country% - страница %page%";
$txt_meta_desc_1  = "Пользуется спросом %plural_name% в Соединенных Штатах. Показывать %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

// case: category = defined, state = defined (e.g. mexican restaurant in California)
$txt_html_title_2 = "%plural_name% в %state_name% - Страница %page%";
$txt_meta_desc_2  = "Пользуется спросом %plural_name% в %state_name%. Показывать %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

// case: category = undefined, state = defined (e.g. all types of venues in California)
$txt_html_title_3 = "Популярные места в %state_name% - Страница %page%";
$txt_meta_desc_3  = "Популярные места в %state_name%. Показывать %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
$txt_html_title_4 = "%plural_name% в %city_name%, %state_abbr% - Страница %page%";
$txt_meta_desc_4  = "Пользуется спросом %plural_name% в %city_name%. Показывать %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
$txt_html_title_5 = "Популярные места в %city_name% - Страница %page%";
$txt_meta_desc_5  = "Популярные места в %city_name%. Показывать %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
$txt_html_title_6 = "%plural_name% в %neighborhood_name%, %city_name%, %state_abbr% - Страница %page%";
$txt_meta_desc_6  = "Пользуется спросом %plural_name% в %neighborhood_name%, %city_name%. Показывать %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

// case: category = undefined, neighborhood = defined (e.g. all types of venues in Downtown)
$txt_html_title_7 = "Популярные места в %neighborhood_name%, %city_name% - Страница %page%";
$txt_meta_desc_7  = "Популярные места в %neighborhood_name%, %city_name%. Показывать  %limit% результатов на странице %page%. %meta_desc_str% и прочие.";

$txt_results          = "Результат(ы)";
$txt_temp_empty_msg_1 = "По вашему поиску получено 0 результатов.";
$txt_temp_empty_msg_2 = " Если вы хотите отнести свой бизнес к этой категории, пожалуйста, нажмите ниже:";
$txt_temp_empty_msg_3 = "Внести бизнес";
$txt_pager_page1      = "Страница 1";
$txt_pager_lastpage   = "Последняя страница";

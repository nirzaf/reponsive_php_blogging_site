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
$txt_html_title_1 = "%plural_name% dans %default_country% - Page %page%";
$txt_meta_desc_1  = "%plural_name% populaires aux Etats Unis. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

// case: category = defined, state = defined (e.g. mexican restaurant in California)
$txt_html_title_2 = "%plural_name% dans %state_name% - Page %page%";
$txt_meta_desc_2  = "%plural_name% populaires dans %state_name%. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

// case: category = undefined, state = defined (e.g. all types of venues in California)
$txt_html_title_3 = "Places populaires dans %state_name% - Page %page%";
$txt_meta_desc_3  = "Places populaires dans %state_name%. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
$txt_html_title_4 = "%plural_name% dans %city_name%, %state_abbr% - Page %page%";
$txt_meta_desc_4  = "%plural_name% populaires dans %city_name%. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
$txt_html_title_5 = "Places populaires dans %city_name% - Page %page%";
$txt_meta_desc_5  = "Places populaires dans %city_name%. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
$txt_html_title_6 = "%plural_name% dans %neighborhood_name%, %city_name%, %state_abbr% - Page %page%";
$txt_meta_desc_6  = "%plural_name% populaires dans %neighborhood_name%, %city_name%. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

// case: category = undefined, neighborhood = defined (e.g. all types of venues in Downtown)
$txt_html_title_7 = "Places populaires dans %neighborhood_name%, %city_name% - Page %page%";
$txt_meta_desc_7  = "Places populaires dans %neighborhood_name%, %city_name%. Montrant %limit% résultats sur la page %page%. %meta_desc_str% et autres.";

$txt_results          = "Résultat(s)";
$txt_temp_empty_msg_1 = "Votre recherche a donnée 0 résultas.";
$txt_temp_empty_msg_2 = "Si vous voulez lister votre entreprise dans cette catégorie, veuillez cliquer ci-dessous:";
$txt_temp_empty_msg_3 = "Lister Entreprise";
$txt_pager_page1      = "Page 1";
$txt_pager_lastpage   = "Dernière page";
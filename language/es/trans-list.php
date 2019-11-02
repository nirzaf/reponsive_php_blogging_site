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
$txt_html_title_1 = "%plural_name% en %default_country% - Page %page%";
$txt_meta_desc_1  = "%plural_name% populares en los Estados Unidos. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

// case: category = defined, state = defined (e.g. mexican restaurant in California)
$txt_html_title_2 = "%plural_name% en %state_name% - Page %page%";
$txt_meta_desc_2  = "%plural_name% populares en %state_name%. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

// case: category = undefined, state = defined (e.g. all types of venues in California)
$txt_html_title_3 = "Lugares populares en %state_name% - Page %page%";
$txt_meta_desc_3  = "Lugares populares en %state_name%. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
$txt_html_title_4 = "%plural_name% en %city_name%, %state_abbr% - Page %page%";
$txt_meta_desc_4  = "%plural_name% populares en %city_name%. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
$txt_html_title_5 = "Lugares populares en %city_name% - Page %page%";
$txt_meta_desc_5  = "Lugares populares en %city_name%. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
$txt_html_title_6 = "%plural_name% en %neighborhood_name%, %city_name%, %state_abbr% - Page %page%";
$txt_meta_desc_6  = "%plural_name% populares en %neighborhood_name%, %city_name%. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

// case: category = undefined, neighborhood = defined (e.g. all types of venues in Downtown)
$txt_html_title_7 = "Lugares populares en %neighborhood_name%, %city_name% - Page %page%";
$txt_meta_desc_7  = "Lugares populares en %neighborhood_name%, %city_name%. Mostrando %limit% resultados en la página %page%. %meta_desc_str% y otros.";

$txt_results          = "Resultado(s)";
$txt_temp_empty_msg_1 = "Tu búsqueda tuvo 0 resultados.";
$txt_temp_empty_msg_2 = "Si deseas enlistar tu negocio en esta categoría, por favor haz clic a continuación:";
$txt_temp_empty_msg_3 = "Enlistar negocio";
$txt_pager_page1      = "Página 1";
$txt_pager_lastpage   = "Última página";
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
$txt_html_title_1 = "%plural_name% no %default_country% - Página %page%";
$txt_meta_desc_1  = "Popular %plural_name% no %default_country%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

// case: category = defined, state = defined (e.g. mexican restaurant in California)
$txt_html_title_2 = "%plural_name% - %state_name% - Página %page%";
$txt_meta_desc_2  = "%plural_name% em destaque - %state_name%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

// case: category = undefined, state = defined (e.g. all types of venues in California)
$txt_html_title_3 = "Empresas em destaque - %state_name% - Página %page%";
$txt_meta_desc_3  = "Empresas em destaque - %state_name%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
$txt_html_title_4 = "%plural_name% - %city_name%, %state_abbr% - Página %page%";
$txt_meta_desc_4  = "%plural_name% em destaque em %city_name%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
$txt_html_title_5 = "Empresas em destaque - %city_name% - Página %page%";
$txt_meta_desc_5  = "Empresas em destaque - %city_name%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
$txt_html_title_6 = "%plural_name% - %neighborhood_name%, %city_name%, %state_abbr% - Página %page%";
$txt_meta_desc_6  = "%plural_name% - %neighborhood_name%, %city_name%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

// case: category = undefined, neighborhood = defined (e.g. all types of venues in Downtown)
$txt_html_title_7 = "Empresas em destaque - %neighborhood_name%, %city_name% - Página %page%";
$txt_meta_desc_7  = "Empresas em destaque - %neighborhood_name%, %city_name%. Mostrando %limit% resultados na página %page%. %meta_desc_str% e mais.";

$txt_results          = "Resultados";
$txt_temp_empty_msg_1 = "Sua busca retornou 0 resultados.";
$txt_temp_empty_msg_2 = "Se deseja anunciar sua empresa nessa categoria, clique abaixo:";
$txt_temp_empty_msg_3 = "Anunciar sua empresa";
$txt_pager_page1      = "Página 1";
$txt_pager_lastpage   = "Last Página";
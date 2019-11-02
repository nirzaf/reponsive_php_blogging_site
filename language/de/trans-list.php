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
%meta_desc_str% - this variable will be replaced with the first 3 business names (e.g. "Joey's place, Jane's CafÃ©, Mike's Joint")
*/

// case: category = defined, location = country (e.g. mexican restaurant in United States)
$txt_html_title_1 = "%plural_name% in %default_country% - Seite %page%";
$txt_meta_desc_1  = "Beliebte %plural_name% in den Vereinigten Staaten. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

// case: category = defined, state = defined (e.g. mexican restaurant in California)
$txt_html_title_2 = "%plural_name% in %state_name% - Seite %page%";
$txt_meta_desc_2  = "Beliebte %plural_name% in %state_name%. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

// case: category = undefined, state = defined (e.g. all types of venues in California)
$txt_html_title_3 = "Beliebte Orte in %state_name% - Seite %page%";
$txt_meta_desc_3  = "Beliebte Orte in %state_name%. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
$txt_html_title_4 = "%plural_name% in %city_name%, %state_abbr% - Seite %page%";
$txt_meta_desc_4  = "Beliebte %plural_name% in %city_name%. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
$txt_html_title_5 = "Beliebte Orte in %city_name% - Seite %page%";
$txt_meta_desc_5  = "Beliebte Orte in %city_name%. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
$txt_html_title_6 = "%plural_name% in %neighborhood_name%, %city_name%, %state_abbr% - Seite %page%";
$txt_meta_desc_6  = "Beliebte %plural_name% in %neighborhood_name%, %city_name%. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

// case: category = undefined, neighborhood = defined (e.g. all types of venues in Downtown)
$txt_html_title_7 = "Beliebte Orte in %neighborhood_name%, %city_name% - Seite %page%";
$txt_meta_desc_7  = "Beliebte Orte in %neighborhood_name%, %city_name%. Zeige %limit% Ergebnise auf Seite %page%. %meta_desc_str% und anderen.";

$txt_results          = "Ergebnis(se)";
$txt_temp_empty_msg_1 = "Deine Suche ergab 0 Treffer.";
$txt_temp_empty_msg_2 = "Falls du dein Unternehmen in dieser Kategorie auflisten möchtest, klicke bitte unten:";
$txt_temp_empty_msg_3 = "Unternehmen auflisten";
$txt_pager_page1      = "Seite 1";
$txt_pager_lastpage   = "Letzte Seite";

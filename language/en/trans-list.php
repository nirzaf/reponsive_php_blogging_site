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
$txt_html_title_1 = "%plural_name% in %default_country% - Page %page%";
$txt_meta_desc_1  = "Popular %plural_name% in the United States. Showing %limit% results on page %page%. %meta_desc_str% and others.";

// case: category = defined, state = defined (e.g. mexican restaurant in California)
$txt_html_title_2 = "%plural_name% in %state_name% - Page %page%";
$txt_meta_desc_2  = "Popular %plural_name% in %state_name%. Showing %limit% results on page %page%. %meta_desc_str% and others.";

// case: category = undefined, state = defined (e.g. all types of venues in California)
$txt_html_title_3 = "Popular places in %state_name% - Page %page%";
$txt_meta_desc_3  = "Popular places in %state_name%. Showing %limit% results on page %page%. %meta_desc_str% and others.";

// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
$txt_html_title_4 = "%plural_name% in %city_name%, %state_abbr% - Page %page%";
$txt_meta_desc_4  = "Popular %plural_name% in %city_name%. Showing %limit% results on page %page%. %meta_desc_str% and others.";

// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
$txt_html_title_5 = "Popular places in %city_name% - Page %page%";
$txt_meta_desc_5  = "Popular places in %city_name%. Showing %limit% results on page %page%. %meta_desc_str% and others.";

// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
$txt_html_title_6 = "%plural_name% in %neighborhood_name%, %city_name%, %state_abbr% - Page %page%";
$txt_meta_desc_6  = "Popular %plural_name% in %neighborhood_name%, %city_name%. Showing %limit% results on page %page%. %meta_desc_str% and others.";

// case: category = undefined, neighborhood = defined (e.g. all types of venues in Downtown)
$txt_html_title_7 = "Popular places in %neighborhood_name%, %city_name% - Page %page%";
$txt_meta_desc_7  = "Popular places in %neighborhood_name%, %city_name%. Showing %limit% results on page %page%. %meta_desc_str% and others.";

$txt_results          = "Result(s)";
$txt_temp_empty_msg_1 = "Your search returned 0 results.";
$txt_temp_empty_msg_2 = "If you'd like to list your business in this category, please click below:";
$txt_temp_empty_msg_3 = "List Business";
$txt_pager_page1      = "Page 1";
$txt_pager_lastpage   = "Last Page";
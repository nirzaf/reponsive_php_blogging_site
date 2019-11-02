-- phpMyAdmin SQL Dump
-- version 4.4.13.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 13, 2017 at 09:44 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `directoryapp_108`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

CREATE TABLE IF NOT EXISTS `business_hours` (
  `id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `day` int(1) NOT NULL,
  `open` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `close` char(4) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cats`
--

CREATE TABLE IF NOT EXISTS `cats` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plural_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `iconfont_tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cat_order` int(10) NOT NULL DEFAULT '0',
  `cat_status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=518 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cats`
--

INSERT INTO `cats` (`id`, `name`, `plural_name`, `parent_id`, `iconfont_tag`, `cat_order`, `cat_status`) VALUES
(1, 'Auto', 'Auto', 0, '<i class="flaticon-transport-1"></i>', 0, 1),
(29, 'Bar & Restaurant', 'Bars & Restaurants', 0, '<i class="flaticon-restaurant"></i>', 0, 1),
(74, 'Computer', 'Computers', 0, '<i class="flaticon-computer"></i>', 0, 1),
(83, 'Diet & Health', 'Diet & Health', 0, '<i class="flaticon-heart-beats"></i>', 0, 1),
(99, 'Entertainment', 'Entertainment', 0, '<i class="flaticon-tickets"></i>', 0, 1),
(133, 'Fitness', 'Fitness', 0, '<i class="flaticon-weights"></i>', 0, 1),
(144, 'Home', 'Home', 0, '<i class="flaticon-paint"></i>', 0, 1),
(176, 'Insurance', 'Insurance', 0, '<i class="flaticon-home"></i>', 0, 1),
(218, 'Store', 'Stores', 0, '<i class="flaticon-commerce"></i>', 0, 1),
(300, 'Pet & Animal', 'Pets & Animals', 0, '<i class="flaticon-animals"></i>', 0, 1),
(317, 'Professional Service', 'Professional Services', 0, '<i class="flaticon-cogwheel"></i>', 0, 1),
(345, 'Real Estate', 'Real Estate', 0, '<i class="flaticon-building"></i>', 0, 1),
(390, 'Education', 'Education', 0, '<i class="flaticon-book"></i>', 0, 1),
(420, 'Shopping', 'Shopping', 0, '<i class="flaticon-commerce-1"></i>', 0, 1),
(452, 'Travel', 'Travel', 0, '<i class="flaticon-transport"></i>', 0, 1),
(510, 'Beauty', 'Beauty Salon and Spas', 0, '<i class="flaticon-beauty"></i>', 2, 1),
(516, 'Pizza', 'Pizza', 29, '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `state_id` int(11) DEFAULT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities_feat`
--

CREATE TABLE IF NOT EXISTS `cities_feat` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `property` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `type`, `property`, `value`) VALUES
(1, 'email', 'admin_email', 'admin@example.com'),
(2, 'email', 'dev_email', 'dev@example.com'),
(3, 'email', 'smtp_server', 'mail.example.com'),
(4, 'email', 'smtp_user', 'admin@example.com'),
(5, 'email', 'smtp_pass', 'YOUR_SMTP_PASSWORD'),
(6, 'email', 'smtp_port', '26'),
(7, 'api', 'google_key', 'YOUR_GOOGLE_API_KEY'),
(8, 'display', 'items_per_page', '20'),
(9, 'display', 'site_name', 'Business Directory'),
(10, 'display', 'country_name', 'the United States'),
(11, 'display', 'default_country_code', 'us'),
(12, 'display', 'default_city_slug', 'new-york'),
(13, 'display', 'default_loc_id', '1'),
(14, 'display', 'timezone', 'America/Los_Angeles'),
(15, 'maps', 'default_lat', '37.3002752813443'),
(16, 'maps', 'default_lng', '-94.482421875'),
(17, 'display', 'html_lang', 'en'),
(18, 'display', 'max_pics', '15'),
(19, 'display', 'paypal_merchant_id', 'PAYPAL_MERCHANT_ID'),
(20, 'display', 'paypal_bn', 'Directoryapp'),
(21, 'display', 'paypal_checkout_logo_url', 'http://example.com/imgs/checkout_logo.png'),
(22, 'payment', 'currency_code', 'USD'),
(23, 'payment', 'currency_symbol', '$'),
(24, 'payment', 'paypal_locale', 'US'),
(25, 'payment', 'notify_url', 'https://example.com/ipn-handler.php'),
(26, 'payment', 'paypal_mode', '0'),
(27, 'payment', 'paypal_sandbox_merch_id', 'PAYPAL_SANDBOX_MERCHANT_ID'),
(28, 'api', 'facebook_key', 'FACEBOOK_KEY'),
(29, 'api', 'facebook_secret', 'FACEBOOK_SECRET'),
(30, 'api', 'twitter_key', 'TWITTER_KEY'),
(31, 'api', 'twitter_secret', 'TWITTER_SECRET'),
(32, 'payment', '_2checkout_mode', '0'),
(33, 'payment', '_2checkout_sid', '2CHECKOUT_ACCOUNT_ID'),
(34, 'payment', '_2checkout_sandbox_sid', '2CHECKOUT_SANDBOX_ACCOUNT_ID'),
(35, 'payment', '_2checkout_secret', '2CHECKOUT_SECRET'),
(36, 'payment', '_2checkout_currency_code', 'USD'),
(37, 'payment', '_2checkout_currency_symbol', '$'),
(38, 'payment', '_2checkout_lang', 'en'),
(39, 'payment', '_2checkout_notify_url', 'http://example.com/ipn-2checkout.php'),
(40, 'payment', 'mercadopago_mode', '0'),
(41, 'payment', 'mercadopago_client_id', 'MERCADO_PAGO_ID'),
(42, 'payment', 'mercadopago_client_secret', 'MERCADO_PAGO_CLIENT_SECRET'),
(43, 'payment', 'mercadopago_currency_id', 'BRL'),
(44, 'payment', 'mercadopago_notification_url', 'http://example.com/ipn-mercadopago.php'),
(100, 'plugin', 'plugin_contact_owner', 'a:3:{s:8:"question";s:6:"2 + 3?";s:6:"answer";s:1:"5";s:13:"email_subject";s:32:"Message from a DirectoryApp user";}'),
(101, 'payment', 'stripe_mode', '0'),
(102, 'payment', 'stripe_test_secret_key', 'YOUR_TEST_SECRET_KEY'),
(103, 'payment', 'stripe_test_publishable_key', 'YOUR_TEST_PUBLISHABLE_KEY'),
(104, 'payment', 'stripe_live_secret_key', 'YOUR_LIVE_SECRET_KEY'),
(105, 'payment', 'stripe_live_publishable_key', 'YOUR_LIVE_PUBLISHABLE_KEY'),
(106, 'payment', 'stripe_data_currency', 'usd'),
(107, 'payment', 'stripe_currency_symbol', '$'),
(108, 'payment', 'stripe_data_image', 'https://stripe.com/img/v3/home/twitter.png'),
(109, 'payment', 'stripe_data_description', 'Directoryapp membership');

-- --------------------------------------------------------

--
-- Table structure for table `contact_msgs`
--

CREATE TABLE IF NOT EXISTS `contact_msgs` (
  `id` int(11) NOT NULL,
  `sender_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_ip` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `place_id` int(11) NOT NULL,
  `msg` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country_abbr` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'country',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_id`, `country_name`, `country_abbr`, `slug`) VALUES
(1, 'United States', 'US', 'us');

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE IF NOT EXISTS `custom_fields` (
  `field_id` int(11) NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `values_list` text COLLATE utf8mb4_unicode_ci,
  `tooltip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `searchable` tinyint(1) NOT NULL DEFAULT '1',
  `field_order` int(11) NOT NULL DEFAULT '0',
  `field_status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `type`, `description`, `subject`, `body`) VALUES
(1, 'reset_pass', 'Reset password email', 'Reset your password - Business Directory', 'Hello,\r\n\r\nSomeone has requested a link to change your password on Business Directory. You can do this through the link below. \r\n\r\n%reset_link%\r\n\r\nIf you didn''t request this, please ignore this email. \r\n\r\nThanks,\r\n\r\nBusiness Directory'),
(2, 'signup_confirm', 'Signup email address confirmation email', 'Welcome to Business Directory! Please confirm your email', 'Hello,\r\n\r\nYou have signed up for Business Directory.\r\n\r\nIf you received this email by mistake, simply delete it. Your account will be removed if you don''t click the confirmation link below.\r\n\r\nConfirm: %confirm_link%\r\n\r\nThanks,\r\n\r\nBusiness Directory - http://yoursite.com'),
(4, 'subscr_failed', 'Subscription payment failed email', 'Subscription payment failed - Business Directory', 'Hello %username%,\n\nYour subscription payment failed. Please take moment to check your payment info, you may need to update the credit card expiration date, etc. You still have access, we''ll try again in a few days.\n\nThanks,\n\nBusiness Directory - http://yoursite.com'),
(5, 'subscr_signup', 'Subscription successful email', 'Thank you! Welcome to Business Directory', 'Hello %username%,\r\n\r\nYour subscription is active. The link to your listing is:\r\n\r\n%place_link%.\r\n\r\nYou can edit your listing at any moment by logging into your account.\r\n\r\nThanks,\r\n\r\nBusiness Directory - http://yoursite.com'),
(6, 'web_accept', 'One time payment successful email', 'Thank you! Welcome to Business Directory', 'Hello %username%,\r\n\r\nYour listing is active. The link to your listing is:\r\n\r\n%place_link%.\r\n\r\nYou can edit your listing at any moment by logging into your account.\r\n\r\nThanks,\r\n\r\nBusiness Directory - http://yoursite.com'),
(7, 'subscr_eot', 'Subscription expired email', 'Subscription expired - Business Directory', 'Hello %username%, \r\n\r\nYour subscription on Business Directory expired. The link to your listing is:\r\n\r\n%place_link%.\r\n\r\nThanks,\r\n\r\nBusiness Directory - http://yoursite.com'),
(8, 'web_accept_fail', 'One time payment failed email', 'Your most recent payment failed', 'Hi there,\n\nUnfortunately your most recent payment for your ad on our site was declined. This could be due to a change in your card number or your card expiring, cancelation of your credit card, or the bank not recognizing the payment and taking action to prevent it.\n\nPlease update your payment information as soon as possible by logging in here:\nhttps://yoursite.com/user/login''\n\nThanks,\n\nBusiness Directory - http://yoursite.com'),
(9, 'process_add_place', 'User submission notification', 'A new listing was submitted', 'Hello,\n\nA new listing was submitted on:\n\nhttp://yoursite.com/admin\n\nthanks'),
(10, 'process_edit_place', 'User edit listing notification', 'A new listing was edited', 'Hello,\r\n\r\nA user has edited a listing on:\r\n\r\nhttp://yoursite.com/admin\r\n\r\nthanks');

-- --------------------------------------------------------

--
-- Table structure for table `loggedin`
--

CREATE TABLE IF NOT EXISTS `loggedin` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `provider` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `neighborhoods`
--

CREATE TABLE IF NOT EXISTS `neighborhoods` (
  `neighborhood_id` int(10) NOT NULL,
  `neighborhood_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `neighborhood_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL,
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `page_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `page_contents` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `page_order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `page_title`, `page_slug`, `meta_desc`, `page_contents`, `page_group`, `page_order`) VALUES
(1, 'About', 'about', 'About us', '<h2>About Us</h2>\r\n\r\n<p>"Your Site" is a business/place search web application. Users can search for restaurants, nightlife spots, shops, services and other points of interest and leave reviews and tips for other users. Business owners can advertise their services on the site.</p>\r\n\r\n<p>Any business type and points of interest can be submitted to Coopcities. Examples are restaurants, nightlife spots, all kinds of shops, services, etc</p>', 'footer_menu', 0),
(2, 'Privacy Policy', 'privacy-policy', 'Privacy policy', '<h2>Privacy Policy</h2>\r\n\r\n		<p>At "Your Site" we are committed to safeguarding and preserving the privacy of our visitors. This Privacy Policy document (the "Policy") has been provided by the legal resource DIY Legals and reviewed and approved by their solicitors.</p>\r\n\r\n		<p>This Policy explains what happens to any personal data that you provide to us, or that we collect from you whilst you visit our site and how we use cookies on this website.</p>\r\n\r\n		<p>We do update this Policy from time to time so please do review this Policy regularly.</p>\r\n\r\n		<h3>Information That We Collect</h3>\r\n\r\n		<p>In running and maintaining our website we may collect and process the following data about you:</p>\r\n\r\n		<ul>\r\n		<li>Information about your use of our site including details of your visits such as pages viewed and the resources that you access. Such information includes traffic data, location data and other communication data.</li>\r\n		<li>Information provided voluntarily by you. For example, when you register for information or make a purchase.</li>\r\n		<li>Information that you provide when you communicate with us by any means.</li>\r\n		</ul>\r\n\r\n		<h3>Use of Cookies</h3>\r\n\r\n		<p>Cookies provide information regarding the computer used by a visitor. We may use cookies where appropriate to gather information about your computer in order to assist us in improving our website.</p>\r\n\r\n		<p>We may gather information about your general internet use by using the cookie. Where used, these cookies are downloaded to your computer and stored on the computer’s hard drive. Such information will not identify you personally; it is statistical data which does not identify any personal details whatsoever.</p>\r\n\r\n		<p>Our advertisers may also use cookies, over which we have no control. Such cookies (if used) would be downloaded once you click on advertisements on our website.</p>\r\n\r\n		<p>You can adjust the settings on your computer to decline any cookies if you wish. This can be done within the “settings” section of your computer. For more information please read the advice at AboutCookies.org.</p>\r\n\r\n		<h3>Use of Your Information</h3>\r\n\r\n		<p>We use the information that we collect from you to provide our services to you. In addition to this we may use the information for one or more of the following purposes:</p>\r\n\r\n		<ul>\r\n		<li>To provide information to you that you request from us relating to our products or services.</li>\r\n		<li>To provide information to you relating to other products that may be of interest to you. Such additional information will only be provided where you have consented to receive such information.</li>\r\n		<li>To inform you of any changes to our website, services or goods and products.</li>\r\n		</ul>\r\n\r\n		<p>If you have previously purchased goods or services from us we may provide to you details of similar goods or services, or other goods and services, that you may be interested in.</p>\r\n\r\n		<p><strong>We never give your details to third parties to use your data to enable them to provide you with information regarding unrelated goods or services.</strong></p>\r\n\r\n		<h3>Storing Your Personal Data</h3>\r\n\r\n		<p>In operating our website it may become necessary to transfer data that we collect from you to locations outside of the European Union for processing and storing. By providing your personal data to us, you agree to this transfer, storing and processing. We do our utmost to ensure that all reasonable steps are taken to make sure that your data is stored securely.</p>\r\n\r\n		<p>Unfortunately the sending of information via the internet is not totally secure and on occasion such information can be intercepted. We cannot guarantee the security of data that you choose to send us electronically, sending such information is entirely at your own risk.\r\n		</p>\r\n\r\n		<h3>Disclosing Your Information</h3>\r\n\r\n		<p>We will not disclose your personal information to any other party other than in accordance with this Privacy Policy and in the circumstances detailed below:</p>\r\n\r\n		<ul>\r\n		<li>In the event that we sell any or all of our business to the buyer.</li>\r\n		<li>Where we are legally required by law to disclose your personal information.</li>\r\n		<li>To further fraud protection and reduce the risk of fraud.</li>\r\n		</ul>\r\n\r\n		<h3>Third Party Links</h3>\r\n\r\n		<p>On occasion we include links to third parties on this website. Where we provide a link it does not mean that we endorse or approve that site''s policy towards visitor privacy. You should review their privacy policy before sending them any personal data.\r\n		</p>\r\n\r\n		<h3>Access to Information</h3>\r\n\r\n		<p>In accordance with the Data Protection Act 1998 you have the right to access any information that we hold relating to you. Please note that we reserve the right to charge a fee of £10 to cover costs incurred by us in providing you with the information.\r\n		</p>\r\n\r\n		<h3>Contacting Us</h3>\r\n\r\n		<p>Please do not hesitate to contact us regarding any matter relating to this Privacy and Cookies Policy via email at <a href="mailto:contact@yoursite.com">contact@yoursite.com</a>.\r\n		</p>', 'footer_menu', 0),
(3, 'index', 'index', '', '<p>hello world</p>', 'index_page', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pass_request`
--

CREATE TABLE IF NOT EXISTS `pass_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `photo_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `dir` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE IF NOT EXISTS `places` (
  `place_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `place_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cross_street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `neighborhood` int(10) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `inside` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `area_code` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `twitter` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `facebook` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `foursq_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `website` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci,
  `has_menu` tinyint(1) DEFAULT '0',
  `business_hours_info` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `submission_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `origin` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USER',
  `feat` tinyint(1) NOT NULL DEFAULT '0',
  `feat_home` tinyint(1) NOT NULL DEFAULT '0',
  `plan` int(11) DEFAULT NULL,
  `valid_until` datetime NOT NULL DEFAULT '9999-01-01 00:00:00',
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE IF NOT EXISTS `plans` (
  `plan_id` int(11) NOT NULL,
  `plan_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_description1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_description2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_description3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_description4` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_description5` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `plan_period` int(10) NOT NULL DEFAULT '0',
  `plan_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `plan_order` int(11) NOT NULL DEFAULT '0',
  `plan_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`plan_id`, `plan_type`, `plan_name`, `plan_description1`, `plan_description2`, `plan_description3`, `plan_description4`, `plan_description5`, `plan_period`, `plan_price`, `plan_order`, `plan_status`) VALUES
(1, 'monthly', 'Monthly', '<i class="fa fa-times" aria-hidden="true"></i> Featured', '<i class="fa fa-check" aria-hidden="true"></i>  Upload photos', '<i class="fa fa-check" aria-hidden="true"></i> Included at the top of city and categoy results', '<i class="fa fa-check" aria-hidden="true"></i>  Pay monthly', '', 0, '2.90', 5, 1),
(2, 'monthly_feat', 'Monthly Featured', '<i class="fa fa-check" aria-hidden="true"></i> Featured', '<i class="fa fa-check" aria-hidden="true"></i>  Upload photos', '<i class="fa fa-check" aria-hidden="true"></i> Included at the top of city and categoy results', '<i class="fa fa-check" aria-hidden="true"></i>  Pay monthly', '', 0, '3.90', 6, -1),
(3, 'one_time_feat', 'Long Term Featured', '<i class="fa fa-check" aria-hidden="true"></i> Featured', '<i class="fa fa-check" aria-hidden="true"></i>  Upload photos', '<i class="fa fa-check" aria-hidden="true"></i> Included at the top of city and categoy results', '<i class="fa fa-check" aria-hidden="true"></i> Pay once for a permanent ad', '', 4, '2.00', 4, 1),
(8, 'one_time', 'Long Term', '<i class="fa fa-times" aria-hidden="true"></i> Featured', '<i class="fa fa-check" aria-hidden="true"></i>  Upload photos', '<i class="fa fa-check" aria-hidden="true"></i> Included at the top of city and categoy results', '<i class="fa fa-check" aria-hidden="true"></i> No recurring fees', '', 3, '1.00', 3, 1),
(19, 'free', 'Free Listings', '<i class="fa fa-times" aria-hidden="true"></i> Featured', '<i class="fa fa-check" aria-hidden="true"></i> Upload photos', '<i class="fa fa-check" aria-hidden="true"></i> Included at the top of city and categoy results', '<i class="fa fa-check" aria-hidden="true"></i> Permanent ad', '<i class="fa fa-check" aria-hidden="true"></i> 100% Free', 1, '0.00', 1, 1),
(20, 'free_feat', 'Free Featured', '<i class="fa fa-check" aria-hidden="true"></i> Featured', '<i class="fa fa-check" aria-hidden="true"></i> Upload photos', '<i class="fa fa-check" aria-hidden="true"></i> Included at the top of city and categoy results', '<i class="fa fa-check" aria-hidden="true"></i> Permanent ad', '<i class="fa fa-check" aria-hidden="true"></i> 100% Free', 2, '0.00', 2, 1),
(21, 'monthly_feat', 'Monthly Featured', '', '', '', '', '', 0, '3.90', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rel_cat_custom_fields`
--

CREATE TABLE IF NOT EXISTS `rel_cat_custom_fields` (
  `rel_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `field_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rel_place_cat`
--

CREATE TABLE IF NOT EXISTS `rel_place_cat` (
  `id` int(11) NOT NULL,
  `place_id` int(11) DEFAULT '0',
  `cat_id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=258 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rel_place_custom_fields`
--

CREATE TABLE IF NOT EXISTS `rel_place_custom_fields` (
  `rel_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=496 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `text` mediumtext COLLATE utf8mb4_unicode_ci,
  `pubdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB AUTO_INCREMENT=20000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signup_confirm`
--

CREATE TABLE IF NOT EXISTS `signup_confirm` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `confirm_str` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_abbr` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country_abbr` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'country',
  `country_id` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_photos`
--

CREATE TABLE IF NOT EXISTS `tmp_photos` (
  `id` int(11) NOT NULL,
  `submit_token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `filename` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL,
  `ipn_description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `place_id` int(11) DEFAULT NULL,
  `payer_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `txn_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `payment_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` decimal(4,2) DEFAULT NULL,
  `txn_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `parent_txn_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subscr_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ipn_response` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ipn_vars` text COLLATE utf8mb4_unicode_ci,
  `txn_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `b_year` int(4) DEFAULT NULL,
  `b_month` int(2) DEFAULT NULL,
  `b_day` int(2) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hybridauth_provider_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'Provider name',
  `hybridauth_provider_uid` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'Provider user ID',
  `ip_addr` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `profile_pic_status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none'
) ENGINE=InnoDB AUTO_INCREMENT=3700 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `city_name`, `country_name`, `gender`, `b_year`, `b_month`, `b_day`, `created`, `hybridauth_provider_name`, `hybridauth_provider_uid`, `ip_addr`, `status`, `profile_pic_status`) VALUES
(1, 'admin@example.com', '$2y$10$yw1O/oUUjGDFRAgVdVAaP.IEGRPpW.R5z6kMtVjPwtVVaua62rlPG', 'Admin', '', '', '', '', NULL, NULL, 0, '2016-03-19 23:24:10', 'local', '', '', 'approved', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `cats`
--
ALTER TABLE `cats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `cat_status` (`cat_status`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `cities_feat`
--
ALTER TABLE `cities_feat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `city_id` (`city_id`) USING BTREE;

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_msgs`
--
ALTER TABLE `contact_msgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loggedin`
--
ALTER TABLE `loggedin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `neighborhoods`
--
ALTER TABLE `neighborhoods`
  ADD PRIMARY KEY (`neighborhood_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `pass_request`
--
ALTER TABLE `pass_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`place_id`),
  ADD KEY `area_code` (`area_code`),
  ADD KEY `userid` (`userid`),
  ADD KEY `status` (`status`),
  ADD KEY `neighborhood` (`neighborhood`),
  ADD KEY `city_id` (`city_id`),
  ADD FULLTEXT KEY `place_name_descrip` (`place_name`,`description`);
ALTER TABLE `places`
  ADD FULLTEXT KEY `description` (`description`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `rel_cat_custom_fields`
--
ALTER TABLE `rel_cat_custom_fields`
  ADD PRIMARY KEY (`rel_id`),
  ADD KEY `cat_id` (`cat_id`),
  ADD KEY `field_id` (`field_id`);

--
-- Indexes for table `rel_place_cat`
--
ALTER TABLE `rel_place_cat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `place_x_cat` (`place_id`,`cat_id`) USING BTREE,
  ADD KEY `place_id` (`place_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `rel_place_custom_fields`
--
ALTER TABLE `rel_place_custom_fields`
  ADD PRIMARY KEY (`rel_id`),
  ADD KEY `option_id` (`field_id`),
  ADD KEY `place_id` (`place_id`),
  ADD FULLTEXT KEY `field_value` (`field_value`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `signup_confirm`
--
ALTER TABLE `signup_confirm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `tmp_photos`
--
ALTER TABLE `tmp_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filename` (`filename`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_hours`
--
ALTER TABLE `business_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cats`
--
ALTER TABLE `cats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=518;
--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10000;
--
-- AUTO_INCREMENT for table `cities_feat`
--
ALTER TABLE `cities_feat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=110;
--
-- AUTO_INCREMENT for table `contact_msgs`
--
ALTER TABLE `contact_msgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `loggedin`
--
ALTER TABLE `loggedin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `neighborhoods`
--
ALTER TABLE `neighborhoods`
  MODIFY `neighborhood_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `pass_request`
--
ALTER TABLE `pass_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `place_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10000;
--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `rel_cat_custom_fields`
--
ALTER TABLE `rel_cat_custom_fields`
  MODIFY `rel_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `rel_place_cat`
--
ALTER TABLE `rel_place_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=258;
--
-- AUTO_INCREMENT for table `rel_place_custom_fields`
--
ALTER TABLE `rel_place_custom_fields`
  MODIFY `rel_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=496;
--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20000;
--
-- AUTO_INCREMENT for table `signup_confirm`
--
ALTER TABLE `signup_confirm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tmp_photos`
--
ALTER TABLE `tmp_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3700;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD CONSTRAINT `business_hours_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`place_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cities_feat`
--
ALTER TABLE `cities_feat`
  ADD CONSTRAINT `cities_feat_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `loggedin`
--
ALTER TABLE `loggedin`
  ADD CONSTRAINT `loggedin_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pass_request`
--
ALTER TABLE `pass_request`
  ADD CONSTRAINT `pass_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`place_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rel_cat_custom_fields`
--
ALTER TABLE `rel_cat_custom_fields`
  ADD CONSTRAINT `rel_cat_custom_fields_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_cat_custom_fields_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rel_place_cat`
--
ALTER TABLE `rel_place_cat`
  ADD CONSTRAINT `rel_place_cat_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`place_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_place_cat_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rel_place_custom_fields`
--
ALTER TABLE `rel_place_custom_fields`
  ADD CONSTRAINT `rel_place_custom_fields_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_place_custom_fields_ibfk_3` FOREIGN KEY (`place_id`) REFERENCES `places` (`place_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`place_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `signup_confirm`
--
ALTER TABLE `signup_confirm`
  ADD CONSTRAINT `signup_confirm_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// csrf check
require_once(__DIR__ . '/_user_inc_request_with_php.php');

$email        = (!empty($_POST['email']))           ? $_POST['email']           : '';
$first_name   = (!empty($_POST['first_name']))      ? $_POST['first_name']      : '';
$last_name    = (!empty($_POST['last_name']))       ? $_POST['last_name']       : '';
$city_name    = (!empty($_POST['profile_city']))    ? $_POST['profile_city']    : '';
$country_name = (!empty($_POST['profile_country'])) ? $_POST['profile_country'] : '';
$gender       = (!empty($_POST['gender']))          ? $_POST['gender']          : '';
$b_year       = (!empty($_POST['b_year']))          ? $_POST['b_year']          : 0;
$b_month      = (!empty($_POST['b_month']))         ? $_POST['b_month']         : 0;
$b_day        = (!empty($_POST['b_day']))           ? $_POST['b_day']           : 0;
$ip           = getenv('HTTP_CLIENT_IP')?:
				getenv('HTTP_X_FORWARDED_FOR')?:
				getenv('HTTP_X_FORWARDED')?:
				getenv('HTTP_FORWARDED_FOR')?:
				getenv('HTTP_FORWARDED')?:
				getenv('REMOTE_ADDR');

// check if email already exists
$query = "SELECT email FROM users WHERE email = :email";
$stmt = $conn->prepare($query);
$stmt->bindValue(':email', $email);;
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!empty($row)) {
	header("Location: $baseurl/_msg.php?msg=email_exists");
}

$stmt = $conn->prepare('
	UPDATE users SET
		email        = :email,
		first_name   = :first_name,
		last_name    = :last_name,
		city_name    = :city_name,
		country_name = :country_name,
		gender       = :gender,
		b_year       = :b_year,
		b_month      = :b_month,
		b_day        = :b_day,
		ip_addr      = :ip
	WHERE id = :id
	');

	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	$stmt->bindValue(':city_name', $city_name);
	$stmt->bindValue(':country_name', $country_name);
	$stmt->bindValue(':gender', $gender);
	$stmt->bindValue(':b_year', $b_year);
	$stmt->bindValue(':b_month', $b_month);
	$stmt->bindValue(':b_day', $b_day);
	$stmt->bindValue(':ip', $ip);
	$stmt->bindValue(':id', $_SESSION['userid']);

	if($stmt->execute()) {
		header("Location: $baseurl/user/my-profile.php");
	}

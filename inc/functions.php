<?php
// default exception handler function
function exception_handler($e) {
	echo "<strong>Uncaught exception:</strong> " . htmlspecialchars($e->getMessage());
	echo "<br>Terminating script";
}

// default error handler function
function error_handler($errno, $errstr, $errfile, $errline) {
	$errstr = htmlspecialchars($errstr);
	echo "<strong>Error Number:</strong> $errno <br>";
	echo "<strong>Error String:</strong> $errstr <br>";
	echo "<strong>Error Line:</strong> $errline <br>";
	echo "<strong>Error File:</strong> $errfile <br>";
	echo "Terminating script";
}

function destroy_session_and_cookie() {
	global $install_path;
	// clear session
	$_SESSION["user_connected"] = false;

	//destroy session
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}

	// remove cookie * setcookie() must be set before any output
	// signature: setcookie(name, value, expire, path, domain, secure, httponly);
	setcookie('loggedin', '', time()-3600, $install_path, '', '', true);

	// delete all instances from the loggedin table
	// better not delete because it would be vulnerable to DoS
}

function record_tokens($userid, $provider, $token) {
	global $conn;
	$token = sha1($token);

	try {
		$conn->beginTransaction();

		// delete existing row if exist
		$query = 'DELETE FROM loggedin WHERE userid = :userid';
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':userid', $userid);
		$stmt->execute();

		// insert new record
		$query = 'INSERT INTO loggedin(userid, provider, token, created )
					VALUES(:userid, :provider, :token, NOW())';
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':userid', $userid);
		$stmt->bindValue(':provider', $provider);
		$stmt->bindValue(':token', $token);
		$stmt->execute();

		// commit
		$conn->commit();
	}

	catch(PDOException $e) {
		$conn->rollBack();
	}
}

// clean strings for mail function
function safe($str) {
	return(str_ireplace(array( "\r", "\n", "%0a", "%0d", "Content-Type:", "bcc:","to:","cc:" ), "", $str ));
}

function e($str) {
	return htmlspecialchars($str);
}

function generatePassword($length = 8) {
    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if($length > $maxlength) {
		$length = $maxlength;
	}

    // set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while($i < $length) {
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, $maxlength-1), 1);

		// have we already used this character in $password?
		if (!strstr($password, $char)) {
			// no, so it's OK to add it onto the end of whatever we've already got...
			$password .= $char;
			// ... and increase the counter by one
			$i++;
		}
	}

    return $password;
}

function mb_ucfirst($string, $encoding) {
	$strlen = mb_strlen($string, $encoding);
	$firstChar = mb_substr($string, 0, 1, $encoding);
	$then = mb_substr($string, 1, $strlen - 1, $encoding);
	return mb_strtoupper($firstChar, $encoding) . $then;
}

function print_r2($val) {
	$out = print_r($val, true);

	$pattern = '/\n\(/i';
	$replacement = '(';
	$out = preg_replace($pattern, $replacement, $out);

	$pattern = '/\n\s+\(/i';
	$replacement = '(';
	$out = preg_replace($pattern, $replacement, $out);

	$pattern = '/\n\s+\)/i';
	$replacement = ')';
	$out = preg_replace($pattern, $replacement, $out);

	$pattern = '/^[ \t]*[\r\n]+/m';
	$replacement = '';
	$out = preg_replace($pattern, $replacement, $out);

	echo '<pre>';
    print_r($out);
    echo  '</pre>';
}

function to_slug($string, $maxLength=40, $separator='-') {
	if(function_exists('transliterator_transliterate')) {
		$slug = transliterator_transliterate("Any-Latin; Latin-ASCII;", $string);
	} else {
		$slug = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string));
	}
	$slug = preg_replace('/[^a-zA-Z0-9 -]/', '', $slug);
	$slug = trim(substr(strtolower($slug), 0, $maxLength));
	$slug = preg_replace("/[\/_|+ -]+/", $separator, $slug);
	$slug = (!empty($slug)) ? $slug : 'str_0';
	return $slug;
}

/**
 * Get Headers function
 * @param str #url
 * @return array
 */
function getHeaders($url) {
	$ch = curl_init($url);
	curl_setopt( $ch, CURLOPT_NOBODY, true );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
	curl_exec( $ch );
	$headers = curl_getinfo( $ch );
	curl_close( $ch );

	return $headers;
}

/**
 * Download
 * @param str $url, $path
 * @return bool || void
 */
function download($url, $path) {
	# open file to write
	$fp = fopen ($path, 'w+');
	# start curl
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	# set return transfer to false
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
	curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	# increase timeout to download big file
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	# write data to local file
	curl_setopt( $ch, CURLOPT_FILE, $fp );
	# execute curl
	curl_exec( $ch );
	# close curl
	curl_close( $ch );
	# close local file
	fclose( $fp );

	if (filesize($path) > 0) return true;
}

// using to validate twitter username
function validate_username($username) {
    return preg_match('/^@?([A-Za-z0-9_]{1,15})(?![.A-Za-z])$/', $username);
}

function facebook_url($str) {
	$pattern = "/^https?:\/\//";
	if(!preg_match($pattern, $str)){
		$pattern = "/^(.*)facebook\.com\/(.*)/";
		if(preg_match($pattern, $str)){
			return preg_replace($pattern, '$2', $str);
		}
		else {
			return $str;
		}
	}
	else {
		$pattern = "/^(.*)facebook\.com\/(.*)/";
		if(preg_match($pattern, $str)){
			return preg_replace($pattern, '$2', $str);
		}
		else {
			return $str;
		}
	}
}

function twitter_url($str) {
	$str = str_replace('@', '', $str);
	$pattern = "/^https?:\/\//";
	if(!preg_match($pattern, $str)){
		$pattern = "/^(.*)twitter\.com\/(.*)/";
		if(preg_match($pattern, $str)){
			return preg_replace($pattern, '$2', $str);
		}
		else {
			return $str;
		}
	}
	else {
		$pattern = "/^(.*)twitter\.com\/(.*)/";
		if(preg_match($pattern, $str)){
			return preg_replace($pattern, '$2', $str);
		}
		else {
			return $str;
		}
	}
}

function site_url($str) {
	$pattern = "/^https?:\/\//";
	if(!preg_match($pattern, $str)){
		if(!strstr($str, '.')) {
			return '';
		}
		else {
			return 'http://' . $str;
		}
	}
	else {
		if(!strstr($str, '.')) {
			return '';
		}
		else {
			return $str;
		}
	}
}

// alternative to nl2br(), <p> instead of <br>
function nl2p($string) {
    $paragraphs = '';

    foreach (explode(PHP_EOL, $string) as $line) {
        if (trim($line)) {
            $paragraphs .= '<p>' . $line . '</p>';
        }
    }

    return $paragraphs;
}

// function to get categories structure (parents, etc)
function get_parent($cat_id, $arr, PDO $conn) {
	$stmt = $conn->prepare('SELECT parent_id FROM cats WHERE cat_status = 1 AND id = :cat_id');
	$stmt->bindValue(':cat_id', $cat_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$parent_id = $row['parent_id'];

	if($parent_id != 0) {
		$arr[] = $parent_id;
		return get_parent($parent_id, $arr, $conn);  // see http://stackoverflow.com/questions/15379421/php-function-not-returning-array-in-php
	}
	else {
		return $arr;
	}
}

function get_snippet($sentence, $count = 20) {
	preg_match("/(?:[^\s,\.;\?\!]+(?:[\s,\.;\?\!]+|$)){0,$count}/", $sentence, $matches);
	return $matches[0];
}

function get_children_cats_ids($cat_id, PDO $conn) {
	$arr = array();
	$query = "SELECT id FROM cats WHERE parent_id = :cat_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':cat_id', $cat_id);
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$cat_id = $row['id'];
		$arr[]  = $cat_id;

		$g = get_children_cats_ids($cat_id, $conn);

		if(!empty($g)) {
			foreach($g as $v) {
				$arr[] = $v;
			}
		}
	}
	return $arr;
}

function get_cities(PDO $conn) {
	$query = "SELECT city_id, city_name, state FROM cities";
	$stmt = $conn->prepare($query);
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$city_id   = $row['city_id'];
		$city_name = $row['city_name'];
		$state     = $row['state'];
		?>
		<option value="<?= $city_id; ?>"><?= $city_name; ?>, <?= $state; ?></option>
		<?php
	}
}

// function that returns current user IP
function get_ip() {
	if($_SERVER) {
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                return $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else if(isset($_SERVER["HTTP_CLIENT_IP"])) {
                return $_SERVER["HTTP_CLIENT_IP"];
		}
		else if(isset($_SERVER["REMOTE_ADDR"])) {
                return $_SERVER["REMOTE_ADDR"];
		}
		else {
			return '0.0.0.0';
		}
	}
	else {
		if( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			return getenv( 'HTTP_X_FORWARDED_FOR' );
		}
		else if( getenv( 'HTTP_CLIENT_IP' ) ) {
			return getenv( 'HTTP_CLIENT_IP' );
		}
		else if( getenv( 'REMOTE_ADDR' ) ) {
			return getenv( 'REMOTE_ADDR' );
		}
		else {
			return '0.0.0.0';
		}
	}
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d G:i:s', $date);
    return $d && $d->format('Y-m-d G:i:s') === $date;
}

// function to build pages menu
function show_menu($group, $ul = true) {
	global $baseurl;
	global $conn;

	$stmt = $conn->prepare('SELECT page_id, page_title, page_slug FROM pages WHERE page_group = :page_group');
	$stmt->bindValue(':page_group', $group);
	$stmt->execute();

	$menu = '';

	if($ul) {
		$menu = '<ul class="' . $group . '">';
	}

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$page_id    = $row['page_id'];
		$page_title = $row['page_title'];
		$page_slug  = $row['page_slug'];

		$menu .= '<li><a href="' . $baseurl . '/p/' . $page_id . '/' . $page_slug . '">' . $page_title . '</a></li>';
	}

	if($ul) {
		$menu .= '</ul>';
	}

	return $menu;
}

// function to return category's children
function get_children($parent_id, $place_cat, $level, PDO $conn) {
	// check if node has children
	$has_child = false;
	$query = "SELECT COUNT(*) AS total_leaves FROM cats WHERE cat_status = 1 AND parent_id = :parent_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':parent_id', $parent_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row['total_leaves'] > 0) $has_child = true;

	if(is_null($parent_id)) {
		$query = "SELECT * FROM cats WHERE cat_status = 1 AND parent_id IS NULL ORDER BY name";
		$stmt = $conn->prepare($query);
	}
	else {
		$query = "SELECT * FROM cats WHERE cat_status = 1 AND parent_id = :parent_id ORDER BY name";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':parent_id', $parent_id);
	}
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$cat_id    = $row['id'];
		$name      = $row['name'];
		$parent_id = $row['parent_id'];

		$selected = '';
		if($cat_id == $place_cat) {
			$selected = 'selected';
		}

		if($level == 0) {
			echo "<option value='$cat_id' $selected><strong>$name</strong></option>";
			echo "\n";
			get_children($cat_id, $place_cat, $level+1, $conn);
		}

		else {
			$indent = str_repeat("-", (($level * 2) - 1));
			echo "<option value='$cat_id' $selected><strong>$indent $name</strong></option>";
			echo "\n";
			get_children($cat_id, $place_cat, $level+1, $conn);
		}
	}
} // end function get_children

// function to show form select dropdown for categories with category selected
function show_cat_dropdown($parent_id, $level, $place_cat, PDO $conn) {
	// check if node has children
	$has_child = false;
	$query = "SELECT COUNT(*) AS total_leaves FROM cats WHERE cat_status = 1 AND parent_id = :parent_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':parent_id', $parent_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row['total_leaves'] > 0) $has_child = true;

	if(is_null($parent_id)) {
		$query = "SELECT * FROM cats WHERE cat_status = 1 AND parent_id IS NULL ORDER BY name";
		$stmt = $conn->prepare($query);
	}
	else {
		$query = "SELECT * FROM cats WHERE cat_status = 1 AND parent_id = :parent_id ORDER BY name";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':parent_id', $parent_id);
	}
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$row_cat_id    = $row['id'];
		$row_name      = $row['name'];
		$row_parent_id = $row['parent_id'];

		$selected = '';
		if($row_cat_id == $place_cat) {
			$selected = 'selected';
		}

		if($level == 0) {
			echo "<option value='$row_cat_id' $selected><strong>$row_name</strong></option>";
			echo "\n";
			get_children($row_cat_id, $place_cat, $level+1, $conn);
		}

		else {
			$indent = str_repeat("-", (($level * 2) - 1));
			echo '<option value="' . $row_cat_id . '">' . '<strong>' . $indent . ' ' . $row_name . '</strong>' . '</option>';
			echo "<option value='$row_cat_id'><strong>$indent $row_name</strong></option>";
			echo "\n";
			get_children($row_cat_id, $place_cat, $level+1, $conn);
		}
	}
} // end function show_cat_dropdown

/**
 * Output categories in hierarchical form, with checked attribute
 *
 * Created for the Custom Fields Plugin
 *
 * @param array $cats_grouped_by_parent (see function group_cats_by_parent())
 * @param int $parent_id
 * @param array $checked_cats
 * @param int $is_first
 *
 * @return array
 */
function show_cats($cats_grouped_by_parent, $parent_id = 0, $checked_cats, $is_first = 0) {
	if($is_first) {
		echo '<ul class="no-margin show-cats" id="cat-checkboxes">';
	}
	else {
		echo '<ul>';
	}

	if(!empty($cats_grouped_by_parent)) {
		foreach ($cats_grouped_by_parent[$parent_id] as $v) {
			$checked = (in_array($v['cat_id'], $checked_cats)) ? 'checked' : '';
			if(empty($checked_cats)) {
				$checked = 'checked';
			}
			echo '<li><label><input type="checkbox" name="cats[]" value="' . $v['cat_id'] . '"' . "$checked> ";
				echo $v['plural_name'];
				$cur_id = $v['cat_id'];
				//if there are children
				if (!empty($cats_grouped_by_parent[$cur_id])) {
					show_cats($cats_grouped_by_parent, $cur_id, $checked_cats, 0);
				}
			echo '</label></li>';
		}
	}

	echo '</ul>';
}

/**
 * Group categories by parent id
 *
 * Returns an associative array where the index of each item is an cat_id and each
 * item contains an array of cats where the parent cat is cat_id
 *
 * @param array $cats_arr is an array of arrays, each item is an array
 * with keys: 'cat_id', 'plural_name', 'parent_id'
 *
 * @return array

 Array(
	[cat_id] => Array(
				 [0] => Array of a child cat of cat_id
				 [1] => Array of another child cat of cat_id
				 [2] => Array of another child cat of cat_id
				 ...)
	[cat_id] => Array(
				 [0] => Array of a child cat of cat_id
				 ...)
	[cat_id] => Array(
				 [0] => Array of a child cat of cat_id
				 [1] => Array of another child cat of cat_id
				 [2] => Array of another child cat of cat_id
				 ...)
	)
 */
function group_cats_by_parent($cats_arr) {
	$cats_by_parent = array();

	foreach ($cats_arr as $v) {
		if (!isset($cats_by_parent[ $v['parent_id'] ])) {
			$cats_by_parent[$v['parent_id']] = array();
		}

		$cats_by_parent[$v['parent_id']][] = $v;
	}

	return $cats_by_parent;
}

// alternative to exif_imagetype
if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
		return false;
    }
}
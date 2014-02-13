<?php

/**
 * WordpressMover v1.0
 * Copyright 2010 Matthew Hamann
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class WordpressMover {

	private $connected = false;
	private $dbHost;
	private $dbUser;
	private $dbPwd;
	private $dbName;
	private $conn;
	
	private $oldUrl;
	private $newUrl;
	private $tablePrefix;
	
	private $optionsUpdate = "UPDATE {table_prefix}wp_options SET option_value = replace(option_value, '{oldUrl}', '{newUrl}') WHERE option_name = 'home' OR option_name = 'siteurl';";
	private $guidUpdate = "UPDATE {table_prefix}wp_posts SET guid = replace(guid, '{oldUrl}','{newUrl}');";
	private $contentUpdate = "UPDATE {table_prefix}wp_posts SET post_content = replace(post_content, '{oldUrl}', '{newUrl}');";

	public function __construct($hostname, $username, $password, $database, $oldUrl, $newUrl, $tablePrefix = "") {
		$this->dbHost = $hostname;
		$this->dbUser = $username;
		$this->dbPwd = $password;
		$this->dbName = $database;
		$this->oldUrl = $oldUrl;
		$this->newUrl = $newUrl;
		$this->tablePrefix = $tablePrefix;
	}
	
	public function migrate() {
		$conn = $this->connect();
		
		try {
			mysql_query(str_replace(array("{table_prefix", "{oldUrl}", "{newUrl}"), array($this->tablePrefix, $this->oldUrl, $this->newUrl), $this->optionsUpdate)) or die(mysql_error());
			echo "<p>" . mysql_affected_rows($conn) . " option rows updated</p>";
			
			mysql_query(str_replace(array("{table_prefix", "{oldUrl}", "{newUrl}"), array($this->tablePrefix, $this->oldUrl, $this->newUrl), $this->guidUpdate)) or die(mysql_error());
			echo "<p>" . mysql_affected_rows($conn) . " guid rows updated</p>";
			
			mysql_query(str_replace(array("{table_prefix", "{oldUrl}", "{newUrl}"), array($this->tablePrefix, $this->oldUrl, $this->newUrl), $this->contentUpdate)) or die(mysql_error());
			echo "<p>" . mysql_affected_rows($conn) . " content rows updated</p>";
		} catch (Exception $ex) {
			echo "<p>Whoops, an error occurred during the migration.<br />Code: " . $ex . "</p>";
		}		
		
		$this->disconnect();
		
		echo "<p>Your Wordpress database has been updated successfully! You may move or copy your Wordpress installation to the new location if you have no done so already.</p>";
	}
	
	private function connect() {
		if ($this->connected) {
			return $this->conn;
		}
		
		$conn = mysql_connect($this->dbHost, $this->dbUser, $this->dbPwd) or die(mysql_error());
		mysql_select_db($this->dbName) or die(mysql_error());
		
		$this->conn = $conn;
		return $conn;
	}
	
	private function disconnect() {
		mysql_close($this->conn);
	}
}

/**
 * Process form submission.
 * Initialize the class and do the process. Class will handle errors.
 */

if (isset($_POST['submit'])) {

	$mover = new WordpressMover($_POST['hostname'], $_POST['username'], $_POST['password'], $_POST['database'], $_POST['oldurl'], $_POST['newurl'], $_POST['table_prefix']);
	$mover->migrate();
	die();
}

/**
 * Initial setup of app.
 * We'll check for a wp-config file first, otherwise prompt for login details
 * Regardless, we'll need to get the old and new install URLs
 * (but may add auto-detection of old URL in a subsequent version)
 */
 
if (file_exists("./wp-config.php") && !isset($_POST['submit'])) {
	include('wp-config.php');
	$_POST['hostname'] = DB_HOST;
	$_POST['username'] = DB_USER;
	$_POST['password'] = DB_PASSWORD;
	$_POST['database'] = DB_NAME;
	$_POST['table_prefix'] = $table_prefix;
} else if (!isset($_POST['submit'])) {
	$_POST['hostname'] = "localhost";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<title>Wordpress Mover Utility</title>

</head>

<body>
	<form name="migrationform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table>
			<tr>
				<th colspan="2">Database Information</th>
			</tr>
			
			<tr>
				<td>Hostname:</td>
				<td><input type="text" name="hostname" value="<?php echo $_POST['hostname']; ?>" /><br /><small>Usually "localhost"</small></td>
			</tr>
			
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username" value="<?php echo $_POST['username']; ?>" /></td>
			</tr>
			
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password" value="<?php echo $_POST['password']; ?>" /></td>
			</tr>
			
			<tr>
				<td>Database:</td>
				<td><input type="text" name="database" value="<?php echo $_POST['database']; ?>" /></td>
			</tr>
			
			<tr>
				<td>Table prefix:</td>
				<td><input type="text" name="table_prefix" value="<?php echo $_POST['table_prefix']; ?>" /></td>
			</tr>
			
			<tr></tr>
			
			<tr>
				<th colspan="2">Wordpress Migration Information</th>
			</tr>
			
			<tr>
				<td>Current (old) url:</td>
				<td><input type="text" name="oldurl" value="<?php echo $_POST['oldurl']; ?>" /></td>
			</tr>
			
			<tr>
				<td>New url:</td>
				<td><input type="text" name="newurl" value="<?php echo $_POST['newurl']; ?>" /></td>
			</tr>
			
			<tr>
				<td></td>
				<td><button type="submit" name="submit">Run Mover</button></td>
			</tr>
		</table>
	</form>
</body>

</html>

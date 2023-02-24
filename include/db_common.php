<?php
include("config.php");

/*
 * database common function
 */

function getTypes(array $data = null)
{
	if (empty($data)) return "";
	$len = count($data);
	if ($len > 0) {
		return str_pad("", $len, "s");
	}
	return "";
}

function getMySqli()
{
	global $database;
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$conn = new mysqli($database["host"], $database["username"], $database["password"], $database["db"]);
	mysqli_set_charset($conn, 'UTF8');
	return $conn;
}

function execute_query(&$conn, $sql, $data)
{
	$conn = getMySqli();
	$type = getTypes($data);
	$query = $conn->prepare($sql);
	if (!empty($data)) {
		foreach ($data as $i => $x) {
			$data[$i] = htmlspecialchars($x);
		}
		$query->bind_param($type, ...$data);
	}
	$query->execute();
	return $query;
}

function db_select($sql, array $data = null): array
{
	try {
		$query = execute_query($conn, $sql, $data);
		$result = $query->get_result();
		$arr_result = [];
		if ($result->num_rows > 0) {
			while ($record = $result->fetch_assoc()) {
				array_push($arr_result, $record);	// push record to array
			}
		}
		$conn->close();
		return $arr_result;
	} catch (Exception $ex) {
		dumpData($ex->getMessage(), "Query: $sql", $ex->getTraceAsString(), $data);
	}
}

function db_execute($sql, array $data = null): bool
{
	try {
		$query = execute_query($conn, $sql, $data);
		$affected = $query->affected_rows;
		$conn->close();
		return $affected;
	} catch (Exception $ex) {
		dumpData($ex->getMessage(), "Query: $sql", $ex->getTraceAsString(), $data);
	}
}

/*
 * Select
 */

function gen_option($table, $col_value = "`id`", $col_text = "`name`", $selected_value = "")
{
	$sql = "select $col_value, $col_text from $table order by $col_value desc";
	$data = db_select($sql);

	$col_value = end(explode(" as ", $col_value));
	$col_value = str_replace("`", "", $col_value);
	$col_text = end(explode(" as ", $col_text));
	$col_text = str_replace("`", "", $col_text);
	$str = "";
	foreach ($data as $item) {
		if ($item[$col_value] == $selected_value) {
			$str .= "<option value='$item[$col_value]' selected>$item[$col_text]</option>";
		} else {
			$str .= "<option value='$item[$col_value]'>$item[$col_text]</option>";
		}
	}
	return $str;
}

function dumpData(...$data)
{
	$textArr = ["Dump data..."];
	foreach ($data as $key => $value) {
		$textArr[] = print_r($value, true);
	}
	$text = implode("\n\n", $textArr);
	echo "<pre style='background-color: #efefef; padding: 15px; box-sizing: border-box'>$text</pre>";
	die;
}

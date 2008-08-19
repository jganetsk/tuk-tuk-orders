<?php

mysql_connect('localhost','jason') || die('Could not connect: ' . mysql_error());
mysql_select_db('Tuktuk') || die('Could not open database: ' . mysql_error());

$user_id = $_POST['user'];

if ($user_id == 0) {
    die("Select a user! <a href='index.php'>Go back.</a>");
}

if (strpos($_POST['entree'], '-')) {
    $arr = explode('-', $_POST['entree']);
    $entree_id = $arr[0];
    $type_id = $arr[1];
} else {
    $entree_id = $_POST['entree'];
    $type_id = "NULL";
}

if ($entree_id == 0) {
    die("Select an entree! <a href='index.php'>Go back.</a>");
}

$query = 'INSERT INTO UserOrder (user_id, date, entree_id, type_id) VALUES (';
$query .= $user_id . ',NOW(),' . $entree_id . ',' . $type_id . ')';

if (!mysql_query($query)) {
    die("Can't change orders yet! Please <a href='mailto:jason@amiestreet.com'>e-mail Jason!</a>");
}

$order_id = mysql_insert_id();

if (is_array($_POST['extra'])) {
    foreach($_POST['extra'] as $extra) {
        $query = 'INSERT INTO UserOrderExtra (userorder_id, extra_id) VALUES (';
        $query .= $order_id . ',' . $extra . ')';
        mysql_query($query);
    }
}

?>

Thank you!
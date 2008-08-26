<?php

mysql_connect('localhost','jason') || die('Could not connect: ' . mysql_error());
mysql_select_db('Tuktuk') || die('Could not open database: ' . mysql_error());

$result = mysql_query("SELECT value FROM Config WHERE name = 'taking_orders'");
$row = mysql_fetch_row($result);
if (!$row) {
    die('Please add taking_orders config option to the database.');
}
if (!$row[0]) {
    die('No longer taking orders! Talk to Jason.');
}

$result = mysql_query('SELECT id, name FROM User ORDER BY name ASC');
$users = array();
while ($row = mysql_fetch_row($result)) {
    $users[] = $row;
}

$result = mysql_query('SELECT Entree.id, EntreeType.type_id, Entree.name, Type.name ' .
                      'FROM Entree ' .
                      'LEFT JOIN EntreeType ON Entree.id = EntreeType.entree_id ' .
                      'LEFT JOIN Type ON EntreeType.type_id = Type.id ' .
                      'ORDER BY Entree.id ASC');
$entrees = array();
while ($row = mysql_fetch_row($result)) {
    $id = $row[0] . (is_null($row[1]) ? '' : ('-' . $row[1]));
    $name = $row[2] . (is_null($row[3]) ? '' : (' w/ ' . $row[3]));
    $entrees[] = array(0 => $id, 1 => $name);
}

$result = mysql_query('SELECT id, name FROM Extra');
$extras = array();
while ($row = mysql_fetch_row($result)) {
    $extras[] = $row;
}
?>

<html>

<head><title>Tuk Tuk Orders</title></head>

<body>

<a target="_blank" href="http://www.tuktukny.com/menu/TukTuk_Lunch_Menu_08.pdf">Menu</a><br />

<form action='order.php' method='post'>
<table>
<tr>
<td>Your name:</td>
<td>
<select name='user'>
<option value='0'>-- Select --</option>
<?php
foreach ($users as $user) {
    $id = $user[0];
    $name = $user[1];
    print("<option value='$id'>$name</option>\n");
}
?>
</select></td></tr>
<tr><td>
Your entree:</td><td>
<select name='entree'>
<option value='0'>-- Select --</option>
<?php
foreach ($entrees as $entree) {
    $id = $entree[0];
    $name = $entree[1];
    print("<option value='$id'>$name</option>\n");
}    
?>
</select></td>
<tr>
<td>Extras:</td>
<td>
<?php
foreach ($extras as $extra) {
    $id = $extra[0];
    $name = $extra[1];
    print("<input name='extra[]' type='checkbox' value='$id'>$name</input><br/>");
}
?>
</td>

<tr><td>
<input type='Submit'/>
</td></tr>
</tr>

</form>

</body>

</html>
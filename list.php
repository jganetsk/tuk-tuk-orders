<?php

mysql_connect('localhost','jason') || die('Could not connect: ' . mysql_error());
mysql_select_db('Tuktuk') || die('Could not open database: ' . mysql_error());

# TODO: make it work for multiple days

$query = 'SELECT COUNT(*), Entree.name, Type.name, Entree.price FROM UserOrder ' .
    'JOIN Entree ON UserOrder.entree_id = Entree.id ' .
    'LEFT JOIN Type ON UserOrder.type_id = Type.id ' .
    'WHERE UserOrder.date = curdate() ' .
    'GROUP BY UserOrder.entree_id, UserOrder.type_id';

$total = 0.0;

$result = mysql_query($query);

$items = array();
while ($row = mysql_fetch_row($result)) {
    $items[] = $row;
    $total += $row[3] * $row[0];
}

$query = 'SELECT COUNT(*), Extra.name, Extra.price FROM UserOrderExtra ' .
    'JOIN Extra ON Extra.id = UserOrderExtra.extra_id ' .
    'JOIN UserOrder ON UserOrder.id = UserOrderExtra.userorder_id ' .
    'WHERE UserOrder.date = curdate() ' .
    'GROUP BY extra_id';

$result = mysql_query($query);
while ($row = mysql_fetch_row($result)) {
    $total += $row[2] * $row[0];
    $row[2] = null;
    $items[] = $row;
}

$tax = $total * 0.085;

?>

<html>

<head><title>Tuk Tuk Items</title></head>

<body>
<table>
<?php
foreach ($items as $item) {
    echo '<tr><td>' . $item[0] . '</td><td>' . 
        ($item[2] ? $item[2] . ' ' : '') . $item[1] . '</td>';
    echo '</tr>';
}
?>
</table>
<hr />
<form method='post' action='calc.php'>
<input type='hidden' name='tax' value='<?php echo $tax?>' />
<table>
<tr><td><b>Sub-total:</b></td><td><b><?php echo $total ?></b></td></tr>
<tr><td><b>Total:</b></td><td><b><?php echo $total + $tax ?></b></td></tr>
<tr><td>Tip: </td><td><input name='tip' /></td></tr>
<tr><td>Tuk Tuk Fund: </td><td><input name='fund' /></td></tr>
</table>
<input type='submit'>
</form>
</body>

</html>
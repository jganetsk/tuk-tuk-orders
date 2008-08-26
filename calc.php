<?php

mysql_connect('localhost','jason') || die('Could not connect: ' . mysql_error());
mysql_select_db('Tuktuk') || die('Could not open database: ' . mysql_error());

function email_user($toname, $toemail, $amount) {
    $msg = $toname . ", you owe $amount dollars for Tuk Tuk.";
    $fromemail = 'jason@amiestreet.com';
    $fromname = 'Jason Ganetsky';
    $hdr = "Reply-to: $fromname <$fromemail>\n";
    $hdr .= "From: $fromname <$fromemail>\n";
    $options = "-f $fromemail";
    mail("$toname <$toemail>", 'Tuk Tuk Tuesday', $msg, $hdr, $options);
}

$extra = max($_POST['tax'] + $_POST['tip'] - $_POST['fund'], 0);

$query = 'SELECT User.name, User.email, IFNULL(E.p, 0) + Entree.price FROM UserOrder ' .
    'JOIN User ON User.id = UserOrder.user_id ' .
    'JOIN Entree ON Entree.id = UserOrder.entree_id ' .
    'LEFT JOIN ' .
    '(SELECT SUM(Extra.price) p, userorder_id FROM UserOrderExtra ' .
    'JOIN Extra ON Extra.id = UserOrderExtra.extra_id ' .
    'JOIN UserOrder ON userorder_id = UserOrder.id '.
    'WHERE UserOrder.date = curdate() ' .
    'GROUP BY userorder_id) E ' .
    'ON E.userorder_id = UserOrder.id ' .
    'WHERE UserOrder.date = curdate()';

$result = mysql_query($query);

$users = array();
while ($row = mysql_fetch_row($result)) {
    $users[] = $row;
}

$count = count($users);
$extra_per = $extra / $count;

$new_users = array();
$total = 0;
foreach ($users as $user) {
    $row = $user;
    $row[2] += ceil($extra_per);
    $new_users[] = $row;
    $total += $row[2];
    if ($_POST['email']) {
        email_user($row[0], $row[1] . "@amiestreet.com", $row[2]);
    }
}

$users = $new_users;

$fund = $_POST['fund'];
$fund_needed = min($fund, ceil($_POST['tax'] + $_POST['tip'] - ceil($extra_per) * $count));

?>

<html>

<head><title>Tuk Tuk People</title></head>

<body>
<table>
<?php
foreach ($users as $user) {
    echo '<tr><td>';
    echo $user[0] . '</td><td>' . $user[2] . '</td>';
    echo '<td><input type="text" size="3" /></td>';
}
?>
<tr><td><b>Fund</b></td><td><b><?php echo $fund_needed ?></b></td></tr>
<tr><td><b>Total</b></td><td><b><?php echo $total + $fund_needed ?></b></td></tr>
</table>

<form action='calc.php' method='post'>
<input type='hidden' name='email' value='1'/>
<input type='hidden' name='tax' value='<?php echo $_POST['tax']?>'/>
<input type='hidden' name='tip' value='<?php echo $_POST['tip']?>'/>
<input type='hidden' name='fund' value='<?php echo $_POST['fund']?>'/>
<input type='submit' value='E-mail' />
</form>
</body>

</html>
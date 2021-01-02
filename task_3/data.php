<?php
function getProvider($email){
	$start=strpos($email,'@');
	return	substr($email,$start+1);
}
require_once 'config.php';
$pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
$data = $pdo->query("select * from emails ");
foreach($data as $key => $row) {
	$provider = getProvider($row['email']);
	if ($provider != 'hubspot.com') {
		$providers[$provider] = true;
	}
}
$order_by='created_at';
if(!empty($_POST['sort'])){
	$order_by=$_POST['sort'];
}
$provider="";
if(!empty($_POST['providers'])){
	$provider= "where `email` like '%".$_POST['providers']."'";
}

$sql="select * from emails ".$provider." order by ".$order_by;
$data = $pdo->query($sql);
foreach ($data as $key=> $row){
	$rows[$key]=$row;
}
$i=0;
if(!empty($_POST['search'])){
	foreach ($rows as $key=>$row){
		if(strpos($row['email'],$_POST['search']) !== false ){
			$emails[$i]=$row;
			$i++;
		}
	}
	$rows=$emails;
}

if(isset($_POST['checkbox'])){

	$pdo=$pdo->prepare("Delete from emails where `id` like ?");
	foreach ($_POST['checkbox'] as $value){
		$pdo->execute([$value]);
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<form method="POST" action="">
	<label>
		Sort by
		<select name="sort">
			<option value="created_at">Date</option>
			<option value="email">Email</option>
		</select>
	</label>
	<br>
	<br>
	<label>
		Provider :
		<select name="providers">
			<option></option>
			<?php
			foreach ($providers as $key=>$provider){
				echo '<option>'.$key.'</option>';
			}
			?>
		</select>
	</label>
	<br>	<br>

	<label>
		Search by email
		<input name="search" >
	</label>
	<br>
	<br>

	<button type="submit"> Filter & Sort </button>
</form>
<form method="post" action="" name="delete">
	<table>
		<tr>
			<th>#</th>
			<th>Email</th>
		</tr>
		<?if(isset($rows)){
		foreach ($rows as $key => $row){ ?>
		<tr>
			<td><input type="checkbox" name="checkbox[]" value="<? echo $row['id']?>"></td>
			<td><? echo $row['email']?></td>
		</tr>
		<? }} ?>
	</table>
	<button type="submit"> delete</button>
</form>
</body>
</html>

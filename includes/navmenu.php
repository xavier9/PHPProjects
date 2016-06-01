<?php
$menus = get_menu($conn);

if(isset($_POST["menu"]))
	$_SESSION["menu"]=$_POST["menu"];

echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
echo "<div id='menu'>";

foreach ($menus as $menu) {
	$rights = get_user_rights ($conn, $_SESSION["user"]["ID"]);
	if(count_items ($conn, $menu["ID"], $rights) > 0) {
		echo "<input  type='submit'  name='menu' value='".$menu["Description"]."' class='menu_button'/></br>";
		if(isset($_SESSION["menu"]) && $_SESSION["menu"]==$menu["Description"]) {
			$items = get_menu_items ($conn, $menu["ID"]);
			foreach ($items as $item) {
				if(has_access ($item["Rights"], $rights))
					echo "<input  type='button' value='".$item["Description"]."' class='menu_item_button' onclick=\"window.location.href='".$item["Link"]."'\"></br>";
			}
		}
	}
}


echo "</div>";
echo "</form>";

function count_items ($conn, $menu_id, $rights) {
	$counter=0;
	$items = get_menu_items ($conn, $menu_id);
	foreach ($items as $item)
		if(has_access ($item["Rights"], $rights))
			$counter++;
	return $counter;
}

function has_access ($right, $user_rights) {
	for($i=0;$i<strlen($user_rights["Rights"]);$i++) {
		if(strcmp(substr($user_rights["Rights"],$i,1),$right)==0)
			return true;
	}
	return false;
}
?>

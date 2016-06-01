<?php
// header("Location: maintenance.php");

session_start();
require_once("../includes/header.php");

$err_msg = "";
if(!isset($_SESSION["user"])) {
    if(isset($_POST["submit"])) {
        $user_username = trim($_POST["username"]);
        $data = explode ("@", $user_username);
        $user_username = $data[0];
        $user_password = trim($_POST["password"]);
        $ldapconn = get_ldap_connection ();
//professoren
        if (!empty($user_username) && !empty($user_password)) {
            $format = "uid=%s,ou=Users,dc=eeb2,dc=be";
            $ldap_user = sprintf($format,$user_username);
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            //$result = ldap_search($ldapconn,"ou=Users,dc=eeb2,dc=be","uid=$user_username",array ("cn","employeenumber","gidnumber","postalcode")) or die ("Error in Query")
            $login = $_SESSION ["user"];
            echo $login;
            //echo @ldap_bind($ldapconn, $ldap_user, $user_password);
            if(ldap_bind($ldapconn, $ldap_user, $user_password)) {
                echo $ldapconn."<br/>".$ldap_user;
                $result = ldap_search($ldapconn,"ou=Users,dc=eeb2,dc=be","uid=$user_username",array ("cn","employeenumber","gidnumber","postalcode")) or die ("Error in Query");
                $data = ldap_get_entries($ldapconn, $result);
                ldap_close($ldapconn);
                /*if (is_teacher($conn, $data[0]["employeenumber"][0]))*/
                    set_login($conn,$data[0]["employeenumber"][0], $user_password);
                /*else
                    $err_msg = "You are not authorized to view this page!</br>Please contact your administrator!";*/
               // $login = urldecode($_GET[$_SESSION ["user"]]);
               // echo $login;
                //$home_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . '/app_messages.php';
               // header("Location: " . $home_url);
           }
                else{
                    $err_msg = "Username or Password incorrect!";
            }
        }
        else
            $err_msg = "Please fill in Username and Password!";
    }
}

if (empty($_SESSION["user"])) {
    echo "<div id='error_login'>" . $err_msg . "</div>";
    ?>
    <div id="login">
        <form method="post" action=<?php echo $_SERVER['PHP_SELF']; ?>>
            <table class='table_no_border'>
                <tr><td><label for="username">Username:</label></td><td><input type="text" name="username" placeholder='username' size='25' value="<?php if (!empty($user_username)) echo $user_username; ?>" autofocus="autofocus"></td></tr>
                <tr><td><label for="password">Password:</label></td><td><input type="password" size='25' name="password" placeholder='password'></td></tr>
                <tr><td colspan='2' class='center'><input class='text_button' type="submit" value="Login" name="submit" /></td></tr>
            </table>

        </form>
    </div>
    <?php
}
else {
    //echo "Succes";
    $home_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . '/../home.php';
    header("Location: " . $home_url);
}

require_once("../includes/footer.php");

function set_login ($conn,$id, $password) {
    $_SESSION ["user"] = get_student($conn,$id);
    $db_password = decrypt_password (get_password($conn,$id));
    if($db_password!=$password)
        update_password($conn,$id,encrypt_password($password));
    if($last_login = get_last_login ($conn, $_SESSION["user"]))
        update_last_login ($conn, $_SESSION["user"], date ("Y-m-d H:i:s"));
    else
        insert_last_login ($conn, $_SESSION["user"], date ("Y-m-d H:i:s"));
}
?>
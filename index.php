<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <link rel="icon" href="./img/icon.png" type="image/png" />
    <link rel="stylesheet" type="text/css" href="index_default.css">
    <script type="text/javascript" src="md5.js"></script>
    <script src="index_default.js"></script>
    <title>迷因之地</title>
<?php
    if(@$_SESSION['LOGIN'])
    {
        if(isset($_POST["move"]) && $_POST["move"] == "logout")
        {
            session_destroy();
            echo "<script type=\"text/javascript\">";
            echo "window.location.assign('index.php');";
            echo "</script>";
            exit;
        }
        else if(md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) != $_SESSION['ENV_CHECK'])
        {
            session_destroy();
            echo "<script type=\"text/javascript\">";
            echo "alert(\"系統已自動登出，請重新登入\");";
            echo "window.location.assign('index.php');";
            echo "</script>";
            exit;
        }
    }
    if(isset($_SESSION["MSG"]))
    {
        $msg = $_SESSION["MSG"];
        $msg = str_replace("\\", "\\\\", $msg);
        $msg = str_replace("'", "\\'", $msg);
        echo "<script type=\"text/javascript\">";
        echo "alert('".$msg."');";
        echo "</script>";
        unset($_SESSION["MSG"]);
    }
?>
</head>
<body>
    <!--////////////////////////////////////////////-->
    <!--/////////////////  Signup  /////////////////-->
    <!--////////////////////////////////////////////-->
    <div id="div_signup_background" style="display:none;" onclick="signup_close();">
        <div id="div_signup" onclick="window.event.stopPropagation();">
            <button id="button_signup_close" onclick="signup_close();">X</button>
            <div id="div_signup_content">
                <h2 style="margin:20px auto;">註冊</h2>
                <form id="form_signup" action="" method="post" onsubmit="return  signup_psw_encrypt();">
                    <input name="move" type="text" value="signup" style="display:none;">
                    <table id="table_signup_content">
                        <tr>
                            <td style="width:20%;">信箱</td>
                            <td style="height:70px;display:flex;justify-content:center;align-items:center;">
                                <input id="input_signup_email" type="email" name="signup_email" onchange="signup_email_check();" onfocusin="button_signup_email_duplicate.setAttribute('style', 'border:2px solid #2687ff;border-left:none;')" onfocusout="button_signup_email_duplicate.removeAttribute('style');" required>
                                <button id="button_signup_email_duplicate" type="button" onclick="input_signup_email.focus();" tabindex="-1"></button><!--✔✘-->
                            </td>
                        </tr>
                        <tr>
                            <td>密碼</td>
                            <td style="height:70px;display:flex;justify-content:center;align-items:center;">
                                <input id="input_signup_psw" type="password" name="signup_psw" onfocusin="button_signup_psw_visible.setAttribute('style', 'border:2px solid #2687ff;border-left:none;')" onfocusout="if(button_signup_psw_visible.getAttribute('style') != 'color:white;') button_signup_psw_visible.removeAttribute('style');" required>
                                <button id="button_signup_psw_visible" type="button" onmousedown="signup_psw_visible_ctrl('down');" onmouseup="signup_psw_visible_ctrl('up');" onmouseleave="signup_psw_visible_ctrl('leave');" title="顯示密碼" tabindex="-1">☀</button>
                            </td>
                        </tr>
                        <tr>
                            <td>暱稱</td>
                            <td>
                                <input id="input_signup_name" type="text" name="signup_name" maxlength="20">
                            </td>
                        </tr>
                        <tr>
                            <td>性別</td>
                            <td style="height:70px;display:flex;justify-content:center;align-items:center;">
                                <label style="margin:0 30px;">
                                    <input type="radio" class="input_signup_gender" name="signup_gender" value="1" checked>男
                                </label>
                                <label style="margin:0 30px;">
                                    <input type="radio" class="input_signup_gender" name="signup_gender" value="0">女
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>生日</td>
                            <td>
                                <input id="input_signup_birth" type="date" name="signup_birth" required>
                            </td>
                        </tr>
                    </table>
                    <button id="button_signup_submit">送出</button>
                </form>
            </div>
        </div>
    </div>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->



    <!--////////////////////////////////////////////-->
    <!--/////////////////  Login  //////////////////-->
    <!--////////////////////////////////////////////-->
    <div id="div_login_background" style="display:none;" onclick="login_close();">
        <div id="div_login" onclick="window.event.stopPropagation();">
            <button id="button_login_close" onclick="login_close();">X</button>
            <div id="div_login_content">
                <h2 style="margin:20px auto;">登入</h2>
                <form id="form_login" action="" method="post" onsubmit="login_psw_encrypt();">
                    <input name="move" type="text" value="login" style="display:none;">
                    <table id="table_login_content">
                        <tr>
                            <td style="width:10em;">信箱</td>
                            <td style="width:10em;">
                                <input id="input_login_email" type="email" name="login_email" onchange="login_email_check();" required>
                            </td>
                        </tr>
                        <tr>
                            <td>密碼</td>
                            <td>
                                <input id="input_login_psw" type="password" name="login_psw" required>
                            </td>
                        </tr>
                    </table>
                    <button id="button_login_submit">登入</button>
                </form>
            </div>
        </div>
    </div>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->



    <!--////////////////////////////////////////////-->
    <!--//////////////  Head Upload  ///////////////-->
    <!--////////////////////////////////////////////-->
    <div id="div_head_upload_background" style="display:none;" onclick="head_upload_close();">
        <div id="div_head_upload" onclick="window.event.stopPropagation();">
            <button id="button_head_upload_close" onclick="head_upload_close();">X</button>
            <div id="div_head_upload_content">
                <h2 style="margin:20px auto;">上傳頭貼</h2>
                <form action="ajax_response.php" method="post" enctype="multipart/form-data" onsubmit="head_upload_ajax();">
                    <input id="input_head_upload" type="file" name="head_upload" accept="image/*" onchange="head_upload_preview();">(大小限制:15MB)
                    <div id="div_head_upload_preview">
                        <img id="img_head_upload_preview" style="display:none;">
                    </div>
                    <button id="button_head_upload">送出</button>
                </form>
            </div>
        </div>
    </div>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->



    <!--////////////////////////////////////////////-->
    <!--///////////////  New Post  /////////////////-->
    <!--////////////////////////////////////////////-->
    <div id="div_new_post_background" style="display:none;" onclick="new_post_close();">
        <div id="div_new_post" onclick="window.event.stopPropagation();">
            <button id="button_new_post_close" onclick="new_post_close();">X</button>
            <div id="div_new_post_content">
                <h2 style="margin:20px auto;">新貼文</h2>
                <form id="form_new_post" action="test.php" method="post" enctype="multipart/form-data" onsubmit="new_post_ajax();">
                    <input id="input_new_post" type="file" name="new_post" accept="image/*" onchange="new_post_preview();">(大小限制:15MB)
                    <div id="div_new_post_preview">
                        <img id="img_new_post_preview" style="display:none;">
                    </div>
                    <textarea id="textarea_new_post" placeholder="想說些什麼...?" name="content" onkeydown="document.getElementsByTagName('body')[0].onbeforeunload = function() { return 1; };" maxlength="10000"></textarea>
                    <br>
                    <button id="button_new_post">送出</button>
                </form>
            </div>
        </div>
    </div>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->



    <!--////////////////////////////////////////////-->
    <!--///////////////  Edit Post  ////////////////-->
    <!--////////////////////////////////////////////-->
    <div id="div_edit_post_background" style="display:none;" onclick="edit_post_close();">
        <div id="div_edit_post" onclick="window.event.stopPropagation();">
            <button id="button_edit_post_close" onclick="edit_post_close();">X</button>
            <div id="div_edit_post_content">
                <h2 style="margin:20px auto;">編輯貼文</h2>
                <form id="form_edit_post" action="test.php" method="post" enctype="multipart/form-data" onsubmit="edit_post_ajax();">
                    <input id="input_edit_post" type="text" style="display:none">
                    <textarea id="textarea_edit_post" placeholder="想說些什麼...?" name="content" onkeydown="document.getElementsByTagName('body')[0].onbeforeunload = function() { return 1; };" maxlength="10000"></textarea>
                    <br>
                    <button id="button_edit_post">送出</button>
                </form>
            </div>
        </div>
    </div>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->



    <!--////////////////////////////////////////////-->
    <!--/////////////////  To top  /////////////////-->
    <!--////////////////////////////////////////////-->
    <button id="button_to_top" onclick="window.scrollTo(0, 0);" title="回到頂端">Top</button>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->

<?php
    if(isset($_POST["move"]))
    {
        switch($_POST["move"])
        {
            case "signup":
                $email = isset($_POST["signup_email"]) ? $_POST["signup_email"] : "";
                $email = str_replace("\\", "\\\\", $email);
                $email = str_replace("'", "\\'", $email);
                $psw = isset($_POST["signup_psw"]) ? $_POST["signup_psw"] : "";
                $psw = password_hash($psw, PASSWORD_DEFAULT);
                $name = isset($_POST["signup_name"]) ? $_POST["signup_name"] : "";
                $name = str_replace("\\", "\\\\", $name);
                $name = str_replace("'", "\\'", $name);
                $gender = isset($_POST["signup_gender"]) ? $_POST["signup_gender"] : "";
                $birth = isset($_POST["signup_birth"]) ? $_POST["signup_birth"] : "";

                include("database_config.php");
                $conn = new mysqli($database_host, $database_user, $database_password, $database_name);
                if ($conn->connect_error)
                {
                    error_log("Error : ".$conn->connect_error);
                    die("CONNECT_ERROR");
                }
                $conn->set_charset("utf8");
                $sql = "INSERT INTO `account` (`account_email`, `account_password`, `account_name`, `account_gender`, `account_birth`) VALUES ('".$email."', '".$psw."', '".$name."', b'".$gender."', '".$birth."');";
                if ($conn->query($sql) === FALSE)
                {
                    error_log("Error : ".$conn->error." in SQL : ".$sql);
                    $conn->close();
                    die("QUERY_ERROR");
                }
                else
                    echo "<script type=\"text/javascript\">alert('註冊成功');window.location.assign('index.php');</script>";
                $conn->close();
                break;

            case "login":
                $email = isset($_POST["login_email"]) ? $_POST["login_email"] : "";
                $email = str_replace("\\", "\\\\", $email);
                $email = str_replace("'", "\\'", $email);
                $psw = isset($_POST["login_psw"]) ? $_POST["login_psw"] : "";

                include("database_config.php");
                $conn = new mysqli($database_host, $database_user, $database_password, $database_name);
                if ($conn->connect_error)
                {
                    error_log("Error : ".$conn->connect_error);
                    die("CONNECT_ERROR");
                }
                $conn->set_charset("utf8");
                $sql = "SELECT * FROM `account` WHERE `account_email`='".$email."';";
                $result = $conn->query($sql);
                if ($result === FALSE)
                {
                    error_log("Error : ".$conn->error." in SQL : ".$sql);
                    $conn->close();
                    die("QUERY_ERROR");
                }
                else if($result->num_rows)
                {
                    $row = $result->fetch_assoc();
                    if(password_verify($psw, $row["account_password"]))
                    {
                        //$_SESSION["MSG"] = $row["account_name"]." 登入成功";
                        $_SESSION['LOGIN'] = $row["account_id"];
                        $_SESSION['ENV_CHECK'] = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);

                        echo "<script type=\"text/javascript\">";
                        echo "window.location.assign('index.php');";
                        echo "</script>";
                    }
                    else
                    {
                        $_SESSION["MSG"] = "信箱或密碼錯誤，登入失敗";
                        echo "<script type=\"text/javascript\">";
                        echo "window.location.assign('index.php');";
                        echo "</script>";
                    }
                }
                else
                {
                    $_SESSION["MSG"] = "信箱或密碼錯誤，登入失敗";
                    echo "<script type=\"text/javascript\">";
                    echo "window.location.assign('index.php');";
                    echo "</script>";
                }
                $conn->close();
                break;
        }
    }
    else
    {
        $login;
        if(@$_SESSION['LOGIN'])
            $login = true;
        else
            $login = false;
        $account = isset($_GET["id"]) ? $_GET["id"] : "";
        $self = null;
        $row = null;

        include("database_config.php");
        $conn = new mysqli($database_host, $database_user, $database_password, $database_name);
        if ($conn->connect_error)
        {
            error_log("Error : ".$conn->connect_error);
            die("CONNECT_ERROR");
        }
        $conn->set_charset("utf8");

        if($account)
        {
            $sql = "SELECT * FROM `account` WHERE `account_id`=".(int)$account.";";
            $result = $conn->query($sql);
            if ($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            else if($result->num_rows)
            {
                $row = $result->fetch_assoc();
                if($login && $row["account_id"] == $_SESSION["LOGIN"])
                    $self = true;
                else
                    $self = false;
                $account = (int)$account;
            }
            else
            {
                $account = false;
                $tmp = "<script type=\"text/javascript\">";
                $tmp .= "alert(\"無此用戶\");";
                $tmp .= "window.location.assign('index.php');";
                $tmp .= "</script>";
                die($tmp);
            }
        }
        else
        {
            $account = 0;
            $sql = "SELECT * FROM `account` WHERE `account_id`='".(@$_SESSION['LOGIN'] ? $_SESSION['LOGIN'] : 0)."';";
            $result = $conn->query($sql);
            if ($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            $row = $result->fetch_assoc();
        }

?>
    <!--////////////////////////////////////////////-->
    <!--//////////////////  Main  //////////////////-->
    <!--////////////////////////////////////////////-->
    <!--/////  Topbar  /////-->
    <div id="div_topbar">
        <div id="div_topbar_index">
            <a href="index.php">
                <img id="img_topbar_index" src="./img/index_img.png">
            </a>
        </div>
        <div id="div_topbar_button">
<?php
        if($login)
        {
            echo '<button onclick="logout();">登出</button>';
        }
        else
        {
            echo '<button onclick="signup_apear();">註冊</button><button onclick="login_apear();">登入</button>';
        }
?>
        </div>
    </div>
<?php
        if($login || $account)
        {
?>
    <!--/////  Person  /////-->
<?php
            $person_html = '<div id="div_person">';

            //Head image
            $person_html .= '<div id="div_person_img">';
            if($self === null)
                $person_html .= '<a href="index.php?id='.$row["account_id"].'">';
            $person_html .= '<img id="img_person"';
            //src
            if($row["account_head"] == null)
                $person_html .= ' src="'.($row["account_gender"] ? './img/male.png' : './img/female.png').'"';
            else
                $person_html .= ' src="data:image;base64,'.$row["account_head"].'"';
            //event
            if($self === true)
                $person_html .= ' onclick="head_upload_apear();" style="cursor:pointer;" title="更換頭貼">';
            else if($self === null)
                $person_html .= ' onmouseover="person_focus();" onmouseout="person_unfocus();" style="cursor:pointer;">';
            else
                $person_html .= '>';

            if($self === null)
                $person_html .= '</a>';
            $person_html .= '</div>';

            //Name
            $person_html .= '<div id="div_person_name">';
            if($self === null)
                $person_html .= '<a href="index.php?id='.$row["account_id"].'">';
            if($self === null)
                $person_html .= '<label id="label_person_name" onmouseover="person_focus();" onmouseout="person_unfocus();">'.$row["account_name"].'</label>';
            else
                $person_html .= '<label id="label_person_name">'.$row["account_name"].'</label>';
            if($self === null)
                $person_html .= '</a>';
            $person_html .= '</div>';

            //follow button
            if($login && $account && !$self)
            {
                $follow_sql = "SELECT * FROM `followership` WHERE `account_id_to` = ".$account." AND `account_id_from` = ".$_SESSION['LOGIN'].";";
                $follow_result = $conn->query($follow_sql);
                if ($follow_result === FALSE)
                {
                    error_log("Error : ".$conn->error." in SQL : ".$follow_sql);
                    $conn->close();
                    die("QUERY_ERROR");
                }
                if($follow_result->num_rows)
                    $person_html .= '<div id="div_person_follow"><button id="button_person_follow" onclick="person_follow('.$account.');">取消追蹤</button></div>';
                else
                    $person_html .= '<div id="div_person_follow"><button id="button_person_follow" onclick="person_follow('.$account.');">追蹤</button></div>';
            }

            $person_html .= '</div>';
            echo $person_html;
        }
?>
    <!--//////  Post  //////-->
<?php
        $post_html = '<div id="div_post" style="';
        //post div style
        if(!$login && $account === 0)
            $post_html .= 'margin:50px 20vw;">';
        else
            $post_html .= 'margin:50px 10vw 50px 30vw;">';
        echo $post_html;

        //javascript
?>
        <script type="text/javascript">
<?php
        if($self === null)
        {
            if($login)
            {
?>
            var global_get_post_flag = false;
            var global_like_post_flag = false;
            var global_dislike_post_flag = false;
            var post_class = "follow";

            window.onload = function(){
                get_post(post_class);
            };
            window.onscroll = function(){
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight)
                {
                    get_post(post_class, 5);
                }
            };
<?php
            }
            else
            {
?>
            var global_get_post_flag = false;
            var post_class = "hot";

            window.onload = function(){
                get_post(post_class);
            };
            window.onscroll = function(){
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight)
                {
                    get_post(post_class, 5);
                }
            };
<?php
            }
        }
        else if($self === true)
        {
?>
            var global_get_post_flag = false;
            var global_like_post_flag = false;
            var global_dislike_post_flag = false;

            window.onload = function(){
                get_person_post(<?php echo $account; ?>);
            };
            window.onscroll = function(){
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight)
                {
                    get_person_post(<?php echo $account; ?>, 5);
                }
            };
<?php
        }
        else if($self === false)
        {
?>
            var global_get_post_flag = false;
            var global_like_post_flag = false;
            var global_dislike_post_flag = false;
            var global_follow_flag = false;

            window.onload = function(){
                get_person_post(<?php echo $account; ?>);
            };
            window.onscroll = function(){
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight)
                {
                    get_person_post(<?php echo $account; ?>, 5);
                }
            };
<?php
        }
?>
        </script>
<?php
        //post class (for index)
        $post_html = '';
        if($self === null)
        {
            $post_html .= '<div id="div_post_class">';
            if($login)
            {
                $post_html .= '<label class="label_post_class" id="label_post_class_selected" post_type="follow">跟隨</label>';
                $post_html .= '<label class="label_post_class" onclick="post_class = \'hot\';post_class_change(post_class);" post_type="hot">熱門</label>';
                $post_html .= '<label class="label_post_class" onclick="post_class = \'like\';post_class_change(post_class);" post_type="like">喜歡</label>';
            }
            else
            {
                $post_html .= '<label class="label_post_class" id="label_post_class_selected">熱門</label>';
            }
            $post_html .= '</div>';
        }
        else if($self === true)
        {
            $post_html .= '<div id="div_post_new">';
            $post_html .= '<button id="button_post_new" onclick="new_post_apear();">新貼文</button>';
            $post_html .= '</div>';
        }
        $post_html .= '<div id="div_post_content"></div></div>';
        echo $post_html;
?>
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
    <!--////////////////////////////////////////////-->
<?php
        $conn->close();
    }
?>
</body>
</html>
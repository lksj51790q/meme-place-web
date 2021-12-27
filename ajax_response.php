<?php
    error_reporting(E_ERROR | E_PARSE);
    session_start();

    //single post html generator
    function single_post_html($row, $login, $like)
    {
        $html  = '<div class="div_single_post_place">';
        if($login && $_SESSION["LOGIN"] == $row["account_id"])
        {
            $html .= '<div class="div_single_post_delete">';
            $html .= '<button class="button_single_post_edit" onclick="edit_post(this);">✎</button>';
            $html .= '<button class="button_single_post_delete" onclick="delete_post(this);">X</button>';
            $html .= '</div>';
        }

        $html .= '<img class="img_single_post" src="data:image;base64,'.$row["post_img"].'">';
        $html .= '<div class="div_single_post_content">';
        $html .= nl2br($row["post_content"]);
        $html .= '</div>';
        $html .= '<div class="div_single_post_time">';
        $html .= '<label class="label_single_post_time">'.substr($row["post_time"], 0, 16).'</label>';
        $html .= '</div>';
        $html .= '<div class="div_single_post_bar">';
        $html .= '<div class="div_single_post_author">';
        $html .= '<a href="index.php?id='.$row["account_id"].'">';
        $html .= '<label class="label_single_post_author">';
        if($row["account_head"] == null)
            $html .= '<img class="img_single_post_author" src="'.($row["account_gender"] ? './img/male.png' : './img/female.png').'">';
        else
            $html .= '<img class="img_single_post_author" src="data:image;base64,'.$row["account_head"].'">';
        $html .= $row["account_name"].'</label></a>';
        $html .= '</div>';
        $html .= '<div class="div_single_post_button">';
        //Like Button
        if($like === true)
            $html .= '<button class="button_single_post_like_do" onclick="like_click(this, '.$row["post_id"].');" title="收回喜歡">'.$row["like"].'</button>';
        else if(!$login)
            $html .= '<button class="button_single_post_like" onclick="login_apear();" title="喜歡">'.$row["like"].'</button>';
        else
            $html .= '<button class="button_single_post_like" onclick="like_click(this, '.$row["post_id"].');" title="喜歡">'.$row["like"].'</button>';
        //Disike Button
        if($like === false)
            $html .= '<button class="button_single_post_dislike_do" onclick="dislike_click(this, '.$row["post_id"].');" title="收回討厭">'.$row["dislike"].'</button>';
        else if(!$login)
            $html .= '<button class="button_single_post_dislike" onclick="login_apear();" title="討厭">'.$row["dislike"].'</button>';
        else
            $html .= '<button class="button_single_post_dislike" onclick="dislike_click(this, '.$row["post_id"].');" title="討厭">'.$row["dislike"].'</button>';
        $html .= '<button class="button_single_post_comment" onclick="comment_display(this);" title="展開留言">'.$row["comment_count"].'</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="div_single_post_more_comment" style="display:none;">';
        $html .= '<button class="button_single_post_more_comment" onclick="more_comment(this);">顯示更舊留言</button>';
        $html .= '</div>';
        $html .= '<div class="div_single_post_comment" post_id="'.$row["post_id"].'"></div>';
        //New comment
        if($login)
        {
            $html .= '<div class="div_single_post_new_comment">';
            $html .= '<form onsubmit="new_comment_send(this);">';
            $html .= '<textarea class="textarea_single_post_new_comment" maxlength="10000"></textarea>';
            $html .= '<button class="button_single_post_new_comment">留言</button>';
            $html .= '</form>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    //Database configure
    include("database_config.php");
    $conn = new mysqli($database_host, $database_user, $database_password, $database_name);
    if ($conn->connect_error)
    {
        error_log("Error : ".$conn->connect_error);
        die("CONNECT_ERROR");
    }
    $conn->set_charset("utf8");

    //Do the command
    $move = isset($_POST["move"]) ? $_POST["move"] : "";
    switch($move)
    {
        case "signup_email_check"://Check if email have been used
            $email = isset($_POST["current_email"]) ? $_POST["current_email"] : "";
            $email = str_replace("\\", "\\\\", $email);
            $email = str_replace("'", "\\'", $email);

            $sql = "SELECT * FROM `account` WHERE `account_email`='".$email."';";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            if($result->num_rows != 0)
                echo "EMAIL_DUPLICATE";
            break;

        case "head_upload"://Upload new head image
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $image = addslashes($_FILES["head_upload"]["tmp_name"]);
            $image = file_get_contents($image);
            $image = base64_encode($image);

            $sql = "UPDATE `account` SET `account_head` ='".$image."' WHERE `account_id` = ".$_SESSION['LOGIN'].";";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            else
                echo $image;
            break;

        case "new_post"://Upload new post
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $image = addslashes($_FILES["new_post"]["tmp_name"]);
            $image = file_get_contents($image);
            $image = base64_encode($image);
            $content = isset($_POST["content"]) ? $_POST["content"] : "";
            $content = htmlentities($content);
            $content = str_replace("\\", "\\\\", $content);
            $content = str_replace("'", "\\'", $content);

            $sql = "INSERT INTO `post` (`post_img`, `account_id`, `post_content`) SELECT '".$image."', `account_id`, '".$content."' FROM `account` WHERE `account_id`=".$_SESSION['LOGIN'].";";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            break;

        case "get_post"://Get specific type's posts (FOLLOW HOT LIKE)
            $type = isset($_POST["type"]) ? $_POST["type"] : "";
            $num = isset($_POST["num"]) ? (int)$_POST["num"] : 0;
            $last_post_id = isset($_POST["last_post_id"]) ? (int)$_POST["last_post_id"] : 0;

            $post_array = Array();
            $post_array["single_post"] = Array();

            switch($type)
            {
                case "follow":
                    $login;
                    if(@$_SESSION['LOGIN'])
                        $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
                    else
                        $login = false;
                    if(!$login)
                    {
                        $conn->close();
                        die("AUTO_LOGOUT");
                    }

                    $sql = "SELECT `post_id`, `post_img`, `account_id`, `post_content`, `post_time`, `account_name`, `account_head`, `account_gender`, `account_email`, (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=1) AS 'like', (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=0) AS 'dislike', (SELECT COUNT(*) FROM `comment` WHERE `post_id` = `post`.`post_id`) AS 'comment_count' FROM `post` NATURAL JOIN `account` WHERE `account_id` IN (SELECT `account_id_to` FROM `followership` WHERE `account_id_from` = ".$_SESSION['LOGIN'].")";
                    if($last_post_id)
                        $sql .= " AND `post_id` < ".$last_post_id;
                    $sql .= " ORDER BY `post_id` DESC LIMIT ".$num.";";
                    $result = $conn->query($sql);
                    if($result === FALSE)
                    {
                        error_log("Error : ".$conn->error." in SQL : ".$sql);
                        $conn->close();
                        die("QUERY_ERROR");
                    }
                    while($row = $result->fetch_assoc())
                    {
                        $like = null;
                        $like_sql = "SELECT `likes` FROM `likes` WHERE `account_id` = ".$_SESSION['LOGIN']." AND `post_id` = ".$row["post_id"].";";
                        $like_result = $conn->query($like_sql);
                        if($like_result === FALSE)
                        {
                            error_log("Error : ".$conn->error." in SQL : ".$like_sql);
                            $conn->close();
                            die("QUERY_ERROR");
                        }
                        if($like_row = $like_result->fetch_assoc())
                            $like = $like_row["likes"] ? true : false;
                        $tmp_array = Array();
                        $tmp_array["post_id"] = $row["post_id"];
                        $tmp_array["html"] = single_post_html($row, $login, $like);
                        array_push($post_array["single_post"], $tmp_array);
                    }
                    break;
                case "hot":
                    $login;
                    if(@$_SESSION['LOGIN'])
                        $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
                    else
                        $login = false;

                    $sql = "SELECT `post_id`, `post_img`, `account_id`, `post_content`, `post_time`, `account_name`, `account_head`, `account_gender`, `account_email`, (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=1) AS 'like', (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=0) AS 'dislike', (SELECT COUNT(*) FROM `comment` WHERE `post_id` = `post`.`post_id`) AS 'comment_count' FROM `post` NATURAL JOIN `account` ORDER BY (`like`+`dislike`) DESC LIMIT ".$last_post_id.", ".$num.";";
                    $result = $conn->query($sql);
                    if($result === FALSE)
                    {
                        error_log("Error : ".$conn->error." in SQL : ".$sql);
                        $conn->close();
                        die("QUERY_ERROR");
                    }
                    while($row = $result->fetch_assoc())
                    {
                        if(($row["like"] + $row["dislike"]) < 5)//HOT LIMIT
                            break;
                        $like = null;
                        if($login)
                        {
                            $like_sql = "SELECT `likes` FROM `likes` WHERE `account_id` = ".$_SESSION['LOGIN']." AND `post_id` = ".$row["post_id"].";";
                            $like_result = $conn->query($like_sql);
                            if($like_result === FALSE)
                            {
                                error_log("Error : ".$conn->error." in SQL : ".$like_sql);
                                $conn->close();
                                die("QUERY_ERROR");
                            }
                            if($like_row = $like_result->fetch_assoc())
                                $like = $like_row["likes"] ? true : false;
                        }
                        $tmp_array = Array();
                        $tmp_array["post_id"] = $row["post_id"];
                        $tmp_array["html"] = single_post_html($row, $login, $like);
                        array_push($post_array["single_post"], $tmp_array);
                    }
                    break;
                case "like":
                    $login;
                    if(@$_SESSION['LOGIN'])
                        $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
                    else
                        $login = false;
                    if(!$login)
                    {
                        $conn->close();
                        die("AUTO_LOGOUT");
                    }

                    $sql = "SELECT `post_id`, `post_img`, `account_id`, `post_content`, `post_time`, `account_name`, `account_head`, `account_gender`, `account_email`, (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=1) AS 'like', (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=0) AS 'dislike', (SELECT COUNT(*) FROM `comment` WHERE `post_id` = `post`.`post_id`) AS 'comment_count' FROM `post` NATURAL JOIN `account` WHERE `post_id` IN (SELECT `post_id` FROM `likes` WHERE `likes` = 1 AND `account_id` = ".$_SESSION["LOGIN"].")";
                    if($last_post_id)
                        $sql .= " AND `post_id` < ".$last_post_id;
                    $sql .= " ORDER BY `post_id` DESC LIMIT ".$num.";";
                    $result = $conn->query($sql);
                    if($result === FALSE)
                    {
                        error_log("Error : ".$conn->error." in SQL : ".$sql);
                        $conn->close();
                        die("QUERY_ERROR");
                    }
                    while($row = $result->fetch_assoc())
                    {
                        $like = null;
                        $like_sql = "SELECT `likes` FROM `likes` WHERE `account_id` = ".$_SESSION["LOGIN"]." AND `post_id` = ".$row["post_id"].";";
                        $like_result = $conn->query($like_sql);
                        if($like_result === FALSE)
                        {
                            error_log("Error : ".$conn->error." in SQL : ".$like_sql);
                            $conn->close();
                            die("QUERY_ERROR");
                        }
                        if($like_row = $like_result->fetch_assoc())
                            $like = $like_row["likes"] ? true : false;
                        $tmp_array = Array();
                        $tmp_array["post_id"] = $row["post_id"];
                        $tmp_array["html"] = single_post_html($row, $login, $like);
                        array_push($post_array["single_post"], $tmp_array);
                    }
                    break;
            }
            echo json_encode($post_array);
            break;

        case "person_post"://Get specific account's posts
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;

            $account_id = isset($_POST["account_id"]) ? (int)$_POST["account_id"] : 0;
            $num = isset($_POST["num"]) ? (int)$_POST["num"] : 0;
            $last_post_id = isset($_POST["last_post_id"]) ? (int)$_POST["last_post_id"] : 0;

            $post_array = Array();
            $post_array["single_post"] = Array();

            //Check account ID
            $sql = "SELECT `account_id`, `account_head`, `account_name`, `account_gender` FROM `account` WHERE `account_id`=".$account_id.";";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            if($result->num_rows == 0)
            {
                $conn->close();
                die("ID_NOT_FOUND");
            }
            $row = $result->fetch_assoc();
            $author_id = $row["account_id"];
            $author_img = $row["account_head"];
            $author_name = $row["account_name"];
            $author_gender = $row["account_gender"];

            //Query for posts
            $sql = "SELECT `post_id`, `post_img`, `account_id`, `post_content`, `post_time`, (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=1) AS 'like', (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=0) AS 'dislike', (SELECT COUNT(*) FROM `comment` WHERE `post_id` = `post`.`post_id`) AS 'comment_count'  FROM `post` WHERE `account_id` = ".$account_id;
            if($last_post_id)
                $sql .= " AND `post_id` < ".$last_post_id;
            $sql .= " ORDER BY `post_id` DESC LIMIT ".$num.";";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }

            //Iterate all posts
            while($row = $result->fetch_assoc())
            {
                //Check if user already like this post
                $like = null;
                if($login)
                {
                    $like_sql = "SELECT `likes` FROM `likes` WHERE `account_id` = ".$_SESSION['LOGIN']." AND `post_id` = ".$row["post_id"].";";
                    $like_result = $conn->query($like_sql);
                    if($like_result === FALSE)
                    {
                        error_log("Error : ".$conn->error." in SQL : ".$like_sql);
                        $conn->close();
                        die("QUERY_ERROR");
                    }
                    if($like_row = $like_result->fetch_assoc())
                        $like = $like_row["likes"] ? true : false;
                }

                //
                //Construct HTML code
                //
                $html_tmp  = '<div class="div_single_post_place">';
                if($login && $_SESSION['LOGIN'] == $author_id)
                {
                    $html_tmp .= '<div class="div_single_post_delete">';
                    $html_tmp .= '<button class="button_single_post_edit" onclick="edit_post(this);">✎</button>';
                    $html_tmp .= '<button class="button_single_post_delete" onclick="delete_post(this);">X</button>';
                    $html_tmp .= '</div>';
                }
                $html_tmp .= '<img class="img_single_post" src="data:image;base64,'.$row["post_img"].'">';
                $html_tmp .= '<div class="div_single_post_content">';
                $html_tmp .= nl2br($row["post_content"]);
                $html_tmp .= '</div>';
                $html_tmp .= '<div class="div_single_post_time">';
                $html_tmp .= '<label class="label_single_post_time">'.substr($row["post_time"], 0, 16).'</label>';
                $html_tmp .= '</div>';
                $html_tmp .= '<div class="div_single_post_bar">';
                $html_tmp .= '<div class="div_single_post_author">';
                $html_tmp .= '<a href="index.php?id='.$account_id.'">';
                $html_tmp .= '<label class="label_single_post_author">';
                if($author_img == null)
                    $html_tmp .= '<img class="img_single_post_author" src="'.($author_gender ? './img/male.png' : './img/female.png').'">';
                else
                    $html_tmp .= '<img class="img_single_post_author" src="data:image;base64,'.$author_img.'">';
                $html_tmp .= $author_name.'</label></a>';
                $html_tmp .= '</div>';
                $html_tmp .= '<div class="div_single_post_button">';
                //Like Button
                if($like === true)
                    $html_tmp .= '<button class="button_single_post_like_do" onclick="like_click(this, '.$row["post_id"].');" title="收回喜歡">'.$row["like"].'</button>';
                else if(!$login)
                    $html_tmp .= '<button class="button_single_post_like" onclick="login_apear();" title="喜歡">'.$row["like"].'</button>';
                else
                    $html_tmp .= '<button class="button_single_post_like" onclick="like_click(this, '.$row["post_id"].');" title="喜歡">'.$row["like"].'</button>';
                //Disike Button
                if($like === false)
                    $html_tmp .= '<button class="button_single_post_dislike_do" onclick="dislike_click(this, '.$row["post_id"].');" title="收回討厭">'.$row["dislike"].'</button>';
                else if(!$login)
                    $html_tmp .= '<button class="button_single_post_dislike" onclick="login_apear();" title="討厭">'.$row["dislike"].'</button>';
                else
                    $html_tmp .= '<button class="button_single_post_dislike" onclick="dislike_click(this, '.$row["post_id"].');" title="討厭">'.$row["dislike"].'</button>';
                $html_tmp .= '<button class="button_single_post_comment" onclick="comment_display(this);" title="展開留言">'.$row["comment_count"].'</button>';
                $html_tmp .= '</div>';
                $html_tmp .= '</div>';
                $html_tmp .= '<div class="div_single_post_more_comment" style="display:none;">';
                $html_tmp .= '<button class="button_single_post_more_comment" onclick="more_comment(this);">顯示更舊留言</button>';
                $html_tmp .= '</div>';
                $html_tmp .= '<div class="div_single_post_comment" post_id="'.$row["post_id"].'"></div>';
                //New comment
                if($login)
                {
                    $html_tmp .= '<div class="div_single_post_new_comment">';
                    $html_tmp .= '<form onsubmit="new_comment_send(this);">';
                    $html_tmp .= '<textarea class="textarea_single_post_new_comment" maxlength="10000"></textarea>';
                    $html_tmp .= '<button class="button_single_post_new_comment">留言</button>';
                    $html_tmp .= '</form>';
                    $html_tmp .= '</div>';
                }
                $html_tmp .= '</div>';
                $tmp_array = Array();
                $tmp_array["post_id"] = $row["post_id"];
                $tmp_array["html"] = $html_tmp;
                //$tmp_array["html"] = single_post_html($row, $login, $like, $author_img, $author_name);
                array_push($post_array["single_post"], $tmp_array);
            }
            echo json_encode($post_array);
            break;

        case "like_post"://Someone like the post
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;

            $sql = "INSERT INTO `likes` (`post_id`, `account_id`, `likes`) VALUES (".$post_id.", ".$_SESSION['LOGIN'].", b'1');";
            if($conn->query($sql) === FALSE)
                echo "DISLIKE";
            else
                echo "like_post";
            break;

        case "like_post_recover"://Someone don't like the post anymore
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;

            $sql = "DELETE FROM `likes` WHERE `post_id` = ".$post_id." AND `account_id` = ".$_SESSION["LOGIN"]." AND `likes` = b'1'";
            $conn->query($sql);
            echo "like_post_recover";
            break;

        case "dislike_post"://Someone dislike the post
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;

            $sql = "INSERT INTO `likes` (`post_id`, `account_id`, `likes`) VALUES (".$post_id.", ".$_SESSION["LOGIN"].", b'0');";
            if ($conn->query($sql) === FALSE)
                echo "LIKE";
            else
                echo "dislike_post";
            break;

        case "dislike_post_recover"://Someone don't dislike the post anymore
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;

            $sql = "DELETE FROM `likes` WHERE `post_id` = ".$post_id." AND `account_id` = ".$_SESSION["LOGIN"]." AND `likes` = b'0'";
            $conn->query($sql);
            echo "dislike_post_recover";
            break;

        case "get_post_comment"://Get specific post's comments
            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;
            $num = isset($_POST["num"]) ? (int)$_POST["num"] : 0;
            $last_comment_floor = isset($_POST["last_comment_floor"]) ? (int)$_POST["last_comment_floor"] : 0;

            $comment_array = Array();
            $comment_array["single_comment"] = Array();
            $comment_array["have_more"] = false;
            $comment_count = 0;

            $sql = "SELECT `account_id`, `account_head`, `account_name`, `account_gender`, `comment_floor`, `comment_content`, `post_time` FROM `account` NATURAL JOIN `comment` WHERE `post_id` = ".$post_id;
            if($last_comment_floor)
                $sql .= " AND `comment_floor` < ".$last_comment_floor;
            $sql .= " ORDER BY `comment_floor` DESC LIMIT ".($num+1).";";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            while($row = $result->fetch_assoc())
            {
                $comment_count++;
                if($comment_count > $num)
                {
                    $comment_array["have_more"] = true;
                    break;
                }
                $html_tmp = '<div class="div_single_post_single_comment_author">';
                /**/
                $html_tmp .= '<a href="index.php?id='.$row["account_id"].'">';
                $html_tmp .= '<label class="label_single_post_single_comment_author">';
                if($row["account_head"] == null)
                    $html_tmp .= '<img class="img_single_post_single_comment_author" src="'.($row["account_gender"] ? './img/male.png' : './img/female.png').'">';
                else
                    $html_tmp .= '<img class="img_single_post_single_comment_author" src="data:image;base64,'.$row["account_head"].'">';
                $html_tmp .= $row["account_name"].'</label>';
                $html_tmp .= '</a>';
                $html_tmp .= '</div>';
                $html_tmp .= '<div class="div_single_post_single_comment_content" title="'.substr($row["post_time"], 5, 11).'">';
                $html_tmp .= nl2br($row["comment_content"]);
                $html_tmp .= '</div>';
                $tmp_array = Array();
                $tmp_array["html"] = $html_tmp;
                $tmp_array["floor"] = $row["comment_floor"];
                array_push($comment_array["single_comment"], $tmp_array);
            }
            echo json_encode($comment_array);
            break;

        case "new_comment"://Some one send a comment to the post
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;
            $content = isset($_POST["content"]) ? $_POST["content"] : "";
            $content = htmlentities($content);
            $content = str_replace("\\", "\\\\", $content);
            $content = str_replace("'", "\\'", $content);

            $sql = "INSERT INTO `comment` (`post_id`, `comment_floor`, `account_id`, `comment_content`) SELECT ".$post_id.", (SELECT COALESCE(MAX(`comment_floor`)+1, 1) FROM `comment` WHERE `post_id` = ".$post_id."), `account_id`, '".$content."' FROM `account` WHERE `account_id`=".$_SESSION["LOGIN"].";";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            break;

        case "follow_add"://Someone want to follow someone
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $account_id = isset($_POST["account_id"]) ? (int)$_POST["account_id"] : 0;

            $sql = "INSERT INTO `followership` (`account_id_from`, `account_id_to`) VALUES (".$_SESSION["LOGIN"].", ".$account_id.");";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            break;

        case "follow_cancel"://Someone don't want to follow someone anymore
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $account_id = isset($_POST["account_id"]) ? (int)$_POST["account_id"] : 0;
            $sql = "DELETE FROM `followership` WHERE `account_id_from` = ".$_SESSION["LOGIN"]." AND `account_id_to` = ".$account_id.";";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            break;

        case "delete_post"://Someone want to delete the post
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;

            $sql = "DELETE FROM `post` WHERE `post_id` = ".$post_id.";";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            break;

        case "get_current_post"://When Someone want to edit the post, get current post content
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }

            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;

            $sql = "SELECT `post_content` FROM `post` WHERE `post_id` = ".$post_id." AND `account_id` = ".$_SESSION["LOGIN"].";";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            if($row = $result->fetch_assoc())
                echo $row["post_content"];
            break;

        case "update_post"://Update new post content
            $login;
            if(@$_SESSION['LOGIN'])
                $login = (md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']) == $_SESSION['ENV_CHECK']) ? true : false;
            else
                $login = false;
            if(!$login)
            {
                $conn->close();
                die("AUTO_LOGOUT");
            }
            $post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;
            $content = isset($_POST["content"]) ? $_POST["content"] : "";
            $content = htmlentities($content);
            $content = str_replace("\\", "\\\\", $content);
            $content = str_replace("'", "\\'", $content);

            $sql = "UPDATE `post` SET `post_content` = '".$content."' WHERE `post_id` = ".$post_id." AND `account_id` = ".$_SESSION["LOGIN"].";";
            if ($conn->query($sql) === FALSE)
            {
                error_log("Error : ".$conn->error." in SQL : ".$sql);
                $conn->close();
                die("QUERY_ERROR");
            }
            break;

    }


    $conn->close();
?>
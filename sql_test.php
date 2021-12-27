<?php
include("database_config.php");
$conn = new mysqli($database_host, $database_user, $database_password, $database_name);
if ($conn->connect_error)
    die("資料庫連接錯誤 : ".$conn->connect_error."<br>");
$post_id;
$post_img;
$account_id;
$account_name;
$account_head;
$account_gender;
$account_email;
$post_content;
$post_time;
$like;
$dislike;
$comment_count;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$start = microtime(true);
for($i=0; $i<1000; $i++)
{
    $sql = "SELECT `post_id`, `post_img`, `account_id`, `post_content`, `post_time`, `account_name`, `account_head`, `account_gender`, `account_email`, (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=1) AS 'like', (SELECT COUNT(*) FROM `likes` WHERE `post_id` = `post`.`post_id` AND `likes`=0) AS 'dislike', (SELECT COUNT(*) FROM `comment` WHERE `post_id` = `post`.`post_id`) AS 'comment_count' FROM `post` NATURAL JOIN `account` WHERE `account_id` IN (SELECT `account_id_to` FROM `followership` WHERE `account_id_from` = 6) AND `post_id` < 13  ORDER BY `post_id` DESC LIMIT 5;";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc())
    {
        $post_id = $row["post_id"];
        $post_img = $row["post_img"];
        $account_id = $row["account_id"];
        $post_content = $row["post_content"];
        $post_time = $row["post_time"];
        $account_name = $row["account_name"];
        $account_head = $row["account_head"];
        $account_gender = $row["account_gender"];
        $account_email = $row["account_email"];
        $like = $row["like"];
        $dislike = $row["dislike"];
        $comment_count = $row["comment_count"];
    }
}
$end = microtime(true);
echo "One SQL : ".($end - $start)."<br>";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$start = microtime(true);
for($i=0; $i<1000; $i++)
{
    $sql = "SELECT `post_id`, `post_img`, `account_id`, `post_content`, `post_time`, `account_name`, `account_head`, `account_gender`, `account_email`, (SELECT COUNT(*) FROM `comment` WHERE `post_id` = `post`.`post_id`) AS 'comment_count' FROM `post` NATURAL JOIN `account` WHERE `account_id` IN (SELECT `account_id_to` FROM `followership` WHERE `account_id_from` = 6) AND `post_id` < 13  ORDER BY `post_id` DESC LIMIT 5;";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc())
    {
        $post_id = $row["post_id"];
        $post_img = $row["post_img"];
        $account_id = $row["account_id"];
        $post_content = $row["post_content"];
        $post_time = $row["post_time"];
        $account_name = $row["account_name"];
        $account_head = $row["account_head"];
        $account_gender = $row["account_gender"];
        $account_email = $row["account_email"];
        $comment_count = $row["comment_count"];
        $like_sql = "SELECT `likes`, COUNT(*) as 'count' FROM `likes` WHERE `post_id` = ".$row["post_id"]." GROUP BY `likes`;";
        $like_result = $conn->query($like_sql);
        while($like_row = $like_result->fetch_assoc())
        {
            if($like_row["likes"] == 1)
                $like = $like_row["count"];
            else
                $dislike = $like_row["count"];
        }
    }
}
$end = microtime(true);
echo "Mutiple SQL : ".($end - $start)."<br>";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$conn->close();
?>
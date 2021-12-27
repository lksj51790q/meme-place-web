////////////////////////////////////////////
/////////////////  Signup  /////////////////
////////////////////////////////////////////
function signup_apear()
{
    div_signup_background.removeAttribute('style');
    input_signup_email.focus();
    return;
}
function signup_close()
{
    div_signup_background.setAttribute('style', 'display:none;');
    return;
}
function signup_email_check()
{
    var xhttp = new XMLHttpRequest();
    var current_email = input_signup_email.value;
    var move = "signup_email_check";
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "EMAIL_DUPLICATE")
            {
                button_signup_email_duplicate.innerHTML = "✖";
                button_signup_email_duplicate.setAttribute("title", "Email已被使用");
            }
            else if(this.responseText == "")
            {
                button_signup_email_duplicate.innerHTML = "";
                button_signup_email_duplicate.removeAttribute("title");
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("current_email=" + current_email + "&move=" + move);
    return;
}
function signup_psw_visible_ctrl(move)
{
    if(move == "down")
    {
        button_signup_psw_visible.setAttribute("style", "color:white;");
        input_signup_psw.setAttribute('type', 'text');
    }
    else if(move == "up")
    {
        button_signup_psw_visible.removeAttribute("style");
        input_signup_psw.setAttribute('type', 'password');
    }
    else
    {
        if(button_signup_psw_visible.getAttribute("style") != "border:2px solid #2687ff;border-left:none;")
        {
            button_signup_psw_visible.removeAttribute("style");
            input_signup_psw.setAttribute('type', 'password');
        }
    }
    return;
}
function signup_psw_encrypt()
{
    if(button_signup_email_duplicate.innerHTML != "")
    {
        alert("Email已被使用");
        return false;
    }
    input_signup_psw.setAttribute('type', 'password');
    input_signup_psw.value = hex_md5(input_signup_psw.value);
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
/////////////////  Login  //////////////////
////////////////////////////////////////////
function login_apear()
{
    div_login_background.removeAttribute('style');
    input_login_email.focus();
    return;
}
function login_close()
{
    div_login_background.setAttribute('style', 'display:none;');
    return;
}
function login_psw_encrypt()
{
    input_login_psw.setAttribute('type', 'password');
    input_login_psw.value = hex_md5(input_login_psw.value);
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
/////////////////  Logout  /////////////////
////////////////////////////////////////////
function logout()
{
    var f = document.createElement("form");
    f.setAttribute('method',"post");
    f.setAttribute('action',"index.php");
    f.setAttribute('style',"display:none;");

    var i = document.createElement("input");
    i.setAttribute('type',"text");
    i.setAttribute('name',"move");
    i.setAttribute('value',"logout");

    f.appendChild(i);
    document.getElementsByTagName('body')[0].appendChild(f);
    f.submit();
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
//////////////  Head Upload  ///////////////
////////////////////////////////////////////
function head_upload_apear()
{
    div_head_upload_background.removeAttribute('style');
    return;
}
function head_upload_close()
{
    document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
    div_head_upload_background.setAttribute('style', 'display:none;');
    return;
}
function head_upload_preview()
{
    document.getElementsByTagName("body")[0].onbeforeunload = function() { return 1; };
    var file = input_head_upload.files[0];
    if (file)
    {
        var URL = window.URL || window.webkitURL;
        var img_url = URL.createObjectURL(file);
        img_head_upload_preview.removeAttribute("style");
        img_head_upload_preview.setAttribute("src", img_url);
    }
    else
    {
        img_head_upload_preview.setAttribute("style", "display:none;");
        document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
    }
    return;
}
function head_upload_ajax()
{
    event.preventDefault();
    var file = input_head_upload.files[0];
    if(file.size > 15728640)//15MB
    {
        alert("上傳檔案過大(15MB)");
        return false;
    }
    var formData = new FormData();
    formData.append('head_upload', file, file.name);
    formData.append('move', 'head_upload');

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
                location.reload();
            }
            else
            {
                img_person.setAttribute("src", "data:image;base64," + this.responseText);
                head_upload_close();
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
///////////////  New Post  /////////////////
////////////////////////////////////////////
function new_post_apear()
{
    div_new_post_background.removeAttribute('style');
    return;
}
function new_post_close()
{
    document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
    div_new_post_background.setAttribute('style', 'display:none;');
    return;
}
function new_post_preview()
{
    document.getElementsByTagName("body")[0].onbeforeunload = function() { return 1; };
    var file = input_new_post.files[0];
    if (file)
    {
        var URL = window.URL || window.webkitURL;
        var img_url = URL.createObjectURL(file);
        img_new_post_preview.removeAttribute("style");
        img_new_post_preview.setAttribute("src", img_url);
    }
    else
    {
        document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
        img_new_post_preview.setAttribute("style", "display:none;");
    }
    return;
}
function new_post_ajax()
{
    event.preventDefault();
    var file = input_new_post.files[0];
    if(file.size > 15728640)//15MB
    {
        alert("上傳檔案過大");
        return false;
    }
    var formData = new FormData();
    formData.append('new_post', file, file.name);
    formData.append('move', 'new_post');
    formData.append('content', textarea_new_post.value);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
                location.reload();
            }
            else
            {
                new_post_close();
                location.reload();
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
///////////////  New Post  /////////////////
////////////////////////////////////////////
function edit_post_apear(text)
{
    div_edit_post_background.removeAttribute('style');
    textarea_edit_post.value = text;
    return;
}
function edit_post_close()
{
    document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
    div_edit_post_background.setAttribute('style', 'display:none;');
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
/////////////////  Person  /////////////////
////////////////////////////////////////////
function person_focus()
{
    label_person_name.setAttribute("style", "text-decoration:underline;cursor:pointer;");
    return;
}
function person_unfocus()
{
    label_person_name.removeAttribute("style");
    return;
}
function person_click(id)
{
    window.location.assign('index.php?id='+id);
    return;
}
function person_follow(id)
{
    if(global_follow_flag)
        return;
    else
        global_follow_flag = true;

    var formData = new FormData();
    var move = (button_person_follow.innerHTML == "追蹤") ? "follow_add" : "follow_cancel";
    formData.append('move', move);
    formData.append('account_id', id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                location.reload();
            }
            else
            {
                button_person_follow.innerHTML = (move == "follow_add") ? "取消追蹤" : "追蹤";
                global_follow_flag = false;
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////



////////////////////////////////////////////
/////////////////  Post  /////////////////
////////////////////////////////////////////
function get_post(post_type, num=10)
{
    if(global_get_post_flag)
        return;
    else
        global_get_post_flag = true;
    if(div_post_content.childNodes.length != 0)
        last_post_id = parseInt(div_post_content.childNodes[div_post_content.childNodes.length-1].getAttribute("post_id"));
    else
        last_post_id = 0;
    if(post_type == "hot")
        last_post_id = div_post_content.childNodes.length;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else
            {
                var posts = JSON.parse(this.responseText);
                if(label_post_class_selected.getAttribute("post_type") != post_type)
                    return;
                if(posts["single_post"].length == 0)
                {
                    var new_div = document.createElement("div");
                    new_div.setAttribute("style", "width:100%;text-align:center;margin:20px 0;");
                    new_div.innerHTML = "沒有更多";
                    div_post_content.appendChild(new_div);
                }
                else
                {
                    for(var i = 0; i < posts["single_post"].length; i++)
                    {
                        var new_div = document.createElement("div");
                        new_div.setAttribute("class", "div_single_post");
                        new_div.setAttribute("post_id", posts["single_post"][i]["post_id"]);
                        new_div.innerHTML = posts["single_post"][i]["html"];
                        div_post_content.appendChild(new_div);
                    }
                    global_get_post_flag = false;
                }
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if(last_post_id)
        xhttp.send("move=get_post&type=" + post_type + "&num=" + num + "&last_post_id=" + last_post_id);
    else
        xhttp.send("move=get_post&type=" + post_type + "&num=" + num);
    return;
}
function get_person_post(account_id, num=10)
{
    if(global_get_post_flag)
        return;
    else
        global_get_post_flag = true;
    if(div_post_content.childNodes.length != 0)
        last_post_id = parseInt(div_post_content.childNodes[div_post_content.childNodes.length-1].getAttribute("post_id"));
    else
        last_post_id = 0;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "ID_NOT_FOUND")
            {
                ;
            }
            else
            {
                var posts = JSON.parse(this.responseText);
                if(posts["single_post"].length == 0)
                {
                    var new_div = document.createElement("div");
                    new_div.setAttribute("style", "width:100%;text-align:center;margin:20px 0;");
                    new_div.innerHTML = "沒有更多";
                    div_post_content.appendChild(new_div);
                }
                else
                {
                    for(var i = 0; i < posts["single_post"].length; i++)
                    {
                        var new_div = document.createElement("div");
                        new_div.setAttribute("class", "div_single_post");
                        new_div.setAttribute("post_id", posts["single_post"][i]["post_id"]);
                        new_div.innerHTML = posts["single_post"][i]["html"];
                        div_post_content.appendChild(new_div);
                    }
                    global_get_post_flag = false;
                }
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if(last_post_id)
        xhttp.send("move=person_post&account_id=" + account_id + "&num=" + num + "&last_post_id=" + last_post_id);
    else
        xhttp.send("move=person_post&account_id=" + account_id + "&num=" + num);
    return;
}
function post_class_change(type)
{
    var class_label = document.getElementsByClassName("label_post_class");
    for(var i = 0; i < class_label.length; i++)
    {
        if(class_label[i].innerHTML == "跟隨")
        {
            if(type == "follow")
            {
                class_label[i].removeAttribute("onclick");
                class_label[i].setAttribute("id", "label_post_class_selected");
            }
            else
            {
                class_label[i].setAttribute("onclick", "post_class = 'follow';post_class_change(post_class);");
                class_label[i].removeAttribute("id");
            }
        }
        else if(class_label[i].innerHTML == "熱門")
        {
            if(type == "hot")
            {
                class_label[i].removeAttribute("onclick");
                class_label[i].setAttribute("id", "label_post_class_selected");
            }
            else
            {
                class_label[i].setAttribute("onclick", "post_class = 'hot';post_class_change(post_class);");
                class_label[i].removeAttribute("id");
            }
        }
        else if(class_label[i].innerHTML == "喜歡")
        {
            if(type == "like")
            {
                class_label[i].removeAttribute("onclick");
                class_label[i].setAttribute("id", "label_post_class_selected");
            }
            else
            {
                class_label[i].setAttribute("onclick", "post_class = 'like';post_class_change(post_class);");
                class_label[i].removeAttribute("id");
            }
        }
    }
    div_post_content.innerHTML = "";
    global_get_post_flag = false;
    get_post(type);
    return;
}
function like_click(target, post_id)
{
    if(global_like_post_flag)
        return;
    else
        global_like_post_flag = true;

    var move = (target.getAttribute("class") == "button_single_post_like") ? "like_post" : "like_post_recover";
    var formData = new FormData();
    formData.append('move', move);
    formData.append('post_id', post_id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                location.reload();
            }
            else if(this.responseText == "DISLIKE")
            {
                alert('您已按討厭');
            }
            else if(this.responseText == "like_post")
            {
                target.setAttribute("class", "button_single_post_like_do");
                target.setAttribute("title", "收回喜歡");
                target.innerHTML = parseInt(target.innerHTML) + 1;
            }
            else if(this.responseText == "like_post_recover")
            {
                target.setAttribute("class", "button_single_post_like");
                target.setAttribute("title", "喜歡");
                target.innerHTML = parseInt(target.innerHTML) - 1;
            }
            global_like_post_flag = false;
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
function dislike_click(target, post_id)
{
    if(global_dislike_post_flag)
        return;
    else
        global_dislike_post_flag = true;

    var move = (target.getAttribute("class") == "button_single_post_dislike") ? "dislike_post" : "dislike_post_recover";
    var formData = new FormData();
    formData.append('move', move);
    formData.append('post_id', post_id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                location.reload();
            }
            else if(this.responseText == "LIKE")
            {
                alert('您已按喜歡');
            }
            else if(this.responseText == "dislike_post")
            {
                target.setAttribute("class", "button_single_post_dislike_do");
                target.setAttribute("title", "收回討厭");
                target.innerHTML = parseInt(target.innerHTML) + 1;
            }
            else if(this.responseText == "dislike_post_recover")
            {
                target.setAttribute("class", "button_single_post_dislike");
                target.setAttribute("title", "討厭");
                target.innerHTML = parseInt(target.innerHTML) - 1;
            }
            global_dislike_post_flag = false;
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
function comment_display(target)
{
    var comment_div = target.parentNode.parentNode.parentNode.querySelector(".div_single_post_comment");
    var more_comment_div = target.parentNode.parentNode.parentNode.querySelector(".div_single_post_more_comment");
    if(target.getAttribute("class") == "button_single_post_comment")
    {
        target.setAttribute("class", "button_single_post_comment_display");
        target.setAttribute("title", "收起留言");
        more_comment_div.removeAttribute("style");
        get_comment(comment_div, 10);
    }
    else
    {
        target.setAttribute("class", "button_single_post_comment");
        target.setAttribute("title", "展開留言");
        more_comment_div.setAttribute("style", "display:none;");
        comment_div.innerHTML = "";
    }
    return;
}
function get_comment(comment_div, num)
{
    //Get the last floor
    var floor = 0;
    if(comment_div.childNodes[0])
        floor = parseInt(comment_div.childNodes[0].getAttribute("floor"));
    var post_id = comment_div.getAttribute("post_id");

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else
            {
                var comments = JSON.parse(this.responseText);
                for(var i = 0; i < comments["single_comment"].length; i++)
                {
                    var new_div = document.createElement("div");
                    new_div.setAttribute("class", "div_single_post_single_comment");
                    new_div.setAttribute("floor", comments["single_comment"][i]["floor"]);
                    new_div.innerHTML = comments["single_comment"][i]["html"];
                    comment_div.insertBefore(new_div, comment_div.childNodes[0]);
                }
                if(!comments["have_more"])
                {
                    /*var new_div = document.createElement("div");
                    new_div.setAttribute("style", "width:100%;text-align:center;margin:20px 0;");
                    new_div.innerHTML = "沒有更多了";
                    comment_div.insertBefore(new_div, comment_div.childNodes[0]);*/
                    comment_div.parentNode.querySelector(".div_single_post_more_comment").setAttribute("style", "display:none;");
                }
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("move=get_post_comment&post_id=" + post_id + "&num=" + num + "&last_comment_floor=" + floor);
    return;
}
function more_comment(target)
{
    var comment_div = target.parentNode.parentNode.querySelector(".div_single_post_comment");
    get_comment(comment_div, 10);
    return;
}
function new_comment_send(target)
{
    event.preventDefault();
    var content = target.querySelector(".textarea_single_post_new_comment").value;
    if(!content)
        return;
    var post_id = target.parentNode.parentNode.querySelector(".div_single_post_comment").getAttribute("post_id");
    var formData = new FormData();
    formData.append('move', 'new_comment');
    formData.append('content', content);
    formData.append('post_id', post_id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                location.reload();
            }
            else
            {
                target.querySelector(".textarea_single_post_new_comment").value = "";
                var display = target.parentNode.parentNode.querySelector(".button_single_post_comment_display");
                if(display)
                {
                    display.click();
                    display.click();
                    display.innerHTML = parseInt(display.innerHTML) + 1;
                }
                else
                    target.parentNode.parentNode.querySelector(".button_single_post_comment").innerHTML = parseInt(target.parentNode.parentNode.querySelector(".button_single_post_comment").innerHTML) + 1;
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
function edit_post(target)
{
    var post_id = target.parentNode.parentNode.parentNode.getAttribute("post_id");
    var formData = new FormData();
    formData.append('move', 'get_current_post');
    formData.append('post_id', post_id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                location.reload();
            }
            else
            {
                input_edit_post.value = post_id;
                edit_post_apear(this.responseText);
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
function edit_post_ajax()
{
    event.preventDefault();
    var formData = new FormData();
    formData.append('move', 'update_post');
    formData.append('post_id', input_edit_post.value);
    formData.append('content', textarea_edit_post.value);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                document.getElementsByTagName("body")[0].onbeforeunload = function() { return; };
                location.reload();
            }
            else if(this.responseText == "")
            {
                edit_post_close();
                alert("修改成功");
                location.reload();
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
function delete_post(target)
{
    if(!confirm("確定要刪除此篇貼文嗎?"))
        return;
    var post_id = target.parentNode.parentNode.parentNode.getAttribute("post_id");
    var formData = new FormData();
    formData.append('move', 'delete_post');
    formData.append('post_id', post_id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            if(this.responseText == "CONNECT_ERROR")
            {
                alert("資料庫連結錯誤");
            }
            else if(this.responseText == "QUERY_ERROR")
            {
                alert("發生錯誤");
            }
            else if(this.responseText == "AUTO_LOGOUT")
            {
                location.reload();
            }
            else
            {
                location.reload();
            }
        }
    };
    xhttp.open("POST", "ajax_response.php", true);
    xhttp.send(formData);
    return;
}
////////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////
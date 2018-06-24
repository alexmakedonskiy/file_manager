<?php
function xprint($param, $title = 'Отладочная информация')
{
    ini_set('xdebug.var_display_max_depth', 50);
    ini_set('xdebug.var_display_max_children', 25600);
    ini_set('xdebug.var_display_max_data', 9999999999);
    if (PHP_SAPI == 'cli') {
        echo "\n---------------[ $title ]---------------\n";
        echo print_r($param, true);
        echo "\n-------------------------------------------\n";
    } else {
        ?>
        <style>
            .xprint-wrapper {
                padding: 10px;
                margin-bottom: 25px;
                color: black;
                background: #f6f6f6;
                position: relative;
                top: 18px;
                border: 1px solid gray;
                font-size: 11px;
                font-family: InputMono, Monospace;
                width: 80%;
            }

            .xprint-title {
                padding-top: 1px;
                color: #000;
                background: #ddd;
                position: relative;
                top: -18px;
                width: 170px;
                height: 15px;
                text-align: center;
                border: 1px solid gray;
                font-family: InputMono, Monospace;
            }
        </style>
        <div class="xprint-wrapper">
        <div class="xprint-title"><?= $title ?></div>
        <pre style="color:#000;"><?= htmlspecialchars(print_r($param, true)) ?></pre>
        </div><?php
    }
}

function xd($val, $title = null)
{
    xprint($val, $title);
    die();
}

/***********************ПРОВЕРКА*ПРОКСИ*******************************/
function CheckProxy($_proxy, $_type = "PROXY", $timer = 60)
{
    $url = "http://httpbin.org/ip";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_PROXY, $_proxy);
    if ($_type === "SOCKS5")
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    else curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

    curl_setopt($ch, CURLOPT_TIMEOUT, $timer);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($curl_scraped_page, true);
    if ($response['origin'] != null) $errno = 0;
    else $errno = 110;
    $tmp = explode(":", $_proxy);
    return [
        "ip" => $tmp[0],
        "port" => $tmp[1],
        "errno" => $errno,
        "type" => $_type
    ];
}

/***********************ПРОВЕРКА*ПРОКСИ*******************************/
function updateProxy($_mysqli, $_check)
{
    $proxy = $_check['ip'].":".$_check['port'];

    if ($_check['errno'] > 0)
    {
        $_mysqli->query("UPDATE `proxy` SET `is_working` = 0 WHERE `address` = '" . $proxy . "'");
        return 0;
    }else {
        $_mysqli->query("UPDATE `proxy` SET `is_working` = 1 WHERE `address` = '" . $proxy . "'");
    }

    return 1;
}

/***********************ПОЛУЧЕНИЕ ПРОКСИ*******************************/
function GetProxy($mysqli)
{
    // ИСПОЛЬЗОВАНЫЕ ПРОКСИ
    $id_proxy = [];
    // СПИСОК ЛИДОВ
    $_str_id_proxy = "SELECT `id_proxy` FROM `lead`";
    $res_proxy = $mysqli->query($_str_id_proxy);
    while ($row = $res_proxy->fetch_assoc())
        $id_proxy[] = $row['id_proxy'];
    // ПРОКСИ НЕ ГДЕ НЕ ИСПОЛЬЗОВАНОЕ
    $res = $mysqli->query("SELECT * FROM `proxy` WHERE `is_working` = 1 AND `id` NOT IN (" . implode(",", $id_proxy) . ") AND `is_use` = 0 ORDER BY RAND() LIMIT 1")->fetch_assoc();
    // ЕСЛИ ПРОКСЕЙ ВООБЩЕ НЕТ
    if ($res == null) return null;
    // ЗДЕСЬ ПРОВЕРЯЕМ ПРОКСЯ
    $res_check = CheckProxy($res['address'], $res['type']);
    if(updateProxy($mysqli, $res_check) == 0) return null;

    return array_merge($res, $res_check);

}

/***********************ПОЛУЧЕНИЕ ПРОКСИ*******************************/


?>

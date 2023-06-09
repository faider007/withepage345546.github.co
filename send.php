<?php
//send v5

session_start();

include("conf.php");

function check($str = '')
{
    if (is_int($str)) {
        $str = intval($str);
    } else {
        $str = htmlspecialchars($str);
        $str = stripslashes(trim($str));
    }

    return $str;
}


$name = (!empty($_POST['name'])) ? check($_POST['name']) : '';
$phone = (!empty($_POST['phone'])) ? check($_POST['phone']) : '';
$aff_click_id = (!empty($_POST['aff_click_id'])) ? check($_POST['aff_click_id']) : '';
$sub1 = (!empty($_POST['sub1'])) ? check($_POST['sub1']) : '';
$pixel = (!empty($_POST['pixel'])) ? check($_POST['pixel']) : '';
$custom1 = (!empty($_POST['custom1'])) ? check($_POST['custom1']) : '';
$sub_id2 = (!empty($_POST['sub_id2'])) ? check($_POST['sub_id2']) : '';
$sub_id3 = (!empty($_POST['sub_id3'])) ? check($_POST['sub_id3']) : '';
$sub_id4 = (!empty($_POST['sub_id4'])) ? check($_POST['sub_id4']) : '';
$sub_id5 = (!empty($_POST['sub_id5'])) ? check($_POST['sub_id5']) : '';
$aff_param1 = (!empty($_POST['aff_param1'])) ? check($_POST['aff_param1']) : '';
$aff_param2 = (!empty($_POST['aff_param2'])) ? check($_POST['aff_param2']) : '';
$aff_param3 = (!empty($_POST['aff_param3'])) ? check($_POST['aff_param3']) : '';
$aff_param4 = (!empty($_POST['aff_param4'])) ? check($_POST['aff_param4']) : '';
$aff_param5 = (!empty($_POST['aff_param5'])) ? check($_POST['aff_param5']) : '';
$site = $_SERVER['HTTP_HOST'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
try {
    if (!empty($phone)) {

        if (empty($aff_click_id)) {
            $aff_click_id = bin2hex(random_bytes(12));
        }

        if ((!empty($aff_param1)) && ($aff_param1 == 'site')) {
            $aff_param1 = $site;
        }

        if ((!empty($aff_param2)) && ($aff_param2 == 'ip')) {
            $aff_param2 = $ip;
        }

        if ((!empty($aff_param3)) && ($aff_param3 == 'pixel')) {
            $aff_param3 = $pixel;
        }

        $params = array(
            'goal_id' => $pp_affscale_offerid,
            'firstname' => $name,
            'phone' => $phone,
            'aff_click_id' => $aff_click_id,
            'custom1' => $custom1,
            'sub_id2' => $sub_id2,
            'sub_id3' => $sub_id3,
            'sub_id4' => $sub_id4,
            'sub_id5' => $sub_id5,
            'aff_param1' => $aff_param1,
            'aff_param2' => $aff_param2,
            'aff_param3' => $aff_param3,
            'aff_param4' => $aff_param4,
            'aff_param5' => $aff_param5
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://tracking.affscalecpa.com/api/v2/affiliate/leads?api-key=' . $pp_affscale_apikey);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);

        if (empty($res)) {
            throw new Exception('Error: Empty response for order. ' . var_export($params, true));
        }

        $response = json_decode($res);
        if (empty($response)) {
            throw new Exception('Error: Broken json format for order. ' . PHP_EOL . var_export($params, true));
        }
        if ($response->code !== 200) {
            throw new Exception('Invalid: Order processing error. ' . PHP_EOL . var_export($params, true));
        }

        file_put_contents(
            __DIR__ . '/order.success.log',
            date('Y.m.d H:i:s') . ' ' . $res
        );

        curl_close($ch);
        header('Location: ' . 'thanks.php?pixel=' . $pixel . '&name=' . $name . '&phone=' . $phone);

    } else {
        header('Location: ' . 'index.php?pixel=' . $pixel . '&name=' . $name . '&phone=' . $phone);
        exit;
    }
} catch (\Exception $e) {

    file_put_contents(
        __DIR__ . '/order.error.log',
        date('Y.m.d H:i:s') . ' ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . $res
    );
}

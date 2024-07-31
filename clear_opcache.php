<?php
if (PHP_SAPI !== 'cli') {
    var_dump(opcache_reset());
    return;
}
//build cgi msg
$str    = getStreamBin();
$len    = strlen($str);
$padLen = (8 - ($len % 8)) % 8;
$header = '0101000100080000000100000000' . '000001040001' . bin2hex(pack('n', strlen($str))) . '0' . $padLen . '00';
$str    = hex2bin($header) . $str . str_repeat("\0", $padLen);
$str    .= hex2bin('01040001000000000105000100000000');
//send cgi msg
$fp = stream_socket_client('tcp://127.0.0.1:9000', $errno, $errStr, 5);
if ( !$fp) {
    echo 'connect error' . PHP_EOL;
    exit(-1);
}
fwrite($fp, $str);
$ret = fread($fp, 8096);
if ( !$ret) {
    echo 'stream read error' . PHP_EOL;
    exit(-2);
}
//var_dump($errno, $errStr, $ret);
var_dump(bin2hex($ret));
fclose($fp);
function getStreamBin()
{
    $arr = [
        'SCRIPT_FILENAME' => __FILE__,
        'REQUEST_METHOD'  => 'GET',
        /*        'QUERY_STRING' => '',
                'CONTENT_TYPE' => '',
                'CONTENT_LENGTH' => '',
                'SCRIPT_NAME' => '/index.php',
                'REQUEST_URI' => '/site/restart',
                'DOCUMENT_URI' => '/site/restart',
                'DOCUMENT_ROOT' => '/home/dev/rntd/rntd-yii/crm/web',
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'REQUEST_SCHEME' => 'https',
                'HTTPS' => 'on',
                'GATEWAY_INTERFACE' => 'CGI/1.1',
                'SERVER_SOFTWARE' => 'nginx/1.16.1',
                'REMOTE_ADDR' => '39.107.142.185',
                'REMOTE_PORT' => '49604',
                'SERVER_ADDR' => '172.17.203.19',
                'SERVER_PORT' => '443',
                'SERVER_NAME' => 'dev-crm.rntd.org',
                'REDIRECT_STATUS' => '200',
                'HTTP_HOST' => 'dev-crm.rntd.org',*/
        //'HTTP_ACCEPT' => '*/*',
    ];
    $ret = [];
    foreach ($arr as $key => $value) {
        if (strlen($value) > 127) {
            $s     = sprintf("%'.02x8000%'.04x", strlen($key), strlen($value));
            $ret[] = hex2bin($s) . $key . $value;
        } else {
            $ret[] = pack('n', (strlen($key) << 8) + strlen($value)) . $key . $value;
        }
    }
    return implode('', $ret);
}

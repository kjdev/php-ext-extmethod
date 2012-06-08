--TEST--
Check module info
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

ob_start();
phpinfo(INFO_MODULES);
$str = ob_get_clean();

$array = explode("\n", $str);

$section = false;
$blank = 0;
foreach ($array as $key => $val)
{
    if (strcmp($val, 'extmethod') == 0 || $section)
    {
        $section = true;
    }
    else
    {
        continue;
    }

    if (empty($val))
    {
        $blank++;
        if ($blank == 2)
        {
            $section = false;
        }
    }

    echo $val, PHP_EOL;
}
?>
--EXPECTF--
extmethod

extmethod support => enabled
extension version => 0.0.1

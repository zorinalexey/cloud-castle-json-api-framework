<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error !!!</title>
</head>
<body>
<main>
    <?php
    /**
     * @var Throwable $error
     * @var Config $config
     */
    
    use CloudCastle\Core\Config;
    
    global $arrToStr;
    
    $arrToStr = function (mixed $arr): string{
        global $arrToStr;
        if (!is_array($arr)) {
            return $arr;
        }
        
        $str = '';
        
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $str .= $key . ' => ' . $arrToStr($value);
            } else {
                $str .= $key . ' => ' . $value . '<br />';
            }
        }
        
        return $str;
    }
    ?>
    <h1>Error !!! Code <?php echo $error->getCode(); ?></h1>
    <p>
        <?php echo $error->getMessage(); ?>
    </p>
    <?php if ($config->get('app.debug', false)) { ?>
        <?php foreach ($error->getTrace() as $trace) { ?>
            <p>
                <?php echo $arrToStr($trace); ?>
            </p>
        <?php } ?>
    <?php } ?>
</main>
</body>
</html>
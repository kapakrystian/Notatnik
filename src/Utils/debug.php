<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

//debug function
function dump($data)
{
    echo '<br/><div style="display: inline-block;
     background: lightgray;
     padding: 0 5px;
     border: 1px solid;
     margin: 2px">
    <pre>';
    print_r($data);
    echo '</pre>
    </div>
    <br/>';
}

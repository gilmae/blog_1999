<?php

include('IXR_Library.inc.php');

function sayHello($args) {
    return 'Hello!';
}
function addTwoNumbers($args) {
    $number1 = $args[0];
    $number2 = $args[1];
    return $number1 + $number2;
}
$server = new IXR_Server(array(
    'demo.sayHello' => 'sayHello',
    'demo.addTwoNumbers' => 'addTwoNumbers'
));

>>

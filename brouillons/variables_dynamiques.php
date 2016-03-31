<?php

$info = 'hello';
echo $info . '<br>'; // hello
${$info} = 'autre valeur'; // équivaut à $hello = 'autre valeur';
echo $hello . '<br>'; // 'autre valeur'

${$info . 'G'} = 'lac'; // équivaut à $helloG = 'lac';
echo $helloG . '<br>';


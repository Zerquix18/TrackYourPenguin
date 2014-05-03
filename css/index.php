<?php

$x = array("&amp;" => "&");
echo str_replace( array_keys($x), $x, '<input type="hidden" name="attachment[params][images][0]" value="https://fbexternal-a.akamaihd.net/safe_image.php?d=AQA6WM_2AikA5QnY&amp;w=100&amp;h=100&amp;url=https%3A%2F%2Ffbcdn-sphotos-f-a.akamaihd.net%2Fhphotos-ak-prn1%2F1184991_428965067214830_184943618_n.jpg&amp;cfs=1&amp;upscale">');
<?php
function push_array_in_array(&$arr1, $arr2){
    for ($i=0; $i < sizeof($arr2); $i++) {
        $arr1[] = $arr2[$i];
    }
}

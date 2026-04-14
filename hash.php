<?php

/**
 * 
 * @param string 
 * @param int 
 * @param int 
 */
function password($password, $index, $shift) {
    if (!isset($password[$index])) return $password;

    $char = $password[$index];
    
    
    if ($char >= 'a' && $char <= 'z') {
        $ascii_start = ord('a'); 
     
        $old_pos = ord($char) - $ascii_start;
        $new_pos = ($old_pos + $shift) % 26;
        
      
        if ($new_pos < 0) $new_pos += 26;
        
        $password[$index] = chr($new_pos + $ascii_start);
    }
     if ($char >= 'A' && $char <= 'Z') {
        $ascii_start = ord('A'); 
     
        $old_pos = ord($char) - $ascii_start;
        $new_pos = ($old_pos + $shift) % 26;
        
      
        if ($new_pos < 0) $new_pos += 26;
        
        $password[$index] = chr($new_pos + $ascii_start);
    }
     if ($char >= '0' && $char <= '9') {
        $ascii_start = ord('0'); 
     
        $old_pos = ord($char) - $ascii_start;
        $new_pos = ($old_pos + $shift) % 10;
        
      
        if ($new_pos < 0) $new_pos += 10;
        
        $password[$index] = chr($new_pos + $ascii_start);
    }
    
    return $password;
}


$password = "aAz19";

$password_modified = password($password, 2, 1);
$password_modified = password($password_modified, 0, -1);
$password_modified = password($password_modified, 1, 2);
$password_modified = password($password_modified, 3, -1);
$password_modified = password($password_modified, 4, 3);
$password_modified = password($password_modified, 5, 3);
$password_modified = password($password_modified, 6, 3);
$password_modified = password($password_modified, 7, 3);
$password_modified = password($password_modified, 8, 3);
$password_modified = password($password_modified, 9, 3);
$password_modified = password($password_modified, 10, 3);
$password_modified = password($password_modified, 11, 3);
$password_modified = password($password_modified, 12, 3);
$password_modified = password($password_modified, 13, 3);
$password_modified = password($password_modified, 14, 3);



echo "Password Asli    : " . $password . "\n";         
echo "Setelah Dimodif  : " . $password_modified . "\n";  
?>

<?php
require_once 'config/db.php';

if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Connection failed!";
}
?>
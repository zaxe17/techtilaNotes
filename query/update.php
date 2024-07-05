<?php
// DATABASE CONNECTION
include("../config.php");
session_start();

if(isset($_POST['submit'])) {
    $note_id = $_POST['note_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    // UPDATE BASE ON THE PASS NOTE_ID
    $update = "UPDATE notess SET title='$title', content='$content' WHERE note_id='$note_id'";
    $query_run = mysqli_query($conn, $update);

    // CHECK IF THE UPDATE WAS SUCCES
    if($query_run) {
        header("Location: ../note.php");
        exit();
    } else {
        echo '<script> alert("Data not update"); </script>';
    }
} 
 
?>

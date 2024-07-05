<?php
// DATABASE CONNECTION
include("../config.php");
session_start();

if(isset($_POST['delete'])) {
    $note_id = $_POST['note_id'];

    // DELETE NOTE BASE ON THE NOTE_ID
    $query = "DELETE FROM notess WHERE note_id = '$note_id'";
    $query_run = mysqli_query($conn, $query);

    if($query_run) {
        header("location: ../note.php");
    }
}
?>
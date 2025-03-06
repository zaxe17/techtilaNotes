<?php
include '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_pin'])) {
    $note_id = $_POST['note_id'];

    // RETIRIVE CURRENT PINNED STATUS
    $query = "SELECT pinned FROM notess WHERE note_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $stmt->bind_result($currentPinned);
    $stmt->fetch();
    $stmt->close();

    // TOGGLE PINNED STATUS
    $newPinned = ($currentPinned == 1) ? 0 : 1;

    // UPDATE PINNED STATUS IN THE DATABASE
    $update_query = "UPDATE notess SET pinned = ? WHERE note_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $newPinned, $note_id);
    $update_stmt->execute();
    $update_stmt->close();

    // REDIRECT BACK TO THE PREVIOUS PAGE OR HANDLE SUCCES MESSAGE
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}
?>
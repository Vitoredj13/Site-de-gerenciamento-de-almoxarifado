<?php
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCategoria = $_POST['nome'];

    if (!empty($nomeCategoria)) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM categoria WHERE nomedacategoria = ?");
        $stmt->bind_param("s", $nomeCategoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data['total'] > 0) {
            echo json_encode(['existe' => true]);
        } else {
            echo json_encode(['existe' => false]);
        }

        $stmt->close();
    } else {
        echo json_encode(['existe' => false]);
    }
}
?>


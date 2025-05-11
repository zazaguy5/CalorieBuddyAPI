<?php
function handleWorkFlow($method, $conn)
{
    switch ($method) {
        case 'GET':
            $nextId = $conn->query("SHOW TABLE STATUS FROM `db_e_saraban_sawang` WHERE `name` = 'tbl_workflow'")->fetch_assoc()['Auto_increment'];
            sendResponse(200, ["Auto_increment" => $nextId]);
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data["username"] ?? '';

            $sql = "SELECT * FROM tbl_workflow where workflow_by = ? ORDER BY workflow_datesave DESC;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $data = [];
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                sendResponse(200, $data);
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
            break;
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}

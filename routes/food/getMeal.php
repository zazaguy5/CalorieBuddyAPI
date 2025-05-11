<?php
function getMealMethod($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            // ตรวจสอบข้อมูลที่จำเป็น
            if (!isset($data['id'])) {
                sendResponse(400, ["error" => "กรุณากรอก id"]);
            }

            $sql = "SELECT meal.meal_type,meal.quantity,f.name,f.calories,f.protien,f.carb,f.fat,f.food_category,f.image,meal.create_at FROM users_eated as meal JOIN foods as f WHERE meal.user_id = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $data['id']);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $data = $result->fetch_assoc();

                    sendResponse(200, [
                        "status" => "success",
                        "message" => $data
                    ]);
                } else {
                    sendResponse(404, ["error" => "ไม่พบข้อมูล"]);
                }
            } else {
                sendResponse(500, ["error" => "เกิดข้อผิดพลาด: " . $stmt->error]);
            }
            break;

        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
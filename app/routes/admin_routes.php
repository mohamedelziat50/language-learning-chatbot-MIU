<?php
require_once __DIR__ . '/../controllers/admin/manage_users.php';

function registerUserRoutes($request, $method) {

    if ($request === '/getUsers' && $method === 'GET') {
        try {
            $users = UserController::getUsers();
            echo json_encode([
                "status" => "success",
                "data" => $users
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to retrieve users: " . $e->getMessage()
            ]);
        }
        return true;
    }

    if ($request === '/addUser' && $method === 'POST') {
        try {
            header('Content-Type: application/json');
            $result = UserController::addUser();
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create user: " . $e->getMessage()
            ]);
        }
        return true;
    }

    if ($request === '/getTotalUsersCount' && $method === 'GET') {
        try {
            $count = UserController::getTotalUsersCount();
            echo json_encode([
                "status" => "success",
                "total_users" => $count
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to retrieve user count: " . $e->getMessage()
            ]);
        }
        return true;
    }

     if ($request === '/updateUser' && $method === 'POST') {
        try {
            header('Content-Type: application/json');
            $result = UserController::updateUser();
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update user: " . $e->getMessage()
            ]);
        }
        return true;
    }

    if ($request === '/deleteUser' && $method === 'POST') {
        try {
            header('Content-Type: application/json');
            $result = UserController::deleteUser();
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete user: " . $e->getMessage()
            ]);
        }
        return true;
    }
    
  return false;

}

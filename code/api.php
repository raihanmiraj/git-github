<?php

header("Access-Control-Allow-Origin: *"); // You can replace * with your frontend domain for security
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}


// File path
$htmlFile = __DIR__ . '/index.html';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Read the HTML file
            if (!file_exists($htmlFile)) {
                http_response_code(404);
                echo json_encode(['error' => 'File not found']);
                exit;
            }
            
            $content = file_get_contents($htmlFile);
            if ($content === false) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to read file']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'content' => $content,
                'timestamp' => date('Y-m-d H:i:s'),
                'file_size' => filesize($htmlFile)
            ]);
            break;
            
        case 'POST':
        case 'PUT':
            // Update the HTML file
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || !isset($data['content'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Content is required']);
                exit;
            }
            
            $newContent = $data['content'];
            
            // Basic validation - ensure it's HTML
            if (strpos($newContent, '<!DOCTYPE html>') === false && strpos($newContent, '<html') === false) {
                http_response_code(400);
                echo json_encode(['error' => 'Content must be valid HTML']);
                exit;
            }
            
            // Write to file
            $result = file_put_contents($htmlFile, $newContent);
            if ($result === false) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to write file']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'File updated successfully',
                'timestamp' => date('Y-m-d H:i:s'),
                'file_size' => filesize($htmlFile)
            ]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?> 
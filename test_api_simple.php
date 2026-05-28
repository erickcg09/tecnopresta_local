<?php
// test_api_simple.php - Prueba básica de conexión con DeepSeek
session_start();
require_once("conexion.php"); // Para tener la estructura de conexión

class DeepSeekTester {
    private $api_key = 'sk-cc1451be304749218b319c5f6646293e'; // REEMPLAZA CON TU KEY REAL
    private $api_url = 'https://api.deepseek.com/v1/chat/completions';
    
    public function testConexion() {
        $data = [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Responde solo con la palabra "CONEXION_EXITOSA" si este mensaje llega correctamente.'
                ]
            ],
            'max_tokens' => 10
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Temporal para pruebas
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'data_sent' => $data
        ];
    }
}

// Ejecutar prueba
$tester = new DeepSeekTester();
$resultado = $tester->testConexion();

echo "<h1>Prueba de Conexión DeepSeek API</h1>";
echo "<pre>";
echo "HTTP Code: " . $resultado['http_code'] . "\n";
echo "Error: " . ($resultado['error'] ?: 'Ninguno') . "\n";
echo "Response: " . $resultado['response'] . "\n";
echo "Data Sent: " . json_encode($resultado['data_sent'], JSON_PRETTY_PRINT) . "\n";
echo "</pre>";

// Intentar decodificar la respuesta
if ($resultado['http_code'] === 200) {
    $responseData = json_decode($resultado['response'], true);
    if ($responseData) {
        echo "<h2>Respuesta Decodificada:</h2>";
        echo "<pre>" . print_r($responseData, true) . "</pre>";
        
        $mensaje = $responseData['choices'][0]['message']['content'] ?? 'No content';
        echo "<h3>Mensaje de DeepSeek:</h3>";
        echo "<p><strong>" . htmlspecialchars($mensaje) . "</strong></p>";
    }
} else {
    echo "<h2 style='color: red;'>ERROR EN LA CONEXIÓN</h2>";
    echo "<p>Revisa:</p>";
    echo "<ul>";
    echo "<li>Tu API Key</li>";
    echo "<li>Conexión a internet</li>";
    echo "<li>URL de la API</li>";
    echo "</ul>";
}
?>
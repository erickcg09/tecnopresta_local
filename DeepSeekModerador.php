<?php
class DeepSeekModerador {
    private $api_key = 'sk-cc1451be304749218b319c5f6646293e';
    private $api_url = 'https://api.deepseek.com/v1/chat/completions';
    
    public function analizarModelo($nuevoModelo, $modelosSimilares) {
        // Preparar la lista de modelos similares con TODOS los detalles
        $listaModelos = "";
        foreach($modelosSimilares as $modelo) {
            $listaModelos .= "- ID: {$modelo['id_activo']} | Modelo: {$modelo['modelo']} | Marca: {$modelo['marca']} | Clase: {$modelo['clase']} | Color: {$modelo['color']}\n";
        }
        
        $prompt = "Eres un asistente inteligente para un sistema de inventario de equipos tecnológicos. 

UN USUARIO QUIERE CREAR ESTE NUEVO MODELO: \"{$nuevoModelo}\"

MODELOS SIMILARES EXISTENTES en el sistema:
{$listaModelos}

ANÁLISIS REQUERIDO:
- Compara el nuevo modelo con los existentes
- Detecta si son esencialmente el mismo equipo con variaciones menores
- Evalúa si las diferencias justifican un nuevo registro

RESPONDE en formato JSON:

{
    \"accion_recomendada\": \"usar_existente|crear_nuevo\",
    \"mensaje_persuasivo\": \"Mensaje convincente y educativo\",
    \"modelo_sugerido\": \"ID del modelo más similar o null\",
    \"razones\": [\"razón1\", \"razón2\"],
    \"confianza\": 0-100
}

ENFOQUE PERSUASIVO:
- Sé amable pero firme en mantener la integridad del inventario
- Destaca los beneficios de usar modelos existentes
- Explica por qué las variaciones menores no justifican duplicados
- Usa ejemplos concretos de los modelos encontrados";

        $data = [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 800
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
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $responseData = json_decode($response, true);
            $content = $responseData['choices'][0]['message']['content'];
            
            // Log para debugging
            error_log("DeepSeek Response: " . $content);
            
            return json_decode($content, true);
        } else {
            error_log("DeepSeek API Error: HTTP " . $httpCode . " - " . $curlError);
            error_log("Response: " . $response);
            return null;
        }
    }
}
?>
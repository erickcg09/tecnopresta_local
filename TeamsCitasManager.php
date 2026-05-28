<?php
class TeamsCitasManager {
    private $accessToken;
    
    public function __construct($accessToken) {
        $this->accessToken = $accessToken;
    }
    
    /**
     * Crear evento en calendario de Teams/Outlook
     */
    public function crearEvento($eventData) {
        $url = 'https://graph.microsoft.com/v1.0/me/events';
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'Prefer: outlook.timezone="America/Costa_Rica"'
        ];
        
        $payload = [
            'subject' => $eventData['asunto'],
            'body' => [
                'contentType' => 'HTML',
                'content' => $eventData['descripcion']
            ],
            'start' => [
                'dateTime' => $eventData['inicio'],
                'timeZone' => 'America/Costa_Rica'
            ],
            'end' => [
                'dateTime' => $eventData['fin'],
                'timeZone' => 'America/Costa_Rica'
            ],
            'location' => [
                'displayName' => 'Reunión Teams - TecnoPresta'
            ],
            'attendees' => [
                [
                    'emailAddress' => [
                        'address' => $eventData['correo_destinatario'],
                        'name' => $eventData['nombre_destinatario']
                    ],
                    'type' => 'required'
                ]
            ],
            'allowNewTimeProposals' => false,
            'isOnlineMeeting' => true,
            'onlineMeetingProvider' => 'teamsForBusiness'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            throw new Exception('Error al crear evento en Teams: ' . $response);
        }
    }
    
    /**
     * Verificar si el token es válido
     */
    public function verificarToken() {
        $url = 'https://graph.microsoft.com/v1.0/me';
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
}
?>
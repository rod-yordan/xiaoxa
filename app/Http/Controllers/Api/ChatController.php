<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\MessageSentEvent;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    // Configuración de Ollama
    protected $ollamaUrl = 'http://localhost:11434'; // Cambia si tu servidor está en otra IP
    protected $model = 'phi3:mini'; // Modelo que estás usando

    public function sendMessage(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $userId = $request->user_id;
        $userMessage = $request->message;

        $user = Usuario::find($userId);
        $userName = $user ? $user->nombres : 'usuario';

        try {
            // Preparar los mensajes para Ollama
            $messages = [
                [
                    "role" => "system",
                    "content" => "Eres Luck, asistente virtual de la tienda C'Lucky. Atiendes a clientes de forma breve, clara y amigable. Si el cliente saluda (como 'hola', 'buenas'), responde con un saludo cordial y pregunta en qué puedes ayudar. Ayudas a encontrar ropa como poleras, abrigos, polos y pantalones, y puedes mencionar promociones como pantalones 2x1. Solo si realmente no sabes la respuesta o es un caso especial, sugiere amablemente contactar al número 992387342. Evita responder siempre con el número. No des respuestas largas ni técnicas."
                ],
                [
                    "role" => "user",
                    "content" => $userMessage
                ]
            ];

            // Llamar a Ollama
            $response = Http::timeout(30) // Timeout de 30 segundos
                ->post("{$this->ollamaUrl}/api/chat", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.7,
                        'max_tokens' => 500
                    ]
                ]);

            // Verificar si la petición fue exitosa
            if ($response->successful()) {
                $ollamaResponse = $response->json();
                $aiMessage = $ollamaResponse['message']['content'] ?? 'Lo siento, no pude procesar tu solicitud.';
            } else {
                // Fallback si Ollama no responde
                $aiMessage = "Lo siento, el servicio de asistente no está disponible en este momento. Por favor, contacta al 992387342 para atención personalizada.";
                \Log::error('Error al llamar a Ollama: ' . $response->body());
            }

        } catch (\Exception $e) {
            // Manejar errores de conexión
            $aiMessage = "Lo siento, no puedo conectarme al asistente. Por favor, contacta al 992387342 para atención personalizada.";
            \Log::error('Excepción al llamar a Ollama: ' . $e->getMessage());
        }

        // Transmitir la respuesta
        broadcast(new MessageSentEvent(
            $userId,
            $aiMessage
        ));

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada',
            'ai_response' => $aiMessage // Opcional: incluir la respuesta en el JSON
        ]);
    }

    // Método adicional para verificar el estado de Ollama
    public function checkOllamaStatus()
    {
        try {
            $response = Http::timeout(5)->get("{$this->ollamaUrl}/api/tags");

            if ($response->successful()) {
                return response()->json([
                    'status' => 'online',
                    'models' => $response->json()['models'] ?? []
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}

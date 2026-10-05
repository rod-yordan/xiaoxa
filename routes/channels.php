<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Usuario; // ✅ ESTÁ BIEN, USAS USER.PHP

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    \Log::info('=== AUTORIZACIÓN DE CANAL ===', [
        'user_id' => $user->id_usuario ?? 'no-id',       
        'user_nombre' => $user->nombres ?? 'no-nombre',
        'user_correo' => $user->correo ?? 'no-email',
        'user_id_solicitado' => $userId,
        'coinciden' => (int) $user->id_usuario === (int) $userId, 
        'canal' => 'chat.' . $userId
    ]);

    // El usuario solo puede escuchar su propio canal
    return (int) $user->id_usuario === (int) $userId; 
});
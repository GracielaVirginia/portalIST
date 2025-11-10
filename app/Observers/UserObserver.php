<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AlertaService;

class UserObserver
{
    public function updated(User $user): void
    {
        if ($user->wasChanged('is_blocked') && (int)$user->is_blocked === 1) {
            app(AlertaService::class)->registrar('usuario_bloqueado', [
                'user_id'   => $user->id,
                'documento' => $user->rut ?? $user->email ?? null,
                'extra'     => ['mensaje' => 'El usuario fue bloqueado (is_blocked=1).'],
            ]);
        }
    }
}

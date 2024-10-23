<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MercadoLivreController extends Controller
{
    public function handleRedirect(Request $request)
    {
        if ($request->has('code')) {
            $authorizationCode = $request->input('code');

            $response = Http::post('https://api.mercadolibre.com/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => env('MERCADO_LIVRE_CLIENT_ID'),
                'client_secret' => env('MERCADO_LIVRE_CLIENT_SECRET'),
                'code' => $authorizationCode,
                'redirect_uri' => route('mercadolivre.redirect'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $accessToken = $data['access_token'];

                $this->saveAccessTokenToEnv($accessToken);

                return redirect()->route('products.index')->with('success', 'Token de acesso salvo com sucesso.');
            } else {
                return redirect()->route('products.index')->with('error', 'Erro ao tentar capturar o token.');
            }
        }

        return redirect()->route('products.index')->with('error', 'Código de autorização não encontrado.');
    }

    protected function saveAccessTokenToEnv($accessToken)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (strpos($envContent, 'MERCADO_LIVRE_ACCESS_TOKEN') !== false) {
            $envContent = preg_replace('/MERCADO_LIVRE_ACCESS_TOKEN=.*/', 'MERCADO_LIVRE_ACCESS_TOKEN=' . $accessToken, $envContent);
        } else {
            $envContent .= "\nMERCADO_LIVRE_ACCESS_TOKEN=" . $accessToken;
        }

        file_put_contents($envPath, $envContent);

        Artisan::call('config:clear');
        Artisan::call('config:cache');
    }

    public function handleNotification(Request $request)
    {
        $notificationData = $request->all();

        Log::info('Received Mercado Livre Notification: ', $notificationData);


        return response()->json(['message' => 'Notification received'], 200);
    }
}

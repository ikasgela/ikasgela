<?php

namespace App\Gitea;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class GiteaClient
{
    private static Client $cliente;
    private static array $headers;

    private static function init()
    {
        self::$cliente = new Client(['base_uri' => env('GITEA_URL') . '/api/v1/']);

        self::$headers = [
            'Authorization' => 'token ' . env('GITEA_TOKEN'),
            'Accept' => 'application/json',
        ];
    }

    public static function repo($repositorio)
    {
        self::init();

        $request = self::$cliente->get('repos/' . $repositorio, [
            'headers' => self::$headers
        ]);

        $response = json_decode($request->getBody(), true);

        $data = [
            'id' => $response['id'],
            'name' => $response['name'],
            'description' => $response['description'],
            'http_url_to_repo' => $response['html_url'],
            'path_with_namespace' => $repositorio
        ];

        return $data;
    }

    public static function clone($repositorio, $username, $destino)
    {
        self::init();

        // Obtener el ID de usuario de destino
        $request = self::$cliente->get('users/' . $username, [
            'headers' => self::$headers
        ]);

        $response = json_decode($request->getBody(), true);
        $uid = $response['id'];

        // Hacer la copia del repositorio
        $request = self::$cliente->post('repos/migrate', [
            'headers' => self::$headers,
            'form_params' => [
                "auth_username" => env('GITEA_USER'),
                "auth_password" => env('GITEA_PASSWORD'),
                "clone_addr" => env('GITEA_URL') . '/' . $repositorio . '.git',
                "uid" => $uid,
                "repo_name" => $destino,
                "private" => true,
            ]
        ]);

        return $request->getStatusCode();
    }
}

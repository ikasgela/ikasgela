<?php

namespace App\Gitea;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Log;

class GiteaClient
{
    private static $cliente = null;
    private static $headers = null;

    private static function init()
    {
        if (is_null(self::$cliente))
            self::$cliente = new Client(['base_uri' => config('gitea.url') . '/api/v1/']);

        if (is_null(self::$headers))
            self::$headers = [
                'Authorization' => 'token ' . config('gitea.token'),
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
            'http_url_to_repo' => $response['clone_url'],
            'path_with_namespace' => $response['full_name'],
            'web_url' => $response['html_url'],
            'owner' => $response['owner']['login'],
        ];

        return $data;
    }

    public static function file($owner, $repo, $filepath, $branch)
    {
        self::init();

        $query = "repos/$owner/$repo/contents/$filepath?ref=$branch";

        $request = self::$cliente->get($query, [
            'headers' => self::$headers
        ]);

        $response = json_decode($request->getBody(), true);

        return base64_decode($response['content']);
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

        try {// Hacer la copia del repositorio
            $request = self::$cliente->post('repos/migrate', [
                'headers' => self::$headers,
                'json' => [
                    "auth_username" => config('gitea.user'),
                    "auth_password" => config('gitea.password'),
                    "clone_addr" => config('gitea.url') . '/' . $repositorio . '.git',
                    "uid" => $uid,
                    "repo_name" => $destino,
                    "private" => true,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        if ($request->getStatusCode() == 201) {
            $response = json_decode($request->getBody(), true);

            $data = [
                'id' => $response['id'],
                'name' => $response['name'],
                'description' => $response['description'],
                'http_url_to_repo' => $response['clone_url'],
                'path_with_namespace' => $response['full_name'],
                'web_url' => $response['html_url'],
                'owner' => $response['owner']['login'],
            ];

            return $data;
        } else {
            return 409;
        }
    }

    public static function dump_gitlab($repositorio, $username, $destino)
    {
        self::init();

        // Obtener el ID de usuario de destino
        $request = self::$cliente->get('users/' . $username, [
            'headers' => self::$headers
        ]);

        $response = json_decode($request->getBody(), true);
        $uid = $response['id'];

        try {// Hacer la copia del repositorio
            $request = self::$cliente->post('repos/migrate', [
                'headers' => self::$headers,
                'json' => [
                    "auth_username" => config('gitea.user'),
                    "auth_password" => config('gitea.gitlab_password'),
                    "clone_addr" => 'http://gitlab' . '/' . $repositorio['path_with_namespace'] . '.git',
                    "uid" => $uid,
                    "repo_name" => $destino,
                    "description" => $repositorio['name'],
                    "private" => true,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        if ($request->getStatusCode() == 201) {
            $response = json_decode($request->getBody(), true);

            $data = [
                'id' => $response['id'],
                'name' => $response['name'],
                'description' => $response['description'],
                'http_url_to_repo' => $response['clone_url'],
                'path_with_namespace' => $response['full_name'],
                'web_url' => $response['html_url'],
                'owner' => $response['owner']['login'],
            ];

            return $data;
        } else {
            return $request->getStatusCode() ?: 400;
        }
    }

    public static function repos()
    {
        self::init();

        $request = self::$cliente->get('repos/search', [
            'headers' => self::$headers,
            'query' => [
                'limit' => 1000
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        return $response['data'];
    }

    public static function borrar()
    {
        self::init();

        $repos = self::repos();

        $total = 0;
        while (count($repos) > 0) {
            foreach ($repos as $repo) {
                self::$cliente->delete('repos/' . $repo['owner']['username'] . '/' . $repo['name'], [
                    'headers' => self::$headers
                ]);
                echo '.';
                $total++;
            }

            $repos = self::repos();
        }

        return $total;
    }

    public static function user($email, $username, $name, $password = null)
    {
        self::init();

        try {
            self::$cliente->post('admin/users', [
                'headers' => self::$headers,
                'json' => [
                    "email" => $email,
                    "full_name" => $name,
                    "username" => $username,
                    "password" => $password ?: Str::random(62) . '._',
                    "must_change_password" => false,
                ]
            ]);
            Log::info('Gitea: Nuevo usuario creado.', [
                'username' => $username
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Gitea: Error al crear un nuevo usuario.', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);
        }
        return false;
    }

    public static function password($email, $username, $password)
    {
        self::init();

        try {
            self::$cliente->patch('admin/users/' . $username, [
                'headers' => self::$headers,
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ]
            ]);
            Log::info('Gitea: Contraseña cambiada.', [
                'username' => $username
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Gitea: Error al cambiar la contraseña.', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);
        }
        return false;
    }

    public static function full_name($email, $username, $full_name)
    {
        self::init();

        try {
            self::$cliente->patch('admin/users/' . $username, [
                'headers' => self::$headers,
                'json' => [
                    'email' => $email,
                    'full_name' => $full_name,
                ]
            ]);
            Log::info('Gitea: Nombre actualizado.', [
                'username' => $username
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Gitea: Error al actualizar el nombre.', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);
        }
        return false;
    }

    public static function block($email, $username)
    {
        self::init();

        try {
            self::$cliente->patch('admin/users/' . $username, [
                'headers' => self::$headers,
                'json' => [
                    'email' => $email,
                    'active' => false,
                    'allow_create_organization' => false,
                ]
            ]);
            Log::info('Gitea: Usuario bloqueado.', [
                'username' => $username
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Gitea: Error al bloquear un usuario.', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);
        }
        return false;
    }

    public static function unblock($email, $username)
    {
        self::init();

        try {
            self::$cliente->patch('admin/users/' . $username, [
                'headers' => self::$headers,
                'json' => [
                    'email' => $email,
                    'active' => true,
                    'allow_create_organization' => false,
                ]
            ]);
            Log::info('Gitea: Usuario desbloqueado.', [
                'username' => $username
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Gitea: Error al desbloquear un usuario.', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);
        }
        return false;
    }

    public static function block_repo($username, $repositorio, $block = true)
    {
        self::init();

        try {
            self::$cliente->patch('repos/' . $username . '/' . $repositorio, [
                'headers' => self::$headers,
                'json' => [
                    'archived' => $block,
                ]
            ]);
            Log::info('Gitea: Repositorio ' . ($block ? 'bloqueado' : 'desbloqueado') . '.', [
                'username' => $username,
                'repository' => $repositorio,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Gitea: Error al bloquear/desbloquear un repositorio.', [
                'username' => $username,
                'repository' => $repositorio,
                'archived' => $block,
                'exception' => $e->getMessage()
            ]);
        }
        return false;
    }
}

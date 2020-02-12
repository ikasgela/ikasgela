<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class PruebaController extends Controller
{
    public function index()
    {
        // token a2fa7a83e98d44e9b59c0d87ed2c2ef9f4c14422

        // https://www.itsolutionstuff.com/post/laravel-57-guzzle-http-client-post-request-exampleexample.html
        // https://docs.gitea.io/en-us/api-usage/

        // https://try.gitea.io/api/swagger#/repository/repoTransfer

        $client1 = new Client(['base_uri' => 'http://gitea:3000/api/v1/']);
        $client2 = new Client(['base_uri' => 'http://gitea:3000/api/v1/']);
        $client3 = new Client(['base_uri' => 'http://gitea:3000/api/v1/']);
        $client4 = new Client(['base_uri' => 'http://gitea:3000/api/v1/']);

        $request = $client1->get('admin/users', [
            'headers' => [
                'Authorization' => 'token 71ca83aad0c3298ce0b9784cf7b52de4f6883107',
                'Accept' => 'application/json',
            ]
        ]);

        var_dump($request->getStatusCode());
//        var_dump(json_decode($request->getBody(), true));

        $request = $client1->post('repos/migrate', [
            'headers' => [
                'Authorization' => 'token 71ca83aad0c3298ce0b9784cf7b52de4f6883107',
                'Accept' => 'application/json',
            ],
            'form_params' => [
                "uid" => 2,
                "clone_addr" => "http://gitea:3000/root/prueba.git",
                "repo_name" => Str::slug(Str::uuid()),
                "auth_password" => "12345Abcde.",
                "auth_username" => "root",
                "private" => true,
            ]
        ]);

        var_dump($request->getReasonPhrase());
        var_dump($request->getStatusCode());
//        var_dump(json_decode($request->getBody(), true));

    }
}

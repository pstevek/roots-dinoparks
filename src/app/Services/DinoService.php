<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DinoService
{
    /**
     * Gets data from the DinoPark NULDS Feed
     *
     * @return mixed
     */
    public function proceessFeed()
    {
        $endpoint = config('services.dinopark.nulds.endpoint');

        $response = Http::get($endpoint);

        return json_decode($response, true);
    }
}
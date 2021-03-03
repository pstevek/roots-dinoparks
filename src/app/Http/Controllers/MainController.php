<?php

namespace App\Http\Controllers;

use App\Services\DinoService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MainController extends Controller
{

    protected DinoService $service;

    /**
     * MainController constructor.
     *
     * @param DinoService $dinoService
     */
    public function __construct(DinoService $dinoService)
    {
        $this->service = $dinoService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {

        return view('index');
    }

    public function getFeed()
    {
        $data = $this->service->proceessFeed();

        dd($data);
    }
}

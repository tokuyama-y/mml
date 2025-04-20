<?php

namespace App\Http\Controllers;

use App\Repositories\MindscapeResultRepository;
use Illuminate\Http\Request;


class MindscapeResultController extends Controller
{
    protected $MindscapeResultRepository;

    public function __construct(MindscapeResultRepository $MindscapeResult)
    {
        $this->MindscapeResultRepository = $MindscapeResult;
    }

    public function index()
    {
        $results = $this->MindscapeResultRepository->getAll();

        return response()->json($results);
    }
}

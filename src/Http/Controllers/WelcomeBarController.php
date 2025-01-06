<?php

namespace Daikazu\WelcomeBar\Http\Controllers;

use Daikazu\WelcomeBar\Services\WelcomeBarService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WelcomeBarController extends Controller
{
    protected $welcomeBarService;

    public function __construct(WelcomeBarService $welcomeBarService)
    {
        $this->welcomeBarService = $welcomeBarService;
    }

    /**
     * GET /welcome-bar/data
     * Return the currently stored Welcome Bar data as JSON
     */
    public function show()
    {
        $data = $this->welcomeBarService->getData();

        return response()->json($data);
    }

    /**
     * POST /welcome-bar/update
     * Accept JSON data from an external source and write it to the file
     */
    public function update(Request $request)
    {
        // 1) Validate or parse the incoming data
        //    Example: require a JSON array of items
        $request->validate([
            '*.message'  => 'required|string',
            '*.cta.show' => 'boolean',
            // ... add other validations as needed
        ]);

        $jsonData = $request->all();  // This is the parsed JSON array

        // 2) Possibly do some transformations or checks
        // ...

        // 3) Save it
        $this->welcomeBarService->storeData($jsonData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Welcome Bar data updated',
        ]);
    }
}

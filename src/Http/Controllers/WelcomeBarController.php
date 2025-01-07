<?php

namespace Daikazu\WelcomeBar\Http\Controllers;

use Daikazu\WelcomeBar\Services\WelcomeBarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WelcomeBarController extends Controller
{
    public function __construct(protected WelcomeBarService $welcomeBarService) {}

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
     * Sends a response indicating the success status of the ping operation.
     *
     * @return JsonResponse A JSON response containing the status of the operation.
     */
    public function ping()
    {
        return response()->json([
            'status' => 'success',
        ]);
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
            '*.message' => 'required|string',
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

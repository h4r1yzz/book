<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events; // Replace with your actual model

class ValidateContestController 
{
    public function checkTitleAvailability(Request $request)
    {
        // Get the title from the request
        $title = $request->input('title');

        // Check if the title already exists in the database
        $exists = Events::where('title', $title)->exists();

        // Return a JSON response indicating whether the title exists
        return response()->json(['exists' => $exists]);
    }
}
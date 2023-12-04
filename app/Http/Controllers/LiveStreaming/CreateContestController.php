<?php

namespace App\Http\Controllers\LiveStreaming;

use Illuminate\Http\Request;
use App\Models\Events;

/**
 * Class CreateContestController
 *
 * This controller is responsible for rendering the view where users can input details to create a new contest.
 *
 * @package App\Http\Controllers\LiveStreaming
 */
class CreateContestController
{
    /**
     * Renders the view for creating a new contest.
     *
     * @return \Illuminate\View\View Returns the 'contests.create' view for inputting contest details.
     */
    public function create()
    {
        // Return the view for creating a new contest, allowing users to input contest details
        return view('contests.create'); // Create a view to input contest details
    }
}
?>

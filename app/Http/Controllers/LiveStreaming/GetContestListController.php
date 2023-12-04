<?php
namespace App\Http\Controllers\LiveStreaming;

use Illuminate\Http\Request;
use App\Models\Events;

/**
 * Class GetContestListController
 *
 * This controller is responsible for retrieving and displaying a list of contests.
 *
 * @package App\Http\Controllers\LiveStreaming
 */
class GetContestListController
{    
    /**
     * Displays a paginated list of contests ordered by start date.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * @return \Illuminate\View\View Returns the contests view with paginated contest data.
     */
    public function display(Request $request)
    {
        // Retrieve contests, order them by start date in descending order, and paginate
        $events = Events::orderBy('contest_start_at', 'desc')->paginate(10);

        // Return the contests view with the paginated contest data
        return view('contests.post', compact('events'));
    }

}

?>

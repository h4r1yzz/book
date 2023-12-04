<?php

namespace App\Http\Controllers\LiveStreaming;

use Illuminate\Http\Request;
use App\Models\Events;

/**
 * Class DeleteContestController
 *
 * This controller is responsible for handling the deletion of contests.
 *
 * @package App\Http\Controllers\LiveStreaming
 */

class DeleteContestController
{
    /**
     * Deletes a contest based on the provided ID.
     *
     * @param int $id The ID of the contest to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirects back with success or error message.
     */
    public function delete($id)
    {
        // Find the contest by ID
        $event = Events::find($id);

        // Check if the contest exists
        if (!$event) {
            return back()->with('error', 'Contest not found');
        }

        // Delete the contest
        $event->delete();

        // Redirect back with success message
        return back()->with('success', 'Contest deleted successfully');
    }
}

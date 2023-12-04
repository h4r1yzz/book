<?php

namespace App\Http\Controllers\LiveStreaming;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Http\Requests\StoreContestRequest;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class EditContestController
 *
 * This controller is responsible for handling the editing and updating of contest details.
 *
 * @package App\Http\Controllers\LiveStreaming
 */
class EditContestController
{
    /**
     * Displays the view for editing a contest.
     *
     * @param  int  $id The ID of the contest to be edited.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View Redirects to the contest list if the contest is not found; otherwise, returns the 'contests.edit' view.
     */
    public function edit($id)
    {
        $event = Events::find($id);
        if (!$event) {
            return redirect()->route('contests.post')->with('error', 'Contest not found');
        }
        return view('contests.edit', compact('event'));
    }

    /**
     * Updates the contest details based on the provided request data.
     *
     * @param  \App\Http\Requests\StoreContestRequest  $request The request containing the updated contest data.
     * @param  int  $id The ID of the contest to be updated.
     * @return \Illuminate\Http\RedirectResponse Redirects to the contest list with a success or error message.
     */
    public function update(StoreContestRequest $request, $id)
    {
        // Retrieve the existing contest
        $event = Events::find($id);
        if (!$event) {
            return redirect()->route('contests.post')->with('error', 'Contest not found');
        }
        //It retrieves the selected tier ID from the request and converts it to an integer.
        $selectedTierId = (int)$request->input('tier_system');
        //t then attempts to retrieve the tier configuration from the configuration file using Config::get('contest_tier.tier_system')
        $tierConfig = collect(Config::get('contest_tier.tier_system'))->firstWhere('id', $selectedTierId);

        // Check if the tier configuration exists
        if (!$tierConfig) {
            // Handle the case where the tier configuration doesn't exist (you may want to add appropriate error handling)
            return redirect()->route('contests.edit', $id)
                ->withErrors(['tier_system' => 'Invalid tier system selected.'])
                ->withInput();
        }

        // Update tier_system
        $event->tier_system = $selectedTierId;
        // Validate the input data
        $validatedData = $request->validated();

        // Check if the title is being changed
        if ($event->title !== $validatedData['title']) {
            // Check if a contest with the new title already exists
            $existingContest = Events::where('title', $validatedData['title'])->first();
            if ($existingContest) {
                // If a contest with the same title exists, return a validation error with input data
                return redirect()->route('contests.edit', ['id' => $id])
                //display an error
                    ->withErrors(['title' => 'The title already exists. Please choose a different title.'])
                    //retain the input if there is an error
                    ->withInput();
            }
        } else {
            // If the title is not changed, use the previous title to update title_locale
            $validatedData['title_locale'] = $event->title_locale;
        }

        // Generate title_locale based on the updated title
        $title = $validatedData['title'];
        $title_locale = 'contest.title.' . str_replace(' ', '', strtolower($title));
        $validatedData['title_locale'] = $title_locale;

        // Generate URL based on the title
        $prefix = 'https://sugarbook.me/live-streaming/contests/';
        $title = $request->input('title');
        $url = $prefix . str_replace(' ', '-', strtolower($title));
        $event->url = $url;

        //Copies additional data from the existing event to the $validatedData array.
        $validatedData['contest_id'] = $event->contest_id;
        $validatedData['sorting'] = $event->sorting;
        $validatedData['is_countdown_required'] = $event->is_countdown_required;
        $validatedData['auto_enrollment'] = $event->auto_enrollment;

        // Convert date-time fields to MongoDB UTCDateTime
        $event->contest_start_at = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_start_at));
        $event->contest_end_at = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_end_at));
        $event->contest_display_start_at = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_display_start_at));
        $event->contest_display_end_at = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_display_end_at));

        //Graphic Gifter banner
        //initialize the variable if it exists, if not assigned to empty array
        $existingGifterGraphics = $event->graphics ?? [];

        // Update gifter graphics
        $gifterGraphics = [];
        $gifterGraphicsInput = $request->input('gifterGraphics');
        
        // Check if the tier configuration exists
        if ($tierConfig && isset($tierConfig['tiers'])) {
            // Determine the maximum number of gifter graphics based on tier_system
            //if more than 2 max is set to 3 graphic , else 4 graphics
            $maxGraphics = count($tierConfig['tiers']) + 1;
            $existingVersion = 0;
            // Iterate over the gifter graphics
            for ($i = 0; $i < $maxGraphics; $i++) {
                //Two nested loops iterate over the gifter graphics. The outer loop iterates $maxGraphics times, 
                //and the inner loop iterates over the elements in the $gifterGraphicsInput array.
                foreach ($gifterGraphicsInput as $index => $graphicData) {
                    $filename = "leaderboard-gifters-main";
                    foreach ($existingGifterGraphics as $existingGraphic) {
                        // Check if the 'asset_url' and 'targeted_audience' and 'type' keys exist in the existing graphic
                        if (
                            isset($existingGraphic['asset_url'])&&
                            isset($existingGraphic['type']) &&
                            isset($existingGraphic['targeted_audiences'])&&
                            $existingGraphic['type'] === 'leaderboard_banner' &&
                            $existingGraphic['targeted_audiences'] === 'viewers'
                        ) {
                            //Extract the version number from the asset URL
                            //the () in (\d+) captures the digits as a matching group.
                            //If there is a match, the captured digits (the version number) are stored in the $matches array.
                            if (preg_match('/-v(\d+)\.png$/', $existingGraphic['asset_url'], $matches)) {
//$matches[0]: This represents the entire matched string, not just the content captured by a specific capturing group. In the context of your regular expression, $matches[0] would be the entire string that matches the pattern /-v(\d+)\.png$/. It includes the -v, the digits, and the .png.
//$matches[1]: This represents the content captured by the first capturing group in your regular expression, which is the part inside the parentheses (\d+). In the context of your regular expression, $matches[1] would be just the digits that match the \d+ part.
                                $existingVersion = $matches[1];
                                //dd($existingVersion);
                            }
                        }
                    }
                    // Increment the version for the new file
                    $newVersion = $existingVersion + 1;
                    // Append the version to the filename
                    $filenameWithVersion = $filename . '-v' . $newVersion;
                    // Handle file upload and store the file path
                    $assetUrl = $this->uploadFileAndGetPath($request, "gifterGraphics.$index.asset_url", (new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->input('contest_start_at'))))->toDateTime()->format('Ymd'),
                    str_replace(' ', '-', $request->input('title')), $filenameWithVersion);
        
                    // Initialize an empty array for the current gifter graphic
                    $currentGifterGraphic = [
                        'type' => 'leaderboard_banner',
                        'asset_url' => $assetUrl,
                        'reference' => 'live_contest_lv_banner_1',
                        'url' => $url,
                        'targeted_audiences' => 'viewers',
                        'is_countdown_required' => (bool) ($graphicData['is_countdown_required'] ?? true),
                        'countdown_font_color' => $graphicData['countdown_font_color'],
                        'title_font_color' => $graphicData['title_font_color'],
                        'cta' => 'leaderboard',
                    ];

                    // Find the tier configuration based on the selected tier ID
                    $selectedTierConfig = collect($tierConfig['tiers'])->firstWhere('tier', $i);
                    if ($selectedTierConfig) {
                        $currentGifterGraphic['tier'] = $selectedTierConfig['tier'];
                    }
                    $currentGifterGraphic = array_filter($currentGifterGraphic, function ($value) {
                        return !is_null($value);
                    });
        
                    //It uses the collect function to create a collection from the 'tiers' array within $tierConfig.
                    // Check if the current gifter graphic exists in the existing graphics
                    $existingGifterGraphic = collect($existingGifterGraphics)->first(function ($existingGraphic) use ($currentGifterGraphic) {
                        // Check if the 'targeted_audiences' key exists in both existing and current graphics
                        return isset($existingGraphic['targeted_audiences']) && isset($currentGifterGraphic['targeted_audiences']) && $existingGraphic['type'] === $currentGifterGraphic['type'] && $existingGraphic['targeted_audiences'] === $currentGifterGraphic['targeted_audiences'];
                    });
                    if ($existingGifterGraphic) {
                        // If the gifter graphic hasn't changed, use the previous data
                        $currentGifterGraphic = array_merge($existingGifterGraphic, $currentGifterGraphic);
                    }
                    $gifterGraphics[] = $currentGifterGraphic;
                }
            }
        }

        // Update streamer graphics
        $streamerGraphics = [];
        $streamerGraphicsTier1 = [];
        $streamerGraphicsTier2 = [];
        $streamerGraphicsTier3 = [];
        
        $streamerGraphicsInputs = [
            'streamerGraphics' => $request->input('streamerGraphics'),
            'streamerGraphicsTier1' => $request->input('streamerGraphicsTier1'),
            'streamerGraphicsTier2' => $request->input('streamerGraphicsTier2'),
            'streamerGraphicsTier3' => $request->input('streamerGraphicsTier3'),
        ];
        
        // Define the tiers
        $tierConfigs = config('contest_tier.tier_system');
        $tiers = array_merge([null], array_column($tierConfigs, 'id'));

        //iterates through each tier
        foreach ($tiers as $tier) {

            // tier not null it will append to the string tier followed by the value $tier
            //It builds a key by concatenating the string 'streamerGraphics' with 'Tier' followed by the value of $tier if $tier is not null. 
            //This key construction is likely used for identifying and organizing streamer graphics data.
            $key = 'streamerGraphics' . ($tier !== null ? 'Tier' . $tier : ''); // Build the key

            // check if input data for the key exist, if not skip to the next iteration
            if (!isset($streamerGraphicsInputs[$key])) {
                continue; // Skip if the input data doesn't exist
            }
            //skips to the next iteration for the tier is more than 3
            // Skip processing Tier 3 graphics if the selected tier is more than 2
            $numArraysForSelectedTierRRR = count($tierConfig['tiers']);

            if($numArraysForSelectedTierRRR === 2 && $tier === 3){
                continue;
            }
            foreach ($streamerGraphicsInputs[$key] as $index => $graphicData) {
                $filename = "leaderboard-streamers-main";
                $existingVersion = 0;
                if ($tier !== null) {
                    $filename = "leaderboard-streamers-tier{$tier}";
                }
                foreach ($existingGifterGraphics as $existingGraphic) {
                    // Check if the 'asset_url' key exists in the existing graphic
                    if (
                        isset($existingGraphic['tier']) &&
                        isset($existingGraphic['asset_url']) &&
                        isset($existingGraphic['type']) &&
                        isset($existingGraphic['targeted_audiences'])
                    ) {
                        if (
                            $existingGraphic['type'] === 'leaderboard_banner' &&
                            $existingGraphic['targeted_audiences'] === 'streamers' &&
                            ($existingGraphic['tier'] === $tier || $existingGraphic['tier'] === null)
                            ) {
                            if (preg_match('/-v(\d+)\.png$/', $existingGraphic['asset_url'], $matches)) {
                                $existingVersion = (int) $matches[1];
                            }
                        }
                    }
                }
                $newVersion = $existingVersion + 1;
                $filenameWithVersion = $filename . "-v{$newVersion}";
                $assetUrl = $this->uploadFileAndGetPath($request, "$key.$index.asset_url",(new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->input('contest_start_at'))))->toDateTime()->format('Ymd'),
                str_replace(' ', '-', $request->input('title')), $filenameWithVersion);

                $currentStreamerGraphic = [
                    'type' => 'leaderboard_banner',
                    'asset_url' => $assetUrl,
                    'reference' => 'live_contest_lv_banner_1',
                    'url' => $url,
                    'targeted_audiences' => 'streamers',
                    'is_countdown_required' => (bool) ($graphicData['is_countdown_required'] ?? true),
                    'title_font_color' => $graphicData['title_font_color'],
                    'countdown_font_color' => $graphicData['countdown_font_color'],
                    'cta' => 'leaderboard',
                ];

                $selectedTierConfig = collect($tierConfig['tiers'])->firstWhere('tier', $tier);
                if ($selectedTierConfig) {
                    $currentStreamerGraphic['tier'] = $selectedTierConfig['tier'];
                }
                $currentStreamerGraphic = array_filter($currentStreamerGraphic, function ($value) {
                    return !is_null($value);
                });

                $existingGifterGraphic = collect($existingGifterGraphics)->first(function ($existingGraphic) use ($currentStreamerGraphic, $tier) {
                    return isset($existingGraphic['targeted_audiences']) 
                        && isset($currentStreamerGraphic['targeted_audiences']) 
                        && isset($existingGraphic['tier'])
                        && isset($currentStreamerGraphic['tier'])
                        && $existingGraphic['type'] === $currentStreamerGraphic['type'] 
                        && $existingGraphic['targeted_audiences'] === $currentStreamerGraphic['targeted_audiences']
                        && $existingGraphic['tier'] === $currentStreamerGraphic['tier'];
                });

                if ($existingGifterGraphic) {
                    $currentStreamerGraphic = array_merge($existingGifterGraphic, $currentStreamerGraphic);
                }
                $existingStreamerGifterGraphic = collect($existingGifterGraphics)->first(function ($existingGraphic) use ($currentStreamerGraphic) {
                    return isset($existingGraphic['targeted_audiences']) && isset($currentStreamerGraphic['targeted_audiences']) && $existingGraphic['type'] === $currentStreamerGraphic['type'] && $existingGraphic['targeted_audiences'] === $currentStreamerGraphic['targeted_audiences'];
                });
                if ($existingStreamerGifterGraphic) {
                    $currentStreamerGraphic = array_merge($existingStreamerGifterGraphic, $currentStreamerGraphic);
                }

                if ($tier === 1) {
                    $streamerGraphicsTier1[] = $currentStreamerGraphic;
                } elseif ($tier === 2) {
                    $streamerGraphicsTier2[] = $currentStreamerGraphic;
                } elseif ($tier === 3) {
                    $streamerGraphicsTier3[] = $currentStreamerGraphic;
                } else {
                    // If tier is null or any other value, add it to the general streamerGraphics array
                    $streamerGraphics[] = $currentStreamerGraphic;
                }
            }
        }

        // Update floating graphics
        $floatingGifterGraphics = [];
        $floatingStreamerGraphics = [];

        $floatingGifterGraphicsInput = $request->input('floatingGifterGraphics');
        $floatingStreamerGraphicsInput = $request->input('floatingStreamerGraphics');

            // Floating Banner Gifter
        foreach ($floatingGifterGraphicsInput as $index => $graphicData) {

            $filename = "player-floating-banner";
            $existingVersion = 0;
            foreach ($existingGifterGraphics as $existingGraphic) {
                if (
                    isset($existingGraphic['asset_url'])&&
                    isset($existingGraphic['type']) &&
                    isset($existingGraphic['targeted_audiences'])&&
                    $existingGraphic['type'] === 'player_floating_banner' &&
                    $existingGraphic['targeted_audiences'] === 'viewers'
                ) {
                    // Extract the version number from the asset URL
                    if (preg_match('/-v(\d+)\.png$/', $existingGraphic['asset_url'], $matches)) {
                        $existingVersion = (int) $matches[1];
                    }
                }
            }
            $newVersion = $existingVersion + 1;
            $filenameWithVersion = $filename . '-v' . $newVersion;
            $assetUrl = $this->uploadFileAndGetPath($request, "floatingGifterGraphics.$index.asset_url", (new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->input('contest_start_at'))))->toDateTime()->format('Ymd'),
            str_replace(' ', '-', $request->input('title')), $filenameWithVersion);

            $currentFloatingGifterGraphic = [
                'type' => 'player_floating_banner',
                'asset_url' => $assetUrl,
                'targeted_audiences' => 'viewers',
                'is_countdown_required' => (bool) ($graphicData['is_countdown_required'] ?? true),
                'text' => $graphicData['text'],
                'title_font_color' => $graphicData['title_font_color'],
                'countdown_font_color' => $graphicData['countdown_font_color'],
                'template' => $graphicData['template'],
                'cta' => 'mini_leaderboard',
            ];
            $currentFloatingGifterGraphic = array_filter($currentFloatingGifterGraphic, function ($value) {
                return !is_null($value);
            });
            $existingGifterGraphic = collect($existingGifterGraphics)->first(function ($existingGraphic) use ($currentFloatingGifterGraphic) {
                return isset($existingGraphic['targeted_audiences']) && isset($currentFloatingGifterGraphic['targeted_audiences']) && $existingGraphic['type'] === $currentFloatingGifterGraphic['type'] && $existingGraphic['targeted_audiences'] === $currentFloatingGifterGraphic['targeted_audiences'];
            });
            if ($existingGifterGraphic) {
                $currentFloatingGifterGraphic = array_merge($existingGifterGraphic, $currentFloatingGifterGraphic);
            }

            $floatingGifterGraphics[] = $currentFloatingGifterGraphic;
        }

        // Floating Banner Streamer
        foreach ($floatingStreamerGraphicsInput as $index => $graphicData) {

            $filename = "player-floating-banner";
            $existingVersion = 0;
            foreach ($existingGifterGraphics as $existingGraphic) {
                if (
                    isset($existingGraphic['asset_url'])&&
                    isset($existingGraphic['type']) &&
                    isset($existingGraphic['targeted_audiences'])&&
                    $existingGraphic['type'] === 'player_floating_banner' &&
                    $existingGraphic['targeted_audiences'] === 'streamers'
                ) {
                    if (preg_match('/-v(\d+)\.png$/', $existingGraphic['asset_url'], $matches)) {
                        $existingVersion = (int) $matches[1];
                    }
                }
            }
            $newVersion = $existingVersion + 1;
            $filenameWithVersion = $filename . '-v' . $newVersion;
            $assetUrl = $this->uploadFileAndGetPath($request, "floatingStreamerGraphics.$index.asset_url", (new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->input('contest_start_at'))))->toDateTime()->format('Ymd'),
            str_replace(' ', '-', $request->input('title')), $filenameWithVersion);
            $currentFloatingStreamerGraphic = [
                'type' => 'player_floating_banner',
                'asset_url' => $assetUrl,
                'targeted_audiences' => 'streamers',
                'is_countdown_required' => (bool) ($graphicData['is_countdown_required'] ?? true),
                'title_font_color' => $graphicData['title_font_color'],
                'text' => $graphicData['text'],
                'countdown_font_color' => $graphicData['countdown_font_color'],
                'template' => $graphicData['template'],
                'cta' => 'mini_leaderboard',
            ];
            $currentFloatingStreamerGraphic = array_filter($currentFloatingStreamerGraphic, function ($value) {
                return !is_null($value);
            });
            $existingGifterGraphic = collect($existingGifterGraphics)->first(function ($existingGraphic) use ($currentFloatingStreamerGraphic) {
                return isset($existingGraphic['targeted_audiences']) && isset($currentFloatingStreamerGraphic['targeted_audiences']) && $existingGraphic['type'] === $currentFloatingStreamerGraphic['type'] && $existingGraphic['targeted_audiences'] === $currentFloatingStreamerGraphic['targeted_audiences'];
            });
            if ($existingGifterGraphic) {
                $currentFloatingStreamerGraphic = array_merge($existingGifterGraphic, $currentFloatingStreamerGraphic);
            }
            $floatingStreamerGraphics[] = $currentFloatingStreamerGraphic;
        }

        // Update coin graphics
        $coinGraphics = [];
        $coinGraphicsInput = $request->input('coinGraphics');
        foreach ($coinGraphicsInput as $index => $graphicData) {
            $filename = "coin-shop";
            $existingVersion = 0;
            foreach ($existingGifterGraphics as $existingGraphic) {
                if (
                    isset($existingGraphic['asset_url'])&&
                    isset($existingGraphic['type']) &&
                    isset($existingGraphic['targeted_audiences'])&&
                    $existingGraphic['type'] === 'coins_contest_banner' &&
                    $existingGraphic['targeted_audiences'] === 'all'
                ) {
                    if (preg_match('/-v(\d+)\.png$/', $existingGraphic['asset_url'], $matches)) {
                        $existingVersion = (int) $matches[1];
                    }
                }
            }
            $newVersion = $existingVersion + 1;
            $filenameWithVersion = $filename . '-v' . $newVersion;
            $assetUrl = $this->uploadFileAndGetPath($request, "coinGraphics.$index.asset_url", (new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->input('contest_start_at'))))->toDateTime()->format('Ymd'),
            str_replace(' ', '-', $request->input('title')), $filenameWithVersion);
            $currentCoinGraphic = [
                'type' => 'coins_contest_banner',
                'asset_url' => $assetUrl,
                'targeted_audiences' => 'all',
            ];
            $currentCoinGraphic = array_filter($currentCoinGraphic, function ($value) {
                return !is_null($value);
            });
            $existingGifterGraphic = collect($existingGifterGraphics)->first(function ($existingGraphic) use ($currentCoinGraphic) {
                return isset($existingGraphic['targeted_audiences']) && isset($currentCoinGraphic['targeted_audiences']) && $existingGraphic['type'] === $currentCoinGraphic['type'] && $existingGraphic['targeted_audiences'] === $currentCoinGraphic['targeted_audiences'];
            });
            if ($existingGifterGraphic) {
                $currentCoinGraphic = array_merge($existingGifterGraphic, $currentCoinGraphic);
            }
            $coinGraphics[] = $currentCoinGraphic;
        }

        // Get the existing gifts_bounded array from the event or initialize an empty array
        $existingGiftBounded = $event->gifts_bounded ?? [];
        $giftsBounded = [];
        $giftsInput = $request->input('gifts_bounded');

        foreach ($giftsInput as $gift) {
            // Check if the required keys are present in the input
            if (isset($gift['id'], $gift['pricing_id'])) {
                // Initialize a currentGift array with non-null values
                $currentGift = [
                    'id' => $gift['id'],
                    'pricing_id' => $gift['pricing_id'],
                ];

                // Check if the current gift exists in the existing gifts_bounded array
                $existingGift = collect($existingGiftBounded)->first(function ($existing) use ($currentGift) {
                    return isset($existing['id']) && isset($currentGift['id']) && $existing['id'] === $currentGift['id'] && $existing['pricing_id'] === $currentGift['pricing_id'];
                });
                if ($existingGift) {
                    $currentGift = array_merge($existingGift, $currentGift);
                }
                $currentGift = array_filter($currentGift, function ($value) {
                    return $value !== null;
                });

                // Add the current gift to the "gifts_bounded" array if it's not empty
                if (!empty($currentGift)) {
                    $giftsBounded[] = $currentGift;
                }
            }
        }

        // Update the contest with the new data
        $event->update([
            'title' => $validatedData['title'],
            'title_locale' => $validatedData['title_locale'],
            'graphics' => array_merge(
                $gifterGraphics,
                $streamerGraphics,
                $streamerGraphicsTier1,
                $streamerGraphicsTier2,
                $streamerGraphicsTier3,
                $floatingGifterGraphics,
                $floatingStreamerGraphics,
                $coinGraphics, 
            ),
            'gifts_bounded' => array_merge($giftsBounded),
            //'gifts_bounded' => $validatedData['gifts_bounded'],
            // Add other fields you want to update
        ]);

        $selectedTierId = (int)$request->input('tier_system');
        // Update tier_system
        $event->tier_system = $selectedTierId;

        $event->save();
        //$event->update($request->all());

        return redirect()->route('contests.post')->with('success', 'Contest updated successfully');
    }

    /**
     * Uploads a file and returns its path.
     *
     * @param  \Illuminate\Http\Request  $request The request containing the file.
     * @param  string  $fileInputName The name of the file input in the request.
     * @param  string  $contestStartAt The start date of the contest.
     * @param  string  $title The title of the contest.
     * @param  string  $filename The desired filename.
     * @return string|null The full URL of the uploaded file, or null if no file was uploaded.
     */

    private function uploadFileAndGetPath($request, $fileInputName, $contestStartAt, $title, $filename)
    {
        //CHECK if a file with the specified input name exists in the request
        if ($request->hasFile($fileInputName)) {

            //file exist it gets the file instance from the request
            $file = $request->file($fileInputName);

            // Extract the file extension from the original file name
            $extension = $file->getClientOriginalExtension();

            // Store the file with the new path
            $path = $file->storeAs('coin_graphics', $filename . '.' . $extension, 'public');

            // Construct and return the full URL
            return "https://image.sgrbk.com/assets/contest/{$contestStartAt}-{$title}/{$filename}.{$extension}";
        }
        return null;
    }
}

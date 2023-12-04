<?php

namespace App\Http\Controllers\LiveStreaming;

use Illuminate\Http\Request;
use App\Models\Events; // Make sure to use the correct mode
use Hashids\Hashids;
use Carbon\Carbon;
use App\Http\Requests\StoreContestRequest;
use Config;


/**
 * Class StoreContestController
 *
 * This controller is responsible for storing a newly created contest in the database.
 *
 * @package App\Http\Controllers\LiveStreaming
 */
class StoreContestController
{
    /**
     * Store a newly created contest in the database.
     *
     * @param  \App\Http\Requests\StoreContestRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreContestRequest $request)
    {
        // Retrieve selected tier ID from the request and convert it to an integer
        $selectedTierId = (int)$request->input('tier_system');

        // Attempt to retrieve the tier configuration from the configuration file
        $tierConfig = collect(Config::get('contest_tier.tier_system'))->firstWhere('id', $selectedTierId);

        // Check if the tier configuration exists
        if (!$tierConfig) {
            // Handle the case where the tier configuration doesn't exist (you may want to add appropriate error handling)
            return redirect()->route('contests.create')
                ->withErrors(['tier_system' => 'Invalid tier system selected.'])
                ->withInput();
        }        

        // Validate and store the input data in the MongoDB database
        $uniqueContestId = $this->generateUniqueContestId();
        $validatedData = $request->validated();

        $validatedData['tier_system'] = $selectedTierId;
        //$validatedData = $this->removeNullValues($validatedData);

        // Check if a record with the same title already exists
        $existingContest = Events::where('title', $validatedData['title'])->first();

        if ($existingContest) {
            // If a contest with the same title exists, return a validation error with input data
            return redirect()->route('contests.create')
                ->withErrors(['title' => 'The title already exists. Please choose a different title.'])
                ->withInput();
        }

        // Generate URL based on the title
        $prefix = 'https://sugarbook.me/live-streaming/contests/';
        $title = $validatedData['title'];
        $url = $prefix . str_replace(' ', '-', strtolower($title));
        $validatedData['url'] = $url;


        // Set default values for some fields
        $validatedData['contest_id'] = $uniqueContestId;
        $validatedData['sorting'] = null;
        $validatedData['is_countdown_required'] = true;
        $validatedData['auto_enrollment'] = 1;

        // Generate title_locale based on title
        $title = $validatedData['title'];
        $title_locale = 'contest.title.' . str_replace(' ', '', strtolower($title));
        $validatedData['title_locale'] = $title_locale;
        
        // Convert is_countdown_required to boolean
        $validatedData['is_countdown_required'] = (bool)$validatedData['is_countdown_required'];

        // Convert date-time fields to MongoDB UTCDateTime
        $validatedData['contest_start_at'] = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_start_at));
        $validatedData['contest_end_at'] = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_end_at));
        $validatedData['contest_display_start_at'] = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_display_start_at));
        $validatedData['contest_display_end_at'] = new \MongoDB\BSON\UTCDateTime(Carbon::parse($request->contest_display_end_at));

        // Initialize arrays for gifter, streamer, floating graphics, and coin graphics
        $gifterGraphics = [];
        $streamerGraphics = [];
        $streamerGraphicsTier1 = [];
        $streamerGraphicsTier2 = [];
        $streamerGraphicsTier3 = [];
        $floatingGifterGraphics = [];
        $floatingStreamerGraphics = [];
        $coinGraphics = [];
        // Loop through the form input to create the gifter graphics array
        $gifterGraphicsInput = $request->input('gifterGraphics');

        // Initialize an array to store streamer graphics based on different tiers
        $streamerGraphicsInputs = [
            'streamerGraphics' => $request->input('streamerGraphics'),
            'streamerGraphicsTier1' => $request->input('streamerGraphicsTier1'),
            'streamerGraphicsTier2' => $request->input('streamerGraphicsTier2'),
            'streamerGraphicsTier3' => $request->input('streamerGraphicsTier3'),
        ];

        // Initialize arrays for floating gifter graphics and floating streamer graphics
        $floatingGifterGraphicsInput = $request->input('floatingGifterGraphics');
        $floatingStreamerGraphicsInput = $request->input('floatingStreamerGraphics');

        // Initialize an array for coin graphics
        $coinGraphicsInput = $request->input('coinGraphics');

        // Check if the tier configuration exists
        if ($tierConfig && isset($tierConfig['tiers'])) {
            // Determine the maximum number of gifter graphics based on tier_system

            $numArraysForSelectedTier = count($tierConfig['tiers']);

            $maxGraphics = $numArraysForSelectedTier + 1;
            //dd($maxGraphics);

            // Iterate over the gifter graphics
            for ($i = 0; $i < $maxGraphics; $i++) {
                foreach ($gifterGraphicsInput as $index => $graphicData) {

                    $filename = "leaderboard_gifters_main_v1";
                    // Handle file upload and store the file path
                    $assetUrl = $this->uploadFileAndGetPath($request, "gifterGraphics.$index.asset_url", $validatedData['contest_start_at']->toDateTime()->format('Ymd'), str_replace(' ', '-', $request->input('title')), $filename);

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

                    $gifterGraphics[] = $currentGifterGraphic;
                }
            }
        }

        // Define the tiers

        $tierConfigs = config('contest_tier.tier_system');
        $tiers = array_merge([null], array_column($tierConfigs, 'id'));

        foreach ($tiers as $tier) {
            $key = 'streamerGraphics' . ($tier !== null ? 'Tier' . $tier : ''); // Build the key

            if (!isset($streamerGraphicsInputs[$key])) {
                continue; // Skip if the input data doesn't exist
            }

            $numArraysForSelectedTierRRR = count($tierConfig['tiers']);

            if($numArraysForSelectedTierRRR === 2 && $tier === 3){
                continue;
            }
            
            /*
            // Skip processing Tier 3 graphics if the selected tier is more than 2
            if ($tier === 3 && $selectedTierId > 2) {
                continue;
            }
            */
            foreach ($streamerGraphicsInputs[$key] as $index => $graphicData) {
                // Determine the filename based on the tier
                $filename = "leaderboard-streamers-main-v1";
                
                if ($tier !== null) {
                    $filename = "leaderboard-streamers-tier{$tier}-v1";
                }

                // Handle file upload and store the file path
                $assetUrl = $this->uploadFileAndGetPath($request, "$key.$index.asset_url", $validatedData['contest_start_at']->toDateTime()->format('Ymd'), str_replace(' ', '-', $request->input('title')), $filename);

                // Initialize an empty array for the current streamer graphic
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
                // Find the tier configuration based on the selected tier ID
                $selectedTierConfig = collect($tierConfig['tiers'])->firstWhere('tier', $tier);
                    
                if ($selectedTierConfig) {
                    $currentStreamerGraphic['tier'] = $selectedTierConfig['tier'];
                }

                // Add the current streamer graphic to the appropriate tier array
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

        // Floating Banner Gifter
        foreach ($floatingGifterGraphicsInput as $index => $graphicData) {

            $filename = "player-floating-banner-v1";

            // Handle file upload and store the file path
            $assetUrl = $this->uploadFileAndGetPath($request, "floatingGifterGraphics.$index.asset_url", $validatedData['contest_start_at']->toDateTime()->format('Ymd'), str_replace(' ', '-', $request->input('title')), $filename );

            // Initialize an empty array for the current streamer graphic
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

            $floatingGifterGraphics[] = $currentFloatingGifterGraphic;
        }

        // Floating Banner Streamer
        foreach ($floatingStreamerGraphicsInput as $index => $graphicData) {

            $filename = "player-floating-banner-v1";

            // Handle file upload and store the file path
            $assetUrl = $this->uploadFileAndGetPath($request, "floatingStreamerGraphics.$index.asset_url", $validatedData['contest_start_at']->toDateTime()->format('Ymd'), str_replace(' ', '-', $request->input('title')), $filename );

            // Initialize an empty array for the current streamer graphic
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

            $floatingStreamerGraphics[] = $currentFloatingStreamerGraphic;
        }


        foreach ($coinGraphicsInput as $index => $graphicData) {

            $filename = "coin_shop";

            // Handle file upload and store the file path
            $assetUrl = $this->uploadFileAndGetPath($request, "coinGraphics.$index.asset_url", $validatedData['contest_start_at']->toDateTime()->format('Ymd'), str_replace(' ', '-', $request->input('title')), $filename );

            // Initialize an empty array for the current streamer graphic
            $currentCoinGraphic = [
                'type' => 'coins_contest_banner',
                'asset_url' => $assetUrl,
                'targeted_audiences' => 'all',
            ];
            $coinGraphics[] = $currentCoinGraphic;
        }

        $validatedData['graphics'] = array_merge($gifterGraphics, $streamerGraphics, $streamerGraphicsTier1, $streamerGraphicsTier2, $streamerGraphicsTier3, $floatingStreamerGraphics, $floatingGifterGraphics, $coinGraphics);

        //GIFTS
        // Initialize an empty array for "gifts_bounded"
        $giftsBounded = [];

        // Loop through the form input to create the "gifts_bounded" array
        $giftsInput = $request->input('gifts_bounded');
        foreach ($giftsInput as $gift) {
            // Initialize an empty array for the current gift
            if (isset($gift['id'], $gift['pricing_id'])){
                $currentGift = [
                    'id' => $gift['id'],
                    'pricing_id' => $gift['pricing_id'],
                ];

                // Remove null values from the current gift
                $currentGift = array_filter($currentGift, function ($value) {
                    return $value !== null;
                });

                // Add the current gift to the "gifts_bounded" array if it's not empty
                if (!empty($currentGift)) {
                    $giftsBounded[] = $currentGift;
                }
            }
        }

        // Add the "gifts_bounded" array to the $validatedData
        $validatedData['gifts_bounded'] = $giftsBounded;
        $validatedData['tier_system'] = $selectedTierId;

        $newContest = Events::create($validatedData);
        return response()->json(["result" => "ok"], 201);
    
    }

    /**
     * Upload a file and get its storage path.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $fileInputName
     * @param  string  $contestStartAt
     * @param  string  $title
     * @param  string  $filename
     * @return string|null
     */

    private function uploadFileAndGetPath($request, $fileInputName, $contestStartAt, $title, $filename)
    {
        if ($request->hasFile($fileInputName)) {
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

    /**
     * Check if a contest title already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTitle(Request $request)
    {
        $title = $request->input('title');

        $exists = Events::where('title', $title)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Generate a unique contest ID.
     *
     * @return string
     */
    private function generateUniqueContestId()
    {
        // Generate a unique "contest_id" with a maximum length of 12 characters
        $hashids = new Hashids('your_salt_here', 12); // Adjust the salt and min length as needed

        // Create a unique "contest_id" based on the current timestamp
        $uniqueContestId = $hashids->encode(time());

        // Check if the "contest_id" already exists in the database and generate a new one if it does
        while (Events::where('contest_id', $uniqueContestId)->exists()) {
            $uniqueContestId = $hashids->encode(time() + rand(10000, 99999)); // Add randomness
        }

        return $uniqueContestId;
    }
}
?>

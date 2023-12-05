<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <title>Edit Contest</title>
    <style>
        body {
            background: linear-gradient(135deg, #dfd8d8, #150505); 
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }

        a {
            text-align: center;
            color: #00f; 
            margin-bottom: 20px;
        }

        .delete-button-blue {
            background-color: #00f;
        }

        h1 {
            text-align: center;
            color: #fff; 
            font-family: Georgia, 'Times New Roman', Times, serif;
            text-shadow: 2px 2px;
        }

        form {
            max-width: 50%;
            margin: 20px auto;
            padding: 20px;
            background: linear-gradient(135deg,rgb(78, 19, 19), rgb(31, 2, 2) ); 
            border-radius: 8px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        select,
        button {
            
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            background-color: #555; 
            color: #fff;
            border: none;
            border-radius: 4px;
        }

        input{
            width:97%;
            padding: 1px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .datetime-input {
            width: 150%;
        }

        .alert {
            background-color: #f00; 
            color: #fff; 
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .graphic-container {
            margin-bottom: 20px;
        }

        .side-by-side {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-content {
            background-color: black;
        }

        .standout-label {
            color: #ffffff;
            font-weight: bold; 
            font-size: 1.3em; 
        }

        .isCountdownRequiredCheckbox:checked + .text {
            display: block;
        }

        .isCountdownRequiredCheckbox:not(:checked) + .text {
            display: none;
        }

        .color-input-container {
            display:flex;
            justify-content:space-evenly;
            
        }

        .color-input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
        }

        .color-label {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Page title -->
    <h1>Edit contest</h1>
    
    <div>
        <!-- Display validation errors -->
        @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
        @endif
    </div>
    <!-- Contest form -->
    <form method="post" action="{{ route('contests.update', $event->id) }}" enctype="multipart/form-data">

        <!-- Contest view link -->
        <a href="{{ route('contests.post') }}" style="color: #008080; text-decoration: underline; font-weight: bold;">View Contest</a>

        <!-- CSRF Token -->
        @csrf
        @method('put')

        <br><br>
        <!-- Select Tier System -->
        <div style="display:flex; gap:10px;">
            <label class="standout-label" for="tier_system">Select Tier System:</label>
            <select name="tier_system" id="tier_system" required>
                <!-- Populate options dynamically based on the tiers in contest_tier.php -->
                <!--retreive the tier system-->
                @foreach(Config::get('contest_tier.tier_system') as $tier)
                    <option value="{{ (int)$tier['id'] }}" data-toggle="tooltip" title="{{ json_encode($tier['tiers']) }}">{{ (int)$tier['id'] }}</option>
                @endforeach
            </select>
        </div>
        <div id="tierAttributesContainer"></div>
    
        <!-- Tier Attributes Modal -->
        <div class="modal fade" id="tierAttributesModal" tabindex="-1" role="dialog" aria-labelledby="tierAttributesModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="tierAttributesModalLabel" style="width: 1427%"></h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Content will be dynamically populated here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contest Title -->
        <div>
            <label class="standout-label" for="title">Title:</label>
            <input type="text" name="title" id="title" required value="{{ old('title', $event->title) }}">
        </div>

        <!-- Start and End Dates -->
        <div style="display:flex ; gap: 30%;">
            <div>
                <label class="standout-label" for="contest_start_at">Start <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_start_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_start_at', $event->contest_start_at->toDateTime()->format('Y-m-d H:i')) }}" required>
            </div>
        
            <div>
                <label class="standout-label" for="contest_end_at">End <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_end_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_end_at', $event->contest_end_at->toDateTime()->format('Y-m-d H:i')) }}" required>
            </div>
        </div>

        <!-- Display Start and End Dates -->
        <div style="display:flex ; gap: 30%;">
            <div>
                <label class="standout-label" for="contest_display_start_at">Display Start <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_display_start_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_display_start_at', $event->contest_display_start_at->toDateTime()->format('Y-m-d H:i')) }}" required>
            </div>

            <div>
                <label class="standout-label" for="contest_display_end_at">Display End <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_display_end_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_display_end_at', $event->contest_display_end_at->toDateTime()->format('Y-m-d H:i')) }}" required>
            </div>
        </div>    
        <br>

        <!-- Contest Type -->
        <div style="display:flex; gap:10px;">
            <label class="standout-label" for="contest_type">Type:</label>
            <select name="contest_type" required>
                <option value="single" {{ old('contest_type', $event->contest_type) === 'single' ? 'selected' : '' }}>Single</option>
                <option value="multi" {{ old('contest_type', $event->contest_type) === 'multi' ? 'selected' : '' }}>Multi</option>
            </select>
        </div>
        <br>

        <!-- Gift Section -->
        <div>
            <label class="standout-label" for="gifts_bounded">Gift:</label>
            <button type="button" style="background-color: #00f; color:#fff; cursor: pointer;" id="addGift">Add Gift</button>
            <div id="giftFields">
                <!-- Display existing gifts -->
                @if($event->gifts_bounded)
                    @foreach($event->gifts_bounded as $index => $gift)
                        <div class="gift">
                            <div style="display:flex ; gap: 15%">
                                <input  style="width:40%;"   type="text" name="gifts_bounded[0][id]" placeholder="Gift ID" value="{{ old("gifts_bounded.$index.id", $gift['id']) }}">
                                <input style="width:40%;" type="text" name="gifts_bounded[0][pricing_id]" placeholder="Pricing ID" value="{{ old("gifts_bounded.$index.pricing_id", $gift['pricing_id']) }}">
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Template for gift fields -->
                    <div class="gift">
                        <input type="text" name="gifts_bounded[0][id]" placeholder="Gift ID" value="{{ old('gifts_bounded.0.id') }}">
                        <input type="text" name="gifts_bounded[0][pricing_id]" placeholder="Pricing ID" value="{{ old('gifts_bounded.0.pricing_id') }}">
                    </div>
                @endif
            </div>
        </div>

        
        <!-- Gifter Graphics Main Banner -->
        <div class="graphic-container">
            <label class="standout-label" for="graphics">Gifter Banner:</label>
            <label type="button" id="addGifterGraphic"></label>
            <div id="gifterGraphicFields">
                @if($event->graphics)
                    @php
                        $firstMatchingGraphic = collect($event->graphics)->first(function ($graphic) {
                            return $graphic['type'] == 'leaderboard_banner' && $graphic['targeted_audiences'] == 'viewers';
                        });
                    @endphp
                    @if($firstMatchingGraphic)
                        <!-- Initial gifter graphic field -->
                        <div class="gifterGraphic">
                            @isset($firstMatchingGraphic['asset_url'])
                                <img id="gifterImagePreview" src="{{ asset($firstMatchingGraphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                            @endisset
                            <input type="file" name="gifterGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'gifterImagePreview')">
                            <div class="side-by-side">
                                <label for="is_countdown_required1" style="width: 50%; style:flex;">Is Countdown Required:</label>
                                <input type="hidden" name="gifterGraphics[0][is_countdown_required]" value="0">
                                <input type="checkbox" name="gifterGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("gifterGraphics.0.is_countdown_required", $firstMatchingGraphic['is_countdown_required']) ? ' checked' : '' }}>
                            </div>
                            <div class="color-input-container">
                                <div class="color-input-group">
                                    <label class="color-label titleFontColorLabel">Title Font Color</label>
                                    <input type="color" name="gifterGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('gifterGraphics.0.title_font_color', $firstMatchingGraphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamerGifter')">
                                    <p id="titleFontColorCodeStreamerGifter" class="additionalText1">Selected Color Code: <span>{{ old('gifterGraphics.0.title_font_color', $firstMatchingGraphic['title_font_color'] ?? '#000000') }}</span></p>
                                </div>
                                <div class="color-input-group">
                                    <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                    <input type="color" name="gifterGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('gifterGraphics.0.countdown_font_color', $firstMatchingGraphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeGifter')">
                                    <p id="countdownFontColorCodeGifter" class="additionalText1">Selected Color Code: <span>{{ old('gifterGraphics.0.countdown_font_color', $firstMatchingGraphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                </div>   
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        @if($event->graphics)
                    @php
                        // Find the first matching graphic for leaderboard_banner and viewers
                        $matchingGraphic = collect($event->graphics)->first(function ($graphic) {
                            return $graphic['type'] == 'leaderboard_banner' && $graphic['targeted_audiences'] == 'streamers';
                        });
                    @endphp
                <!-- Check if a matching graphic exists -->
                @if($matchingGraphic)
            <!-- Streamer Graphics Main Banner -->
            <div class="graphic-container">
                <label class="standout-label" for="graphics">Streamer Banner</label>
                <label class="standout-label" for="graphics">Main:</label>
                <div id="streamerGraphicFieldsMain">
                    <div id="gifterGraphicFields">
                        <!-- Initial streamer graphic field -->
                        <div class="streamerGraphic">
                            <!-- Display existing image if asset_url is set -->
                            @isset($matchingGraphic['asset_url'])
                                <img id="streamerImagePreview" src="{{ asset($matchingGraphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                            @endisset
                            <!-- Input field for uploading a new image -->
                            <input type="file" name="streamerGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerImagePreview')">
                            <!-- Checkbox for Is Countdown Required -->
                            <div class="side-by-side">
                                <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                                <input type="hidden" name="streamerGraphics[0][is_countdown_required]" value="0">
                                <input type="checkbox" name="streamerGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphics.0.is_countdown_required", $matchingGraphic['is_countdown_required'] ?? false) ? ' checked' : '' }}>
                            </div>
                            <!-- Color input container for Title and Countdown Font Color -->
                            <div class="color-input-container">
                                <!-- Title Font Color -->
                                <div class="color-input-group">
                                    <label class="color-label titleFontColorLabel">Title Font Color</label>
                                    <input type="color" name="streamerGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphics.0.title_font_color', $matchingGraphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamer')">
                                    <p id="titleFontColorCodeStreamer" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphics.0.title_font_color', $matchingGraphic['title_font_color'] ?? '#000000') }}</span></p>
                                </div>
                                <!-- Countdown Font Color -->
                                <div class="color-input-group">
                                    <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                    <input type="color" name="streamerGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphics.0.countdown_font_color', $matchingGraphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeStreamer')">
                                    <p id="countdownFontColorCodeStreamer" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphics.0.countdown_font_color', $matchingGraphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif

            @if($event->graphics)
                    @php
                        $matchingTier1Graphic = collect($event->graphics)->first(function ($graphic) {
                            return $graphic['type'] == 'leaderboard_banner' && $graphic['targeted_audiences'] == 'streamers' &&
                                    isset($graphic['tier']) && // Check if 'tier' exists
                                    is_numeric($graphic['tier']) && // Check if 'tier' is a number
                                    $graphic['tier'] == 1; // Check if 'tier' is equal to 2
                        });
                    @endphp
                @if($matchingTier1Graphic)
                <!-- Streamer Graphics Tier 1 Banner -->
                <div class="graphic-container">
                <label class="standout-label" for="graphics">Tier 1:</label>
                <div id="streamerGraphicFieldsMain">
                    <div id="gifterGraphicFields">
                        <!-- Initial streamer graphic field -->
                        <div class="streamerGraphic">
                            @isset($matchingTier1Graphic['asset_url'])
                                <img id="streamerTier1ImagePreview" src="{{ asset($matchingTier1Graphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                            @endisset
                            <input type="file" name="streamerGraphicsTier1[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerTier1ImagePreview')">
                            <div class="side-by-side">
                                <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                                <input type="hidden" name="streamerGraphicsTier1[0][is_countdown_required]" value="0">
                                <input type="checkbox" name="streamerGraphicsTier1[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphicsTier1.0.is_countdown_required", $matchingTier1Graphic['is_countdown_required'] ?? false) ? ' checked' : '' }}>
                            </div>
                            <div class="color-input-container">
                                <div class="color-input-group">
                                    <label class="color-label titleFontColorLabel">Title Font Color</label>
                                    <input type="color" name="streamerGraphicsTier1[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphicsTier1.0.title_font_color', $matchingTier1Graphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamerTier1')">
                                    <p id="titleFontColorCodeStreamerTier1" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphicsTier1.0.title_font_color', $matchingTier1Graphic['title_font_color'] ?? '#000000') }}</span></p>
                                </div>
                                <div class="color-input-group">
                                    <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                    <input type="color" name="streamerGraphicsTier1[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphicsTier1.0.countdown_font_color', $matchingTier1Graphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeStreamerTier1')">
                                    <p id="countdownFontColorCodeStreamerTier1" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphicsTier1.0.countdown_font_color', $matchingTier1Graphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                @endif
            @endif

            @if($event->graphics)
                    @php
                        $matchingTier2Graphic = collect($event->graphics)->first(function ($graphic) {
                            return $graphic['type'] == 'leaderboard_banner' && $graphic['targeted_audiences'] == 'streamers' &&
                                    isset($graphic['tier']) && // Check if 'tier' exists
                                    is_numeric($graphic['tier']) && // Check if 'tier' is a number
                                    $graphic['tier'] == 2; // Check if 'tier' is equal to 2
                        });
                    @endphp
                @if($matchingTier2Graphic)
                <!-- Streamer Graphics Tier 2 Banner -->
                <div class="graphic-container">
                <label class="standout-label" for="graphics">Tier 2:</label>
                <div id="streamerGraphicFieldsMain">
                    <div id="gifterGraphicFields">
                        <!-- Initial streamer graphic field -->
                        <div class="streamerGraphic">
                            @isset($matchingTier2Graphic['asset_url'])
                                <img id="streamerTier2ImagePreview" src="{{ asset($matchingTier2Graphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                            @endisset
                            <input type="file" name="streamerGraphicsTier2[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerTier2ImagePreview')">
                            <div class="side-by-side">
                                <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                                <input type="hidden" name="streamerGraphicsTier2[0][is_countdown_required]" value="0">
                                <input type="checkbox" name="streamerGraphicsTier2[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphicsTier2.0.is_countdown_required", $matchingTier2Graphic['is_countdown_required'] ?? false) ? ' checked' : '' }}>
                            </div>
                            <div class="color-input-container">
                                <div class="color-input-group">
                                    <label class="color-label titleFontColorLabel">Title Font Color</label>
                                    <input type="color" name="streamerGraphicsTier2[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphicsTier2.0.title_font_color', $matchingTier2Graphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamerTier2')">
                                    <p id="titleFontColorCodeStreamerTier2" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphicsTier2.0.title_font_color', $matchingTier2Graphic['title_font_color'] ?? '#000000') }}</span></p>
                                </div>
                                <div class="color-input-group">
                                    <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                    <input type="color" name="streamerGraphicsTier2[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphicsTier2.0.countdown_font_color', $matchingTier2Graphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeStreamerTier2')">
                                    <p id="countdownFontColorCodeStreamerTier2" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphicsTier2.0.countdown_font_color', $matchingTier2Graphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                @endif
            @endif


            @if($event->graphics)
                    @php
                        $matchingTier3Graphic = collect($event->graphics)->first(function ($graphic) {
                            return $graphic['type'] == 'leaderboard_banner' && $graphic['targeted_audiences'] == 'streamers' &&
                                    isset($graphic['tier']) && // Check if 'tier' exists
                                    is_numeric($graphic['tier']) && // Check if 'tier' is a number
                                    $graphic['tier'] == 3; // Check if 'tier' is equal to 3
                        });
                    @endphp
                @if($matchingTier3Graphic)
                    <!-- Streamer Graphics Tier 3 Banner -->
                    <div class="graphic-containerr">
                        <label type="button" id="addStreamerGraphic"></label>
                        <div id="streamerGraphicFields">
                            <!-- Initial streamer graphic field -->
                            <div class="streamerGraphicTier3">
                                <label class="standout-label" for="graphics">Tier 3:</label>
                                @isset($matchingTier3Graphic['asset_url'])
                                    <img id="streamerTier3ImagePreview" src="{{ asset($matchingTier3Graphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                                @endisset
                                <input type="file" name="streamerGraphicsTier3[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerTier3ImagePreview')">
                                <div class="side-by-side">
                                    <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                                    <input type="hidden" name="streamerGraphicsTier3[0][is_countdown_required]" value="0">
                                    <input type="checkbox" name="streamerGraphicsTier3[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphicsTier3.0.is_countdown_required", $matchingTier3Graphic['is_countdown_required'] ?? false) ? ' checked' : '' }}>
                                </div>
                                <div class="color-input-container">
                                    <div class="color-input-group">
                                        <label class="color-label titleFontColorLabel">Title Font Color</label>
                                        <input type="color" name="streamerGraphicsTier3[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphicsTier3.0.title_font_color', $matchingTier3Graphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamerTier3')">
                                        <p id="titleFontColorCodeStreamerTier3" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphicsTier3.0.title_font_color', $matchingTier3Graphic['title_font_color'] ?? '#000000') }}</span></p>
                                    </div>
                                    <div class="color-input-group">
                                        <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                        <input type="color" name="streamerGraphicsTier3[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('streamerGraphicsTier3.0.countdown_font_color', $matchingTier3Graphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeStreamerTier3')">
                                        <p id="countdownFontColorCodeStreamerTier3" class="additionalText1">Selected Color Code: <span>{{ old('streamerGraphicsTier3.0.countdown_font_color', $matchingTier3Graphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if($event->graphics)
                    @php
                        $matchingFloatingGifterGraphic = collect($event->graphics)->first(function ($graphic) {
                            return $graphic['type'] == 'player_floating_banner' && $graphic['targeted_audiences'] == 'viewers';
                        });
                    @endphp
        
                @if($matchingFloatingGifterGraphic)
                    <!-- Floating Banner Gifter -->
                    <div class="graphic-container">
                    <label class="standout-label" for="graphics">Floating Gifter Banner:</label>
                    <div id="streamerGraphicFieldsMain">
                    <div id="gifterGraphicFields">
                        
                        <!-- Initial streamer graphic field -->
                        <div class="floatingGifterGraphics">
                            @isset($matchingFloatingGifterGraphic['asset_url'])
                                <img id="streamerFloatingGifterImagePreview" src="{{ asset($matchingFloatingGifterGraphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                            @endisset
                            <input type="file" name="floatingGifterGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerFloatingGifterImagePreview')">
                            <input type="text" name="floatingGifterGraphics[0][text]" placeholder="copies" value="{{ old("floatingGifterGraphics.0.text", $matchingFloatingGifterGraphic['text'] ?? '') }}">

                            <div class="side-by-side">
                                <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                                <input type="hidden" name="floatingGifterGraphics[0][is_countdown_required]" value="0">
                                <input type="checkbox" name="floatingGifterGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("floatingGifterGraphics.0.is_countdown_required", $matchingFloatingGifterGraphic['is_countdown_required'] ?? false) ? ' checked' : '' }}>
                            </div>
                            <div class="color-input-container">
                                <div class="color-input-group">
                                    <label class="color-label titleFontColorLabel">Title Font Color</label>
                                    <input type="color" name="floatingGifterGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('floatingGifterGraphics.0.title_font_color', $matchingFloatingGifterGraphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamerFloatingGifter')">
                                    <p id="titleFontColorCodeStreamerFloatingGifter" class="additionalText1">Selected Color Code: <span>{{ old('floatingGifterGraphics.0.title_font_color', $matchingFloatingGifterGraphic['title_font_color'] ?? '#000000') }}</span></p>
                                </div>
                                <div class="color-input-group">
                                    <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                    <input type="color" name="floatingGifterGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('floatingGifterGraphics.0.countdown_font_color', $matchingFloatingGifterGraphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeStreamerFloatingGifter')">
                                    <p id="countdownFontColorCodeStreamerFloatingGifter" class="additionalText1">Selected Color Code: <span>{{ old('floatingGifterGraphics.0.countdown_font_color', $matchingFloatingGifterGraphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                </div>
                            </div>
                            <div style="display:flex; gap:3%;">
                            <label class="standout-label" for="template">Template <span style="color: red;">*</span></label>
                                <select name="floatingGifterGraphics[0][template]" required>
                                    <option value="">-- Select One --</option>
                                    <option value="brown_to_red" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'brown_to_red' ? ' selected' : '' }}>
                                        Brown to Red</option>
                                    <option value="purple_gradient" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'purple_gradient' ? ' selected' : '' }}>
                                        Purple Gradient</option>
                                    <option value="blue_gradient" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'blue_gradient' ? ' selected' : '' }}> Blue Gradient</option>
                                    
                                    <option value="purple_to_red" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'purple_to_red' ? ' selected' : '' }}>
                                        Purple to Red</option>
                                    <option value="dark_red" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'dark_red' ? ' selected' : '' }}>
                                        Dark Red</option>
                                    <option value="turquoise_to_pink" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'turquoise_to_pink' ? ' selected' : '' }}>
                                        Turquoise to Pink</option>
                                    <option value="light_blue_to_yellow" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'light_blue_to_yellow' ? ' selected' : '' }}>
                                        Light Blue to Yellow</option>
                                    <option value="orange_gradient" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'orange_gradient' ? ' selected' : '' }}> Orange Gradient</option>
                                    <option value="light_pink_to_yellow" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'light_pink_to_yellow' ? ' selected' : '' }}>Light Pink to Yellow</option>
                                    <option value="yellow_gradient" {{ old('floatingGifterGraphics.0.template', $matchingFloatingGifterGraphic['template'] ?? '') == 'yellow_gradient' ? ' selected' : '' }}>Yellow Gradient</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                @endif
            @endif

            @if($event->graphics)

                @php
                    $matchingFloatingStreamerGraphic = collect($event->graphics)->first(function ($graphic) {
                        return $graphic['type'] == 'player_floating_banner' && $graphic['targeted_audiences'] == 'streamers';
                    });
                @endphp

                @if($matchingFloatingStreamerGraphic)
                <!-- Floating Banner Streamer -->
                <div class="graphic-container">
                    <label class="standout-label" for="graphics">Floating Streamer Banner:</label>
                    <div id="streamerGraphicFieldsMain">
                        <div id="gifterGraphicFields">
                            <!-- Initial streamer graphic field -->
                            <div class="floatingStreamerGraphics">
                                @isset($matchingFloatingStreamerGraphic['asset_url'])
                                    <img id="streamerFloatingStreamerImagePreview" src="{{ asset($matchingFloatingStreamerGraphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                                @endisset
                                <input type="file" name="floatingStreamerGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerFloatingStreamerImagePreview')">
                                <input type="text" name="floatingStreamerGraphics[0][text]" placeholder="copies" value="{{ old("floatingStreamerGraphics.0.text", $matchingFloatingStreamerGraphic['text'] ?? '') }}">
                                <div class="side-by-side">
                                    <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                                    <input type="hidden" name="floatingStreamerGraphics[0][is_countdown_required]" value="0">
                                    <input type="checkbox" name="floatingStreamerGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("floatingStreamerGraphics.0.is_countdown_required", $matchingFloatingStreamerGraphic['is_countdown_required'] ?? false) ? ' checked' : '' }}>
                                </div>
                                <div class="color-input-container">
                                    <div class="color-input-group">
                                        <label class="color-label titleFontColorLabel">Title Font Color</label>
                                        <input type="color" name="floatingStreamerGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('floatingStreamerGraphics.0.title_font_color', $matchingFloatingStreamerGraphic['title_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'titleFontColorCodeStreamerFloatingStreamer')">
                                        <p id="titleFontColorCodeStreamerFloatingStreamer" class="additionalText1">Selected Color Code: <span>{{ old('floatingStreamerGraphics.0.title_font_color', $matchingFloatingStreamerGraphic['title_font_color'] ?? '#000000') }}</span></p>
                                    </div>
                                    <div class="color-input-group">
                                        <label class="color-label countdownFontColorLabel">Countdown Font Color</label>
                                        <input type="color" name="floatingStreamerGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" value="{{ old('floatingStreamerGraphics.0.countdown_font_color', $matchingFloatingStreamerGraphic['countdown_font_color'] ?? '#000000') }}" required onchange="updateColorCode(this, 'countdownFontColorCodeStreamerFloatingStreamer')">
                                        <p id="countdownFontColorCodeStreamerFloatingStreamer" class="additionalText1">Selected Color Code: <span>{{ old('floatingStreamerGraphics.0.countdown_font_color', $matchingFloatingStreamerGraphic['countdown_font_color'] ?? '#000000') }}</span></p>
                                    </div>
                                </div>
                                <div style="display:flex; gap: 3%;" >
                                <label class="standout-label" for="template">Template <span style="color: red;">*</span></label>
                                    <select name="floatingStreamerGraphics[0][template]" required>
                                        <option value="">-- Select One --</option>
                                        <option value="brown_to_red" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'brown_to_red' ? ' selected' : '' }}>
                                            Brown to Red</option>
                                        <option value="purple_gradient" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'purple_gradient' ? ' selected' : '' }}>
                                            Purple Gradient</option>
                                        <option value="blue_gradient" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'blue_gradient' ? ' selected' : '' }}> Blue Gradient</option>
                                        <option value="purple_to_red" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'purple_to_red' ? ' selected' : '' }}>
                                            Purple to Red</option>
                                        <option value="dark_red" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'dark_red' ? ' selected' : '' }}>
                                            Dark Red</option>
                                        <option value="turquoise_to_pink" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'turquoise_to_pink' ? ' selected' : '' }}>
                                            Turquoise to Pink</option>
                                        <option value="light_blue_to_yellow" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'light_blue_to_yellow' ? ' selected' : '' }}>
                                            Light Blue to Yellow</option>
                                        <option value="orange_gradient" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'orange_gradient' ? ' selected' : '' }}> Orange Gradient</option>
                                        <option value="light_pink_to_yellow" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'light_pink_to_yellow' ? ' selected' : '' }}>Light Pink to Yellow</option>
                                        <option value="yellow_gradient" {{ old('floatingStreamerGraphics.0.template', $matchingFloatingStreamerGraphic['template'] ?? '') == 'yellow_gradient' ? ' selected' : '' }}>Yellow Gradient</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif

            <!-- Coin Graphics Banner -->
            <div class="graphic-container">
                <label class="standout-label" for="graphics">Coin Graphics Banner:</label>
                <div id="coinGraphicFields">
                    @if($event->graphics)
                            @php
                            $firstMatchingCoinGraphic = collect($event->graphics)->first(function ($graphic) {
                                return $graphic['type'] == 'coins_contest_banner' && $graphic['targeted_audiences'] == 'all';
                            });
                        @endphp
                        @if($firstMatchingCoinGraphic)
                        <!-- Initial streamer graphic field -->
                        <div class="coinGraphic">
                            @isset($firstMatchingCoinGraphic['asset_url'])
                                <img id="coinImagePreview" src="{{ asset($firstMatchingCoinGraphic['asset_url']) }}" alt="Existing Graphic Image" style="max-width: 200px; max-height: 200px;">
                            @endisset
                            <input type="file" name="coinGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'coinImagePreview')">
                            <div class="side-by-side">
                                <input type="hidden" name="coinGraphics[0][is_countdown_required]" value="0">
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>

    <div style="display:flex; gap:5%;">
        <button style="background-color: #00f; color:#fff; cursor: pointer; padding:10px 50px; border-radius: 20px; " type="submit" >Update Contest</button>
        <button style="background-color: #fff; color:#00f; cursor: pointer; padding:10px 50px; border-radius: 20px; border: 2px solid #00f;" href="{{ route('contests.post') }}" style="align-items: center">Cancel  </button>
    </div>

    
    <script>
        /**
         * Updates the color code display based on the input value.
         * @param {HTMLInputElement} input - The input element representing the color.
         * @param {string} codeContainerId - The ID of the container element to display the color code.
         */
        function updateColorCode(input, codeContainerId) {
            var colorCodeContainer = document.getElementById(codeContainerId);
            var colorCodeSpan = colorCodeContainer.querySelector('span');

            // Set the color of the span based on the input value
            colorCodeSpan.textContent = input.value;
            colorCodeSpan.style.color = input.value;
        }

        /**
         * Toggles the display of text elements based on the state of corresponding checkboxes.
         */
        function toggleTextDisplay() {
            var checkBoxes = document.querySelectorAll(".myCheck");
            var texts = document.querySelectorAll(".titleFontColorLabel");
            var anotherTexts = document.querySelectorAll(".countdownFontColorLabel");
            var additionalTexts1 = document.querySelectorAll(".additionalText1");
            var additionalTexts2 = document.querySelectorAll(".additionalText2");

            checkBoxes.forEach(function(checkBox, index) {
                var text = texts[index];
                var anotherText = anotherTexts[index];
                var additionalText1 = additionalTexts1[index];
                var additionalText2 = additionalTexts2[index];

                if (checkBox.checked) {
                    text.style.display = "block";
                    anotherText.style.display = "block"; // Display another text for checked checkbox
                    additionalText1.style.display = "block"; // Display additional text 1 for checked checkbox
                    additionalText2.style.display = "block"; 

                } else {
                    text.style.display = "none";
                    anotherText.style.display = "none"; // Hide another text for unchecked checkbox
                    additionalText1.style.display = "none"; // Hide additional text 1 for unchecked checkbox
                    additionalText2.style.display = "none";

                }
            });
        }

        /**
         * Displays a live preview of the selected image.
         *
         * @param {HTMLInputElement} input - The file input element.
         * @param {string} previewId - The ID of the image element where the preview will be displayed.
         */
        function previewImage(input, previewId) {
                var preview = document.getElementById(previewId);
                var file = input.files[0];
                var reader = new FileReader();

                reader.onloadend = function () {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }

                if (file) {
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                }
        }

        /**
         * Validates contest start and end dates, and display start and end dates.
         * Displays a warning message and clears the end date field if validation fails.
         */
        $(document).ready(function () {
                var warningShown = false; // Flag to track if the warning has been shown

                // Function to check if end date is before start date
                function validateDates() {
                    var startDate = new Date($('[name="contest_start_at"]').val());
                    var endDate = new Date($('[name="contest_end_at"]').val());
                    var startDisplayDate = new Date($('[name="contest_display_start_at"]').val());
                    var endDisplayDate = new Date($('[name="contest_display_end_at"]').val());


                    if (endDate < startDate) {
                        warningMessage = "End date cannot be before the start date. Please choose a valid date.";
                        $('[name="contest_end_at"]').val(""); // Clear the end date field
                    } else if (endDisplayDate < startDisplayDate) {
                        warningMessage = "Display End date cannot be before the Display Start date. Please choose a valid date.";
                        $('[name="contest_display_end_at"]').val(""); // Clear the end date field
                    }

                    if (warningMessage && !warningShown) {
                        alert(warningMessage);
                        warningShown = true; // Set the flag to true
                    } else {
                        warningShown = false; // Reset the flag if dates are valid
                    }
                }

            // Attach the functions to the change event of the start, end date, and title inputs
            $('[name="contest_start_at"], [name="contest_end_at"]').change(validateDates);
            $('[name="contest_display_start_at"], [name="contest_display_end_at"]').change(validateDates);
        });

        /**
         * Toggles the visibility of the Tier 3 banner based on the selected tier.
         */
        document.addEventListener("DOMContentLoaded", function () {
                var tierSystemSelect = document.getElementById("tier_system");
                var tier3Banner = document.querySelector(".streamerGraphicTier3");

                // Function to toggle visibility of Tier 3 banner
                function toggleTier3Banner() {
                    var selectedTier = parseInt(tierSystemSelect.value);

                    // Show Tier 3 banner if the selected tier is 3 or 4, hide otherwise
                    tier3Banner.style.display = (selectedTier === 1 || selectedTier === 2) ? "block" : "none";
                }

                // Initial toggle when the page loads
                toggleTier3Banner();

                // Attach the function to the change event of the tier_system dropdown
                tierSystemSelect.addEventListener("change", toggleTier3Banner);
        });

        $(document).ready(function () {
            // Initialize Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Function to update tier sub-attributes based on the selected ID
            function updateTierAttributes() {
                var selectedId = $("#tier_system").val();
                var tierAttributesContainer = $("#tierAttributesContainer");

                // Clear previous content
                tierAttributesContainer.empty();

                // Find the selected tier configuration
                var selectedTierConfig = {!! json_encode(Config::get('contest_tier.tier_system')) !!}
                    .find(function (tier) {
                        return tier.id == selectedId;
                    });

                // Display sub-attributes if the selected tier configuration exists
                if (selectedTierConfig && selectedTierConfig.tiers) {
                    // Open Bootstrap modal
                    $('#tierAttributesModal').modal('show');

                    // Append sub-attributes to the modal body
                    var modalBody = $('#tierAttributesModal .modal-body');
                    modalBody.empty();
                    modalBody.append("<h3>Tier Attributes</h3>");
                    selectedTierConfig.tiers.forEach(function (tier) {
                        modalBody.append(
                            "Tier: " + tier.tier + "<br>" +
                            "Amount Min: " + tier.amount_min +
                            "<p>Amount Max: " + tier.amount_max + "</p>"
                        );
                    });
                }
            }

            // Update tier sub-attributes on select change
            $("#tier_system").change(function () {
                updateTierAttributes();
            });
        });

        flatpickr(".datetime-input", {
            enableTime: true, // Enable time selection
            dateFormat: "Y-m-d H:i", // Format for date and time
            time_24hr: true, // 24-hour time format
        });

        // Scripts for adding and deleting gift fields dynamically
        let currentGiftIndex = 0;
        
        function addGiftField() {
            const giftTemplate = document.querySelector('.gift');
            const giftField = giftTemplate.cloneNode(true);
            const newIndex = currentGiftIndex + 1;
            giftField.innerHTML = giftField.innerHTML.replace(/gifts_bounded\[0\]/g, `gifts_bounded[${newIndex}]`); // Update placeholders
        
            if (newIndex > 0) {
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'deleteGift delete-button-blue'; // Add the class here
                deleteButton.textContent = 'Delete';
                giftField.appendChild(deleteButton);
            }
        
            document.getElementById('giftFields').appendChild(giftField);
            currentGiftIndex++;
        }
        function deleteGiftField(event) {
            if (event.target.classList.contains('deleteGift') && currentGiftIndex > 0) {
                event.target.closest('.gift').remove();
                currentGiftIndex--;
            }
        }
        document.getElementById('addGift').addEventListener('click', addGiftField);
        document.getElementById('giftFields').addEventListener('click', deleteGiftField);
        

        //Real-time validation scripts for date and color inputs
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.isCountdownRequiredCheckbox');

            checkboxes.forEach(function (checkbox) {

                const parentContainer = checkbox.parentElement.parentElement;
                const titleFontColor = checkbox.parentElement.parentElement.querySelector('.titleFontColor');
                const countdownFontColor = checkbox.parentElement.parentElement.querySelector('.countdownFontColor');
                
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        titleFontColor.style.display = 'block';
                        countdownFontColor.style.display = 'block';

                        // Show the color input for titleFontColor
                        titleFontColor.type = "color";
                        titleFontColor.value = "#000000";

                        // Show the color input for countdownFontColor
                        countdownFontColor.type = "color";
                        countdownFontColor.value = "#000000";

                        // Real-time validation for titleFontColor
                        titleFontColor.addEventListener('input', function () {
                            validateColorInput(titleFontColor);
                        });

                        // Real-time validation for countdownFontColor
                        countdownFontColor.addEventListener('input', function () {
                            validateColorInput(countdownFontColor);
                        });
                    } else {
                        titleFontColor.style.display = 'none';
                        countdownFontColor.style.display = 'none';
                        titleFontColor.type = "text";
                        countdownFontColor.type = "text";
                        titleFontColor.value = '#000000'; // Set the default value to #000000
                        countdownFontColor.value = '#000000'; // Set the default value to #000000
                    }
                });

                // Initialize the visibility based on the initial checkbox state
                if (!checkbox.checked) {
                    titleFontColor.style.display = 'none';
                    countdownFontColor.style.display = 'none';
                    titleFontColor.type = "text";
                    countdownFontColor.type = "text";
                    titleFontColor.value = '#000000'; // Set the default value to #000000
                    countdownFontColor.value = '#000000'; // Set the default value to #000000
                }
            });

            // Function to validate color input
            function validateColorInput(input) {
                const isValidColor = /^#[0-9A-Fa-f]{6}$/.test(input.value);

                if (!isValidColor) {
                    input.setCustomValidity('Invalid color code. Please use the format #RRGGBB');
                } else {
                    input.setCustomValidity('');
                }
            }
        });

    </script>
</form>
</body>
</html>

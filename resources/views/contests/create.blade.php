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



    
    <title>Create Contest</title>

    <style>
        body {
            
            background: linear-gradient(135deg, #e7dada, #150505); /* Dark background color */
            color: #fff; /* Text color */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-family: Georgia, 'Times New Roman', Times, serif;
            border-radius: 8px; /* Rounded corners */

        }

        body a {
            justify-content: center;
            color: rgb(255, 255, 255); /* Link color */ 
            border-radius: 8px; /* Rounded corners */
        }

        h1 {
            text-align: center;
            color: #fff; /* Header text color */
            font-family: Georgia, 'Times New Roman', Times, serif;
            text-shadow: 2px 2px;
        }

        form {
            max-width: 55%;
            margin: 20px auto;
            padding: 20px;
            background: linear-gradient(135deg,rgb(78, 19, 19), rgb(31, 2, 2) ); /* Form background color */
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
            background-color: #555; /* Input background color */
            color: #fff; /* Input text color */
            border: none;
            border-radius: 4px;
        }

        .delete-button-blue {
            background-color: #00f;
        }

        input{
            width: 95%;
            padding: 1px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border-radius: 10px;

        }

        .datetime-input {
            width: 170%;
        }

        .alert {
            background-color: #f00; /* Alert background color */
            color: #fff; /* Alert text color */
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* Add your additional styles for graphics containers, side-by-side elements, etc. */
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
            color: #ffffff; /* Change the color to make labels stand out */
            font-weight: bold; /* Add bold font weight for emphasis */
            font-size: 1.3em; /* Adjust font size for emphasis */
        }

        .isCountdownRequiredCheckbox:checked + .text {
            display: block;
        }

        .isCountdownRequiredCheckbox:not(:checked) + .text {
            display: none;
        }

        .color-input-container {
            display: flex;
            justify-content:space-evenly;
        }

        .color-input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
        }

        .color-label {
            margin-bottom: 5px; /* Adjust as needed */
        }
</style>


</head>

<body>
     <!-- Page title -->
    <h1>Create Contest</h1>
    
    <!-- Contest form -->
    <form method="post" action="{{ route('contests.store') }}" enctype="multipart/form-data">

        <!-- Contest view link -->
        <a href="{{ route('contests.post') }}" style="color: #008080; text-decoration: underline; font-weight: bold;">View Contest</a>

        <!-- CSRF Token -->
        @csrf
        @method('post')

        <!-- Display validation errors -->
         @if ($errors->any())
            <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            </div>
        @endif
        <br><br>

        <!-- Select Tier System -->
        <div>
            <div style="display:flex; gap:10px;" >
                <label class="standout-label" for="tier_system">Select Tier System:</label>
                <select name="tier_system" id="tier_system" required>
                    <!-- Populate options dynamically based on the tiers in contest_tier.php -->
                    @foreach(Config::get('contest_tier.tier_system') as $tier)
                        <option value="{{ (int)$tier['id'] }}" data-toggle="tooltip" title="{{ json_encode($tier['tiers']) }}">{{ (int)$tier['id'] }}</option>
                    @endforeach
                </select>
            </div>
            <div id="tierAttributesContainer"></div>
        <div>
    
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
            <label class="standout-label" for="title">Title <span style="color: red;">*</span></label>
            <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="title">
        </div>

        <!-- Start and End Dates -->
        <div style="display:flex ; gap: 30%;">
            <div>
                <label class="standout-label" for="contest_start_at">Start <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_start_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_start_at') }}" required>
            </div>
            <div>
                <label class="standout-label" for="contest_end_at"> End <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_end_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_end_at') }}" required>
            </div>
        </div>
        
        <!-- Display Start and End Dates -->
        <div style="display:flex ; gap: 30%;">
            <div>
                <label class="standout-label" for="contest_display_start_at"> Display Start <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_display_start_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_display_start_at') }}" required>
            </div>
            
            <div>
                <label class="standout-label" for="contest_display_end_at"> Display End <span style="color: red;">*</span></label>
                <input class="datetime-input" type="text" name="contest_display_end_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('contest_display_end_at') }}" required>
            </div>
        </div>
    
        <!-- Contest Type -->
        <div style="display:flex; gap:5%;">
            <label class="standout-label" for="contest_type"> Type:</label>
            <select name="contest_type" required>
                <option value="single" {{ old('contest_type') === 'single' ? 'selected' : '' }}>Single</option>
                <option value="multi" {{ old('contest_type') === 'multi' ? 'selected' : '' }}>Multi</option>
            </select>
        </div>

        <!-- Gift Section -->
        <div>
            <label class="standout-label" for="gifts_bounded">Gift:</label>
            <button type="button" style="background-color: #00f; color:#fff; cursor: pointer;" id="addGift">Add Gift</button>
            <div id="giftFields">
                <!-- Template for gift fields -->
                <div class="gift">
                    <div style="display:flex ; gap: 13%; width: 97%">
                        <input type="text" name="gifts_bounded[0][id]" placeholder="Gift ID" value="{{ old('gifts_bounded.0.id') }}">
                        <input type="text" name="gifts_bounded[0][pricing_id]" placeholder="Pricing ID" value="{{ old('gifts_bounded.0.pricing_id') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Gifter Graphics Main Banner -->
        <div class="graphic-container">
            <label class="standout-label" for="graphics">Gifter Banner:</label>
            <label type="button" id="addGifterGraphic"></label>
            <div id="gifterGraphicFields">
                <!-- Initial gifter graphic field -->
                <div class="gifterGraphic">

                    <input type="file" name="gifterGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'gifterImagePreview')">
                    <!-- Image preview -->
                    <img id="gifterImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    <div class="side-by-side">
                        <!-- Checkbox for countdown requirement -->
                        <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                        <input type="hidden" name="gifterGraphics[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="gifterGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("gifterGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div class="color-input-container">
                        <div class="color-input-group">
                            <!-- Title font color input -->
                            <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                            <input type="text" name="gifterGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamerGifter')">
                            <p id="titleFontColorCodeStreamerGifter" class="additionalText1" style="display:none">Selected Color Code: <span id="selectedColorCode"></span></p>
                        </div>
                        <div class="color-input-group">
                            <!-- Countdown font color input -->
                            <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                            <input type="text" name="gifterGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamerGifter')">
                            <p id="countdownFontColorCodeStreamerGifter" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  

        <!-- Streamer Graphics Main Banner -->
        <div class="graphic-container">
            <label class="standout-label" for="graphics"> Streamer</label>
            <label class="standout-label" for="graphics"> Main:</label>
            <div id="streamerGraphicFieldsMain">
                <!-- Initial streamer graphic field -->
                <div class="streamerGraphic">
                    <input type="file" name="streamerGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerImagePreview')">
                    <img id="streamerImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphics[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div class="color-input-container">
                        <div class="color-input-group">
                            <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                            <input type="text" name="streamerGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamer')">
                            <p id="titleFontColorCodeStreamer" class="additionalText1" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                        <div class="color-input-group">
                            <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                            <input type="text" name="streamerGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamer')">
                            <p id="countdownFontColorCodeStreamer" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Streamer Graphics Tier 1 Banner -->
        <div class="graphic-containerr">
            <label class="standout-label" for="graphics">Tier 1:</label>
            <div id="streamerGraphicFieldsTier1">
                <div class="streamerGraphicTier1">
                    <input type="file" name="streamerGraphicsTier1[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerTier1ImagePreview')">
                    <img id="streamerTier1ImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphicsTier1[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphicsTier1[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphicsTier1[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div class="color-input-container">
                        <div class="color-input-group">
                            <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                            <input type="text" name="streamerGraphicsTier1[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamerTier1')">
                            <p id="titleFontColorCodeStreamerTier1" class="additionalText1" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                        <div class="color-input-group">
                            <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                            <input type="text" name="streamerGraphicsTier1[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamerTier1')">
                            <p id="countdownFontColorCodeStreamerTier1" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Streamer Graphics Tier 2 Banner -->
        <div class="graphic-containerr">
            <label class="standout-label" for="graphics">Tier 2:</label>
            <div id="streamerGraphicFieldsTier2">
                <div class="streamerGraphicTier2">
                    <input type="file" name="streamerGraphicsTier2[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerTier2ImagePreview')">
                    <img id="streamerTier2ImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphicsTier2[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphicsTier2[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphicsTier2[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div class="color-input-container">
                        <div class="color-input-group">
                            <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                            <input type="text" name="streamerGraphicsTier2[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamerTier2')">
                            <p id="titleFontColorCodeStreamerTier2" class="additionalText1" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                        <div class="color-input-group">
                            <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                            <input type="text" name="streamerGraphicsTier2[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamerTier2')">
                            <p id="countdownFontColorCodeStreamerTier2" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Streamer Graphics Tier 3 Banner -->
        <div class="graphic-containerr">
            <label type="button" id="addStreamerGraphic"></label>
            <div id="streamerGraphicFields">
                <div class="streamerGraphicTier3">
                    <label class="standout-label" for="graphics">Tier 3:</label>
                    <input type="file" name="streamerGraphicsTier3[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerTier3ImagePreview')">
                    <img id="streamerTier3ImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphicsTier3[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphicsTier3[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("streamerGraphicsTier3[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div class="color-input-container">
                        <div class="color-input-group">
                            <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                            <input type="text" name="streamerGraphicsTier3[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamerTier3')">
                            <p id="titleFontColorCodeStreamerTier3" class="additionalText1" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                        <div class="color-input-group">
                            <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                            <input type="text" name="streamerGraphicsTier3[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamerTier3')">
                            <p id="countdownFontColorCodeStreamerTier3" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        
        <!-- Floating Banner Gifter -->
        <div class="graphic-container">
            <label class="standout-label" for="graphics">Floating Gifter Banner:</label>
            <div id="gifterGraphicFields">
                    <div class="floatingGifterGraphics">
                        <input type="file" name="floatingGifterGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerFloatingGifterImagePreview')">
                        <img id="streamerFloatingGifterImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                        <input type="text" name="floatingGifterGraphics[0][text]" placeholder="copies">
                        <div class="side-by-side">
                            <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                            <input type="hidden" name="floatingGifterGraphics[0][is_countdown_required]" value="0">
                            <input type="checkbox" name="floatingGifterGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("floatingGifterGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                        </div>
                        <div class="color-input-container">
                            <div class="color-input-group">
                                <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                                <input type="text" name="floatingGifterGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamerFloatingGifter')">
                                <p id="titleFontColorCodeStreamerFloatingGifter" class="additionalText1" style="display:none">Selected Color Code: <span></span></p>
                            </div>
                            <div class="color-input-group">
                                <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                                <input type="text" name="floatingGifterGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamerFloatingGifter')">
                                <p id="countdownFontColorCodeStreamerFloatingGifter" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex ; gap: 2%;"> 
                        <label class="standout-label" for="template">Template <span style="color: red;">*</span></label>
                        <select name="floatingGifterGraphics[0][template]" required>
                            <option value="">-- Select One --</option>
                            <option value="brown_to_red">Brown to Red</option>
                            <option value="purple_gradient">Purple Gradient</option>
                            <option value="blue_gradient">Blue Gradient</option>
                            <option value="purple_to_red">Purple to Red</option>
                            <option value="dark_red">Dark Red</option>
                            <option value="turquoise_to_pink">Turquoise to Pink</option>
                            <option value="light_blue_to_yellow">Light Blue to Yellow</option>
                            <option value="orange_gradient">Orange Gradient</option>
                            <option value="light_pink_to_yellow">Light Pink to Yellow</option>
                            <option value="yellow_gradient">Yellow Gradient</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Banner Streamer -->
        <div class="graphic-container">
            <label class="standout-label" for="graphics">Floating Streamer Banner:</label>
            <div id="streamerGraphicFields">
                    <div class="floatingStreamerGraphics">
                        <input type="file" name="floatingStreamerGraphics[0][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerFloatingStreamerImagePreview')">
                        <img id="streamerFloatingStreamerImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
                        <input type="text" name="floatingStreamerGraphics[0][text]" placeholder="copies">
                        <div class="side-by-side">
                            <label for="is_countdown_required1" style="width: 35%;" >Is Countdown Required:</label>
                            <input type="hidden" name="floatingStreamerGraphics[0][is_countdown_required]" value="0">
                            <input type="checkbox" name="floatingStreamerGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox myCheck" onclick="toggleTextDisplay()" value="1"{{ old("floatingStreamerGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                        </div>
                        <div class="color-input-container">
                            <div class="color-input-group">
                                <label class="color-label titleFontColorLabel" style="display:none">Title Font Color</label>
                                <input type="text" name="floatingStreamerGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'titleFontColorCodeStreamerFloatingStreamer')">
                                <p id="titleFontColorCodeStreamerFloatingStreamer" class="additionalText1" style="display:none">Selected Color Code: <span></span></p>
                            </div>
                            <div class="color-input-group">
                                <label class="color-label countdownFontColorLabel" style="display:none">Countdown Font Color</label>
                                <input type="text" name="floatingStreamerGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required oninput="updateColorCode(this, 'countdownFontColorCodeStreamerFloatingStreamer')">
                                <p id="countdownFontColorCodeStreamerFloatingStreamer" class="additionalText2" style="display:none">Selected Color Code: <span></span></p>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex ; gap: 2%;">
                        <label class="standout-label" for="template">Template <span style="color: red;">*</span></label>
                        <select name="floatingStreamerGraphics[0][template]" required>
                            <option value="">-- Select One --</option>
                            <option value="brown_to_red">Brown to Red</option>
                            <option value="purple_gradient">Purple Gradient</option>
                            <option value="blue_gradient">Blue Gradient</option>
                            <option value="purple_to_red">Purple to Red</option>
                            <option value="dark_red">Dark Red</option>
                            <option value="turquoise_to_pink">Turquoise to Pink</option>
                            <option value="light_blue_to_yellow">Light Blue to Yellow</option>
                            <option value="orange_gradient">Orange Gradient</option>
                            <option value="light_pink_to_yellow">Light Pink to Yellow</option>
                            <option value="yellow_gradient">Yellow Gradient</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coin Graphics Banner -->
        <div class="graphic-container">
            <label class="standout-label" for="graphics">Coin Banner:</label>
            <div id="coinGraphicFields">
                <div class="coinGraphic">
                    <input type="file" name="coinGraphics[INDEX][asset_url]" accept="image/*" onchange="previewImage(this, 'streamerCoinImagePreview')">
                    <img id="streamerCoinImagePreview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">

                    <div class="image-preview-container">
                        <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;" onchange="previewImage(this)">
                    </div>
                        <div class="side-by-side">
                            <input type="hidden" name="coinGraphics[INDEX][is_countdown_required]" value="0">
                        </div>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:5%;">
            <button style="background-color: #00f; color:#fff; cursor: pointer; padding:10px 50px; border-radius: 20px; " type="submit" > Submit</button>
            <button style="background-color: #fff; color:#00f; cursor: pointer; padding:10px 50px; border-radius: 20px; border: 2px solid #00f;" onclick="window.location.href='{{ route('contests.post') }}'">Cancel</button>
        </div>

    <script>

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

            document.getElementById('selectedColorCode').innerText = input.value;


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


        flatpickr(".datetime-input", {
            enableTime: true,        // Enable time selection
            dateFormat: "Y-m-d H:i", // Format for date and time
            time_24hr: true,         // 24-hour time format
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
        

        //Real-time validation scripts for color inputs
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
                            "Amount Min: " + tier.amount_min + " diamonds" +
                            "<p>Amount Max: " + tier.amount_max + " diamonds" +"</p>"
                        );
                    });
                }
            }
            // Update tier sub-attributes on select change
            $("#tier_system").change(function () {
                updateTierAttributes();
            });
        });

    </script>        
    </form>
</body>
</html>


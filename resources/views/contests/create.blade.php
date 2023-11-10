<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>



    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <title>Create New Contest</title>

    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #1e1e1e; /* Dark background color */
        color: #ddd; /* Light text color */
    }

    h1 {
        color: #bbb; /* Light heading color */
    }

    form {
        max-width: 500px;
        margin: 20px auto;
        background-color: #333; /* Dark form background color */
        padding: 20px;
        border-radius: 8px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: #ddd;
    }

    select,
    button{
        margin-bottom: 15px;
        padding: 8px;
        width: 50%;
        box-sizing: border-box;
        background-color: #444; /* Dark input background color */
        color: #ddd; /* Light input text color */
        border: 1px solid #555; /* Input border color */
        border-radius: 4px;
    }

    input{
        margin-bottom: 15px;
        padding: 1px;
        width: 50%;
    }

    .datetime-input {
        width: calc(100% - 18px);
    }

    .graphic-container {
        margin-bottom: 20px;
    }

    .side-by-side {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .titleFontColor,
    .countdownFontColor {
        margin-top: 5px;
        width: 48%;
        display: inline-block;
        box-sizing: border-box;
    }

    button[type="submit"] {
        background-color: #4caf50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }

    .alert {
        background-color: #4f1f1f;
        color: #a94442;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ebccd1;
        border-radius: 4px;
    }
</style>


</head>

<body>
    <a href="{{ route('contests.post') }}">View Posts</a>
    <h1>Create New Contest</h1>
    <form method="post" action="{{ route('contests.store') }}" enctype="multipart/form-data">
        @csrf
        @method('post')

                @if ($errors->any())
            <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            </div>
        @endif

        <!-- Form fields for contest attributes -->
        <div>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required value="{{ old('title') }}">
        </div>

        <div>
            <label for="contest_start_at">Contest Start:</label>
            <input class="datetime-input" type="text" name="contest_start_at" value="{{ old('contest_start_at') }}" required>
        </div>
        
        <div>
            <label for="contest_end_at">Contest End:</label>
            <input class="datetime-input" type="text" name="contest_end_at" value="{{ old('contest_end_at') }}" required>
        </div>
        
        <div>
            <label for="contest_display_start_at">Contest Display Start:</label>
            <input class="datetime-input" type="text" name="contest_display_start_at" value="{{ old('contest_display_start_at') }}" required>
        </div>
        
        <div>
            <label for="contest_display_end_at">Contest Display End:</label>
            <input class="datetime-input" type="text" name="contest_display_end_at" value="{{ old('contest_display_end_at') }}" required>
        </div>
        

        <div>
            <label for="contest_type">Contest Type:</label>
            <select name="contest_type" required>
                <option value="single" {{ old('contest_type') === 'single' ? 'selected' : '' }}>Single</option>
                <option value="multi" {{ old('contest_type') === 'multi' ? 'selected' : '' }}>Multi</option>
            </select>
        </div>

        <div>
            <label for="gifts_bounded">Gift:</label>
            <button type="button" id="addGift">Add Gift</button>
            <div id="giftFields">
                <!-- Template for gift fields -->
                <div class="gift">
                    <input type="text" name="gifts_bounded[0][id]" placeholder="Gift ID" value="{{ old('gifts_bounded.0.id') }}">
                    <input type="text" name="gifts_bounded[0][pricing_id]" placeholder="Pricing ID" value="{{ old('gifts_bounded.0.pricing_id') }}">
                </div>
            </div>
        </div>

        <!-- Gifter Graphics Main Banner -->
        <div class="graphic-container">
            <label for="graphics">Gifter Graphics:</label>
            <button type="button" id="addGifterGraphic">Add Graphic for Gifters</button>
            <div id="gifterGraphicFields">
                <!-- Initial gifter graphic field -->
                <div class="gifterGraphic">
                    <input type="file" name="gifterGraphics[0][asset_url]" accept="image/*">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                        <input type="hidden" name="gifterGraphics[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="gifterGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("gifterGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div>
                        <input type="text" name="gifterGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                        <input type="text" name="gifterGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>

                    </div>
                </div>
            </div>
        </div>
                
        <!-- Streamer Graphics Main Banner -->
        <div class="graphic-container">
            <label for="graphics">Streamer Graphics Main Banner:</label>
            <div id="streamerGraphicFieldsMain">
                <!-- Initial streamer graphic field -->
                <div class="streamerGraphic">
                    <input type="file" name="streamerGraphics[0][asset_url]" accept="image/*">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphics[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("streamerGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div>
                        <input type="text" name="streamerGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                        <input type="text" name="streamerGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Streamer Graphics Tier 1 Banner -->
        <div class="graphic-container side-by-side-container">
            <label for="graphics">Streamer Graphics Tier 1 Banner:</label>
            <div id="streamerGraphicFieldsTier1">
                <!-- Initial streamer graphic field -->
                <div class="streamerGraphicTier1">
                    <input type="file" name="streamerGraphicsTier1[0][asset_url]" accept="image/*">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphicsTier1[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphicsTier1[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("streamerGraphicsTier1[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div>
                        <input type="text" name="streamerGraphicsTier1[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                        <input type="text" name="streamerGraphicsTier1[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Streamer Graphics Tier 2 Banner -->
        <div class="graphic-container side-by-side-container">
            <label for="graphics">Streamer Graphics Tier 2 Banner:</label>
            <button type="button" id="addStreamerGraphic">Add Graphic for Streamers</button>
            <div id="streamerGraphicFields">
                <!-- Initial streamer graphic field -->
                <div class="streamerGraphicTier2">
                    <input type="file" name="streamerGraphicsTier2[0][asset_url]" accept="image/*">
                    <div class="side-by-side">
                        <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                        <input type="hidden" name="streamerGraphicsTier2[0][is_countdown_required]" value="0">
                        <input type="checkbox" name="streamerGraphicsTier2[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("streamerGraphicsTier2[0][is_countdown_required]") ? ' checked' : '' }}>
                    </div>
                    <div>
                        <input type="text" name="streamerGraphicsTier2[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                        <input type="text" name="streamerGraphicsTier2[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Floating Banner Gifter -->
        <div class="graphic-container">
            <label for="graphics">Floating Graphics Gifter Banner:</label>
            <div id="gifterGraphicFields">
                <!-- Initial streamer graphic field -->
                    <div class="floatingGifterGraphics">
                        <input type="file" name="floatingGifterGraphics[0][asset_url]" accept="image/*">
                        <input type="text" name="floatingGifterGraphics[0][text]" placeholder="text">
                        <div class="side-by-side">
                            <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                            <input type="hidden" name="floatingGifterGraphics[0][is_countdown_required]" value="0">
                            <input type="checkbox" name="floatingGifterGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("floatingGifterGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                        </div>
                        <div>
                            <input type="text" name="floatingGifterGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                            <input type="text" name="floatingGifterGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                        </div>
                    </div>
                    <label for="template">Select Template:</label>
                    <select name="floatingGifterGraphics[0][template]" required>
                        <option value="">Select One</option>
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

        <!-- Floating Banner Streamer -->
        <div class="graphic-container">
            <label for="graphics">Floating Graphics Streamer Banner:</label>
            <div id="streamerGraphicFields">
                <!-- Initial streamer graphic field -->
                    <div class="floatingStreamerGraphics">
                        <input type="file" name="floatingStreamerGraphics[0][asset_url]" accept="image/*">
                        <input type="text" name="floatingStreamerGraphics[0][text]" placeholder="text">
                        <div class="side-by-side">
                            <label for="is_countdown_required1" style="width: 50%;" >Is Countdown Required:</label>
                            <input type="hidden" name="floatingStreamerGraphics[0][is_countdown_required]" value="0">
                            <input type="checkbox" name="floatingStreamerGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("floatingStreamerGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                        </div>
                        <div>
                            <input type="text" name="floatingStreamerGraphics[0][title_font_color]" class="titleFontColor" placeholder="Title Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                            <input type="text" name="floatingStreamerGraphics[0][countdown_font_color]" class="countdownFontColor" placeholder="Countdown Font Color (e.g., #RRGGBB)" pattern="^#[0-9A-Fa-f]{6}$" required>
                        </div>
                    </div>
                    <label for="template">Select Template:</label>
                    <select name="floatingStreamerGraphics[0][template]" required>
                        <option value="">Select One</option>
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

        <!-- Coin Graphics Banner -->
        <div class="graphic-container">
            <label for="graphics">Coin Graphics Banner:</label>
            <button type="button" id="addCoinGraphic">Add Graphic for Coin</button>
            <div id="coinGraphicFields">
                <!-- Initial streamer graphic field -->
                <div class="coinGraphic">
                    <input type="file" name="coinGraphics[0][asset_url]" accept="image/*">
                    <input type="text" name="coinGraphics[0][text]" placeholder="text">
                        <div class="side-by-side">
                            <label for="is_countdown_required1">Is Countdown Required:</label>
                            <input type="hidden" name="coinGraphics[0][is_countdown_required]" value="0">
                            <input type="checkbox" name="coinGraphics[0][is_countdown_required]" class="isCountdownRequiredCheckbox" value="1"{{ old("coinGraphics[0][is_countdown_required]") ? ' checked' : '' }}>
                        </div>
                </div>
            </div>
        </div>

        <button type="submit">Submit</button>

    <script>
        flatpickr(".datetime-input", {
            enableTime: true, // Enable time selection
            dateFormat: "Y-m-d H:i", // Format for date and time
            time_24hr: true, // 24-hour time format
        });
    
        // GIFT
        let currentGiftIndex = 0;
        
        function addGiftField() {
            const giftTemplate = document.querySelector('.gift');
            const giftField = giftTemplate.cloneNode(true);
            const newIndex = currentGiftIndex + 1;
            giftField.innerHTML = giftField.innerHTML.replace(/gifts_bounded\[0\]/g, `gifts_bounded[${newIndex}]`); // Update placeholders
        
            if (newIndex > 0) {
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'deleteGift';
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
        
        //GRAPHIC
        let currentGifterGraphicIndex = 0;
        let currentStreamerGraphicIndex = 0;
        //let currentCoinGraphicIndex = 0;

        function addGifterGraphicField() {
            const gifterGraphicTemplate = document.querySelector('.gifterGraphic');
            const gifterGraphicField = gifterGraphicTemplate.cloneNode(true);
            const newIndex = currentGifterGraphicIndex + 1;

            // Update the INDEX placeholder in the cloned gifter graphic field
            gifterGraphicField.innerHTML = gifterGraphicField.innerHTML.replace(/gifterGraphics\[0\]/g, `gifterGraphics[${newIndex}]`);

            if (newIndex > 0) {
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'deleteGifterGraphic';
                deleteButton.textContent = 'Delete';
                gifterGraphicField.appendChild(deleteButton);
            }

            document.getElementById('gifterGraphicFields').appendChild(gifterGraphicField);
            currentGifterGraphicIndex++;
        }

        function deleteGifterGraphicField(event) {
            if (event.target.classList.contains('deleteGifterGraphic') && currentGifterGraphicIndex > 0) {
                event.target.closest('.gifterGraphic').remove();
                currentGifterGraphicIndex--;
            }
        }

        function addStreamerGraphicField() {
            const streamerGraphicTemplate = document.querySelector('.streamerGraphic');
            const streamerGraphicField = streamerGraphicTemplate.cloneNode(true);
            const newIndex = currentStreamerGraphicIndex + 1;

            streamerGraphicField.innerHTML = streamerGraphicField.innerHTML.replace(/streamerGraphics\[0\]/g, `streamerGraphics[${newIndex}]`);

            if (newIndex > 0) {
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'deleteStreamerGraphic';
                deleteButton.textContent = 'Delete';
                streamerGraphicField.appendChild(deleteButton);
            }

            document.getElementById('streamerGraphicFields').appendChild(streamerGraphicField);
            currentStreamerGraphicIndex++;
        }

        function deleteStreamerGraphicField(event) {
            if (event.target.classList.contains('deleteStreamerGraphic') && currentStreamerGraphicIndex > 0) {
                event.target.closest('.streamerGraphic').remove();
                currentStreamerGraphicIndex--;
            }
        }

        /*
        function addCoinGraphicField() {
            const coinGraphicTemplate = document.querySelector('.coinGraphic');
            const coinGraphicField = coinGraphicTemplate.cloneNode(true);
            const newIndex = currentCoinGraphicIndex + 1;

            coinGraphicField.innerHTML = coinGraphicField.innerHTML.replace(/coinGraphics\[0\]/g, `coinGraphics[${newIndex}]`);

            if (newIndex > 0) {
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'deleteCoinGraphic';
                deleteButton.textContent = 'Delete';
                coinGraphicField.appendChild(deleteButton);
            }

            document.getElementById('coinGraphicFields').appendChild(coinGraphicField);
            currentCoinGraphicIndex++;
        }

        function deleteCoinGraphicField(event) {
            if (event.target.classList.contains('deleteCoinGraphic') && currentCoinGraphicIndex > 0) {
                event.target.closest('.coinGraphic').remove();
                currentCoinGraphicIndex--;
            }
        }

        */
        
        

        document.getElementById('addGifterGraphic').addEventListener('click', addGifterGraphicField);
        document.getElementById('gifterGraphicFields').addEventListener('click', deleteGifterGraphicField);

        document.getElementById('addStreamerGraphic').addEventListener('click', addStreamerGraphicField);
        document.getElementById('streamerGraphicFields').addEventListener('click', deleteStreamerGraphicField);

        //document.getElementById('addCoinGraphic').addEventListener('click', addCoinGraphicField);
        //document.getElementById('coinGraphicFields').addEventListener('click', deleteCoinGraphicField);

        // Add an event listener to the checkboxes
        document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.isCountdownRequiredCheckbox');

        checkboxes.forEach(function (checkbox) {
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


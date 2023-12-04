<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contest List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        html,
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg,#abdad8,#111111); /* Charcoal gray background */
            color: #fff; /* White text color */
            font-family: Georgia, 'Times New Roman', Times, serif
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #3498db; /* Blue title color */
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 100%; /* Adjusted width to 100% */
            background-color: #2c3e50; /* Dark blue-gray table background */
            color: #ecf0f1; /* Light gray text color */
        }

        table, th, td {
            border: 1px solid #34495e; /* Dark blue-gray borders */
        }

        th, td {
            text-align: left;
            padding: 8px;
            font-size: 14px; /* Adjusted font size */
        }

        th {
            background-color: #700000; /* Blue header background */
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #2c3e50; /* Dark blue-gray alternate row */
        }

        tr:nth-child(odd) {
            background-color: #34495e; /* Darker blue-gray alternate row */
        }

        a {
            text-decoration: none;
            color: #3498db; /* Blue link color */
        }

        a:hover {
            text-decoration: underline;
        }

        .btn {
            background-color: #27ae60; /* Green button */
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }


        .pagination {
            text-align: center;
            margin-top: 20px;
            color: #3498db; /* Blue pagination text color */
        }

        .pagination a {
            background-color: #3498db; /* Blue pagination link background */
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 5px; /* Add some space between pagination links */
        }

        .pagination span {
            background-color: #2980b9; /* Slightly darker blue active page */
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 5px; /* Add some space between pagination links */
        }

        
        .pop-out {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.9); /* Dark background color */
            color: #fff; /* White text color for contrast */
            border: 1px solid #fff; /* White border for contrast */
            z-index: 1000;
            width: 50%; /* Adjust the width as needed */
            max-height: 70vh; /* Set a maximum height with a viewport height unit for scrollability */
            overflow-y:scroll; /* Enable vertical scrolling if content exceeds the max height */
        }

        .pop-out-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        z-index: 999; /* Ensure the pop-out content is on top */
    }

        .close-button {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 999; /* Ensure the close button is on top */
        cursor: pointer;
        }

        .table-container {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .graphics-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;

    }

    .graphic-item {
        flex-basis: calc(55% - 50px); /* Adjust the width as needed */
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .graphic-item ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
        font-size: 14px; /* Adjust the font size as needed */
    }

    .graphic-item li {
        margin-bottom: 5px;
    }

    .gifts-container {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
        justify-content: space-between;
    }

    .gift-item {
        flex-basis: calc(40% - 10px); /* Adjust the width as needed */
        margin-right: 20px;
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .gift-info {
        margin-bottom: 5px;
    }

    .backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
        z-index: 99; /* Ensure the backdrop is above other elements */
    }

    .graphics-containers{
        vertical-align: text-bottom;
        text-align: center;
    }
        

        
    </style>
</head>
<body>
    <h1>Contest List</h1>

    <div>
        <a href="{{ route('contests.create') }}" class="btn">Create new contest</a>
    </div>

    <table>
        <thead>
            <tr>
                <th scope="col">#</th>
                
                <th scope="col">Title</th>
                <th scope="col">Contest Start</th>
                <th scope="col">Contest End</th>
                <th scope="col">Contest Display Start</th>
                <th scope="col">Contest Display End</th>
                <th scope="col">Edit</th>
                <th scope="col">Delete</th>
            </tr>
        </thead>
        
        <tbody>
            <div id="backdrop" class="backdrop" onclick="closeAllPopOuts()"></div>
            

            @foreach($events as $event)
                <tr>
                    <th scope="row">{{ ($events->currentPage() - 1) * $events->perPage() + $loop->iteration }}</th>
                    <td>
                        <!-- Add a click event to show the pop-out when the title is clicked -->
                        <span style="cursor: pointer; text-decoration: underline;" onclick="showPopOut('{{ $event->contest_id }}')">{{ $event->title }}</span>
                    </td>
                    
                    <td>{{ $event->contest_start_at->toDateTime()->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $event->contest_end_at->toDateTime()->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $event->contest_display_start_at ? $event->contest_display_start_at->toDateTime()->format('Y-m-d H:i:s') : '' }}</td>
                    <td>{{ $event->contest_display_end_at ? $event->contest_display_end_at->toDateTime()->format('Y-m-d H:i:s') : '' }}</td>


                    
                    <td>
                        <a href="{{ route('contests.edit', $event->_id) }}" class="btn">Edit</a>
                    </td>

                    <td>
                        <form method="post" action="{{ route('contests.delete', $event->_id) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn">Delete</button>
                        </form>
                    </td>
                </tr>
                
                <div id="popOut_{{ $event->contest_id }}" class="pop-out">
                
                    <div class="table-container">
                        <ul>
                            <li><strong>Contest ID:</strong> {{ $event->contest_id }}</li>
                            <li><strong>Title (Locale):</strong> {{ $event->title_locale }}</li>
                            <li><strong>Is Countdown Required:</strong> {{ $event->is_countdown_required ? 'true' : 'false' }}</li>
                            <br>
                            <!-- Add other attributes as needed -->
                
                            @if (!empty($event->gifts_bounded))
                            @php $giftsExist = false; @endphp

                            <div class="gifts-container">
                                @foreach ($event->gifts_bounded as $gift)
                                    @if (isset($gift['id']) || isset($gift['pricing_id']))
                                        <div class="gift-item">
                                            <div class="gift-info">
                                                <strong>ID:</strong> {{ isset($gift['id']) ? $gift['id'] : 'N/A' }}
                                            </div>
                                            <div class="gift-info">
                                                <strong>Pricing ID:</strong> {{ isset($gift['pricing_id']) ? $gift['pricing_id'] : 'N/A' }}
                                            </div>
                                        </div>
                                        @php $giftsExist = true; @endphp
                                    @endif
                                @endforeach

                                @if (!$giftsExist)
                                    <p>No gift bounded</p>
                                @endif
                            </div>
                        @else
                            <p>No gifts bounded</p>
                        @endif
                            @if ($event->graphics)
                                @php $allGraphicsNull = true; @endphp
                                <div class="graphics-container">
                                    @foreach ($event->graphics as $graphic)
                                        @if (!empty($graphic['type']) || !empty($graphic['asset_url']) || !empty($graphic['reference']) || !empty($graphic['url']) || !empty($graphic['targeted_audiences']) || !empty($graphic['is_countdown_required']) || !empty($graphic['countdown_font_color']) || !empty($graphic['title_font_color']) || !empty($graphic['cta']))
                                            @php $allGraphicsNull = false; @endphp
                                            <div class="graphic-item">
                                                <ul>
                                                    @isset($graphic['tier'])
                                                        @php                                                 
                                                            $selectedId = $event->tier_system; // Assuming tier_system is an attribute in $event
                                                            // Find the corresponding tier configuration in contest_tier.php
                                                            $tierConfig = null;
                                                    
                                                            foreach (config('contest_tier.tier_system') as $tierSystem) {
                                                                if ($tierSystem['id'] === $selectedId) {
                                                                    $selectedTier = $graphic['tier'];
                                                                    // Find the specific tier configuration
                                                                    foreach ($tierSystem['tiers'] as $tier) {
                                                                        if ($tier['tier'] === $selectedTier) {
                                                                            $tierConfig = $tier;
                                                                            break;
                                                                        }
                                                                    }
                                                                    // Break out of the outer loop once we find the tier system
                                                                    break;
                                                                }
                                                            }
                                                        @endphp  
                                                        @if ($tierConfig)
                                        
                                                            @if ($selectedTier === 1)
                                                                <p>Tier 1</p>

                                                            @else
                                                                <p>Tier {{ $selectedTier }}</p>

                                                            @endif
                                                        @endif
                                                    @endisset

                                                    <div class="graphic-containers">
                                                    @php
                                                        $GraphicType = $graphic['type'] ?? null;
                                                        $TargetedAudience = $graphic['targeted_audiences'] ?? null;
                
                                                        switch ("$GraphicType-$TargetedAudience") {
                                                            case 'leaderboard_banner-viewers':
                                                                $assetUrlLabel = 'Gifter Banner';
                                                                break;
                                                            case 'leaderboard_banner-streamers':
                                                                $assetUrlLabel = "Streamer Banner ";
                                                                break;
                                                            case 'player_floating_banner-viewers':
                                                                $assetUrlLabel = 'Floating Gifter Banner';
                                                                break;
                                                            case 'player_floating_banner-streamers':
                                                                $assetUrlLabel = 'Floating Streamer Banner';
                                                                break;
                                                            case 'coins_contest_banner-all':
                                                                $assetUrlLabel = 'Coin Banner';
                                                                break;
                                                            default:
                                                                $assetUrlLabel = 'Unknown Graphic Type or Audience';
                                                        }
                                                    @endphp
                                                        <li>{{ $assetUrlLabel }}</li>
                
                                                    @isset($graphic['asset_url'])
                                                        <li>
                                                            <img src="{{ asset($graphic['asset_url']) }}" alt="Graphic Image" style="max-width: 150px; max-height: 150px;">
                                                        </li>
                                                    @endisset
                
                                                    @isset($graphic['template'])
                                                        <li>Template: {{ $graphic['template'] }}</li>
                                                    @endisset
                
                                                    @isset($graphic['text'])
                                                        <li>Text: {{ $graphic['text'] }}</li>
                                                    @endisset
                
                                                    @isset($graphic['is_countdown_required'])
                                                        <li>Is Countdown Required: {{ $graphic['is_countdown_required'] ? 'Yes' : 'No' }}</li>
                                                    @endisset
                
                                                    @isset($graphic['countdown_font_color'])
                                                        <li>
                                                            Countdown Font Color:
                                                            {{ $graphic['countdown_font_color'] }}
                                                            <div style="vertical-align: text-bottom; width: 20px; height: 20px; background-color: {{ $graphic['countdown_font_color'] }}; display: inline-block; margin-left: 5px;"></div>
                                                        </li>
                                                    @endisset
                
                                                    @isset($graphic['title_font_color'])
                                                        <li>
                                                            Title Font Color:
                                                            {{ $graphic['title_font_color'] }}
                                                            <div style="vertical-align: text-bottom; width: 20px; height: 20px; background-color: {{ $graphic['title_font_color'] }}; display: inline-block; margin-left: 5px;"></div>
                                                        </li>
                                                    @endisset
                                                    </div>
                                                </ul>
                                            </div>
                                            <br>
                                            <hr>
                                        @endif
                                    @endforeach
                                </div>
                                <a href="{{ route('contests.edit', $event->_id) }}" class="btn">Edit</a>
                
                                @if ($allGraphicsNull)
                                    <p>No graphics available</p>
                                @endif
                            @else
                                <p>No graphics available</p>
                            @endif
                        </ul>
                    </div>
                </div>
                
            @endforeach
        </tbody>  
    </table>

        <div style="display: flex; justify-content: center;">
            {{ $events->links('pagination::bootstrap-4') }}
        </div>

    <script>

        function toggleTextDisplay() {
        var checkBoxes = document.querySelectorAll(".myCheck");
        var texts = document.querySelectorAll(".text");

        checkBoxes.forEach(function(checkBox, index) {
            var text = texts[index];

            if (checkBox.checked) {
            text.style.display = "block";
            } else {
            text.style.display = "none";
            }
        });
        }

        function closeAllPopOuts() {
            var popOuts = document.getElementsByClassName('pop-out');
            var backdrop = document.getElementById('backdrop');

            for (var i = 0; i < popOuts.length; i++) {
                popOuts[i].style.display = 'none';
            }

            if (backdrop) {
                backdrop.style.display = 'none';
            }
        }

        function showPopOut(contest_id) {
            closeAllPopOuts(); // Close any open pop-outs
            
            // Show the selected pop-out
            var selectedPopOut = document.getElementById('popOut_' + contest_id);
            var backdrop = document.getElementById('backdrop');

            if (selectedPopOut && backdrop) {
                selectedPopOut.style.display = 'block';
                backdrop.style.display = 'block';
            }
        }

        // Store the current scroll position in local storage
        window.addEventListener("beforeunload", function() {
            localStorage.setItem("scrollPosition", window.scrollY);
        });

        // Restore the scroll position on page load
        window.addEventListener("load", function() {
            var scrollPosition = localStorage.getItem("scrollPosition");
            if (scrollPosition) {
                window.scrollTo(0, scrollPosition);
            }
        });

        // Function to submit the form for deleting selected contests
        function deleteSelected() {
            document.forms[0].submit();
        }
    </script>
</body>
</html>

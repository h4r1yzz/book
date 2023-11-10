<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contest List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #c42424;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }

        table, th, td {
            border: 1px solid #161fbc;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #333;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #c10c81;
        }

        tr:nth-child(odd) {
            background-color: #279d3f;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn {
            background-color: #b4c728;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Contest List</h1>

    <div>
        <a href="{{ route('contests.create') }}" class="btn">Create another contest</a>
    </div>

    <table>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Contest ID</th>
                <th scope="col">Title</th>
                <th scope="col">Title (Locale)</th>
                <th scope="col">Contest Start</th>
                <th scope="col">Contest End</th>
                <th scope="col">Contest Display Start</th>
                <th scope="col">Contest Display End</th>
                <th scope="col">Contest Type</th>
                <th scope="col">Auto Enrollment</th>
                <th scope="col">Sorting</th>
                <th scope="col">Is Countdown Required</th>
                <th scope="col">Gifts Bounded</th>
                <th scope="col">Coins Bounded</th>
                <th scope="col">Graphics</th>
                <th scope="col">Updated At</th>
                <th scope="col">Created At</th>
                <th scope="col">Edit</th>
                <th scope="col">Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $event->contest_id }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->title_locale }}</td>
                    <td>{{ $event->contest_start_at }}</td>
                    <td>{{ $event->contest_end_at }}</td>
                    <td>{{ $event->contest_display_start_at }}</td>
                    <td>{{ $event->contest_display_end_at }}</td>
                    <td>{{ $event->contest_type }}</td>
                    <td>{{ $event->auto_enrollment }}</td>
                    <td>{{ $event->sorting ?? 'null' }}</td>
                    <td>{{ $event->is_countdown_required ? 'true' : 'false' }}</td>
                    
                    <td>
                        @if (!empty($event->gifts_bounded))
                            <ul>
                                @php $giftsExist = false; @endphp
                                @foreach ($event->gifts_bounded as $gift)
                                    @if (isset($gift['id']) || isset($gift['pricing_id']))
                                        <li>ID: {{ isset($gift['id']) ? $gift['id'] : 'N/A' }}, Pricing ID: {{ isset($gift['pricing_id']) ? $gift['pricing_id'] : 'N/A' }}</li>
                                        <br>
                                        @php $giftsExist = true; @endphp
                                    @endif
                                @endforeach
                                @if (!$giftsExist)
                                    <p>No gift bounded</p>
                                @endif
                            </ul>
                        @else
                            <p>No gifts bounded</p>
                        @endif
                    </td>

                    <td>
                        @if ($event->graphics)
                            @php
                                $allGraphicsNull = true; // Flag to track if all graphics are null
                            @endphp
                    
                            <ul>
                                @foreach ($event->graphics as $graphic)
                                    @if (!empty($graphic['type']) || !empty($graphic['asset_url']) || !empty($graphic['reference']) || !empty($graphic['url']) || !empty($graphic['targeted_audiences']) || !empty($graphic['is_countdown_required']) || !empty($graphic['countdown_font_color']) || !empty($graphic['title_font_color']) || !empty($graphic['cta']))
                                        @php
                                            $allGraphicsNull = false;
                                        @endphp
                                        <li>
                                            <ul>
                                                @isset($graphic['type'])
                                                    <li>Type: {{ $graphic['type'] }}</li>
                                                @endisset
                                                @isset($graphic['asset_url'])
                                                    <li>Asset URL: <a href="{{ $graphic['asset_url'] }}" target="_blank">{{ $graphic['asset_url'] }}</a></li>
                                                @endisset
                                                @isset($graphic['asset_url'])
                                                    <li>Asset URL: <img src="{{ asset($graphic['asset_url']) }}" alt="Graphic Image" style="width: 150px; height: 150px;">
                                                    </li>
                                                @endisset
                                                @isset($graphic['reference'])
                                                    <li>Reference: {{ $graphic['reference'] }}</li>
                                                @endisset
                                                @isset($graphic['url'])
                                                    <li>URL: <a href="{{ $graphic['url'] }}" target="_blank">{{ $graphic['url'] }}</a></li>
                                                @endisset
                                                @isset($graphic['targeted_audiences'])
                                                    <li>Targeted Audience: {{ $graphic['targeted_audiences'] }}</li>
                                                @endisset
                                                @isset($graphic['is_countdown_required'])
                                                    <li>Is Countdown Required: {{ $graphic['is_countdown_required'] ? 'Yes' : 'No' }}</li>
                                                @endisset
                                                @isset($graphic['countdown_font_color'])
                                                    <li>Countdown Font Color: {{ $graphic['countdown_font_color'] }}</li>
                                                @endisset
                                                @isset($graphic['title_font_color'])
                                                    <li>Title Font Color: {{ $graphic['title_font_color'] }}</li>
                                                @endisset
                                                @isset($graphic['cta'])
                                                    <li>CTA: {{ $graphic['cta'] }}</li>
                                                @endisset
                                                @isset($graphic['tier'])
                                                    <li>Tier: {{$graphic['tier']}}</li>
                                                @endisset
                                                @isset($graphic['template'])
                                                    <li>Template: {{$graphic['template']}}</li>
                                                @endisset
                                                @isset($graphic['text'])
                                                    <li>Text: {{$graphic['text']}}</li>
                                                @endisset
                                            </ul>
                                        </li>
                                        <br>
                                    @endif 
                                @endforeach
                            </ul>
                    
                            @if ($allGraphicsNull)
                                <p>No graphics available</p>
                            @endif
                        @else
                            <p>No graphics available</p>
                        @endif
                    </td>
                    
                    <td>{{ $event->updated_at }}</td>
                    <td>{{ $event->created_at }}</td>
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
            @endforeach
        </tbody>
    </table>

    <script>
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






{{--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contest List</title>
</head>
<body>
    <h1>Contest List</h1>

    <div>
        <a href="{{route('contests.create')}}"> Create another contest </a>
    </div>
    
    <table class="table table-striped table-dark">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Contest ID</th>
                <th scope="col">Title</th>
                <th scope="col">Title (Locale)</th>
                <th scope="col">Contest Start</th>
                <th scope="col">Contest End</th>
                <th scope="col">Contest Display Start</th>
                <th scope="col">Contest Display End</th>
                <th scope="col">Contest Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $event->contest_id }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->title_locale }}</td>
                    <td>{{ $event->contest_start_at }}</td>
                    <td>{{ $event->contest_end_at }}</td>
                    <td>{{ $event->contest_display_start_at }}</td>
                    <td>{{ $event->contest_display_end_at }}</td>
                    <td>{{ $event->contest_type }}</td>


                    <td>
                        <a href="{{ route('contests.edit', $event->_id) }}" class="btn btn-primary">Edit</a>
                    </td>


                    <!-- Inside the <tr> element for each contest -->
                <td>
                    <form method="post" action="{{ route('contests.delete', $event->_id) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

--}}


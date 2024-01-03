<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

    <title>Weather App</title>

    <!-- Loading third party fonts -->
    <link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|" rel="stylesheet" type="text/css">
    <link href="fonts/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Loading main CSS file -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="site-content">
    <div class="hero" data-bg-image="images/banner.png">
        <div class="container">
            <form id="weatherSearchForm" action="{{ route('getWeather') }}" method="post">
                @csrf
                <input type="hidden" @if(isset($currentWeather)) value="{{ $currentWeather['latitude'] }}" @endif name="latitude" id="latitudeInput">
                <input type="hidden" @if(isset($currentWeather)) value="{{ $currentWeather['longitude'] }}" @endif name="longitude" id="longitudeInput">
                <input type="text" name="cityInput" id="cityInput"   placeholder="Enter city name">
                {{-- <input type="text" name="cityInput" id="cityInput" required placeholder="Enter city name" @if(isset($currentWeather)) value="{{ $currentWeather['city'] }}" @endif> --}}

                <label>
                    <input type="radio" name="unit" value="celsius" {{ session('unit', 'celsius') == 'celsius' ? 'checked' : '' }}>
                    Celsius
                </label>
                <label>
                    <input type="radio" name="unit" value="fahrenheit" {{ session('unit', 'celsius') == 'fahrenheit' ? 'checked' : '' }}>
                    Fahrenheit
                </label>
                
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
    <div class="forecast-table">
        <div class="container">
            <div class="forecast-container" id="weatherData">
                @if(isset($currentWeather))
                    <div class="today forecast">
                        <div class="forecast-header">
                            <div class="day">{{ $currentWeather['day'] }}</div>
                            <div class="date">{{ $currentWeather['date'] }}</div>
                        </div>
                        <div class="forecast-content">
                            <div class="location">{{ $currentWeather['city'] }}</div>
                            <div class="degree">
                                <div class="num">
                                    @if(session('unit', 'celsius') == 'celsius')
                                        {{ $currentWeather['temperature'] }}<sup>o</sup>C
                                    @else
                                        {{ $currentWeather['temperature'] }}<sup>o</sup>F
                                    @endif
                                </div>
                                <div class="forecast-icon">
                                    <img src="https://openweathermap.org/img/wn/{{ $currentWeather['icon'] }}.png" alt="" width=90>
                                </div>
                            </div>
                            <span>Humidity: {{ $currentWeather['humidity'] }}%</span>
                            <span>Wind: {{ $currentWeather['windSpeed'] }} km/h</span>
                        </div>
                    </div>
                @endif

                @if(isset($fiveDayForecast))
                    @foreach($fiveDayForecast as $forecast)
                        <div class="forecast">
                            <div class="forecast-header">
                                <div class="day">{{ $forecast['day'] }}</div>
                            </div>
                            <div class="forecast-content">
                                <div class="forecast-icon">
                                    <img src="https://openweathermap.org/img/wn/{{ $forecast['icon'] }}.png" alt="" width="48">
                                </div>
                                <div class="degree">
                                    @if(session('unit', 'celsius') == 'celsius')
                                        {{ $forecast['temperature'] }}<sup>o</sup>C
                                    @else
                                        {{ $forecast['temperature'] }}<sup>o</sup>F
                                    @endif
                                </div>
                                <small>{{ $forecast['windSpeed'] }}<sup>o</sup></small>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div id="map" style="height: 400px;"></div>

    <footer class="site-footer">
    </footer>
</div>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/app.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Display current weather marker
    @if(isset($currentWeather) && !empty($currentWeather['latitude']) && !empty($currentWeather['longitude']))
        var currentWeatherMarker = L.marker([{{ $currentWeather['latitude'] }}, {{ $currentWeather['longitude'] }}])
            .addTo(map)
            .bindPopup("<b>{{ $currentWeather['city'] }}</b><br>Temperature: {{ $currentWeather['temperature'] }}°{{ session('unit', 'C') }}<br>Humidity: {{ $currentWeather['humidity'] }}%<br>Wind: {{ $currentWeather['windSpeed'] }} km/h");
    @endif

    // Add an event listener to the map for click events
    map.on('click', function(e) {
        document.getElementById("latitudeInput").value = e.latlng.lat;
        document.getElementById("longitudeInput").value = e.latlng.lng;
        sessionStorage.setItem('formSubmitted', 'true');
        document.getElementById("weatherSearchForm").submit();
    });

    function currentLocation() {
        const formSubmittedFlag = sessionStorage.getItem('formSubmitted');

        if (!formSubmittedFlag && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                document.getElementById("latitudeInput").value = latitude;
                document.getElementById("longitudeInput").value = longitude;

                sessionStorage.setItem('formSubmitted', 'true');

                document.getElementById("weatherSearchForm").submit();
            }, function (error) {
                console.error('Error getting geolocation:', error);
            });
        } else {
            console.error('Geolocation is not supported by this browser or the form has already been submitted.');
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        currentLocation()
    });

    @if(isset($error))
        @if($error == 'City not found')
            toastr.error('City not found. Please enter a valid city name.');
            sessionStorage.removeItem('formSubmitted');
            currentLocation()
        @else
            toastr.error('Error: {{ $error }}');
        @endif
    @endif
</script>


</body>
</html>

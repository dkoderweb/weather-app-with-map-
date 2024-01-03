<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

    <title>Compass Starter by Ariona, Rian</title>

    <!-- Loading third party fonts -->
    <link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|" rel="stylesheet" type="text/css">
    <link href="fonts/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Loading main CSS file -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="site-content">
    <div class="hero" data-bg-image="images/banner.png">
        <div class="container">
            <form id="weatherSearchForm" action="{{ route('getWeather') }}" method="post">
                @csrf
                <input type="hidden" name="latitude" id="latitudeInput">
                <input type="hidden" name="longitude" id="longitudeInput">
                <input type="text" name="cityInput" id="cityInput" required placeholder="Enter city name">
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
                                <div class="num">{{ $currentWeather['temperature'] }}<sup>o</sup>C</div>
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
                                <div class="degree">{{ $forecast['temperature'] }}<sup>o</sup>C</div>
                                <small>{{ $forecast['windSpeed'] }}<sup>o</sup></small>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <footer class="site-footer">
    </footer>
</div>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/app.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    function currentLocation(){
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

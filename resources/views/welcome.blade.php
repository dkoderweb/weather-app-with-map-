<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1">
		
		<title>Compass Starter by Ariona, Rian</title>

		<!-- Loading third party fonts -->
		<link href="http://fonts.googleapis.com/css?family=Roboto:300,400,700|" rel="stylesheet" type="text/css">
		<link href="fonts/font-awesome.min.css" rel="stylesheet" type="text/css">

		<!-- Loading main css file -->
		<link rel="stylesheet" href="style.css">
		 

	</head>


	<body>
		
		<div class="site-content"> 
			<div class="hero" data-bg-image="images/banner.png">
				<div class="container">
                        <form id="weatherSearchForm">
                            <input type="text" id="cityInput" placeholder="Enter city name">
                            <button type="submit">Search</button>
                        </form>
				</div>
			</div>
			<div class="forecast-table">
				<div class="container">
					<div class="forecast-container" id="weatherData">
                        {{-- <div  class="today forecast">

                        </div>
                        <div id="five_day" class="forecast">

                        </div>  --}}
					</div>
				</div>
			</div> 

			<footer class="site-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-8">
							<form action="#" class="subscribe-form">
								<input type="text" placeholder="Enter your email to subscribe...">
								<input type="submit" value="Subscribe">
							</form>
						</div>
						<div class="col-md-3 col-md-offset-1">
							<div class="social-links">
								<a href="#"><i class="fa fa-facebook"></i></a>
								<a href="#"><i class="fa fa-twitter"></i></a>
								<a href="#"><i class="fa fa-google-plus"></i></a>
								<a href="#"><i class="fa fa-pinterest"></i></a>
							</div>
						</div>
					</div>

					<p class="colophon">Copyright 2014 Company name. Designed by Themezy. All rights reserved</p>
				</div>
			</footer> <!-- .site-footer -->
		</div>
		
		<script src="js/jquery-1.11.1.min.js"></script>
		<script src="js/plugins.js"></script>
		<script src="js/app.js"></script>
		
	</body>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const apiKey = '1da0038384090ff8d5fa254255a8d0c5';

            function fetchWeatherData(cityName) {
                fetch(`https://api.openweathermap.org/data/2.5/weather?q=${cityName}&appid=${apiKey}`)
                    .then(response => response.json())
                    .then(data => {
                        const temperature = Math.round(data.main.temp - 273.15);
                        const icon = data.weather[0].icon;
                        const humidity = data.main.humidity;
                        const windSpeed = data.wind.speed;
                        const city = data.name;
                        const day = new Date().toLocaleDateString('en-US', {
                            weekday: 'long'
                        });
                        const date = new Date().toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short'
                        });

                        const weatherDataContainer = document.getElementById('weatherData');
                        weatherDataContainer.innerHTML = '';
                        weatherDataContainer.innerHTML += `
                            <div class="today forecast">
                                <div class="forecast-header">
                                    <div class="day">${day}</div>
                                    <div class="date">${date}</div>
                                </div>
                                <div class="forecast-content">
                                    <div class="location">${city}</div>
                                    <div class="degree">
                                        <div class="num">${temperature}<sup>o</sup>C</div>
                                        <div class="forecast-icon">
                                            <img src="https://openweathermap.org/img/wn/${icon}.png" alt="" width=90>
                                        </div>
                                    </div>
                                    <span>Humidity: ${humidity}%</span>
                                    <span>Wind: ${windSpeed} km/h</span>
                                </div>
                            </div>`;
                    })
                    .catch(error => {
                        console.error('Error fetching current weather data:', error);
                    });
            }

            function fetchFiveDayForecast(cityName) {
                fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${cityName}&appid=${apiKey}`)
                    .then(response => response.json())
                    .then(data => {
                        const forecastContainer = document.getElementById('weatherData');

                        // Get tomorrow's date
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        tomorrow.setHours(0, 0, 0, 0);

                        // Filter forecast data starting from tomorrow
                        const filteredForecast = data.list.filter(forecast => {
                            const forecastDate = new Date(forecast.dt * 1000);
                            return forecastDate >= tomorrow;
                        });

                        for (let i = 0; i < filteredForecast.length; i += 8) {
                            const forecast = filteredForecast[i];
                            const day = new Date(forecast.dt * 1000).toLocaleDateString('en-US', {
                                weekday: 'long'
                            });
                            const temperature = Math.round(forecast.main.temp - 273.15);
                            const icon = forecast.weather[0].icon;

                            const forecastElement = document.createElement('div');
                            forecastElement.className = 'forecast';
                            forecastElement.innerHTML = `
                                <div class="forecast-header">
                                    <div class="day">${day}</div>
                                </div>
                                <div class="forecast-content">
                                    <div class="forecast-icon">
                                        <img src="https://openweathermap.org/img/wn/${icon}.png" alt="" width="48">
                                    </div>
                                    <div class="degree">${temperature}<sup>o</sup>C</div>
                                    <small>${forecast.wind.speed}<sup>o</sup></small>
                                </div>
                            `;

                            forecastContainer.appendChild(forecastElement);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching five-day forecast data:', error);
                    });
            }

            function fetchCurrentLocationWeather() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=${apiKey}`)
                            .then(response => response.json())
                            .then(data => {
                                const cityName = data.name;
                                fetchWeatherData(cityName);
                                fetchFiveDayForecast(cityName);
                            })
                            .catch(error => {
                                console.error('Error fetching weather data for current location:', error);
                            });
                    }, function (error) {
                        console.error('Error getting geolocation:', error);
                    });
                } else {
                    console.error('Geolocation is not supported by this browser.');
                }
            }

            fetchCurrentLocationWeather();

            const searchForm = document.getElementById('weatherSearchForm');
            searchForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const cityInput = document.getElementById('cityInput').value;
                if (cityInput) {
                    fetchWeatherData(cityInput);
                    fetchFiveDayForecast(cityInput);
                }
            });
        });
    </script>
</html>

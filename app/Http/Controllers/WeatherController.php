<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getWeather(Request $request)
    {
        $apiKey = '1da0038384090ff8d5fa254255a8d0c5';
        $cityInput = $request->input('cityInput');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        try {
            $unit = $request->input('unit', 'celsius');
            session(['unit' => $unit]);
            if (!empty($cityInput)) {
                $currentWeather = $this->fetchCurrentWeather($cityInput, $apiKey);
                $fiveDayForecast = $this->fetchFiveDayForecast($cityInput, $apiKey);
            } elseif (!empty($latitude) && !empty($longitude)) {
                $currentWeather = $this->fetchCurrentWeatherByCoordinates($latitude, $longitude, $apiKey);
                $fiveDayForecast = $this->fetchFiveDayForecastByCoordinates($latitude, $longitude, $apiKey);
            } else {
                return redirect()->route('index');
            }

            return view('welcome', [
                'currentWeather' => $currentWeather,
                'fiveDayForecast' => $fiveDayForecast,
            ]);
        } catch (\Exception $e) {
            return view('welcome', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function fetchCurrentWeather($cityName, $apiKey)
    {
        $cacheKey = 'current_weather_' . $cityName;
        $cachedWeather = Cache::get($cacheKey);

        if ($cachedWeather) {
            return $cachedWeather;
        }

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q={$cityName}&appid={$apiKey}");

        if ($response->successful()) {
            $data = $response->json();
            $weatherData = $this->formatWeatherData($data); 

            Cache::put($cacheKey,  60);

            return $weatherData;
        }

        if ($response->status() === 404) {
            throw new \Exception('City not found');
        }

        Log::error('Error fetching current weather data: ' . $response->status());
        throw new \Exception('Error fetching current weather data: ' . $response->status());
    }

    private function fetchFiveDayForecast($cityName, $apiKey)
    {
        $cacheKey = 'five_day_forecast_' . $cityName;
        $cachedForecast = Cache::get($cacheKey);

        if ($cachedForecast) {
            return $cachedForecast;
        }

        $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?q={$cityName}&appid={$apiKey}");

        if ($response->successful()) {
            $data = $response->json();
            $forecastData = $this->filterFiveDayForecast($data);

            

            // Cache the result for 1 hour (adjust as needed)
            Cache::put($cacheKey,  60);

            return $forecastData;
        }

        Log::error('Error fetching five-day forecast data: ' . $response->status());
        throw new \Exception('Error fetching five-day forecast data: ' . $response->status());
    } 

    private function fetchCurrentWeatherByCoordinates($latitude, $longitude, $apiKey)
    {
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}");

        if ($response->successful()) {
            $data = $response->json();
            return $this->formatWeatherData($data);
        }

        throw new \Exception('Error fetching current weather data: ' . $response->status());
    }

    private function fetchFiveDayForecastByCoordinates($latitude, $longitude, $apiKey)
    {
        $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?lat={$latitude}&lon={$longitude}&appid={$apiKey}");

        if ($response->successful()) {
            $data = $response->json();
            return $this->filterFiveDayForecast($data);
        }

        throw new \Exception('Error fetching five-day forecast data: ' . $response->status());
    }

    private function formatWeatherData($data)
    {
        // dump($data);
        $temperatureInKelvin = $data['main']['temp'];
        $unit = session('unit', 'celsius');
    
        if ($unit == 'fahrenheit') {
            $temperature = round(($temperatureInKelvin * 9/5) - 459.67);
        } else {
            $temperature = round($temperatureInKelvin - 273.15);
        }
    
        $icon = $data['weather'][0]['icon'];
        $humidity = $data['main']['humidity'];
        $windSpeed = $data['wind']['speed'];
        $city = $data['name'];
        $day = now()->format('l');
        $date = now()->format('j M');
        $latitude = isset($data['coord']['lat']) ? $data['coord']['lat'] : null;
        $longitude = isset($data['coord']['lon']) ? $data['coord']['lon'] : null;

    
        return [
            'day' => $day,
            'date' => $date,
            'city' => $city,
            'temperature' => $temperature,
            'icon' => $icon,
            'humidity' => $humidity,
            'windSpeed' => $windSpeed,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
    

    private function filterFiveDayForecast($data)
    {
        $forecastData = $data['list'];
        $filteredForecast = [];
    
        $tomorrow = now()->addDay()->startOfDay();
    
        foreach ($forecastData as $forecast) {
            $forecastDate = now()->setTimestamp($forecast['dt']);
            if ($forecastDate >= $tomorrow && count($filteredForecast) < 5) {
                $temperatureInKelvin = $forecast['main']['temp'];
                $unit = session('unit', 'celsius');
    
                if ($unit == 'fahrenheit') {
                    $temperature = round(($temperatureInKelvin * 9/5) - 459.67);
                } else {
                    $temperature = round($temperatureInKelvin - 273.15);
                }
    
                $icon = $forecast['weather'][0]['icon'];
                $day = $forecastDate->format('l');
    
                $filteredForecast[] = [
                    'day' => $day,
                    'temperature' => $temperature,
                    'icon' => $icon,
                    'windSpeed' => $forecast['wind']['speed'],
                ];
            }
        }
    
        return $filteredForecast;
    }
    
}

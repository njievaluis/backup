const cityInput = document.getElementById('city-input');
const searchBtn = document.getElementById('search-btn');
const locationBtn = document.getElementById('location-btn');
const currentWeatherDetails = document.querySelector('.current-weather .details');
const weatherIcon = document.querySelector('.weather-icon img');
const cardFooter = document.querySelector('.card-footer');
const forecastItems = document.querySelectorAll('.forecast-item');
const hourlyForecastItems = document.querySelectorAll('.hourly-forecast .card');
const humidityVal = document.getElementById('humidity-val');
const pressureVal = document.getElementById('pressure-val');
const visibilityVal = document.getElementById('visibility-val');
const windSpeedVal = document.getElementById('wind-speed-val');
const feelsVal = document.getElementById('feels-val');
const airIndexElements = document.querySelectorAll('.air-indices .item h2');
const sunriseElement = document.querySelector('.sunrise-sunset .item:nth-child(1) h2');
const sunsetElement = document.querySelector('.sunrise-sunset .item:nth-child(2) h2');
const airQualityIndex = document.querySelector('.air-index');
document.addEventListener('DOMContentLoaded', function() {
    const voiceButton = document.getElementById('voice-btn');
    const searchInput = document.getElementById('city-input');
    const searchButton = document.getElementById('search-btn');
    const voiceMessage = document.getElementById('voice-message');
    
    document.addEventListener('DOMContentLoaded', function() {
        const aboutLink = document.getElementById('about-link');
        const aboutModal = document.getElementById('aboutUsContent');
        const closeButton = document.querySelector('.close-button');
        
        aboutLink.addEventListener('click', function(e) {
            e.preventDefault();
            aboutModal.style.display = 'block';
        });
        
        closeButton.addEventListener('click', function() {
            aboutModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === aboutModal) {
                aboutModal.style.display = 'none';
            }
        });
    });
    
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if (SpeechRecognition) {
        const recognition = new SpeechRecognition();
        recognition.lang = 'en-US';
        recognition.continuous = false;
        recognition.interimResults = false;
        
        voiceButton.addEventListener('click', function() {
            if (voiceButton.classList.contains('voice-active')) {
                recognition.stop();
                voiceButton.classList.remove('voice-active');
                voiceMessage.textContent = '';
            } else {
                recognition.start();
                voiceButton.classList.add('voice-active');
                voiceMessage.textContent = 'Listening... Say a city name';
            }
        });

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            searchInput.value = transcript;
            voiceMessage.textContent = `Searching for: "${transcript}"`;
            
            setTimeout(() => {
                searchButton.click();
            }, 500);
            
            voiceButton.classList.remove('voice-active');
        };
        
        recognition.onend = function() {
            voiceButton.classList.remove('voice-active');
            if (voiceMessage.textContent === 'Listening... Say a city name') {
                voiceMessage.textContent = 'No speech detected. Try again.';
                setTimeout(() => {
                    if (voiceMessage.textContent === 'No speech detected. Try again.') {
                        voiceMessage.textContent = '';
                    }
                }, 3000);
            }
        };
        
        recognition.onerror = function(event) {
            voiceButton.classList.remove('voice-active');
            voiceMessage.textContent = 'Speech recognition error. Try again.';
            setTimeout(() => {
                voiceMessage.textContent = '';
            }, 3000);
        };
        
    } else {
        voiceButton.style.display = 'none';
        console.log('Speech recognition not supported in this browser');
    }
});

let map = L.map('map').setView([0, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

let marker;

const API_KEY = '939a5ffff5a410a92d68ca4db1857cc8';

window.addEventListener('load', () => {
    searchCity("Douala, Cameroon");
});

searchBtn.addEventListener('click', () => {
    searchCity(cityInput.value.trim());
});

cityInput.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') {
        searchCity(cityInput.value.trim());
    }
});

locationBtn.addEventListener('click', getCurrentLocation);

function searchCity(cityName) {
    if (!cityName) return;
    
    cityInput.value = '';
    const GEOCODING_API_URL = `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(cityName)}&limit=1&appid=${API_KEY}`;

    fetch(GEOCODING_API_URL)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.length > 0) {
                const { lat, lon, name, country } = data[0];
                updateMap(lat, lon, name);
                getWeatherData(lat, lon, name, country);
            } else {
                alert(`City "${cityName}" not found.Evalius says, Please check the spelling and try again.`);
            }
        })
        .catch(error => {
            console.error("Error fetching coordinates:", error);
            alert("Failed to fetch city coordinates. Please try again later.");
        });
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const { latitude, longitude } = position.coords;
                updateMap(latitude, longitude);
                getCityName(latitude, longitude);
            },
            error => {
                console.error("Error getting location:", error);
                alert("Unable to get your location. Please check your internet connection.");
            }
        );
    } else {
        alert("Geolocation is not supported by your browser");
    }
}

function getCityName(lat, lon) {
    const REVERSE_GEOCODING_URL = `https://api.openweathermap.org/geo/1.0/reverse?lat=${lat}&lon=${lon}&limit=1&appid=${API_KEY}`;
    
    fetch(REVERSE_GEOCODING_URL)
        .then(res => res.ok ? res.json() : Promise.reject(`Status: ${res.status}`))
        .then(data => {
            if (data.length > 0) {
                const { name, country } = data[0];
                getWeatherData(lat, lon, name, country);
            }
        })
        .catch(error => {
            console.error("Error in reverse geocoding:", error);
            getWeatherData(lat, lon);
        });
}

function updateMap(lat, lon, name = "Your Location") {
    map.setView([lat, lon], 10);
    
    if (marker) {
        map.removeLayer(marker);
    }
    
    marker = L.marker([lat, lon]).addTo(map)
        .bindPopup(name)
        .openPopup();
}

function getWeatherData(lat, lon, city = "Your Location", country = "") {
    const timestamp = new Date().getTime();
    
    const CURRENT_WEATHER_API_URL = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${API_KEY}&_=${timestamp}`;
    const ONE_CALL_API_URL = `https://api.openweathermap.org/data/3.0/onecall?lat=${lat}&lon=${lon}&units=metric&exclude=minutely&appid=${API_KEY}&_=${timestamp}`;
    const FORECAST_API_URL = `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&appid=${API_KEY}&_=${timestamp}`;
    const AIR_POLLUTION_API_URL = `https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat}&lon=${lon}&appid=${API_KEY}&_=${timestamp}`;
    
    Promise.all([
        fetch(CURRENT_WEATHER_API_URL).then(res => res.ok ? res.json() : Promise.reject(`Status: ${res.status}`)),
        fetch(ONE_CALL_API_URL).catch(err => null).then(res => res && res.ok ? res.json() : null)
    ])
    .then(([currentData, oneCallData]) => {
        let temp = currentData.main.temp;
        let feels_like = currentData.main.feels_like;
        
        if (oneCallData && oneCallData.current) {
            temp = oneCallData.current.temp;
            feels_like = oneCallData.current.feels_like;
            if (oneCallData.hourly && oneCallData.daily) {
                displayOneCallForecasts(oneCallData, city, country);
            }
        }
        
        if (city === "Bamenda" && country === "CM") {
            const hour = new Date().getHours();
            
            if (hour >= 6 && hour < 9) {
                temp = 20 + Math.random() * 2;
            } else if (hour >= 9 && hour < 18) {
                temp = 25 + Math.random() * 2;
            } else {
                temp = 19 + Math.random() * 2;
            }
            feels_like = temp + (Math.random() * 2 - 1);
        }
        currentData.main.temp = temp;
        currentData.main.feels_like = feels_like;
        displayCurrentWeather(currentData, city, country);
        if (!oneCallData || !oneCallData.hourly || !oneCallData.daily) {
            return fetch(FORECAST_API_URL);
        } else {
            return null;
        }
    })
    .then(res => res ? res.json() : null)
    .then(forecastData => {
        if (forecastData) {
            displayForecast(forecastData);
            displayHourlyForecast(forecastData);
        }
        
        return fetch(AIR_POLLUTION_API_URL);
    })
    .then(res => res.ok ? res.json() : Promise.reject(`Status: ${res.status}`))
    .then(airData => {
        displayAirQuality(airData);
    })
    .catch(error => {
        console.error("Error in weather data chain:", error);
        alert("Unable to fetch complete weather data. Some information may be missing.");
    });
}

function displayOneCallForecasts(data, city, country) {
    const dailyForecasts = data.daily.slice(1, 8);
    
    forecastItems.forEach((item, index) => {
        if (index < dailyForecasts.length) {
            const forecast = dailyForecasts[index];
            const date = new Date(forecast.dt * 1000);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            
            const iconWrapper = item.querySelector('.icon-wrapper');
            iconWrapper.innerHTML = `
                <img src="https://openweathermap.org/img/wn/${forecast.weather[0].icon}.png" alt="Weather forecast icon">
                <span>${Math.round(forecast.temp.day)}&deg;C</span>
            `;
            
            const dayNameElem = item.querySelector('.day-name');
            const forecastDesc = item.querySelector('.forecast-desc');
            dayNameElem.textContent = dayName;
            forecastDesc.textContent = forecast.weather[0].main;
        }
    });
    
    const hourlyForecasts = data.hourly.slice(1, 9);
    
    hourlyForecastItems.forEach((item, index) => {
        if (index < hourlyForecasts.length) {
            const forecast = hourlyForecasts[index];
            const time = new Date(forecast.dt * 1000);
            const hour = time.getHours();
            const formattedHour = hour % 12 || 12;
            const ampm = hour >= 12 ? 'PM' : 'AM';
            
            const paragraphs = item.querySelectorAll('p');
            if (paragraphs[0]) {
                paragraphs[0].textContent = `${formattedHour} ${ampm}`;
            }
            
            const img = item.querySelector('img');
            if (img) {
                img.src = `https://openweathermap.org/img/wn/${forecast.weather[0].icon}.png`;
            }
            
            if (paragraphs[1]) {
                paragraphs[1].textContent = `${Math.round(forecast.temp)}\u00B0C`;
            }
        }
    });
}

function displayCurrentWeather(data, city, country) {
    const { 
        main: { temp, feels_like, humidity, pressure },
        weather: [{ description, icon }],
        visibility,
        wind: { speed },
        sys: { sunrise, sunset },
        dt
    } = data;
    
    currentWeatherDetails.innerHTML = `
        <p>NOW</p>
        <h2>${Math.round(temp)}\u00B0C</h2>
        <p>${description.charAt(0).toUpperCase() + description.slice(1)}</p>
    `;
    
    weatherIcon.src = `https://openweathermap.org/img/wn/${icon}@2x.png`;
    
    const date = new Date(dt * 1000);
    const options = { weekday: 'long', month: 'long', day: 'numeric' };
    const formattedDate = date.toLocaleDateString('en-US', options);
    
    cardFooter.innerHTML = `
        <p><i class="fa-light fa-calendar"></i> ${formattedDate}</p>
        <p><i class="fa-light fa-location-dot"></i> ${city}${country ? `, ${country}` : ''}</p>
    `;
    
    humidityVal.textContent = `${humidity}%`;
    pressureVal.textContent = `${pressure}hPa`;
    visibilityVal.textContent = `${(visibility / 1000).toFixed(1)}km`;
    windSpeedVal.textContent = `${speed}m/s`;
    feelsVal.textContent = `${Math.round(feels_like)}\u00B0C`;
    
    sunriseElement.textContent = formatTime(sunrise);
    sunsetElement.textContent = formatTime(sunset);
}

function displayForecast(data) {
    const forecastMap = new Map();
    
    data.list.forEach(item => {
        const date = new Date(item.dt * 1000);
        const day = date.toISOString().split('T')[0];
        const hour = date.getHours();
        
        if (!forecastMap.has(day) || Math.abs(hour - 12) < Math.abs(forecastMap.get(day).hour - 12)) {
            forecastMap.set(day, {
                forecast: item,
                hour: hour
            });
        }
    });
    
    const dailyForecasts = Array.from(forecastMap.values())
        .map(item => item.forecast)
        .sort((a, b) => a.dt - b.dt);
    const today = new Date().toISOString().split('T')[0];
    let displayForecasts = dailyForecasts.filter(item => {
        const itemDate = new Date(item.dt * 1000).toISOString().split('T')[0];
        return itemDate > today;
    }).slice(0, 7);
    forecastItems.forEach((item, index) => {
        if (index < displayForecasts.length) {
            const forecast = displayForecasts[index];
            const date = new Date(forecast.dt * 1000);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            
            const iconWrapper = item.querySelector('.icon-wrapper');
            iconWrapper.innerHTML = `
                <img src="https://openweathermap.org/img/wn/${forecast.weather[0].icon}.png" alt="Weather forecast icon">
                <span>${Math.round(forecast.main.temp)}\u00B0C</span>
            `;
            const dayNameElem = item.querySelector('.day-name');
            const forecastDesc = item.querySelector('.forecast-desc');
            dayNameElem.textContent = dayName;
            forecastDesc.textContent = forecast.weather[0].main;
        }
    });
}

function displayHourlyForecast(data) {
    const today = new Date().toISOString().split('T')[0];
    const now = new Date().getTime() / 1000;
    
    const hourlyForecasts = data.list
        .filter(item => item.dt > now)
        .slice(0, 8);
    
    hourlyForecastItems.forEach((item, index) => {
        if (index < hourlyForecasts.length) {
            const forecast = hourlyForecasts[index];
            const time = new Date(forecast.dt * 1000);
            const hour = time.getHours();
            const formattedHour = hour % 12 || 12;
            const ampm = hour >= 12 ? 'PM' : 'AM';
            
            const paragraphs = item.querySelectorAll('p');
            if (paragraphs[0]) {
                paragraphs[0].textContent = `${formattedHour} ${ampm}`;
            }
            
            const img = item.querySelector('img');
            if (img) {
                img.src = `https://openweathermap.org/img/wn/${forecast.weather[0].icon}.png`;
            }
            
            if (paragraphs[1]) {
                paragraphs[1].textContent = `${Math.round(forecast.main.temp)}\u00B0C`;
            }
        }
    });
}

function displayAirQuality(data) {
    if (!data.list || data.list.length === 0) {
        console.error("Air quality data not available");
        return;
    }
    
    const pollutants = data.list[0].components;
    const aqi = data.list[0].main.aqi;
    const aqiTexts = ['', 'Good', 'Fair', 'Moderate', 'Poor', 'Very Poor'];
    airQualityIndex.textContent = aqiTexts[aqi];
    for (let i = 1; i <= 5; i++) {
        airQualityIndex.classList.remove(`aqi-${i}`);
    }
    airQualityIndex.classList.add(`aqi-${aqi}`);
    const pollutantKeys = ['pm2_5', 'pm10', 'so2', 'co', 'no', 'no2', 'nh3', 'o3'];
    pollutantKeys.forEach((key, index) => {
        if (index < airIndexElements.length && pollutants[key] !== undefined) {
            airIndexElements[index].textContent = pollutants[key].toFixed(1);
        }
    });
}

function formatTime(timestamp) {
    const date = new Date(timestamp * 1000);
    let hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    
    hours = hours % 12;
    hours = hours ? hours : 12;
    
    const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
    
    return `${hours}:${formattedMinutes} ${ampm}`;
}

setInterval(() => {
    if (marker) {
        const position = marker.getLatLng();
        getWeatherData(position.lat, position.lng);
    }
}, 900000);s
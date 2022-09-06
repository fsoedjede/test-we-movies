/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import $ from 'jquery';
import 'bootstrap';
import 'autocomplete.js/dist/autocomplete.jquery';


// start the Stimulus application
import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // Filter by genre
    document.querySelectorAll('#genders input[type=radio]').forEach((genreRadio) => {
        genreRadio.addEventListener('click', function () {
            fetch('/discover?' + new URLSearchParams({with_genres: genreRadio.value}))
                .then((response) => response.text())
                .then((text) => {
                    document.getElementById('movies').innerHTML = text;
                });
        })
    });
});

// Autocomplete search
$("input[name=search]").autocomplete({hint: false, minLength: 3}, [{
    source: function (query, callback) {
        fetch('/search?' + new URLSearchParams({query: query}))
            .then((response) => response.json())
            .then((data) => {
                console.debug(data);
                callback(data);
            });
    },
    displayKey: "title",
    debounce: 500,
    templates: {
        suggestion: function (movie) {
            const baseUri = $("input[name=search]").data('img-uri');
            return `<a href="/view/${movie.id}"><img src="${baseUri}${movie.poster_path}" alt="${movie.id}"><span class="title">${movie.title}</span></div>`;
        }
    }
}]).on('autocomplete:selected', function (event, suggestion) {
    window.location.assign(suggestion.url);
});

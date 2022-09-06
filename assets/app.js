/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import 'bootstrap';

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

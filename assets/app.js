/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// CSS
import 'bootstrap/dist/css/bootstrap.css'
import './styles/polices.scss';
import './styles/jkeyboard.scss';
import './styles/accueil.scss';
import './styles/app.scss';
import './styles/sidenav.scss';
import './styles/amis.scss';
import './styles/hub.scss';
import 'glightbox/dist/css/glightbox.css'
// JS
// import 'jquery'
// create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;
import './jkeyboard';
require('bootstrap')

import './partie';
// LIBS





import { createApp } from 'vue'
// import Vue from 'vue'
import App from './App.vue'

import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faPlus,
  faEnvelope,
  faMapMarkerAlt,
  faPhone,
  faUserGraduate,
  faBook,
  faBuilding,
  faHeart as fasHeart,
  faChevronDown,
  faEllipsisH
} from '@fortawesome/free-solid-svg-icons'
import { faCalendarAlt, faHeart, faCommentDots } from '@fortawesome/free-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faPlus);
library.add(faEnvelope);
library.add(faCalendarAlt);
library.add(faMapMarkerAlt);
library.add(faPhone);
library.add(faUserGraduate);
library.add(faBook);
library.add(faBuilding);
library.add(faHeart);
library.add(faCommentDots);
library.add(fasHeart);
library.add(faChevronDown);
library.add(faEllipsisH);

var app = createApp(App);

app.component('Icon', FontAwesomeIcon)

app.mount('#app')
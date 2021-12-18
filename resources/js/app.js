require('./bootstrap');

import Vue from 'vue';
import Admin_home from './vue/admin_home';
import Admin_resren from './vue/admin_resren';
import User_home from './vue/user_home';
import User_resren from './vue/user_resren';

import VueObserveVisibility from 'vue-observe-visibility';

Vue.use(VueObserveVisibility);

import { library } from '@fortawesome/fontawesome-svg-core';
import {
    faPlusSquare,
    faTrash,
    faPencilAlt,
    faCut,
    faWindowClose,
    faWind,
    faSave,
    faSearch,
    faPlus,
    faCheck,
    faTimes,
    faBook,
    faBookReader,
    faClock,
    faBackward,
    faForward,
    faAngleDown,
    faUser,
    faHammer,
    faMinus,
    faArrowsAltV,
    faCheckCircle,
    faExclamationCircle
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import 'vue-search-select/dist/VueSearchSelect.css';

library.add(
    faPlusSquare,
    faTrash,
    faPencilAlt,
    faCut,
    faWindowClose,
    faSave,
    faSearch,
    faPlus,
    faCheck,
    faTimes,
    faBook,
    faBookReader,
    faClock,
    faBackward,
    faForward,
    faAngleDown,
    faUser,
    faHammer,
    faMinus,
    faArrowsAltV,
    faCheckCircle,
    faExclamationCircle
);

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.directive('click-outside', {
    bind(el, { value }) {
        el._handler = (e) => {
            if (!el.contains(e.target)) {
                value(e);
            }
        };

        // *** Using capturing ***
        document.addEventListener('click', el._handler, { capture: true });
    },

    unbind(el) {
        document.removeEventListener('click', el._handler, { capture: true });
    }
});

const admin_home = new Vue({
    el: '#admin_home',
    components: { Admin_home }
});

const admin_resren = new Vue({
    el: '#admin_resren',
    components: { Admin_resren }
});

const user_home = new Vue({
    el: '#user_home',
    components: { User_home }
});

const user_resren = new Vue({
    el: '#user_resren',
    components: { User_resren }
});

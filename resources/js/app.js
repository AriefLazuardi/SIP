import "./bootstrap";

import.meta.glob(["../images/**"]);

import Alpine from "alpinejs";

import Swal from "sweetalert2";

window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();

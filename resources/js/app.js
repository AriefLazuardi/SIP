import "./bootstrap";

import.meta.glob(["../images/**"]);

import ChartDataLabels from "chartjs-plugin-datalabels";

import { Chart } from "chart.js";
import "chart.js/auto";

import Alpine from "alpinejs";

import Swal from "sweetalert2";

import flatpickr from "flatpickr";

import "flatpickr/dist/flatpickr.min.css";

window.Chart = Chart;
window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();

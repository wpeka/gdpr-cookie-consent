import Vue from "vue";
import VueApexCharts from "vue-apexcharts";

Vue.use(VueApexCharts);
Vue.component("apexchart", VueApexCharts);

var gdpr_ab_testing_banner1_noChoice = settings_obj.ab_options.hasOwnProperty(
  "noChoice1"
)
  ? settings_obj.ab_options["noChoice1"]
  : 0;
var gdpr_ab_testing_banner2_noChoice = settings_obj.ab_options.hasOwnProperty(
  "noChoice2"
)
  ? settings_obj.ab_options["noChoice2"]
  : 0;

var gdpr_ab_testing_banner1_accept = settings_obj.ab_options.hasOwnProperty(
  "accept1"
)
  ? settings_obj.ab_options["accept1"]
  : 0;
var gdpr_ab_testing_banner2_accept = settings_obj.ab_options.hasOwnProperty(
  "accept2"
)
  ? settings_obj.ab_options["accept2"]
  : 0;

var gdpr_ab_testing_banner1_acceptAll = settings_obj.ab_options.hasOwnProperty(
  "acceptAll1"
)
  ? settings_obj.ab_options["acceptAll1"]
  : 0;
var gdpr_ab_testing_banner2_acceptAll = settings_obj.ab_options.hasOwnProperty(
  "acceptAll2"
)
  ? settings_obj.ab_options["acceptAll2"]
  : 0;

var gdpr_ab_testing_banner1_reject = settings_obj.ab_options.hasOwnProperty(
  "reject1"
)
  ? settings_obj.ab_options["reject1"]
  : 0;
var gdpr_ab_testing_banner2_reject = settings_obj.ab_options.hasOwnProperty(
  "reject2"
)
  ? settings_obj.ab_options["reject2"]
  : 0;

var gdpr_ab_testing_banner1_bypass = settings_obj.ab_options.hasOwnProperty(
  "bypass1"
)
  ? settings_obj.ab_options["bypass1"]
  : 0;
var gdpr_ab_testing_banner2_bypass = settings_obj.ab_options.hasOwnProperty(
  "bypass2"
)
  ? settings_obj.ab_options["bypass2"]
  : 0;

var gdpr_ab_testing_banner1_name = settings_obj.the_options["cookie_bar1_name"];
var gdpr_ab_testing_banner2_name = settings_obj.the_options["cookie_bar2_name"];

export default Vue.component("ab-testing-chart", {
  template: `
    <div>
      <apexchart class="ab_testing_analytics" type="bar" height="500" :options="chartOptions" :series="series"></apexchart>
    </div>
  `,

  data() {
    const series = [
      {
        name: gdpr_ab_testing_banner1_name,
        data: [
          gdpr_ab_testing_banner1_accept + gdpr_ab_testing_banner1_acceptAll,
          gdpr_ab_testing_banner1_noChoice +
            gdpr_ab_testing_banner1_reject +
            gdpr_ab_testing_banner1_bypass,
        ],
      },
      {
        name: gdpr_ab_testing_banner2_name,
        data: [
          gdpr_ab_testing_banner2_accept + gdpr_ab_testing_banner2_acceptAll,
          gdpr_ab_testing_banner2_noChoice +
            gdpr_ab_testing_banner2_bypass +
            gdpr_ab_testing_banner2_reject,
        ],
      },
    ];
    return {
      series,
      chartOptions: {
        chart: {
          type: "bar",
          height: 500,
          width: 700,
          toolbar: {
            show: false, // Disable the menu
          },
        },
        colors: ["#0059B3", "#14873E"],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "85%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          show: true,
          width: 5,
          colors: ["transparent"],
        },
        xaxis: {
          categories: ["Positives", "Negatives"],
          labels: {
            style: {
              colors: ["#1b770f", "#b81717"],
              fontSize: "12px",
            },
          },
        },
        yaxis: {
          title: {
            text: "Conversions",
          },
        },
        grid: {
          show: true,
          borderColor: "#6b6b6b",
          strokeDashArray: 1,
          position: "back",
          xaxis: {
            lines: {
              show: true, // Enable vertical grid lines
            },
          },
          yaxis: {
            lines: {
              show: true, // Enable horizontal grid lines
            },
          },
        },
        fill: {
          opacity: 1,
        },
        tooltip: {
          theme: "dark",

          y: {
            formatter: function (val) {
              return val;
            },
          },
          onDatasetHover: {
            highlightDataSeries: false,
          },
          x: {
            show: true,
            format: "dd MMM",
            formatter: function (val) {
              return "Category: " + val;
            },
          },
          marker: {
            show: true,
          },
        },
      },
    };
  },
  mounted() {
    // Custom CSS for tooltip text color
    const style = document.createElement("style");
    style.type = "text/css";
    style.innerHTML = `
      .apexcharts-tooltip {
        color: #ffffff !important;
      }
    `;
    document.getElementsByTagName("head")[0].appendChild(style);
  },
});

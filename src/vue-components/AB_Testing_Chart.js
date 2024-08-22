import Vue from "vue";
import VueApexCharts from "vue-apexcharts";

Vue.use(VueApexCharts);
Vue.component("apexchart", VueApexCharts);

var gdpr_ab_testing_banner1_DNT = settings_obj.ab_options.hasOwnProperty("DNT1")
  ? settings_obj.ab_options["DNT1"]
  : 0;
var gdpr_ab_testing_banner1_noChoice = settings_obj.ab_options.hasOwnProperty(
  "noChoice1"
)
  ? settings_obj.ab_options["noChoice1"]
  : 0;
var gdpr_ab_testing_banner1_noWarning = settings_obj.ab_options.hasOwnProperty(
  "noWarning1"
)
  ? settings_obj.ab_options["noWarning1"]
  : 0;
var gdpr_ab_testing_banner1_necessary = settings_obj.ab_options.hasOwnProperty(
  "necessary1"
)
  ? settings_obj.ab_options["necessary1"]
  : 0;
var gdpr_ab_testing_banner1_analytics = settings_obj.ab_options.hasOwnProperty(
  "analytics1"
)
  ? settings_obj.ab_options["analytics1"]
  : 0;
var gdpr_ab_testing_banner1_marketing = settings_obj.ab_options.hasOwnProperty(
  "marketing1"
)
  ? settings_obj.ab_options["marketing1"]
  : 0;
var gdpr_ab_testing_banner2_DNT = settings_obj.ab_options.hasOwnProperty("DNT2")
  ? settings_obj.ab_options["DNT2"]
  : 0;
var gdpr_ab_testing_banner2_noChoice = settings_obj.ab_options.hasOwnProperty(
  "noChoice2"
)
  ? settings_obj.ab_options["noChoice2"]
  : 0;
var gdpr_ab_testing_banner2_noWarning = settings_obj.ab_options.hasOwnProperty(
  "noWarning2"
)
  ? settings_obj.ab_options["noWarning2"]
  : 0;
var gdpr_ab_testing_banner2_necessary = settings_obj.ab_options.hasOwnProperty(
  "necessary2"
)
  ? settings_obj.ab_options["necessary2"]
  : 0;
var gdpr_ab_testing_banner2_analytics = settings_obj.ab_options.hasOwnProperty(
  "analytics2"
)
  ? settings_obj.ab_options["analytics2"]
  : 0;
var gdpr_ab_testing_banner2_marketing = settings_obj.ab_options.hasOwnProperty(
  "marketing2"
)
  ? settings_obj.ab_options["marketing2"]
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
          gdpr_ab_testing_banner1_DNT,
          gdpr_ab_testing_banner1_noChoice,
          gdpr_ab_testing_banner1_noWarning,
          gdpr_ab_testing_banner1_necessary,
          gdpr_ab_testing_banner1_marketing,
          gdpr_ab_testing_banner1_analytics,
        ],
      },
      {
        name: gdpr_ab_testing_banner2_name,
        data: [
          gdpr_ab_testing_banner2_DNT,
          gdpr_ab_testing_banner2_noChoice,
          gdpr_ab_testing_banner2_noWarning,
          gdpr_ab_testing_banner2_necessary,
          gdpr_ab_testing_banner2_marketing,
          gdpr_ab_testing_banner2_analytics,
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
          categories: [
            "Do Not Track",
            "No Choice",
            "No Warning",
            "Necessary",
            "Marketing",
            "Analytics",
          ],
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

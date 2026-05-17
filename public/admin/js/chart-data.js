var randomScalingFactor = function () {
	return Math.round(Math.random() * 1000);
};
function getPrevious7Months() {
	const months = [
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December",
	];

	const currentMonth = new Date().getMonth(); // 0-indexed: 0 = Jan, 5 = June, etc.
	const result = [];

	for (let i = 6; i >= 0; i--) {
		// Tính tháng lùi i bước, và +12 rồi %12 để không bị âm
		const monthIndex = (currentMonth - i + 12) % 12;
		result.push(months[monthIndex]);
	}

	return result;
}

const el = document.getElementById('userStats');
const info = el ? JSON.parse(el.dataset.info) : {};

const chartLabels =
	info.labels && info.labels.length ? info.labels : getPrevious7Months();
const chartOrders =
	info.dailyOrders && info.dailyOrders.length
		? info.dailyOrders.map((v) => parseInt(v, 10) || 0)
		: [0, 0, 0, 0, 0, 0, parseInt(info.totalUsers, 10) || 0];
const chartNewUsers =
	info.dailyNewUsers && info.dailyNewUsers.length
		? info.dailyNewUsers.map((v) => parseInt(v, 10) || 0)
		: [0, 0, 0, 0, 0, 0, parseInt(info.newUsers, 10) || 0];

var lineChartData = {
	labels: chartLabels,
	datasets: [
		{
			label: "Đơn hàng",
			fillColor: "rgba(220,220,220,0.2)",
			strokeColor: "rgba(220,220,220,1)",
			pointColor: "rgba(220,220,220,1)",
			pointStrokeColor: "#fff",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgba(220,220,220,1)",
			data: chartOrders,
		},
		{
			label: "Khách mới",
			fillColor: "rgba(48, 164, 255, 0.2)",
			strokeColor: "rgba(48, 164, 255, 1)",
			pointColor: "rgba(48, 164, 255, 1)",
			pointStrokeColor: "#fff",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgba(48, 164, 255, 1)",
			data: chartNewUsers,
		},
	],
};

var barChartData = {
	labels: ["January", "February", "March", "April", "May", "June", "July"],
	datasets: [
		{
			fillColor: "rgba(220,220,220,0.5)",
			strokeColor: "rgba(220,220,220,0.8)",
			highlightFill: "rgba(220,220,220,0.75)",
			highlightStroke: "rgba(220,220,220,1)",
			data: [
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
			],
		},
		{
			fillColor: "rgba(48, 164, 255, 0.2)",
			strokeColor: "rgba(48, 164, 255, 0.8)",
			highlightFill: "rgba(48, 164, 255, 0.75)",
			highlightStroke: "rgba(48, 164, 255, 1)",
			data: [
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
				randomScalingFactor(),
			],
		},
	],
};

var pieData = [
	{
		value: 300,
		color: "#30a5ff",
		highlight: "#62b9fb",
		label: "Blue",
	},
	{
		value: 50,
		color: "#ffb53e",
		highlight: "#fac878",
		label: "Orange",
	},
	{
		value: 100,
		color: "#1ebfae",
		highlight: "#3cdfce",
		label: "Teal",
	},
	{
		value: 120,
		color: "#f9243f",
		highlight: "#f6495f",
		label: "Red",
	},
];

var doughnutData = [
	{
		value: 300,
		color: "#30a5ff",
		highlight: "#62b9fb",
		label: "Blue",
	},
	{
		value: 50,
		color: "#ffb53e",
		highlight: "#fac878",
		label: "Orange",
	},
	{
		value: 100,
		color: "#1ebfae",
		highlight: "#3cdfce",
		label: "Teal",
	},
	{
		value: 120,
		color: "#f9243f",
		highlight: "#f6495f",
		label: "Red",
	},
];

window.onload = function () {
	var lineChartEl = document.getElementById("line-chart");
	if (lineChartEl) {
		window.myLine = new Chart(lineChartEl.getContext("2d")).Line(lineChartData, {
			responsive: true,
		});
	}

	var barChartEl = document.getElementById("bar-chart");
	if (barChartEl) {
		window.myBar = new Chart(barChartEl.getContext("2d")).Bar(barChartData, {
			responsive: true,
		});
	}

	var doughnutChartEl = document.getElementById("doughnut-chart");
	if (doughnutChartEl) {
		window.myDoughnut = new Chart(doughnutChartEl.getContext("2d")).Doughnut(
			doughnutData,
			{ responsive: true }
		);
	}

	var pieChartEl = document.getElementById("pie-chart");
	if (pieChartEl) {
		window.myPie = new Chart(pieChartEl.getContext("2d")).Pie(pieData, {
			responsive: true,
		});
	}
};

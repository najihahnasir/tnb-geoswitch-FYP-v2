function createChart(
    chartSelector,
    chartOptions,
    chartFilter,
    chartOptionZero
) {
    return {
        chart: null,
        options: chartOptions,
        filter: chartFilter,
        zeroOptions: chartOptionZero,
        render: function (option) {
            this.chart = new ApexCharts(
                document.querySelector(chartSelector),
                option
            );
            this.chart.render();
        },
    };
}

var kontraktorChart = createChart(
    "#kontraktor",
    {
        series: [5, 8, 10],
        chart: {
            height: 390,
            type: "radialBar",
        },
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: {
                    margin: 5,
                    size: "30%",
                    background: "transparent",
                    image: undefined,
                },
                dataLabels: {
                    name: {
                        show: false,
                    },
                    value: {
                        show: false,
                    },
                },
            },
        },
        colors: ["#884EA0", "#F4D03F", "#1E8449"],
        labels: ["Pembalakan", "KKJ", "Pembangunan Hutan"],
        legend: {
            show: true,
            floating: true,
            fontSize: "16px",
            position: "left",
            offsetX: 1,
            offsetY: 15,
            labels: {
                useSeriesColors: true,
            },
            markers: {
                size: 0,
            },
            formatter: function (seriesName, opts) {
                return (
                    seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]
                );
            },
            itemMargin: {
                vertical: 3,
            },
        },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    legend: {
                        show: false,
                    },
                },
            },
        ],
    },
    function (data) {
        const kontraktorChartData = {
            pembalakan: 0,
            KKJ: 0,
            pembangunan: 0,
        };

        data.forEach((item) => {
            switch (item.jenis_pendaftaran) {
                case 1:
                    kontraktorChartData.pembalakan++;
                    break;
                case 2:
                    kontraktorChartData.KKJ++;
                    break;
                case 3:
                    kontraktorChartData.pembangunan++;
                    break;
            }
        });

        kontraktorChart.options.series = [
            kontraktorChartData.pembalakan,
            kontraktorChartData.KKJ,
            kontraktorChartData.pembangunan,
        ];
    },
    {}
);

var kelulusanPermohonanChart = createChart(
    "#kelulusan-permohonan",
    {
        series: [],
        chart: {
            type: "donut",
        },
        colors: ["#1E8449", "#F4D03F"],
        labels: ["HSK", "TK"],
        responsive: [
            {
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200,
                        height: 200,
                    },
                    legend: {
                        position: "bottom",
                        formatter: function (seriesName, opts) {
                            return (
                                seriesName +
                                ":  " +
                                opts.w.globals.series[opts.seriesIndex]
                            );
                        },
                    },
                },
            },
        ],
    },
    function (data) {
        const lulusPermohonanChartData = {
            tk: 0,
            hsk: 0,
        };

        data.forEach((item) => {
            switch (item.status_tanah) {
                case "tk":
                    lulusPermohonanChartData.tk++;
                    break;
                case "hsk":
                    lulusPermohonanChartData.hsk++;
                    break;
            }
        });

        kelulusanPermohonanChart.options.series = [
            lulusPermohonanChartData.hsk,
            lulusPermohonanChartData.tk,
        ];
    },
    {
        series: [],
        chart: {
            type: "donut",
        },
        labels: ["HSK", "TK"],
        colors: ["#1E8449", "#F4D03F"],
        noData: {
            text: "Tiada rekod data",
            align: "center",
            verticalAlign: "middle",
            offsetX: 0,
            offsetY: 0,
            style: {
                fontSize: "12px",
                fontFamily: "Helvetica Neue,Helvetica,Arial,sans-serif",
            },
        },
    }
);

var royaltiChart = createChart(
    "#royalti-ses",
    {
        series: [
            {
                name: "Royalti dan Ses",
                data: [10, 41, 35, 51, 49, 62, 69, 91, 148],
            },
        ],
        chart: {
            height: 350,
            type: "line",
            zoom: {
                enabled: false,
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
        },
        title: {
            text: "RM",
            align: "left",
        },
        grid: {
            row: {
                colors: ["#f3f3f3", "transparent"], // takes an array which will be repeated on columns
                opacity: 0.5,
            },
        },
        xaxis: {
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
            ],
        },
    },
    function (data) {},
    {}
);

var tandaPokokChart = createChart(
    "#tanda-pokok",
    {
        series: [
            {
                data: [
                    {
                        x: "Pokok Tebangan",
                        y: 400,
                    },
                    {
                        x: "Pokok Ibu",
                        y: 250,
                    },
                    {
                        x: "Pokok Perlindungan",
                        y: 350,
                    },
                    {
                        x: "Pokok Jalan",
                        y: 448,
                    },
                    {
                        x: "Pokok Matau",
                        y: 540,
                    },
                    {
                        x: "Pokok Kongsi",
                        y: 510,
                    },
                ],
            },
        ],
        chart: {
            type: "bar",
            height: 350,
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true,
            },
        },
        dataLabels: {
            enabled: false,
        },
        xaxis: {
            categories: [
                "Pokok Tebangan",
                "Pokok Ibu",
                "Pokok Perlindungan",
                "Pokok Jalan",
                "Pokok Matau",
                "Pokok Kongsi",
            ],
        },
    },
    function (data) {},
    {}
);

var initialRender = () => {
    kontraktorChart.filter(kontraktorData);
    kontraktorChart.render(kontraktorChart.options);

    kelulusanPermohonanChart.filter(lulusPermohonanData);
    kelulusanPermohonanChart.render(kelulusanPermohonanChart.options);

    const lulusPermohonanChartData = {
        tk: 0,
        hsk: 0,
    };

    lulusPermohonanData.forEach((item) => {
        switch (item.status_tanah) {
            case "tk":
                lulusPermohonanChartData.tk++;
                break;
            case "hsk":
                lulusPermohonanChartData.hsk++;
                break;
        }
    });
    
    if (lulusPermohonanChartData.tk == 0 && lulusPermohonanChartData.hsk == 0) {
        kelulusanPermohonanChart.chart.destroy();
        kelulusanPermohonanChart.render(kelulusanPermohonanChart.zeroOptions);
    }

    royaltiChart.render(royaltiChart.options);
    tandaPokokChart.render(tandaPokokChart.options);
};

var update = (type, data) => {
    var chartToUpdate;

    switch (type) {
        case "kontraktor":
            chartToUpdate = kontraktorChart;
            break;
        case "kelulusan-permohonan":
            chartToUpdate = kelulusanPermohonanChart;
            break;
    }

    if (chartToUpdate) {
        chartToUpdate.chart.destroy();
        chartToUpdate.filter(data);
        chartToUpdate.render(chartToUpdate.options);
    }
};

var setZero = (type) => {
    var chartToSetZero;

    switch (type) {
        case "kontraktor":
            chartToSetZero = kontraktorChart;
            break;
        case "kelulusan-permohonan":
            chartToSetZero = kelulusanPermohonanChart;
            break;
    }

    if (chartToSetZero) {
        chartToSetZero.chart.destroy();
        chartToSetZero.render(chartToSetZero.zeroOptions);
    }
};

var apexChart = {
    initialRender: initialRender,
    update: update,
    setZero: setZero,
};

apexChart.initialRender();

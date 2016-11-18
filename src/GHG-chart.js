
var GHGChart = function (element, data) {

    if (element == null) {
        throw { message: "Invalid element", value: element };
    }
    if (data == null) {
        throw { message: "Invalid data", value: data };
    }

    this.data = data;
    this.chartDiv = element;
    this.colLeft;
    this.colRight;
    this.row;

    this.init = function () {
        this.addRow();
        this.createCharts(data);
    };

    this.getColor = function (n) {
        var color = [
            '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#9966ff', '#ffff99', '#66ff99', '#ff6699', '#d2d6de'
        ];
        return color[(n % color.length)];
    };

    this.createCharts = function (data) {
        for (var i = 0; i < data.results.length; i++) {
            var chart = data.results[i];
            if (chart.typeid == 1)
                this.addBarLineChart(i, chart);
            else if (chart.typeid == 2)
                this.addPieChart(i, chart);
        }
    };

    this.addRow = function () {
        if (this.chartDiv == null)
            return;
        this.row = document.createElement('div');
        this.colLeft = document.createElement('div');
        this.colRight = document.createElement('div')
        $(this.row).addClass('row').append($(this.colLeft).addClass('col-md-6')).append($(this.colRight).addClass('col-md-6')).appendTo(this.chartDiv); //main div    
    };
    this.addBarLineChart = function (numChart, chart) {
        var chartData = {};
        chartData.labels = chart.labels;
        chartData.datasets = [];
        for (var j = 0; j < chart.data.length; j++) {
            var tempData = chart.data[j];
            var tempdataset = {
                type: tempData.type,
                label: tempData.label,
                data: tempData.data,
                fill: false,
                backgroundColor: this.getColor(j),
                borderColor: this.getColor(j),
                hoverBackgroundColor: this.getColor(j),
                hoverBorderColor: this.getColor(j),
                yAxisID: 'y-axis-1'
            }
            if (tempData.type == "line") {
                tempdataset.yAxisID = 'y-axis-2';
                tempdataset.borderColor = this.getColor(j);
                tempdataset.backgroundColor = this.getColor(j);
                tempdataset.pointBorderColor = this.getColor(j);
                tempdataset.pointBackgroundColor = this.getColor(j);
                tempdataset.pointHoverBackgroundColor = this.getColor(j);
                tempdataset.pointHoverBorderColor = this.getColor(j);
            }
            chartData.datasets.push(tempdataset);
        }

        var chartDiv = '<div class="box box-primary filter ' + chart.filterType + '">' +
            '<div class="box-header with-border">' +
            '<h3 class="box-title" id="chartTitle' + numChart + '">' + chart.title + '</h3>' +
            '<!-- BAR CHART -->' +
            '<div class="box-tools pull-right">' +
            '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>' +
            '</button>' +
            '<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>' +
            '</div>' +
            '</div>' +
            '<div class="box-body">' +
            '<div class="chart">' +
            '<canvas id="barChart' + numChart + '" style="height:250px"></canvas>' +
            '</div>' +
            '</div>' +
            '<!-- /.box-body -->' +
            '</div>' +
            '<!-- /.box -->';

        if (numChart % 2 == 0)
            $(this.colLeft).append(chartDiv);
        else
            $(this.colRight).append(chartDiv);

        var ctx = document.getElementById("barChart" + numChart).getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'label'
                },
                elements: {
                    line: {
                        fill: false
                    }
                },
                scales: {
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        labels: {
                            show: true,
                        }
                    }],
                    yAxes: [{
                        type: "linear",
                        display: true,
                        position: "left",
                        id: "y-axis-1",
                        gridLines: {
                            display: false
                        },
                        labels: {
                            show: true,

                        }
                    }, {
                        type: "linear",
                        display: true,
                        position: "right",
                        id: "y-axis-2",
                        gridLines: {
                            display: false
                        },
                        labels: {
                            show: true,

                        }
                    }]
                }
            }
        });
    };


    this.barChartCount = 0;
    this.addPieChart = function (numChart, chart) {
        var chartDiv = '<div class="box box-primary filter ' + chart.filterType + '">' +
            '<div class="box-header with-border">' +
            '<h3 class="box-title" id="chartTitle' + numChart + '">' + chart.title + '</h3>' +
            '<!-- BAR CHART -->' +
            '<div class="box-tools pull-right">' +
            '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>' +
            '</button>' +
            '<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>' +
            '</div>' +
            '</div>' +
            '<div class="box-body">' +
            '<div class="chart">';
        for (var i = 0; i < chart.data.length; i++) {
            chartDiv += '<canvas id="pieChart' + (this.barChartCount + i) + '" style="height:250px"></canvas>';
        }
        chartDiv += '</div>' +
            '</div>' +
            '<!-- /.box-body -->' +
            '</div>' +
            '<!-- /.box -->';

        if (numChart % 2 == 0)
            $(this.colLeft).append(chartDiv);
        else
            $(this.colRight).append(chartDiv);
        var n = 0;
        for (var i = 0; i < chart.data.length; i++) {
            var data = chart.data[i];
            var PieData = {};
            var backgroundColor = [];
            for (var j = 0; j < data.labels.length; j++ , n++) {
                backgroundColor.push(this.getColor(n));
            }
            var PieData = {
                labels: data.labels,
                datasets: [
                    {
                        data: data.data,
                        backgroundColor: backgroundColor,
                        hoverBackgroundColor: backgroundColor
                    }]
            };
            var pieOptions = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke: true,
                //String - The colour of each segment stroke
                segmentStrokeColor: "#fff",
                //Number - The width of each segment stroke
                segmentStrokeWidth: 2,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 50, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps: 100,
                //String - Animation easing effect
                animationEasing: "easeOutBounce",
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate: true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale: false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
            };

            var ctx = $("#pieChart" + (this.barChartCount + i)).get(0).getContext("2d");
            var pieChart = new Chart(ctx, {
                type: 'pie',
                data: PieData,
                options: pieOptions
            });
        }
        this.barChartCount += chart.data.length;
    };
    return this;
};



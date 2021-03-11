$(document).ready(function() {

$('.changePeriod').click(function() {
    getDiagramsByPeriod($(this).attr('data-period'))
});

function getDiagramsByPeriod(per)
{
    $.ajax({
        url : '/admin/getDiagrams',
        data : {
            period : per
        },
        type : 'post',
        success : function (data) {

            console.log(data);

           if(typeof window.sales != 'undefined') window.sales.destroy();
           if(typeof window.team != 'undefined') window.team.destroy();

            var sales = {
                type: 'line',
                data: {
                    labels: data.x,
                    type: 'line',
                    defaultFontFamily: 'Montserrat',
                    datasets: [{
                        label: "Registrations",
                        data: data.u,
                        backgroundColor: 'transparent',
                        borderColor: '#e6a1f2',
                        borderWidth: 3,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#e6a1f2',

                            }, {
                        label: "Online",
                        data: data.o,
                        backgroundColor: 'transparent',
                        borderColor: '#ed7f7e',
                        borderWidth: 3,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#ed7f7e',
                    },
                    {
                label: "Users",
                data: data.t,
                backgroundColor: 'transparent',
                borderColor: '#8fc9fb',
                borderWidth: 3,
                pointStyle: 'circle',
                pointRadius: 5,
                pointBorderColor: 'transparent',
                pointBackgroundColor: '#8fc9fb',
                    }]
                },
                options: {
                    responsive: true,

                    tooltips: {
                        mode: 'index',
                        titleFontSize: 12,
                        titleFontColor: '#000',
                        bodyFontColor: '#000',
                        backgroundColor: '#fff',
                        titleFontFamily: 'Montserrat',
                        bodyFontFamily: 'Montserrat',
                        cornerRadius: 3,
                        intersect: false,
                    },
                    legend: {
                        labels: {
                            usePointStyle: true,
                            fontFamily: 'Montserrat',
                        },
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            scaleLabel: {
                                display: false,
                                labelString: 'Month'
                            }
                                }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            }
                                }]
                    },
                    title: {
                        display: false,
                        text: 'Normal Legend'
                    }
                }
            };

            var team = {
                type: 'line',
                data: {
                    labels: data.x,
                    type: 'line',
                    defaultFontFamily: 'Montserrat',
                    datasets: [{
                        label: "Deposits",
                        data: data.d,
                        backgroundColor: 'transparent',
                        borderColor: '#e86add',
                        borderWidth: 1,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#e86add',
                            }, {
                        label: "Withdraws [C]",
                        data: data.w,
                        backgroundColor: 'transparent',
                        borderColor: '#e8bb6a',
                        borderWidth: 1,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#e8bb6a',
                            },
                            {
                        label: "Withdraws [A]",
                        data: data.wc,
                        backgroundColor: 'transparent',
                        borderColor: '#a1f2b3',
                        borderWidth: 1,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#a1f2b3',
                            },
                            {
                        label: "Withdraws [D]",
                        data: data.wd,
                        backgroundColor: 'transparent',
                        borderColor: '#ed7f7e',
                        borderWidth: 1,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#ed7f7e',
                            },
                            {
                        label: "Withdraws [O]",
                        data: data.wo,
                        backgroundColor: 'transparent',
                        borderColor: '#8fc9fb',
                        borderWidth: 1,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: '#8fc9fb',
                            }
                        ]
                },
                options: {
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                        titleFontSize: 12,
                        titleFontColor: '#000',
                        bodyFontColor: '#000',
                        backgroundColor: '#fff',
                        titleFontFamily: 'Montserrat',
                        bodyFontFamily: 'Montserrat',
                        cornerRadius: 3,
                        intersect: false,
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            fontFamily: 'Montserrat',
                        },


                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            scaleLabel: {
                                display: false,
                                labelString: 'Month'
                            }
                                }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            }
                                }]
                    },
                    title: {
                        display: false,
                    }
                }
            };


            window.sales = new Chart($("#sales-chart"), sales);
            window.team = new Chart($("#team-chart"), team);
        },
        error : function (err) {
            toastr.error('Ошибка при загрузке графиков!');
            console.log(err.responseText);
        }
    });
}

getDiagramsByPeriod('month');

    // window.onload = function () {
    //     // var ctx = $("#sales-chart").getContext("2d");
    //     window.myLine = new Chart($("#sales-chart"), sales);
    //
    //     // var ctx2 = $("#team-chart").getContext("2d");
    //     window.myLine = new Chart($("#team-chart"), team);
    // };
});

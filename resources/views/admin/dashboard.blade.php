<x-app-layout>
    <div class="container-fluid">
        <!-- Print Rekapan & Filter Bulan/Tahun -->
        <div class="gap-3 mb-4 d-flex align-items-center flex-wrap">
            <form id="printForm" action="{{ route('dashboard.print') }}" method="GET" target="_blank" class="gap-2 d-flex align-items-center flex-wrap">
                <label for="bulan" class="mb-0">Bulan</label>
                <select name="bulan" id="bulan" class="form-select" style="width:auto;">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <label for="tahun" class="mb-0 ms-2">Tahun</label>
                <select name="tahun" id="tahun" class="form-select" style="width:auto;">
                    @foreach(range(now()->year-5, now()->year) as $y)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary ms-2">Print Rekapan Bulanan</button>
            </form>
            <form id="printYearForm" action="{{ route('dashboard.print') }}" method="GET" target="_blank" class="d-flex align-items-center gap-2">
                <input type="hidden" name="tahun" value="{{ now()->year }}">
                <input type="hidden" name="bulanan" value="0">
                <button type="submit" class="btn btn-success">Print Rekapan Tahunan</button>
            </form>
        </div>

        <!-- Content Row -->
        <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="mb-4 col-xl-3 col-md-6">
                <div class="py-2 shadow card border-left-primary h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="mr-2 col">
                                <div class="mb-1 text-xs font-weight-bold text-primary text-uppercase">
                                    Earnings (Monthly)
                                </div>
                                <div class="mb-0 text-gray-800 h5 font-weight-bold">
                                    Rp {{ number_format($monthlyEarnings, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="text-gray-300 fas fa-calendar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-xl-3 col-md-6">
                <div class="py-2 shadow card border-left-success h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="mr-2 col">
                                <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">
                                    Earnings (Annual)
                                </div>
                                <div class="mb-0 text-gray-800 h5 font-weight-bold">
                                    Rp {{ number_format($annualEarnings, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="text-gray-300 fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-xl-3 col-md-6">
                <div class="py-2 shadow card border-left-warning h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="mr-2 col">
                                <div class="mb-1 text-xs font-weight-bold text-warning text-uppercase">
                                    Total Orders
                                </div>
                                <div class="mb-0 text-gray-800 h5 font-weight-bold">
                                    {{ $totalOrders }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="text-gray-300 fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-xl-3 col-md-6">
                <div class="py-2 shadow card border-left-info h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="mr-2 col">
                                <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">
                                    Most Sold Menu
                                </div>
                                <div class="mb-0 text-gray-800 h5 font-weight-bold">
                                    @if ($topMenu)
                                        {{ $topMenu->nama }} : {{ $topMenu->total }}
                                    @else
                                        No menu sold
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="text-gray-300 fas fa-utensils fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Earnings (Monthly) Card Example -->
            <!-- <div class="mb-4 col-xl-3 col-md-6">
                <div class="py-2 shadow card border-left-info h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="mr-2 col">
                                <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">Tasks
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="mb-0 mr-3 text-gray-800 h5 font-weight-bold">50%</div>
                                    </div>
                                    <div class="col">
                                        <div class="mr-2 progress progress-sm">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                                                aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="text-gray-300 fas fa-clipboard-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Pending Requests Card Example -->
            <!-- <div class="mb-4 col-xl-3 col-md-6">
                <div class="py-2 shadow card border-left-warning h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="mr-2 col">
                                <div class="mb-1 text-xs font-weight-bold text-warning text-uppercase">
                                    Pending Requests</div>
                                <div class="mb-0 text-gray-800 h5 font-weight-bold">18</div>
                            </div>
                            <div class="col-auto">
                                <i class="text-gray-300 fas fa-comments fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Content Row -->

        <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="mb-4 shadow card">
                    <!-- Card Header - Dropdown -->
                    <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Pendapatan Tiap Bulan</h6>
                        {{-- <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="text-gray-400 fas fa-ellipsis-v fa-sm fa-fw"></i>
                            </a>
                            <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Dropdown Header:</div>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </div> --}}
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="myAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="mb-4 shadow card">
                    <!-- Card Header - Dropdown -->
                    <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Metode Pembayaran</h6>
                        {{-- <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="text-gray-400 fas fa-ellipsis-v fa-sm fa-fw"></i>
                            </a>
                            <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Dropdown Header:</div>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </div> --}}
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="pt-4 pb-2 mb-5 chart-pie">
                            <canvas id="myPieChart"></canvas>
                            <div class="mt-4 text-center small" id="paymentMethodLegend">
                                <!-- Legend will be dynamically populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">

            <!-- Content Column -->
            <div class="mb-4 col-lg-6">

            </div>

            <div class="mb-4 col-lg-6">

            </div>
        </div>

    </div>
    <!-- /.container-fluid -->


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Get the total earnings and month labels passed from the controller
            var earningsData = @json($earningsData); // Data from controller
            var months = @json($months); // Month labels from controller

            // Area Chart Example
            var ctxArea = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctxArea, {
                type: 'line',
                data: {
                    labels: months, // Months of the current year
                    datasets: [{
                        label: "Pendapatan Tiap Bulan",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: earningsData, // Monthly earnings data
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) {
                                    return 'Rp.' + value
                                        .toLocaleString(); // Format numbers with currency symbol
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Rp.' + tooltipItem.yLabel
                                    .toLocaleString(); // Format numbers with currency symbol
                            }
                        }
                    }
                }
            });

            var ctxPie = document.getElementById("myPieChart");

            // Data passed from the controller
            var paymentMethods = @json($paymentMethods); // Payment methods (labels)
            var orderCounts = @json($orderCounts); // Order counts for each payment method
            console.log(paymentMethods);
            // Colors for each payment method (can be adjusted or dynamically generated)
            var colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f1c40f', '#e74c3c', '#8e44ad', '#2ecc71', '#3498db'];

            var myPieChart = new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: paymentMethods, // Use payment methods as labels
                    datasets: [{
                        data: orderCounts, // Use order counts for each payment method
                        backgroundColor: colors.slice(0, paymentMethods.length), // Assign colors
                        hoverBackgroundColor: colors.slice(0, paymentMethods.length),
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false // Hide default legend (we'll create our own below)
                    },
                    cutoutPercentage: 80,
                },
            });

            var legendContainer = document.getElementById("paymentMethodLegend");
            paymentMethods.forEach((method, index) => {
                var legendItem = document.createElement("span");
                legendItem.classList.add("mr-2");

                // Create colored circle and label
                legendItem.innerHTML = `<i class="fas fa-circle" style="color: ${colors[index]}"></i> ${method}`;
                legendContainer.appendChild(legendItem);
            });
        </script>
    @endpush
</x-app-layout>

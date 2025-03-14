@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('plugins.Chartjs', true)
@section('plugins.Select2', true)

@section('content')

    <div class="row">
        {{-- Bar Chart Section --}}

        <div class="col">
            <x-adminlte-card title="Jumlah MR per Tahun" theme="info" collapsible>
                <div class="col-md-12 mb-3">
                    <form id="filter-form">
                        <div class="form-row d-flex align-items-center justify-content-center">
                            <div class="form-group col-sm-5">
                                <label for="year">Pilih Tahun</label>
                                <x-adminlte-select2 name="year" id="year-select" class="form-control">
                                    @foreach ($availableMonths->pluck('year')->unique() as $availableYear)
                                        <option value="{{ $availableYear }}"
                                            {{ $availableYear == $year ? 'selected' : '' }}>
                                            {{ $availableYear }}
                                        </option>
                                    @endforeach
                                </x-adminlte-select2>
                            </div>
                        </div>
                    </form>
                </div>
                <div style="width: 100%; height: 300px;">
                    <canvas id="monthlyMRChart"></canvas>
                </div>
            </x-adminlte-card>
        </div>
    </div>
    <div class="row">
        {{-- Pie Chart Section --}}
        <div class="col">
            <x-adminlte-card title="Status Item per Bulan" theme="info" collapsible>
                {{-- Filter Form --}}
                <div class="col-md-12 mb-3">
                    <form id="filter-form">
                        <div class="form-row d-flex align-items-center justify-content-center">
                            <div class="form-group col-sm-5">
                                <label for="month">Pilih Bulan</label>
                                <x-adminlte-select2 name="month" id="month-select" class="form-control">
                                    @foreach ($availableMonths->where('year', $year)->pluck('month') as $availableMonth)
                                        <option value="{{ str_pad($availableMonth, 2, '0', STR_PAD_LEFT) }}"
                                            {{ $availableMonth == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $availableMonth)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </x-adminlte-select2>
                            </div>
                        </div>
                    </form>
                </div>
                <div style="width: 100%; height: 300px;">
                    <canvas id="materialRequestStatusChart"></canvas>
                </div>
            </x-adminlte-card>
        </div>
    </div>
    <div class="row">
        {{-- Top 5 Most Requested Items --}}
        <div class="col">
            <x-adminlte-card title="Top 5 Item Terbanyak yang Diminta" theme="info" collapsible>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="topItemsTable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi dari API -->
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Top 5 Projects with Most Requests --}}
        <div class="col">
            <x-adminlte-card title="Top 5 Proyek dengan Permintaan Terbanyak" theme="info" collapsible>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="topProjectsTable">
                        <thead>
                            <tr>
                                <th>Proyek</th>
                                <th>Jumlah Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi dari API -->
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('css')
    <style>
        canvas {
            width: 100% !important;
            height: auto !important;
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearSelect = $('#year-select');
            const monthSelect = $('#month-select');

            let pieChart, barChart;

            // Inisialisasi Dashboard
            function initDashboard() {
                initCharts();
                loadInitialData();
                attachEventListeners();
            }

            // Inisialisasi Chart
            function initCharts() {
                const pieCtx = document.getElementById('materialRequestStatusChart').getContext('2d');
                const barCtx = document.getElementById('monthlyMRChart').getContext('2d');

                pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: { labels: [], datasets: [{ label: 'Material Request Item Status', data: [] }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                barChart = new Chart(barCtx, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { xAxes: [{ stacked: true }], yAxes: [{ stacked: true }] },
                        plugins: {
                            tooltip: { callbacks: { label: context => `${context.dataset.label}: ${context.raw}` } },
                            legend: { position: 'top' }
                        }
                    }
                });
            }

            // Memuat Data Awal
            function loadInitialData() {
                const selectedYear = yearSelect.val();
                updateMonthOptions(selectedYear);
                updateBarChart(selectedYear);
                updatePieChart(selectedYear, monthSelect.val());
                updateTables(selectedYear, monthSelect.val());
            }

            // Memuat Opsi Bulan Berdasarkan Tahun
            function updateMonthOptions(year) {
                fetch(`/dashboard/months/${year}`)
                    .then(response => response.json())
                    .then(data => {
                        monthSelect.empty();
                        data.forEach(month => {
                            const monthValue = String(month).padStart(2, '0');
                            const monthText = new Date(0, month - 1).toLocaleString('default', { month: 'long' });
                            monthSelect.append(new Option(monthText, monthValue));
                        });

                        if (data.length > 0) {
                            const firstMonth = String(data[0]).padStart(2, '0');
                            monthSelect.val(firstMonth).trigger('change');
                        }
                    });
            }

            // Memperbarui Chart Batang (Bar Chart)
            function updateBarChart(year) {
                fetch(`/dashboard/monthly-status-chart/${year}`)
                    .then(response => response.json())
                    .then(data => {
                        barChart.data.labels = data.labels;
                        barChart.data.datasets = data.datasets.map(dataset => ({
                            label: dataset.status, data: dataset.data, backgroundColor: dataset.color
                        }));
                        barChart.update();
                    });
            }

            // Memperbarui Chart Pie
            function updatePieChart(year, month) {
                fetch(`/dashboard/chart/${year}/${month}`)
                    .then(response => response.json())
                    .then(data => {
                        pieChart.data.labels = data.labels;
                        pieChart.data.datasets[0].data = data.data;
                        pieChart.data.datasets[0].backgroundColor = data.colors;
                        pieChart.update();
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }

            // Memperbarui Tabel Top 5 Item
            function updateTopItemsTable(year, month) {
                fetch(`/dashboard/top-items/${year}/${month}`)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.getElementById('topItemsTable').querySelector('tbody');
                        tableBody.innerHTML = '';
                        data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td>${item.description}</td><td>${item.total_quantity}</td>`;
                            tableBody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error fetching top items data:', error));
            }

            // Memperbarui Tabel Top 5 Proyek
            function updateTopProjectsTable(year, month) {
                fetch(`/dashboard/top-projects/${year}/${month}`)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.getElementById('topProjectsTable').querySelector('tbody');
                        tableBody.innerHTML = '';
                        data.forEach(project => {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td>${project.project_name}</td><td>${project.total_quantity}</td>`;
                            tableBody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error fetching top projects data:', error));
            }

            // Memperbarui Semua Tabel
            function updateTables(year, month) {
                updateTopItemsTable(year, month);
                updateTopProjectsTable(year, month);
            }

            // Handler untuk Perubahan Tahun
            function handleYearChange() {
                const selectedYear = this.value;
                updateMonthOptions(selectedYear);
                updateBarChart(selectedYear);
                updatePieChart(selectedYear, monthSelect.val());
                updateTables(selectedYear, monthSelect.val());
            }

            // Handler untuk Perubahan Bulan
            function handleMonthChange() {
                const selectedYear = yearSelect.val();
                const selectedMonth = this.value;
                updatePieChart(selectedYear, selectedMonth);
                updateTables(selectedYear, selectedMonth);
            }

            // Menambahkan Event Listener
            function attachEventListeners() {
                yearSelect.on('change', handleYearChange);
                monthSelect.on('change', handleMonthChange);
            }

            // Jalankan Inisialisasi Dashboard
            initDashboard();
        });
    </script>
@stop

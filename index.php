<?php
if(isset($_POST['url'])) {
    $url = $_POST['url'];
    
    // Menambahkan protokol "https://" jika tidak ada
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "https://" . $url;
    }

    // Fungsi untuk mengukur waktu muat halaman
    function get_page_load_time($url) {
        $start = microtime(true);
        file_get_contents($url);
        $end = microtime(true);
        return $end - $start;
    }
    
    // Fungsi untuk mengambil ukuran halaman
    function get_page_size($url) {
        return strlen(file_get_contents($url));
    }
    
    // Fungsi untuk menghitung jumlah permintaan HTTP
    function get_http_requests($url) {
        $headers = get_headers($url, 1);
        return count($headers);
    }

    // Fungsi untuk mendapatkan kode sumber halaman web
    function get_page_source($url) {
        return htmlspecialchars(file_get_contents($url));
    }

    // Fungsi untuk mendapatkan domain dari URL
    function get_domain($url) {
        $url_parts = parse_url($url);
        return $url_parts['host'];
    }
    
    $page_load_time = get_page_load_time($url);
    $page_size = get_page_size($url);
    $http_requests = get_http_requests($url);
    $page_source = get_page_source($url);
    $domain = get_domain($url);
    
    // Menentukan warna font berdasarkan kriteria
    $load_time_color = $page_load_time <= 2 ? 'green' : 'red'; // Waktu muat kurang dari atau sama dengan 2 detik akan berwarna hijau
    $size_color = $page_size <= 1024 * 1024 ? 'green' : 'red'; // Ukuran halaman kurang dari atau sama dengan 1 MB akan berwarna hijau
    $request_color = $http_requests <= 20 ? 'green' : 'red'; // Jumlah permintaan HTTP kurang dari atau sama dengan 20 akan berwarna hijau
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PageSpeed Analyzer</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        @media screen and (max-width: 600px) {
            table {
                border: 0;
            }
            table caption {
                font-size: 1.3em;
            }
            table thead {
                display: none;
            }
            table tr, table td {
                display: block;
                width: 100%;
            }
            table td {
                text-align: right;
                padding-left: 50%;
                text-align: right;
                position: relative;
            }
            table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: bold;
            }
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 10px 0;
            text-align: center;
        }
        .page-source {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">PageSpeed Analyzer</h1>
        <form method="post">
            <div class="form-group">
                <label for="url">Enter URL:</label>
                <input type="text" name="url" id="url" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Analyze</button>
        </form>
        <?php if(isset($page_load_time) && isset($page_size) && isset($http_requests)): ?>
        <div class="mt-5">
            <h2 class="text-center">Analysis Result for <?php echo $domain; ?>:</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Page Load Time">Page Load Time</td>
                            <td data-label="Value" style="color: <?php echo $load_time_color; ?>"><?php echo round($page_load_time, 2); ?> seconds</td>
                        </tr>
                        <tr>
                            <td data-label="Page Size">Page Size</td>
                            <td data-label="Value" style="color: <?php echo $size_color; ?>"><?php echo round($page_size / 1024, 2); ?> KB</td>
                        </tr>
                        <tr>
                            <td data-label="HTTP Requests">HTTP Requests</td>
                            <td data-label="Value" style="color: <?php echo $request_color; ?>"><?php echo $http_requests; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="page-source mt-5">
            <h2 class="text-center">Page Source</h2>
            <div class="table-responsive">
                <textarea class="form-control" rows="10"><?php echo $page_source; ?></textarea>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> javatech.id - All Rights Reserved
    </footer>
    <!-- Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

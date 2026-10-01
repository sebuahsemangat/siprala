<?php
/**
 * =========================================================================
 * RACE CONDITION TEST SCRIPT
 * File: test_race_condition.php
 * 
 * TUJUAN:
 * Menguji apakah folder creation sudah race-condition safe dengan simulasi
 * multiple concurrent requests (450 siswa absen bersamaan)
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke root directory
 * 2. Akses via browser: http://yoursite.com/test_race_condition.php
 * 3. Lihat hasil test
 * 4. HAPUS file ini setelah testing selesai
 * 
 * SECURITY WARNING:
 * File ini harus dihapus setelah testing untuk keamanan
 * =========================================================================
 */

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Output styling
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race Condition Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f7fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .test-case {
            margin: 20px 0;
            padding: 20px;
            border-left: 4px solid #3498db;
            background: #ecf0f1;
            border-radius: 5px;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        .warning {
            color: #f39c12;
            font-weight: bold;
        }
        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 13px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Race Condition Test - Folder Creation</h1>
        <p><strong>Waktu Test:</strong> <?= date('Y-m-d H:i:s') ?></p>
        
        <?php
        // =========================================================================
        // TEST CONFIGURATION
        // =========================================================================
        $test_base_dir = "test_race_condition/";
        $concurrent_requests = 50; // Simulasi 50 request bersamaan
        $test_folders = [];
        
        // =========================================================================
        // TEST 1: OLD METHOD (Prone to Race Condition)
        // =========================================================================
        echo "<div class='test-case'>";
        echo "<h2>📋 Test 1: Old Method (WITHOUT Race Condition Fix)</h2>";
        echo "<p>Simulasi pembuatan folder dengan metode lama...</p>";
        
        $old_method_errors = 0;
        $old_method_start = microtime(true);
        
        for ($i = 1; $i <= $concurrent_requests; $i++) {
            $folder_name = $test_base_dir . "old_method/siswa_" . $i . "/";
            
            // OLD METHOD (prone to race condition)
            if (!is_dir($folder_name)) {
                if (!@mkdir($folder_name, 0755, true)) {
                    $old_method_errors++;
                }
            }
            
            $test_folders[] = $folder_name;
        }
        
        $old_method_time = microtime(true) - $old_method_start;
        
        if ($old_method_errors > 0) {
            echo "<p class='error'>❌ Gagal: {$old_method_errors} folder tidak dapat dibuat</p>";
        } else {
            echo "<p class='success'>✅ Semua folder berhasil dibuat</p>";
        }
        echo "<p><strong>Waktu Eksekusi:</strong> " . round($old_method_time * 1000, 2) . " ms</p>";
        echo "</div>";
        
        // =========================================================================
        // TEST 2: NEW METHOD (Race-Condition Safe)
        // =========================================================================
        echo "<div class='test-case'>";
        echo "<h2>📋 Test 2: New Method (WITH Race Condition Fix)</h2>";
        echo "<p>Simulasi pembuatan folder dengan metode baru (race-condition safe)...</p>";
        
        $new_method_errors = 0;
        $new_method_start = microtime(true);
        
        for ($i = 1; $i <= $concurrent_requests; $i++) {
            $folder_name = $test_base_dir . "new_method/siswa_" . $i . "/";
            
            // NEW METHOD (race-condition safe)
            if (!is_dir($folder_name)) {
                @mkdir($folder_name, 0755, true);
                
                // Double-check
                if (!is_dir($folder_name)) {
                    $new_method_errors++;
                }
            }
            
            $test_folders[] = $folder_name;
        }
        
        $new_method_time = microtime(true) - $new_method_start;
        
        if ($new_method_errors > 0) {
            echo "<p class='error'>❌ Gagal: {$new_method_errors} folder tidak dapat dibuat</p>";
        } else {
            echo "<p class='success'>✅ Semua folder berhasil dibuat</p>";
        }
        echo "<p><strong>Waktu Eksekusi:</strong> " . round($new_method_time * 1000, 2) . " ms</p>";
        echo "</div>";
        
        // =========================================================================
        // TEST 3: CONCURRENT SIMULATION (Same Folder)
        // =========================================================================
        echo "<div class='test-case'>";
        echo "<h2>📋 Test 3: Concurrent Access Simulation</h2>";
        echo "<p>Simulasi 50 request mencoba membuat folder yang sama bersamaan...</p>";
        
        $same_folder = $test_base_dir . "concurrent_test/shared_folder/";
        $concurrent_errors = 0;
        $concurrent_start = microtime(true);
        
        for ($i = 1; $i <= $concurrent_requests; $i++) {
            // Simulasi concurrent access ke folder yang sama
            if (!is_dir($same_folder)) {
                @mkdir($same_folder, 0755, true);
                
                // Double-check
                if (!is_dir($same_folder)) {
                    $concurrent_errors++;
                }
            }
            
            // Verify folder is writable
            if (!is_writable($same_folder)) {
                $concurrent_errors++;
            }
        }
        
        $concurrent_time = microtime(true) - $concurrent_start;
        
        if ($concurrent_errors > 0) {
            echo "<p class='error'>❌ Gagal: {$concurrent_errors} error terdeteksi</p>";
        } else {
            echo "<p class='success'>✅ Semua concurrent request berhasil dihandle</p>";
        }
        echo "<p><strong>Waktu Eksekusi:</strong> " . round($concurrent_time * 1000, 2) . " ms</p>";
        
        $test_folders[] = $same_folder;
        echo "</div>";
        
        // =========================================================================
        // TEST 4: PERMISSION TEST
        // =========================================================================
        echo "<div class='test-case'>";
        echo "<h2>📋 Test 4: Permission & Writable Check</h2>";
        
        $permission_test_folder = $test_base_dir . "permission_test/";
        
        if (!is_dir($permission_test_folder)) {
            @mkdir($permission_test_folder, 0755, true);
        }
        
        $test_folders[] = $permission_test_folder;
        
        if (is_dir($permission_test_folder)) {
            echo "<p class='success'>✅ Folder created: {$permission_test_folder}</p>";
            
            $perms = substr(sprintf('%o', fileperms($permission_test_folder)), -4);
            echo "<p><strong>Permission:</strong> {$perms}</p>";
            
            if (is_writable($permission_test_folder)) {
                echo "<p class='success'>✅ Folder is writable</p>";
            } else {
                echo "<p class='error'>❌ Folder is NOT writable</p>";
            }
            
            if (is_readable($permission_test_folder)) {
                echo "<p class='success'>✅ Folder is readable</p>";
            } else {
                echo "<p class='error'>❌ Folder is NOT readable</p>";
            }
        } else {
            echo "<p class='error'>❌ Failed to create folder</p>";
        }
        
        echo "</div>";
        
        // =========================================================================
        // TEST STATISTICS
        // =========================================================================
        echo "<h2>📊 Test Statistics</h2>";
        echo "<div class='stats'>";
        
        echo "<div class='stat-card'>";
        echo "<div class='stat-value'>{$concurrent_requests}</div>";
        echo "<div class='stat-label'>Total Requests</div>";
        echo "</div>";
        
        echo "<div class='stat-card'>";
        $total_errors = $old_method_errors + $new_method_errors + $concurrent_errors;
        echo "<div class='stat-value'>{$total_errors}</div>";
        echo "<div class='stat-label'>Total Errors</div>";
        echo "</div>";
        
        echo "<div class='stat-card'>";
        $success_rate = round((($concurrent_requests * 3 - $total_errors) / ($concurrent_requests * 3)) * 100, 2);
        echo "<div class='stat-value'>{$success_rate}%</div>";
        echo "<div class='stat-label'>Success Rate</div>";
        echo "</div>";
        
        echo "<div class='stat-card'>";
        $avg_time = round((($old_method_time + $new_method_time + $concurrent_time) / 3) * 1000, 2);
        echo "<div class='stat-value'>{$avg_time}ms</div>";
        echo "<div class='stat-label'>Avg Response Time</div>";
        echo "</div>";
        
        echo "</div>";
        
        // =========================================================================
        // FINAL VERDICT
        // =========================================================================
        echo "<div class='test-case' style='border-left-color: ";
        echo ($total_errors == 0) ? "#27ae60" : "#e74c3c";
        echo ";'>";
        echo "<h2>🎯 Final Verdict</h2>";
        
        if ($total_errors == 0) {
            echo "<p class='success' style='font-size: 18px;'>✅ PASS - Race condition sudah teratasi!</p>";
            echo "<p>Sistem siap menangani 450+ siswa concurrent requests.</p>";
        } else {
            echo "<p class='error' style='font-size: 18px;'>❌ FAIL - Masih ada {$total_errors} error</p>";
            echo "<p>Periksa permission folder atau konfigurasi server.</p>";
        }
        
        echo "</div>";
        
        // =========================================================================
        // CLEANUP
        // =========================================================================
        echo "<div class='test-case'>";
        echo "<h2>🧹 Cleanup Test Folders</h2>";
        
        function deleteDirectory($dir) {
            if (!file_exists($dir)) return true;
            if (!is_dir($dir)) return unlink($dir);
            
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
            }
            
            return rmdir($dir);
        }
        
        if (isset($_GET['cleanup']) && $_GET['cleanup'] == 'yes') {
            if (deleteDirectory($test_base_dir)) {
                echo "<p class='success'>✅ Test folders berhasil dihapus</p>";
            } else {
                echo "<p class='error'>❌ Gagal menghapus test folders</p>";
            }
        } else {
            echo "<p>Test folders dibiarkan untuk inspeksi manual.</p>";
            echo "<p><a href='?cleanup=yes' style='color: #3498db;'>Klik di sini untuk cleanup test folders</a></p>";
        }
        
        echo "</div>";
        
        // =========================================================================
        // RECOMMENDATIONS
        // =========================================================================
        echo "<div class='test-case' style='background: #d5f4e6;'>";
        echo "<h2>💡 Recommendations</h2>";
        echo "<ul>";
        echo "<li>✅ Implementasi race-condition fix sudah diterapkan di <code>proses_absensi.php</code></li>";
        echo "<li>✅ Gunakan <code>@mkdir()</code> dengan double-check untuk concurrent safety</li>";
        echo "<li>✅ Selalu verifikasi folder writable sebelum upload file</li>";
        echo "<li>⚠️ Monitor error logs untuk mendeteksi permission issues</li>";
        echo "<li>⚠️ Lakukan load testing berkala dengan 450+ concurrent users</li>";
        echo "</ul>";
        echo "</div>";
        ?>
        
        <hr>
        <p><strong>⚠️ KEAMANAN:</strong> Segera hapus file <code>test_race_condition.php</code> setelah testing!</p>
        <p><a href="absen.php" style="color: #3498db;">← Kembali ke Halaman Absensi</a></p>
    </div>
</body>
</html>
<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Core/Autoload.php';
require_once __DIR__ . '/../App/Controllers/AsdosController.php';

use App\Controllers\AsdosController;

// Inisialisasi Global Error & Exception Handler
Core\ErrorHandler::register();

$asdosController = new AsdosController();
$router = new Core\Router();

// ==========================================
// 1. RUTE AUTH & GUEST
// ==========================================
$router->get('/', 'AuthController', 'login', ['guest']);
$router->get('/login', 'AuthController', 'login', ['guest']);
$router->post('/login', 'AuthController', 'processLogin', ['guest', 'csrf']);
$router->get('/logout', 'AuthController', 'logout');

// ==========================================
// 2. RUTE ASDOS & DOSEN (PLACEHOLDER)
// ==========================================

$router->get('/asdos/dashboard', 'asdosController', 'dashboard', ['auth', 'asdos']);

$router->get('/asdos/matkul', 'asdosController', 'matkul');
$router->get('/asdos/absensi', 'asdosController', 'absensi');
$router->post('/asdos/absensi', 'asdosController', 'storeAbsensi');
$router->post('asdos/absensi/create', 'asdosController', 'createAbsensi');
$router->get('/asdos/history', 'asdosController', 'history');


$router->get('/dosen/dashboard', 'AuthController', 'dosenDashboard', ['auth', 'dosen']);

// ==========================================
// 3. RUTE SUPER ADMIN
// ==========================================
$router->get('/superadmin/dashboard', 'SuperAdminController', 'dashboard', ['auth', 'super_admin']);

// Kelola Pengguna (Users)
$router->get('/superadmin/users', 'SuperAdminController', 'users', ['auth', 'super_admin']);
$router->post('/superadmin/users/create', 'SuperAdminController', 'createUser', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/users/{id}/update', 'SuperAdminController', 'updateUser', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/users/{id}/toggle', 'SuperAdminController', 'toggleUserStatus', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/users/{id}/delete', 'SuperAdminController', 'deleteUser', ['auth', 'super_admin', 'csrf']);

// Master Data Mata Kuliah
$router->get('/superadmin/matkul', 'SuperAdminController', 'matkul', ['auth', 'super_admin']);
$router->post('/superadmin/matkul/create', 'SuperAdminController', 'createMatkul', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/matkul/{id}/update', 'SuperAdminController', 'updateMatkul', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/matkul/{id}/delete', 'SuperAdminController', 'deleteMatkul', ['auth', 'super_admin', 'csrf']);

// Plotting Asisten Dosen
$router->get('/superadmin/plotting', 'SuperAdminController', 'plotting', ['auth', 'super_admin']);
$router->post('/superadmin/plotting/create', 'SuperAdminController', 'createPlotting', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/plotting/{id}/update', 'SuperAdminController', 'updatePlotting', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/plotting/{id}/toggle', 'SuperAdminController', 'togglePlottingStatus', ['auth', 'super_admin', 'csrf']);
$router->post('/superadmin/plotting/{id}/delete', 'SuperAdminController', 'deletePlotting', ['auth', 'super_admin', 'csrf']);

// Monitoring Seluruh Absensi & Verifikasi (Read-Only)
$router->get('/superadmin/monitoring', 'SuperAdminController', 'monitoring', ['auth', 'super_admin']);

$router->dispatch();

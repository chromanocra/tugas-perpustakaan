<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/home/coba-parameter/(:alpha)/(:num)/(:alphanum)', 'Home::belajar_segment/$1/$2/$3/');
// $routes->get('/dashboard','Home::dashboard');

// admin 

$routes->get('/admin/login-admin', 'Auth::login');
$routes->get('/admin/dashboard-admin', 'Dashboard::dashboard');
$routes->post('/admin/autentikasi-login', 'Auth::autentikasi');
$routes->get('/admin/logout', 'Auth::logout');

// routes untuk module admin

$routes->get('/admin/master-data-admin', 'Admin::master_data_admin');
$routes->get('/admin/input-data-admin', 'Admin::input_data_admin');
$routes->post('/admin/simpan-admin', 'Admin::simpan_data_admin');
$routes->get('/admin/edit-data-admin/(:alphanum)', 'Admin::edit_data_admin/$1');
$routes->post('/admin/update-admin', 'Admin::update_data_admin');
$routes->get('/admin/hapus-data-admin/(:alphanum)', 'Admin::hapus_data_admin/$1');

$routes->get('/admin/edit-data-admin/(:alphanum)', 'Admin::edit_data_admin/$1');
$routes->post('/admin/update-admin', 'Admin::update_data_admin');
$routes->get('/admin/hapus-data-admin/(:alphanum)', 'Admin::hapus_data_admin/$1');

// Routes Anggota
$routes->get('/admin/master-data-anggota', 'Anggota::master_data_anggota');
$routes->get('/admin/input-data-anggota', 'Anggota::input_data_anggota');
$routes->post('/admin/simpan-anggota', 'Anggota::simpan_data_anggota');
$routes->get('/admin/edit-data-anggota/(:alphanum)', 'Anggota::edit_data_anggota/$1');
$routes->post('/admin/update-anggota', 'Anggota::update_data_anggota');
$routes->get('/admin/hapus-data-anggota/(:alphanum)', 'Anggota::hapus_data_anggota/$1');

// Routes Rak
$routes->get('/admin/master-data-rak', 'Rak::master_data_rak');
$routes->get('/admin/input-data-rak', 'Rak::input_data_rak');
$routes->post('/admin/simpan-rak', 'Rak::simpan_data_rak');
$routes->get('/admin/edit-data-rak/(:alphanum)', 'Rak::edit_data_rak/$1');
$routes->post('/admin/update-rak', 'Rak::update_data_rak');
$routes->get('/admin/hapus-data-rak/(:alphanum)', 'Rak::hapus_data_rak/$1');

// Routes Kategori
$routes->get('/admin/master-data-kategori', 'Kategori::master_data_kategori');
$routes->get('/admin/input-data-kategori', 'Kategori::input_data_kategori');
$routes->post('/admin/simpan-kategori', 'Kategori::simpan_data_kategori');
$routes->get('/admin/edit-data-kategori/(:alphanum)', 'Kategori::edit_data_kategori/$1');
$routes->post('/admin/update-kategori', 'Kategori::update_data_kategori');
$routes->get('/admin/hapus-data-kategori/(:alphanum)', 'Kategori::hapus_data_kategori/$1');

// Routes Buku
$routes->get('/admin/master-data-buku-buku', 'Buku::master_buku');
$routes->get('/admin/input-buku', 'Buku::input_buku');
$routes->post('/admin/simpan-buku', 'Buku::simpan_buku');
$routes->get('/admin/edit-buku/(:alphanum)', 'Buku::edit_buku/$1');
$routes->post('/admin/update-buku', 'Buku::update_buku');
$routes->get('/admin/hapus-buku/(:alphanum)', 'Buku::hapus_buku/$1');

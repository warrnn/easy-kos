Feature: Manajemen Admin

  Scenario: Admin membuat akun pemilik kos baru
    When "admin" membuka aplikasi
    And admin melakukan login
    Then halaman dashboard admin ditampilkan
    When admin menekan tombol "+ Users" pada halaman dashboard admin
    And admin mengisi form data akun pemilik kos baru
    And admin menekan tombol "Tambah Akun"
    Then data akun tampil di tabel user pada halaman manajemen data user

  Scenario: Admin membuat data kos baru
    When "admin" membuka aplikasi
    And admin melakukan login
    Then halaman dashboard admin ditampilkan
    When admin menekan tombol "+ Kos" pada halaman dashboard admin
    And admin mengisi form data kos baru
    And admin menekan tombol "Tambah Kos"
    Then data kos tampil di tabel kos pada halaman manajemen data kos

  Scenario: Admin mengubah data kos yang sudah ada
    When "admin" membuka aplikasi
    And admin melakukan login
    Then halaman dashboard admin ditampilkan
    When admin menuju ke halaman manajemen
    And admin menekan tab "Kos" untuk menampilkan data-data kos yang ada
    And admin menekan "ikon edit" yang ada di salah satu data
    And admin melakukan perubahan data di form
    And admin menekan tombol "Edit Kos"
    Then data tampil pada tabel di halaman manajemen kos dengan data yang sudah berubah

  Scenario: Admin menghapus data kos yang sudah ada
    When "admin" membuka aplikasi
    And admin melakukan login
    Then halaman dashboard admin ditampilkan
    When admin menuju ke halaman manajemen
    And admin menekan tab "Kos" untuk menampilkan data-data kos yang ada
    And admin menekan "ikon delete trash" yang ada di salah satu data
    And admin menekan tombol "Yes, delete it!" pada modal alert
    Then data tidak lagi tampil di aplikasi
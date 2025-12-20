Feature: Manajemen Pemilik Kos

  Scenario: Pemilik kos menambahkan data kamar baru pada kos-nya
    When pemilik kos membuka aplikasi
    And pemilik kos melakukan login
    Then halaman dashboard pemilik kos ditampilkan
    When pemilik kos menekan tombol "Laporan"
    Then halaman menuju ke manajemen kamar kos
    When pemilik kos menekan tombol "ADD KAMAR"
    Then halaman menuju ke halaman pembuatan data kamar
    When pemilik kos mengisi form data kamar
    And pemilik kos menekan tombol "Simpan"
    Then halaman kembali ke halaman tabel data kos
    And menampilkan data kamar kos baru

  Scenario: Pemilik kos menerima pesanan dari penghuni kos
    When pemilik kos membuka aplikasi
    And pemilik kos melakukan login
    Then halaman dashboard pemilik kos ditampilkan
    When pemilik kos menekan tombol "Request"
    Then halaman menuju ke tabel data pesanan kamar
    When pemilik kos menekan tombol "Terima"
    Then pada tabel status berubah menjadi "Terima"

  Scenario: Pemilik kos melihat laporan kamar kos
    When pemilik kos membuka aplikasi
    And pemilik kos melakukan login
    Then halaman dashboard pemilik kos ditampilkan
    When pemilik kos menekan tombol "Laporan"
    Then halaman menuju ke manajemen kamar kos
    And halaman laporan menampilkan data-data kamar kos beserta status-nya
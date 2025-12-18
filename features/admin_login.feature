Feature: Admin Login
  Sebagai admin
  Saya ingin login ke sistem
  Agar saya bisa mengakses dashboard admin

  Scenario: Admin berhasil login dan mengakses dashboard
    Given saya berada di halaman login
    When saya mengisi username dengan "admin"
    And saya mengisi password dengan "admin"
    And saya menekan tombol login
    Then saya harus berada di halaman dashboard admin
    And saya harus melihat teks "Total Overview"
